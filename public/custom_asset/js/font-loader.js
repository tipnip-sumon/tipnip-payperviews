/**
 * Font Loader - Optimized font loading with fallback system
 * Handles Google Fonts loading with proper fallbacks
 */

(function() {
    'use strict';

    // Font loading configuration
    const FONT_CONFIG = {
        primary: 'Inter',
        fallback: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        googleFontsUrl: 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap',
        timeout: 3000, // 3 seconds timeout
        retryAttempts: 2
    };

    // Font loader class
    class FontLoader {
        constructor() {
            this.isLoaded = false;
            this.isLoading = false;
            this.retryCount = 0;
            this.init();
        }

        init() {
            // Check if fonts are already cached
            if (this.isFontCached()) {
                this.onFontLoaded();
                return;
            }

            // Start loading fonts
            this.loadFonts();
            
            // Set fallback timer
            this.setFallbackTimer();
        }

        isFontCached() {
            // Check if font is already available in browser cache
            try {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                
                // Test with fallback font
                context.font = '16px ' + FONT_CONFIG.fallback;
                const fallbackWidth = context.measureText('Test').width;
                
                // Test with primary font
                context.font = '16px ' + FONT_CONFIG.primary + ', ' + FONT_CONFIG.fallback;
                const primaryWidth = context.measureText('Test').width;
                
                return fallbackWidth !== primaryWidth;
            } catch (e) {
                return false;
            }
        }

        loadFonts() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            
            // Create link element for Google Fonts
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = FONT_CONFIG.googleFontsUrl;
            link.crossOrigin = 'anonymous';
            
            // Handle successful loading
            link.onload = () => {
                console.log('Font stylesheet loaded successfully');
                this.waitForFontRender();
            };
            
            // Handle loading errors
            link.onerror = () => {
                console.warn('Font stylesheet failed to load');
                this.onFontError();
            };
            
            // Add to document head
            document.head.appendChild(link);
        }

        waitForFontRender() {
            // Use Font Loading API if available
            if ('fonts' in document) {
                document.fonts.load('16px ' + FONT_CONFIG.primary).then(() => {
                    console.log('Font loaded via Font Loading API');
                    this.onFontLoaded();
                }).catch(() => {
                    console.warn('Font loading failed via Font Loading API');
                    this.onFontError();
                });
            } else {
                // Fallback method: polling
                this.pollForFont();
            }
        }

        pollForFont() {
            const startTime = Date.now();
            const poll = () => {
                if (Date.now() - startTime > FONT_CONFIG.timeout) {
                    console.warn('Font loading timeout');
                    this.onFontError();
                    return;
                }

                if (this.isFontCached()) {
                    console.log('Font loaded via polling');
                    this.onFontLoaded();
                } else {
                    setTimeout(poll, 100);
                }
            };
            poll();
        }

        onFontLoaded() {
            if (this.isLoaded) return;
            
            this.isLoaded = true;
            this.isLoading = false;
            
            // Update CSS custom property
            document.documentElement.style.setProperty('--font-status', 'loaded');
            
            // Add loaded class to body
            document.body.classList.add('font-loaded');
            document.body.classList.remove('font-loading', 'font-error');
            
            // Dispatch custom event
            window.dispatchEvent(new CustomEvent('fontLoaded', {
                detail: { font: FONT_CONFIG.primary }
            }));
            
            console.log('Font loading completed successfully');
        }

        onFontError() {
            if (this.retryCount < FONT_CONFIG.retryAttempts) {
                this.retryCount++;
                console.log(`Font loading failed, retrying (${this.retryCount}/${FONT_CONFIG.retryAttempts})`);
                this.isLoading = false;
                setTimeout(() => this.loadFonts(), 1000);
                return;
            }

            console.warn('Font loading failed after all retry attempts');
            this.isLoading = false;
            
            // Use fallback fonts
            document.documentElement.style.setProperty('--font-status', 'error');
            document.body.classList.add('font-error');
            document.body.classList.remove('font-loading', 'font-loaded');
            
            // Dispatch error event
            window.dispatchEvent(new CustomEvent('fontLoadError', {
                detail: { font: FONT_CONFIG.primary }
            }));
        }

        setFallbackTimer() {
            setTimeout(() => {
                if (!this.isLoaded && this.isLoading) {
                    console.warn('Font loading timeout reached, using fallback');
                    this.onFontError();
                }
            }, FONT_CONFIG.timeout);
        }
    }

    // Initialize font loader when DOM is ready
    function initFontLoader() {
        // Set initial loading state
        document.body.classList.add('font-loading');
        
        // Create font loader instance
        window.fontLoader = new FontLoader();
    }

    // Initialize based on document state
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFontLoader);
    } else {
        initFontLoader();
    }

    // Export for global access
    window.FontLoader = FontLoader;
    window.FONT_CONFIG = FONT_CONFIG;

})();
