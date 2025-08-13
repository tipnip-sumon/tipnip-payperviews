<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearLoginCache
{
    /**
     * Handle an incoming request and clear cache headers for login pages.
     * This middleware is designed to prevent login form caching while preserving
     * session functionality and CSRF token handling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Less aggressive cache control that preserves session functionality
        // This prevents form caching but allows session cookies and CSRF tokens to work
        return $response->header('Cache-Control', 'no-cache, private, must-revalidate')
                       ->header('Pragma', 'no-cache')
                       ->header('X-Frame-Options', 'DENY')
                       ->header('X-Content-Type-Options', 'nosniff')
                       ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
