<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends BaseController
{
    /**
     * Display a listing of all transactions/payments.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Payment::with(['reservation', 'reservation.user', 'reservation.court'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('reservation', function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('kasir.transactions.index', [
            'title'        => 'Daftar Transaksi',
            'transactions' => $transactions,
            'status'       => $status,
            'search'       => $search,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
        ]);
    }
}
