<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role->slug;

        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user,
                'role' => $role,
                'dashboard_data' => $this->getDashboardData($role)
            ]);
        }

        return view("dashboard.{$role}-dashboard");
    }

    private function getDashboardData($role)
    {
        // Add role-specific dashboard data here
        $data = [
            'admin' => [
                'total_users' => User::count(),
                'total_roles' => Role::count(),
                // Add more admin-specific data
            ],
            'salesperson' => [
                'sales_target' => 100000,
                'current_sales' => 75000,
                // Add more salesperson-specific data
            ],
            // Add more roles as needed
        ];

        return $data[$role] ?? [];
    }
}
