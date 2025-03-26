<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CarpenterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalespersonController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PerformanceController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});


// Protected routes
Route::middleware(['auth.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('api.admin.dashboard');
        Route::get('/attendance-data', [AdminController::class, 'getAttendanceData']);
        Route::get('/performance-data', [AdminController::class, 'getPerformanceData']);
        Route::get('/recent-activities', [AdminController::class, 'getRecentActivities']);
        
        // User management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::get('/users/{user}', [AdminController::class, 'showUser']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);

        // Tasks
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::put('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

        // Attendance overview
        Route::get('/attendance/overview', [AttendanceController::class, 'overview']);
        Route::get('/attendance/export', [AttendanceController::class, 'export']);

        // Performance metrics
        Route::get('/performance/overview', [PerformanceController::class, 'overview']);
        Route::get('/performance/export', [PerformanceController::class, 'export']);

        // Location tracking
        Route::get('/locations/current', [LocationController::class, 'current']);
        Route::get('/locations/history', [LocationController::class, 'history']);
    });

    // Salesperson routes
    Route::middleware(['role:salesperson'])->prefix('salesperson')->group(function () {
        Route::get('/dashboard', [SalespersonController::class, 'dashboard'])->name('api.salesperson.dashboard');
        Route::get('/leads', [LeadController::class, 'index']);
        Route::get('/sales', [SaleController::class, 'index']);
        Route::get('/attendance', [AttendanceController::class, 'index']);
        Route::get('/performance', [PerformanceController::class, 'index']);
        
        // Attendance
        Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/status', [AttendanceController::class, 'status']);

        // Leads
        Route::post('/leads', [LeadController::class, 'store']);
        Route::get('/leads/{lead}', [LeadController::class, 'show']);
        Route::put('/leads/{lead}', [LeadController::class, 'update']);

        // Sales
        Route::post('/sales', [SaleController::class, 'store']);
        Route::get('/sales/{sale}', [SaleController::class, 'show']);
        Route::put('/sales/{sale}', [SaleController::class, 'update']);

        // Location updates
        Route::post('/location/update', [LocationController::class, 'update']);
    });

    // Dealer routes
    Route::middleware(['role:dealer'])->prefix('dealer')->group(function () {
        Route::get('/dashboard', [DealerController::class, 'dashboard'])->name('api.dealer.dashboard');
    });

    // Carpenter routes
    Route::middleware(['role:carpenter'])->prefix('carpenter')->group(function () {
        Route::get('/dashboard', [CarpenterController::class, 'dashboard'])->name('api.carpenter.dashboard');
    });

    // Common routes for all authenticated users
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/profile', [SettingsController::class, 'updateProfile']);
        Route::put('/password', [SettingsController::class, 'updatePassword']);
        Route::put('/notifications', [SettingsController::class, 'updateNotificationPreferences']);
        Route::put('/targets', [SettingsController::class, 'updateTargets']);
    });
});
