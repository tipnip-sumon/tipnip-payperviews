/**
 * Font Loading Handler with Fallback System
 * Handles Google Fonts connection failures gracefully
 */

class FontLoader {
    constructor() {
        this.fontTimeout = 2000; // Reduced to 2 seconds for faster fallback
        this.fontFamily = 'Inter';
        this.fallbackApplied = false;
        this.loadingStartTime = Date.now();
        this.checkInterval = 100; // Check every 100ms instead of default
        
        this.init();
    }

    init() {
        // Apply loading state immediately
        document.documentElement.classList.add('font-loading');
        
        // Check if fonts are already available (cached)
        if (this.areFontsAlreadyLoaded()) {
            // console.log('Fonts already loaded from cache');
            this.onFontLoadSuccess();
            return;
        }
        
        // Start font loading detection
        this.detectFontLoading();
        
        // Set up timeout fallback
        this.setupFontTimeout();
        
        // Monitor for network errors
        this.monitorNetworkErrors();
    }
    
    areFontsAlreadyLoaded() {
        if (!document.fonts) return false;
        
        try {
            // Check if our primary fonts are already loaded
            const interLoaded = document.fonts.check('16px Inter');
            const poppinsLoaded = document.fonts.check('16px Poppins');
            
            return interLoaded || poppinsLoaded;
        } catch (error) {
            return false;
        }
    }

    detectFontLoading() {
        if ('fonts' in document) {
            // Use Font Loading API if available
            this.useNativeFontAPI();
        } else {
            // Fallback to manual detection
            this.useManualDetection();
        }
    }

    useNativeFontAPI() {
        // Use a more efficient approach with timeout race
        const fontPromises = [
            document.fonts.load('400 16px Inter'),
            document.fonts.load('500 16px Inter'),
            document.fonts.load('400 16px Poppins'),
            document.fonts.load('500 16px Poppins')
        ];

        // Race between font loading and timeout
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Font loading timeout')), this.fontTimeout);
        });

        Promise.race([
            Promise.allSettled(fontPromises),
            timeoutPromise
        ])
        .then((results) => {
            // Check if we got results from allSettled (not timeout)
            if (Array.isArray(results)) {
                const successful = results.filter(result => result.status === 'fulfilled').length;
                // console.log(`${successful}/${results.length} fonts loaded successfully`);
                
                if (successful > 0) {
                    this.onFontLoadSuccess();
                } else {
                    console.warn('No fonts loaded successfully');
                    this.onFontLoadFailure();
                }
            } else {
                // Timeout occurred
                this.onFontLoadFailure();
            }
        })
        .catch((error) => {
            console.warn('Font loading failed:', error.message);
            this.onFontLoadFailure();
        });

        // Also listen for font loading events as backup
        const fontLoadHandler = () => {
            if (!this.fallbackApplied) {
                // Double-check that fonts are actually loaded
                const interLoaded = document.fonts.check('16px Inter');
                const poppinsLoaded = document.fonts.check('16px Poppins');
                
                if (interLoaded || poppinsLoaded) {
                    // console.log('Fonts detected via loadingdone event');
                    this.onFontLoadSuccess();
                }
            }
        };

        document.fonts.addEventListener('loadingdone', fontLoadHandler, { once: true });
    }

    useManualDetection() {
        // Create test elements to detect font loading
        const testElement = this.createFontTestElement();
        document.body.appendChild(testElement);

        const fallbackWidth = testElement.offsetWidth;
        
        // Check periodically if font has loaded
        const checkInterval = setInterval(() => {
            if (testElement.offsetWidth !== fallbackWidth) {
                clearInterval(checkInterval);
                document.body.removeChild(testElement);
                this.onFontLoadSuccess();
            }
        }, 100);

        // Cleanup after timeout
        setTimeout(() => {
            clearInterval(checkInterval);
            if (document.body.contains(testElement)) {
                document.body.removeChild(testElement);
                this.onFontLoadFailure();
            }
        }, this.fontTimeout);
    }

    createFontTestElement() {
        const element = document.createElement('div');
        element.style.cssText = `
            position: absolute;
            left: -9999px;
            top: -9999px;
            font-size: 100px;
            font-family: Arial, sans-serif;
            visibility: hidden;
        `;
        element.textContent = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return element;
    }

    setupFontTimeout() {
        setTimeout(() => {
            if (!this.fallbackApplied) {
                // console.log('Font loading timeout (2s) - applying system font fallback');
                this.onFontLoadFailure();
            }
        }, this.fontTimeout);
    }

    monitorNetworkErrors() {
        // Listen for network errors
        window.addEventListener('error', (event) => {
            if (event.target && event.target.href && 
                (event.target.href.includes('fonts.googleapis.com') || 
                 event.target.href.includes('fonts.gstatic.com'))) {
                console.warn('Google Fonts network error detected:', event.target.href);
                this.onFontLoadFailure();
            }
        }, true);

        // Monitor fetch failures with better error handling
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            return originalFetch(...args).catch(error => {
                // Only handle font-related fetch failures
                if (args[0] && (
                    args[0].includes('fonts.g') || 
                    args[0].includes('gstatic.com') ||
                    args[0].includes('googleapis.com')
                )) {
                    console.warn('Font fetch failed:', error);
                    // Don't trigger failure callback for CORS errors as they're handled by service worker
                    if (!error.message.includes('CORS') && !error.message.includes('Failed to fetch')) {
                        this.onFontLoadFailure();
                    }
                }
                throw error;
            });
        };
    }

    onFontLoadSuccess() {
        if (this.fallbackApplied) return;
        this.fallbackApplied = true;
        
        const loadTime = Date.now() - this.loadingStartTime;
        // console.log(`✓ Custom fonts loaded successfully in ${loadTime}ms`);
        
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('fonts-loaded');
        
        // Dispatch custom event
        this.dispatchFontEvent('fontloaded', { loadTime });
    }

    onFontLoadFailure() {
        if (this.fallbackApplied) return;
        this.fallbackApplied = true;
        
        const loadTime = Date.now() - this.loadingStartTime;
        // console.log(`Font loading completed with system fallback after ${loadTime}ms`);
        
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('font-failed');
        
        // Apply system font stack
        this.applySystemFonts();
        
        // Dispatch custom event (without automatic diagnostics)
        this.dispatchFontEvent('fontfailed', { loadTime });
        
        // Don't show notification for timeout - it's normal behavior
        // this.showFallbackNotification();
    }

    applySystemFonts() {
        const style = document.createElement('style');
        style.textContent = `
            * {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 
                           'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 
                           'Helvetica Neue', sans-serif !important;
            }
        `;
        document.head.appendChild(style);
    }

    dispatchFontEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { 
            detail,
            bubbles: true,
            cancelable: false 
        });
        document.dispatchEvent(event);
    }

    showFallbackNotification() {
        // Only show if user seems to have network issues
        if (navigator.onLine === false) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                background: #f59e0b;
                color: white;
                padding: 12px 16px;
                border-radius: 6px;
                font-size: 14px;
                z-index: 10000;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                max-width: 300px;
            `;
            notification.innerHTML = `
                <strong>Network Issue Detected</strong><br>
                Using system fonts for better performance.
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
    }

    // Method to preload fonts for better performance
    preloadFonts() {
        const weights = ['300', '400', '500', '600', '700'];
        weights.forEach(weight => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'font';
            link.type = 'font/woff2';
            link.crossOrigin = 'anonymous';
            link.href = `https://fonts.gstatic.com/s/inter/v19/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIa1ZL7W6T7GlUNQw.woff2`;
            
            link.onerror = () => {
                console.warn(`Failed to preload font weight ${weight}`);
                this.onFontLoadFailure();
            };
            
            document.head.appendChild(link);
        });
    }
}

// Initialize font loader when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.fontLoader = new FontLoader();
    
    // Listen for font events
    document.addEventListener('fontloaded', (e) => {
        console.log('Font loaded event:', e.detail);
    });
    
    document.addEventListener('fontfailed', (e) => {
        console.warn('Font failed event:', e.detail);
    });
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FontLoader;
}
