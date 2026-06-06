<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    /**
     * Display the kasir dashboard.
     */
    public function index()
    {
        $today = Carbon::today();

        // Today's income (paid payments for today's reservations)
        $todayIncome = Payment::where('status', 'paid')
            ->whereHas('reservation', fn($q) => $q->whereDate('date', $today))
            ->sum('amount');

        // Today's transactions count
        $todayTransactions = Payment::where('status', 'paid')
            ->whereHas('reservation', fn($q) => $q->whereDate('date', $today))
            ->count();

        // Pending payments count
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Today's reservations with details
        $todayReservations = Reservation::with(['user', 'court', 'payment'])
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();

        return view('kasir.dashboard', [
            'title'              => 'Dashboard Kasir',
            'todayIncome'        => $todayIncome,
            'todayTransactions'  => $todayTransactions,
            'pendingPayments'    => $pendingPayments,
            'todayReservations'  => $todayReservations,
        ]);
    }
}
