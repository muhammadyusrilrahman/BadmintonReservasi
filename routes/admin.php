<?php

use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Routes for admin role. Prefix: /admin, Name: admin.*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelola Lapangan
        Route::resource('courts', CourtController::class);
        Route::post('courts/{court}/toggle-active', [CourtController::class, 'toggleActive'])->name('courts.toggle-active');
        
        // Kelola Jadwal Lapangan
        Route::get('courts/{court}/schedules', [\App\Http\Controllers\Admin\CourtScheduleController::class, 'index'])->name('courts.schedules.index');
        Route::post('courts/{court}/schedules', [\App\Http\Controllers\Admin\CourtScheduleController::class, 'store'])->name('courts.schedules.store');
        Route::delete('courts/{court}/schedules/bulk', [\App\Http\Controllers\Admin\CourtScheduleController::class, 'destroyBulk'])->name('courts.schedules.destroy-bulk');
        Route::delete('courts/{court}/schedules/{schedule}', [\App\Http\Controllers\Admin\CourtScheduleController::class, 'destroy'])->name('courts.schedules.destroy');

        // Kelola Pengguna
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Kelola Reservasi & Pembayaran
        Route::get('reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/create', [\App\Http\Controllers\Admin\ReservationController::class, 'create'])->name('reservations.create');
        Route::post('reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'store'])->name('reservations.store');
        Route::get('reservations/{reservation}', [\App\Http\Controllers\Admin\ReservationController::class, 'show'])->name('reservations.show');
        Route::post('reservations/{reservation}/verify-payment', [\App\Http\Controllers\Admin\ReservationController::class, 'verifyPayment'])->name('reservations.verify-payment');
        Route::post('reservations/{reservation}/cancel', [\App\Http\Controllers\Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Kelola Refund
        Route::get('refunds', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('refunds.index');
        Route::get('refunds/{refund}', [\App\Http\Controllers\Admin\RefundController::class, 'show'])->name('refunds.show');
        Route::post('refunds/{refund}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('refunds.approve');
        Route::post('refunds/{refund}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('refunds.reject');
        Route::post('refunds/{refund}/complete', [\App\Http\Controllers\Admin\RefundController::class, 'complete'])->name('refunds.complete');

        // Activity Logs
        Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Keuangan (Analisa Grafik & Finansial)
        Route::get('finance', [\App\Http\Controllers\Admin\ReportController::class, 'finance'])->name('finance.index');

        // Laporan (Tabel Data & Export)
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('reports/export/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    });
