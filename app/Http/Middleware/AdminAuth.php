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
        $adminSession = $request->session()->get('admin');
        
        // Only apply admin logic for admin routes
        if (str_starts_with($path, 'admin')) {
            if ($path == 'admin') {
                // Admin login page - redirect if already logged in
                if ($adminSession) {
                    return redirect()->route('admin.dashboard')->with('success', 'Already Logged In.');
                }
            } else {
                // Other admin routes - require admin session
                if (!$adminSession) {
                    return redirect()->route('admin.index')->with('error', 'Please Login First.');
                }
            }
        }
        
        return $next($request);
    }
}
