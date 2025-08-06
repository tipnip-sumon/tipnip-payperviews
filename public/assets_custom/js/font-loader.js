/**
 * Font Loading Handler with Fallback System
 * Handles Google Fonts connection failures gracefully
 */

class FontLoader {
    constructor() {
        this.fontTimeout = 3000; // 3 second timeout
        this.fontFamily = 'Inter';
        this.fallbackApplied = false;
        this.loadingStartTime = Date.now();
        
        this.init();
    }

    init() {
        // Apply loading state immediately
        document.documentElement.classList.add('font-loading');
        
        // Start font loading detection
        this.detectFontLoading();
        
        // Set up timeout fallback
        this.setupFontTimeout();
        
        // Monitor for network errors
        this.monitorNetworkErrors();
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
        const fontPromises = [
            document.fonts.load('400 16px Inter'),
            document.fonts.load('500 16px Inter'),
            document.fonts.load('600 16px Inter'),
            document.fonts.load('700 16px Inter')
        ];

        Promise.all(fontPromises)
            .then(() => {
                this.onFontLoadSuccess();
            })
            .catch((error) => {
                console.warn('Font loading failed:', error);
                this.onFontLoadFailure();
            });

        // Also listen for font loading events
        document.fonts.addEventListener('loadingdone', () => {
            this.onFontLoadSuccess();
        });

        document.fonts.addEventListener('loadingerror', () => {
            this.onFontLoadFailure();
        });
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
                console.warn('Font loading timeout - applying fallback');
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

        // Monitor fetch failures
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            return originalFetch(...args).catch(error => {
                if (args[0] && args[0].includes('fonts.g')) {
                    console.warn('Font fetch failed:', error);
                    this.onFontLoadFailure();
                }
                throw error;
            });
        };
    }

    onFontLoadSuccess() {
        if (this.fallbackApplied) return;
        
        const loadTime = Date.now() - this.loadingStartTime;
        console.log(`Fonts loaded successfully in ${loadTime}ms`);
        
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('font-loaded');
        
        // Dispatch custom event
        this.dispatchFontEvent('fontloaded', { loadTime });
    }

    onFontLoadFailure() {
        if (this.fallbackApplied) return;
        this.fallbackApplied = true;
        
        const loadTime = Date.now() - this.loadingStartTime;
        console.warn(`Font loading failed after ${loadTime}ms - using system fonts`);
        
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('font-failed');
        
        // Apply system font stack
        this.applySystemFonts();
        
        // Dispatch custom event
        this.dispatchFontEvent('fontfailed', { loadTime });
        
        // Show user notification if needed
        this.showFallbackNotification();
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
