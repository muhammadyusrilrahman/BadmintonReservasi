<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MidtransService
{
    /**
     * Get Midtrans Snap Base URL.
     */
    protected function getBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Generate Midtrans Snap Token for a Payment.
     *
     * @param Payment $payment
     * @return string
     * @throws Exception
     */
    public function generateSnapToken(Payment $payment): string
    {
        // Format unique order ID for Midtrans, e.g. RSV-1-PMT-1-1716187200
        // Adding timestamp ensures uniqueness in Sandbox if bookings are deleted/reset
        $orderId = 'RSV-' . $payment->reservation_id . '-PMT-' . $payment->id . '-' . time();

        $user = $payment->reservation->user;
        $courtName = $payment->reservation->court->name;
        $dateFormatted = $payment->reservation->date->format('d-m-Y');
        $startTime = substr($payment->reservation->start_time, 0, 5);
        $endTime = substr($payment->reservation->end_time, 0, 5);

        // Midtrans Snap API Payload
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $payment->amount,
            ],
            'item_details' => [
                [
                    'id' => 'court-' . $payment->reservation->court_id,
                    'price' => (int) $payment->amount,
                    'quantity' => 1,
                    'name' => "Booking {$courtName} ({$dateFormatted} {$startTime}-{$endTime})",
                ]
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
            'expiry' => [
                // Set checkout expiration to match automatic cancellation logic
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => config('reservation.expiry_minutes'),
            ],
        ];

        try {
            $response = Http::withBasicAuth(config('services.midtrans.server_key'), '')
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->getBaseUrl()}/transactions", $payload);

            if ($response->failed()) {
                Log::error('Midtrans Snap Generation Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payment_id' => $payment->id,
                ]);
                throw new Exception('Gagal menghubungi server pembayaran Midtrans: ' . ($response->json('error_messages')[0] ?? $response->body()));
            }

            $snapToken = $response->json('token');
            if (!$snapToken) {
                throw new Exception('Token pembayaran tidak ditemukan di respon Midtrans.');
            }

            // Save the generated snap token to the payment record
            $payment->update([
                'snap_token' => $snapToken
            ]);

            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);
            throw $e;
        }
    }

    /**
     * Validate Webhook Notification Signature Key from Midtrans.
     *
     * @param array $payload
     * @return bool
     */
    public function validateSignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key');
        $receivedSignature = $payload['signature_key'] ?? '';

        if (!$orderId || !$statusCode || !$grossAmount || !$receivedSignature) {
            return false;
        }

        // Midtrans signature formula: SHA512(order_id + status_code + gross_amount + ServerKey)
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($calculatedSignature, $receivedSignature);
    }
}
