<?php

namespace App\Services;

use App\Models\Reservation;
use App\Notifications\BadmintonNotification;

class NotificationService
{
    /**
     * Send notification for booking success.
     */
    public function sendBookingSuccess(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'booking_success',
                'Pemesanan Berhasil! 🏸',
                "Reservasi Anda untuk kode booking " . ($reservation->booking_code ?? "#{$reservation->id}") . " telah dibuat. Silakan selesaikan pembayaran sebelum waktu habis.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }

    /**
     * Send notification for payment success.
     */
    public function sendPaymentSuccess(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'payment_success',
                'Pembayaran Berhasil! ✅',
                "Pembayaran untuk reservasi " . ($reservation->booking_code ?? "#{$reservation->id}") . " telah diverifikasi dan lunas.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }

    /**
     * Send notification for payment failed.
     */
    public function sendPaymentFailed(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'payment_failed',
                'Pembayaran Gagal/Kedaluwarsa ❌',
                "Pembayaran untuk reservasi " . ($reservation->booking_code ?? "#{$reservation->id}") . " gagal atau batas waktu pembayaran telah habis.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }

    /**
     * Send notification for refund approved.
     */
    public function sendRefundApproved(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'refund_approved',
                'Pengajuan Refund Disetujui 💰',
                "Pengajuan refund untuk reservasi " . ($reservation->booking_code ?? "#{$reservation->id}") . " telah disetujui oleh admin.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }

    /**
     * Send notification for reschedule approved.
     */
    public function sendRescheduleApproved(Reservation $reservation): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'reschedule_approved',
                'Reschedule Disetujui 🕒',
                "Reschedule jadwal untuk reservasi " . ($reservation->booking_code ?? "#{$reservation->id}") . " telah berhasil diperbarui.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }

    /**
     * Send notification for schedule changed.
     */
    public function sendScheduleChanged(Reservation $reservation, string $oldSchedule, string $newSchedule): void
    {
        $user = $reservation->user;
        if ($user) {
            $user->notify(new BadmintonNotification(
                'schedule_changed',
                'Jadwal Reservasi Berubah ⚠️',
                "Jadwal reservasi " . ($reservation->booking_code ?? "#{$reservation->id}") . " telah diubah dari {$oldSchedule} menjadi {$newSchedule} oleh staf.",
                route('customer.reservations.show', $reservation),
                $reservation->id
            ));
        }
    }
}
