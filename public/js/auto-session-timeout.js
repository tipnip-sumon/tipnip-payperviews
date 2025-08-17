/**
 * Automatic Session Timeout Handler with Complete Session Destruction
 * Monitors user activity and handles automatic logout
 */

class AutoSessionTimeout {
    constructor(options = {}) {
        this.options = {
            timeoutMinutes: options.timeoutMinutes || 30, // Default 30 minutes
            warningMinutes: options.warningMinutes || 5,  // Show warning 5 minutes before timeout
            checkInterval: options.checkInterval || 30000, // Check every 30 seconds
            idleEvents: ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'],
            enableWarnings: options.enableWarnings !== false,
            enableLogging: options.enableLogging !== false,
            ...options
        };

        this.lastActivity = Date.now();
        this.warningShown = false;
        this.checkTimer = null;
        this.warningTimer = null;
        this.isActive = true;

        this.init();
    }

    init() {
        if (!this.isUserLoggedIn()) {
            return;
        }

        this.bindActivityEvents();
        this.startTimeoutCheck();
        
        if (this.options.enableLogging) {
            console.log('AutoSessionTimeout initialized', {
                timeoutMinutes: this.options.timeoutMinutes,
                warningMinutes: this.options.warningMinutes
            });
        }
    }

    isUserLoggedIn() {
        // Check if user is authenticated (you can customize this)
        return document.querySelector('meta[name="user-authenticated"]')?.content === 'true' ||
               document.body.classList.contains('authenticated') ||
               window.isAuthenticated === true;
    }

    bindActivityEvents() {
        this.options.idleEvents.forEach(event => {
            document.addEventListener(event, () => {
                this.updateActivity();
            }, true);
        });

        // Handle visibility change (tab switching)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.updateActivity();
            }
        });
    }

    updateActivity() {
        if (!this.isActive) return;

        this.lastActivity = Date.now();
        
        if (this.warningShown) {
            this.hideWarning();
        }

        // Reset warning flag
        this.warningShown = false;
    }

    startTimeoutCheck() {
        this.checkTimer = setInterval(() => {
            this.checkTimeout();
        }, this.options.checkInterval);
    }

    checkTimeout() {
        if (!this.isActive || !this.isUserLoggedIn()) {
            return;
        }

        const now = Date.now();
        const inactiveTime = now - this.lastActivity;
        const inactiveMinutes = inactiveTime / (1000 * 60);

        // Check for server-side session timeout warnings
        this.checkServerTimeout();

        // Show warning before timeout
        if (inactiveMinutes >= (this.options.timeoutMinutes - this.options.warningMinutes) && !this.warningShown) {
            this.showTimeoutWarning(this.options.timeoutMinutes - inactiveMinutes);
        }

        // Auto logout if timeout reached
        if (inactiveMinutes >= this.options.timeoutMinutes) {
            this.performAutoLogout('client_timeout', inactiveMinutes);
        }
    }

    checkServerTimeout() {
        // Make a lightweight request to check server session status
        fetch('/user/session-check', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Check for timeout headers from server
            if (response.headers.get('X-Session-Timeout-Warning') === 'true') {
                const remainingMinutes = parseFloat(response.headers.get('X-Session-Remaining-Minutes') || 0);
                if (remainingMinutes > 0 && !this.warningShown) {
                    this.showTimeoutWarning(remainingMinutes);
                }
            }

            if (!response.ok && response.status === 401) {
                // Session expired on server
                this.performAutoLogout('server_timeout', 0);
            }
        })
        .catch(error => {
            if (this.options.enableLogging) {
                console.warn('Session check failed:', error);
            }
        });
    }

    showTimeoutWarning(remainingMinutes) {
        if (!this.options.enableWarnings || this.warningShown) {
            return;
        }

        this.warningShown = true;
        const minutes = Math.max(1, Math.ceil(remainingMinutes));

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Timeout Warning',
                html: `
                    <div class="text-center">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <p>Your session will expire in <strong>${minutes} minute(s)</strong> due to inactivity.</p>
                        <p class="text-muted">Click "Stay Logged In" to continue your session.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-clock me-1"></i>Stay Logged In',
                cancelButtonText: '<i class="fas fa-sign-out-alt me-1"></i>Logout Now',
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: minutes * 60 * 1000, // Auto close after remaining time
                timerProgressBar: true,
                customClass: {
                    popup: 'session-timeout-warning'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // User chose to stay logged in
                    this.extendSession();
                } else if (result.isDismissed) {
                    // User chose to logout or timer expired
                    this.performAutoLogout('user_choice', 0);
                }
            });
        } else {
            // Fallback to native confirm dialog
            const stayLoggedIn = confirm(
                `Your session will expire in ${minutes} minute(s) due to inactivity.\n\n` +
                'Click "OK" to stay logged in, or "Cancel" to logout now.'
            );

            if (stayLoggedIn) {
                this.extendSession();
            } else {
                this.performAutoLogout('user_choice', 0);
            }
        }
    }

    hideWarning() {
        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
            Swal.close();
        }
        this.warningShown = false;
    }

    extendSession() {
        this.updateActivity();
        this.hideWarning();

        // Make request to server to extend session
        fetch('/user/extend-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                extend_session: true,
                timestamp: Date.now()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (this.options.enableLogging) {
                    console.log('Session extended successfully');
                }
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Session Extended',
                        text: 'Your session has been extended successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        })
        .catch(error => {
            console.error('Failed to extend session:', error);
            this.performAutoLogout('extend_failed', 0);
        });
    }

    performAutoLogout(reason, inactiveMinutes) {
        this.isActive = false;
        
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
        }

        if (this.options.enableLogging) {
            console.log('Performing auto logout', { reason, inactiveMinutes });
        }

        // Show logout message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Expired',
                html: `
                    <div class="text-center">
                        <i class="fas fa-sign-out-alt fa-3x text-danger mb-3"></i>
                        <p>Your session has expired due to inactivity.</p>
                        ${inactiveMinutes > 0 ? `<p class="text-muted">Inactive for: ${Math.round(inactiveMinutes)} minutes</p>` : ''}
                        <p>You will be redirected to the login page.</p>
                    </div>
                `,
                icon: 'warning',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                this.redirectToLogin();
            });
        } else {
            alert('Your session has expired due to inactivity. You will be redirected to the login page.');
            this.redirectToLogin();
        }
    }

    redirectToLogin() {
        // Clear local storage
        try {
            localStorage.clear();
            sessionStorage.clear();
        } catch(e) {
            console.warn('Could not clear storage:', e);
        }

        // Redirect to login page
        window.location.href = '/login?session_expired=1&reason=auto_timeout&t=' + Date.now();
    }

    destroy() {
        this.isActive = false;
        
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
        }

        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
        }

        // Remove event listeners
        this.options.idleEvents.forEach(event => {
            document.removeEventListener(event, this.updateActivity, true);
        });
    }

    // Public methods
    setTimeoutMinutes(minutes) {
        this.options.timeoutMinutes = minutes;
    }

    getInactiveTime() {
        return (Date.now() - this.lastActivity) / (1000 * 60); // Return in minutes
    }

    isWarningShown() {
        return this.warningShown;
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is authenticated
    if (document.querySelector('meta[name="user-authenticated"]')?.content === 'true' ||
        document.body.classList.contains('authenticated')) {
        
        // Get timeout settings from meta tag or use defaults
        const timeoutMinutes = parseInt(document.querySelector('meta[name="session-timeout"]')?.content) || 30;
        
        window.autoSessionTimeout = new AutoSessionTimeout({
            timeoutMinutes: timeoutMinutes,
            warningMinutes: 5,
            checkInterval: 30000, // Check every 30 seconds
            enableWarnings: true,
            enableLogging: true
        });
        
        console.log(`Auto session timeout initialized with ${timeoutMinutes} minute timeout`);
    }
});

// Export for manual initialization if needed
window.AutoSessionTimeout = AutoSessionTimeout;
