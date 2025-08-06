<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HighTrafficMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check concurrent user count
        $concurrentUsers = Cache::remember('concurrent_users_emergency', 60, function () {
            return DB::table('users')
                ->where('last_seen', '>=', Carbon::now()->subMinutes(5))
                ->count();
        });

        // Enable emergency mode if threshold exceeded
        $emergencyThreshold = config('scaling.emergency_mode.auto_enable_threshold', 8000);
        
        if ($concurrentUsers > $emergencyThreshold) {
            // Set emergency mode flag
            Cache::put('emergency_mode_active', true, 300); // 5 minutes
            
            // Add emergency response headers
            $response = $next($request);
            
            $response->headers->set('X-Emergency-Mode', 'active');
            $response->headers->set('X-Concurrent-Users', $concurrentUsers);
            $response->headers->set('Cache-Control', 'public, max-age=60'); // Aggressive caching
            
            return $response;
        }

        // Normal mode
        Cache::forget('emergency_mode_active');
        
        return $next($request);
    }
}
