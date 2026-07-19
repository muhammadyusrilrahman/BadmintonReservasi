<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display the customer dashboard.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $upcomingReservations = Reservation::where('user_id', $userId)
            ->with(['court', 'payment'])
            ->upcoming()
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $totalReservations = Reservation::where('user_id', $userId)->count();
        $pendingCount = Reservation::where('user_id', $userId)->where('status', 'pending')->count();

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

        return view('customer.dashboard', [
            'title'                => 'Dashboard',
            'upcomingReservations' => $upcomingReservations,
            'totalReservations'    => $totalReservations,
            'pendingCount'         => $pendingCount,
            'courts'               => $courts,
            'scheduleDate'         => $scheduleDate,
            'operationalHours'     => $operationalHours,
        ]);
    }
}
