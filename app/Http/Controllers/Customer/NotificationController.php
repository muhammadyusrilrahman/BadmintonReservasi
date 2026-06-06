<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Show notifications index page.
     */
    public function index(Request $request): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(15);

        return view('customer.notifications.index', [
            'title'         => 'Notifikasi Saya',
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai telah dibaca.',
            ]);
        }

        // Redirect to target URL if provided
        $url = $notification->data['url'] ?? route('customer.dashboard');
        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse|RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai telah dibaca.',
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
