<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Refund;
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
     * Display all refund requests in Admin dashboard.
     */
    public function index(Request $request): View
    {
        $refunds = Refund::with(['reservation.court', 'user'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Calculate simple stats
        $stats = [
            'pending'   => Refund::where('status', Refund::STATUS_REQUESTED)->count(),
            'approved'  => Refund::where('status', Refund::STATUS_APPROVED)->count(),
            'rejected'  => Refund::where('status', Refund::STATUS_REJECTED)->count(),
            'completed' => Refund::where('status', Refund::STATUS_COMPLETED)->count(),
            'total_amount' => Refund::where('status', Refund::STATUS_COMPLETED)->sum('amount'),
        ];

        return view('admin.refunds.index', [
            'title'   => 'Kelola Pengajuan Refund',
            'refunds' => $refunds,
            'stats'   => $stats,
        ]);
    }

    /**
     * Show refund request details.
     */
    public function show(Refund $refund): View
    {
        $refund->load(['reservation.court', 'reservation.payment', 'user', 'processedBy']);

        // Load logs of the reservation
        $statusLogs = $refund->reservation->statusLogs()->with('user')->latest()->get();

        return view('admin.refunds.show', [
            'title'      => 'Detail Pengajuan Refund #' . $refund->id,
            'refund'     => $refund,
            'statusLogs' => $statusLogs,
        ]);
    }

    /**
     * Approve refund.
     */
    public function approve(Request $request, Refund $refund): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [], [
            'admin_notes' => 'catatan admin',
        ]);

        try {
            $this->refundService->approveRefund($refund, $request->admin_notes, auth()->id());
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', 'Pengajuan refund berhasil disetujui! Status reservasi kini Dibatalkan.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Reject refund.
     */
    public function reject(Request $request, Refund $refund): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [], [
            'admin_notes' => 'catatan alasan penolakan',
        ]);

        try {
            $this->refundService->rejectRefund($refund, $request->admin_notes, auth()->id());
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', 'Pengajuan refund berhasil ditolak. Reservasi tetap aktif.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Complete refund (mark as transferred).
     */
    public function complete(Request $request, Refund $refund): RedirectResponse
    {
        try {
            $this->refundService->completeRefund($refund, auth()->id());
            return redirect()
                ->route('admin.refunds.show', $refund)
                ->with('success', 'Refund telah diselesaikan. Dana berhasil dikirim ke rekening customer.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }
}
