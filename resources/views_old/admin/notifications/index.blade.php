<x-layout>

@section('title', 'Admin Notifications')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Admin Notifications</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-success btn-wave">
                <i class="fe fe-plus me-2"></i>Create Notification
            </a>
            <button class="btn btn-primary btn-wave ms-2" onclick="markAllAsRead()">
                <i class="fe fe-check-circle me-2"></i>Mark All Read
            </button>
            <button class="btn btn-outline-danger btn-wave ms-2" onclick="clearAllNotifications()">
                <i class="fe fe-trash me-2"></i>Clear All
            </button>
            <button class="btn btn-outline-info btn-wave ms-2" data-bs-toggle="modal" data-bs-target="#sendAnnouncementModal">
                <i class="fe fe-megaphone me-2"></i>Send Announcement
            </button>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="row">
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Total Notifications</h6>
                                <h3 class="text-dark mb-0" id="total-notifications">{{ $notifications->total() }}</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-primary-transparent text-primary">
                                <i class="fe fe-bell fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Unread</h6>
                                <h3 class="text-warning mb-0" id="unread-count">{{ $stats['unread'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-warning-transparent text-warning">
                                <i class="fe fe-bell-off fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">Urgent</h6>
                                <h3 class="text-danger mb-0" id="urgent-count">{{ $stats['urgent'] ?? 0 }}</h3>
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
        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="flex-fill">
                                <h6 class="mb-2 tx-12 text-muted">This Week</h6>
                                <h3 class="text-info mb-0" id="week-count">{{ $stats['this_week'] ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-info-transparent text-info">
                                <i class="fe fe-calendar fs-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Filters -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Filter Notifications</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="filter-type" onchange="filterNotifications()">
                                <option value="">All Types</option>
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Danger</option>
                                <option value="primary">Primary</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="filter-priority" onchange="filterNotifications()">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filter-status" onchange="filterNotifications()">
                                <option value="">All Status</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <input type="date" class="form-control" id="filter-date" onchange="filterNotifications()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">All Notifications</div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshNotifications()">
                            <i class="fe fe-refresh-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush" id="notifications-container">
                            @foreach($notifications as $notification)
                                <div class="notification-item list-group-item {{ !$notification->read ? 'bg-light border-start border-3' : '' }} {{ $notification->priority_class }}" 
                                     data-notification-id="{{ $notification->id }}"
                                     data-type="{{ $notification->type }}"
                                     data-priority="{{ $notification->priority }}"
                                     data-status="{{ $notification->read ? 'read' : 'unread' }}"
                                     data-date="{{ $notification->created_at->format('Y-m-d') }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <span class="avatar avatar-md bg-{{ $notification->type }}-transparent text-{{ $notification->type }}">
                                                <i class="{{ $notification->icon }} fs-16"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold">{{ $notification->title }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(!$notification->read)
                                                        <span class="badge bg-primary">New</span>
                                                    @endif
                                                    @if($notification->priority === 'urgent')
                                                        <span class="badge bg-danger">Urgent</span>
                                                    @elseif($notification->priority === 'high')
                                                        <span class="badge bg-warning">High</span>
                                                    @endif
                                                    <small class="text-muted">{{ $notification->time_ago }}</small>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-2">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-light text-dark">{{ ucfirst($notification->type) }}</span>
                                                    @if($notification->metadata && isset($notification->metadata['user']))
                                                        <span class="badge bg-info">User: {{ $notification->metadata['user'] }}</span>
                                                    @endif
                                                    @if($notification->expires_at)
                                                        <span class="badge bg-secondary">Expires: {{ $notification->expires_at->format('M j, Y') }}</span>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if(!$notification->read)
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                onclick="markAsRead({{ $notification->id }})"
                                                                title="Mark as Read">
                                                            <i class="fe fe-check"></i>
                                                        </button>
                                                    @endif
                                                    @if($notification->action_url)
                                                        <a href="{{ route('admin.notifications.redirect', $notification->id) }}" 
                                                           class="btn btn-outline-info" title="View Details">
                                                            <i class="fe fe-external-link"></i>
                                                        </a>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="showNotificationDetails({{ $notification->id }})"
                                                            title="View Details">
                                                        <i class="fe fe-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteNotification({{ $notification->id }})"
                                                            title="Delete">
                                                        <i class="fe fe-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications
                                </div>
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications yet</h5>
                            <p class="text-muted">Admin notifications will appear here when available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Announcement Modal -->
<div class="modal fade" id="sendAnnouncementModal" tabindex="-1" aria-labelledby="sendAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendAnnouncementModalLabel">
                    <i class="fe fe-megaphone me-2"></i>Send System Announcement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="announcementForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="announcement-title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="announcement-title" name="title" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label for="announcement-type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="announcement-type" name="type" required>
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Danger</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="announcement-priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="announcement-priority" name="priority" required>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="announcement-message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="announcement-message" name="message" rows="4" required maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>
                        <div class="col-md-6">
                            <label for="announcement-target" class="form-label">Send To</label>
                            <select class="form-select" id="announcement-target" name="target">
                                <option value="all">All Users</option>
                                <option value="active">Active Users Only</option>
                                <option value="admins">Admins Only</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="announcement-expires" class="form-label">Expires At</label>
                            <input type="datetime-local" class="form-control" id="announcement-expires" name="expires_at">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-send me-2"></i>Send Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationDetailsModal" tabindex="-1" aria-labelledby="notificationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationDetailsModalLabel">
                    <i class="fe fe-info-circle me-2"></i>Notification Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="notification-details-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    // Notification management functions
    function markAsRead(notificationId) {
        fetch(`{{ route("admin.notifications.read", ":id") }}`.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationElement) {
                    notificationElement.classList.remove('bg-light', 'border-start', 'border-3');
                    notificationElement.setAttribute('data-status', 'read');
                    
                    const badge = notificationElement.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                    
                    const readBtn = notificationElement.querySelector('.btn-outline-primary');
                    if (readBtn) readBtn.remove();
                }
                
                updateStats();
                showToast('Notification marked as read', 'success');
            } else {
                showToast('Failed to mark notification as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function markAllAsRead() {
        if (!confirm('Mark all notifications as read?')) return;

        fetch('{{ route("admin.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('[data-status="unread"]').forEach(element => {
                    element.classList.remove('bg-light', 'border-start', 'border-3');
                    element.setAttribute('data-status', 'read');
                    
                    const badge = element.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                    
                    const readBtn = element.querySelector('.btn-outline-primary');
                    if (readBtn) readBtn.remove();
                });
                
                updateStats();
                showToast('All notifications marked as read', 'success');
            } else {
                showToast('Failed to mark all notifications as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function deleteNotification(notificationId) {
        if (!confirm('Are you sure you want to delete this notification?')) return;

        fetch(`{{ route("admin.notifications.delete", ":id") }}`.replace(':id', notificationId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationElement) {
                    notificationElement.remove();
                }
                updateStats();
                showToast('Notification deleted', 'success');
            } else {
                showToast('Failed to delete notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function clearAllNotifications() {
        if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) return;

        fetch('{{ route("admin.notifications.clear-all") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('notifications-container').innerHTML = `
                    <div class="text-center p-5">
                        <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications</h5>
                        <p class="text-muted">All notifications have been cleared.</p>
                    </div>
                `;
                updateStats();
                showToast('All notifications cleared successfully!', 'success');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('Failed to clear notifications', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function filterNotifications() {
        const typeFilter = document.getElementById('filter-type').value;
        const priorityFilter = document.getElementById('filter-priority').value;
        const statusFilter = document.getElementById('filter-status').value;
        const dateFilter = document.getElementById('filter-date').value;
        const notifications = document.querySelectorAll('.notification-item');

        notifications.forEach(notification => {
            const type = notification.getAttribute('data-type');
            const priority = notification.getAttribute('data-priority');
            const status = notification.getAttribute('data-status');
            const date = notification.getAttribute('data-date');
            
            let showNotification = true;
            
            if (typeFilter && type !== typeFilter) {
                showNotification = false;
            }
            
            if (priorityFilter && priority !== priorityFilter) {
                showNotification = false;
            }
            
            if (statusFilter && status !== statusFilter) {
                showNotification = false;
            }
            
            if (dateFilter && date !== dateFilter) {
                showNotification = false;
            }
            
            notification.style.display = showNotification ? 'block' : 'none';
        });
    }

    function refreshNotifications() {
        window.location.reload();
    }

    function updateStats() {
        const totalNotifications = document.querySelectorAll('.notification-item').length;
        const unreadNotifications = document.querySelectorAll('[data-status="unread"]').length;
        const urgentNotifications = document.querySelectorAll('[data-priority="urgent"]').length;
        
        document.getElementById('total-notifications').textContent = totalNotifications;
        document.getElementById('unread-count').textContent = unreadNotifications;
        document.getElementById('urgent-count').textContent = urgentNotifications;
    }

    function showNotificationDetails(notificationId) {
        // Add loading state
        showToast('Loading notification details...', 'info');
        
        fetch(`{{ route("admin.notifications.show", ":id") }}`.replace(':id', notificationId), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const notification = data.notification;
                document.getElementById('notification-details-content').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title:</label>
                            <p>${notification.title}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type:</label>
                            <p><span class="badge bg-${notification.type}">${notification.type.charAt(0).toUpperCase() + notification.type.slice(1)}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority:</label>
                            <p><span class="badge bg-${notification.priority === 'urgent' ? 'danger' : notification.priority === 'high' ? 'warning' : 'secondary'}">${notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1)}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status:</label>
                            <p><span class="badge bg-${notification.read ? 'success' : 'primary'}">${notification.read ? 'Read' : 'Unread'}</span></p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Message:</label>
                            <p>${notification.message}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Created:</label>
                            <p>${notification.formatted_time}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Read At:</label>
                            <p>${notification.read_at || 'Not read yet'}</p>
                        </div>
                        ${notification.action_url ? `
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Action URL:</label>
                            <p><a href="${notification.action_url}" target="_blank">${notification.action_url}</a></p>
                        </div>
                        ` : ''}
                        ${notification.expires_at ? `
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Expires At:</label>
                            <p>${notification.expires_at}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('notificationDetailsModal'));
                modal.show();
                showToast('Notification details loaded successfully', 'success');
            } else {
                showToast(data.message || 'Failed to load notification details', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading notification details:', error);
            showToast(`Error: ${error.message}`, 'error');
        });
    }

    // Send announcement form
    document.getElementById('announcementForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        fetch('{{ route("admin.notifications.send-announcement") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('sendAnnouncementModal')).hide();
                this.reset();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Failed to send announcement', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    });

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'} me-2"></i>
                ${message}
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fe fe-x"></i>
            </button>
        `;
        
        if (!document.getElementById('toast-styles')) {
            const toastStyles = document.createElement('style');
            toastStyles.id = 'toast-styles';
            toastStyles.textContent = `
                .toast-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    border-left: 4px solid #007bff;
                    min-width: 300px;
                    animation: slideInRight 0.3s ease-out;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 12px 16px;
                    margin-bottom: 10px;
                }
                .toast-success { border-left-color: #28a745; }
                .toast-error { border-left-color: #dc3545; }
                .toast-warning { border-left-color: #ffc107; }
                .toast-content { flex: 1; display: flex; align-items: center; }
                .toast-close { 
                    background: none; 
                    border: none; 
                    cursor: pointer; 
                    padding: 4px;
                    margin-left: 8px;
                    border-radius: 4px;
                    transition: background-color 0.2s;
                }
                .toast-close:hover { background-color: rgba(0,0,0,0.1); }
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(toastStyles);
        }
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    }
</script>
@endpush
</x-layout>
