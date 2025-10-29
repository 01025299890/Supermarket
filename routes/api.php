<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\CustomerPurchases;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::resource('products', ProductController::class)
    ->middleware(['auth:sanctum', 'role:admin'])->except(['index', 'show']);
Route::get('products', [ProductController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('products/{id}', [ProductController::class, 'show'])
    ->middleware('auth:sanctum');

Route::resource('customer-purchases', CustomerPurchases::class)
    ->middleware(['auth:sanctum', 'role:user'])->only(['index']);

// Route::get('/search', [ProductController::class, 'search'])
//     ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum']);

Route::post('rate/{id}', [ProductController::class, 'rate'])->middleware(['auth:sanctum', 'role:user']);


Route::get('/search-by-qr', [ProductController::class, 'searchByQR']);
// Route::get('/search-by-qr_index', [ProductController::class, 'searchByQR_index']);


Route::post('/paypal-payment', [PaypalController::class, 'createPayment'])->middleware('auth:sanctum');
Route::get('/paypal-success', [PaypalController::class, 'success']);
Route::get('/paypal-cancel', [PaypalController::class, 'cancel']);