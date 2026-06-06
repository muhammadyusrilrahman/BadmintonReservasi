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
     * Uses pessimistic locking to prevent race conditions with
     * concurrent webhook processing that may confirm the payment.
     */
    public function handle(ReservationService $reservationService): void
    {
        DB::transaction(function () use ($reservationService) {
            // Lock reservation and payment rows to prevent race condition with webhook
            $reservation = Reservation::where('id', $this->reservation->id)
                ->lockForUpdate()
                ->first();

            if (!$reservation) {
                return;
            }

            $payment = Payment::where('reservation_id', $reservation->id)
                ->lockForUpdate()
                ->first();

            // Cancel only if both reservation and payment are still pending
            if ($reservation->status === 'pending' &&
                ($payment && $payment->status === 'pending')) {

                $reservationService->cancelReservation($reservation);

                Log::info("Reservasi #{$reservation->id} dibatalkan otomatis setelah 15 menit karena belum dibayar.");
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
