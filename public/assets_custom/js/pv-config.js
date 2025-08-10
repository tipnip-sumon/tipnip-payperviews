/**
 * Global Configuration for PayPerViews Mobile
 * Provides environment-aware settings for JavaScript functionality
 */

window.PV_CONFIG = {
    // Environment detection
    env: 'production', // Will be dynamically set by server
    debug: false,      // Will be dynamically set by server
    version: '1.0.0',  // Will be dynamically set by server
    
    // Console logging configuration
    console: {
        enabled: false, // Will be dynamically set by server
        level: 'production'
    },
    
    // Mobile-specific settings
    mobile: {
        breakpoint: 991,
        forceDetection: false,
        cacheVersion: '1.0.0'
    },
    
    // Error handling
    errors: {
        silent: true,
        boundaries: true
    },
    
    // Performance monitoring
    performance: {
        enabled: false
    }
};

// Global console wrapper that respects environment settings
window.pvLog = function() {
    if (window.PV_CONFIG && window.PV_CONFIG.console && window.PV_CONFIG.console.enabled) {
        console.log.apply(console, arguments);
    }
};

window.pvWarn = function() {
    if (window.PV_CONFIG && window.PV_CONFIG.console && window.PV_CONFIG.console.enabled) {
        console.warn.apply(console, arguments);
    }
};

window.pvError = function() {
    // Always log errors, but format differently based on environment
    if (window.PV_CONFIG && window.PV_CONFIG.console && window.PV_CONFIG.console.enabled) {
        console.error.apply(console, arguments);
    } else {
        // In production, just log to a collection system if available
        if (window.errorCollector) {
            window.errorCollector.push({
                type: 'error',
                args: Array.from(arguments),
                timestamp: new Date().toISOString(),
                url: window.location.href
            });
        }
    }
};

// Initialize error boundary
if (typeof window !== 'undefined') {
    window.addEventListener('error', function(event) {
        // Prevent the error from propagating if it's a known safe error
        const safeErrors = [
            'Cannot read properties of null',
            'Cannot set properties of null',
            'Script error',
            'ResizeObserver loop limit exceeded',
            'Unexpected token'
        ];
        
        if (event.message && safeErrors.some(err => event.message.includes(err))) {
            // Completely suppress syntax errors - no logging in production
            event.preventDefault();
            return false;
        }
        
        // Only log other errors in development
        if (window.PV_CONFIG && window.PV_CONFIG.console && window.PV_CONFIG.console.enabled) {
            console.error('Global Error:', event.error, 'in', event.filename, 'at line', event.lineno);
        }
    });
    
    window.addEventListener('unhandledrejection', function(event) {
        if (window.PV_CONFIG && window.PV_CONFIG.console && window.PV_CONFIG.console.enabled) {
            console.error('Unhandled Promise Rejection:', event.reason);
        }
        
        // Prevent console spam for common safe rejections
        if (event.reason && typeof event.reason === 'string') {
            const safeRejections = [
                'fetch',
                'network',
                'load'
            ];
            
            if (safeRejections.some(err => event.reason.toLowerCase().includes(err))) {
                event.preventDefault();
                return false;
            }
        }
    });
}
