/**
 * Admin Cache Manager - Clean Version
 * Simple cache management without auto-refresh for admin panel
 */

class AdminCacheManager {
    constructor() {
        this.version = this.getCacheVersion();
        this.isAdmin = true;
        this.storagePrefix = 'admin_';
        this.debug = false; // Disable debug to prevent auto-refresh
        
        // Initialize without automatic version checking
        this.initBasic();
    }

    /**
     * Basic initialization without auto-refresh triggers
     */
    initBasic() {
        //console.log('Admin Cache Manager initialized (clean version)');
        
        // Setup cross-tab sync only
        this.setupCrosTabSync();
    }

    /**
     * Get current cache version from meta tag
     */
    getCacheVersion() {
        const meta = document.querySelector('meta[name="cache-version"]');
        return meta ? meta.getAttribute('content') : Date.now().toString();
    }

    /**
     * Setup cross-tab synchronization for admin logout
     */
    setupCrosTabSync() {
        // Listen for admin logout events from other tabs
        window.addEventListener('storage', (e) => {
            if (e.key === `${this.storagePrefix}logout_event` && e.newValue) {
                const logoutData = JSON.parse(e.newValue);
                //console.log('Admin logout event received from another tab');
                
                // Clear cache and redirect
                this.clearAllCache(false);
                setTimeout(() => {
                    window.location.href = logoutData.redirect || '/admin';
                }, 500);
            }
        });
    }

    /**
     * Broadcast admin logout event to other tabs
     */
    broadcastLogoutEvent() {
        const logoutEvent = {
            timestamp: Date.now(),
            source: 'admin',
            redirect: '/admin',
            reason: 'admin_logout'
        };
        
        localStorage.setItem(`${this.storagePrefix}logout_event`, JSON.stringify(logoutEvent));
        
        // Clean up the event after broadcast
        setTimeout(() => {
            localStorage.removeItem(`${this.storagePrefix}logout_event`);
        }, 1000);
    }

    /**
     * Clear all types of cache
     */
    async clearAllCache(showFeedback = true) {
        const results = {
            localStorage: false,
            sessionStorage: false,
            indexedDB: false,
            cacheStorage: false,
            serviceWorker: false,
            cookies: false
        };

        try {
            // Clear localStorage (admin-specific items)
            results.localStorage = this.clearLocalStorage();
            
            // Clear sessionStorage
            results.sessionStorage = this.clearSessionStorage();
            
            // Clear IndexedDB
            results.indexedDB = await this.clearIndexedDB();
            
            // Clear Cache Storage (Service Worker caches)
            results.cacheStorage = await this.clearCacheStorage();
            
            // Unregister Service Workers
            results.serviceWorker = await this.clearServiceWorkers();
            
            // Clear relevant cookies
            results.cookies = this.clearCookies();

            //console.log('Admin cache clearing completed', results);

            if (showFeedback) {
                this.showClearFeedback(results);
            }

            return results;
        } catch (error) {
            //console.error('Error during admin cache clearing:', error);
            if (showFeedback) {
                this.showErrorFeedback(error);
            }
            return results;
        }
    }

    /**
     * Clear localStorage (admin-specific)
     */
    clearLocalStorage() {
        try {
            const keysToRemove = [];
            
            // Get all localStorage keys
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key) {
                    // Remove admin-specific keys and general cache keys
                    if (key.startsWith(this.storagePrefix) || 
                        key.includes('admin') || 
                        key.includes('cache') ||
                        key.includes('auth') ||
                        key.includes('session')) {
                        keysToRemove.push(key);
                    }
                }
            }
            
            // Remove identified keys
            keysToRemove.forEach(key => {
                localStorage.removeItem(key);
            });
            
            return true;
        } catch (error) {
            //console.error('Error clearing admin localStorage:', error);
            return false;
        }
    }

    /**
     * Clear sessionStorage
     */
    clearSessionStorage() {
        try {
            sessionStorage.clear();
            return true;
        } catch (error) {
            //console.error('Error clearing sessionStorage:', error);
            return false;
        }
    }

    /**
     * Clear IndexedDB
     */
    async clearIndexedDB() {
        try {
            if (!window.indexedDB) {
                return false;
            }

            const databases = await indexedDB.databases();
            await Promise.all(
                databases.map(db => {
                    return new Promise((resolve, reject) => {
                        const deleteReq = indexedDB.deleteDatabase(db.name);
                        deleteReq.onsuccess = () => resolve();
                        deleteReq.onerror = () => reject(deleteReq.error);
                    });
                })
            );
            
            return true;
        } catch (error) {
            //console.error('Error clearing IndexedDB:', error);
            return false;
        }
    }

    /**
     * Clear Cache Storage (Service Worker caches)
     */
    async clearCacheStorage() {
        try {
            if (!('caches' in window)) {
                return false;
            }

            const cacheNames = await caches.keys();
            await Promise.all(
                cacheNames.map(cacheName => {
                    return caches.delete(cacheName);
                })
            );
            
            return true;
        } catch (error) {
            //console.error('Error clearing cache storage:', error);
            return false;
        }
    }

    /**
     * Clear Service Workers
     */
    async clearServiceWorkers() {
        try {
            if (!('serviceWorker' in navigator)) {
                return false;
            }

            const registrations = await navigator.serviceWorker.getRegistrations();
            await Promise.all(
                registrations.map(registration => {
                    return registration.unregister();
                })
            );
            
            return true;
        } catch (error) {
            //console.error('Error clearing service workers:', error);
            return false;
        }
    }

    /**
     * Clear relevant cookies
     */
    clearCookies() {
        try {
            const cookies = document.cookie.split(';');
            
            cookies.forEach(cookie => {
                const eqPos = cookie.indexOf('=');
                const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
                
                // Clear admin and authentication related cookies
                if (name.includes('admin') || 
                    name.includes('auth') || 
                    name.includes('session') ||
                    name.includes('remember') ||
                    name.includes('token')) {
                    
                    // Clear for current domain
                    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;`;
                    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=${window.location.hostname};`;
                }
            });
            
            return true;
        } catch (error) {
            //console.error('Error clearing cookies:', error);
            return false;
        }
    }

    /**
     * Show success feedback
     */
    showClearFeedback(results) {
        const successCount = Object.values(results).filter(Boolean).length;
        const totalCount = Object.keys(results).length;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Admin Cache Cleared!',
                html: `
                    <div class="text-start">
                        <p><strong>Successfully cleared ${successCount}/${totalCount} cache types:</strong></p>
                        <ul class="list-unstyled">
                            <li>${results.localStorage ? '✅' : '❌'} Local Storage</li>
                            <li>${results.sessionStorage ? '✅' : '❌'} Session Storage</li>
                            <li>${results.indexedDB ? '✅' : '❌'} IndexedDB</li>
                            <li>${results.cacheStorage ? '✅' : '❌'} Cache Storage</li>
                            <li>${results.serviceWorker ? '✅' : '❌'} Service Workers</li>
                            <li>${results.cookies ? '✅' : '❌'} Admin Cookies</li>
                        </ul>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Perfect!',
                timer: 5000,
                timerProgressBar: true
            });
        } else {
            alert(`Admin Cache Cleared! Successfully cleared ${successCount}/${totalCount} cache types.`);
        }
    }

    /**
     * Show error feedback
     */
    showErrorFeedback(error) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Cache Clear Error',
                text: `An error occurred while clearing admin cache: ${error.message}`,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`Cache clear error: ${error.message}`);
        }
    }

    /**
     * Mark that logout is happening
     */
    setLoggingOut() {
        this.isLoggingOut = true;
    }
}

// Initialize admin cache manager
const adminCacheManager = new AdminCacheManager();

/**
 * Admin-specific logout with cache clearing
 */
function handleAdminLogoutWithCacheClearing(event) {
    event.preventDefault();
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Admin Logout Confirmation',
            text: 'Are you sure you want to logout? This will clear all admin cache data.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, logout & clear cache',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                // Show clearing progress
                Swal.fire({
                    title: 'Logging out...',
                    html: 'Clearing admin cache and logging out securely...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Mark as logging out for cross-tab sync
                    adminCacheManager.setLoggingOut();
                    
                    // Clear all cache
                    await adminCacheManager.clearAllCache(false);
                    
                    // Broadcast logout event to other tabs
                    adminCacheManager.broadcastLogoutEvent();
                    
                    // Small delay to ensure cache clearing completes
                    setTimeout(() => {
                        // Submit the logout form
                        const form = document.getElementById('logoutForm') || document.getElementById('sidebarLogoutForm');
                        if (form) {
                            form.submit();
                        } else {
                            // Fallback redirect
                            window.location.href = '/admin';
                        }
                    }, 1000);
                    
                } catch (error) {
                    //console.error('Error during admin logout:', error);
                    Swal.fire({
                        title: 'Logout Warning',
                        text: 'Cache clearing encountered an issue, but logout will continue.',
                        icon: 'warning',
                        confirmButtonText: 'Continue Logout',
                        timer: 3000
                    }).then(() => {
                        const form = document.getElementById('logoutForm') || document.getElementById('sidebarLogoutForm');
                        if (form) {
                            form.submit();
                        } else {
                            window.location.href = '/admin';
                        }
                    });
                }
            }
        });
    } else {
        // Fallback without SweetAlert
        if (confirm('Are you sure you want to logout? This will clear all admin cache data.')) {
            adminCacheManager.setLoggingOut();
            adminCacheManager.clearAllCache(false);
            
            const form = document.getElementById('logoutForm') || document.getElementById('sidebarLogoutForm');
            if (form) {
                form.submit();
            } else {
                window.location.href = '/admin';
            }
        }
    }
}

/**
 * Emergency admin logout (double-click)
 */
function emergencyAdminLogoutWithCacheClearing() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Emergency Admin Logout',
            text: 'Performing immediate logout with cache clearing...',
            icon: 'warning',
            showConfirmButton: false,
            timer: 2000,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Mark as logging out
    adminCacheManager.setLoggingOut();
    
    // Clear cache immediately
    adminCacheManager.clearAllCache(false);
    
    // Broadcast logout event
    adminCacheManager.broadcastLogoutEvent();
    
    // Immediate logout
    setTimeout(() => {
        const form = document.getElementById('logoutForm') || document.getElementById('sidebarLogoutForm');
        if (form) {
            form.submit();
        } else {
            window.location.href = '/admin';
        }
    }, 500);
}

/**
 * Show admin cache management modal
 */
function showAdminCacheManagementModal() {
    if (typeof Swal === 'undefined') {
        alert('Admin Cache Manager: SweetAlert2 is required for the modal interface.');
        return;
    }

    const currentVersion = adminCacheManager ? adminCacheManager.version : 'Unknown';
    
    Swal.fire({
        title: 'Admin Cache Management',
        html: `
            <div class="text-start">
                <p class="mb-3">Manage admin panel cache and storage:</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-danger btn-sm" onclick="adminCacheManager.clearAllCache()">
                        <i class="fe fe-trash-2 me-2"></i>Clear All Cache
                    </button>
                </div>
                <hr>
                <small class="text-muted">
                    <strong>Current Version:</strong> ` + currentVersion + `
                </small>
            </div>
        `,
        icon: 'info',
        showConfirmButton: false,
        showCloseButton: true,
        width: '400px'
    });
}

// Global functions for admin cache management
window.handleAdminLogoutWithCacheClearing = handleAdminLogoutWithCacheClearing;
window.emergencyAdminLogoutWithCacheClearing = emergencyAdminLogoutWithCacheClearing;
window.showAdminCacheManagementModal = showAdminCacheManagementModal;
window.adminCacheManager = adminCacheManager;

// Log initialization
//console.log('✅ Admin Cache Manager loaded successfully with all functions available');
