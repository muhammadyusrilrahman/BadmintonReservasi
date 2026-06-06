<?php

use App\Http\Controllers\Kasir\DashboardController;
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

        // Future routes:
        // Route::resource('transactions', TransactionController::class);
        // Route::get('/today', [TodayReservationController::class, 'index'])->name('today.index');
        // Route::get('/daily-report', [DailyReportController::class, 'index'])->name('daily-report.index');
    });
