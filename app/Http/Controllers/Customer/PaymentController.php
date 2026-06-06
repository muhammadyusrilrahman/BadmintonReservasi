<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Reservation;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends BaseController
{
    public function __construct(private readonly MidtransService $midtransService)
    {
    }

    /**
     * Get or generate Midtrans Snap Token for a Reservation.
     *
     * @param Reservation $reservation
     * @return JsonResponse
     */
    public function getSnapToken(Reservation $reservation): JsonResponse
    {
        // 1. Ensure the customer owns the reservation
        if ($reservation->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki otorisasi untuk melakukan tindakan ini.'
            ], 403);
        }

        // 2. Ensure payment exists
        $payment = $reservation->payment;
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan.'
            ], 404);
        }

        // 3. Ensure payment is pending
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran untuk reservasi ini sudah selesai atau dibatalkan.',
                'status' => $payment->status
            ], 400);
        }

        try {
            // 4. Return cached snap token if it exists, otherwise generate a new one
            $snapToken = $payment->snap_token;
            if (!$snapToken) {
                $snapToken = DB::transaction(fn() => $this->midtransService->generateSnapToken($payment));
            }

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'client_key' => config('services.midtrans.client_key')
            ]);
        } catch (Exception $e) {
            Log::error('Snap token generation failed', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.'
            ], 500);
        }
    }
}
