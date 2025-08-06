<!-- Notification Dropdown -->
<div class="dropdown notification-dropdown">
    <button class="btn btn-link nav-link" type="button" id="messageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fs-18"></i>
        <span class="badge bg-danger notification-badge" id="notificationCount" style="display: none;">0</span>
    </button>
    
    <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu" aria-labelledby="messageDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Notifications</h6>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary" id="markAllAsRead" title="Mark All Read">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" id="clearAllNotifications" title="Clear All">
                    <i class="fas fa-trash"></i>
                </button>
                <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-outline-info" title="View All">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
        
        <div class="notification-loading text-center py-3" id="notificationLoading">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mb-0 mt-2">Loading notifications...</p>
        </div>
        
        <div class="notification-list" id="notificationList" style="max-height: 400px; overflow-y: auto;">
            <!-- Notifications will be loaded here via AJAX -->
        </div>
        
        <div class="dropdown-footer">
            <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-primary w-100">
                <i class="fas fa-eye me-1"></i> View All Notifications
            </a>
        </div>
    </div>
</div>

<style>
.notification-dropdown .dropdown-menu {
    width: 350px;
    max-width: 90vw;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 12px;
}

.notification-dropdown .dropdown-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 12px 12px 0 0;
    margin: 0;
}

.notification-dropdown .dropdown-header h6 {
    color: white;
    font-weight: 600;
}

.notification-dropdown .dropdown-header .btn-outline-primary {
    border-color: rgba(255,255,255,0.3);
    color: white;
    background: rgba(255,255,255,0.1);
    font-size: 12px;
    padding: 4px 8px;
}

.notification-dropdown .dropdown-header .btn-outline-primary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9ff;
}

.notification-item.unread {
    background-color: #fff7e6;
    border-left: 4px solid #ff9500;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: #ff9500;
    border-radius: 50%;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    color: white;
}

.notification-icon.welcome { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.notification-icon.deposit { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.notification-icon.withdrawal { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.notification-icon.commission { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.notification-icon.lottery { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
.notification-icon.referral { background: linear-gradient(135deg, #c471ed 0%, #f64f59 100%); }
.notification-icon.default { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 4px;
    line-height: 1.3;
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-time {
    font-size: 11px;
    color: #999;
    font-weight: 500;
}

.notification-actions {
    display: flex;
    gap: 5px;
    margin-top: 8px;
}

.notification-actions .btn {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
}

.dropdown-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
    margin: 0;
}

.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    font-size: 10px;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.empty-notifications {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-notifications i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

@media (max-width: 576px) {
    .notification-dropdown .dropdown-menu {
        width: 300px;
        margin-right: 10px;
    }
    
    .notification-item {
        padding: 12px 15px;
    }
    
    .notification-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.getElementById('messageDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationCount = document.getElementById('notificationCount');
    const notificationLoading = document.getElementById('notificationLoading');
    const markAllAsReadBtn = document.getElementById('markAllAsRead');
    
    let isLoading = false;
    
    // Load notifications when dropdown is opened
    notificationDropdown.addEventListener('click', function() {
        if (!isLoading) {
            loadNotifications();
        }
    });
    
    // Mark all as read
    markAllAsReadBtn.addEventListener('click', function() {
        markAllNotificationsAsRead();
    });
    
    // Clear all notifications
    const clearAllBtn = document.getElementById('clearAllNotifications');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            clearAllNotifications();
        });
    }
    
    // Load notifications function
    function loadNotifications() {
        isLoading = true;
        notificationLoading.style.display = 'block';
        notificationList.innerHTML = '';
        
        fetch('{{ route("user.notifications.dropdown") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            isLoading = false;
            notificationLoading.style.display = 'none';
            
            if (data.success) {
                updateNotificationCount(data.unread_count);
                renderNotifications(data.notifications);
            } else {
                showError('Failed to load notifications');
            }
        })
        .catch(error => {
            isLoading = false;
            notificationLoading.style.display = 'none';
            console.error('Error loading notifications:', error);
            showError('Failed to load notifications');
        });
    }
    
    // Render notifications
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="empty-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p class="mb-0">No notifications yet</p>
                </div>
            `;
            return;
        }
        
        const notificationsHtml = notifications.map(notification => {
            const iconClass = getNotificationIconClass(notification.type || 'default');
            const unreadClass = notification.read ? '' : 'unread';
            
            return `
                <div class="notification-item ${unreadClass}" data-id="${notification.id}" onclick="handleNotificationClick(${notification.id}, '${notification.action_url || ''}')">
                    <div class="d-flex">
                        <div class="notification-icon ${notification.type || 'default'}">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${notification.title}</div>
                            <div class="notification-message">${notification.message}</div>
                            <div class="notification-time">
                                <i class="fas fa-clock me-1"></i>${notification.time_ago}
                            </div>
                            ${!notification.read ? `
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); markAsRead(${notification.id})">
                                        <i class="fas fa-check"></i> Mark Read
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        notificationList.innerHTML = notificationsHtml;
    }
    
    // Update notification count
    function updateNotificationCount(count) {
        if (count > 0) {
            notificationCount.textContent = count > 99 ? '99+' : count;
            notificationCount.style.display = 'flex';
        } else {
            notificationCount.style.display = 'none';
        }
    }
    
    // Get icon class based on notification type
    function getNotificationIconClass(type) {
        const iconMap = {
            'welcome': 'welcome',
            'deposit': 'deposit', 
            'withdrawal': 'withdrawal',
            'commission': 'commission',
            'lottery': 'lottery',
            'referral': 'referral',
            'default': 'default'
        };
        return iconMap[type] || 'default';
    }
    
    // Handle notification click
    window.handleNotificationClick = function(id, actionUrl) {
        // Mark as read first
        markAsRead(id, false);
        
        // Redirect if action URL exists
        if (actionUrl && actionUrl !== '') {
            window.location.href = `{{ route('user.notifications.redirect', '') }}/${id}`;
        }
    };
    
    // Mark single notification as read
    window.markAsRead = function(id, updateUI = true) {
        fetch(`{{ route('user.notifications.read', '') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && updateUI) {
                // Remove unread styling
                const notificationItem = document.querySelector(`[data-id="${id}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('unread');
                    const actions = notificationItem.querySelector('.notification-actions');
                    if (actions) {
                        actions.remove();
                    }
                }
                // Update count
                updateNotificationCount();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    };
    
    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        fetch('{{ route("user.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove all unread styling
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const actions = item.querySelector('.notification-actions');
                    if (actions) {
                        actions.remove();
                    }
                });
                updateNotificationCount(0);
                showSuccess('All notifications marked as read');
            } else {
                showError('Failed to mark all notifications as read');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            showError('Failed to mark all notifications as read');
        });
    }
    
    // Clear all notifications
    function clearAllNotifications() {
        if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
            return;
        }
        
        fetch('{{ route("user.notifications.clear-all") }}', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the notification list
                notificationList.innerHTML = '';
                updateNotificationCount(0);
                
                // Show empty state
                notificationList.innerHTML = `
                    <div class="empty-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p class="mb-0">No notifications</p>
                    </div>
                `;
                showSuccess('All notifications cleared successfully');
            } else {
                showError('Failed to clear notifications');
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
            showError('Failed to clear notifications');
        });
    }
    
    // Show success message
    function showSuccess(message) {
        // You can replace this with your toast notification system
        console.log('Success:', message);
    }
    
    // Show error message
    function showError(message) {
        // You can replace this with your toast notification system
        console.error('Error:', message);
    }
    
    // Load notification count on page load
    loadNotificationCount();
    
    function loadNotificationCount() {
        fetch('{{ route("user.notifications.count") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading notification count:', error);
        });
    }
    
    // Auto-refresh notification count every 30 seconds
    setInterval(loadNotificationCount, 30000);
});
</script>
