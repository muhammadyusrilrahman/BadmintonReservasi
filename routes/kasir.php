<?php

use App\Http\Controllers\Kasir\DashboardController;
use App\Http\Controllers\Kasir\TransactionController;
use App\Http\Controllers\Kasir\TodayReservationController;
use App\Http\Controllers\Kasir\DailyReportController;
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

        // Kelola Promo (read-only)
        Route::get('/promos', [\App\Http\Controllers\Admin\PromoCodeController::class, 'index'])->name('promos.index');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // Today's Reservations
        Route::get('/today', [TodayReservationController::class, 'index'])->name('today.index');
        
        // Daily Report
        Route::get('/daily-report', [DailyReportController::class, 'index'])->name('daily-report.index');
    });
