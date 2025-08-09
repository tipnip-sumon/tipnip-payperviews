/**
 * Cache Buster for Desktop/Mobile Layout Switching
 * Fixes cache issues when users switch between desktop and mobile versions
 */

(function() {
    'use strict';
    
    // Cache buster configuration
    const CACHE_BUSTER = {
        version: '1.0.0',
        storageKey: 'pv_device_cache',
        debug: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    };
    
    // Device detection
    function getCurrentDevice() {
        const width = window.innerWidth;
        const userAgent = navigator.userAgent.toLowerCase();
        const isMobileUA = /android|webos|iphone|ipad|ipod|blackberry|iemobile|mobile|phone/.test(userAgent);
        const isMobileScreen = width <= 991;
        
        return {
            type: (isMobileUA || isMobileScreen) ? 'mobile' : 'desktop',
            width: width,
            userAgent: userAgent,
            timestamp: Date.now()
        };
    }
    
    // Get stored device info
    function getStoredDevice() {
        try {
            const stored = localStorage.getItem(CACHE_BUSTER.storageKey);
            return stored ? JSON.parse(stored) : null;
        } catch (e) {
            if (CACHE_BUSTER.debug) console.warn('Failed to read stored device info:', e);
            return null;
        }
    }
    
    // Store current device info
    function storeCurrentDevice(deviceInfo) {
        try {
            localStorage.setItem(CACHE_BUSTER.storageKey, JSON.stringify(deviceInfo));
            if (CACHE_BUSTER.debug) console.log('✅ Device info stored:', deviceInfo);
        } catch (e) {
            if (CACHE_BUSTER.debug) console.warn('Failed to store device info:', e);
        }
    }
    
    // Clear browser cache
    function clearBrowserCache() {
        try {
            // Clear localStorage except our device tracking
            const deviceInfo = localStorage.getItem(CACHE_BUSTER.storageKey);
            localStorage.clear();
            if (deviceInfo) {
                localStorage.setItem(CACHE_BUSTER.storageKey, deviceInfo);
            }
            
            // Clear sessionStorage
            sessionStorage.clear();
            
            // Clear cookies (except essential ones)
            document.cookie.split(";").forEach(function(c) {
                const cookieName = c.replace(/^ +/, "").replace(/=.*/, "");
                // Don't clear essential cookies
                if (!['XSRF-TOKEN', 'laravel_session', 'screen_width'].includes(cookieName)) {
                    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                }
            });
            
            if (CACHE_BUSTER.debug) console.log('🧹 Browser cache cleared');
        } catch (e) {
            if (CACHE_BUSTER.debug) console.warn('Failed to clear browser cache:', e);
        }
    }
    
    // Force reload with cache busting (smarter version)
    function forceReloadWithCacheBust() {
        const currentUrl = new URL(window.location.href);
        
        // Avoid adding parameters if we're already on a dashboard or authenticated page
        const isAuthenticatedPage = currentUrl.pathname.includes('/user/') || 
                                   currentUrl.pathname.includes('/dashboard') ||
                                   currentUrl.pathname.includes('/admin/');
        
        if (isAuthenticatedPage) {
            // For authenticated pages, just do a simple cache-busted reload
            if (CACHE_BUSTER.debug) {
                console.log('🔄 Simple cache reload for authenticated page');
            }
            
            // Clear cache before reload
            clearBrowserCache();
            
            // Simple reload without URL parameters
            window.location.reload(true);
            return;
        }
        
        // For non-authenticated pages, use the full cache busting
        currentUrl.searchParams.set('cache_bust', Date.now());
        currentUrl.searchParams.set('device_switch', '1');
        currentUrl.searchParams.set('v', CACHE_BUSTER.version);
        
        if (CACHE_BUSTER.debug) {
            console.log('🔄 Force reloading with cache bust:', currentUrl.href);
        }
        
        // Clear cache before reload
        clearBrowserCache();
        
        // Force reload
        window.location.href = currentUrl.href;
    }
    
    // Check if device has changed and handle cache
    function checkDeviceChange() {
        const currentDevice = getCurrentDevice();
        const storedDevice = getStoredDevice();
        
        if (CACHE_BUSTER.debug) {
            console.log('🔍 Current device:', currentDevice);
            console.log('📱 Stored device:', storedDevice);
        }
        
        // If no stored device, just store current
        if (!storedDevice) {
            storeCurrentDevice(currentDevice);
            return false;
        }
        
        // Check if device type changed
        const deviceChanged = storedDevice.type !== currentDevice.type;
        
        // Check if significant width change (more than 300px)
        const significantWidthChange = Math.abs(storedDevice.width - currentDevice.width) > 300;
        
        if (deviceChanged || significantWidthChange) {
            if (CACHE_BUSTER.debug) {
                console.log('⚠️ Device change detected!', {
                    typeChanged: deviceChanged,
                    widthChanged: significantWidthChange,
                    from: storedDevice,
                    to: currentDevice
                });
            }
            
            // Update stored device
            storeCurrentDevice(currentDevice);
            
            return true; // Device changed
        }
        
        // Update timestamp but keep device type
        storeCurrentDevice(currentDevice);
        return false; // No significant change
    }
    
    // Handle service worker cache
    function clearServiceWorkerCache() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                registrations.forEach(function(registration) {
                    if (CACHE_BUSTER.debug) {
                        console.log('🔧 Updating service worker:', registration.scope);
                    }
                    registration.update();
                });
            });
            
            // Clear caches API
            if ('caches' in window) {
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.map(function(cacheName) {
                            if (CACHE_BUSTER.debug) {
                                console.log('🗑️ Clearing cache:', cacheName);
                            }
                            return caches.delete(cacheName);
                        })
                    );
                });
            }
        }
    }
    
    // Main cache buster function
    function runCacheBuster() {
        if (CACHE_BUSTER.debug) {
            console.log('🚀 Running cache buster...');
        }
        
        // Check URL parameters for forced cache bust
        const urlParams = new URLSearchParams(window.location.search);
        const forceCacheBust = urlParams.has('cache_bust') || urlParams.has('device_switch');
        
        if (forceCacheBust) {
            if (CACHE_BUSTER.debug) {
                console.log('🔥 Forced cache bust detected');
            }
            clearServiceWorkerCache();
            return;
        }
        
        // Check for device change
        const deviceChanged = checkDeviceChange();
        
        if (deviceChanged) {
            // Show notification to user
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Device Change Detected',
                    text: 'Your device layout has changed. Refreshing for optimal experience...',
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    forceReloadWithCacheBust();
                });
            } else {
                // Fallback without SweetAlert
                const userConfirm = confirm('Device layout has changed. Refresh page for optimal experience?');
                if (userConfirm) {
                    forceReloadWithCacheBust();
                }
            }
        }
    }
    
    // Initialize cache buster
    function initCacheBuster() {
        if (CACHE_BUSTER.debug) {
            console.log('📱 Initializing Cache Buster v' + CACHE_BUSTER.version);
        }
        
        // Run initial check
        runCacheBuster();
        
        // Monitor resize events (debounced)
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (checkDeviceChange()) {
                    if (CACHE_BUSTER.debug) {
                        console.log('🔄 Device change on resize - cache bust recommended');
                    }
                    // Don't auto-reload on resize, just update storage
                }
            }, 1000);
        });
        
        // Expose global functions for manual cache clearing
        window.clearPayPerViewsCache = function() {
            console.log('🧹 Manual cache clear requested');
            clearBrowserCache();
            clearServiceWorkerCache();
            forceReloadWithCacheBust();
        };
        
        window.checkDeviceCache = function() {
            console.log('🔍 Manual device check requested');
            return {
                current: getCurrentDevice(),
                stored: getStoredDevice(),
                changed: checkDeviceChange()
            };
        };
        
        if (CACHE_BUSTER.debug) {
            console.log('✅ Cache Buster initialized successfully');
            console.log('🛠️ Use window.clearPayPerViewsCache() to manually clear cache');
            console.log('🛠️ Use window.checkDeviceCache() to check device status');
        }
    }
    
    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCacheBuster);
    } else {
        initCacheBuster();
    }
    
})();
