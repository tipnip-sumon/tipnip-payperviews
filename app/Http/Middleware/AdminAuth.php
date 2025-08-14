<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        
        // Only apply admin authentication logic to admin routes
        if (!str_starts_with($path, 'admin')) {
            // This middleware should not apply to non-admin routes
            return $next($request);
        }
        
        $adminSession = $request->session()->get('admin');
        
        if ($path == 'admin') {
            // Admin root path
            if ($adminSession) {
                return redirect()->route('admin.dashboard')->with('success', 'Already Logged In.');
            }
            // Allow access to login page if not authenticated
        } else {
            // Admin sub-paths - require authentication
            if (!$adminSession) {
                return redirect()->route('admin.index')->with('error', 'Please Login First.');
            }
        }
        
        return $next($request);
    }
}
