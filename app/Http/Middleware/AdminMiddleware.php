<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.index')->with('error', 'Please login as admin.');
        }

        $user = Auth::user();
        
        // Check if user is admin (adjust this based on your user table structure)
        if (!$user->is_admin && $user->role !== 'admin' && $user->user_type !== 'admin') {
            return redirect()->route('home')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
