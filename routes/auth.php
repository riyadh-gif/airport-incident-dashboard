<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (NIP-based)
|--------------------------------------------------------------------------
|
| Login is performed with NIP + password (see LoginRequest). Accounts are
| provisioned by an administrator, so all email-dependent Breeze flows
| (public registration, password reset by email, email verification and
| password confirmation) are intentionally NOT registered for this
| application. The corresponding controllers and views were removed.
|
| Only login/logout and the authenticated change-password flow remain.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Change-password (authenticated, current-password protected).
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
