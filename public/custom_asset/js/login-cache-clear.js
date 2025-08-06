/**
 * Login Cache Clear System
 * Handles cache clearing on login/logout operations
 * 
 * @author System
 * @version 1.0
 */

class LoginCacheClear {
    constructor() {
        this.init();
    }

    /**
     * Initialize login cache clearing system
     */
    init() {
        this.bindEvents();
        this.clearLoginCache();
    }

    /**
     * Bind login/logout events
     */
    bindEvents() {
        // Clear cache on login form submission
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.querySelector('#loginForm, .login-form, form[action*="login"]');
            if (loginForm) {
                loginForm.addEventListener('submit', () => {
                    this.clearLoginCache();
                });
            }

            // Clear cache on logout
            const logoutLinks = document.querySelectorAll('a[href*="logout"], .logout-btn');
            logoutLinks.forEach(link => {
                link.addEventListener('click', () => {
                    this.clearLoginCache();
                });
            });
        });
    }

    /**
     * Clear login-related cache
     */
    clearLoginCache() {
        try {
            // Clear localStorage items
            const loginCacheKeys = [
                'user_session',
                'login_data',
                'auth_token',
                'user_preferences',
                'remember_me',
                'last_login',
                'session_data'
            ];

            loginCacheKeys.forEach(key => {
                localStorage.removeItem(key);
            });

            // Clear sessionStorage
            sessionStorage.clear();

            // Clear any login-specific cookies
            this.clearLoginCookies();

            console.log('Login cache cleared successfully');
        } catch (error) {
            console.error('Error clearing login cache:', error);
        }
    }

    /**
     * Clear login-related cookies
     */
    clearLoginCookies() {
        const cookiesToClear = [
            'remember_token',
            'laravel_session',
            'auth_session',
            'user_token'
        ];

        cookiesToClear.forEach(cookieName => {
            document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
        });
    }

    /**
     * Force page reload without cache
     */
    forceReload() {
        if (window.location.reload) {
            window.location.reload(true);
        } else {
            window.location.href = window.location.href + '?t=' + new Date().getTime();
        }
    }
}

// Initialize the login cache clear system
document.addEventListener('DOMContentLoaded', () => {
    new LoginCacheClear();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoginCacheClear;
}
