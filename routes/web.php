<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentDetailController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Shared dashboard + map (all authenticated users).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map', [MapController::class, 'index'])->name('map');

    // Dashboard / map drill-down detail endpoints (JSON).
    Route::get('/details/incidents', [IncidentDetailController::class, 'index'])->name('details.incidents');

    // Incidents & Flights CRUD (shared).
    Route::resource('incidents', IncidentController::class)->except(['show']);
    Route::resource('flights', FlightController::class)->except(['show']);

    // Profile.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users CRUD (admin-only).
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
