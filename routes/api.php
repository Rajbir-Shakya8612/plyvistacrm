<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SalespersonController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PerformanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('api.admin.dashboard');
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
        Route::get('/performance', [SalespersonController::class, 'getPerformance']);
        Route::get('/recent-activities', [SalespersonController::class, 'getRecentActivities']);
        
        // Attendance
        Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/status', [AttendanceController::class, 'status']);

        // Leads
        Route::get('/leads', [LeadController::class, 'index']);
        Route::post('/leads', [LeadController::class, 'store']);
        Route::get('/leads/{lead}', [LeadController::class, 'show']);
        Route::put('/leads/{lead}', [LeadController::class, 'update']);

        // Sales
        Route::get('/sales', [SaleController::class, 'index']);
        Route::post('/sales', [SaleController::class, 'store']);
        Route::get('/sales/{sale}', [SaleController::class, 'show']);
        Route::put('/sales/{sale}', [SaleController::class, 'update']);

        // Location updates
        Route::post('/location/update', [LocationController::class, 'update']);
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
