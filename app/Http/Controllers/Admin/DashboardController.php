<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        $totalCourts = Court::where('is_active', true)->count();

        $todayReservations = Reservation::whereDate('date', today())->count();

        $totalCustomers = User::role('customer')->count();

        $incomeThisMonth = Payment::where('status', 'paid')
            ->whereMonth('created_at', today()->month)
            ->whereYear('created_at', today()->year)
            ->sum('amount');

        $recentActivities = Reservation::with(['user', 'court'])
            ->latest()
            ->take(5)
            ->get();

        // ── Schedule View ──────────────────────────────────────────
        // Ambil schedule_date; tangani bila dikirim sebagai array oleh browser
        $rawDate = $request->input('schedule_date');
        if (is_array($rawDate)) {
            $rawDate = $rawDate[0] ?? null;
        }

        $scheduleDate = ($rawDate && is_string($rawDate))
            ? Carbon::parse($rawDate)->startOfDay()
            : today();

        $scheduleDateStr = $scheduleDate->toDateString(); // 'YYYY-MM-DD'

        // Jam operasional: 06.00 – 21.00
        $operationalHours = range(6, 21);

        $courts = Court::where('is_active', true)
            ->with([
                'reservations' => function ($q) use ($scheduleDateStr) {
                    $q->whereDate('date', $scheduleDateStr)  // whereDate handles datetime columns
                      ->whereNotIn('status', ['cancelled'])
                      ->with('user:id,name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', [
            'title'            => 'Dashboard Admin',
            'totalCourts'      => $totalCourts,
            'todayReservations' => $todayReservations,
            'totalCustomers'   => $totalCustomers,
            'incomeThisMonth'  => $incomeThisMonth,
            'recentActivities' => $recentActivities,
            'courts'           => $courts,
            'scheduleDate'     => $scheduleDate,
            'operationalHours' => $operationalHours,
        ]);
    }
}
