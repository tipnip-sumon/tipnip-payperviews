<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\helpers\PermissionHelper;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The required permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated as admin
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access this area.');
        }

        // Check if admin has the required permission
        if (!PermissionHelper::hasPermission($permission)) {
            return redirect()->back()->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
