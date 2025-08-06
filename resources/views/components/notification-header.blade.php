<!-- Enhanced Notification Header Component -->
@php
    $notificationUnreadCount = 0;
    try {
        if(Auth::check()) {
            $notificationUnreadCount = \App\Models\UserNotification::where('user_id', Auth::id())
                ->unread()
                ->notExpired()
                ->count();
        }
    } catch (\Exception $e) {
        $notificationUnreadCount = 0;
    }
@endphp

<div class="header-element notifications-dropdown position-relative">
    <!-- Notification Bell Icon -->
    <a href="javascript:void(0);" 
       class="header-link position-relative notification-trigger" 
       data-bs-toggle="offcanvas" 
       data-bs-target="#notification-sidebar-canvas" 
       id="messageDropdown"
       title="Notifications">
        
        <!-- Bell Icon with Enhanced Styling -->
        <i class="fe fe-bell fs-18 text-muted notification-bell"></i>
        
        <!-- Notification Badge -->
        @if($notificationUnreadCount > 0)
            <span class="position-absolute translate-middle badge rounded-pill bg-danger" 
                  id="header-notification-badge"
                  style="top: 8px; right: 8px; min-width: 20px; height: 20px; font-size: 11px; line-height: 20px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                {{ $notificationUnreadCount > 99 ? '99+' : $notificationUnreadCount }}
            </span>
        @else
            <span class="position-absolute translate-middle badge rounded-pill bg-danger d-none" 
                  id="header-notification-badge"
                  style="top: 8px; right: 8px; min-width: 20px; height: 20px; font-size: 11px; line-height: 20px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                0
            </span>
        @endif
        
        <!-- Pulse Animation for New Notifications -->
        @if($notificationUnreadCount > 0)
            <span class="position-absolute translate-middle p-1 bg-danger border border-light rounded-circle notification-pulse" 
                  style="top: 8px; right: 8px;">
                <span class="visually-hidden">New alerts</span>
            </span>
        @endif
    </a>
</div>

<!-- Enhanced CSS for Notification Icon -->
<style>
/* Notification Bell Styling */
.notification-trigger {
    transition: all 0.3s ease;
    padding: 8px;
    border-radius: 8px;
}

.notification-trigger:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    transform: translateY(-1px);
}

.notification-trigger:hover .notification-bell {
    color: var(--bs-primary) !important;
    transform: scale(1.1);
}

.notification-bell {
    transition: all 0.3s ease;
    font-size: 20px !important;
}

/* Notification Badge Enhanced Styling */
#header-notification-badge {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    color: white !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4) !important;
    animation: badgePulse 2s infinite;
}

/* Pulse Animation */
@keyframes badgePulse {
    0% { box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4); }
    50% { box-shadow: 0 2px 12px rgba(220, 53, 69, 0.6); }
    100% { box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4); }
}

.notification-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .notification-trigger {
        padding: 6px;
    }
    
    .notification-bell {
        font-size: 18px !important;
    }
    
    #header-notification-badge {
        min-width: 18px !important;
        height: 18px !important;
        font-size: 10px !important;
        line-height: 18px !important;
        top: 6px !important;
        right: 6px !important;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .notification-trigger:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
}

/* Loading state for notification icon */
.notification-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Enhanced header spacing for equal distribution */
.header-element.notifications-dropdown {
    margin-left: 12px;
    margin-right: 12px;
}

/* Ensure consistent header element alignment */
.header-content-right .header-element {
    display: flex;
    align-items: center;
    height: 60px;
}

/* Better visual hierarchy */
.header-element.notifications-dropdown .header-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    text-decoration: none;
}

/* Active state when offcanvas is open */
.header-element.notifications-dropdown .header-link[aria-expanded="true"] {
    background-color: rgba(var(--bs-primary-rgb), 0.15);
}

.header-element.notifications-dropdown .header-link[aria-expanded="true"] .notification-bell {
    color: var(--bs-primary) !important;
}

/* Focus states for accessibility */
.notification-trigger:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.2);
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    #header-notification-badge {
        border: 2px solid currentColor !important;
    }
}
</style>

<!-- Enhanced JavaScript for Real-time Updates -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeNotificationUpdates();
});

function initializeNotificationUpdates() {
    // Auto refresh notification count every 30 seconds
    setInterval(refreshNotificationCount, 30000);
    
    // Add visibility change listener to refresh when tab becomes active
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshNotificationCount();
        }
    });
    
    // Add online/offline listeners
    window.addEventListener('online', refreshNotificationCount);
}

function refreshNotificationCount() {
    fetch('{{ route("user.notifications.count") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON - likely redirected to login or error page');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateHeaderNotificationBadge(data.count);
        } else {
            throw new Error(data.message || 'Failed to refresh notification count');
        }
    })
    .catch(error => {
        // Silently handle errors in production to avoid console spam
        // Only log if it's a development environment
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.warn('Header notification count refresh failed:', error.message);
        }
        
        // If it's an authentication error, user might need to refresh
        if (error.message.includes('redirected to login')) {
            // Optionally could show a subtle notification or handle gracefully
        }
    });
}

function updateHeaderNotificationBadge(count) {
    const badge = document.getElementById('header-notification-badge');
    const pulse = document.querySelector('.notification-pulse');
    
    if (!badge) return;
    
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('d-none');
        
        // Add pulse animation for new notifications
        if (pulse) {
            pulse.style.display = 'block';
        }
        
        // Animate the bell icon
        const bell = document.querySelector('.notification-bell');
        if (bell) {
            bell.style.animation = 'bellShake 0.5s ease-in-out';
            setTimeout(() => {
                bell.style.animation = '';
            }, 500);
        }
    } else {
        badge.classList.add('d-none');
        if (pulse) {
            pulse.style.display = 'none';
        }
    }
}

// Bell shake animation
const style = document.createElement('style');
style.textContent = `
    @keyframes bellShake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-5deg); }
        75% { transform: rotate(5deg); }
    }
`;
document.head.appendChild(style);

// Global function to update notification count from other scripts
window.updateNotificationCount = updateHeaderNotificationBadge;
</script>
