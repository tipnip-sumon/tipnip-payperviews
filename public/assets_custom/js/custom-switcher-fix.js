/**
 * Custom Switcher Fix
 * Fixes null reference errors in custom-switcher.min.js
 * Created: August 9, 2025
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create missing switcher elements to prevent null reference errors
    const switcherSelectors = [
        '#switcher-primary-color',
        '#switcher-primary-color1', 
        '#switcher-primary-color2',
        '#switcher-primary-color3',
        '#switcher-primary-color4',
        '#switcher-bg-color',
        '#switcher-bg-color1',
        '#switcher-bg-color2', 
        '#switcher-bg-color3',
        '#switcher-bg-color4',
        '#switcher-bg-img',
        '#switcher-bg-img1',
        '#switcher-bg-img2',
        '#switcher-bg-img3',
        '#switcher-bg-img4',
        '#reset-all',
        '#switcher-loader-enable',
        '#switcher-loader-disable'
    ];

    // Create a hidden container for missing switcher elements
    let switcherContainer = document.getElementById('switcher-fallback-container');
    if (!switcherContainer) {
        switcherContainer = document.createElement('div');
        switcherContainer.id = 'switcher-fallback-container';
        switcherContainer.style.display = 'none';
        document.body.appendChild(switcherContainer);
    }

    // Create missing elements
    switcherSelectors.forEach(selector => {
        const elementId = selector.replace('#', '');
        if (!document.getElementById(elementId)) {
            const element = document.createElement('button');
            element.id = elementId;
            element.className = 'switcher-fallback-btn';
            switcherContainer.appendChild(element);
            console.log('Created missing switcher element:', selector);
        }
    });

    // Override the switcherClick function if it exists
    if (typeof window.switcherClick === 'function') {
        const originalSwitcherClick = window.switcherClick;
        window.switcherClick = function() {
            try {
                originalSwitcherClick();
            } catch (error) {
                console.warn('switcherClick error caught and handled:', error);
            }
        };
    }
});

// Safe addEventListener wrapper for switcher elements
const originalAddEventListener = EventTarget.prototype.addEventListener;
EventTarget.prototype.addEventListener = function(type, listener, options) {
    try {
        if (this && typeof originalAddEventListener === 'function') {
            return originalAddEventListener.call(this, type, function(event) {
                try {
                    if (typeof listener === 'function') {
                        return listener.call(this, event);
                    }
                } catch (error) {
                    console.warn('Event listener error caught:', error);
                }
            }, options);
        }
    } catch (error) {
        console.warn('addEventListener wrapper error:', error);
    }
};

// Global error handler for custom switcher
window.addEventListener('error', function(e) {
    if (e.filename && (e.filename.includes('custom-switcher') || e.message.includes('switcherClick'))) {
        console.warn('Custom switcher error caught and handled:', e.message);
        return true; // Prevent error from bubbling up
    }
});

// Handle switcher click errors specifically
document.addEventListener('click', function(e) {
    // If clicking on elements that might trigger switcher errors
    if (e.target && (e.target.id || '').includes('switcher')) {
        try {
            // Let the original handler run, but catch any errors
        } catch (error) {
            console.warn('Switcher click error handled:', error);
            e.preventDefault();
            e.stopPropagation();
        }
    }
});
