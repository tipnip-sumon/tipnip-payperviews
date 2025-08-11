<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOptimized
{
    /**
     * Handle an incoming request - Optimized for admin panel performance
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Routes that need fresh CSRF tokens and no caching
        $csrfSensitiveRoutes = [
            'admin.transfer-withdraw-conditions',
            'admin.settings',
            'admin.users.edit',
            'admin.deposits.approve',
            'admin.withdrawals.approve',
            'admin.profile.update',
            'admin.change-password'
        ];
        
        // Routes that can have moderate caching
        $moderateCacheRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.deposits.index',
            'admin.withdrawals.index'
        ];
        
        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $isPostRequest = $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH');
        
        // For POST/PUT/PATCH requests or CSRF-sensitive routes - no caching
        if ($isPostRequest || in_array($currentRoute, $csrfSensitiveRoutes) || str_contains($currentRoute, 'update') || str_contains($currentRoute, 'store')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        } elseif (in_array($currentRoute, $moderateCacheRoutes)) {
            // Moderate caching for dashboard and listing pages
            $response->headers->set('Cache-Control', 'private, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        } else {
            // Allow short-term caching for static admin resources
            $response->headers->set('Cache-Control', 'private, max-age=180'); // 3 minutes
        }
        
        // Security headers (always apply these)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        return $response;
    }
}
