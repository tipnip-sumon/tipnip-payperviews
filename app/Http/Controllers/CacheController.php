<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheController extends Controller
{
    /**
     * Get cache status for debugging device switching issues
     */
    public function status(Request $request)
    {
        $isMobile = $request->attributes->get('is_mobile', false);
        $deviceType = $request->attributes->get('device_type', 'desktop');
        $userAgent = $request->header('User-Agent', '');
        $screenWidth = $request->header('X-Screen-Width');

        return response()->json([
            'cache_status' => [
                'config_cached' => app()->configurationIsCached(),
                'routes_cached' => app()->routesAreCached(),
                'events_cached' => app()->eventsAreCached(),
            ],
            'device_info' => [
                'is_mobile' => $isMobile,
                'device_type' => $deviceType,
                'screen_width' => $screenWidth,
                'user_agent' => substr($userAgent, 0, 100) . '...',
            ],
            'session_info' => [
                'session_id' => session()->getId(),
                'driver' => config('session.driver'),
                'has_device_cache' => session()->has('device_detection'),
            ],
            'cache_info' => [
                'default_driver' => config('cache.default'),
                'has_device_cache' => Cache::has('device_detection'),
            ],
            'recommendations' => [
                'clear_browser_cache' => 'Use Ctrl+F5 or Cmd+Shift+R',
                'clear_server_cache' => 'Run: php artisan cache:clear-device',
                'manual_js_clear' => 'Use: window.clearPayPerViewsCache()',
            ]
        ]);
    }

    /**
     * Clear device-specific cache via API
     */
    public function clearDevice(Request $request)
    {
        try {
            // Clear device-specific cache keys
            Cache::forget('device_detection');
            Cache::forget('mobile_layout');
            Cache::forget('desktop_layout');
            
            // Clear session device data
            session()->forget(['device_detection', 'screen_width']);
            
            return response()->json([
                'success' => true,
                'message' => 'Device cache cleared successfully',
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear device cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
