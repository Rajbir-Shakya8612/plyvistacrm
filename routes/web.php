<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CarpenterController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Salesperson\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;



Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('check-session', function() {
        return response()->json(['status' => 'active']);
    })->name('check.session');

    // Role-based dashboard routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    Route::middleware('role:salesperson')->group(function () {
        Route::get('/sales/dashboard', [DashboardController::class, 'index'])->name('sales.dashboard');
    });

    // Default dashboard redirect based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Dashboard Data APIs
    Route::get('/attendance/overview', [AdminController::class, 'getAttendanceOverview']);
    Route::get('/performance/overview', [AdminController::class, 'getPerformanceOverview']);
    Route::get('/activities/recent', [AdminController::class, 'getRecentActivities']);
    
    // Tasks Management
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('admin.tasks');
    Route::post('/tasks', [AdminController::class, 'createTask'])->name('admin.tasks.create');
    Route::get('/tasks/{task}', [AdminController::class, 'showTask'])->name('admin.tasks.show');
    Route::put('/tasks/{task}', [AdminController::class, 'updateTask'])->name('admin.tasks.update');
    Route::delete('/tasks/{task}', [AdminController::class, 'deleteTask'])->name('admin.tasks.delete');
    Route::put('/tasks/{task}/status', [AdminController::class, 'updateTaskStatus'])->name('admin.tasks.status');
    
    // Attendance Management
    Route::get('/attendance', [AdminController::class, 'attendance'])->name('admin.attendance');
    Route::get('/attendance/export', [AdminController::class, 'exportAttendance'])->name('admin.attendance.export');
    Route::post('/attendance/bulk', [AdminController::class, 'bulkUpdateAttendance'])->name('admin.attendance.bulk');
    
    // Sales Management
    Route::get('/sales', [AdminController::class, 'sales'])->name('admin.sales');
    Route::get('/sales/export', [AdminController::class, 'exportSales'])->name('admin.sales.export');
    Route::get('/sales/analytics', [AdminController::class, 'salesAnalytics'])->name('admin.sales.analytics');
    
    // Leads Management
    Route::get('/leads', [AdminController::class, 'leads'])->name('admin.leads');
    Route::get('/leads/export', [AdminController::class, 'exportLeads'])->name('admin.leads.export');
    Route::get('/leads/analytics', [AdminController::class, 'leadsAnalytics'])->name('admin.leads.analytics');
    
    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    
    // Locations Management
    Route::get('/locations', [AdminController::class, 'locations'])->name('admin.locations');
    Route::post('/locations', [AdminController::class, 'createLocation'])->name('admin.locations.create');
    Route::get('/locations/{location}', [AdminController::class, 'showLocation'])->name('admin.locations.show');
    Route::put('/locations/{location}', [AdminController::class, 'updateLocation'])->name('admin.locations.update');
    Route::delete('/locations/{location}', [AdminController::class, 'deleteLocation'])->name('admin.locations.delete');
});

// Salesperson routes
Route::middleware(['auth', 'role:salesperson'])->prefix('salesperson')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('salesperson.dashboard');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
});

// Dealer routes
Route::middleware(['auth', 'role:dealer'])->prefix('dealer')->group(function () {
    Route::get('/dashboard', [DealerController::class, 'dashboard'])->name('dealer.dashboard');
});

// Carpenter routes
Route::middleware(['auth', 'role:carpenter'])->prefix('carpenter')->group(function () {
    Route::get('/dashboard', [CarpenterController::class, 'dashboard'])->name('carpenter.dashboard');
});
