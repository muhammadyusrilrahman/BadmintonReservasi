<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireReservationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Backoff intervals (seconds) between retries.
     *
     * @var array<int>
     */
    public array $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(public Reservation $reservation)
    {
    }

    /**
     * Execute the job.
     *
     * Jika reservasi memiliki booking_session_id, semua reservasi dalam sesi akan diperiksa.
     * Payment ditemukan melalui booking_session_id (session payment) ATAU reservation_id (single).
     * Uses pessimistic locking to prevent race conditions with concurrent webhook processing.
     */
    public function handle(ReservationService $reservationService): void
    {
        DB::transaction(function () use ($reservationService) {
            // Lock reservation row untuk prevent race condition
            $reservation = Reservation::where('id', $this->reservation->id)
                ->lockForUpdate()
                ->first();

            if (!$reservation) {
                return;
            }

            // Skip jika reservasi sudah tidak pending (sudah diproses)
            if ($reservation->status !== 'pending') {
                return;
            }

            // Cari payment: session payment (via booking_session_id) atau direct payment
            $payment = null;
            if ($reservation->booking_session_id) {
                $payment = Payment::where('booking_session_id', $reservation->booking_session_id)
                    ->lockForUpdate()
                    ->first();
            } else {
                $payment = Payment::where('reservation_id', $reservation->id)
                    ->lockForUpdate()
                    ->first();
            }

            // Cancel hanya jika payment masih pending (belum dibayar)
            if ($payment && $payment->status === 'pending') {
                $reservationService->cancelReservation($reservation);

                Log::info("Reservasi #{$reservation->id} (sesi: {$reservation->booking_session_id}) dibatalkan otomatis setelah batas waktu karena belum dibayar.");
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error("ExpireReservationJob gagal untuk Reservasi #{$this->reservation->id}", [
            'error' => $exception?->getMessage(),
        ]);
    }
}
