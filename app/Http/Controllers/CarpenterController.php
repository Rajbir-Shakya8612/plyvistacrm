<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarpenterController extends Controller
{
    /**
     * Display carpenter dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Carpenter dashboard data'
                ]);
            }

            return view('carpenter.dashboard');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch dashboard data',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }
} 