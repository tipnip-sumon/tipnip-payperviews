/**
 * Session Manager - Enhanced session management with CSRF protection
 * Handles session expiration, CSRF token refresh, and secure form submissions
 */

(function() {
    'use strict';

    // Session configuration
    const SESSION_CONFIG = {
        csrfRefreshUrl: '/csrf-refresh',
        sessionCheckUrl: '/session-check',
        refreshInterval: 300000, // 5 minutes
        warningTime: 60000, // 1 minute warning
        enableLogging: true,
        enableNetworkRetry: true,
        maxRetryAttempts: 3
    };

    // Session manager class
    class SessionManager {
        constructor() {
            this.isActive = true;
            this.lastActivity = Date.now();
            this.warningShown = false;
            this.refreshInProgress = false;
            this.networkRetryCount = 0;
            
            this.init();
        }

        init() {
            this.setupEventListeners();
            this.startSessionMonitoring();
            this.updateCSRFTokens();
        }

        setupEventListeners() {
            // Track user activity
            const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
            
            activityEvents.forEach(event => {
                document.addEventListener(event, () => {
                    this.updateActivity();
                }, { passive: true });
            });

            // Page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.updateActivity();
                    this.checkSession();
                }
            });

            // Network status changes
            window.addEventListener('online', () => {
                this.log('Network reconnected, checking session');
                this.checkSession();
            });

            // Before page unload
            window.addEventListener('beforeunload', () => {
                this.isActive = false;
            });
        }

        updateActivity() {
            this.lastActivity = Date.now();
            if (this.warningShown) {
                this.warningShown = false;
                this.hideSessionWarning();
            }
        }

        startSessionMonitoring() {
            setInterval(() => {
                if (this.isActive && !document.hidden) {
                    this.checkSessionTimeout();
                    this.refreshCSRFToken();
                }
            }, SESSION_CONFIG.refreshInterval);
        }

        async checkSession() {
            try {
                const response = await this.makeRequest(SESSION_CONFIG.sessionCheckUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.handleSessionStatus(data);
                } else if (response.status === 419) {
                    this.handleSessionExpired();
                }
            } catch (error) {
                this.log('Session check failed: ' + error.message);
            }
        }

        handleSessionStatus(data) {
            if (data.active) {
                this.isActive = true;
                this.updateCSRFToken(data.csrf_token);
            } else {
                this.handleSessionExpired();
            }
        }

        checkSessionTimeout() {
            const timeInactive = Date.now() - this.lastActivity;
            const sessionTimeout = 24 * 60 * 60 * 1000; // 24 hours
            const warningTime = sessionTimeout - SESSION_CONFIG.warningTime;

            if (timeInactive >= warningTime && !this.warningShown) {
                this.showSessionWarning();
            }

            if (timeInactive >= sessionTimeout) {
                this.handleSessionExpired();
            }
        }

        async refreshCSRFToken() {
            if (this.refreshInProgress) {
                this.log('CSRF refresh already in progress');
                return;
            }

            this.refreshInProgress = true;

            try {
                const response = await this.makeRequest(SESSION_CONFIG.csrfRefreshUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCurrentCSRFToken()
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.updateCSRFToken(data.csrf_token);
                    this.networkRetryCount = 0;
                    this.log('CSRF token refreshed successfully');
                    return data.csrf_token;
                } else if (response.status === 419) {
                    this.handleSessionExpired();
                    return null;
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            } catch (error) {
                this.log('CSRF token refresh failed: ' + error.message);
                
                if (SESSION_CONFIG.enableNetworkRetry && this.networkRetryCount < SESSION_CONFIG.maxRetryAttempts) {
                    this.networkRetryCount++;
                    setTimeout(() => this.refreshCSRFToken(), 2000 * this.networkRetryCount);
                }
                
                return null;
            } finally {
                this.refreshInProgress = false;
            }
        }

        getCurrentCSRFToken() {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            return metaToken ? metaToken.getAttribute('content') : null;
        }

        updateCSRFToken(newToken) {
            if (!newToken) return;

            // Update meta tag
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                metaToken.setAttribute('content', newToken);
            }

            // Update all CSRF input fields
            const csrfInputs = document.querySelectorAll('input[name="_token"]');
            csrfInputs.forEach(input => {
                input.value = newToken;
            });

            // Update any CSRF headers in ongoing requests
            this.currentCSRFToken = newToken;
        }

        updateCSRFTokens() {
            const currentToken = this.getCurrentCSRFToken();
            if (currentToken) {
                this.updateCSRFToken(currentToken);
            }
        }

        async makeRequest(url, options = {}) {
            // Ensure CSRF token is included
            const csrfToken = this.getCurrentCSRFToken();
            
            if (csrfToken && !options.headers) {
                options.headers = {};
            }
            
            if (csrfToken && options.headers && !options.headers['X-CSRF-TOKEN']) {
                options.headers['X-CSRF-TOKEN'] = csrfToken;
            }

            return fetch(url, options);
        }

        showSessionWarning() {
            this.warningShown = true;
            
            // Create or show session warning modal
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Session Expiring Soon',
                    text: 'Your session will expire in 1 minute due to inactivity. Click anywhere to stay logged in.',
                    icon: 'warning',
                    timer: 60000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Stay Logged In',
                    allowOutsideClick: true
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss === Swal.DismissReason.backdrop) {
                        this.updateActivity();
                    }
                });
            } else {
                // Fallback notification
                const notification = this.createNotification(
                    'Session Expiring Soon',
                    'Your session will expire in 1 minute. Click to stay logged in.',
                    () => this.updateActivity()
                );
                document.body.appendChild(notification);
            }
        }

        hideSessionWarning() {
            // Close any active session warning
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            // Remove any fallback notifications
            const notifications = document.querySelectorAll('.session-notification');
            notifications.forEach(notification => notification.remove());
        }

        handleSessionExpired() {
            this.isActive = false;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired. Please log in again.',
                    icon: 'error',
                    confirmButtonText: 'Login Again',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    this.redirectToLogin();
                });
            } else {
                alert('Your session has expired. Please log in again.');
                this.redirectToLogin();
            }
        }

        redirectToLogin() {
            const currentPath = window.location.pathname + window.location.search;
            const loginUrl = `/login${currentPath !== '/' ? '?redirect=' + encodeURIComponent(currentPath) : ''}`;
            window.location.href = loginUrl;
        }

        createNotification(title, message, onClick) {
            const notification = document.createElement('div');
            notification.className = 'session-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <h4>${title}</h4>
                    <p>${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()">OK</button>
                </div>
            `;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 15px;
                border-radius: 5px;
                z-index: 10000;
                max-width: 300px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            `;
            
            if (onClick) {
                notification.addEventListener('click', onClick);
            }
            
            return notification;
        }

        // Validation methods for forms
        async validateUsername(username) {
            await this.refreshCSRFToken();
            
            const response = await this.makeRequest('/validate-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ username })
            });

            return response.json ? await response.json() : response;
        }

        async validateEmail(email) {
            await this.refreshCSRFToken();
            
            const response = await this.makeRequest('/validate-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email })
            });

            return response.json ? await response.json() : response;
        }

        async validateSponsor(sponsor) {
            await this.refreshCSRFToken();
            
            const response = await this.makeRequest('/validate-sponsor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ sponsor })
            });

            return response.json ? await response.json() : response;
        }

        log(message) {
            if (SESSION_CONFIG.enableLogging) {
                console.log(`[SessionManager] ${message}`);
            }
        }
    }

    // Initialize session manager
    function initSessionManager() {
        window.sessionManager = new SessionManager();
        
        // Global convenience methods
        window.refreshCSRF = () => window.sessionManager.refreshCSRFToken();
        window.checkSession = () => window.sessionManager.checkSession();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSessionManager);
    } else {
        initSessionManager();
    }

    // Export class
    window.SessionManager = SessionManager;

})();
