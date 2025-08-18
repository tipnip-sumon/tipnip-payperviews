<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AutoSessionRecovery
{
    /**
     * Handle an incoming request and automatically recover from session issues.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check for session issues after logout
            if ($this->hasSessionIssue($request)) {
                return $this->handleSessionRecovery($request);
            }
            
            return $next($request);
            
        } catch (\Exception $e) {
            Log::warning('Session recovery middleware error', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);
            
            // If middleware fails, continue with request
            return $next($request);
        }
    }
    
    /**
     * Detect if there's a session issue
     */
    private function hasSessionIssue(Request $request)
    {
        // Check for common session issue indicators
        $indicators = [
            // URL contains session error parameters
            $request->has('session_error'),
            $request->has('session_expired'),
            
            // Referrer indicates logout but user appears authenticated in session
            $this->isPostLogoutWithStaleSession($request),
            
            // Session ID exists but user data is inconsistent
            $this->hasInconsistentSessionState($request),
            
            // Flash message indicates session issue
            session()->has('session_issue') || session()->has('session_timeout')
        ];
        
        return collect($indicators)->contains(true);
    }
    
    /**
     * Check if this is a post-logout request with stale session
     */
    private function isPostLogoutWithStaleSession(Request $request)
    {
        $referer = $request->header('referer', '');
        
        // If coming from logout and still has session data
        if (str_contains($referer, 'logout') || str_contains($referer, 'force-logout')) {
            return session()->has('_token') && !Auth::check();
        }
        
        return false;
    }
    
    /**
     * Check for inconsistent session state
     */
    private function hasInconsistentSessionState(Request $request)
    {
        // If session has user ID but Auth doesn't recognize user
        if (session()->has('login_user_id') && !Auth::check()) {
            return true;
        }
        
        // If session is very old but still active
        $lastActivity = session('last_activity_time', time());
        $inactiveMinutes = (time() - $lastActivity) / 60;
        
        if ($inactiveMinutes > 60 && Auth::check()) { // More than 1 hour
            return true;
        }
        
        return false;
    }
    
    /**
     * Handle session recovery
     */
    private function handleSessionRecovery(Request $request)
    {
        Log::info('Auto session recovery triggered', [
            'url' => $request->url(),
            'referer' => $request->header('referer'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Perform clean session reset
        $this->performCleanSessionReset($request);
        
        // Determine where to redirect
        $redirectUrl = $this->getRecoveryRedirectUrl($request);
        
        // For AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'session_recovered',
                'message' => 'Session has been automatically recovered.',
                'redirect' => $redirectUrl,
                'action' => 'auto_recovery'
            ], 200);
        }
        
        // For regular requests, redirect with success message
        return redirect($redirectUrl)->with([
            'success' => 'Session automatically recovered. You can continue browsing.',
            'session_recovered' => true
        ]);
    }
    
    /**
     * Perform clean session reset
     */
    private function performCleanSessionReset(Request $request)
    {
        try {
            // Ensure user is logged out
            if (Auth::check()) {
                Auth::logout();
            }
            
            // Clear problematic session data but keep important stuff
            $preserveKeys = ['_token', 'locale', 'timezone', 'currency'];
            $preservedData = [];
            
            foreach ($preserveKeys as $key) {
                if (session()->has($key)) {
                    $preservedData[$key] = session($key);
                }
            }
            
            // Flush session
            session()->flush();
            
            // Restore preserved data
            foreach ($preservedData as $key => $value) {
                session([$key => $value]);
            }
            
            // Regenerate session ID for security
            session()->regenerate(true);
            
            Log::info('Clean session reset completed', [
                'preserved_keys' => array_keys($preservedData),
                'new_session_id' => session()->getId()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Session reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Determine where to redirect after recovery
     */
    private function getRecoveryRedirectUrl(Request $request)
    {
        // If trying to access login page, go there
        if (str_contains($request->path(), 'login')) {
            return route('login');
        }
        
        // If trying to access register page, go there
        if (str_contains($request->path(), 'register')) {
            return route('register');
        }
        
        // If trying to access dashboard/admin, redirect to login
        if (str_contains($request->path(), 'dashboard') || str_contains($request->path(), 'admin')) {
            return route('login');
        }
        
        // For most other pages, go to home
        return route('home', [], false) ?: '/';
    }
}
