<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtransService,
        private readonly \App\Services\NotificationService $notificationService
    ) {
    }

    /**
     * Handle incoming Midtrans Webhook Notification.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::debug('Midtrans Webhook Received', [
            'order_id' => $payload['order_id'] ?? 'unknown',
            'transaction_status' => $payload['transaction_status'] ?? 'unknown',
            'payment_type' => $payload['payment_type'] ?? 'unknown',
        ]);

        // 1. Validate Webhook Signature
        if (!$this->midtransService->validateSignature($payload)) {
            Log::warning('Midtrans Webhook Signature Invalid', [
                'order_id' => $payload['order_id'] ?? 'unknown',
                'signature_received' => $payload['signature_key'] ?? 'none'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Signature key tidak valid.'
            ], 403);
        }

        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? '';
        $transactionId = $payload['transaction_id'] ?? '';

        // 2. Parse Reservation & Payment ID from order_id, format: RSV-{reservation_id}-PMT-{payment_id}-{timestamp}
        if (!preg_match('/RSV-(\d+)-PMT-(\d+)/', $orderId, $matches)) {
            Log::error('Midtrans Webhook: Gagal parse Order ID format', ['order_id' => $orderId]);
            return response()->json([
                'success' => false,
                'message' => 'Format Order ID tidak dapat dikenali.'
            ], 400);
        }

        $reservationId = (int) $matches[1];
        $paymentId = (int) $matches[2];

        try {
            // 3. Process Status Updates safely using DB Transaction & Pessimistic Locking
            DB::transaction(function () use ($paymentId, $reservationId, $transactionStatus, $fraudStatus, $paymentType, $transactionId) {
                // Lock payment row for update
                $payment = Payment::where('id', $paymentId)->lockForUpdate()->first();
                if (!$payment) {
                    throw new Exception("Pembayaran #{$paymentId} tidak ditemukan.");
                }

                // Lock reservation row for update
                $reservation = Reservation::where('id', $reservationId)->lockForUpdate()->first();
                if (!$reservation) {
                    throw new Exception("Reservasi #{$reservationId} tidak ditemukan.");
                }

                // Validate payment belongs to the parsed reservation
                if ($payment->reservation_id !== $reservationId) {
                    throw new Exception("Payment #{$paymentId} tidak terkait dengan Reservasi #{$reservationId}.");
                }

                // Skip processing if payment is already in a terminal state (idempotency guard)
                if (in_array($payment->status, ['paid', 'failed', 'refunded'])) {
                    Log::info("Midtrans Webhook: Pembayaran #{$paymentId} sudah berstatus '{$payment->status}', abaikan duplikat.");
                    return;
                }

                $paymentStatus = 'pending';
                $reservationStatus = 'pending';
                $isPaid = false;

                // Map Midtrans transaction status
                if ($transactionStatus === 'capture') {
                    if ($paymentType === 'credit_card') {
                        if ($fraudStatus === 'accept') {
                            $paymentStatus = 'paid';
                            $reservationStatus = 'confirmed';
                            $isPaid = true;
                        } else {
                            $paymentStatus = 'failed';
                            $reservationStatus = 'cancelled';
                        }
                    }
                } elseif ($transactionStatus === 'settlement') {
                    $paymentStatus = 'paid';
                    $reservationStatus = 'confirmed';
                    $isPaid = true;
                } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                    $paymentStatus = 'failed';
                    $reservationStatus = 'cancelled';
                } elseif ($transactionStatus === 'pending') {
                    $paymentStatus = 'pending';
                    $reservationStatus = 'pending';
                }

                // Update Payment attributes
                $paymentUpdateData = [
                    'status' => $paymentStatus,
                    'payment_type' => $paymentType,
                    'midtrans_transaction_id' => $transactionId,
                ];

                if ($isPaid) {
                    $paymentUpdateData['paid_at'] = now();
                }

                $payment->update($paymentUpdateData);

                // Update Reservation attributes
                $reservation->update([
                    'status' => $reservationStatus,
                ]);

                // Send Notification
                if ($paymentStatus === 'paid') {
                    $this->notificationService->sendPaymentSuccess($reservation);
                } elseif ($paymentStatus === 'failed') {
                    $this->notificationService->sendPaymentFailed($reservation);
                }

                Log::info("Midtrans Webhook: Berhasil sinkronisasi status pembayaran #{$paymentId} menjadi {$paymentStatus} dan reservasi #{$reservationId} menjadi {$reservationStatus}.");
            });

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui.'
            ]);
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Processing Exception: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal saat memproses notifikasi.'
            ], 500);
        }
    }
}
