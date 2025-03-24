<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'settings' => $user->settings ?? [
                'email_notifications' => true,
                'sms_notifications' => true,
                'theme' => 'light'
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'whatsapp_number' => 'sometimes|required|string|max:20',
            'pincode' => 'sometimes|required|string|max:10',
            'address' => 'sometimes|required|string',
            'location' => 'sometimes|required|string',
            'designation' => 'sometimes|required|string',
            'photo' => 'nullable|image|max:2048',
            'settings' => 'sometimes|required|array',
            'settings.email_notifications' => 'boolean',
            'settings.sms_notifications' => 'boolean',
            'settings.theme' => 'in:light,dark'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $photoPath = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $photoPath;
        }

        // Update user data
        $user->fill($request->except(['photo', 'settings']));
        
        // Update settings
        if ($request->has('settings')) {
            $user->settings = array_merge($user->settings ?? [], $request->settings);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
} 