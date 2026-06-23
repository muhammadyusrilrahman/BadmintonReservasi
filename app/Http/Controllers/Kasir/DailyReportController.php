<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyReportController extends BaseController
{
    /**
     * Display the daily report for a specific date (defaults to today).
     */
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $parsedDate = Carbon::parse($date);

        // Get paid transactions on this date
        $transactions = Payment::with(['reservation', 'reservation.user'])
            ->where('status', 'paid')
            ->whereDate('paid_at', $parsedDate)
            ->orderBy('paid_at', 'desc')
            ->get();

        // Calculate metrics
        $totalRevenue = $transactions->sum('amount');
        $totalPaidTransactions = $transactions->count();

        // Group revenue by payment method
        $revenueByMethod = $transactions->groupBy('payment_method')->map(function ($group) {
            return $group->sum('amount');
        });

        return view('kasir.daily-report.index', [
            'title'                 => 'Laporan Harian Kasir',
            'date'                  => $parsedDate,
            'transactions'          => $transactions,
            'totalRevenue'          => $totalRevenue,
            'totalPaidTransactions' => $totalPaidTransactions,
            'revenueByMethod'       => $revenueByMethod,
        ]);
    }
}
