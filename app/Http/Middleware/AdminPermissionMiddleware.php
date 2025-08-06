<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.index')->with('error', 'Please login first.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.index')->with('error', 'Your account has been deactivated.');
        }
        
        if (!$admin->canDo($permission)) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}
