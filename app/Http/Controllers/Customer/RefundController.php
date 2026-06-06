<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Refund;
use App\Models\Reservation;
use App\Services\RefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class RefundController extends BaseController
{
    public function __construct(private readonly RefundService $refundService)
    {
    }

    /**
     * Display customer refund history.
     */
    public function index(): View
    {
        $refunds = Refund::where('user_id', auth()->id())
            ->with(['reservation.court'])
            ->latest()
            ->paginate(10);

        return view('customer.refunds.index', [
            'title'   => 'Daftar Refund Saya',
            'refunds' => $refunds,
        ]);
    }

    /**
     * Submit refund request.
     */
    public function request(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reason'         => ['required', 'string', 'min:10', 'max:500'],
            'bank_name'      => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name'   => ['required', 'string', 'max:100'],
        ], [], [
            'reason'         => 'alasan refund',
            'bank_name'      => 'nama bank',
            'account_number' => 'nomor rekening',
            'account_name'   => 'nama pemilik rekening',
        ]);

        try {
            $this->refundService->requestRefund(
                reservation: $reservation,
                data: $request->only(['reason', 'bank_name', 'account_number', 'account_name']),
                userId: auth()->id()
            );

            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('success', 'Pengajuan refund berhasil dikirim. Menunggu verifikasi admin.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }
}
