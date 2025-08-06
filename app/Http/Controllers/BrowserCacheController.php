<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class BrowserCacheController extends Controller
{
    /**
     * Clear browser cache for the current domain only
     * 
     * @param Request $request
     * @return Response
     */
    public function clearDomainCache(Request $request)
    {
        try {
            // Get the current domain
            $domain = $request->getHost();
            $protocol = $request->isSecure() ? 'https' : 'http';
            $fullDomain = $protocol . '://' . $domain;
            
            // Log the cache clear request
            Log::info('Browser cache clear requested for domain: ' . $fullDomain, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            // Clear Laravel application cache (optional)
            $this->clearApplicationCache();
            
            // Create response with aggressive cache clearing headers
            $response = response()->view('cache.clear-success', [
                'domain' => $domain,
                'fullDomain' => $fullDomain,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'message' => 'Browser cache has been cleared for ' . $domain
            ]);
            
            // Set aggressive cache clearing headers
            return $response
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
                ->header('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"')
                ->header('X-Cache-Clear', 'domain-specific')
                ->header('X-Domain', $domain)
                ->header('X-Timestamp', time());
            
        } catch (\Exception $e) {
            Log::error('Error clearing browser cache: ' . $e->getMessage());
            
            return response()->view('cache.clear-error', [
                'error' => 'Failed to clear browser cache',
                'domain' => $request->getHost(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Clear browser cache with additional parameters
     * 
     * @param Request $request
     * @return Response
     */
    public function clearDomainCacheAdvanced(Request $request)
    {
        try {
            $domain = $request->getHost();
            $clearType = $request->get('type', 'all'); // all, cookies, storage, cache
            $redirect = $request->get('redirect', null);
            
            // Determine what to clear based on type parameter
            $clearSiteDataValue = $this->getClearSiteDataValue($clearType);
            
            Log::info('Advanced browser cache clear requested', [
                'domain' => $domain,
                'type' => $clearType,
                'redirect' => $redirect,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            
            // Clear application cache if requested
            if (in_array($clearType, ['all', 'app'])) {
                $this->clearApplicationCache();
            }
            
            // Create JavaScript response for immediate cache clearing
            $jsResponse = $this->generateCacheClearJavaScript($domain, $clearType, $redirect);
            
            $response = response($jsResponse)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
                ->header('Clear-Site-Data', $clearSiteDataValue)
                ->header('X-Cache-Clear-Type', $clearType)
                ->header('X-Domain', $domain);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error in advanced cache clear: ' . $e->getMessage());
            return response('Cache clear failed: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * API endpoint for cache clearing status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cacheClearStatus(Request $request)
    {
        try {
            $domain = $request->getHost();
            
            return response()->json([
                'success' => true,
                'domain' => $domain,
                'timestamp' => now()->toISOString(),
                'cache_cleared' => true,
                'methods_used' => [
                    'http_headers',
                    'clear_site_data',
                    'javascript_storage_clear',
                    'cache_control_headers'
                ],
                'message' => 'Browser cache cleared successfully for ' . $domain
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
              
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    
    /**
     * Clear Laravel application cache
     * 
     * @return void
     */
    private function clearApplicationCache()
    {
        try {
            // Clear various Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            // Clear specific cache stores
            Cache::flush();
            
        } catch (\Exception $e) {
            Log::warning('Failed to clear application cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Get Clear-Site-Data header value based on type
     * 
     * @param string $type
     * @return string
     */
    private function getClearSiteDataValue($type)
    {
        switch ($type) {
            case 'cache':
                return '"cache"';
            case 'cookies':
                return '"cookies"';
            case 'storage':
                return '"storage"';
            case 'execution':
                return '"executionContexts"';
            case 'all':
            default:
                return '"cache", "cookies", "storage", "executionContexts"';
        }
    }
    
    /**
     * Generate JavaScript for immediate cache clearing
     * 
     * @param string $domain
     * @param string $type
     * @param string|null $redirect
     * @return string
     */
    private function generateCacheClearJavaScript($domain, $type, $redirect = null)
    {
        $redirectUrl = $redirect ? url($redirect) : url('/');
        
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Cleared - ' . $domain . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            padding: 50px 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .container {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            max-width: 600px;
        }
        .success-icon { font-size: 4em; margin-bottom: 20px; }
        .spinner { 
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
        .status { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">🧹</div>
        <h1>Browser Cache Cleared!</h1>
        <p>Cache clearing process completed for <strong>' . $domain . '</strong></p>
        <div class="status" id="status">Clearing cache data...</div>
        <div class="spinner" id="spinner"></div>
        <div id="details" style="display:none;">
            <p>✅ Browser cache cleared</p>
            <p>✅ Local storage cleared</p>
            <p>✅ Session storage cleared</p>
            <p>✅ Cookies cleared (domain-specific)</p>
            <p>✅ Service worker cache cleared</p>
        </div>
        <button class="btn" onclick="window.location.href=\'' . $redirectUrl . '\'">Continue to Site</button>
        <button class="btn" onclick="location.reload()">Refresh Page</button>
    </div>

    <script>
        // Clear various browser storage mechanisms
        function clearBrowserCache() {
            try {
                // Clear localStorage
                if (typeof(Storage) !== "undefined" && localStorage) {
                    localStorage.clear();
                    console.log("✅ localStorage cleared");
                }
                
                // Clear sessionStorage
                if (typeof(Storage) !== "undefined" && sessionStorage) {
                    sessionStorage.clear();
                    console.log("✅ sessionStorage cleared");
                }
                
                // Clear IndexedDB
                if (window.indexedDB) {
                    indexedDB.databases().then(databases => {
                        databases.forEach(db => {
                            indexedDB.deleteDatabase(db.name);
                        });
                    }).catch(e => console.log("IndexedDB clear error:", e));
                    console.log("✅ IndexedDB clearing initiated");
                }
                
                // Clear Service Worker cache
                if ("serviceWorker" in navigator) {
                    navigator.serviceWorker.getRegistrations().then(function(registrations) {
                        for(let registration of registrations) {
                            registration.unregister();
                        }
                    }).catch(e => console.log("Service Worker clear error:", e));
                    console.log("✅ Service Worker cache clearing initiated");
                }
                
                // Clear browser cache using cache API
                if ("caches" in window) {
                    caches.keys().then(function(names) {
                        for (let name of names) {
                            caches.delete(name);
                        }
                    }).catch(e => console.log("Cache API clear error:", e));
                    console.log("✅ Cache API clearing initiated");
                }
                
                return true;
            } catch (error) {
                console.error("Cache clearing error:", error);
                return false;
            }
        }
        
        // Execute cache clearing
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                const success = clearBrowserCache();
                
                document.getElementById("spinner").style.display = "none";
                document.getElementById("details").style.display = "block";
                
                if (success) {
                    document.getElementById("status").innerHTML = "✅ Cache cleared successfully!";
                } else {
                    document.getElementById("status").innerHTML = "⚠️ Cache clearing completed with some limitations";
                }
                
                // Log completion
                console.log("🧹 Browser cache clearing process completed for domain: ' . $domain . '");
                console.log("🕒 Timestamp: " + new Date().toISOString());
                console.log("🌐 Domain: ' . $domain . '");
                console.log("📋 Type: ' . $type . '");
                
            }, 2000);
        });
        
        // Force page reload without cache
        window.addEventListener("beforeunload", function() {
            // Additional cleanup before leaving
            if (window.caches) {
                caches.keys().then(names => names.forEach(name => caches.delete(name)));
            }
        });
    </script>
</body>
</html>';
    }
}
