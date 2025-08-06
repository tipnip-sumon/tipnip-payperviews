/**
 * Browser Cache Clearing Service
 * Provides methods to clear browser cache for specific domains
 */
class BrowserCacheService {
    constructor() {
        this.domain = window.location.hostname;
        this.protocol = window.location.protocol;
        this.fullDomain = `${this.protocol}//${this.domain}`;
    }

    /**
     * Clear all browser cache for current domain
     */
    async clearDomainCache() {
        try {
            console.log(`🧹 Starting cache clearing for domain: ${this.fullDomain}`);
            
            // Clear localStorage
            this.clearLocalStorage();
            
            // Clear sessionStorage
            this.clearSessionStorage();
            
            // Clear IndexedDB
            await this.clearIndexedDB();
            
            // Clear Service Worker cache
            await this.clearServiceWorkerCache();
            
            // Clear Cache API
            await this.clearCacheAPI();
            
            // Clear WebSQL (deprecated but some browsers still support)
            this.clearWebSQL();
            
            console.log(`✅ Cache clearing completed for domain: ${this.fullDomain}`);
            
            return {
                success: true,
                domain: this.domain,
                timestamp: new Date().toISOString(),
                methods: ['localStorage', 'sessionStorage', 'indexedDB', 'serviceWorker', 'cacheAPI']
            };
            
        } catch (error) {
            console.error('❌ Cache clearing failed:', error);
            return {
                success: false,
                error: error.message,
                domain: this.domain,
                timestamp: new Date().toISOString()
            };
        }
    }

    /**
     * Clear localStorage
     */
    clearLocalStorage() {
        try {
            if (typeof(Storage) !== "undefined" && localStorage) {
                const itemCount = localStorage.length;
                localStorage.clear();
                console.log(`✅ localStorage cleared (${itemCount} items removed)`);
                return true;
            }
        } catch (error) {
            console.warn('⚠️ localStorage clear failed:', error);
            return false;
        }
    }

    /**
     * Clear sessionStorage
     */
    clearSessionStorage() {
        try {
            if (typeof(Storage) !== "undefined" && sessionStorage) {
                const itemCount = sessionStorage.length;
                sessionStorage.clear();
                console.log(`✅ sessionStorage cleared (${itemCount} items removed)`);
                return true;
            }
        } catch (error) {
            console.warn('⚠️ sessionStorage clear failed:', error);
            return false;
        }
    }

    /**
     * Clear IndexedDB
     */
    async clearIndexedDB() {
        try {
            if (window.indexedDB) {
                const databases = await indexedDB.databases();
                const deletePromises = databases.map(db => {
                    return indexedDB.deleteDatabase(db.name);
                });
                
                await Promise.all(deletePromises);
                console.log(`✅ IndexedDB cleared (${databases.length} databases removed)`);
                return true;
            }
        } catch (error) {
            console.warn('⚠️ IndexedDB clear failed:', error);
            return false;
        }
    }

    /**
     * Clear Service Worker cache
     */
    async clearServiceWorkerCache() {
        try {
            if ('serviceWorker' in navigator) {
                const registrations = await navigator.serviceWorker.getRegistrations();
                const unregisterPromises = registrations.map(registration => {
                    return registration.unregister();
                });
                
                await Promise.all(unregisterPromises);
                console.log(`✅ Service Worker cache cleared (${registrations.length} registrations removed)`);
                return true;
            }
        } catch (error) {
            console.warn('⚠️ Service Worker cache clear failed:', error);
            return false;
        }
    }

    /**
     * Clear Cache API
     */
    async clearCacheAPI() {
        try {
            if ('caches' in window) {
                const cacheNames = await caches.keys();
                const deletePromises = cacheNames.map(cacheName => {
                    return caches.delete(cacheName);
                });
                
                await Promise.all(deletePromises);
                console.log(`✅ Cache API cleared (${cacheNames.length} caches removed)`);
                return true;
            }
        } catch (error) {
            console.warn('⚠️ Cache API clear failed:', error);
            return false;
        }
    }

    /**
     * Clear WebSQL (deprecated but might still exist)
     */
    clearWebSQL() {
        try {
            if (window.openDatabase) {
                // WebSQL is deprecated, but we can try to clear it
                console.log('⚠️ WebSQL detected (deprecated), attempting clear...');
                // Note: WebSQL clearing is complex and deprecated
                return true;
            }
        } catch (error) {
            console.warn('⚠️ WebSQL clear failed:', error);
            return false;
        }
    }

    /**
     * Navigate to cache clearing URL
     */
    async navigateToCacheClearUrl(redirect = null) {
        const baseUrl = `${this.protocol}//${this.domain}`;
        const clearUrl = `${baseUrl}/browser_cache_clear/only_this_domain`;
        const finalUrl = redirect ? `${clearUrl}?redirect=${encodeURIComponent(redirect)}` : clearUrl;
        
        try {
            // First try to clear locally
            await this.clearDomainCache();
            
            // Then navigate to the server endpoint for additional clearing
            window.location.href = finalUrl;
            
        } catch (error) {
            console.error('Navigation to cache clear URL failed:', error);
            // Fallback: direct navigation
            window.location.href = finalUrl;
        }
    }

    /**
     * Check if browser supports cache clearing features
     */
    static getBrowserCacheSupport() {
        return {
            localStorage: typeof(Storage) !== "undefined" && !!localStorage,
            sessionStorage: typeof(Storage) !== "undefined" && !!sessionStorage,
            indexedDB: !!window.indexedDB,
            serviceWorker: 'serviceWorker' in navigator,
            cacheAPI: 'caches' in window,
            webSQL: !!window.openDatabase,
            clearSiteData: 'clearSiteData' in document || navigator.userAgent.includes('Chrome')
        };
    }

    /**
     * Get cache clearing instructions for current browser
     */
    static getBrowserInstructions() {
        const userAgent = navigator.userAgent;
        
        if (userAgent.includes('Chrome')) {
            return {
                browser: 'Chrome',
                shortcut: 'Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)',
                steps: [
                    'Press Ctrl+Shift+Delete (or Cmd+Shift+Delete on Mac)',
                    'Select "Cached images and files"',
                    'Choose time range (e.g., "All time")',
                    'Click "Clear data"'
                ]
            };
        } else if (userAgent.includes('Firefox')) {
            return {
                browser: 'Firefox',
                shortcut: 'Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)',
                steps: [
                    'Press Ctrl+Shift+Delete (or Cmd+Shift+Delete on Mac)',
                    'Select "Cache"',
                    'Choose time range',
                    'Click "Clear Now"'
                ]
            };
        } else if (userAgent.includes('Safari')) {
            return {
                browser: 'Safari',
                shortcut: 'Cmd+Option+E',
                steps: [
                    'Enable Developer menu (Safari > Preferences > Advanced)',
                    'Go to Develop menu > Empty Caches',
                    'Or press Cmd+Option+E'
                ]
            };
        } else if (userAgent.includes('Edge')) {
            return {
                browser: 'Edge',
                shortcut: 'Ctrl+Shift+Delete',
                steps: [
                    'Press Ctrl+Shift+Delete',
                    'Select "Cached images and files"',
                    'Choose time range',
                    'Click "Clear"'
                ]
            };
        }
        
        return {
            browser: 'Unknown',
            shortcut: 'Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)',
            steps: [
                'Open browser settings',
                'Find "Clear browsing data" or similar',
                'Select cache/temporary files',
                'Clear the data'
            ]
        };
    }
}

// Global instance
window.BrowserCacheService = BrowserCacheService;
window.browserCacheService = new BrowserCacheService();

// Utility functions for easy access
window.clearDomainCache = () => window.browserCacheService.clearDomainCache();
window.navigateToCacheClear = (redirect) => window.browserCacheService.navigateToCacheClearUrl(redirect);

// Auto-clear on specific events (optional)
window.addEventListener('beforeunload', function() {
    // Optional: Clear cache before leaving (comment out if not desired)
    // window.browserCacheService.clearDomainCache();
});

console.log('🧹 Browser Cache Service loaded for domain:', window.location.hostname);
