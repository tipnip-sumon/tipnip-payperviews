<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.index')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();
        
        // Check if user is valid and active
        if (!$user || $user->status == 0) {
            Auth::logout();
            return redirect()->route('admin.index')->with('error', 'Account is inactive.');
        }

        // Check admin privileges (adjust based on your user table)
        if (!$this->isAdmin($user)) {
            return redirect()->route('home')->with('error', 'Admin access required.');
        }

        return $next($request);
    }

    private function isAdmin($user)
    {
        // Adjust these conditions based on your user table structure
        return $user->is_admin == 1 || 
               $user->role === 'admin' || 
               $user->user_type === 'admin' ||
               $user->admin == 1;
    }
}
