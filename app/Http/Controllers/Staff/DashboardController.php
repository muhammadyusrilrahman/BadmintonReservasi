<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display the staff dashboard.
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Active courts count
        $activeCourts = Court::where('is_active', true)->count();

        // Courts needing maintenance (inactive)
        $maintenancePending = Court::where('is_active', false)->count();

        // Today's schedule count (reservations)
        $todaySchedule = Reservation::whereDate('date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

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

        return view('staff.dashboard', [
            'title'              => 'Dashboard Staff',
            'activeCourts'       => $activeCourts,
            'maintenancePending' => $maintenancePending,
            'todaySchedule'      => $todaySchedule,
            'courts'             => $courts,
            'scheduleDate'       => $scheduleDate,
            'operationalHours'   => $operationalHours,
        ]);
    }
}
