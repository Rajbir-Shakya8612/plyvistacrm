<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Get leads statistics
        $leadsQuery = Lead::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!$user->isAdmin()) {
            $leadsQuery->where('user_id', $user->id);
        }

        $leadsStats = $leadsQuery->select(
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('COUNT(CASE WHEN status = "confirmed" THEN 1 END) as confirmed_leads'),
            DB::raw('COUNT(CASE WHEN status = "lost" THEN 1 END) as lost_leads'),
            DB::raw('COUNT(CASE WHEN status = "follow_up" THEN 1 END) as follow_up_leads')
        )->first();

        // Get sales statistics
        $salesQuery = Sale::query()
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if (!$user->isAdmin()) {
            $salesQuery->where('user_id', $user->id);
        }

        $salesStats = $salesQuery->select(
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(amount) as average_amount'),
            DB::raw('COUNT(CASE WHEN payment_status = "completed" THEN 1 END) as completed_sales')
        )->first();

        // Get attendance statistics
        $attendanceQuery = Attendance::query()
            ->whereBetween('date', [$startDate, $endDate]);

        if (!$user->isAdmin()) {
            $attendanceQuery->where('user_id', $user->id);
        }

        $attendanceStats = $attendanceQuery->select(
            DB::raw('COUNT(*) as total_days'),
            DB::raw('COUNT(CASE WHEN status = "present" THEN 1 END) as present_days'),
            DB::raw('COUNT(CASE WHEN status = "late" THEN 1 END) as late_days'),
            DB::raw('COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_days')
        )->first();

        return response()->json([
            'leads' => $leadsStats,
            'sales' => $salesStats,
            'attendance' => $attendanceStats,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function achievements(Request $request)
    {
        $user = $request->user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Get monthly targets vs achievements
        $salesQuery = Sale::query()
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month);

        if (!$user->isAdmin()) {
            $salesQuery->where('user_id', $user->id);
        }

        $salesAchievements = $salesQuery->select(
            'user_id',
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('user_id')
            ->with(['user:id,name,target_amount,target_leads'])
            ->get();

        // Get lead conversion rates
        $leadsQuery = Lead::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if (!$user->isAdmin()) {
            $leadsQuery->where('user_id', $user->id);
        }

        $leadStats = $leadsQuery->select(
            'user_id',
            DB::raw('COUNT(*) as total_leads'),
            DB::raw('COUNT(CASE WHEN status = "confirmed" THEN 1 END) as converted_leads')
        )
            ->groupBy('user_id')
            ->get()
            ->map(function ($stat) {
                $stat->conversion_rate = $stat->total_leads > 0
                    ? round(($stat->converted_leads / $stat->total_leads) * 100, 2)
                    : 0;
                return $stat;
            });

        return response()->json([
            'sales_achievements' => $salesAchievements,
            'lead_stats' => $leadStats,
            'period' => [
                'year' => $year,
                'month' => $month
            ]
        ]);
    }
} 