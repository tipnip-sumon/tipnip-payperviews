<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FreshLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users accessing specific routes
        if (Auth::check()) {
            $response = $next($request);
            
            // Check if this is a fresh login - only on first dashboard access
            $isFreshLogin = session('fresh_login');
            
            if ($isFreshLogin && $request->routeIs('user.dashboard')) {
                // Clear the flag immediately to prevent repeated processing
                session()->forget('fresh_login');
                
                // Add light cache-clearing headers (non-aggressive)
                $response->headers->add([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
                    'X-Fresh-Session' => 'true'
                ]);
                
                // Set a flag that fresh login processing is complete
                session(['fresh_login_processed' => true]);
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
