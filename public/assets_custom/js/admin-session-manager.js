/**
 * Admin Session Management JavaScript
 * Handles session expiration, CSRF token refresh, and graceful logout
 */

class AdminSessionManager {
    constructor() {
        this.sessionTimeout = 120 * 60 * 1000; // 120 minutes in milliseconds
        this.warningTime = 10 * 60 * 1000; // Show warning 10 minutes before expiration
        this.lastActivity = Date.now();
        this.warningShown = false;
        this.sessionTimer = null;
        this.warningTimer = null;
        
        this.init();
    }
    
    init() {
        // Track user activity
        this.trackActivity();
        
        // Set up session timers
        this.startSessionTimer();
        
        // Handle AJAX CSRF errors
        this.setupAjaxErrorHandling();
        
        // Refresh CSRF token periodically
        this.startCSRFRefresh();
        
        console.log('Admin Session Manager initialized');
    }
    
    trackActivity() {
        const activities = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        activities.forEach(activity => {
            document.addEventListener(activity, () => {
                this.updateActivity();
            }, true);
        });
    }
    
    updateActivity() {
        this.lastActivity = Date.now();
        this.warningShown = false;
        
        // Reset timers
        this.clearTimers();
        this.startSessionTimer();
    }
    
    startSessionTimer() {
        // Set warning timer
        this.warningTimer = setTimeout(() => {
            this.showSessionWarning();
        }, this.sessionTimeout - this.warningTime);
        
        // Set session expiration timer
        this.sessionTimer = setTimeout(() => {
            this.handleSessionExpiration();
        }, this.sessionTimeout);
    }
    
    clearTimers() {
        if (this.sessionTimer) {
            clearTimeout(this.sessionTimer);
            this.sessionTimer = null;
        }
        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
            this.warningTimer = null;
        }
    }
    
    showSessionWarning() {
        if (this.warningShown) return;
        
        this.warningShown = true;
        
        // Create warning modal
        const warningModal = `
            <div class="modal fade" id="sessionWarningModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">
                                <i class="fe fe-clock me-2"></i>Session Expiring Soon
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fe fe-alert-triangle fs-1 text-warning"></i>
                            </div>
                            <h6>Your session will expire in <span id="countdown">10:00</span></h6>
                            <p class="text-muted">You will be automatically logged out for security reasons.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-success" onclick="adminSession.extendSession()">
                                <i class="fe fe-refresh-cw me-1"></i>Stay Logged In
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="adminSession.logoutNow()">
                                <i class="fe fe-log-out me-1"></i>Logout Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', warningModal);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('sessionWarningModal'));
        modal.show();
        
        // Start countdown
        this.startCountdown();
    }
    
    startCountdown() {
        let timeLeft = this.warningTime / 1000; // Convert to seconds
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                this.handleSessionExpiration();
            }
        }, 1000);
    }
    
    extendSession() {
        // Make a simple request to extend session
        fetch('/admin/extend-session', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close warning modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('sessionWarningModal'));
                if (modal) {
                    modal.hide();
                    document.getElementById('sessionWarningModal').remove();
                }
                
                // Reset session timer
                this.updateActivity();
                
                // Show success message
                this.showNotification('Session extended successfully', 'success');
            } else {
                this.handleSessionExpiration();
            }
        })
        .catch(error => {
            console.error('Session extension failed:', error);
            this.handleSessionExpiration();
        });
    }
    
    logoutNow() {
        window.location.href = '/admin/logout';
    }
    
    handleSessionExpiration() {
        // Clear any existing timers
        this.clearTimers();
        
        // Close any existing modals
        const existingModal = document.getElementById('sessionWarningModal');
        if (existingModal) {
            const modal = bootstrap.Modal.getInstance(existingModal);
            if (modal) {
                modal.hide();
            }
            existingModal.remove();
        }
        
        // Show expiration message
        this.showNotification('Your session has expired. Redirecting to admin login...', 'error');
        
        // Clear all session data
        sessionStorage.clear();
        localStorage.clear();
        
        // Attempt emergency logout first
        fetch('/admin/emergency-logout', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .catch(error => {
            console.log('Emergency logout failed, proceeding with redirect:', error);
        })
        .finally(() => {
            // Always redirect to admin login page after timeout
            setTimeout(() => {
                window.location.href = '/admin?session_expired=1&reason=timeout';
            }, 1500);
        });
    }
    
    setupAjaxErrorHandling() {
        // Handle jQuery AJAX errors
        if (typeof $ !== 'undefined') {
            $(document).ajaxError((event, xhr, settings) => {
                if (xhr.status === 419) {
                    this.handleCSRFError(xhr);
                } else if (xhr.status === 401) {
                    this.handleSessionExpiration();
                }
            });
        }
        
        // Handle fetch errors globally
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args)
                .then(response => {
                    if (response.status === 419) {
                        adminSession.handleCSRFError(response);
                    } else if (response.status === 401) {
                        adminSession.handleSessionExpiration();
                    }
                    return response;
                });
        };
    }
    
    handleCSRFError(response) {
        console.warn('CSRF token mismatch detected, refreshing token...');
        
        // Try to refresh CSRF token
        this.refreshCSRFToken()
            .then(() => {
                this.showNotification('Security token refreshed. Please try again.', 'warning');
            })
            .catch(() => {
                this.showNotification('Security error. Please refresh the page.', 'error');
            });
    }
    
    startCSRFRefresh() {
        // Refresh CSRF token every 30 minutes
        setInterval(() => {
            this.refreshCSRFToken();
        }, 30 * 60 * 1000);
    }
    
    refreshCSRFToken() {
        return fetch('/admin/csrf-token', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                // Update all CSRF tokens on the page
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                
                // Update all forms with CSRF tokens
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.token;
                });
                
                // Update jQuery AJAX setup if available
                if (typeof $ !== 'undefined' && $.ajaxSetup) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': data.token
                        }
                    });
                }
                
                console.log('CSRF token refreshed');
                return data.token;
            }
            throw new Error('Invalid token response');
        });
    }
    
    startSessionChecking() {
        // Check session status every 5 minutes
        setInterval(() => {
            this.checkSessionStatus();
        }, 5 * 60 * 1000);
        
        // Initial check after 1 minute
        setTimeout(() => {
            this.checkSessionStatus();
        }, 60 * 1000);
    }
    
    checkSessionStatus() {
        fetch('/admin/session-status', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.status === 401 || response.status === 419) {
                // Session expired or invalid
                console.log('Session check: Session expired or invalid');
                this.handleSessionExpiration();
            } else if (!response.ok) {
                throw new Error(`Session check failed: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && !data.authenticated) {
                console.log('Session check: User not authenticated');
                this.handleSessionExpiration();
            } else {
                console.log('Session check: Session valid');
            }
        })
        .catch(error => {
            console.error('Session status check failed:', error);
            // Don't auto-logout on network errors, just log them
        });
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.adminSession = new AdminSessionManager();
    });
} else {
    window.adminSession = new AdminSessionManager();
}
