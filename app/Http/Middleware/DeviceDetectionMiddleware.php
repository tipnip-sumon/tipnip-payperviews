<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DeviceDetectionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for screen width in cookie
        $screenWidth = $request->cookie('screen_width');
        if ($screenWidth) {
            $request->headers->set('X-Screen-Width', $screenWidth);
        }
        
        // Store device type in request for easy access
        $userAgent = $request->header('User-Agent', '');
        $screenWidth = $request->header('X-Screen-Width', 0);
        
        $isMobile = $this->detectMobileDevice($userAgent, $screenWidth);
        $request->attributes->set('is_mobile', $isMobile);
        $request->attributes->set('device_type', $isMobile ? 'mobile' : 'desktop');
        
        return $next($request);
    }
    
    /**
     * Detect if the device is mobile
     */
    private function detectMobileDevice(string $userAgent, int $screenWidth): bool
    {
        // Screen width detection (priority)
        if ($screenWidth > 0 && $screenWidth <= 991) {
            return true;
        }
        
        // User agent detection
        $mobilePatterns = [
            '/android/i',
            '/webos/i',
            '/iphone/i',
            '/ipad/i',
            '/ipod/i',
            '/blackberry/i',
            '/iemobile/i',
            '/mobile/i',
            '/phone/i'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }
        
        return false;
    }
}
