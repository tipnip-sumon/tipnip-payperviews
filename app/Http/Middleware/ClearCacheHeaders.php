<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add headers to prevent caching and clear browser cache
        return $response
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->header('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"')
            ->header('X-Cache-Clear', 'active')
            ->header('X-Frame-Options', 'DENY')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
