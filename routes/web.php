<?php

use App\Http\Controllers\ProfileController;
use App\Models\Review;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page — show to guests, redirect authenticated users to their dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->getDashboardRoute());
    }
    $reviews = Review::with(['user', 'court'])
        ->visible()
        ->latest()
        ->limit(6)
        ->get();
    return view('landing', compact('reviews'));
})->name('landing');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile management (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public payment callback webhook from Midtrans
Route::post('/payment/callback', [\App\Http\Controllers\PaymentCallbackController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('payment.callback');

// Role-specific route files
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/customer.php';
require __DIR__.'/kasir.php';
require __DIR__.'/staff.php';
