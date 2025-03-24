<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PerformanceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Attendance routes
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/status', [AttendanceController::class, 'status']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);

    // Location tracking routes
    Route::post('/location/update', [LocationController::class, 'update']);
    Route::get('/location/current', [LocationController::class, 'current']);
    Route::get('/location/timeline', [LocationController::class, 'timeline']);

    // Lead routes
    Route::apiResource('leads', LeadController::class);
    Route::post('/leads/{lead}/share', [LeadController::class, 'share']);
    Route::get('/leads/status-distribution', [LeadController::class, 'statusDistribution']);

    // Sale routes
    Route::apiResource('sales', SaleController::class);
    Route::get('/sales/monthly-performance', [SaleController::class, 'monthlyPerformance']);

    // Performance routes
    Route::get('/performance/overview', [PerformanceController::class, 'overview']);
    Route::get('/performance/achievements', [PerformanceController::class, 'achievements']);
});
