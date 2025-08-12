<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [  
        'admin/logout',
        '/admin/logout',
        'admin/emergency-logout',
        '/admin/emergency-logout',
        'logout',
        '/logout',
        'simple-logout',
        '/simple-logout',
        'api/*', // API routes typically don't need CSRF
        'user/withdraw', // Temporarily exclude withdrawal POST to test CSRF issues
        '/user/withdraw', // Temporarily exclude withdrawal POST to test CSRF issues
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Special handling for logout requests - allow them to proceed even with CSRF mismatch
            if ($this->isLogoutRequest($request)) {
                \Illuminate\Support\Facades\Log::info('CSRF bypassed for logout request', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                ]);
                
                // Continue with the request without CSRF verification
                return $next($request);
            }
            
            // Log CSRF token mismatches for debugging
            \Illuminate\Support\Facades\Log::warning('CSRF Token Mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
                'session_id' => $request->session()->getId(),
                'user_id' => Auth::check() ? Auth::user()->id : null,
            ]);

            // For AJAX requests, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'error_type' => 'csrf_mismatch',
                    'redirect' => url()->current()
                ], 419);
            }

            // For regular requests, redirect back with error
            return redirect()->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Security verification failed. Please try again.')
                ->with('csrf_error', true);
        }
    }

    /**
     * Check if the request is a logout request
     */
    protected function isLogoutRequest($request): bool
    {
        $logoutPaths = [
            'logout',
            '/logout',
            'simple-logout',
            '/simple-logout',
            'admin/logout',
            '/admin/logout',
            'admin/emergency-logout',
            '/admin/emergency-logout'
        ];
        
        $currentPath = $request->path();
        $currentUri = $request->getRequestUri();
        
        return in_array($currentPath, $logoutPaths) || 
               in_array($currentUri, $logoutPaths) ||
               in_array('/' . $currentPath, $logoutPaths);
    }
}
