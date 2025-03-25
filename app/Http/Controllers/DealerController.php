<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DealerController extends Controller
{
    /**
     * Display dealer dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dealer dashboard data'
                ]);
            }

            return view('dealer.dashboard');
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