<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SessionCleanup
{
    /**
     * Handle an incoming request.
     * Clean up sessions to prevent bloat and multiple session issues.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only run cleanup occasionally to avoid performance issues
        $shouldCleanup = $this->shouldRunCleanup($request);
        
        if ($shouldCleanup && Auth::check()) {
            $user = Auth::user();
            $currentSessionId = session()->getId();
            
            try {
                // Only clean up if user has multiple sessions (not on every request)
                $sessionCount = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->count();
                
                if ($sessionCount > 1) {
                    // Keep only the current session, delete others
                    $deleted = DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->where('id', '!=', $currentSessionId)
                        ->delete();
                    
                    if ($deleted > 0) {
                        Log::info("SessionCleanup: Removed {$deleted} old sessions for user {$user->id}");
                    }
                }
                
            } catch (\Exception $e) {
                // Don't break the request if cleanup fails
                Log::warning('SessionCleanup failed: ' . $e->getMessage());
            }
        }
        
        return $next($request);
    }
    
    /**
     * Determine if cleanup should run on this request
     */
    private function shouldRunCleanup(Request $request): bool
    {
        // Skip for AJAX requests to avoid performance issues
        if ($request->ajax()) {
            return false;
        }
        
        // Skip for API routes
        if ($request->is('api/*')) {
            return false;
        }
        
        // Skip for admin routes (they have their own session handling)
        if ($request->is('admin/*')) {
            return false;
        }
        
        // Skip for logout routes
        if ($request->is('logout') || $request->is('*/logout')) {
            return false;
        }
        
        // Use cache to limit frequency (only run once per minute per user)
        $cacheKey = 'session_cleanup_' . (Auth::check() ? Auth::id() : 'guest') . '_' . session()->getId();
        
        if (Cache::has($cacheKey)) {
            return false;
        }
        
        // Set cache for 1 minute to prevent frequent cleanups
        Cache::put($cacheKey, true, 60);
        
        return true;
    }
}
