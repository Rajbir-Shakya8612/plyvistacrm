<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Salesperson\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'role:salesperson'])->name('salesperson.')->prefix('salesperson')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings');
});
