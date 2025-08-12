<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        
        // Log unauthenticated access attempts for debugging
        Log::info('Unauthenticated access attempt', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId()
        ]);
        
        return route('login'); // Simple redirect without parameters
    }

    /**
     * Handle an unauthenticated user with enhanced response headers.
     */
    protected function unauthenticated($request, array $guards)
    {
        $response = redirect($this->redirectTo($request))
            ->with('info', 'Please log in to continue.');
            
        // Add cache control headers to prevent caching issues
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}
