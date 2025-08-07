<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminSessionHandler
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
        // Check if this is an admin route
        if ($request->is('admin/*')) {
            // Check if user is authenticated with admin guard
            if (!Auth::guard('admin')->check()) {
                // Log the session timeout
                Log::info('Admin session expired or invalid', [
                    'ip_address' => $request->ip(),
                    'route' => $request->route()->getName(),
                    'url' => $request->url(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()
                ]);

                // Clear any remaining session data
                $request->session()->forget([
                    'admin', 
                    'admin_id', 
                    'name', 
                    'username', 
                    'role', 
                    'is_super_admin',
                    'last_activity'
                ]);

                // Handle AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired. Please login again.',
                        'redirect' => route('admin.index'),
                        'session_expired' => true,
                        'timestamp' => time()
                    ], 401);
                }

                // For regular requests, redirect to login with message
                return redirect()->route('admin.index')
                    ->with('error', 'Your session has expired. Please login again.')
                    ->with('session_expired', true);
            }

            // Update last activity if session is valid
            $request->session()->put('last_activity', time());
        }

        return $next($request);
    }
}
