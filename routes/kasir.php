<?php

use App\Http\Controllers\Kasir\DashboardController;
use App\Http\Controllers\Kasir\TransactionController;
use App\Http\Controllers\Kasir\TodayReservationController;
use App\Http\Controllers\Kasir\DailyReportController;
use App\Http\Controllers\Kasir\PromoController;
use App\Http\Controllers\Kasir\ReservationController as KasirReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kasir Routes
|--------------------------------------------------------------------------
| Routes for kasir (cashier) role. Prefix: /kasir, Name: kasir.*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelola Promo (kasir bisa lihat & tambah)
        Route::get('/promos', [\App\Http\Controllers\Admin\PromoCodeController::class, 'index'])->name('promos.index');
        Route::get('/promos/create', [PromoController::class, 'create'])->name('promos.create');
        Route::post('/promos', [PromoController::class, 'store'])->name('promos.store');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // Today's Reservations
        Route::get('/today', [TodayReservationController::class, 'index'])->name('today.index');
        
        // Daily Report
        Route::get('/daily-report', [DailyReportController::class, 'index'])->name('daily-report.index');

        // Reservasi Manual (tambah & konfirmasi pembayaran)
        Route::get('/reservations/create', [KasirReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [KasirReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}', [KasirReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/verify-payment', [KasirReservationController::class, 'verifyPayment'])->name('reservations.verify-payment');
    });
