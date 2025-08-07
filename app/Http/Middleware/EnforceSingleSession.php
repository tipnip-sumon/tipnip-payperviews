<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class EnforceSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session('user_session_id');

            // Skip session check for logout requests to prevent issues
            if ($request->routeIs('logout') || $request->routeIs('simple.logout') || 
                $request->is('logout') || $request->is('simple-logout') ||
                str_contains($request->getRequestUri(), 'logout') ||
                $request->has('from_logout')) {
                return $next($request);
            }

            // Skip session check for fresh login redirects (give login process time to complete)
            if (session('fresh_login') || session('login_success')) {
                // Clear the fresh login flags after first access
                session()->forget(['fresh_login', 'login_success']);
                
                // Set initial activity timestamp
                session(['last_activity' => time()]);
                
                // If no session ID exists for fresh login, try to generate one
                if (!$sessionId) {
                    try {
                        $newSessionId = $user->generateNewSession();
                        session(['user_session_id' => $newSessionId]);
                        
                        Log::info('Generated missing session ID for fresh login', [
                            'user_id' => $user->id,
                            'session_id' => $newSessionId,
                            'ip' => $request->ip()
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to generate session for fresh login', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                            'ip' => $request->ip()
                        ]);
                    }
                }
                
                return $next($request);
            }

            // Also skip for login/register redirects
            if ($request->is('user/dashboard') || $request->is('home') || $request->routeIs('user.dashboard')) {
                // For dashboard access, give it one more chance to set session properly
                if (!$sessionId) {
                    try {
                        $newSessionId = $user->generateNewSession();
                        session(['user_session_id' => $newSessionId, 'last_activity' => time()]);
                        
                        Log::info('Recovery: Generated session ID for dashboard access', [
                            'user_id' => $user->id,
                            'session_id' => $newSessionId,
                            'ip' => $request->ip()
                        ]);
                        
                        return $next($request);
                    } catch (\Exception $e) {
                        Log::error('Failed to recover session for dashboard', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // If session expired (419), skip forced logout and let Laravel handle it
            if ($request->isMethod('POST') && !$request->has('_token')) {
                // Let Laravel's CSRF handler show the 419 page
                return $next($request);
            }

            // ENHANCED: More aggressive session validation
            $isSessionValid = $this->validateUserSession($user, $sessionId, $request);
            
            if (!$isSessionValid) {
                // Determine the reason for logout
                $logoutReason = $this->getLogoutReason($user, $sessionId, $request);
                return $this->forceLogoutResponse($user, $request, $logoutReason);
            }

            // ENHANCED: Check for multiple tabs (especially for video gallery)
            if ($this->shouldCheckMultipleTabs($request)) {
                $multipleTabsResponse = $this->handleMultipleTabs($request, $user);
                if ($multipleTabsResponse) {
                    return $multipleTabsResponse;
                }
            }

            // Update session activity timestamp for tracking inactivity
            session(['last_activity' => time()]);
        }

        return $next($request);
    }

    /**
     * Validate user session with enhanced checks
     */
    private function validateUserSession($user, $sessionId, $request)
    {
        // Check if session ID exists
        if (!$sessionId) {
            Log::warning('No session ID found for authenticated user', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);
            return false;
        }

        // Check if session is valid in database
        if (!$user->isSessionValid($sessionId)) {
            Log::warning('Invalid session detected', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'stored_session' => $user->current_session_id,
                'ip' => $request->ip()
            ]);
            return false;
        }

        // ENHANCED: Check for session inactivity timeout (15 minutes)
        $lastActivity = session('last_activity');
        if ($lastActivity && (time() - $lastActivity) > 900) { // 900 seconds = 15 minutes
            Log::warning('Session expired due to inactivity', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'last_activity' => $lastActivity,
                'current_time' => time(),
                'inactive_duration' => (time() - $lastActivity),
                'ip' => $request->ip()
            ]);
            return false;
        }

        // Additional check: Verify IP consistency (optional, commented out for flexibility)
        // if ($user->session_ip_address && $user->session_ip_address !== $request->ip()) {
        //     Log::warning('IP address changed during session', [
        //         'user_id' => $user->id,
        //         'original_ip' => $user->session_ip_address,
        //         'current_ip' => $request->ip()
        //     ]);
        //     return false;
        // }

        return true;
    }

    /**
     * Check if this route requires multiple tab checking
     */
    private function shouldCheckMultipleTabs(Request $request): bool
    {
        // Routes that require single tab access
        $restrictedRoutes = [
            'user.video-views.gallery',
            'user.video-views.*', // Any video viewing route
        ];
        
        $currentRoute = $request->route()->getName();
        
        foreach ($restrictedRoutes as $pattern) {
            if (fnmatch($pattern, $currentRoute)) {
                return true;
            }
        }
        
        // Also check by URL pattern for direct URL access
        $restrictedPaths = [
            '/user/video-views/gallery',
            '/user/video-views',
        ];

        foreach ($restrictedPaths as $path) {
            if (str_contains($request->getPathInfo(), $path)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Handle multiple tabs detection
     */
    private function handleMultipleTabs(Request $request, User $user): ?Response
    {
        $tabId = $request->header('X-Tab-ID') ?: $request->input('tab_id') ?: session('tab_id');
        $userId = $user->id;
        $cacheKey = "user_tab_{$userId}";
        
        // Generate a new tab ID if none exists
        if (!$tabId) {
            $tabId = uniqid('tab_', true);
            session(['tab_id' => $tabId]);
        }
        
        // Get currently active tab ID
        $activeTabId = Cache::get($cacheKey);
        
        if ($activeTabId && $activeTabId !== $tabId) {
            // Log multiple tab attempt
            Log::warning('Multiple tab access attempt detected', [
                'user_id' => $user->id,
                'username' => $user->username,
                'active_tab' => $activeTabId,
                'current_tab' => $tabId,
                'route' => $request->route() ? $request->route()->getName() : 'unknown',
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Different tab ID means multiple tabs
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Multiple tabs detected for video viewing',
                    'redirect' => route('user.dashboard'),
                    'tab_restriction' => true
                ], 403);
            } else {
                // For regular requests, redirect to dashboard
                session()->flash('error', 'Video viewing is restricted to one tab at a time.');
                return redirect()->route('user.dashboard');
            }
        }
        
        // Set or update the active tab ID (expires in 30 minutes)
        Cache::put($cacheKey, $tabId, now()->addMinutes(30));
        session(['tab_id' => $tabId]);
        
        return null; // Allow request to continue
    }

    /**
     * Determine the reason for logout
     */
    private function getLogoutReason($user, $sessionId, $request)
    {
        // Check if no session ID exists
        if (!$sessionId) {
            return 'no_session';
        }

        // Check if session is invalid in database
        if (!$user->isSessionValid($sessionId)) {
            return 'session_conflict';
        }

        // Check if session expired due to inactivity (15 minutes)
        $lastActivity = session('last_activity');
        if ($lastActivity && (time() - $lastActivity) > 900) { // 900 seconds = 15 minutes
            return 'inactivity_timeout';
        }

        // Check for multiple tabs issue
        if ($this->shouldCheckMultipleTabs($request)) {
            $currentTabId = $request->header('X-Tab-ID') ?: $request->get('tab_id') ?: session('tab_id');
            $activeTabKey = "user_tab_{$user->id}";
            $activeTabId = Cache::get($activeTabKey);
            
            if ($activeTabId && $activeTabId !== $currentTabId) {
                return 'multiple_tabs';
            }
        }

        return 'unknown';
    }

    /**
     * Get appropriate message data based on logout reason
     */
    private function getLogoutMessageData($user, $request, $logoutReason)
    {
        switch ($logoutReason) {
            case 'inactivity_timeout':
                return [
                    'type' => 'session_timeout',
                    'title' => 'Session Timeout',
                    'message' => 'Your session has expired after 15 minutes of inactivity. Please log in again to continue.',
                    'ip' => $request->ip(),
                    'redirect_message' => 'Your session has expired after 15 minutes of inactivity. Please log in again to continue using the platform.'
                ];

            case 'session_conflict':
                $newLoginInfo = '';
                if ($user->session_created_at && $user->session_ip_address) {
                    $loginTime = $user->session_created_at->format('M d, Y \a\t h:i A');
                    $loginIP = $user->session_ip_address;
                    $newLoginInfo = " A new login was detected on {$loginTime} from IP: {$loginIP}.";
                }
                return [
                    'type' => 'session_terminated',
                    'title' => 'Session Terminated',
                    'message' => 'Your session was terminated because someone else logged into your account from another device.',
                    'ip' => $user->session_ip_address,
                    'redirect_message' => 'You have been logged out because your account was accessed from another device or browser.' . $newLoginInfo . ' Only one active session is allowed per account.'
                ];

            case 'no_session':
                return [
                    'type' => 'session_invalid',
                    'title' => 'Session Invalid',
                    'message' => 'Your session is no longer valid. Please log in again.',
                    'ip' => $request->ip(),
                    'redirect_message' => 'Your session is no longer valid. Please log in again to continue.'
                ];

            case 'multiple_tabs':
                return [
                    'type' => 'multiple_tabs_detected',
                    'title' => 'Multiple Tabs Detected',
                    'message' => 'Multiple tabs were detected for video viewing. Only one tab is allowed at a time.',
                    'ip' => $request->ip(),
                    'redirect_message' => 'Access denied: Video viewing is restricted to one tab at a time. Please close other tabs and try again.'
                ];

            default:
                return [
                    'type' => 'session_ended',
                    'title' => 'Session Ended',
                    'message' => 'Your session has ended. Please log in again.',
                    'ip' => $request->ip(),
                    'redirect_message' => 'Your session has ended. Please log in again to continue.'
                ];
        }
    }

    /**
     * Force logout response with enhanced cleanup
     */
    private function forceLogoutResponse($user, $request, $logoutReason = 'unknown')
    {
        // Prepare messages based on logout reason
        $messageData = $this->getLogoutMessageData($user, $request, $logoutReason);

        // Create database notification for this logout event
        try {
            \Illuminate\Support\Facades\DB::table('user_session_notifications')->insert([
                'user_id' => $user->id,
                'type' => $messageData['type'],
                'title' => $messageData['title'],
                'message' => $messageData['message'],
                'new_login_ip' => $messageData['ip'],
                'new_login_device' => $this->getDeviceInfo($request->userAgent()),
                'old_session_ip' => $request->ip(),
                'old_session_duration' => $user->session_created_at ? $user->session_created_at->diffForHumans(now(), true) : 'Unknown',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }

        // Log the forced logout
        Log::info('User forced logout due to single session enforcement', [
            'user_id' => $user->id,
            'username' => $user->username,
            'current_session' => session('user_session_id'),
            'stored_session' => $user->current_session_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'new_login_time' => $user->session_created_at,
            'new_login_ip' => $user->session_ip_address,
        ]);

        // Force logout for all requests except AJAX without valid token
        if ($request->isMethod('GET') || $request->has('_token') || $request->expectsJson()) {
            Auth::logout();
            session()->flush();
            session()->regenerate();

            // Handle AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Your account was accessed from another device.',
                    'redirect' => route('login'),
                    'force_logout' => true
                ], 401);
            }

            // Redirect with detailed message
            return redirect()->route('login')
                ->with('warning', $messageData['redirect_message']);
        }

        return response('Unauthorized', 401);
    }

    /**
     * Get device info from user agent.
     */
    private function getDeviceInfo($userAgent)
    {
        $device = 'Unknown Device';
        
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $device = 'Mobile Device';
        } elseif (strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'Tablet') !== false) {
            $device = 'Tablet';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $device = 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $device = 'Mac Computer';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $device = 'Linux Computer';
        }
        
        // Add browser info
        if (strpos($userAgent, 'Chrome') !== false) {
            $device .= ' (Chrome)';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $device .= ' (Firefox)';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $device .= ' (Safari)';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $device .= ' (Edge)';
        }
        
        return $device;
    }
}
