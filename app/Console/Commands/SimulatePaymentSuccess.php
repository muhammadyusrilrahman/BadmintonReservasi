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
            // Cari semua PAYMENT yang masih pending
            // Ini lebih reliable daripada cari dari Reservation karena session payment
            // hanya terhubung ke reservasi pertama via reservation_id
            $pendingPayments = Payment::where('status', 'pending')
                ->with(['reservation.user', 'reservation.court'])
                ->orderByDesc('created_at')
                ->get();

            if ($pendingPayments->isEmpty()) {
                $this->error('Tidak ada pembayaran pending yang ditemukan.');
                return Command::FAILURE;
            }

            $choices = $pendingPayments->map(function ($pmt) {
                $res = $pmt->reservation;
                if (!$res) return null;

                $user  = $res->user?->name ?? 'N/A';
                $court = $res->court?->name ?? 'N/A';
                $date  = $res->date->format('Y-m-d');
                $amount = number_format($pmt->amount, 0, ',', '.');

                if ($pmt->booking_session_id) {
                    $slotCount = \App\Models\Reservation::where('booking_session_id', $pmt->booking_session_id)->count();
                    return "[ID: {$res->id}] {$user} - {$court} ({$date}) - {$slotCount} slot - Rp {$amount}";
                }

                return "[ID: {$res->id}] {$user} - {$court} ({$date}) - Rp {$amount}";
            })->filter()->values()->toArray();

            if (empty($choices)) {
                $this->error('Tidak ada reservasi valid yang bisa disimulasikan.');
                return Command::FAILURE;
            }

            $choice = $this->choice('Pilih pembayaran yang ingin disimulasikan berhasil:', $choices);

            // Parse ID from choice string, e.g., "[ID: 15] Name - Court ..."
            preg_match('/\[ID:\s*(\d+)\]/', $choice, $matches);
            $reservationId = (int) $matches[1];
        }

        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            $this->error("Reservasi dengan ID {$reservationId} tidak ditemukan.");
            return Command::FAILURE;
        }

        // Temukan payment: session payment atau direct payment
        $payment = null;
        if ($reservation->booking_session_id) {
            $payment = Payment::where('booking_session_id', $reservation->booking_session_id)->first();
        } else {
            $payment = $reservation->payment;
        }

        if (!$payment) {
            $this->error("Reservasi #{$reservationId} tidak memiliki data pembayaran.");
            return Command::FAILURE;
        }

        if ($payment->status === 'paid') {
            $this->warn("Pembayaran untuk reservasi #{$reservationId} sudah berstatus PAID (Lunas).");
            return Command::SUCCESS;
        }

        // Ambil semua reservasi dalam sesi
        $reservationsToUpdate = $reservation->booking_session_id
            ? Reservation::where('booking_session_id', $reservation->booking_session_id)->get()
            : collect([$reservation]);

        $this->info("Mensimulasikan pembayaran sukses untuk Reservasi #{$reservationId}...");
        if ($reservationsToUpdate->count() > 1) {
            $this->info("Sesi booking ini mencakup {$reservationsToUpdate->count()} slot reservasi.");
        }

        try {
            DB::transaction(function () use ($payment, $reservationsToUpdate, $notificationService) {
                // Lock dan update payment
                $lockedPmt = Payment::where('id', $payment->id)->lockForUpdate()->first();

                $lockedPmt->update([
                    'status'                  => 'paid',
                    'payment_type'            => 'credit_card', // simulated
                    'midtrans_transaction_id' => 'SIM-TX-' . strtoupper(bin2hex(random_bytes(6))),
                    'paid_at'                 => now(),
                ]);

                // Update semua reservasi dalam sesi
                foreach ($reservationsToUpdate as $res) {
                    $lockedRes = Reservation::where('id', $res->id)->lockForUpdate()->first();
                    $lockedRes->update(['status' => 'confirmed']);
                    $notificationService->sendPaymentSuccess($lockedRes);
                }
            });

            $count = $reservationsToUpdate->count();
            $this->info("✅ Berhasil! {$count} reservasi telah dikonfirmasi dengan status 'paid'.");
            $this->info("📧 Email notifikasi pembayaran sukses telah dikirimkan ke customer.");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error("Gagal mensimulasikan pembayaran: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
