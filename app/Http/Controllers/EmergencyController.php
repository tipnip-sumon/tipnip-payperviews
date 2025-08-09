<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class EmergencyController extends Controller
{
    /**
     * Emergency cache clear for live server issues
     */
    public function emergencyCacheClear(Request $request)
    {
        try {
            // Log the emergency action
            Log::info('Emergency cache clear initiated', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            // Clear all caches
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            // Clear compiled assets
            if (file_exists(public_path('mix-manifest.json'))) {
                unlink(public_path('mix-manifest.json'));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Emergency cache clear completed',
                'timestamp' => now(),
                'version' => time()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Emergency cache clear failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Emergency cache clear failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get system status for troubleshooting
     */
    public function systemStatus()
    {
        $status = [
            'cache_status' => Cache::has('system_check') ? 'active' : 'cleared',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'storage_writable' => is_writable(storage_path()),
            'cache_writable' => is_writable(storage_path('framework/cache')),
            'views_writable' => is_writable(storage_path('framework/views')),
            'assets_exist' => [
                'bootstrap_css' => file_exists(public_path('assets/libs/bootstrap/css/bootstrap.min.css')),
                'bootstrap_js' => file_exists(public_path('assets/libs/bootstrap/js/bootstrap.bundle.min.js')),
                'custom_js' => file_exists(public_path('assets/js/custom.js')),
                'mobile_functions' => file_exists(public_path('assets_custom/js/mobile-functions.js')),
                'emergency_fix' => file_exists(public_path('emergency-cache-fix.js'))
            ],
            'timestamp' => now()
        ];
        
        return response()->json($status);
    }
    
    /**
     * Force asset refresh for specific user
     */
    public function forceAssetRefresh(Request $request)
    {
        $version = time();
        
        return response()->json([
            'success' => true,
            'version' => $version,
            'cache_buster' => "?v={$version}",
            'message' => 'Asset refresh forced',
            'assets' => [
                'css' => [
                    asset("assets/libs/bootstrap/css/bootstrap.min.css?v={$version}"),
                    asset("assets/css/styles.min.css?v={$version}"),
                    asset("assets/css/icons.css?v={$version}")
                ],
                'js' => [
                    asset("assets/libs/bootstrap/js/bootstrap.bundle.min.js?v={$version}"),
                    asset("assets/js/custom.js?v={$version}"),
                    asset("assets_custom/js/mobile-functions.js?v={$version}"),
                    asset("emergency-cache-fix.js?v={$version}")
                ]
            ]
        ]);
    }
    
    /**
     * Health check endpoint
     */
    public function healthCheck()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'version' => config('app.version', '1.0.0'),
            'cache_version' => time()
        ]);
    }
}
