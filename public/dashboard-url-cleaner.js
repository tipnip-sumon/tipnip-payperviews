/**
 * Dashboard URL Cleaner
 * Automatically cleans dashboard URLs of problematic parameters
 */

(function() {
    'use strict';

    const URL_CLEANER = {
        debug: false, // Set to true for debugging
        version: '1.0.0',
        
        // Parameters that should be removed from dashboard URLs
        problematicParams: [
            'cache_bust',
            'device_switch',
            'v',
            '_token',
            'debug',
            'force_reload'
        ],
        
        // Pages that should have URLs cleaned
        targetPages: [
            '/user/dashboard',
            '/user/',
            '/admin/',
            '/dashboard'
        ]
    };

    // Check if current page needs URL cleaning
    function needsURLCleaning() {
        const currentPath = window.location.pathname;
        return URL_CLEANER.targetPages.some(page => currentPath.includes(page));
    }

    // Check if URL has problematic parameters
    function hasProblematicParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        return URL_CLEANER.problematicParams.some(param => urlParams.has(param));
    }

    // Clean the current URL
    function cleanCurrentURL() {
        if (!needsURLCleaning() || !hasProblematicParameters()) {
            return false;
        }

        try {
            const url = new URL(window.location.href);
            let cleaned = false;

            // Remove problematic parameters
            URL_CLEANER.problematicParams.forEach(param => {
                if (url.searchParams.has(param)) {
                    url.searchParams.delete(param);
                    cleaned = true;
                }
            });

            if (cleaned) {
                // Use replaceState to clean URL without reloading
                const cleanURL = url.pathname + (url.search || '');
                
                if (URL_CLEANER.debug) {
                    console.log('🧹 Cleaning URL from:', window.location.href, 'to:', url.origin + cleanURL);
                }
                
                history.replaceState(null, '', cleanURL);
                
                // Trigger a custom event for other scripts
                window.dispatchEvent(new CustomEvent('urlCleaned', {
                    detail: {
                        originalURL: window.location.href,
                        cleanedURL: url.origin + cleanURL
                    }
                }));
                
                return true;
            }
        } catch (error) {
            if (URL_CLEANER.debug) {
                console.error('❌ Error cleaning URL:', error);
            }
        }
        
        return false;
    }

    // Auto-clean URL on page load
    function autoCleanURL() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cleanCurrentURL);
        } else {
            cleanCurrentURL();
        }
    }

    // Public API
    window.DashboardURLCleaner = {
        clean: cleanCurrentURL,
        needsCleaning: needsURLCleaning,
        hasProblematicParams: hasProblematicParameters,
        setDebug: function(enabled) {
            URL_CLEANER.debug = enabled;
        }
    };

    // Auto-initialize
    if (URL_CLEANER.debug) {
        console.log('🧹 Dashboard URL Cleaner v' + URL_CLEANER.version + ' loaded');
    }
    
    autoCleanURL();

})();
