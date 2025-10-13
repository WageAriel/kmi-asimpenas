<?php
// File: app/Http/Controllers/ActivityController.php

namespace App\Http\Controllers;

use App\Services\ActivityAggregatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Get activity logs untuk user yang sedang login
     */
    public function myActivities(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->input('limit', 10);

        $activities = ActivityAggregatorService::getUserActivities($userId, $limit);

        return response()->json($activities);
    }

    /**
     * Refresh activities (clear cache dan fetch ulang)
     */
    public function refreshActivities(Request $request)
    {
        $userId = Auth::id();
        $limit = $request->input('limit', 10);

        $activities = ActivityAggregatorService::refreshUserActivities($userId, $limit);

        return response()->json([
            'message' => 'Activities refreshed successfully',
            'data' => $activities
        ]);
    }
}