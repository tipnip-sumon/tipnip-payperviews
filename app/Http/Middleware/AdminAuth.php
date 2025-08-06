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
        if ($path == 'admin' || session('admin')) {
            if ($adminSession) {
                return redirect()->route('admin.dashboard')->with('success', 'Already Logged In.');
            }
        } else {
            if (!$adminSession) {
                return redirect()->route('admin.index')->with('error', 'Please Login First.');
            }
        }
        return $next($request);
    }
}
