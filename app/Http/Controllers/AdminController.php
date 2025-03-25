<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Task;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display admin dashboard data
     */
    public function dashboard(Request $request)
    {
        try {
            $stats = $this->getDashboardStats();
            $taskBoard = $this->getTaskBoard();
            $attendanceData = $this->fetchAttendanceReport($request->get('filter', 'month'));
            $recentActivities = $this->getRecentActivitiesReport();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'stats' => $stats,
                        'taskBoard' => $taskBoard,
                        'attendanceData' => $attendanceData,
                        'recentActivities' => $recentActivities
                    ]
                ]);
            }


            // Return view with data
            return view('admin.dashboard', [
                'stats' => $stats,
                'taskBoard' => $taskBoard,
                'attendanceData' => $attendanceData,
                'recentActivities' => $recentActivities,
            ]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch dashboard data',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get attendance data for chart
     */
    private function fetchAttendanceReport(string $filter = 'month')
    {
        $startDate = match($filter) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $data = Attendance::where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function($item) use ($filter) {
                return match($filter) {
                    'week' => $item->created_at->format('D'),
                    'month' => $item->created_at->format('d'),
                    'year' => $item->created_at->format('M'),
                };
            });

        $labels = match($filter) {
            'week' => collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
            'month' => collect(range(1, now()->daysInMonth()))->map(fn($day) => (string) $day),
            'year' => collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']),
        };

        $present = $labels->map(fn($label) => 
            $data->get($label)?->where('status', 'present')->count() ?? 0
        );

        $absent = $labels->map(fn($label) => 
            $data->get($label)?->where('status', 'absent')->count() ?? 0
        );

        $late = $labels->map(fn($label) => 
            $data->get($label)?->where('status', 'late')->count() ?? 0
        );

        return (object) [
            'labels' => $labels,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivitiesReport()
    {
        $activities = collect();

        // Get recent leads
        $leads = Lead::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($lead) {
                return [
                    'type' => 'lead',
                    'description' => "New lead created by {$lead->user->name}",
                    'details' => $lead->name,
                    'created_at' => $lead->created_at,
                ];
            });
        $activities = $activities->concat($leads);

        // Get recent sales
        $sales = Sale::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($sale) {
                return [
                    'type' => 'sale',
                    'description' => "New sale recorded by {$sale->user->name}",
                    'details' => "₹" . number_format($sale->amount),
                    'created_at' => $sale->created_at,
                ];
            });
        $activities = $activities->concat($sales);

        // Get recent attendance
        $attendance = Attendance::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($attendance) {
                return [
                    'type' => 'attendance',
                    'description' => "{$attendance->user->name} marked {$attendance->status}",
                    'details' => $attendance->created_at->format('h:i A'),
                    'created_at' => $attendance->created_at,
                ];
            });
        $activities = $activities->concat($attendance);

        return $activities->sortByDesc('created_at')->take(10)->values();
    }

    /**
     * List all users
     */
    public function users(Request $request)
    {
        $users = User::when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function($query, $role) {
                $query->where('role', $role);
            })
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:admin,salesperson,dealer,carpenter'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'pincode' => ['required', 'string', 'max:10'],
            'date_of_joining' => ['required', 'date'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Show user details
     */
    public function showUser(Request $request, User $user)
    {
        $user->load(['attendances' => function($query) {
            $query->latest()->take(10);
        }, 'leads' => function($query) {
            $query->latest()->take(10);
        }, 'sales' => function($query) {
            $query->latest()->take(10);
        }]);

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Update user details
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,salesperson,dealer,carpenter'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'pincode' => ['required', 'string', 'max:10'],
            'date_of_joining' => ['required', 'date'],
            'status' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        }

        return back()->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser(Request $request, User $user)
    {
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Get attendance data for charts
     */
    public function getAttendanceData(Request $request)
    {
        try {
            $data = $this->fetchAttendanceReport();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return $data;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch attendance data',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get performance data
     */
    public function getPerformanceData(Request $request)
    {
        try {
            $data = $this->getPerformanceData();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return $data;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch performance data',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $activities = $this->getRecentActivitiesReport();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $activities
                ]);
            }

            return $activities;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch recent activities',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfYear = $today->copy()->startOfYear();

        return [
            'total_users' => User::count(),
            'total_leads' => Lead::count(),
            'total_sales' => Sale::count(),
            'monthly_sales' => Sale::where('created_at', '>=', $startOfMonth)->sum('amount'),
            'yearly_sales' => Sale::where('created_at', '>=', $startOfYear)->sum('amount'),
            'active_users' => User::where('status', true)->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
        ];
    }

    /**
     * Get task board data
     */
    private function getTaskBoard()
    {
        return Task::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->groupBy('status')
            ->map(function($tasks) {
                return $tasks->map(function($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'due_date' => $task->due_date,
                        'assigned_to' => $task->user->name,
                        'priority' => $task->priority,
                    ];
                });
            });
    }
} 