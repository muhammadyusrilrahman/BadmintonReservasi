<?php

namespace App\Validators;

use App\Models\Court;
use App\Repositories\Contracts\CourtRepositoryInterface;
use Carbon\Carbon;
use InvalidArgumentException;

class ReservationValidator
{
    public function __construct(private readonly CourtRepositoryInterface $courtRepository)
    {
    }

    /**
     * Validate reservation request.
     * Throws InvalidArgumentException if there is a conflict.
     *
     * @param Court $court
     * @param string $date
     * @param array<int> $scheduleIds
     * @param int|null $excludeReservationId
     * @throws InvalidArgumentException
     */
    public function validateAvailability(Court $court, string $date, array $scheduleIds, ?int $excludeReservationId = null): void
    {
        // 1. Ensure court is active
        if (!$court->is_active) {
            throw new InvalidArgumentException("Lapangan '{$court->name}' sedang tidak aktif.");
        }

        // 2. Ensure date is not in the past
        $dateCarbon = Carbon::parse($date);
        if ($dateCarbon->isBefore(today())) {
            throw new InvalidArgumentException("Tanggal reservasi tidak boleh di masa lalu.");
        }

        // 3. Ensure no pending/active court maintenance on that date
        $hasMaintenance = $court->maintenances()
            ->whereDate('scheduled_date', $date)
            ->pending()
            ->exists();

        if ($hasMaintenance) {
            throw new InvalidArgumentException("Lapangan '{$court->name}' sedang dalam pemeliharaan (maintenance) pada tanggal {$dateCarbon->format('d-m-Y')}.");
        }

        // 4. Retrieve schedules and validate
        $dayOfWeek = $dateCarbon->dayOfWeek;
        $schedules = $court->schedules()
            ->whereIn('id', $scheduleIds)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($schedules->count() !== count($scheduleIds)) {
            throw new InvalidArgumentException("Beberapa jadwal yang dipilih tidak valid, tidak aktif, atau tidak sesuai dengan hari operasional.");
        }

        // 5. Check overlapping with existing confirmed/pending reservations
        foreach ($schedules as $schedule) {
            $isAvailable = $this->courtRepository->isAvailable(
                courtId: $court->id,
                date: $date,
                startTime: $schedule->start_time,
                endTime: $schedule->end_time,
                excludeReservationId: $excludeReservationId
            );

            if (!$isAvailable) {
                $start = Carbon::parse($schedule->start_time)->format('H:i');
                $end = Carbon::parse($schedule->end_time)->format('H:i');
                throw new InvalidArgumentException("Slot {$start} - {$end} pada lapangan '{$court->name}' sudah tidak tersedia.");
            }
        }
    }
}
