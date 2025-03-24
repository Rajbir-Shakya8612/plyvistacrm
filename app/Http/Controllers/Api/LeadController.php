<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        $leads = $query->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($leads);
    }

    public function store(Request $request)
    {
        // Check if user is present today
        if (!Attendance::isPresent($request->user()->id, now()->toDateString())) {
            return response()->json([
                'message' => 'You must check in first to add new leads'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'expected_amount' => 'nullable|numeric',
            'follow_up_date' => 'nullable|date',
            'source' => 'nullable|string',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create(array_merge($request->all(), [
            'user_id' => $request->user()->id,
            'status' => 'new'
        ]));

        return response()->json([
            'message' => 'Lead created successfully',
            'lead' => $lead
        ], 201);
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        return response()->json($lead->load('user', 'sale'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'status' => 'sometimes|required|in:new,follow_up,confirmed,lost,shared',
            'expected_amount' => 'nullable|numeric',
            'follow_up_date' => 'nullable|date',
            'source' => 'nullable|string',
            'location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $lead->update($request->all());

        return response()->json([
            'message' => 'Lead updated successfully',
            'lead' => $lead
        ]);
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();

        return response()->json([
            'message' => 'Lead deleted successfully'
        ]);
    }

    public function share(Request $request, Lead $lead)
    {
        $this->authorize('share', $lead);

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'notes' => 'required|string'
        ]);

        $lead->shareWithOtherBrand($request->brand_id, $request->notes);

        return response()->json([
            'message' => 'Lead shared successfully',
            'lead' => $lead
        ]);
    }

    public function statusDistribution(Request $request)
    {
        $query = Lead::query();

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $distribution = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json($distribution);
    }
} 