<?php

use App\Http\Controllers\Account\AccountDashboardController;
use App\Http\Controllers\Account\OrderHistoryController;
use App\Http\Controllers\Account\SubscriptionController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\SubscriptionActionController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('throttle:5,15')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'requestOtp'])->name('login.request');
    Route::get('/verify', [LoginController::class, 'showVerifyForm'])->name('verify');
    Route::post('/verify', [LoginController::class, 'verifyOtp'])->name('verify.submit');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('throttle:10,1');

Route::middleware(['auth:portal'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderHistoryController::class, 'show'])->name('orders.show');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
});

Route::middleware(['auth:portal'])->prefix('api')->group(function () {
    Route::middleware('subscription.owner')->group(function () {
        Route::post('/subscriptions/{id}/next-charge-date', [SubscriptionActionController::class, 'updateNextChargeDate'])->name('api.subscriptions.next-charge-date');
        Route::post('/subscriptions/{id}/cancel', [SubscriptionActionController::class, 'cancel'])->name('api.subscriptions.cancel');
        Route::post('/subscriptions/{id}/pause', [SubscriptionActionController::class, 'pause'])->name('api.subscriptions.pause');
        Route::post('/subscriptions/{id}/resume', [SubscriptionActionController::class, 'resume'])->name('api.subscriptions.resume');
        Route::post('/subscriptions/{id}/swap', [SubscriptionActionController::class, 'swap'])->name('api.subscriptions.swap');
        Route::post('/subscriptions/{id}/quantity', [SubscriptionActionController::class, 'updateQuantity'])->name('api.subscriptions.quantity');
    });
    Route::put('/addresses/{id}', [AddressController::class, 'update'])->name('api.addresses.update');
});
