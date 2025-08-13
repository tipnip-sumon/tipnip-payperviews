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
        // In Laravel 11, we should just throw the exception and let it handle the redirect
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}
