<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($request->wantsJson()) {
            return response()->json([
                'user' => $user,
                'settings' => $user->settings
            ]);
        }

        // Return view based on user role
        $view = $user->role === 'admin' ? 'admin.settings' : 'salesperson.settings';
        return view($view, compact('user'));
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'pincode' => ['required', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'max:2048']
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Password updated successfully'
            ]);
        }

        return back()->with('success', 'Password updated successfully');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'settings.email_notifications' => ['boolean'],
            'settings.whatsapp_notifications' => ['boolean'],
            'settings.push_notifications' => ['boolean']
        ]);

        $user = $request->user();
        $settings = $user->settings ?? [];
        $user->settings = array_merge($settings, $validated['settings']);
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Notification settings updated successfully',
                'settings' => $user->settings
            ]);
        }

        return back()->with('success', 'Notification settings updated successfully');
    }

    /**
     * Update target settings (for salespersons)
     */
    public function updateTargets(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'salesperson') {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'target_amount' => ['required', 'numeric', 'min:0'],
            'target_leads' => ['required', 'integer', 'min:0']
        ]);

        $user->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Target settings updated successfully',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Target settings updated successfully');
    }
} 