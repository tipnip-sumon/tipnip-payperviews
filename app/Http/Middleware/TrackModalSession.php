<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TrackModalSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set session start time if not already set
        if (!Session::has('session_start_time')) {
            Session::put('session_start_time', now());
        }
        
        // Detect mobile device and set attribute
        $userAgent = $request->header('User-Agent');
        $isMobile = $this->isMobileDevice($userAgent);
        $request->attributes->set('is_mobile', $isMobile);
        
        return $next($request);
    }
    
    /**
     * Detect if the user agent is a mobile device
     */
    private function isMobileDevice($userAgent)
    {
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 
            'Windows Phone', 'Opera Mini', 'IEMobile', 'Mobile Safari'
        ];
        
        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
