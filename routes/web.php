<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Salesperson\DashboardController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\CarpenterController;
use Illuminate\Support\Facades\Request;

// Public routes
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Authentication routes
Route::post('/login', [AuthController::class, 'webLogin'])->name('web.login');

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Salesperson routes
Route::middleware(['auth:sanctum', 'role:salesperson'])->prefix('salesperson')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('salesperson.dashboard');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
});

// Dealer routes
Route::middleware(['auth:sanctum', 'role:dealer'])->prefix('dealer')->group(function () {
    Route::get('/dashboard', [DealerController::class, 'dashboard'])->name('dealer.dashboard');
});

// Carpenter routes
Route::middleware(['auth:sanctum', 'role:carpenter'])->prefix('carpenter')->group(function () {
    Route::get('/dashboard', [CarpenterController::class, 'dashboard'])->name('carpenter.dashboard');
});
