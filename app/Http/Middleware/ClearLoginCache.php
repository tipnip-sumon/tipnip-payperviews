<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearLoginCache
{
    /**
     * Handle an incoming request and clear cache headers for login pages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Clear browser cache for login pages
        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                       ->header('Pragma', 'no-cache')
                       ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT')
                       ->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
                       ->header('X-Frame-Options', 'DENY')
                       ->header('X-Content-Type-Options', 'nosniff')
                       ->header('Referrer-Policy', 'no-referrer');
    }
}
