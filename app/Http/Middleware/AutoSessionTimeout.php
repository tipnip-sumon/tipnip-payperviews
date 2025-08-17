<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserSessionNotification;

class AutoSessionTimeout
{
    /**
     * Handle an incoming request with automatic session timeout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip timeout check for logout routes and AJAX requests that are part of logout process
        if ($this->shouldSkipTimeoutCheck($request)) {
            return $next($request);
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $timeoutMinutes = $this->getSessionTimeoutMinutes($user->id);
            
            // Get last activity time
            $lastActivity = session('last_activity_time', time());
            $currentTime = time();
            $inactiveTime = $currentTime - $lastActivity;
            $inactiveMinutes = $inactiveTime / 60;

            // Log activity for debugging
            Log::info('Session timeout check', [
                'user_id' => $user->id,
                'last_activity' => date('Y-m-d H:i:s', $lastActivity),
                'current_time' => date('Y-m-d H:i:s', $currentTime),
                'inactive_minutes' => round($inactiveMinutes, 2),
                'timeout_limit' => $timeoutMinutes,
                'session_id' => session()->getId()
            ]);

            // Check if session has timed out
            if ($inactiveMinutes > $timeoutMinutes) {
                return $this->handleSessionTimeout($request, $user, $inactiveMinutes);
            }

            // Update last activity time for non-GET requests or important GET requests
            if (!$request->isMethod('GET') || $this->shouldUpdateActivity($request)) {
                session(['last_activity_time' => $currentTime]);
            }

            // Add timeout warning for AJAX requests
            if ($request->ajax() && $inactiveMinutes > ($timeoutMinutes * 0.8)) {
                $remainingMinutes = $timeoutMinutes - $inactiveMinutes;
                $response = $next($request);
                
                if (method_exists($response, 'header')) {
                    $response->header('X-Session-Timeout-Warning', 'true');
                    $response->header('X-Session-Remaining-Minutes', round($remainingMinutes, 1));
                }
                
                return $response;
            }
        }

        return $next($request);
    }

    /**
     * Handle session timeout with complete session destruction
     */
    private function handleSessionTimeout(Request $request, $user, $inactiveMinutes)
    {
        $sessionId = session()->getId();
        $userId = $user->id;
        $username = $user->username ?? 'Unknown';

        // Log the timeout event
        Log::warning('Session timeout - automatic logout', [
            'user_id' => $userId,
            'username' => $username,
            'inactive_minutes' => round($inactiveMinutes, 2),
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Create timeout notification BEFORE session destruction
        try {
            UserSessionNotification::create([
                'user_id' => $userId,
                'type' => 'session_timeout',
                'title' => 'Session Timeout',
                'message' => "Your session was automatically logged out due to inactivity for " . round($inactiveMinutes, 1) . " minutes.",
                'new_login_ip' => $request->ip(),
                'action_taken' => 'automatic_timeout_logout'
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not create timeout notification: ' . $e->getMessage());
        }

        // Perform complete session destruction
        $this->performCompleteSessionDestruction($request, $userId);

        // Handle different request types
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'session_timeout',
                'message' => 'Your session has expired due to inactivity. Please login again.',
                'inactive_minutes' => round($inactiveMinutes, 1),
                'redirect_url' => route('login'),
                'session_expired' => true
            ], 401);
        }

        // For regular requests, redirect to login with timeout message
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with([
            'warning' => 'Your session expired due to inactivity (' . round($inactiveMinutes, 1) . ' minutes). Please login again.',
            'session_timeout' => true,
            'inactive_time' => round($inactiveMinutes, 1)
        ]);
    }

    /**
     * Perform complete session destruction (same as logout)
     */
    private function performCompleteSessionDestruction(Request $request, $userId)
    {
        try {
            $sessionId = session()->getId();
            
            // Step 1: Logout user
            Auth::logout();
            
            // Step 2: Complete session destruction
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flush();
            $request->session()->regenerate(true);
            
            // Step 3: Clear user-specific cache
            $this->clearUserCache($userId);
            
            Log::info('Complete session destruction completed for timeout', [
                'user_id' => $userId,
                'old_session_id' => $sessionId,
                'new_session_id' => session()->getId()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error during timeout session destruction: ' . $e->getMessage());
        }
    }

    /**
     * Clear user-specific cache data
     */
    private function clearUserCache($userId)
    {
        $cacheKeys = [
            'user_' . $userId,
            'user_session_' . $userId,
            'user_data_' . $userId,
            'user_preferences_' . $userId,
            'user_settings_' . $userId,
            'user_permissions_' . $userId,
            'trusted_ips_user_' . $userId,
            'user_session_settings_notifications_' . $userId,
            'user_session_settings_security_' . $userId
        ];

        foreach ($cacheKeys as $key) {
            try {
                \Illuminate\Support\Facades\Cache::forget($key);
            } catch (\Exception $e) {
                Log::warning("Could not clear cache key {$key}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get session timeout minutes for user
     */
    private function getSessionTimeoutMinutes($userId)
    {
        // Try to get user's preferred timeout from cache/settings
        $userSettings = \Illuminate\Support\Facades\Cache::get("user_session_settings_security_{$userId}", []);
        
        // Default timeout settings (in minutes)
        $defaultTimeout = 30; // 30 minutes default
        
        // Check if user has custom timeout setting
        if (isset($userSettings['session_timeout_hours'])) {
            return $userSettings['session_timeout_hours'] * 60; // Convert hours to minutes
        }
        
        // Check environment variable
        if (env('AUTO_LOGOUT_MINUTES')) {
            return (int) env('AUTO_LOGOUT_MINUTES');
        }
        
        return $defaultTimeout;
    }

    /**
     * Determine if we should skip timeout check for this request
     */
    private function shouldSkipTimeoutCheck(Request $request)
    {
        $skipRoutes = [
            'logout',
            'login',
            'register',
            'password.reset',
            'password.email',
            'verification.notice',
            'verification.verify',
            'force.logout',
            'simple.logout'
        ];

        $skipPaths = [
            'logout',
            'login',
            'register',
            'password',
            'email/verify',
            'force-logout',
            'simple-logout'
        ];

        // Skip if route name matches
        if (in_array($request->route()->getName(), $skipRoutes)) {
            return true;
        }

        // Skip if path matches
        foreach ($skipPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if this request should update last activity time
     */
    private function shouldUpdateActivity(Request $request)
    {
        // Update activity for important GET requests
        $importantPaths = [
            'user/dashboard',
            'user/profile',
            'user/sessions',
            'deposit',
            'withdraw'
        ];

        foreach ($importantPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return true;
            }
        }

        return false;
    }
}
