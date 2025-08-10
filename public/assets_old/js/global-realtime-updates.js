/**
 * Global Real-time Updates System
 * Ensures all website positions show dynamic and instant data updates
 * Including navbar, sidebar, dashboard, and all balance displays
 */

class GlobalRealtimeUpdates {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.retryCount = 0;
        this.maxRetries = 3;
        this.isUpdating = false;
        this.cache = {
            lastUpdate: null,
            data: null
        };
        
        this.init();
    }

    init() {
        // Initialize real-time updates
        this.startGlobalUpdates();
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });

        // Handle network status changes
        window.addEventListener('online', () => {
            this.resumeUpdates();
        });

        window.addEventListener('offline', () => {
            this.pauseUpdates();
        });

        // Initialize on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.performGlobalUpdate();
            });
        } else {
            this.performGlobalUpdate();
        }
    }

    startGlobalUpdates() {
        this.stopGlobalUpdates(); // Prevent multiple intervals
        
        this.updateIntervalId = setInterval(() => {
            this.performGlobalUpdate();
        }, this.updateInterval);
    }

    stopGlobalUpdates() {
        if (this.updateIntervalId) {
            clearInterval(this.updateIntervalId);
            this.updateIntervalId = null;
        }
    }

    pauseUpdates() {
        this.stopGlobalUpdates();
    }

    resumeUpdates() {
        this.startGlobalUpdates();
        this.performGlobalUpdate(); // Immediate update on resume
    }

    async performGlobalUpdate() {
        if (this.isUpdating) {
            return;
        }

        this.isUpdating = true;
        
        try {
            // Show loading indicators
            this.showLoadingIndicators();
            
            // Fetch latest data
            const data = await this.fetchGlobalData();
            
            if (data && data.success) {
                // Update all website positions
                await this.updateAllPositions(data.data);
                
                // Update cache
                this.cache.lastUpdate = Date.now();
                this.cache.data = data.data;
                
                // Reset retry count on success
                this.retryCount = 0;
                
                // Hide loading indicators
                this.hideLoadingIndicators();
                
                // Trigger custom event for other scripts
                this.triggerUpdateEvent(data.data);
                
            } else {
                throw new Error(data?.message || 'Failed to fetch data');
            }
            
        } catch (error) {
            // Handle error silently in production
            this.handleUpdateError(error);
        } finally {
            this.isUpdating = false;
        }
    }

    async fetchGlobalData() {
        try {
            const response = await fetch('/user/api/dashboard/global-data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }

            return await response.json();
        } catch (error) {
            // Handle network errors silently in production
            throw error;
        }
    }

    async updateAllPositions(data) {
        const updates = [
            this.updateNavbarElements(data),
            this.updateSidebarElements(data),
            this.updateDashboardElements(data),
            this.updateNotifications(data),
            this.updateProfileDropdown(data)
        ];

        // Execute all updates in parallel
        await Promise.allSettled(updates);
    }

    updateNavbarElements(data) {
        try {
            // Update navbar notification badge
            const notificationBadge = document.getElementById('header-notification-badge');
            if (notificationBadge && data.notifications_count > 0) {
                notificationBadge.textContent = data.notifications_count > 99 ? '99+' : data.notifications_count;
                notificationBadge.classList.remove('d-none');
            } else if (notificationBadge) {
                notificationBadge.classList.add('d-none');
            }

            // Update any navbar balance displays
            const navbarBalances = document.querySelectorAll('.navbar-balance, .header-balance');
            navbarBalances.forEach(element => {
                if (element) {
                    element.textContent = `$${this.formatAmount(data.total_balance)}`;
                }
            });

        } catch (error) {
            // Handle error silently in production
        }
    }

    updateSidebarElements(data) {
        try {
            // Update sidebar total balance
            const sidebarTotalBalance = document.querySelector('[data-realtime-update="sidebar-total-balance"]');
            if (sidebarTotalBalance) {
                sidebarTotalBalance.textContent = `$${this.formatAmount(data.total_balance)}`;
            }

            // Update sidebar deposit wallet
            const sidebarDeposit = document.querySelector('[data-realtime-update="sidebar-deposit"]');
            if (sidebarDeposit) {
                sidebarDeposit.textContent = `$${this.formatAmount(data.deposit_wallet)}`;
            }

            // Update sidebar interest wallet
            const sidebarInterest = document.querySelector('[data-realtime-update="sidebar-interest"]');
            if (sidebarInterest) {
                sidebarInterest.textContent = `$${this.formatAmount(data.interest_wallet)}`;
            }

            // Update any other sidebar balance displays
            const sidebarBalances = document.querySelectorAll('.sidebar-balance');
            sidebarBalances.forEach(element => {
                if (element) {
                    element.textContent = `$${this.formatAmount(data.total_balance)}`;
                }
            });

            // Update sidebar stats
            const sidebarStats = document.querySelectorAll('.sidebar-stat-value');
            sidebarStats.forEach((element, index) => {
                if (element && data.quick_stats && data.quick_stats[index]) {
                    element.textContent = data.quick_stats[index].value;
                }
            });

        } catch (error) {
            // Handle error silently in production
        }
    }

    updateDashboardElements(data) {
        try {
            // Update main balance cards
            const mainBalanceElement = document.querySelector('.main-balance-amount, #main-balance');
            if (mainBalanceElement) {
                mainBalanceElement.textContent = `$${this.formatAmount(data.total_balance)}`;
            }

            // Update Video Access Vault (previously Total Investment)
            const videoAccessElement = document.querySelector('.video-access-amount, #video-access-amount');
            if (videoAccessElement) {
                videoAccessElement.textContent = `$${this.formatAmount(data.total_investment)}`;
            }

            // Update Total Earnings Hub (previously Interest Wallet)
            const earningsHubElement = document.querySelector('.earnings-hub-amount, #earnings-hub-amount');
            if (earningsHubElement) {
                earningsHubElement.textContent = `$${this.formatAmount(data.total_earnings)}`;
            }

            // Update deposit wallet
            const depositWalletElement = document.querySelector('.deposit-wallet-amount, #deposit-wallet');
            if (depositWalletElement) {
                depositWalletElement.textContent = `$${this.formatAmount(data.deposit_wallet)}`;
            }

            // Update interest wallet
            const interestWalletElement = document.querySelector('.interest-wallet-amount, #interest-wallet');
            if (interestWalletElement) {
                interestWalletElement.textContent = `$${this.formatAmount(data.interest_wallet)}`;
            }

            // Update referral earnings
            const referralElement = document.querySelector('.referral-earnings-amount, #referral-earnings');
            if (referralElement) {
                referralElement.textContent = `$${this.formatAmount(data.referral_earnings)}`;
            }

            // Update quick stats
            if (data.quick_stats && Array.isArray(data.quick_stats)) {
                data.quick_stats.forEach(stat => {
                    const statElement = document.querySelector(`[data-stat="${stat.key}"]`);
                    if (statElement) {
                        statElement.textContent = stat.value;
                    }
                });
            }

        } catch (error) {
            // Handle error silently in production
        }
    }

    updateNotifications(data) {
        try {
            // Update notification count displays
            const notificationCounts = document.querySelectorAll('.notification-count');
            notificationCounts.forEach(element => {
                if (element) {
                    element.textContent = data.notifications_count || '0';
                }
            });

            // Update notification list if present
            const notificationList = document.querySelector('.notification-list');
            if (notificationList && data.recent_notifications) {
                this.updateNotificationList(notificationList, data.recent_notifications);
            }

        } catch (error) {
            // Handle error silently in production
        }
    }

    updateProfileDropdown(data) {
        try {
            // Update profile dropdown balance with the specific selector
            const profileBalance = document.querySelector('[data-realtime-update="profile-balance"]');
            if (profileBalance) {
                profileBalance.innerHTML = `Balance: $${this.formatAmount(data.total_balance)}
                    <span class="realtime-loading d-none">
                        <i class="fe fe-loader spin"></i>
                    </span>`;
            }

            // Fallback for any other profile balance elements
            const profileBalanceElements = document.querySelectorAll('.user-balance-info .badge');
            profileBalanceElements.forEach(element => {
                if (element && !element.hasAttribute('data-realtime-update')) {
                    element.textContent = `Balance: $${this.formatAmount(data.total_balance)}`;
                }
            });

            // Update user stats in dropdown
            const userStats = document.querySelectorAll('.dropdown-user-stat');
            userStats.forEach(element => {
                const statType = element.getAttribute('data-stat-type');
                if (statType && data[statType]) {
                    element.textContent = this.formatAmount(data[statType]);
                }
            });

        } catch (error) {
            // Handle error silently in production
        }
    }

    updateNotificationList(container, notifications) {
        try {
            // Create notification items HTML
            const notificationHTML = notifications.map(notification => `
                <div class="notification-item ${notification.read_at ? '' : 'unread'}" data-id="${notification.id}">
                    <div class="notification-icon">
                        <i class="fe fe-${notification.icon || 'bell'} text-${notification.type || 'primary'}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-text">${notification.message}</div>
                        <div class="notification-time">${this.timeAgo(notification.created_at)}</div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = notificationHTML;
        } catch (error) {
            // Handle error silently in production
        }
    }

    showLoadingIndicators() {
        const loadingElements = document.querySelectorAll('.realtime-loading');
        loadingElements.forEach(element => {
            element.classList.remove('d-none');
        });

        // Add loading class to update targets
        const updateTargets = document.querySelectorAll('[data-realtime-update]');
        updateTargets.forEach(element => {
            element.classList.add('updating');
        });
    }

    hideLoadingIndicators() {
        const loadingElements = document.querySelectorAll('.realtime-loading');
        loadingElements.forEach(element => {
            element.classList.add('d-none');
        });

        // Remove loading class from update targets
        const updateTargets = document.querySelectorAll('[data-realtime-update]');
        updateTargets.forEach(element => {
            element.classList.remove('updating');
        });
    }

    handleUpdateError(error) {
        this.retryCount++;
        
        if (this.retryCount >= this.maxRetries) {
            this.stopGlobalUpdates();
            
            // Show detailed error notification
            let errorMessage = 'Real-time updates temporarily unavailable. ';
            
            if (error.message.includes('401')) {
                errorMessage += 'Please log in again.';
            } else if (error.message.includes('403')) {
                errorMessage += 'Access denied. Please refresh the page.';
            } else if (error.message.includes('404')) {
                errorMessage += 'Update service not found. Please contact support.';
            } else if (error.message.includes('500')) {
                errorMessage += 'Server error. Please try again later.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage += 'Check your internet connection.';
            } else {
                errorMessage += 'Please refresh the page.';
            }
            
            this.showErrorNotification(errorMessage);
            
            // Try to restart after a longer delay
            setTimeout(() => {
                this.retryCount = 0;
                this.resumeUpdates();
            }, 60000); // 1 minute
        } else {
            const retryDelay = 5000 * this.retryCount;
            
            // Show temporary warning for first few retries
            if (this.retryCount === 1) {
                this.showErrorNotification('Connection issue detected. Retrying...', 'warning', 5000);
            }
            
            setTimeout(() => {
                this.performGlobalUpdate();
            }, retryDelay);
        }
    }

    showErrorNotification(message, type = 'danger', duration = 10000) {
        // Create or update error notification
        let errorNotification = document.getElementById('global-update-error');
        
        if (!errorNotification) {
            errorNotification = document.createElement('div');
            errorNotification.id = 'global-update-error';
            errorNotification.className = `alert alert-${type} alert-dismissible position-fixed`;
            errorNotification.style.cssText = 'top: 70px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);';
            document.body.appendChild(errorNotification);
        } else {
            errorNotification.className = `alert alert-${type} alert-dismissible position-fixed`;
        }
        
        const iconMap = {
            'danger': 'wifi-off',
            'warning': 'alert-triangle',
            'info': 'info',
            'success': 'check-circle'
        };
        
        const icon = iconMap[type] || 'wifi-off';
        
        errorNotification.innerHTML = `
            <i class="fe fe-${icon} me-2"></i>
            <strong>${type === 'warning' ? 'Connection Issue:' : type === 'danger' ? 'Update Error:' : 'Notice:'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        errorNotification.classList.remove('d-none');
        
        // Add close button functionality if Bootstrap is not available
        const closeButton = errorNotification.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                errorNotification.classList.add('d-none');
            });
        }
        
        // Auto-hide after specified duration
        if (duration > 0) {
            setTimeout(() => {
                if (errorNotification) {
                    errorNotification.classList.add('d-none');
                }
            }, duration);
        }
    }

    triggerUpdateEvent(data) {
        // Trigger custom event that other scripts can listen to
        const event = new CustomEvent('globalDataUpdated', {
            detail: {
                data: data,
                timestamp: Date.now()
            }
        });
        
        document.dispatchEvent(event);
    }

    formatAmount(amount) {
        if (!amount && amount !== 0) return '0.00';
        
        const num = parseFloat(amount);
        if (isNaN(num)) return '0.00';
        
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }

    // Public methods for external control
    forceUpdate() {
        this.performGlobalUpdate();
    }

    setUpdateInterval(intervalMs) {
        this.updateInterval = intervalMs;
        this.startGlobalUpdates();
    }

    getLastUpdateTime() {
        return this.cache.lastUpdate;
    }

    getCachedData() {
        return this.cache.data;
    }

    // Test connection to the API
    async testConnection() {
        try {
            const response = await fetch('/user/api/dashboard/quick-test', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return false;
                }
                
                const result = await response.json();
                if (result.success) {
                    this.showErrorNotification('Connection restored! Updates resuming...', 'success', 3000);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (error) {
            return false;
            return false;
        }
    }

    // Recovery mechanism
    async attemptRecovery() {
        // Test basic connectivity
        const isConnected = await this.testConnection();
        
        if (isConnected) {
            // Reset error state
            this.retryCount = 0;
            
            // Try to resume updates
            this.resumeUpdates();
            
            return true;
        } else {
            return false;
        }
    }
}

// Initialize global real-time updates
window.globalRealtimeUpdates = new GlobalRealtimeUpdates();

// Add CSS for loading states
const style = document.createElement('style');
style.textContent = `
    .updating {
        opacity: 0.7;
        position: relative;
    }
    
    .updating::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 10px;
        width: 12px;
        height: 12px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: realtime-spin 1s linear infinite;
        transform: translateY(-50%);
    }
    
    @keyframes realtime-spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }
    
    .realtime-loading {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .notification-item {
        padding: 10px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        transition: background-color 0.2s;
    }
    
    .notification-item:hover {
        background-color: rgba(0,0,0,0.05);
    }
    
    .notification-item.unread {
        background-color: rgba(0,123,255,0.1);
        border-left: 3px solid #007bff;
    }
`;
document.head.appendChild(style);
