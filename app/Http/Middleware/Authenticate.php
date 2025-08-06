<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        
        // Add parameters to login URL for cache busting and tracking
        $loginUrl = route('login', [
            'from_logout' => '1',
            'redirect_from' => 'dashboard',
            't' => time(),
            'cache_bust' => uniqid()
        ]);
        
        return $loginUrl;
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        // Add cache-busting headers to prevent browser caching issues
        $response = redirect()->guest($this->redirectTo($request));
        
        $response->headers->add([
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff'
        ]);
        
        return $response;
    }
}
