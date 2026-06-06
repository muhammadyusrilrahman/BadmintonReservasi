<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Show activity logs dashboard.
     */
    public function index(Request $request): View
    {
        $query = UserActivityLog::with('user');

        // Search text filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Method filter
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Calculate statistics for dashboard cards
        $todayCount = UserActivityLog::whereDate('created_at', now()->toDateString())->count();
        $uniqueUsersToday = UserActivityLog::whereDate('created_at', now()->toDateString())
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        $crudCount = UserActivityLog::whereIn('method', ['POST', 'PUT', 'PATCH', 'DELETE'])
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $authCount = UserActivityLog::whereDate('created_at', now()->toDateString())
            ->where(function ($q) {
                $q->where('activity', 'like', '%login%')
                  ->orWhere('activity', 'like', '%logout%');
            })->count();

        return view('admin.activity_logs', [
            'title'            => 'Sistem Log Aktivitas',
            'logs'             => $logs,
            'todayCount'       => $todayCount,
            'uniqueUsersToday' => $uniqueUsersToday,
            'crudCount'        => $crudCount,
            'authCount'        => $authCount,
        ]);
    }
}
