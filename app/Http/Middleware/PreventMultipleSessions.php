<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PreventMultipleSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is a CSRF refresh request
        if ($request->is('csrf-refresh') || $request->wantsJson()) {
            // For CSRF refresh requests, use existing session if available
            $sessionId = $request->session()->getId();
            
            if (!$sessionId) {
                // Only start new session if none exists
                $request->session()->start();
                Log::info('New session started for CSRF refresh', ['session_id' => $request->session()->getId()]);
            } else {
                Log::info('Using existing session for CSRF refresh', ['session_id' => $sessionId]);
            }
        }
        
        // For form submissions, ensure we use the same session
        if ($request->isMethod('POST') && $request->has('_token')) {
            $sessionId = $request->session()->getId();
            
            if (!$sessionId) {
                Log::warning('No session ID found for form submission, starting new session');
                $request->session()->start();
            }
            
            // Validate CSRF token properly
            if (!$request->session()->token()) {
                $request->session()->regenerateToken();
                Log::info('Regenerated CSRF token for session', ['session_id' => $request->session()->getId()]);
            }
        }
        
        return $next($request);
    }
}
