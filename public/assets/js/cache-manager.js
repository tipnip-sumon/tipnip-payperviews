/**
 * Advanced Cache Management System
 * Handles automatic cache clearing on logout and site updates
 * Enhanced for post-logout dashboard consistency
 * 
 * @author System
 * @version 3.0
 */

class CacheManager {
    constructor() {
        this.version = new Date().getTime();
        this.storageKeys = [
            'user_data',
            'messages_cache',
            'notifications_cache',
            'dashboard_data',
            'sponsor_data',
            'transaction_history',
            'settings_cache',
            'profile_cache',
            'api_cache',
            'widget_cache',
            'chart_data',
            'sidebar_state',
            'table_settings',
            'form_cache',
            'investment_data',
            'balance_cache',
            'referral_cache',
            'recent_activities'
        ];
        
        this.init();
    }

    /**
     * Initialize cache manager
     */
    init() {
        this.bindEvents();
        this.checkCacheVersion();
        this.setupBeforeUnloadHandler();
        this.checkLogoutCacheClearing();
        this.preventCachedDashboardLoad();
    }

    /**
     * Check if cache should be cleared based on URL parameters
     */
    checkLogoutCacheClearing() {
        const urlParams = new URLSearchParams(window.location.search);
        const fromLogout = urlParams.get('from_logout') === '1';
        
        if (fromLogout) {
            console.log('Post-logout page detected, performing minimal cleanup...');
            this.performMinimalLogoutCleanup();
            
            // Mark that minimal cleanup was performed
            sessionStorage.setItem('logout_cleanup_done', this.version);
        }
    }

    /**
     * Perform minimal cache clearing after logout (just session cleanup)
     */
    performMinimalLogoutCleanup() {
        // Only clear session-specific data, not all cache
        sessionStorage.removeItem('user_session');
        sessionStorage.removeItem('auth_state');
        sessionStorage.removeItem('login_state');
        
        // Clear any authentication-related localStorage
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_preferences');
        
        console.log('Minimal logout cleanup completed');
    }

    /**
     * Prevent loading of cached dashboard data after logout
     */
    preventCachedDashboardLoad() {
        const preventCache = sessionStorage.getItem('prevent_cached_load');
        const cacheCleared = sessionStorage.getItem('logout_cache_cleared');
        
        if (preventCache === 'true' && cacheCleared) {
            // Add flag to prevent cached dashboard loading
            window.PREVENT_CACHED_DASHBOARD = true;
            window.FORCE_FRESH_DASHBOARD = true;
            window.CACHE_VERSION = this.version;
            
            // Remove the flag after successful load
            window.addEventListener('load', () => {
                setTimeout(() => {
                    sessionStorage.removeItem('prevent_cached_load');
                    console.log('Cache prevention flags cleared after fresh load');
                }, 2000);
            });
        }
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Listen for logout events
        document.addEventListener('logout-initiated', () => {
            this.clearAllCache();
        });

        // Listen for page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.checkCacheVersion();
            }
        });

        // Listen for logout button clicks
        document.addEventListener('click', (event) => {
            const target = event.target;
            if (target.matches('[data-action="logout"], .logout-btn, a[href*="logout"], [onclick*="logout"]')) {
                console.log('Logout button clicked, preparing cache clearing...');
                setTimeout(() => {
                    this.performLogoutCacheClearing();
                }, 100);
            }
        });

        // Listen for storage events from other tabs
        window.addEventListener('storage', (event) => {
            if (event.key === 'user_logged_out' && event.newValue === 'true') {
                console.log('Logout detected from another tab, clearing cache...');
                this.performLogoutCacheClearing();
                localStorage.removeItem('user_logged_out');
            }
        });

        // Listen for custom events
        window.addEventListener('force-cache-clear', () => {
            this.performLogoutCacheClearing();
        });
    }

    /**
     * Clear browser cache if possible
     */
    clearBrowserCache() {
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    caches.delete(name);
                });
                console.log('Browser cache cleared');
            }).catch(error => {
                console.warn('Error clearing browser cache:', error);
            });
        }
    }

    /**
     * Clear service worker cache
     */
    clearServiceWorkerCache() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                registrations.forEach(registration => {
                    if (registration.active) {
                        registration.active.postMessage({
                            action: 'clearCache',
                            timestamp: this.version
                        });
                    }
                });
                console.log('Service worker cache clearing requested');
            }).catch(error => {
                console.warn('Error clearing service worker cache:', error);
            });
        }
    }

    /**
     * Clear AJAX cache
     */
    clearAjaxCache() {
        // Clear jQuery cache if available
        if (typeof $ !== 'undefined' && $.ajaxSetup) {
            $.ajaxSetup({
                cache: false,
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0',
                    'X-Cache-Version': this.version
                }
            });
        }

        // Clear axios cache if available
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['Cache-Control'] = 'no-cache, no-store, must-revalidate';
            axios.defaults.headers.common['Pragma'] = 'no-cache';
            axios.defaults.headers.common['X-Cache-Version'] = this.version;
        }

        console.log('AJAX cache configuration updated');
    }

    /**
     * Force reload static assets with cache busting
     */
    reloadStaticAssets() {
        try {
            // Add cache busting to stylesheets
            const links = document.querySelectorAll('link[rel="stylesheet"]');
            links.forEach(link => {
                const href = link.href;
                if (href && !href.includes('?v=')) {
                    link.href = href + (href.includes('?') ? '&' : '?') + 'v=' + this.version;
                }
            });

            console.log('Static assets cache busting applied');
        } catch (error) {
            console.warn('Error reloading static assets:', error);
        }
    }

    /**
     * Clear cached DOM elements
     */
    clearCachedDOMElements() {
        try {
            // Remove cached dashboard widgets
            const cachedElements = document.querySelectorAll('[data-cached="true"], .cached-content, .dashboard-cache');
            cachedElements.forEach(element => {
                element.remove();
            });

            // Clear any data attributes that might cache information
            const elementsWithData = document.querySelectorAll('[data-dashboard-cache], [data-user-cache], [data-api-cache]');
            elementsWithData.forEach(element => {
                delete element.dataset.dashboardCache;
                delete element.dataset.userCache;
                delete element.dataset.apiCache;
            });

            console.log('Cached DOM elements cleared');
        } catch (error) {
            console.warn('Error clearing cached DOM elements:', error);
        }
    }

    /**
     * Check if cache version has changed
     */
    checkCacheVersion() {
        const storedVersion = localStorage.getItem('cache_version');
        const currentVersion = document.querySelector('meta[name="cache-version"]')?.content;
        
        if (currentVersion && storedVersion && currentVersion !== storedVersion) {
            console.log('Cache version mismatch detected, clearing cache...');
            this.clearAllCache();
            localStorage.setItem('cache_version', currentVersion);
        }
    }

    /**
     * Handle cache version change from other tabs
     */
    handleCacheVersionChange(newVersion) {
        if (newVersion) {
            console.log('Cache version updated from another tab, clearing cache...');
            this.clearAllCache();
        }
    }

    /**
     * Clear all browser cache and storage
     */
    clearAllCache() {
        try {
            // Clear localStorage
            this.clearLocalStorage();
            
            // Clear sessionStorage
            this.clearSessionStorage();
            
            // Clear IndexedDB
            this.clearIndexedDB();
            
            // Clear cache storage (Service Worker caches)
            this.clearCacheStorage();
            
            // Clear cookies (application specific)
            this.clearApplicationCookies();
            
            // Force reload stylesheets and scripts
            this.reloadAssets();
            
            console.log('✅ All cache cleared successfully');
            
            // Dispatch event to notify other components
            window.dispatchEvent(new CustomEvent('cache-cleared', {
                detail: { timestamp: new Date().getTime() }
            }));
            
        } catch (error) {
            console.error('❌ Error clearing cache:', error);
        }
    }

    /**
     * Clear localStorage items
     */
    clearLocalStorage() {
        try {
            // Clear specific application keys
            this.storageKeys.forEach(key => {
                localStorage.removeItem(key);
            });
            
            // Clear DataTables state
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('DataTables_') || 
                    key.startsWith('dt_') || 
                    key.includes('_table_')) {
                    localStorage.removeItem(key);
                }
            });
            
            console.log('✅ localStorage cleared');
        } catch (error) {
            console.error('❌ Error clearing localStorage:', error);
        }
    }

    /**
     * Clear sessionStorage items
     */
    clearSessionStorage() {
        try {
            // Clear specific application keys
            this.storageKeys.forEach(key => {
                sessionStorage.removeItem(key);
            });
            
            // Clear any session-based cache
            Object.keys(sessionStorage).forEach(key => {
                if (key.includes('cache') || 
                    key.includes('temp') || 
                    key.startsWith('dt_')) {
                    sessionStorage.removeItem(key);
                }
            });
            
            console.log('✅ sessionStorage cleared');
        } catch (error) {
            console.error('❌ Error clearing sessionStorage:', error);
        }
    }

    /**
     * Clear IndexedDB databases
     */
    async clearIndexedDB() {
        try {
            if ('indexedDB' in window) {
                const databases = await indexedDB.databases();
                
                await Promise.all(databases.map(db => {
                    return new Promise((resolve, reject) => {
                        const deleteReq = indexedDB.deleteDatabase(db.name);
                        deleteReq.onsuccess = () => resolve();
                        deleteReq.onerror = () => reject(deleteReq.error);
                    });
                }));
                
                console.log('✅ IndexedDB cleared');
            }
        } catch (error) {
            console.error('❌ Error clearing IndexedDB:', error);
        }
    }

    /**
     * Clear Cache Storage (Service Worker caches)
     */
    async clearCacheStorage() {
        try {
            if ('caches' in window) {
                const cacheNames = await caches.keys();
                
                await Promise.all(cacheNames.map(cacheName => {
                    return caches.delete(cacheName);
                }));
                
                console.log('✅ Cache Storage cleared');
            }
        } catch (error) {
            console.error('❌ Error clearing Cache Storage:', error);
        }
    }

    /**
     * Clear application-specific cookies
     */
    clearApplicationCookies() {
        try {
            const cookies = document.cookie.split(';');
            
            cookies.forEach(cookie => {
                const eqPos = cookie.indexOf('=');
                const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
                
                // Don't clear essential cookies but clear session/cache related ones
                if (name && !['XSRF-TOKEN', 'laravel_session'].includes(name)) {
                    // Clear for current domain
                    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/`;
                    // Clear for parent domain
                    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=${window.location.hostname}`;
                    // Clear for subdomain
                    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=.${window.location.hostname}`;
                }
            });
            
            console.log('✅ Application cookies cleared');
        } catch (error) {
            console.error('❌ Error clearing cookies:', error);
        }
    }

    /**
     * Force reload CSS and JS assets with cache busting
     */
    reloadAssets() {
        try {
            const timestamp = new Date().getTime();
            
            // Reload stylesheets
            document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                const href = link.href.split('?')[0];
                link.href = `${href}?v=${timestamp}`;
            });
            
            // Don't reload scripts as it would break the page
            // Instead, mark them for reload on next page load
            localStorage.setItem('force_asset_reload', timestamp.toString());
            
            console.log('✅ Assets marked for reload');
        } catch (error) {
            console.error('❌ Error reloading assets:', error);
        }
    }

    /**
     * Setup beforeunload handler for cleanup
     */
    setupBeforeUnloadHandler() {
        window.addEventListener('beforeunload', () => {
            // Quick cleanup of temporary cache
            try {
                sessionStorage.removeItem('temp_data');
                sessionStorage.removeItem('current_page_cache');
            } catch (error) {
                // Ignore errors during page unload
            }
        });
    }

    /**
     * Clear cache for specific module
     */
    clearModuleCache(moduleName) {
        try {
            const moduleKeys = this.storageKeys.filter(key => 
                key.includes(moduleName.toLowerCase())
            );
            
            moduleKeys.forEach(key => {
                localStorage.removeItem(key);
                sessionStorage.removeItem(key);
            });
            
            console.log(`✅ Cache cleared for module: ${moduleName}`);
        } catch (error) {
            console.error(`❌ Error clearing cache for ${moduleName}:`, error);
        }
    }

    /**
     * Get cache status
     */
    getCacheStatus() {
        const status = {
            localStorage: Object.keys(localStorage).length,
            sessionStorage: Object.keys(sessionStorage).length,
            cookies: document.cookie.split(';').length,
            version: localStorage.getItem('cache_version'),
            lastCleared: localStorage.getItem('last_cache_clear')
        };
        
        return status;
    }

    /**
     * Force clear all cache (public method)
     */
    forceClearAll() {
        localStorage.setItem('last_cache_clear', new Date().toISOString());
        this.clearAllCache();
        
        // Show user feedback
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Cache Cleared!',
                text: 'Browser cache has been cleared successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }
}

// Initialize cache manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.cacheManager = new CacheManager();
    
    // Check for cache clear flag in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('clear_cache') === '1') {
        window.cacheManager.clearAllCache();
        
        // Remove the parameter from URL without page reload
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]clear_cache=1/, '');
        window.history.replaceState({}, document.title, newUrl);
    }
    
    // Check for force asset reload flag
    const forceReload = localStorage.getItem('force_asset_reload');
    if (forceReload) {
        localStorage.removeItem('force_asset_reload');
        // Assets will be reloaded with timestamp on next navigation
    }
});

// Enhanced logout function with cache clearing
function performLogoutWithCacheClearing() {
    // Trigger cache clearing
    if (window.cacheManager) {
        window.cacheManager.clearAllCache();
    }
    
    // Dispatch logout event
    document.dispatchEvent(new CustomEvent('logout-initiated'));
    
    // Perform actual logout via AJAX for better control
    fetch('/logout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear cache one more time
            if (window.cacheManager) {
                window.cacheManager.clearAllCache();
            }
            
            // Redirect with cache busting
            window.location.href = data.redirect_url || '/login?clear_cache=1&t=' + new Date().getTime();
        } else {
            console.error('Logout failed:', data.message);
            // Fallback to form submission
            document.getElementById('logout-form')?.submit();
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        // Fallback to form submission
        document.getElementById('logout-form')?.submit();
    });
}

// Export for global use
window.performLogoutWithCacheClearing = performLogoutWithCacheClearing;

// Utility functions for manual cache management
window.clearBrowserCache = () => {
    if (window.cacheManager) {
        window.cacheManager.forceClearAll();
    }
};

window.getCacheStatus = () => {
    if (window.cacheManager) {
        return window.cacheManager.getCacheStatus();
    }
    return null;
};

window.clearModuleCache = (moduleName) => {
    if (window.cacheManager) {
        window.cacheManager.clearModuleCache(moduleName);
    }
};
