<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalCourts = \App\Models\Court::where('is_active', true)->count();
        
        $todayReservations = \App\Models\Reservation::whereDate('date', today())->count();
        
        $totalCustomers = \App\Models\User::role('customer')->count();
        
        $incomeThisMonth = \App\Models\Payment::where('status', 'paid')
            ->whereMonth('created_at', today()->month)
            ->whereYear('created_at', today()->year)
            ->sum('amount');
            
        $recentActivities = \App\Models\Reservation::with(['user', 'court'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'totalCourts' => $totalCourts,
            'todayReservations' => $todayReservations,
            'totalCustomers' => $totalCustomers,
            'incomeThisMonth' => $incomeThisMonth,
            'recentActivities' => $recentActivities,
        ]);
    }
}
