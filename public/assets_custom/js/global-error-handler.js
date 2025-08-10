/**
 * Global JavaScript Error Handler
 * Comprehensive error handling for all JavaScript issues
 * Created: August 9, 2025
 */

(function() {
    'use strict';

    // Global error statistics
    window.errorStats = {
        total: 0,
        handled: 0,
        suppressed: 0
    };

    // Error patterns to suppress (non-critical errors)
    const suppressPatterns = [
        /MetaMask/i,
        /Cannot read properties of null/i,
        /has already been declared/i,
        /Identifier.*already been declared/i,
        /Permissions policy violation/i,
        /unload is not allowed/i,
        /pickr/i,
        /custom-switcher/i
    ];

    // Critical error patterns (should still be logged)
    const criticalPatterns = [
        /CSRF/i,
        /Authentication/i,
        /Session/i,
        /Database/i,
        /Network/i
    ];

    // Enhanced global error handler
    window.addEventListener('error', function(e) {
        window.errorStats.total++;

        const errorMessage = e.message || '';
        const errorFile = e.filename || '';
        const errorLine = e.lineno || 0;

        // Check if this is a critical error
        const isCritical = criticalPatterns.some(pattern => 
            pattern.test(errorMessage) || pattern.test(errorFile)
        );

        // Check if this error should be suppressed
        const shouldSuppress = suppressPatterns.some(pattern => 
            pattern.test(errorMessage) || pattern.test(errorFile)
        );

        if (shouldSuppress && !isCritical) {
            window.errorStats.suppressed++;
            console.warn(`Suppressed non-critical error: ${errorMessage} at ${errorFile}:${errorLine}`);
            return true; // Prevent default error handling
        }

        window.errorStats.handled++;
        
        // Log handled errors with context
        console.error(`Handled error: ${errorMessage} at ${errorFile}:${errorLine}`, {
            error: e.error,
            stack: e.error?.stack,
            timestamp: new Date().toISOString()
        });
    });

    // Enhanced promise rejection handler
    window.addEventListener('unhandledrejection', function(e) {
        window.errorStats.total++;

        const reason = e.reason || {};
        const message = reason.message || reason.toString();

        // Check if this should be suppressed
        const shouldSuppress = suppressPatterns.some(pattern => 
            pattern.test(message)
        );

        const isCritical = criticalPatterns.some(pattern => 
            pattern.test(message)
        );

        if (shouldSuppress && !isCritical) {
            window.errorStats.suppressed++;
            console.warn(`Suppressed promise rejection: ${message}`);
            e.preventDefault();
            return;
        }

        window.errorStats.handled++;
        console.error(`Handled promise rejection: ${message}`, {
            reason: e.reason,
            timestamp: new Date().toISOString()
        });
    });

    // Safe DOM ready function
    window.safeReady = function(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    callback();
                } catch (error) {
                    console.warn('safeReady callback error:', error);
                }
            });
        } else {
            try {
                callback();
            } catch (error) {
                console.warn('safeReady immediate callback error:', error);
            }
        }
    };

    // Safe element selector
    window.safeQuery = function(selector) {
        try {
            return document.querySelector(selector);
        } catch (error) {
            console.warn(`safeQuery error for selector "${selector}":`, error);
            return null;
        }
    };

    // Safe element selector (all)
    window.safeQueryAll = function(selector) {
        try {
            return document.querySelectorAll(selector);
        } catch (error) {
            console.warn(`safeQueryAll error for selector "${selector}":`, error);
            return [];
        }
    };

    // Safe event listener
    window.safeAddEventListener = function(element, event, callback, options) {
        try {
            if (element && typeof element.addEventListener === 'function') {
                element.addEventListener(event, function(e) {
                    try {
                        callback(e);
                    } catch (error) {
                        console.warn(`Event callback error for ${event}:`, error);
                    }
                }, options);
                return true;
            }
            return false;
        } catch (error) {
            console.warn(`safeAddEventListener error:`, error);
            return false;
        }
    };

    // Safe fetch wrapper
    window.safeFetch = async function(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...options.headers
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return response;
        } catch (error) {
            console.warn(`safeFetch error for ${url}:`, error);
            throw error;
        }
    };

    // Error reporting function (optional)
    window.reportError = function(error, context = {}) {
        console.group('Error Report');
        console.error('Error:', error);
        console.log('Context:', context);
        console.log('Stats:', window.errorStats);
        console.log('Timestamp:', new Date().toISOString());
        console.groupEnd();
    };

    // Global error handler initialized (silent mode)

    // Periodic error stats logging (every 5 minutes in development)
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
        setInterval(() => {
            if (window.errorStats.total > 0) {
                console.log('Error Statistics:', window.errorStats);
            }
        }, 300000); // 5 minutes
    }
})();
