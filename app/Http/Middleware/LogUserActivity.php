<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Execute the request first so we only log successful/handled requests and can capture correct state
        $response = $next($request);

        // Only log state-changing requests (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logActivity($request);
        }

        return $response;
    }

    /**
     * Log the user activity.
     */
    private function logActivity(Request $request): void
    {
        $routeName = $request->route() ? $request->route()->getName() : null;
        $url = $request->fullUrl();
        $method = $request->method();
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $user = auth()->user();

        // Map key routes to friendly, readable descriptions
        $routeMap = [
            'customer.booking.store' => 'Membuat reservasi booking baru',
            'customer.reservations.upload-proof' => 'Mengunggah bukti pembayaran transfer',
            'customer.reservations.snap-token' => 'Meminta token pembayaran Midtrans Snap',
            'customer.reservations.reschedule.process' => 'Melakukan reschedule reservasi',
            'customer.reservations.refund.request' => 'Mengajukan refund reservasi',
            
            'admin.courts.store' => 'Menambahkan lapangan baru',
            'admin.courts.update' => 'Memperbarui data lapangan',
            'admin.courts.destroy' => 'Menghapus lapangan',
            'admin.courts.toggle-active' => 'Mengubah status aktif/nonaktif lapangan',
            'admin.courts.schedules.store' => 'Menambahkan jadwal lapangan',
            'admin.courts.schedules.destroy' => 'Menghapus jadwal lapangan',
            'admin.courts.schedules.destroy-bulk' => 'Menghapus banyak jadwal lapangan secara massal',
            'admin.users.store' => 'Menambahkan user baru',
            'admin.users.update' => 'Memperbarui data user',
            'admin.users.destroy' => 'Menghapus user',
            
            'admin.reservations.store' => 'Membuat reservasi baru oleh Admin',
            'admin.reservations.verify-payment' => 'Memverifikasi status pembayaran reservasi',
            'admin.reservations.cancel' => 'Membatalkan reservasi oleh Admin',
            
            'admin.refunds.approve' => 'Menyetujui pengajuan refund reservasi',
            'admin.refunds.reject' => 'Menolak pengajuan refund reservasi',
            'admin.refunds.complete' => 'Menyelesaikan proses transfer refund',
            
            'staff.checkin.process' => 'Memproses check-in customer',
            'payment.callback' => 'Menerima callback status pembayaran dari Midtrans',
        ];

        $activity = $routeMap[$routeName] ?? null;

        // Fallback for other state-changing routes
        if (!$activity) {
            $path = $request->path();
            $activity = match ($method) {
                'POST' => "Membuat data baru di path: {$path}",
                'PUT', 'PATCH' => "Memperbarui data di path: {$path}",
                'DELETE' => "Menghapus data di path: {$path}",
                default => "Melakukan aksi {$method} di path: {$path}",
            };
        }

        // Clean properties to exclude sensitive parameters
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'payment_proof',
            'snap_token',
            '_token',
            '_method',
        ];
        $properties = $request->except($sensitiveKeys);

        // Save log
        UserActivityLog::create([
            'user_id'     => $user ? $user->id : null,
            'activity'    => $activity,
            'method'      => $method,
            'url'         => $url,
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
            'properties'  => count($properties) > 0 ? $properties : null,
        ]);
    }
}
