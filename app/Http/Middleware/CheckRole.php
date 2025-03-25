<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !in_array($request->user()->role)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
} 