<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        try {
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
                    'success' => true,
                    'message' => 'Registration successful',
                    'user' => $user
                ], 201);
            }

            return redirect()->route('login')->with('success', 'Registration successful. Please login to continue.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed',
                    'errors' => $e instanceof ValidationException ? $e->errors() : [$e->getMessage()]
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = Auth::user();
            
            // Revoke all existing tokens
            $user->tokens()->delete();
            
            // Create new token
            $token = $user->createToken('auth-token')->plainTextToken;

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                    'redirect_url' => $this->getRedirectUrl($user)
                ]);
            }

            return redirect($this->getRedirectUrl($user));
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login failed',
                    'errors' => $e instanceof ValidationException ? $e->errors() : [$e->getMessage()]
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        Auth::guard('web')->logout();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }

        return redirect()->route('login');
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(User $user): string
    {
        return match($user->role) {
            'admin' => route('api.admin.dashboard'),
            'salesperson' => route('api.salesperson.dashboard'),
            'dealer' => route('dealer.dashboard'),
            'carpenter' => route('carpenter.dashboard'),
            default => route('login'),
        };
    }
} 