<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if user is authenticated via web session
            if (Auth::guard('web')->check()) {
                return $next($request);
            }

            // Check if user is authenticated via API token
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'success' => false
                ], 401);
            }

            // Check if the request has a valid token
            if (!$request->bearerToken()) {
                return response()->json([
                    'message' => 'No token provided.',
                    'success' => false
                ], 401);
            }

            return $next($request);
        } catch (MissingAbilityException $e) {
            return response()->json([
                'message' => 'Token is invalid or expired.',
                'success' => false
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed.',
                'success' => false
            ], 401);
        }
    }
} 