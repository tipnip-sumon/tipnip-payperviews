/**
 * Login Cache Clear Script
 * This script handles cache clearing and session management for login processes
 * Only runs in development environments for debugging purposes
 */

(function() {
    'use strict';

    // Development environment detection (strict)
    const isDevelopment = () => {
        const hostname = window.location.hostname;
        const port = window.location.port;
        const protocol = window.location.protocol;
        
        // Only allow on localhost, 127.0.0.1, or .local domains
        return (hostname === 'localhost' || 
                hostname === '127.0.0.1' || 
                hostname.endsWith('.local') ||
                hostname.startsWith('local.') ||
                (port === '8000' && (hostname === 'localhost' || hostname === '127.0.0.1')) ||
                (port === '3000' && (hostname === 'localhost' || hostname === '127.0.0.1'))) &&
               // Explicitly exclude production domains
               !hostname.includes('payperviews.net') &&
               !hostname.includes('demo.') &&
               !hostname.includes('www.') &&
               protocol !== 'https:' || (protocol === 'https:' && hostname === 'localhost');
    };

    // Early exit if not in development
    if (!isDevelopment()) {
        console.log('🏭 Production environment detected - login cache tools disabled');
        return;
    }

    console.log('🔧 Development environment detected - login cache tools enabled');

    // Cache clearing functions
    const LoginCacheClear = {
        
        // Clear browser cache
        clearBrowserCache: function() {
            try {
                // Clear localStorage
                if (typeof Storage !== "undefined" && localStorage) {
                    // Clear only application-specific keys, preserve user preferences
                    const keysToRemove = [];
                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && (
                            key.startsWith('temp_') || 
                            key.startsWith('cache_') || 
                            key.startsWith('old_') ||
                            key.includes('expired_')
                        )) {
                            keysToRemove.push(key);
                        }
                    }
                    keysToRemove.forEach(key => localStorage.removeItem(key));
                }

                // Clear sessionStorage
                if (typeof Storage !== "undefined" && sessionStorage) {
                    sessionStorage.clear();
                }

                console.log('✅ Login cache cleared successfully');
                return true;
            } catch (error) {
                console.warn('⚠️ Cache clearing failed:', error);
                return false;
            }
        },

        // Clear form data
        clearFormData: function() {
            try {
                // Clear any cached form data
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    if (form.dataset.clearOnLogin === 'true') {
                        form.reset();
                    }
                });

                // Clear any temporary data attributes
                const elements = document.querySelectorAll('[data-temp]');
                elements.forEach(el => {
                    delete el.dataset.temp;
                });

                return true;
            } catch (error) {
                console.warn('⚠️ Form data clearing failed:', error);
                return false;
            }
        },

        // Handle login success
        onLoginSuccess: function() {
            // Clear old user data
            this.clearBrowserCache();
            
            // Set login timestamp
            if (typeof Storage !== "undefined" && localStorage) {
                localStorage.setItem('last_login', new Date().toISOString());
            }

            // Clear any error states
            this.clearErrorStates();

            console.log('🎉 Login success cleanup completed');
        },

        // Clear error states
        clearErrorStates: function() {
            try {
                // Remove error classes
                const errorElements = document.querySelectorAll('.is-invalid, .has-error, .error-state');
                errorElements.forEach(el => {
                    el.classList.remove('is-invalid', 'has-error', 'error-state');
                });

                // Clear error messages
                const errorMessages = document.querySelectorAll('.invalid-feedback, .error-message, .alert-danger');
                errorMessages.forEach(msg => {
                    if (msg.dataset.temporary === 'true') {
                        msg.remove();
                    }
                });

                return true;
            } catch (error) {
                console.warn('⚠️ Error state clearing failed:', error);
                return false;
            }
        },

        // Initialize cache management
        init: function() {
            try {
                // Auto-clear cache on page load if needed
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('clear_cache') === 'true') {
                    this.clearBrowserCache();
                }

                // Set up event listeners for login forms
                document.addEventListener('DOMContentLoaded', () => {
                    const loginForms = document.querySelectorAll('form[data-login-form="true"], #loginForm, .login-form');
                    loginForms.forEach(form => {
                        form.addEventListener('submit', (e) => {
                            // Clear cache before login attempt
                            this.clearBrowserCache();
                        });
                    });
                });

                // Listen for login success events
                document.addEventListener('loginSuccess', () => {
                    this.onLoginSuccess();
                });

                // Listen for logout events
                document.addEventListener('logout', () => {
                    this.clearBrowserCache();
                    this.clearFormData();
                });

                console.log('🔧 Login cache manager initialized');
                return true;
            } catch (error) {
                console.error('❌ Login cache manager initialization failed:', error);
                return false;
            }
        },

        // Manual cache clear function (can be called from outside)
        clear: function() {
            const success = this.clearBrowserCache() && this.clearFormData() && this.clearErrorStates();
            if (success) {
                console.log('✨ Manual cache clear completed');
            }
            return success;
        }
    };

    // Auto-initialize when script loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => LoginCacheClear.init());
    } else {
        LoginCacheClear.init();
    }

    // Make available globally for manual calls
    window.LoginCacheClear = LoginCacheClear;

    // Expose useful methods globally
    window.clearLoginCache = () => LoginCacheClear.clear();
    window.clearBrowserCache = () => LoginCacheClear.clearBrowserCache();

})();

// Additional utility functions for PayPerViews platform
if (typeof window !== 'undefined') {
    
    // Platform-specific cache clearing
    window.PayPerViewsCacheClear = {
        
        // Clear video-related cache
        clearVideoCache: function() {
            try {
                const videoKeys = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (
                        key.includes('video_') || 
                        key.includes('watch_') || 
                        key.includes('view_count') ||
                        key.includes('video_progress')
                    )) {
                        videoKeys.push(key);
                    }
                }
                videoKeys.forEach(key => localStorage.removeItem(key));
                console.log('🎬 Video cache cleared');
                return true;
            } catch (error) {
                console.warn('⚠️ Video cache clearing failed:', error);
                return false;
            }
        },

        // Clear user session data
        clearUserSession: function() {
            try {
                const sessionKeys = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (
                        key.includes('user_') || 
                        key.includes('session_') || 
                        key.includes('auth_') ||
                        key.includes('token_')
                    )) {
                        sessionKeys.push(key);
                    }
                }
                sessionKeys.forEach(key => localStorage.removeItem(key));
                console.log('👤 User session cache cleared');
                return true;
            } catch (error) {
                console.warn('⚠️ User session clearing failed:', error);
                return false;
            }
        },

        // Full platform cache clear
        clearAll: function() {
            const success = (
                this.clearVideoCache() && 
                this.clearUserSession() && 
                window.LoginCacheClear.clear()
            );
            
            if (success) {
                console.log('🚀 PayPerViews full cache clear completed');
            }
            return success;
        }
    };
}

console.log('📦 login-cache-clear.js loaded successfully');
