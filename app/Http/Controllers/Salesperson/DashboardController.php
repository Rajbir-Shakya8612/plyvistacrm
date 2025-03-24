<?php

namespace App\Http\Controllers\Salesperson;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();

        // Today's Leads
        $todayLeads = Lead::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        $yesterdayLeads = Lead::where('user_id', $user->id)
            ->whereDate('created_at', $yesterday)
            ->count();

        $leadChange = $yesterdayLeads > 0
            ? round((($todayLeads - $yesterdayLeads) / $yesterdayLeads) * 100, 1)
            : 0;

        // Today's Sales
        $todaySales = Sale::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->sum('amount');

        $yesterdaySales = Sale::where('user_id', $user->id)
            ->whereDate('created_at', $yesterday)
            ->sum('amount');

        $salesChange = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;

        // Target Progress
        $currentSales = Sale::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        $targetAmount = $user->target_amount;
        $targetProgress = $targetAmount > 0
            ? round(($currentSales / $targetAmount) * 100, 1)
            : 0;

        // Conversion Rate
        $thisMonthLeads = Lead::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $thisMonthSales = Sale::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $conversionRate = $thisMonthLeads > 0
            ? round(($thisMonthSales / $thisMonthLeads) * 100, 1)
            : 0;

        $lastMonthLeads = Lead::where('user_id', $user->id)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $lastMonthSales = Sale::where('user_id', $user->id)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $lastMonthConversion = $lastMonthLeads > 0
            ? round(($lastMonthSales / $lastMonthLeads) * 100, 1)
            : 0;

        $conversionChange = $lastMonthConversion > 0
            ? round(($conversionRate - $lastMonthConversion), 1)
            : 0;

        // Performance Chart Data
        $performanceData = new \stdClass();
        $performanceData->labels = [];
        $performanceData->leads = [];
        $performanceData->sales = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $performanceData->labels[] = $date->format('D');

            $performanceData->leads[] = Lead::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->count();

            $performanceData->sales[] = Sale::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->count();
        }

        // Lead Sources Chart Data
        $leadSourcesData = new \stdClass();
        $leadSources = Lead::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->get();

        $leadSourcesData->labels = $leadSources->pluck('source')->toArray();
        $leadSourcesData->values = $leadSources->pluck('total')->toArray();

        // Recent Activities
        $activities = Activity::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Today's Schedule
        $schedule = $user->schedule()
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get();

        return view('salesperson.dashboard', compact(
            'todayLeads',
            'leadChange',
            'todaySales',
            'salesChange',
            'targetProgress',
            'currentSales',
            'targetAmount',
            'conversionRate',
            'conversionChange',
            'performanceData',
            'leadSourcesData',
            'activities',
            'schedule'
        ));
    }
} 