<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CacheControlMiddleware
{
    /**
     * Handle an incoming request and add cache control headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Only add cache headers for authenticated users
            if (Auth::check()) {
                // For HTML pages, prevent caching
                if ($request->acceptsHtml()) {
                    $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
                    $response->headers->set('Pragma', 'no-cache');
                    $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
                    $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
                    
                    // Add cache version header for JavaScript detection
                    $cacheVersion = config('app.cache_version', time());
                    $appVersion = config('app.version', '1.0.0');
                    
                    $response->headers->set('X-Cache-Version', $cacheVersion);
                    $response->headers->set('X-App-Version', $appVersion);
                }
                
                // For AJAX requests, add additional headers
                if ($request->ajax() || $request->wantsJson()) {
                    $response->headers->set('X-Cache-Control', 'no-cache');
                    $response->headers->set('X-Timestamp', time());
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the request
            Log::warning('CacheControlMiddleware error: ' . $e->getMessage());
        }

        return $response;
    }
}
