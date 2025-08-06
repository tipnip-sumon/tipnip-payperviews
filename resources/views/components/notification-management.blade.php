<!-- Enhanced Notification Management Modal -->
<div class="modal fade" id="notificationManagementModal" tabindex="-1" aria-labelledby="notificationManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="notificationManagementModalLabel">
                    <i class="fe fe-bell me-2"></i>Notification Management
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Quick Actions -->
                <div class="notification-quick-actions mb-4">
                    <h6 class="fw-semibold mb-3">Quick Actions</h6>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <button class="btn btn-outline-primary w-100 btn-sm" onclick="performBulkAction('mark-read')">
                                <i class="fe fe-check-circle me-1"></i>
                                <div>Mark All Read</div>
                                <small class="text-muted d-block">Clear unread status</small>
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button class="btn btn-outline-danger w-100 btn-sm" onclick="performBulkAction('clear-all')">
                                <i class="fe fe-trash me-1"></i>
                                <div>Clear All</div>
                                <small class="text-muted d-block">Delete all notifications</small>
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-info w-100 btn-sm">
                                <i class="fe fe-list me-1"></i>
                                <div>View All</div>
                                <small class="text-muted d-block">Full notification page</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('user.notifications.settings') }}" class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="fe fe-settings me-1"></i>
                                <div>Settings</div>
                                <small class="text-muted d-block">Manage preferences</small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notification Filters -->
                <div class="notification-filters mb-4">
                    <h6 class="fw-semibold mb-3">Filter Notifications</h6>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="filter-type" onchange="filterNotifications()">
                                <option value="">All Types</option>
                                <option value="investment">Investment</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="referral">Referral</option>
                                <option value="security">Security</option>
                                <option value="system">System</option>
                                <option value="promotion">Promotion</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="filter-status" onchange="filterNotifications()">
                                <option value="">All Status</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="filter-priority" onchange="filterNotifications()">
                                <option value="">All Priority</option>
                                <option value="urgent">Urgent</option>
                                <option value="high">High</option>
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notification List -->
                <div class="notification-modal-list" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center p-4" id="modal-notification-loading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0 text-muted">Loading notifications...</p>
                    </div>
                    <div id="modal-notification-list">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>

                <!-- Bulk Selection -->
                <div class="notification-bulk-selection mt-3 d-none" id="bulk-selection-area">
                    <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-notifications">
                            <label class="form-check-label" for="select-all-notifications">
                                Select All (<span id="selected-count">0</span> selected)
                            </label>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkMarkAsRead()">
                                <i class="fe fe-check me-1"></i>Mark Read
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                                <i class="fe fe-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div class="notification-stats">
                        <small class="text-muted">
                            <span id="modal-total-count">0</span> total, 
                            <span id="modal-unread-count">0</span> unread
                        </small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="refreshModalNotifications()">
                            <i class="fe fe-refresh-cw me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Notification Management JavaScript -->
<script>
let modalNotifications = [];
let selectedNotifications = [];

// Initialize modal when opened
document.getElementById('notificationManagementModal')?.addEventListener('shown.bs.modal', function () {
    loadModalNotifications();
});

function loadModalNotifications() {
    document.getElementById('modal-notification-loading').style.display = 'block';
    document.getElementById('modal-notification-list').innerHTML = '';

    fetch('{{ route("user.notifications.dropdown") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('modal-notification-loading').style.display = 'none';
            
            if (data.success) {
                modalNotifications = data.notifications;
                updateModalStats(data);
                renderModalNotifications(data.notifications);
            } else {
                showModalError('Failed to load notifications');
            }
        })
        .catch(error => {
            document.getElementById('modal-notification-loading').style.display = 'none';
            console.error('Error loading notifications:', error);
            showModalError('An error occurred while loading notifications');
        });
}

function renderModalNotifications(notifications) {
    const container = document.getElementById('modal-notification-list');
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="text-center p-4">
                <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                <h6 class="text-muted">No notifications</h6>
                <p class="text-muted mb-0">You're all caught up!</p>
            </div>
        `;
        return;
    }

    const notificationsHtml = notifications.map(notification => {
        const isSelected = selectedNotifications.includes(notification.id);
        return `
            <div class="notification-modal-item p-3 border-bottom ${!notification.read ? 'bg-light' : ''}" 
                 data-notification-id="${notification.id}"
                 data-type="${notification.type}"
                 data-status="${notification.read ? 'read' : 'unread'}"
                 data-priority="${notification.priority}">
                <div class="d-flex">
                    <div class="form-check me-3">
                        <input class="form-check-input notification-checkbox" 
                               type="checkbox" 
                               value="${notification.id}"
                               ${isSelected ? 'checked' : ''}
                               onchange="updateSelectedNotifications()">
                    </div>
                    <div class="me-3">
                        <i class="${notification.icon} fs-5 text-${getNotificationColorClass(notification.type)}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${notification.title}</h6>
                        <p class="mb-1 text-muted small">${notification.message}</p>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">${notification.time_ago}</small>
                            <span class="badge bg-${getNotificationColorClass(notification.type)}-transparent text-${getNotificationColorClass(notification.type)}">
                                ${notification.type}
                            </span>
                            ${notification.priority !== 'normal' ? `
                                <span class="badge bg-${getPriorityColorClass(notification.priority)}-transparent">
                                    ${notification.priority}
                                </span>
                            ` : ''}
                            ${!notification.read ? '<span class="badge bg-primary">New</span>' : ''}
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown">
                            <i class="fe fe-more-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            ${!notification.read ? `
                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="markSingleAsRead(${notification.id})">
                                    <i class="fe fe-check me-2"></i>Mark as Read
                                </a></li>
                            ` : ''}
                            ${notification.action_url ? `
                                <li><a class="dropdown-item" href="${notification.action_url}">
                                    <i class="fe fe-external-link me-2"></i>View Details
                                </a></li>
                            ` : ''}
                            <li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteSingleNotification(${notification.id})">
                                <i class="fe fe-trash me-2"></i>Delete
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = notificationsHtml;
    updateBulkSelectionVisibility();
}

function updateModalStats(data) {
    document.getElementById('modal-total-count').textContent = data.total_count || 0;
    document.getElementById('modal-unread-count').textContent = data.unread_count || 0;
}

function filterNotifications() {
    const typeFilter = document.getElementById('filter-type').value;
    const statusFilter = document.getElementById('filter-status').value;
    const priorityFilter = document.getElementById('filter-priority').value;

    const filteredNotifications = modalNotifications.filter(notification => {
        const typeMatch = !typeFilter || notification.type === typeFilter;
        const statusMatch = !statusFilter || (statusFilter === 'read' ? notification.read : !notification.read);
        const priorityMatch = !priorityFilter || notification.priority === priorityFilter;
        
        return typeMatch && statusMatch && priorityMatch;
    });

    renderModalNotifications(filteredNotifications);
}

function updateSelectedNotifications() {
    const checkboxes = document.querySelectorAll('.notification-checkbox:checked');
    selectedNotifications = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    document.getElementById('selected-count').textContent = selectedNotifications.length;
    document.getElementById('select-all-notifications').checked = selectedNotifications.length === modalNotifications.length;
    
    updateBulkSelectionVisibility();
}

function updateBulkSelectionVisibility() {
    const bulkArea = document.getElementById('bulk-selection-area');
    if (selectedNotifications.length > 0) {
        bulkArea.classList.remove('d-none');
    } else {
        bulkArea.classList.add('d-none');
    }
}

function performBulkAction(action) {
    switch(action) {
        case 'mark-read':
            if (confirm('Mark all notifications as read?')) {
                bulkMarkAllAsRead();
            }
            break;
        case 'clear-all':
            if (confirm('Delete all notifications? This action cannot be undone.')) {
                clearAllNotifications();
            }
            break;
    }
}

function bulkMarkAllAsRead() {
    fetch('{{ route("user.notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModalSuccess('All notifications marked as read');
            loadModalNotifications();
            refreshNotificationCount();
        } else {
            showModalError('Failed to mark notifications as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModalError('An error occurred');
    });
}

function clearAllNotifications() {
    fetch('{{ route("user.notifications.clear-all") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModalSuccess('All notifications cleared');
            loadModalNotifications();
            refreshNotificationCount();
        } else {
            showModalError('Failed to clear notifications');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModalError('An error occurred');
    });
}

function markSingleAsRead(id) {
    fetch(`{{ route("user.notifications.read", ":id") }}`.replace(':id', id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadModalNotifications();
            refreshNotificationCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteSingleNotification(id) {
    if (!confirm('Delete this notification?')) return;

    fetch(`{{ route("user.notifications.delete", ":id") }}`.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadModalNotifications();
            refreshNotificationCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function refreshModalNotifications() {
    selectedNotifications = [];
    loadModalNotifications();
}

function getNotificationColorClass(type) {
    const colorMap = {
        'investment': 'success',
        'withdrawal': 'warning',
        'referral': 'primary',
        'security': 'danger',
        'system': 'info',
        'promotion': 'purple',
        'welcome': 'info'
    };
    return colorMap[type] || 'secondary';
}

function getPriorityColorClass(priority) {
    const colorMap = {
        'urgent': 'danger',
        'high': 'warning',
        'normal': 'primary',
        'low': 'secondary'
    };
    return colorMap[priority] || 'secondary';
}

function showModalSuccess(message) {
    // Implementation for success toast in modal context
    console.log('Success:', message);
}

function showModalError(message) {
    // Implementation for error toast in modal context
    console.error('Error:', message);
}

// Select all functionality
document.getElementById('select-all-notifications')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedNotifications();
});
</script>

<!-- Modal trigger button (can be placed anywhere) -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#notificationManagementModal">
    <i class="fe fe-settings me-2"></i>Manage Notifications
</button> -->
