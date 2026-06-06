<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Repositories\Contracts\CourtRepositoryInterface;
use App\Validators\ReservationValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RescheduleService extends BaseService
{
    public function __construct(
        ReservationRepositoryInterface $repository,
        private readonly CourtRepositoryInterface $courtRepository,
        private readonly ReservationValidator $validator,
        private readonly \App\Services\NotificationService $notificationService
    ) {
        parent::__construct($repository);
    }

    /**
     * Process reschedule reservation to a new date, court, and schedule slots.
     */
    public function reschedule(Reservation $reservation, string $newDate, array $newScheduleIds, ?int $newCourtId = null, ?int $userId = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $newDate, $newScheduleIds, $newCourtId, $userId) {
            // Refetch with lock
            $reservation = Reservation::lockForUpdate()->findOrFail($reservation->id);

            // 1. Validate eligibility
            if (!$reservation->canReschedule()) {
                throw new InvalidArgumentException("Reservasi ini tidak memenuhi syarat untuk di-reschedule (pastikan status confirmed, belum pernah reschedule, dan minimal H-1).");
            }

            // 2. Validate court
            $courtId = $newCourtId ?? $reservation->court_id;
            $court = $this->courtRepository->find($courtId);
            if (!$court) {
                throw new InvalidArgumentException("Lapangan tidak ditemukan.");
            }

            // Get new schedules to calculate new times/durations
            $dateCarbon = Carbon::parse($newDate);
            $dayOfWeek = $dateCarbon->dayOfWeek;
            $newSchedules = $court->schedules()
                ->whereIn('id', $newScheduleIds)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();

            if ($newSchedules->count() !== count($newScheduleIds)) {
                throw new InvalidArgumentException("Jadwal baru yang dipilih tidak valid.");
            }

            // Calculate duration
            $startTime = Carbon::parse($newSchedules->first()->start_time);
            $endTime = Carbon::parse($newSchedules->last()->end_time);
            $durationHours = (int) $startTime->diffInHours($endTime);
            if ($durationHours < 1) $durationHours = 1;

            if ($durationHours !== $reservation->duration_hours) {
                throw new InvalidArgumentException("Durasi jadwal baru ({$durationHours} Jam) harus sama dengan durasi awal ({$reservation->duration_hours} Jam).");
            }

            // Validate slot availability (exclude current reservation)
            $this->validator->validateAvailability($court, $newDate, $newScheduleIds, $reservation->id);

            // Calculate new total price
            $newTotalPrice = $newSchedules->sum('price');

            // Save old details for status log payload
            $oldPayload = [
                'court_id'       => $reservation->court_id,
                'court_name'     => $reservation->court->name,
                'date'           => $reservation->date->format('Y-m-d'),
                'start_time'     => $reservation->start_time,
                'end_time'       => $reservation->end_time,
                'total_price'    => $reservation->total_price,
                'duration_hours' => $reservation->duration_hours,
            ];

            // 3. Update reservation
            $reservation->update([
                'court_id'         => $courtId,
                'date'             => $newDate,
                'start_time'       => $startTime->format('H:i:s'),
                'end_time'         => $endTime->format('H:i:s'),
                'total_price'      => $newTotalPrice,
                'reschedule_count' => $reservation->reschedule_count + 1,
            ]);

            // If there's an associated payment, update its amount
            if ($reservation->payment) {
                $reservation->payment->update([
                    'amount' => $newTotalPrice,
                ]);
            }

            // 4. Create ReservationStatusLog
            $performerId = $userId ?? auth()->id();
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $performerId,
                'old_status'     => $reservation->status,
                'new_status'     => $reservation->status, // status stays same
                'change_type'    => 'reschedule',
                'description'    => "Jadwal diubah ke {$court->name}, tanggal " . $dateCarbon->format('d-m-Y') . " jam " . $startTime->format('H:i') . " - " . $endTime->format('H:i'),
                'payload'        => [
                    'old' => $oldPayload,
                    'new' => [
                        'court_id'       => $courtId,
                        'court_name'     => $court->name,
                        'date'           => $newDate,
                        'start_time'     => $startTime->format('H:i:s'),
                        'end_time'       => $endTime->format('H:i:s'),
                        'total_price'    => $newTotalPrice,
                        'duration_hours' => $durationHours,
                    ]
                ]
            ]);

            $this->notificationService->sendRescheduleApproved($reservation);

            return $reservation;
        });
    }
}
