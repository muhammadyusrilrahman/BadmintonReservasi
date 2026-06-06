<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    /**
     * Display the staff dashboard.
     */
    public function index()
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

        // Today's court schedule
        $todayReservations = Reservation::with(['user', 'court'])
            ->whereDate('date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('start_time')
            ->get()
            ->groupBy('court_id');

        // Courts for schedule display
        $courts = Court::where('is_active', true)->orderBy('name')->get();

        return view('staff.dashboard', [
            'title'              => 'Dashboard Staff',
            'activeCourts'       => $activeCourts,
            'maintenancePending' => $maintenancePending,
            'todaySchedule'      => $todaySchedule,
            'todayReservations'  => $todayReservations,
            'courts'             => $courts,
        ]);
    }
}
