/**
 * Session Manager - Handles CSRF token management and session locking
 * Prevents multiple concurrent requests that could cause session conflicts
 */
class SessionManager {
    constructor() {
        this.isRefreshing = false;
        this.refreshPromise = null;
        this.requestQueue = [];
        this.initialized = false;
        this.init();
    }

    init() {
        if (this.initialized) return;
        
        console.log('SessionManager: Initializing...');
        
        // Refresh CSRF token periodically (every 30 seconds)
        setInterval(() => {
            this.refreshCSRFToken();
        }, 30000);

        // Refresh token when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshCSRFToken();
            }
        });

        // Intercept all forms to ensure they have fresh CSRF tokens
        this.interceptForms();
        
        this.initialized = true;
        console.log('SessionManager: Initialized successfully');
    }

    /**
     * Get current CSRF token from meta tag
     */
    getCurrentToken() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        return metaToken ? metaToken.getAttribute('content') : null;
    }

    /**
     * Refresh CSRF token with locking to prevent concurrent requests
     */
    async refreshCSRFToken() {
        // If already refreshing, return the existing promise
        if (this.isRefreshing && this.refreshPromise) {
            console.log('SessionManager: CSRF refresh already in progress, waiting...');
            return this.refreshPromise;
        }

        // Set lock and create promise
        this.isRefreshing = true;
        this.refreshPromise = this._performCSRFRefresh();

        try {
            const result = await this.refreshPromise;
            return result;
        } finally {
            // Always release the lock
            this.isRefreshing = false;
            this.refreshPromise = null;
        }
    }

    /**
     * Internal method to perform the actual CSRF refresh
     */
    async _performCSRFRefresh() {
        try {
            console.log('SessionManager: Refreshing CSRF token...');
            
            const response = await fetch('/csrf-refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCurrentToken()
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.csrf_token) {
                // Update meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    metaToken.setAttribute('content', data.csrf_token);
                }

                // Update all forms with hidden CSRF inputs
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.csrf_token;
                });

                console.log('SessionManager: CSRF token refreshed successfully');
                return data.csrf_token;
            } else {
                throw new Error('No CSRF token in response');
            }
        } catch (error) {
            console.error('SessionManager: Failed to refresh CSRF token:', error);
            
            // If it's a session expired error, reload the page
            if (error.message.includes('419') || error.message.includes('expired')) {
                console.log('SessionManager: Session expired, reloading page...');
                window.location.reload();
                return null;
            }
            
            throw error;
        }
    }

    /**
     * Make a safe AJAX request with automatic CSRF token handling
     */
    async makeRequest(url, options = {}) {
        // Ensure we have a fresh token
        await this.refreshCSRFToken();

        // Set default headers, but don't set Content-Type for FormData
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': this.getCurrentToken(),
            ...options.headers
        };

        // Only set Content-Type to application/json if we're not sending FormData
        if (!(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers,
                credentials: 'same-origin'
            });

            // Handle CSRF token mismatch
            if (response.status === 419) {
                console.log('SessionManager: CSRF token mismatch, refreshing and retrying...');
                await this.refreshCSRFToken();
                
                // Update headers with new token
                headers['X-CSRF-TOKEN'] = this.getCurrentToken();
                
                // Retry the request
                return fetch(url, {
                    ...options,
                    headers,
                    credentials: 'same-origin'
                });
            }

            return response;
        } catch (error) {
            console.error('SessionManager: Request failed:', error);
            throw error;
        }
    }

    /**
     * Intercept form submissions to ensure fresh CSRF tokens
     */
    interceptForms() {
        // Handle existing forms
        document.querySelectorAll('form').forEach(form => {
            this.attachFormHandler(form);
        });

        // Handle dynamically added forms
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.tagName === 'FORM') {
                            this.attachFormHandler(node);
                        }
                        // Also check for forms within added elements
                        node.querySelectorAll?.('form').forEach(form => {
                            this.attachFormHandler(form);
                        });
                    }
                });
            });
        });

        // Start observing when DOM is ready
        const startObserving = () => {
            if (document.body) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            } else {
                // Wait for DOM to be ready
                document.addEventListener('DOMContentLoaded', () => {
                    if (document.body) {
                        observer.observe(document.body, {
                            childList: true,
                            subtree: true
                        });
                    }
                });
            }
        };
        
        startObserving();
    }

    /**
     * Attach form submission handler
     */
    attachFormHandler(form) {
        // Skip if already handled
        if (form.dataset.sessionManagerHandled) return;
        
        form.dataset.sessionManagerHandled = 'true';
        
        form.addEventListener('submit', async (e) => {
            // Don't handle forms that explicitly opt out
            if (form.dataset.skipSessionManager === 'true') return;
            
            try {
                // Refresh CSRF token before submission
                await this.refreshCSRFToken();
                
                // Update form token
                const tokenInput = form.querySelector('input[name="_token"]');
                if (tokenInput) {
                    tokenInput.value = this.getCurrentToken();
                }
            } catch (error) {
                console.error('SessionManager: Failed to refresh CSRF token before form submission:', error);
                // Let the form submit anyway, server will handle the error
            }
        });
    }

    /**
     * Validate sponsor with automatic token handling
     */
    async validateSponsor(sponsor) {
        try {
            const response = await this.makeRequest('/validate-sponsor', {
                method: 'POST',
                body: JSON.stringify({ sponsor: sponsor })
            });

            return await response.json();
        } catch (error) {
            console.error('SessionManager: Sponsor validation failed:', error);
            throw error;
        }
    }

    /**
     * Validate username with automatic token handling
     */
    async validateUsername(username) {
        try {
            const response = await this.makeRequest('/validate-username', {
                method: 'POST',
                body: JSON.stringify({ username: username })
            });

            return await response.json();
        } catch (error) {
            console.error('SessionManager: Username validation failed:', error);
            throw error;
        }
    }

    /**
     * Validate sponsor with automatic token handling
     */
    async validateSponsor(sponsor) {
        try {
            const response = await this.makeRequest('/validate-sponsor', {
                method: 'POST',
                body: JSON.stringify({ sponsor: sponsor })
            });

            return await response.json();
        } catch (error) {
            console.error('SessionManager: Sponsor validation failed:', error);
            throw error;
        }
    }

    /**
     * Validate username with automatic token handling
     */
    async validateUsername(username) {
        try {
            const response = await this.makeRequest('/validate-username', {
                method: 'POST',
                body: JSON.stringify({ username: username })
            });

            return await response.json();
        } catch (error) {
            console.error('SessionManager: Username validation failed:', error);
            throw error;
        }
    }

    /**
     * Validate email with automatic token handling
     */
    async validateEmail(email) {
        try {
            const response = await this.makeRequest('/validate-email', {
                method: 'POST',
                body: JSON.stringify({ email: email })
            });

            return await response.json();
        } catch (error) {
            console.error('SessionManager: Email validation failed:', error);
            throw error;
        }
    }
}

// Create global instance
window.sessionManager = new SessionManager();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SessionManager;
}
