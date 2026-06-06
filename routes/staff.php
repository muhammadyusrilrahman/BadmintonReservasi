<?php

use App\Http\Controllers\Staff\CheckInController;
use App\Http\Controllers\Staff\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
| Routes for staff role. Prefix: /staff, Name: staff.*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Check-in Management
        Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
        Route::get('/checkin/search', [CheckInController::class, 'search'])->name('checkin.search');
        Route::get('/checkin/verify/{bookingCode}', [CheckInController::class, 'verify'])->name('checkin.verify');
        Route::post('/checkin/process/{reservation}', [CheckInController::class, 'process'])->name('checkin.process');
        Route::get('/checkin/history', [CheckInController::class, 'history'])->name('checkin.history');

        // Future routes:
        // Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        // Route::resource('maintenance', MaintenanceController::class);
    });
