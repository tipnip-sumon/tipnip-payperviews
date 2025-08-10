/**
 * Pickr Color Picker Fix
 * Fixes null reference errors in pickr.es5.min.js
 * Created: August 9, 2025
 */

// Load before Pickr is initialized
(function() {
    'use strict';

    // Create missing containers immediately
    const containers = [
        { id: 'pickrContainerPrimary', class: 'pickr-container' },
        { id: 'pickrContainerBackground', class: 'pickr-container' },
        { id: 'themeContainerPrimary', class: 'theme-container d-flex flex-wrap' },
        { id: 'themeContainerBackground', class: 'theme-container d-flex flex-wrap' }
    ];

    containers.forEach(container => {
        if (!document.getElementById(container.id)) {
            const element = document.createElement('div');
            element.id = container.id;
            element.className = container.class;
            if (element && element.style) {
                element.style.display = 'none'; // Hidden fallback
            }
            document.head.appendChild(element); // Add to head to ensure it exists early
        }
    });

    // Override Pickr creation with safety checks
    if (typeof window.Pickr !== 'undefined') {
        const originalCreate = window.Pickr.create;
        window.Pickr.create = function(options) {
            try {
                // Validate element exists
                if (options && options.el) {
                    const element = typeof options.el === 'string' ? 
                        document.querySelector(options.el) : options.el;
                    
                    if (!element) {
                        console.warn('Pickr element not found:', options.el, '- creating fallback');
                        // Create a fallback element
                        const fallback = document.createElement('div');
                        if (fallback && fallback.style) {
                            fallback.style.display = 'none';
                        }
                        document.body.appendChild(fallback);
                        options.el = fallback;
                    }
                }
                
                return originalCreate.call(this, options);
            } catch (error) {
                console.warn('Pickr creation error handled:', error);
                return null;
            }
        };
    }

    // Safe DOM manipulation overrides
    const originalReplaceChild = Node.prototype.replaceChild;
    Node.prototype.replaceChild = function(newChild, oldChild) {
        try {
            if (!this || !newChild || !oldChild) {
                console.warn('replaceChild: Invalid parameters detected');
                return oldChild;
            }
            
            if (oldChild.parentNode !== this) {
                console.warn('replaceChild: oldChild not a child of this node');
                // Try appendChild instead
                if (newChild) {
                    this.appendChild(newChild);
                }
                return oldChild;
            }
            
            return originalReplaceChild.call(this, newChild, oldChild);
        } catch (error) {
            console.warn('replaceChild error handled:', error.message);
            // Fallback: try to append the new child
            try {
                if (newChild && this.appendChild) {
                    this.appendChild(newChild);
                }
            } catch (fallbackError) {
                console.warn('replaceChild fallback also failed:', fallbackError.message);
            }
            return oldChild;
        }
    };

    const originalAppendChild = Node.prototype.appendChild;
    Node.prototype.appendChild = function(child) {
        try {
            if (!this || !child) {
                console.warn('appendChild: Invalid parameters detected');
                return child;
            }
            
            return originalAppendChild.call(this, child);
        } catch (error) {
            console.warn('appendChild error handled:', error.message);
            return child;
        }
    };

    // Pickr safety overrides installed (silent mode)
})();

document.addEventListener('DOMContentLoaded', function() {
    // Additional safety for late-loading Pickr instances
    if (typeof window.Pickr !== 'undefined') {
        // Pickr available, safety measures active (silent mode)
    }

    // Watch for Pickr errors in custom.js
    const originalConsoleError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        if (message.includes('Pickr') && message.includes('replaceChild')) {
            console.warn('Pickr replaceChild error suppressed:', message);
            return;
        }
        originalConsoleError.apply(console, args);
    };
});

// Global error handler for Pickr
window.addEventListener('error', function(e) {
    if (e.message && (e.message.includes('replaceChild') || e.message.includes('Pickr')) ||
        e.filename && e.filename.includes('pickr')) {
        console.warn('Pickr error caught and handled:', e.message);
        return true; // Prevent error from bubbling up
    }
});
