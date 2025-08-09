/**
 * Error Prevention Master Initializer
 * Loads all error prevention modules in the correct order
 * Created: August 9, 2025
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        debug: window.location.hostname === 'localhost' || window.location.hostname.includes('dev'),
        loadTimeout: 5000,
        retryAttempts: 3
    };

    // Immediate DOM protection (before other scripts load)
    function setupImmediateProtection() {
        // Create essential elements that scripts expect
        const essentialElements = [
            { id: 'pickrContainerPrimary', tag: 'div', class: 'pickr-container' },
            { id: 'pickrContainerBackground', tag: 'div', class: 'pickr-container' },
            { id: 'themeContainerPrimary', tag: 'div', class: 'theme-container' },
            { id: 'themeContainerBackground', tag: 'div', class: 'theme-container' },
            { id: 'switcher-primary-color', tag: 'button', class: 'switcher-btn' },
            { id: 'switcher-primary-color1', tag: 'button', class: 'switcher-btn' },
            { id: 'switcher-primary-color2', tag: 'button', class: 'switcher-btn' },
            { id: 'switcher-primary-color3', tag: 'button', class: 'switcher-btn' },
            { id: 'switcher-primary-color4', tag: 'button', class: 'switcher-btn' },
            { id: 'reset-all', tag: 'button', class: 'switcher-btn' }
        ];

        const container = document.createElement('div');
        container.id = 'error-prevention-elements';
        container.style.display = 'none';
        
        essentialElements.forEach(elem => {
            if (!document.getElementById(elem.id)) {
                const element = document.createElement(elem.tag);
                element.id = elem.id;
                element.className = elem.class;
                container.appendChild(element);
            }
        });
        
        document.documentElement.appendChild(container);
    }

    // Override critical functions before other scripts load
    function setupCriticalOverrides() {
        // Override querySelector to return fallback elements
        const originalQuerySelector = Document.prototype.querySelector;
        Document.prototype.querySelector = function(selector) {
            const result = originalQuerySelector.call(this, selector);
            if (!result && selector.startsWith('#switcher-')) {
                // Return a fallback element for switcher selectors
                const fallback = document.createElement('button');
                fallback.id = selector.replace('#', '');
                fallback.addEventListener = function(event, handler) {
                    console.warn(`Event listener added to fallback element: ${selector}`);
                    return handler;
                };
                return fallback;
            }
            return result;
        };

        // Override addEventListener to handle null elements gracefully
        const originalAddEventListener = EventTarget.prototype.addEventListener;
        EventTarget.prototype.addEventListener = function(type, listener, options) {
            try {
                if (!this) {
                    console.warn('addEventListener called on null element');
                    return;
                }
                return originalAddEventListener.call(this, type, function(event) {
                    try {
                        return typeof listener === 'function' ? listener.call(this, event) : null;
                    } catch (error) {
                        console.warn(`Event listener error (${type}):`, error.message);
                    }
                }, options);
            } catch (error) {
                console.warn('addEventListener override error:', error.message);
            }
        };

        // Override Node methods for DOM manipulation safety
        const originalReplaceChild = Node.prototype.replaceChild;
        Node.prototype.replaceChild = function(newChild, oldChild) {
            try {
                if (!this || !newChild || !oldChild) return oldChild;
                if (oldChild.parentNode !== this) {
                    if (newChild) this.appendChild(newChild);
                    return oldChild;
                }
                return originalReplaceChild.call(this, newChild, oldChild);
            } catch (error) {
                console.warn('replaceChild error handled:', error.message);
                try {
                    if (newChild) this.appendChild(newChild);
                } catch (e) {}
                return oldChild;
            }
        };
    }

    // Module loading tracker
    const modules = {
        'global-error-handler': { loaded: false, required: true },
        'dashboard-vars-fix': { loaded: false, required: true },
        'custom-switcher-fix': { loaded: false, required: true },
        'pickr-fix': { loaded: false, required: true },
        'metamask-fix': { loaded: false, required: false }
    };

    // Safe script loader
    function loadScript(src, callback, isRequired = false) {
        const script = document.createElement('script');
        script.src = src;
        script.async = false; // Load synchronously to ensure order
        
        let loaded = false;
        
        script.onload = function() {
            if (!loaded) {
                loaded = true;
                const moduleName = src.split('/').pop().replace('.js', '');
                if (modules[moduleName]) {
                    modules[moduleName].loaded = true;
                }
                if (callback) callback(null);
                if (config.debug) console.log(`✓ Loaded: ${src}`);
            }
        };
        
        script.onerror = function() {
            if (!loaded) {
                loaded = true;
                const error = new Error(`Failed to load: ${src}`);
                if (callback) callback(error);
                if (isRequired) {
                    console.error(`✗ Required module failed: ${src}`);
                } else {
                    console.warn(`⚠ Optional module failed: ${src}`);
                }
            }
        };
        
        // Timeout handling
        setTimeout(() => {
            if (!loaded) {
                loaded = true;
                const error = new Error(`Timeout loading: ${src}`);
                if (callback) callback(error);
                console.warn(`⏰ Timeout: ${src}`);
            }
        }, config.loadTimeout);
        
        document.head.appendChild(script);
    }

    // Safe CSS loader
    function loadCSS(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        
        link.onload = function() {
            if (config.debug) console.log(`✓ CSS Loaded: ${href}`);
        };
        
        link.onerror = function() {
            console.warn(`✗ CSS Failed: ${href}`);
        };
        
        document.head.appendChild(link);
    }

    // Initialize error prevention system
    function initializeErrorPrevention() {
        const basePath = '/assets_custom/js/';
        const cssPath = '/assets_custom/css/';
        
        // Load CSS first
        loadCSS(cssPath + 'error-prevention.css');
        
        // Load critical fixes first (synchronously)
        const criticalModules = [
            { name: 'global-error-handler', required: true },
            { name: 'pickr-fix', required: true },
            { name: 'custom-switcher-fix', required: true },
            { name: 'dashboard-vars-fix', required: true }
        ];
        
        let currentIndex = 0;
        
        function loadNext() {
            if (currentIndex >= criticalModules.length) {
                // Load optional modules
                loadScript(basePath + 'metamask-fix.js', null, false);
                onAllModulesLoaded();
                return;
            }
            
            const module = criticalModules[currentIndex];
            currentIndex++;
            
            loadScript(
                basePath + module.name + '.js',
                function(error) {
                    if (error && module.required) {
                        console.error(`Critical module failed: ${module.name}`, error);
                    }
                    loadNext();
                },
                module.required
            );
        }
        
        loadNext();
    }

    // Called when all modules are loaded
    function onAllModulesLoaded() {
        // Verify critical modules loaded
        const criticalModules = Object.keys(modules).filter(name => modules[name].required);
        const failedCritical = criticalModules.filter(name => !modules[name].loaded);
        
        if (failedCritical.length > 0) {
            console.error('Critical error prevention modules failed:', failedCritical);
        }
        
        // Initialize debug info in development
        if (config.debug) {
            createDebugInfo();
        }
        
        // Dispatch event that error prevention is ready
        window.dispatchEvent(new CustomEvent('errorPreventionReady', {
            detail: { modules, config }
        }));
        
        console.log('Error prevention system initialized', {
            modules: Object.keys(modules).filter(name => modules[name].loaded),
            failed: Object.keys(modules).filter(name => !modules[name].loaded),
            timestamp: new Date().toISOString()
        });
    }

    // Create debug information display
    function createDebugInfo() {
        const debugDiv = document.createElement('div');
        debugDiv.className = 'error-stats';
        debugDiv.innerHTML = 'Error Prevention: Active';
        document.body.appendChild(debugDiv);
        
        // Update debug info periodically
        setInterval(() => {
            if (window.errorStats) {
                debugDiv.innerHTML = `Errors: ${window.errorStats.handled}/${window.errorStats.total} | Suppressed: ${window.errorStats.suppressed}`;
            }
        }, 5000);
    }

    // Setup immediate protection
    setupImmediateProtection();
    setupCriticalOverrides();

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeErrorPrevention);
    } else {
        initializeErrorPrevention();
    }

    // Export for external use
    window.errorPrevention = {
        config,
        modules,
        reload: initializeErrorPrevention
    };

    if (config.debug) {
        console.log('Error Prevention Master Initializer loaded');
    }
})();
