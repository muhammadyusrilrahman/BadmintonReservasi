<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan reservasi pending yang melebihi batas waktu pembayaran';

    /**
     * Execute the console command.
     */
    public function handle(ReservationService $reservationService): int
    {
        $this->info('Memulai pengecekan reservasi kedaluwarsa...');

        $count = 0;

        Reservation::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(config('reservation.expiry_minutes')))
            ->chunkById(100, function ($reservations) use ($reservationService, &$count) {
                foreach ($reservations as $reservation) {
                    DB::transaction(function () use ($reservation, $reservationService, &$count) {
                        // Lock rows to prevent race condition with webhook
                        $lockedReservation = Reservation::where('id', $reservation->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$lockedReservation || $lockedReservation->status !== 'pending') {
                            return;
                        }

                        $payment = $lockedReservation->payment()->lockForUpdate()->first();

                        if ($payment && $payment->status === 'pending') {
                            $reservationService->cancelReservation($lockedReservation);
                            $this->line("Reservasi #{$lockedReservation->id} berhasil dibatalkan otomatis.");
                            $count++;
                        }
                    });
                }
            });

        $this->info("Pengecekan selesai. Total {$count} reservasi dibatalkan.");

        if ($count > 0) {
            Log::info("Command reservations:expire membatalkan {$count} reservasi yang kedaluwarsa.");
        }

        return Command::SUCCESS;
    }
}
