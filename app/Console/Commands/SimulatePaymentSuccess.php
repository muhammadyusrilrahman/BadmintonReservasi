<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class SimulatePaymentSuccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:simulate-success {reservation_id? : ID Reservasi yang ingin disimulasikan sukses pembayarannya}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulasikan status pembayaran berhasil (paid) untuk reservasi tertentu (berguna saat pengujian lokal/sandbox)';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $reservationId = $this->argument('reservation_id');

        if (!$reservationId) {
            // Fetch pending reservations
            $pendingReservations = Reservation::where('status', 'pending')
                ->whereHas('payment', function ($query) {
                    $query->where('status', 'pending');
                })
                ->get();

            if ($pendingReservations->isEmpty()) {
                $this->error('Tidak ada reservasi pending dengan status pembayaran pending yang ditemukan.');
                return Command::FAILURE;
            }

            $choices = $pendingReservations->map(function ($res) {
                $user = $res->user ? $res->user->name : 'N/A';
                $court = $res->court ? $res->court->name : 'N/A';
                $date = $res->date->format('Y-m-d');
                $amount = $res->payment ? number_format($res->payment->amount, 0, ',', '.') : '0';
                return "[ID: {$res->id}] {$user} - {$court} ({$date}) - Rp {$amount}";
            })->toArray();

            $choice = $this->choice('Pilih reservasi yang ingin disimulasikan berhasil pembayarannya:', $choices);
            
            // Parse ID from choice string, e.g., "[ID: 15] Name - Court ..."
            preg_match('/\[ID:\s*(\d+)\]/', $choice, $matches);
            $reservationId = (int) $matches[1];
        }

        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            $this->error("Reservasi dengan ID {$reservationId} tidak ditemukan.");
            return Command::FAILURE;
        }

        $payment = $reservation->payment;

        if (!$payment) {
            $this->error("Reservasi #{$reservationId} tidak memiliki data pembayaran.");
            return Command::FAILURE;
        }

        if ($payment->status === 'paid') {
            $this->warn("Pembayaran untuk reservasi #{$reservationId} sudah berstatus PAID (Lunas).");
            return Command::SUCCESS;
        }

        $this->info("Mensimulasikan pembayaran sukses untuk Reservasi #{$reservationId}...");

        try {
            DB::transaction(function () use ($reservation, $payment, $notificationService) {
                // Lock records
                $lockedRes = Reservation::where('id', $reservation->id)->lockForUpdate()->first();
                $lockedPmt = Payment::where('id', $payment->id)->lockForUpdate()->first();

                // Update payment status
                $lockedPmt->update([
                    'status' => 'paid',
                    'payment_type' => 'credit_card', // simulated
                    'midtrans_transaction_id' => 'SIM-TX-' . strtoupper(bin2hex(random_bytes(6))),
                    'paid_at' => now(),
                ]);

                // Update reservation status
                $lockedRes->update([
                    'status' => 'confirmed',
                ]);

                // Fire notification
                $notificationService->sendPaymentSuccess($lockedRes);
            });

            $this->info("✅ Berhasil! Status pembayaran reservasi #{$reservationId} telah diubah menjadi 'paid' dan status reservasi menjadi 'confirmed'.");
            $this->info("📧 Email notifikasi pembayaran sukses telah dikirimkan ke customer.");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error("Gagal mensimulasikan pembayaran: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
