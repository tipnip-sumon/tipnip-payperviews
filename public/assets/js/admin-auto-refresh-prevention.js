/**
 * Admin Auto-Refresh Prevention Script
 * Removes cache-bust parameters and prevents unwanted auto-refresh
 */

(function() {
    'use strict';
    
    // Remove auto-refresh parameters from URL
    function cleanURL() {
        const url = new URL(window.location);
        const paramsToRemove = ['_cache_bust', '_admin_refresh', '_timestamp'];
        let modified = false;
        
        paramsToRemove.forEach(param => {
            if (url.searchParams.has(param)) {
                url.searchParams.delete(param);
                modified = true;
            }
        });
        
        if (modified) {
            //console.log('🧹 Cleaned auto-refresh parameters from URL');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    }
    
    // Prevent any unwanted page refreshes
    function preventAutoRefresh() {
        try {
            // Clear any auto-refresh timers that might have been set
            let highestTimeoutId = setTimeout(function(){});
            for (let i = 0 ; i < highestTimeoutId ; i++) {
                clearTimeout(i); 
            }
            
            let highestIntervalId = setInterval(function(){});
            for (let i = 0 ; i < highestIntervalId ; i++) {
                clearInterval(i); 
            }
            
            //console.log('🛡️ Auto-refresh prevention activated');
        } catch (error) {
            //console.warn('⚠️ Timer clearing had minor issues:', error.message);
        }
    }
    
    // Override any location.reload calls to confirm first
    try {
        const originalReload = window.location.reload.bind(window.location);
        Object.defineProperty(window.location, 'reload', {
            value: function(forcedReload) {
                if (confirm('Are you sure you want to refresh the page?')) {
                    originalReload(forcedReload);
                }
            },
            writable: false,
            configurable: false
        });
    } catch (error) {
        //console.warn('⚠️ Could not override location.reload:', error.message);
        // Alternative approach - intercept common refresh patterns
        window.addEventListener('beforeunload', function(e) {
            if (window.adminPreventRefresh) {
                e.preventDefault();
                e.returnValue = 'Are you sure you want to leave?';
                return 'Are you sure you want to leave?';
            }
        });
    }
    
    // Run cleanup when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            cleanURL();
            preventAutoRefresh();
        });
    } else {
        cleanURL();
        preventAutoRefresh();
    }
    
    //console.log('🚀 Admin Auto-Refresh Prevention loaded');
})();
