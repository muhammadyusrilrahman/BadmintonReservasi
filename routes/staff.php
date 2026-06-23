<?php

use App\Http\Controllers\Staff\CheckInController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\ScheduleController;
use App\Http\Controllers\Staff\MaintenanceController;
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

        // Schedule & Maintenance
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
        Route::patch('/maintenance/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');
    });
