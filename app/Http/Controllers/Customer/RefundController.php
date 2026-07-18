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

    public function index(): View
    {
        $refunds = Refund::where('user_id', auth()->id())
            ->with(['reservation.court'])
            ->latest()
            ->get()
            ->groupBy(function($refund) {
                // Kelompokkan berdasarkan waktu pengajuan agar multi-slot tampil 1 riwayat
                return $refund->created_at->format('Y-m-d H:i:s');
            });

        return view('customer.refunds.index', [
            'title'   => 'Daftar Refund Saya',
            'groupedRefunds' => $refunds,
        ]);
    }

    /**
     * Display refund request page.
     */
    public function create(Reservation $reservation): View|\Illuminate\Http\RedirectResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        // Cek minimal 1 slot bisa direfund
        $canRefundAny = false;
        $sessionReservations = collect([$reservation]);

        if ($reservation->booking_session_id) {
            $sessionReservations = Reservation::where('booking_session_id', $reservation->booking_session_id)
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        }

        foreach ($sessionReservations as $slot) {
            if ($slot->canRequestRefund()) {
                $canRefundAny = true;
                break;
            }
        }

        if (!$canRefundAny) {
            return back()->withError('Reservasi ini sudah tidak dapat di-refund sesuai kebijakan.');
        }

        $sessionData = $sessionReservations->map(function($r) {
            return [
                'id' => $r->id,
                'total_price' => $r->total_price,
            ];
        })->keyBy('id');

        return view('customer.refunds.create', [
            'title'               => 'Ajukan Refund',
            'reservation'         => $reservation,
            'sessionReservations' => $sessionReservations,
            'sessionData'         => $sessionData,
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
            'reservation_ids' => ['nullable', 'array'],
            'reservation_ids.*' => ['integer', 'exists:reservations,id'],
        ], [], [
            'reason'         => 'alasan refund',
            'bank_name'      => 'nama bank',
            'account_number' => 'nomor rekening',
            'account_name'   => 'nama pemilik rekening',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $reservation) {
                $ids = $request->input('reservation_ids', [$reservation->id]);
                
                foreach ($ids as $id) {
                    $targetReservation = Reservation::findOrFail($id);
                    
                    // Pastikan milik user yang sama
                    if ($targetReservation->user_id !== auth()->id()) {
                        continue;
                    }

                    // Hanya proses jika memenuhi syarat
                    if ($targetReservation->canRequestRefund()) {
                        $this->refundService->requestRefund(
                            reservation: $targetReservation,
                            data: $request->only(['reason', 'bank_name', 'account_number', 'account_name']),
                            userId: auth()->id()
                        );
                    }
                }
            });

            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('success', 'Pengajuan refund berhasil dikirim. Menunggu verifikasi admin.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }
}
