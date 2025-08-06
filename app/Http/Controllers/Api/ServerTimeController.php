<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use App\Models\DailyVideoAssignment;
use Illuminate\Support\Facades\Auth;

class ServerTimeController extends Controller
{
    /**
     * Return the current server time
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function now(Request $request)
    {
        $now = now();
        $midnight = Carbon::tomorrow()->startOfDay();
        
        $responseData = [
            'server_time' => $now->toDateTimeString(),
            'server_timestamp' => $now->timestamp,
            'timezone' => config('app.timezone'),
            'next_day_timestamp' => $midnight->timestamp,
            'seconds_until_midnight' => $midnight->diffInSeconds($now),
            'formatted_time_until_midnight' => $this->formatTimeUntilMidnight($now, $midnight)
        ];
        
        // If user is authenticated, add video limit info
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get today's assignments
            $todaysAssignments = DailyVideoAssignment::where('user_id', $user->id)
                ->whereDate('assignment_date', today())
                ->get();
                
            $todaysWatched = $todaysAssignments->where('is_watched', true)->count();
            $todaysTotal = $todaysAssignments->count();
            $remainingViews = max(0, $todaysTotal - $todaysWatched);
            
            $responseData['user'] = [
                'daily_limit_reached' => ($remainingViews <= 0),
                'todays_views' => $todaysWatched,
                'todays_total' => $todaysTotal,
                'remaining_views' => $remainingViews
            ];
        }
        
        return response()->json($responseData);
    }
    
    /**
     * Format time until midnight in HH:MM:SS format
     * 
     * @param Carbon $now
     * @param Carbon $midnight
     * @return string
     */
    private function formatTimeUntilMidnight(Carbon $now, Carbon $midnight): string
    {
        $seconds = $midnight->diffInSeconds($now);
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
