<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LocationTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'photo' => 'required|image|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        // Check if already checked in
        if (Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->exists()) {
            return response()->json([
                'message' => 'Already checked in for today'
            ], 400);
        }

        // Store photo
        $photoPath = $request->file('photo')->store('attendance-photos', 'public');

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in_time' => now(),
            'check_in_location' => $request->location,
            'check_in_photo' => $photoPath,
            'status' => Attendance::isLate(now()->format('H:i:s')) ? 'late' : 'present'
        ]);

        // Record initial location
        LocationTrack::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->location,
            'tracked_at' => now()
        ]);

        // Send WhatsApp notification if late
        if ($attendance->status === 'late') {
            $this->sendLateNotification($user);
        }

        return response()->json([
            'message' => 'Checked in successfully',
            'attendance' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'photo' => 'required|image|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No active check-in found'
            ], 400);
        }

        // Store photo
        $photoPath = $request->file('photo')->store('attendance-photos', 'public');

        // Update attendance record
        $attendance->update([
            'check_out_time' => now(),
            'check_out_location' => $request->location,
            'check_out_photo' => $photoPath
        ]);

        // Record final location
        LocationTrack::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->location,
            'tracked_at' => now()
        ]);

        return response()->json([
            'message' => 'Checked out successfully',
            'attendance' => $attendance
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'is_checked_in' => $attendance && $attendance->check_in_time,
            'is_checked_out' => $attendance && $attendance->check_out_time,
            'status' => $attendance ? $attendance->status : null,
            'check_in_time' => $attendance ? $attendance->check_in_time : null,
            'check_out_time' => $attendance ? $attendance->check_out_time : null
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $month = $request->input('month', now()->format('Y-m'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($attendances);
    }

    private function sendLateNotification($user)
    {
        // Implement WhatsApp notification logic here
        // You can use a WhatsApp API service or SMS gateway
        $message = "Late arrival notification: {$user->name} arrived late at " . now()->format('h:i A');
        
        // Example using WhatsApp API
        // WhatsApp::send($user->whatsapp_number, $message);
    }
} 