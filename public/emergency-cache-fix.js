/**
 * EMERGENCY CACHE FIX for Live Server Issues
 * Force clear all browser cache and reload with fresh assets
 */

(function() {
    'use strict';
    
    console.log('🚨 Emergency Cache Fix Activated');
    
    // Version bump to force cache refresh
    const EMERGENCY_VERSION = Date.now();
    const CACHE_KEYS = [
        'pv_device_cache',
        'mobile_cache_version',
        'app_cache_version',
        'user_preferences',
        'theme_cache',
        'layout_cache',
        'modal_cache',
        'notification_cache'
    ];
    
    // Force clear all localStorage cache
    function emergencyClearCache() {
        console.log('🧹 Clearing all cached data...');
        
        // Clear specific cache keys
        CACHE_KEYS.forEach(key => {
            try {
                localStorage.removeItem(key);
                sessionStorage.removeItem(key);
                console.log(`✅ Cleared cache key: ${key}`);
            } catch (e) {
                console.warn(`❌ Failed to clear ${key}:`, e);
            }
        });
        
        // Clear all cache entries that start with 'pv_' or 'tipnip_'
        try {
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith('pv_') || key.startsWith('tipnip_') || key.startsWith('cache_')) {
                    localStorage.removeItem(key);
                    console.log(`✅ Cleared prefixed cache: ${key}`);
                }
            });
        } catch (e) {
            console.warn('Failed to clear prefixed cache:', e);
        }
        
        // Set emergency version
        localStorage.setItem('emergency_cache_version', EMERGENCY_VERSION);
        console.log('✅ Set emergency cache version:', EMERGENCY_VERSION);
    }
    
    // Force reload CSS files with cache busting
    function forceReloadCSS() {
        console.log('🎨 Force reloading CSS files...');
        
        const cssLinks = document.querySelectorAll('link[rel="stylesheet"]');
        cssLinks.forEach((link, index) => {
            setTimeout(() => {
                const href = link.href;
                const separator = href.includes('?') ? '&' : '?';
                const newHref = `${href}${separator}v=${EMERGENCY_VERSION}&t=${Date.now()}`;
                
                const newLink = document.createElement('link');
                newLink.rel = 'stylesheet';
                newLink.href = newHref;
                newLink.onload = () => {
                    console.log(`✅ Reloaded CSS: ${href}`);
                    if (link.parentNode) {
                        link.parentNode.removeChild(link);
                    }
                };
                
                document.head.appendChild(newLink);
            }, index * 100); // Stagger reloads
        });
    }
    
    // Force reload JavaScript files
    function forceReloadJS() {
        console.log('⚡ Force reloading JS files...');
        
        const jsScripts = document.querySelectorAll('script[src]');
        const criticalScripts = [
            'bootstrap.bundle.min.js',
            'custom.js',
            'mobile-functions.js',
            'bootstrap-modal-fix.js',
            'cache-buster.js'
        ];
        
        criticalScripts.forEach((scriptName, index) => {
            setTimeout(() => {
                const existingScript = Array.from(jsScripts).find(script => 
                    script.src.includes(scriptName)
                );
                
                if (existingScript) {
                    const newScript = document.createElement('script');
                    const src = existingScript.src;
                    const separator = src.includes('?') ? '&' : '?';
                    newScript.src = `${src}${separator}v=${EMERGENCY_VERSION}&t=${Date.now()}`;
                    newScript.onload = () => {
                        console.log(`✅ Reloaded JS: ${scriptName}`);
                    };
                    
                    document.head.appendChild(newScript);
                }
            }, index * 200); // Stagger reloads
        });
    }
    
    // Check if emergency fix is needed
    function checkIfEmergencyNeeded() {
        const lastEmergencyVersion = localStorage.getItem('emergency_cache_version');
        const lastUpdate = new Date('2025-08-09').getTime(); // Today's update
        
        // If no emergency version or it's older than today's update
        if (!lastEmergencyVersion || parseInt(lastEmergencyVersion) < lastUpdate) {
            console.log('🚨 Emergency cache fix needed!');
            return true;
        }
        
        // Check for UI issues
        const hasUIIssues = (
            !document.querySelector('.mobile-optimized') && window.innerWidth <= 991
        ) || (
            !document.querySelector('script[src*="bootstrap"]') ||
            !document.querySelector('link[href*="bootstrap"]')
        );
        
        if (hasUIIssues) {
            console.log('🚨 UI issues detected, emergency fix needed!');
            return true;
        }
        
        return false;
    }
    
    // Show user notification
    function showEmergencyNotification() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'System Update',
                text: 'We are updating your interface for better performance. This will only take a moment.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 3000,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #007bff;
                color: white;
                padding: 15px;
                border-radius: 8px;
                z-index: 10000;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            `;
            notification.innerHTML = '🔄 Updating interface...';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
    }
    
    // Main emergency fix execution
    function executeEmergencyFix() {
        console.log('🚨 EXECUTING EMERGENCY CACHE FIX');
        
        // Show notification
        showEmergencyNotification();
        
        // Clear cache immediately
        emergencyClearCache();
        
        // Force reload assets
        setTimeout(() => {
            forceReloadCSS();
        }, 500);
        
        setTimeout(() => {
            forceReloadJS();
        }, 1000);
        
        // Final reload if needed
        setTimeout(() => {
            const hasBootstrap = typeof window.bootstrap !== 'undefined';
            const hasJQuery = typeof window.$ !== 'undefined';
            const hasMobileFunctions = typeof window.openMobileModal === 'function';
            
            if (!hasBootstrap || !hasMobileFunctions) {
                console.log('🔄 Core functions missing, forcing page reload...');
                window.location.reload(true);
            } else {
                console.log('✅ Emergency fix completed successfully!');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Update Complete!',
                        text: 'Your interface has been updated successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        }, 3000);
    }
    
    // Auto-execute if needed
    if (checkIfEmergencyNeeded()) {
        // Small delay to ensure page is loaded
        setTimeout(executeEmergencyFix, 1000);
    }
    
    // Manual trigger function (for support team)
    window.emergencyCache = {
        fix: executeEmergencyFix,
        clear: emergencyClearCache,
        check: checkIfEmergencyNeeded,
        version: EMERGENCY_VERSION
    };
    
    console.log('🚨 Emergency Cache Fix Ready. Use window.emergencyCache.fix() to trigger manually.');
    
})();
