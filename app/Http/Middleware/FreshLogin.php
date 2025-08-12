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
        // Simplified fresh login handling
        if (Auth::check() && $request->routeIs('user.dashboard')) {
            // Clear any login-related flags to prevent session bloat
            session()->forget(['fresh_login', 'fresh_login_processed', 'login_success']);
        }
        
        return $next($request);
    }
}
