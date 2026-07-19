<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display the kasir dashboard.
     */
    public function index(Request $request)
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

        // ── Schedule View ──────────────────────────────────────────
        $rawDate = $request->input('schedule_date');
        if (is_array($rawDate)) {
            $rawDate = $rawDate[0] ?? null;
        }

        $scheduleDate = ($rawDate && is_string($rawDate))
            ? Carbon::parse($rawDate)->startOfDay()
            : today();

        $scheduleDateStr = $scheduleDate->toDateString();

        $operationalHours = range(6, 21);

        $courts = Court::where('is_active', true)
            ->with([
                'reservations' => function ($q) use ($scheduleDateStr) {
                    $q->whereDate('date', $scheduleDateStr)
                      ->whereNotIn('status', ['cancelled'])
                      ->with('user:id,name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('kasir.dashboard', [
            'title'              => 'Dashboard Kasir',
            'todayIncome'        => $todayIncome,
            'todayTransactions'  => $todayTransactions,
            'pendingPayments'    => $pendingPayments,
            'todayReservations'  => $todayReservations,
            'courts'             => $courts,
            'scheduleDate'       => $scheduleDate,
            'operationalHours'   => $operationalHours,
        ]);
    }
}
