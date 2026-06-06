<?php

namespace App\Services;

use App\Models\Refund;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RefundService extends BaseService
{
    public function __construct(
        ReservationRepositoryInterface $repository,
        private readonly \App\Services\NotificationService $notificationService
    ) {
        parent::__construct($repository);
    }

    /**
     * Customer requests a refund.
     */
    public function requestRefund(Reservation $reservation, array $data, ?int $userId = null): Refund
    {
        return DB::transaction(function () use ($reservation, $data, $userId) {
            // Lock reservation
            $reservation = Reservation::lockForUpdate()->findOrFail($reservation->id);

            // Validate eligibility
            if (!$reservation->canRequestRefund()) {
                throw new InvalidArgumentException("Reservasi ini tidak memenuhi syarat untuk diajukan refund.");
            }

            $requesterId = $userId ?? auth()->id();

            // Create refund request
            $refund = Refund::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $requesterId,
                'amount'         => $reservation->total_price,
                'reason'         => $data['reason'],
                'bank_name'      => $data['bank_name'],
                'account_number' => $data['account_number'],
                'account_name'   => $data['account_name'],
                'status'         => Refund::STATUS_REQUESTED,
            ]);

            // Create status log
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $requesterId,
                'old_status'     => $reservation->status,
                'new_status'     => $reservation->status, // remains confirmed while requested
                'change_type'    => 'refund_requested',
                'description'    => "Mengajukan refund sebesar " . $refund->formatted_amount . " ke rekening {$refund->bank_name} ({$refund->account_number} a.n {$refund->account_name}) dengan alasan: {$refund->reason}",
                'payload'        => [
                    'refund_id'      => $refund->id,
                    'amount'         => $refund->amount,
                    'bank_name'      => $refund->bank_name,
                    'account_number' => $refund->account_number,
                    'account_name'   => $refund->account_name,
                ]
            ]);

            return $refund;
        });
    }

    /**
     * Admin approves a refund request.
     * This cancels the reservation and marks payment as refunded.
     */
    public function approveRefund(Refund $refund, string $adminNotes, int $adminId): Refund
    {
        return DB::transaction(function () use ($refund, $adminNotes, $adminId) {
            $refund = Refund::lockForUpdate()->findOrFail($refund->id);

            if ($refund->status !== Refund::STATUS_REQUESTED) {
                throw new InvalidArgumentException("Hanya pengajuan refund berstatus 'Diajukan' (requested) yang dapat disetujui.");
            }

            $reservation = $refund->reservation;

            // Update refund status
            $refund->update([
                'status'       => Refund::STATUS_APPROVED,
                'admin_notes'  => $adminNotes,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);

            $oldStatus = $reservation->status;

            // Update reservation status to cancelled
            $reservation->update([
                'status' => 'cancelled',
            ]);

            // Update payment status to refunded
            if ($reservation->payment) {
                $reservation->payment->update([
                    'status' => 'refunded',
                ]);
            }

            // Create status log
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $adminId,
                'old_status'     => $oldStatus,
                'new_status'     => 'cancelled',
                'change_type'    => 'refund_approved',
                'description'    => "Pengajuan refund disetujui oleh Admin. Reservasi dibatalkan dan dana dikembalikan. Catatan admin: {$adminNotes}",
                'payload'        => [
                    'refund_id'   => $refund->id,
                    'admin_notes' => $adminNotes,
                ]
            ]);

            $this->notificationService->sendRefundApproved($reservation);

            return $refund;
        });
    }

    /**
     * Admin rejects a refund request.
     * Reservation remains confirmed (active).
     */
    public function rejectRefund(Refund $refund, string $adminNotes, int $adminId): Refund
    {
        return DB::transaction(function () use ($refund, $adminNotes, $adminId) {
            $refund = Refund::lockForUpdate()->findOrFail($refund->id);

            if ($refund->status !== Refund::STATUS_REQUESTED) {
                throw new InvalidArgumentException("Hanya pengajuan refund berstatus 'Diajukan' (requested) yang dapat ditolak.");
            }

            $reservation = $refund->reservation;

            // Update refund status
            $refund->update([
                'status'       => Refund::STATUS_REJECTED,
                'admin_notes'  => $adminNotes,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);

            // Create status log
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $adminId,
                'old_status'     => $reservation->status,
                'new_status'     => $reservation->status, // stays confirmed
                'change_type'    => 'refund_rejected',
                'description'    => "Pengajuan refund ditolak oleh Admin. Reservasi tetap aktif. Alasan penolakan: {$adminNotes}",
                'payload'        => [
                    'refund_id'   => $refund->id,
                    'admin_notes' => $adminNotes,
                ]
            ]);

            return $refund;
        });
    }

    /**
     * Admin marks approved refund as completed (funds transferred).
     */
    public function completeRefund(Refund $refund, int $adminId): Refund
    {
        return DB::transaction(function () use ($refund, $adminId) {
            $refund = Refund::lockForUpdate()->findOrFail($refund->id);

            if ($refund->status !== Refund::STATUS_APPROVED) {
                throw new InvalidArgumentException("Hanya refund berstatus 'Disetujui' (approved) yang dapat diselesaikan.");
            }

            $reservation = $refund->reservation;

            // Update refund status
            $refund->update([
                'status'       => Refund::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // Create status log
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'user_id'        => $adminId,
                'old_status'     => $reservation->status,
                'new_status'     => $reservation->status, // stays cancelled
                'change_type'    => 'refund_completed',
                'description'    => "Proses transfer dana refund sebesar " . $refund->formatted_amount . " ke rekening customer telah diselesaikan.",
                'payload'        => [
                    'refund_id'    => $refund->id,
                    'completed_at' => $refund->completed_at->toDateTimeString(),
                ]
            ]);

            return $refund;
        });
    }
}
