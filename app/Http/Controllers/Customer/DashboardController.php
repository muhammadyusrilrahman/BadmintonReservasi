<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Reservation;

class DashboardController extends BaseController
{
    /**
     * Display the customer dashboard.
     */
    public function index()
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

        return view('customer.dashboard', [
            'title'                => 'Dashboard',
            'upcomingReservations' => $upcomingReservations,
            'totalReservations'    => $totalReservations,
            'pendingCount'         => $pendingCount,
        ]);
    }
}
