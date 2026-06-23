<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
| Routes for customer role. Prefix: /customer, Name: customer.*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Booking
        Route::get('/booking', [ReservationController::class, 'create'])->name('booking.create');
        Route::get('/booking/slots', [ReservationController::class, 'getAvailableSlots'])->name('booking.slots');
        Route::post('/booking', [ReservationController::class, 'store'])->name('booking.store');
        Route::post('/booking/apply-promo', [ReservationController::class, 'applyPromo'])->name('booking.apply-promo');

        // Reservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/proof', [ReservationController::class, 'uploadProof'])->name('reservations.upload-proof');
        Route::post('/reservations/{reservation}/snap-token', [\App\Http\Controllers\Customer\PaymentController::class, 'getSnapToken'])->name('reservations.snap-token');

        // Reschedule
        Route::get('/reservations/{reservation}/reschedule', [\App\Http\Controllers\Customer\RescheduleController::class, 'show'])->name('reservations.reschedule');
        Route::post('/reservations/{reservation}/reschedule', [\App\Http\Controllers\Customer\RescheduleController::class, 'process'])->name('reservations.reschedule.process');

        // Refund
        Route::post('/reservations/{reservation}/refund', [\App\Http\Controllers\Customer\RefundController::class, 'request'])->name('reservations.refund.request');
        Route::get('/refunds', [\App\Http\Controllers\Customer\RefundController::class, 'index'])->name('refunds.index');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Customer\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Customer\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
