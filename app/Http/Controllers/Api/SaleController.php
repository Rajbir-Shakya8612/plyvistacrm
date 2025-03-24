<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Lead;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::query();

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('sale_date', [$request->from_date, $request->to_date]);
        }

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $sales = $query->with(['user', 'lead'])
            ->latest('sale_date')
            ->paginate(10);

        return response()->json($sales);
    }

    public function store(Request $request)
    {
        // Check if user is present today
        if (!Attendance::isPresent($request->user()->id, now()->toDateString())) {
            return response()->json([
                'message' => 'You must check in first to add new sales'
            ], 403);
        }

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,completed',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'product_details' => 'nullable|array',
            'sale_date' => 'required|date',
        ]);

        // Check if lead belongs to user or user is admin
        $lead = Lead::findOrFail($request->lead_id);
        if (!$request->user()->isAdmin() && $lead->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to create a sale for this lead'
            ], 403);
        }

        $sale = Sale::create(array_merge($request->all(), [
            'user_id' => $request->user()->id
        ]));

        // Update lead status to confirmed
        $lead->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Sale created successfully',
            'sale' => $sale->load('lead')
        ], 201);
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        return response()->json($sale->load('user', 'lead'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorize('update', $sale);

        $request->validate([
            'amount' => 'sometimes|required|numeric|min:0',
            'payment_status' => 'sometimes|required|in:pending,partial,completed',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'product_details' => 'nullable|array',
            'sale_date' => 'sometimes|required|date',
        ]);

        $sale->update($request->all());

        return response()->json([
            'message' => 'Sale updated successfully',
            'sale' => $sale
        ]);
    }

    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);
        $sale->delete();

        return response()->json([
            'message' => 'Sale deleted successfully'
        ]);
    }

    public function monthlyPerformance(Request $request)
    {
        $query = Sale::query();

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        // Get monthly sales data for the last 12 months
        $performance = $query->select(
            DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total_sales'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(amount) as average_amount')
        )
            ->whereDate('sale_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($performance);
    }
} 