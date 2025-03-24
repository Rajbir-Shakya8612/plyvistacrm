<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationTrack;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'speed' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $user = $request->user();

        // Check if user is checked in
        $attendance = $user->attendances()
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'User is not checked in'
            ], 400);
        }

        $location = LocationTrack::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'speed' => $request->speed,
            'accuracy' => $request->accuracy,
            'tracked_at' => now()
        ]);

        return response()->json([
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }

    public function current(Request $request)
    {
        $user = $request->user();
        $location = LocationTrack::getCurrentLocation($user->id);

        return response()->json($location);
    }

    public function timeline(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = $request->user();
        $timeline = LocationTrack::getDailyTimeline($user->id, $request->date);

        return response()->json($timeline);
    }
} 