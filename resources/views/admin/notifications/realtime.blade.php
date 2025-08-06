<x-layout>

@section('title', 'Real-time Notifications')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Real-time Notifications</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Real-time</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <div class="btn-group" role="group">
                <button class="btn btn-primary btn-wave" id="toggle-realtime" onclick="toggleRealtime()">
                    <i class="fe fe-play me-2"></i>Start Real-time
                </button>
                <button class="btn btn-outline-info btn-wave" onclick="clearRealTimeLog()">
                    <i class="fe fe-trash me-2"></i>Clear Log
                </button>
                <button class="btn btn-outline-secondary btn-wave" onclick="exportLog()">
                    <i class="fe fe-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="row">
        <div class="col-xl-12">
            <div class="alert alert-info d-flex align-items-center" role="alert" id="connection-status">
                <div class="spinner-border spinner-border-sm me-3" role="status" id="connection-spinner" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>
                    <strong>Status:</strong> <span id="status-text">Disconnected</span>
                    <span class="ms-3"><strong>Last Update:</strong> <span id="last-update">Never</span></span>
                    <span class="ms-3"><strong>Notifications Received:</strong> <span id="notification-count">0</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Stats -->
    <div class="row">
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Active Connections</h6>
                                <h3 class="text-primary mb-0" id="active-connections">0</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-primary-transparent text-primary">
                                <i class="fe fe-wifi fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Today's Notifications</h6>
                                <h3 class="text-success mb-0" id="today-notifications">0</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-success-transparent text-success">
                                <i class="fe fe-bell fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Urgent Alerts</h6>
                                <h3 class="text-danger mb-0" id="urgent-alerts">0</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-danger-transparent text-danger">
                                <i class="fe fe-alert-triangle fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">System Health</h6>
                                <h3 class="text-info mb-0" id="system-health">Good</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-info-transparent text-info">
                                <i class="fe fe-activity fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Notifications Feed -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Live Notification Feed</div>
                    <div class="d-flex gap-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="auto-scroll" checked>
                            <label class="form-check-label" for="auto-scroll">Auto Scroll</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="sound-alerts" checked>
                            <label class="form-check-label" for="sound-alerts">Sound</label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="notification-feed" id="notification-feed" style="height: 500px; overflow-y: auto;">
                        <div class="text-center text-muted p-4" id="feed-empty-state">
                            <i class="fe fe-radio fs-1 mb-3"></i>
                            <h5>Waiting for notifications...</h5>
                            <p>Real-time notifications will appear here when they arrive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Controls -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Filters & Controls</div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Filter by Type</label>
                        <div class="filter-checkboxes">
                            <div class="form-check">
                                <input class="form-check-input filter-type" type="checkbox" id="filter-user-registration" value="user_registration" checked>
                                <label class="form-check-label" for="filter-user-registration">
                                    <i class="fe fe-user-plus text-success me-2"></i>User Registration
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input filter-type" type="checkbox" id="filter-deposit" value="deposit" checked>
                                <label class="form-check-label" for="filter-deposit">
                                    <i class="fe fe-dollar-sign text-primary me-2"></i>Deposits
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input filter-type" type="checkbox" id="filter-withdrawal" value="withdrawal" checked>
                                <label class="form-check-label" for="filter-withdrawal">
                                    <i class="fe fe-arrow-up text-warning me-2"></i>Withdrawals
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input filter-type" type="checkbox" id="filter-support" value="support" checked>
                                <label class="form-check-label" for="filter-support">
                                    <i class="fe fe-message-circle text-info me-2"></i>Support Tickets
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input filter-type" type="checkbox" id="filter-system" value="system" checked>
                                <label class="form-check-label" for="filter-system">
                                    <i class="fe fe-alert-triangle text-danger me-2"></i>System Alerts
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority Filter</label>
                        <select class="form-select" id="priority-filter">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent Only</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update Interval</label>
                        <select class="form-select" id="update-interval">
                            <option value="1000">1 second</option>
                            <option value="3000" selected>3 seconds</option>
                            <option value="5000">5 seconds</option>
                            <option value="10000">10 seconds</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Max Display Items</label>
                        <input type="number" class="form-control" id="max-items" value="50" min="10" max="200" step="10">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6 class="mb-2">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm quick-action-btn" onclick="testNotification()">
                                <i class="fe fe-send me-1"></i>Send Test Notification
                            </button>
                            <button class="btn btn-outline-success btn-sm quick-action-btn" onclick="pauseResume()">
                                <i class="fe fe-pause me-1"></i>Pause/Resume
                            </button>
                            <button class="btn btn-outline-info btn-sm quick-action-btn" onclick="showStats()">
                                <i class="fe fe-bar-chart me-1"></i>Show Statistics
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Summary -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Recent Activity</div>
                </div>
                <div class="card-body">
                    <div class="activity-summary" id="activity-summary">
                        <div class="text-center text-muted">
                            <i class="fe fe-clock mb-2"></i>
                            <p class="mb-0">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pop-up Notification Template -->
<div id="popup-notification-template" style="display: none;">
    <div class="popup-notification">
        <div class="popup-header">
            <div class="popup-icon">
                <i class="fe fe-bell"></i>
            </div>
            <div class="popup-title"></div>
            <button class="popup-close" onclick="closePopup(this)">
                <i class="fe fe-x"></i>
            </button>
        </div>
        <div class="popup-body">
            <div class="popup-message"></div>
            <div class="popup-meta"></div>
        </div>
        <div class="popup-actions">
            <button class="btn btn-sm btn-primary popup-action-btn" style="display: none;">View Details</button>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .notification-feed {
        background: #f8f9fa;
    }

    .notification-item {
        border-bottom: 1px solid #e9ecef;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: white;
        margin-bottom: 1px;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.new {
        border-left: 4px solid #007bff;
        animation: fadeIn 0.5s ease-in;
    }

    .notification-item.urgent {
        border-left: 4px solid #dc3545;
        background: #fff5f5;
    }

    .notification-item.high {
        border-left: 4px solid #ffc107;
        background: #fffbf0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .notification-timestamp {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .notification-type-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .filter-checkboxes .form-check {
        margin-bottom: 8px;
    }

    .activity-summary .activity-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .activity-summary .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 14px;
    }

    /* Pop-up Notification Styles */
    .popup-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        border-left: 4px solid #007bff;
        min-width: 350px;
        max-width: 400px;
        animation: slideInRight 0.4s ease-out;
        margin-bottom: 10px;
    }

    .popup-notification.urgent {
        border-left-color: #dc3545;
        box-shadow: 0 8px 25px rgba(220,53,69,0.3);
    }

    .popup-notification.high {
        border-left-color: #ffc107;
        box-shadow: 0 8px 25px rgba(255,193,7,0.3);
    }

    .popup-header {
        display: flex;
        align-items: center;
        padding: 12px 16px 8px 16px;
        border-bottom: 1px solid #f0f0f0;
    }

    .popup-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 12px;
    }

    .popup-title {
        flex: 1;
        font-weight: 600;
        font-size: 14px;
    }

    .popup-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .popup-close:hover {
        background-color: rgba(0,0,0,0.1);
    }

    .popup-body {
        padding: 12px 16px;
    }

    .popup-message {
        font-size: 13px;
        line-height: 1.4;
        color: #495057;
        margin-bottom: 8px;
    }

    .popup-meta {
        font-size: 11px;
        color: #6c757d;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .popup-actions {
        padding: 8px 16px 12px 16px;
        display: flex;
        justify-content: flex-end;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .notification-item.pulse {
        animation: pulse 0.6s ease-in-out;
    }

    /* Quick Action Button Styles */
    .quick-action-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .quick-action-btn:active {
        transform: translateY(0);
    }

    .quick-action-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .quick-action-btn i {
        transition: transform 0.3s ease;
    }

    .quick-action-btn:hover i {
        transform: scale(1.1);
    }
</style>
@endpush

@push('script')
<script>
    let realtimeActive = false;
    let realtimeInterval = null;
    let notificationCount = 0;
    let lastNotificationId = 0;
    let isPaused = false;

    // Initialize real-time notifications
    function toggleRealtime() {
        if (realtimeActive) {
            stopRealtime();
        } else {
            startRealtime();
        }
    }

    function startRealtime() {
        realtimeActive = true;
        document.getElementById('toggle-realtime').innerHTML = '<i class="fe fe-pause me-2"></i>Stop Real-time';
        document.getElementById('toggle-realtime').className = 'btn btn-danger btn-wave';
        
        updateConnectionStatus('Connected', 'success');
        document.getElementById('connection-spinner').style.display = 'inline-block';
        
        // Start polling for new notifications
        const interval = parseInt(document.getElementById('update-interval').value) || 3000;
        realtimeInterval = setInterval(fetchNewNotifications, interval);
        
        // Initial fetch
        fetchNewNotifications();
    }

    function stopRealtime() {
        realtimeActive = false;
        document.getElementById('toggle-realtime').innerHTML = '<i class="fe fe-play me-2"></i>Start Real-time';
        document.getElementById('toggle-realtime').className = 'btn btn-primary btn-wave';
        
        updateConnectionStatus('Disconnected', 'secondary');
        document.getElementById('connection-spinner').style.display = 'none';
        
        if (realtimeInterval) {
            clearInterval(realtimeInterval);
            realtimeInterval = null;
        }
    }

    function fetchNewNotifications() {
        if (!realtimeActive || isPaused) return;

        const selectedTypes = Array.from(document.querySelectorAll('.filter-type:checked')).map(cb => cb.value);
        const priorityFilter = document.getElementById('priority-filter').value;

        // Build URL with parameters for GET request
        const params = new URLSearchParams({
            last_id: lastNotificationId,
            priority: priorityFilter
        });
        
        // Add types array to params
        selectedTypes.forEach(type => {
            params.append('types[]', type);
        });

        fetch('{{ route("admin.notifications.realtime.get") }}?' + params.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    addNotificationToFeed(notification);
                    
                    // Show pop-up for urgent notifications
                    if (notification.priority === 'urgent' || notification.priority === 'high') {
                        showPopupNotification(notification);
                    }
                    
                    // Play sound if enabled
                    if (document.getElementById('sound-alerts').checked) {
                        playNotificationSound(notification.priority);
                    }
                    
                    lastNotificationId = Math.max(lastNotificationId, notification.id);
                });
                
                updateLastUpdate();
                updateStats();
                updateActivitySummary();
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            updateConnectionStatus('Connection Error', 'danger');
        });
    }

    function addNotificationToFeed(notification) {
        const feed = document.getElementById('notification-feed');
        const emptyState = document.getElementById('feed-empty-state');
        
        if (emptyState) {
            emptyState.remove();
        }

        const notificationElement = document.createElement('div');
        notificationElement.className = `notification-item new ${notification.priority}`;
        notificationElement.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <span class="avatar avatar-sm bg-${getTypeColor(notification.type)}-transparent text-${getTypeColor(notification.type)}">
                        <i class="${getTypeIcon(notification.type)} fs-14"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="mb-0 fw-semibold" style="font-size: 13px;">${notification.title}</h6>
                        <div class="d-flex align-items-center gap-1">
                            ${notification.priority === 'urgent' ? '<span class="badge bg-danger notification-type-badge">Urgent</span>' : ''}
                            ${notification.priority === 'high' ? '<span class="badge bg-warning notification-type-badge">High</span>' : ''}
                            <span class="notification-timestamp">${notification.time_ago}</span>
                        </div>
                    </div>
                    <p class="text-muted mb-1" style="font-size: 12px; line-height: 1.3;">${notification.message}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark notification-type-badge">${notification.type.replace('_', ' ').toUpperCase()}</span>
                        ${notification.action_url ? `<a href="${notification.action_url}" class="btn btn-outline-primary btn-xs">View</a>` : ''}
                    </div>
                </div>
            </div>
        `;

        feed.insertBefore(notificationElement, feed.firstChild);
        
        // Remove class after animation
        setTimeout(() => {
            notificationElement.classList.remove('new');
        }, 500);

        // Limit number of items displayed
        const maxItems = parseInt(document.getElementById('max-items').value) || 50;
        const items = feed.querySelectorAll('.notification-item');
        if (items.length > maxItems) {
            for (let i = maxItems; i < items.length; i++) {
                items[i].remove();
            }
        }

        // Auto-scroll if enabled
        if (document.getElementById('auto-scroll').checked) {
            feed.scrollTop = 0;
        }

        notificationCount++;
    }

    function showPopupNotification(notification) {
        const template = document.getElementById('popup-notification-template');
        const popup = template.querySelector('.popup-notification').cloneNode(true);
        
        popup.classList.add(notification.priority);
        popup.querySelector('.popup-icon i').className = getTypeIcon(notification.type);
        popup.querySelector('.popup-icon').style.background = `var(--bs-${getTypeColor(notification.type)})`;
        popup.querySelector('.popup-title').textContent = notification.title;
        popup.querySelector('.popup-message').textContent = notification.message;
        popup.querySelector('.popup-meta').innerHTML = `
            <span class="badge bg-${getTypeColor(notification.type)}">${notification.type.replace('_', ' ')}</span>
            <span>${notification.time_ago}</span>
        `;

        if (notification.action_url) {
            const actionBtn = popup.querySelector('.popup-action-btn');
            actionBtn.style.display = 'inline-block';
            actionBtn.onclick = () => window.open(notification.action_url, '_blank');
        }

        document.body.appendChild(popup);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (popup.parentNode) {
                popup.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => popup.remove(), 300);
            }
        }, 5000);
    }

    function closePopup(button) {
        const popup = button.closest('.popup-notification');
        popup.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => popup.remove(), 300);
    }

    function playNotificationSound(priority) {
        const soundFile = priority === 'urgent' ? 'urgent-alert.mp3' : 'notification.mp3';
        const audio = new Audio(`/sounds/${soundFile}`);
        audio.volume = 0.7;
        audio.play().catch(error => console.log('Could not play sound:', error));
    }

    function getTypeColor(type) {
        const colors = {
            'user_registration': 'success',
            'deposit': 'primary',
            'withdrawal': 'warning',
            'support': 'info',
            'system': 'danger'
        };
        return colors[type] || 'secondary';
    }

    function getTypeIcon(type) {
        const icons = {
            'user_registration': 'fe fe-user-plus',
            'deposit': 'fe fe-dollar-sign',
            'withdrawal': 'fe fe-arrow-up',
            'support': 'fe fe-message-circle',
            'system': 'fe fe-alert-triangle'
        };
        return icons[type] || 'fe fe-bell';
    }

    function updateConnectionStatus(status, type) {
        const statusElement = document.getElementById('status-text');
        const alertElement = document.getElementById('connection-status');
        
        statusElement.textContent = status;
        alertElement.className = `alert alert-${type} d-flex align-items-center`;
    }

    function updateLastUpdate() {
        document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
    }

    function updateStats() {
        document.getElementById('notification-count').textContent = notificationCount;
        document.getElementById('today-notifications').textContent = notificationCount;
        
        // Update other stats from server if needed
        fetch('{{ route("admin.notifications.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('active-connections').textContent = data.stats.active_connections || 1;
                    document.getElementById('urgent-alerts').textContent = data.stats.urgent_count || 0;
                    document.getElementById('system-health').textContent = data.stats.system_health || 'Good';
                }
            })
            .catch(error => console.error('Error updating stats:', error));
    }

    function updateActivitySummary() {
        // Update recent activity summary
        const summary = document.getElementById('activity-summary');
        const activityTypes = ['user_registration', 'deposit', 'withdrawal', 'support'];
        
        summary.innerHTML = activityTypes.map(type => `
            <div class="activity-item">
                <div class="activity-icon bg-${getTypeColor(type)}-transparent text-${getTypeColor(type)}">
                    <i class="${getTypeIcon(type)}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold" style="font-size: 12px;">${type.replace('_', ' ').toUpperCase()}</div>
                    <div class="text-muted" style="font-size: 11px;">Last: ${getRandomTime()}</div>
                </div>
            </div>
        `).join('');
    }

    function getRandomTime() {
        const times = ['2 min ago', '5 min ago', '10 min ago', '15 min ago', '30 min ago'];
        return times[Math.floor(Math.random() * times.length)];
    }

    function clearRealTimeLog() {
        if (confirm('Clear all notifications from the feed?')) {
            const feed = document.getElementById('notification-feed');
            feed.innerHTML = `
                <div class="text-center text-muted p-4" id="feed-empty-state">
                    <i class="fe fe-radio fs-1 mb-3"></i>
                    <h5>Feed cleared</h5>
                    <p>New notifications will appear here.</p>
                </div>
            `;
            notificationCount = 0;
            updateStats();
        }
    }

    function testNotification() {
        // Debug: Check if CSRF token exists
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showToast('CSRF token not found. Please refresh the page.', 'danger');
            return;
        }

        // Debug: Check if the route exists
        const testRoute = '{{ route("admin.notifications.test") }}';
        if (!testRoute || testRoute.includes('undefined')) {
            showToast('Test route not properly configured.', 'danger');
            return;
        }

        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fe fe-loader me-1"></i>Sending...';
        button.disabled = true;
        
        fetch(testRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (response.status === 419) {
                throw new Error('CSRF token mismatch (419)');
            } else if (response.status === 401) {
                throw new Error('Unauthorized access (401)');
            } else if (response.status === 403) {
                throw new Error('Forbidden access (403)');
            } else if (response.status === 500) {
                throw new Error('Internal server error (500)');
            } else if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                showToast('Test notification sent successfully!', 'success');
                
                // If real-time is active, it will pick up the notification automatically
                if (!realtimeActive) {
                    showToast('Start real-time mode to see the notification appear', 'info');
                }
            } else {
                showToast('Failed to send test notification: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error sending test notification:', error);
            
            if (error.message.includes('401') || error.message.includes('403')) {
                showToast('Authentication error. Please login as admin again.', 'danger');
            } else if (error.message.includes('419')) {
                showToast('CSRF token error. Please refresh the page and try again.', 'danger');
            } else if (error.message.includes('500')) {
                showToast('Server error occurred: ' + error.message + '. Check server logs for details.', 'danger');
            } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                showToast('Network error. Please check your internet connection.', 'danger');
            } else {
                showToast('Error: ' + error.message + '. Please try again.', 'danger');
            }
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }

    function pauseResume() {
        isPaused = !isPaused;
        const button = event.target.closest('button');
        
        if (isPaused) {
            button.innerHTML = '<i class="fe fe-play me-1"></i>Resume';
            button.className = 'btn btn-warning btn-sm';
            updateConnectionStatus('Paused', 'warning');
            showToast('Real-time monitoring paused', 'warning');
        } else {
            button.innerHTML = '<i class="fe fe-pause me-1"></i>Pause/Resume';
            button.className = 'btn btn-outline-success btn-sm';
            updateConnectionStatus('Connected', 'success');
            showToast('Real-time monitoring resumed', 'success');
        }
    }

    function exportLog() {
        const notifications = Array.from(document.querySelectorAll('.notification-item')).map(item => {
            return {
                title: item.querySelector('h6').textContent,
                message: item.querySelector('p').textContent,
                timestamp: item.querySelector('.notification-timestamp').textContent,
                type: item.querySelector('.notification-type-badge').textContent
            };
        });

        const dataStr = JSON.stringify(notifications, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `notification-log-${new Date().toISOString().split('T')[0]}.json`;
        link.click();
    }

    // Event listeners
    document.getElementById('update-interval').addEventListener('change', function() {
        if (realtimeActive) {
            stopRealtime();
            startRealtime();
        }
    });

    document.querySelectorAll('.filter-type').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (realtimeActive) {
                fetchNewNotifications();
            }
        });
    });

    document.getElementById('priority-filter').addEventListener('change', function() {
        if (realtimeActive) {
            fetchNewNotifications();
        }
    });

    function showStats() {
        // Create a modal to show detailed statistics
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'statsModal';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notification Statistics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-primary" id="modal-total-notifications">${notificationCount}</h4>
                                        <p class="mb-0">Total Notifications</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-success" id="modal-session-uptime">0</h4>
                                        <p class="mb-0">Session Uptime (min)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-warning">High Priority</h6>
                                    <h4 id="modal-high-priority">0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-danger">Urgent</h6>
                                    <h4 id="modal-urgent">0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-info">Normal</h6>
                                    <h4 id="modal-normal">0</h4>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6>Type Breakdown</h6>
                                <div id="type-breakdown">
                                    <div class="d-flex justify-content-between"><span>User Registrations:</span> <span id="type-user-reg">0</span></div>
                                    <div class="d-flex justify-content-between"><span>Deposits:</span> <span id="type-deposits">0</span></div>
                                    <div class="d-flex justify-content-between"><span>Withdrawals:</span> <span id="type-withdrawals">0</span></div>
                                    <div class="d-flex justify-content-between"><span>Support Tickets:</span> <span id="type-support">0</span></div>
                                    <div class="d-flex justify-content-between"><span>System Alerts:</span> <span id="type-system">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="refreshStats()">Refresh</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Show the modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remove modal from DOM when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
        
        // Load current stats
        refreshStats();
    }

    function refreshStats() {
        // Calculate session uptime
        const sessionStart = sessionStorage.getItem('realtimeSessionStart');
        if (sessionStart) {
            const uptime = Math.floor((Date.now() - parseInt(sessionStart)) / 60000);
            document.getElementById('modal-session-uptime').textContent = uptime;
        }
        
        // Update notification counts from feed
        const notifications = document.querySelectorAll('.notification-item');
        let highPriorityCount = 0;
        let urgentCount = 0;
        let normalCount = 0;
        let typeCount = {
            'user_registration': 0,
            'deposit': 0,
            'withdrawal': 0,
            'support': 0,
            'system': 0
        };
        
        notifications.forEach(notification => {
            if (notification.classList.contains('high')) highPriorityCount++;
            if (notification.classList.contains('urgent')) urgentCount++;
            if (!notification.classList.contains('high') && !notification.classList.contains('urgent')) normalCount++;
            
            // Count by type (extract from badge)
            const typeBadge = notification.querySelector('.notification-type-badge');
            if (typeBadge) {
                const type = typeBadge.textContent.toLowerCase().replace(' ', '_');
                if (typeCount.hasOwnProperty(type)) {
                    typeCount[type]++;
                }
            }
        });
        
        // Update modal stats
        if (document.getElementById('modal-high-priority')) {
            document.getElementById('modal-high-priority').textContent = highPriorityCount;
            document.getElementById('modal-urgent').textContent = urgentCount;
            document.getElementById('modal-normal').textContent = normalCount;
            document.getElementById('type-user-reg').textContent = typeCount['user_registration'];
            document.getElementById('type-deposits').textContent = typeCount['deposit'];
            document.getElementById('type-withdrawals').textContent = typeCount['withdrawal'];
            document.getElementById('type-support').textContent = typeCount['support'];
            document.getElementById('type-system').textContent = typeCount['system'];
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        
        const iconMap = {
            'success': 'check-circle',
            'danger': 'alert-circle',
            'warning': 'alert-triangle',
            'info': 'info'
        };
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fe fe-${iconMap[type] || 'info'} me-2"></i>
                ${message}
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fe fe-x"></i>
            </button>
        `;
        
        if (!document.getElementById('toast-styles-realtime')) {
            const toastStyles = document.createElement('style');
            toastStyles.id = 'toast-styles-realtime';
            toastStyles.textContent = `
                .toast-notification {
                    position: fixed;
                    bottom: 20px;
                    left: 20px;
                    z-index: 9998;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    border-left: 4px solid #007bff;
                    min-width: 300px;
                    animation: slideInLeft 0.3s ease-out;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 16px;
                    margin-bottom: 10px;
                }
                .toast-success { border-left-color: #28a745; }
                .toast-danger { border-left-color: #dc3545; }
                .toast-warning { border-left-color: #ffc107; }
                .toast-content { flex: 1; display: flex; align-items: center; }
                .toast-close { background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; }
                @keyframes slideInLeft {
                    from { transform: translateX(-100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(toastStyles);
        }
        
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    // Initialize session tracking
    document.addEventListener('DOMContentLoaded', function() {
        if (!sessionStorage.getItem('realtimeSessionStart')) {
            sessionStorage.setItem('realtimeSessionStart', Date.now().toString());
        }
        
        // Check authentication status on page load
        checkAuthenticationStatus();
    });

    function checkAuthenticationStatus() {
        // Simple auth check by trying to access a protected route
        fetch('{{ route("admin.notifications.stats") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.status === 401 || response.status === 403) {
                showToast('You are not logged in as admin. Please login to use this feature.', 'warning');
                // Disable quick action buttons
                document.querySelectorAll('.quick-action-btn').forEach(btn => {
                    btn.disabled = true;
                    btn.title = 'Please login as admin first';
                });
            } else if (response.ok) {
                console.log('Admin authentication verified');
            }
        })
        .catch(error => {
            console.warn('Auth check failed:', error);
        });
    }
</script>
@endpush
</x-layout>
