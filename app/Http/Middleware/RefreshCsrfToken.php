<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class RefreshCsrfToken
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
        // Check if this is a POST request with a token mismatch
        if ($request->isMethod('post') && !$this->tokensMatch($request)) {
            // If AJAX request, return JSON response with new token
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Page expired due to inactivity.',
                    'csrf_token' => csrf_token(),
                    'redirect' => $request->url()
                ], 419);
            }
            
            // For regular requests, redirect back with error and new token
            return redirect()->back()
                ->withInput($request->except('_token', 'password'))
                ->withErrors(['csrf' => 'Your session has expired. Please try again.'])
                ->with('csrf_token', csrf_token());
        }
        
        return $next($request);
    }
    
    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);
        
        return is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);
    }
    
    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = \Illuminate\Cookie\CookieValuePrefix::remove(
                    decrypt($header)
                );
            } catch (\Exception $e) {
                $token = null;
            }
        }
        
        return $token;
    }
}
