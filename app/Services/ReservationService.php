<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Repositories\Contracts\CourtRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Validators\ReservationValidator;
use App\Jobs\ExpireReservationJob;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReservationService extends BaseService
{
    public function __construct(
        ReservationRepositoryInterface $repository,
        private readonly CourtRepositoryInterface $courtRepository,
        private readonly ReservationValidator $validator,
        private readonly \App\Services\NotificationService $notificationService,
        private readonly \App\Services\PromoCodeService $promoCodeService
    ) {
        parent::__construct($repository);
    }

    public function getPaginatedFiltered(
        int $perPage = 10,
        ?string $search = null,
        ?string $date = null,
        ?string $status = null,
        ?int $courtId = null
    ): LengthAwarePaginator {
        return $this->repository->getPaginatedFiltered($perPage, $search, $date, $status, $courtId);
    }

    /**
     * Create offline booking by Admin/Kasir.
     */
    public function createAdminOfflineBooking(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            // Pessimistic Locking: Lock court row for update
            $court = $this->courtRepository->findWithLock($data['court_id']);
            if (!$court || !$court->is_active) {
                throw new InvalidArgumentException("Lapangan tidak ditemukan atau sedang tidak aktif.");
            }

            // Check maintenance schedule
            $hasMaintenance = $court->maintenances()
                ->whereDate('scheduled_date', $data['date'])
                ->pending()
                ->exists();

            if ($hasMaintenance) {
                throw new InvalidArgumentException("Lapangan '{$court->name}' sedang dalam pemeliharaan (maintenance) pada tanggal " . Carbon::parse($data['date'])->format('d-m-Y') . ".");
            }

            // Calculate times
            $startTime = Carbon::parse($data['start_time']);
            $endTime = $startTime->copy()->addHours((int) $data['duration_hours']);
            
            // Check availability under lock
            $isAvailable = $this->courtRepository->isAvailable(
                courtId: $court->id,
                date: $data['date'],
                startTime: $startTime->format('H:i:s'),
                endTime: $endTime->format('H:i:s')
            );

            if (!$isAvailable) {
                throw new InvalidArgumentException("Lapangan sudah dipesan pada rentang waktu tersebut.");
            }

            $dayOfWeek = $startTime->dayOfWeek;
            $slots = $court->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->get();

            $totalPrice = 0;
            $currentHour = $startTime->copy();

            for ($i = 0; $i < $data['duration_hours']; $i++) {
                $nextHour = $currentHour->copy()->addHour();
                $foundSlot = $slots->first(function ($slot) use ($currentHour, $nextHour) {
                    return $slot->start_time <= $currentHour->format('H:i:s')
                        && $slot->end_time >= $nextHour->format('H:i:s');
                });

                if (!$foundSlot) {
                    // Fallback to court's default price_per_hour if schedule is not explicitly set
                    if ($court->price_per_hour > 0) {
                        $totalPrice += $court->price_per_hour;
                    } else {
                        throw new InvalidArgumentException("Jadwal harga untuk jam " . $currentHour->format('H:i') . " belum diatur dan harga default kosong.");
                    }
                } else {
                    $totalPrice += $foundSlot->price;
                }

                $currentHour->addHour();
            }

            // Create Reservation (Confirmed directly for offline)
            $reservation = $this->repository->create([
                'user_id'        => $data['user_id'],
                'court_id'       => $court->id,
                'date'           => $data['date'],
                'start_time'     => $startTime->format('H:i:s'),
                'end_time'       => $endTime->format('H:i:s'),
                'duration_hours' => $data['duration_hours'],
                'total_price'    => $totalPrice,
                'status'         => 'confirmed', // Automatically confirmed
                'notes'          => $data['notes'] ?? 'Offline Booking (Admin/Kasir)',
            ]);

            // Create Payment (Automatically paid via Cash)
            $reservation->payment()->create([
                'amount'         => $totalPrice,
                'payment_method' => 'cash',
                'status'         => 'paid',
                'paid_at'        => now(),
                'verified_by'    => auth()->id(),
                'verified_at'    => now(),
            ]);

            return $reservation;
        });
    }

    /**
     * Verify payment status.
     * Jika payment memiliki booking_session_id, semua reservasi dalam sesi tersebut akan diupdate.
     */
    public function verifyPayment(Reservation $reservation, string $status): void
    {
        DB::transaction(function () use ($reservation, $status) {
            // Gunakan sessionPayment jika tersedia, fallback ke payment langsung
            $payment = $reservation->sessionPayment();

            if (!$payment) {
                throw new InvalidArgumentException("Data pembayaran tidak ditemukan.");
            }

            $updateData = [
                'status'      => $status,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ];

            if ($status === 'paid') {
                $updateData['paid_at'] = now();
            }

            $payment->update($updateData);

            // Ambil semua reservasi yang termasuk dalam sesi ini
            $reservationsToUpdate = $payment->booking_session_id
                ? Reservation::where('booking_session_id', $payment->booking_session_id)->get()
                : collect([$reservation]);

            // Sync reservation status untuk semua reservasi dalam sesi
            foreach ($reservationsToUpdate as $res) {
                if ($status === 'paid') {
                    $res->update(['status' => 'confirmed']);
                    $this->notificationService->sendPaymentSuccess($res);
                } elseif ($status === 'failed') {
                    $res->update(['status' => 'cancelled']);
                    $this->notificationService->sendPaymentFailed($res);
                }
            }
        });
    }

    /**
     * Cancel reservation.
     * Jika reservasi memiliki booking_session_id, SEMUA reservasi dalam sesi dibatalkan.
     */
    public function cancelReservation(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            if ($reservation->booking_session_id) {
                // Batalkan semua reservasi dalam sesi yang sama
                $siblings = Reservation::where('booking_session_id', $reservation->booking_session_id)->get();
                foreach ($siblings as $sib) {
                    $sib->update(['status' => 'cancelled']);
                }

                // Batalkan juga payment sesi
                $sessionPayment = \App\Models\Payment::where('booking_session_id', $reservation->booking_session_id)->first();
                if ($sessionPayment && $sessionPayment->status !== 'paid') {
                    $sessionPayment->update(['status' => 'failed']);
                }
            } else {
                // Single reservation (backward compatible)
                $reservation->update(['status' => 'cancelled']);

                if ($reservation->payment && $reservation->payment->status !== 'paid') {
                    $reservation->payment->update(['status' => 'failed']);
                }
            }
        });
    }

    /**
     * Create customer booking dari slot jadwal yang dipilih.
     * Semua slot dalam 1 sesi menghasilkan 1 Payment tunggal (booking_session_id).
     *
     * @param array $scheduleIds        Array schedule IDs untuk hari ini
     * @param int   $userId
     * @param string $date
     * @param int   $courtId
     * @param string|null $paymentMethod
     * @param string|null $notes
     * @param string|null $promoCode
     * @param string|null $bookingSessionId UUID sesi booking (dibuat di Controller untuk multi-hari)
     * @param bool  $isLastGroup         true jika ini grup terakhir dalam sesi (akan buat payment)
     * @param int   $sessionTotalPrice   Total harga seluruh sesi (untuk payment tunggal)
     * @return array<Reservation>
     */
    public function createCustomerBooking(
        array $scheduleIds,
        int $userId,
        string $date,
        int $courtId,
        ?string $paymentMethod = 'transfer',
        ?string $notes = null,
        ?string $promoCode = null,
        ?string $bookingSessionId = null,
        bool $isLastGroup = true,
        int $sessionTotalPrice = 0
    ): array {
        return DB::transaction(function () use (
            $scheduleIds, $userId, $date, $courtId, $paymentMethod,
            $notes, $promoCode, $bookingSessionId, $isLastGroup, $sessionTotalPrice
        ) {
            // Pessimistic Locking: Lock court row for update
            $court = $this->courtRepository->findWithLock($courtId);
            if (!$court) {
                throw new InvalidArgumentException("Lapangan tidak ditemukan.");
            }

            // Phase 1: Validate ALL slots menggunakan ReservationValidator
            $this->validator->validateAvailability($court, $date, $scheduleIds);

            // Fetch schedules
            $schedules = $court->schedules()
                ->whereIn('id', $scheduleIds)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();

            // Phase 1.5: Validate promo code
            // Jika bookingSessionId = null (single atau backward compat): validasi di sini
            // Jika bookingSessionId ada: promo sudah divalidasi di Controller, tidak perlu validasi ulang
            $promoData = null;
            if ($promoCode && $bookingSessionId === null) {
                $totalOriginalPrice = $schedules->sum('price');
                $promoData = $this->promoCodeService->validateAndApply($promoCode, $totalOriginalPrice);
            }

            // Phase 2: Buat reservasi (tanpa payment individual)
            $reservations = [];
            $totalSlots = count($schedules);
            $remainingDiscount = $promoData ? $promoData['discount'] : 0;

            foreach ($schedules as $index => $schedule) {
                $startCarbon = Carbon::parse($schedule->start_time);
                $endCarbon = Carbon::parse($schedule->end_time);
                $durationHours = (int) $startCarbon->diffInHours($endCarbon);
                if ($durationHours < 1) $durationHours = 1;

                $originalPrice = $schedule->price;
                $slotDiscount = 0;

                if ($promoData && $remainingDiscount > 0) {
                    if ($index === $totalSlots - 1) {
                        $slotDiscount = min($remainingDiscount, $originalPrice);
                    } else {
                        $totalOriginalPrice = $schedules->sum('price');
                        $slotDiscount = (int) floor($promoData['discount'] * $originalPrice / $totalOriginalPrice);
                        $slotDiscount = min($slotDiscount, $originalPrice, $remainingDiscount);
                    }
                    $remainingDiscount -= $slotDiscount;
                }

                $finalPrice = $originalPrice - $slotDiscount;

                $reservationData = [
                    'user_id'            => $userId,
                    'court_id'           => $court->id,
                    'date'               => $date,
                    'start_time'         => $schedule->start_time,
                    'end_time'           => $schedule->end_time,
                    'duration_hours'     => $durationHours,
                    'total_price'        => $finalPrice,
                    'status'             => 'pending',
                    'notes'              => $notes,
                    'booking_session_id' => $bookingSessionId,
                ];

                if ($promoData && $slotDiscount > 0) {
                    $reservationData['promo_code_id']  = $promoData['promo']->id;
                    $reservationData['original_price'] = $originalPrice;
                    $reservationData['discount_amount'] = $slotDiscount;
                }

                $reservation = $this->repository->create($reservationData);

                // Dispatch job expire hanya jika ini adalah group dengan session payment aktif
                ExpireReservationJob::dispatch($reservation)->delay(now()->addMinutes(config('reservation.expiry_minutes')));

                $reservations[] = $reservation;
            }

            // Phase 3: Buat SATU Payment untuk seluruh sesi (hanya di grup terakhir)
            if ($isLastGroup && $bookingSessionId) {
                Payment::create([
                    'reservation_id'     => $reservations[0]->id, // Linked ke reservasi pertama dalam sesi ini
                    'booking_session_id' => $bookingSessionId,
                    'amount'             => $sessionTotalPrice ?: array_sum(array_column(
                        array_map(fn($r) => ['total_price' => $r->total_price], $reservations),
                        'total_price'
                    )),
                    'payment_method'     => $paymentMethod,
                    'status'             => 'pending',
                ]);
            } elseif ($isLastGroup && !$bookingSessionId) {
                // Fallback: single booking tanpa session (backward compatible)
                foreach ($reservations as $reservation) {
                    $reservation->payment()->create([
                        'amount'         => $reservation->total_price,
                        'payment_method' => $paymentMethod,
                        'status'         => 'pending',
                    ]);
                }
            }

            // Increment promo usage
            if ($promoData) {
                $this->promoCodeService->incrementUsage($promoData['promo']);
            }

            return $reservations;
        });
    }

    /**
     * Get available schedule slots for a court on a given date.
     */
    public function getAvailableSlots(int $courtId, string $date): array
    {
        $court = $this->courtRepository->find($courtId);
        if (!$court) return [];

        $dateCarbon = Carbon::parse($date);
        $dayOfWeek = $dateCarbon->dayOfWeek;

        $schedules = $court->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return $schedules->map(function ($schedule) use ($courtId, $date) {
            $isAvailable = $this->courtRepository->isAvailable(
                courtId: $courtId,
                date: $date,
                startTime: $schedule->start_time,
                endTime: $schedule->end_time
            );

            return [
                'id'         => $schedule->id,
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time'   => Carbon::parse($schedule->end_time)->format('H:i'),
                'price'      => $schedule->price,
                'available'  => $isAvailable,
            ];
        })->toArray();
    }

    /**
     * Upload payment proof untuk satu reservasi atau seluruh sesi booking.
     * Jika reservasi memiliki booking_session_id, upload ke payment sesi.
     */
    public function uploadPaymentProof(Reservation $reservation, string $proofPath): void
    {
        $payment = $reservation->sessionPayment();

        if (!$payment) {
            throw new InvalidArgumentException("Data pembayaran tidak ditemukan.");
        }

        $payment->update([
            'payment_proof' => $proofPath,
        ]);
    }
}
