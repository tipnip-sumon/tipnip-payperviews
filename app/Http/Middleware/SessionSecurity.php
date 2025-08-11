<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurity
{
    /**
     * Handle an incoming request.
     * Prevents cross-user session access and enforces session security.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users
        if (Auth::check()) {
            $currentUser = Auth::user();
            $userId = $currentUser->id;
            
            // Get the session user ID that was stored during login
            $sessionUserId = session('auth_user_id');
            
            // If session user ID doesn't match current authenticated user, security violation
            if ($sessionUserId && $sessionUserId != $userId) {
                Log::warning('Session security violation detected', [
                    'session_user_id' => $sessionUserId,
                    'authenticated_user_id' => $userId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl()
                ]);
                
                // Force logout and redirect to login
                Auth::logout();
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Session security violation detected. Please login again.')
                    ->with('security_logout', true);
            }
            
            // Store/update the authenticated user ID in session for validation
            if (!$sessionUserId) {
                session(['auth_user_id' => $userId]);
            }
            
            // Check for concurrent session limit (single session per user)
            $this->enforceSingleSession($currentUser, $request);
        }
        
        return $next($request);
    }
    
    /**
     * Enforce single session per user policy
     */
    private function enforceSingleSession($user, $request)
    {
        try {
            $currentSessionId = session()->getId();
            $storedSessionId = $user->current_session_id ?? null;
            
            // If user has a different active session, this is a concurrent login
            if ($storedSessionId && $storedSessionId !== $currentSessionId) {
                Log::info('Concurrent session detected for user', [
                    'user_id' => $user->id,
                    'stored_session' => $storedSessionId,
                    'current_session' => $currentSessionId,
                    'ip' => $request->ip()
                ]);
                
                // Update to current session (newest login wins)
                $user->update(['current_session_id' => $currentSessionId]);
                
                // Clear any old session data
                \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $currentSessionId)
                    ->delete();
            }
            
        } catch (\Exception $e) {
            Log::warning('Session enforcement error: ' . $e->getMessage());
        }
    }
}
