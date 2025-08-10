<x-smart_layout>

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Notifications</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('user.notifications.settings') }}" class="btn btn-outline-secondary btn-wave">
                <i class="fe fe-settings me-2"></i>Settings
            </a>
            <button class="btn btn-primary btn-wave" onclick="markAllAsRead()">
                <i class="fe fe-check-circle me-2"></i>Mark All Read
            </button>
            <button class="btn btn-outline-danger btn-wave ms-2" onclick="clearAllNotifications()">
                <i class="fe fe-trash me-2"></i>Clear All
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
                                <h3 class="text-dark mb-0">{{ $notifications->total() }}</h3>
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
                                <h3 class="text-warning mb-0" id="unread-count">{{ $notifications->where('read', false)->count() }}</h3>
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
                                <h6 class="mb-2 tx-12 text-muted">Read</h6>
                                <h3 class="text-success mb-0">{{ $notifications->where('read', true)->count() }}</h3>
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="avatar avatar-md br-5 bg-success-transparent text-success">
                                <i class="fe fe-check-circle fs-18"></i>
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
                                <h3 class="text-info mb-0">{{ $notifications->where('created_at', '>=', now()->subWeek())->count() }}</h3>
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

    <!-- Notifications List -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">All Notifications</div>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="filter-type" onchange="filterNotifications()">
                            <option value="">All Types</option>
                            <option value="system">System</option>
                            <option value="transaction">Transaction</option>
                            <option value="referral">Referral</option>
                            <option value="security">Security</option>
                            <option value="promotion">Promotion</option>
                        </select>
                        <select class="form-select form-select-sm" id="filter-status" onchange="filterNotifications()">
                            <option value="">All Status</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush" id="notifications-container">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->read ? 'bg-light border-start border-warning border-3' : '' }}" 
                                     data-notification-id="{{ $notification->id }}"
                                     data-type="{{ $notification->type }}"
                                     data-status="{{ $notification->read ? 'read' : 'unread' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <span class="avatar avatar-md bg-{{ $notification->read ? 'light' : 'primary' }}-transparent text-{{ $notification->read ? 'muted' : 'primary' }}">
                                                <i class="{{ $notification->icon ?? 'fe fe-bell' }} fs-16"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold">{{ $notification->title }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(!$notification->read)
                                                        <span class="badge bg-primary">New</span>
                                                    @endif
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-2">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-light text-dark">{{ ucfirst($notification->type) }}</span>
                                                    @if($notification->data && isset($notification->data['amount']))
                                                        <span class="badge bg-success">${{ number_format($notification->data['amount'], 2) }}</span>
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
                                                        <a href="{{ route('user.notifications.redirect', $notification->id) }}" 
                                                           class="btn btn-outline-info" title="View Details">
                                                            <i class="fe fe-external-link"></i>
                                                        </a>
                                                    @endif
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
                            <p class="text-muted">When you have notifications, they'll appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function markAsRead(notificationId) {
        fetch(`{{ route("user.notifications.read", ":id") }}`.replace(':id', notificationId), {
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
                    notificationElement.classList.remove('bg-light', 'border-start', 'border-warning', 'border-3');
                    notificationElement.setAttribute('data-status', 'read');
                    
                    // Update the icon
                    const icon = notificationElement.querySelector('.avatar');
                    icon.classList.remove('bg-primary-transparent', 'text-primary');
                    icon.classList.add('bg-light-transparent', 'text-muted');
                    
                    // Remove "New" badge
                    const badge = notificationElement.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                    
                    // Remove read button
                    const readBtn = notificationElement.querySelector('.btn-outline-primary');
                    if (readBtn) readBtn.remove();
                }
                
                // Update stats
                updateNotificationStats();
                Swal.fire({
                    title: 'Success!',
                    text: 'Notification marked as read.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to mark notification as read.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while marking notification as read.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
    }

    function markAllAsRead() {
        Swal.fire({
            title: 'Mark All as Read?',
            text: 'This will mark all notifications as read.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fe fe-check-circle me-1"></i>Yes, Mark All',
            cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (!result.isConfirmed) return;

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
                // Update all unread notifications
                document.querySelectorAll('[data-status="unread"]').forEach(element => {
                    element.classList.remove('bg-light', 'border-start', 'border-warning', 'border-3');
                    element.setAttribute('data-status', 'read');
                    
                    // Update the icon
                    const icon = element.querySelector('.avatar');
                    icon.classList.remove('bg-primary-transparent', 'text-primary');
                    icon.classList.add('bg-light-transparent', 'text-muted');
                    
                    // Remove "New" badge
                    const badge = element.querySelector('.badge.bg-primary');
                    if (badge) badge.remove();
                    
                    // Remove read button
                    const readBtn = element.querySelector('.btn-outline-primary');
                    if (readBtn) readBtn.remove();
                });
                
                updateNotificationStats();
                Swal.fire({
                    title: 'Success!',
                    text: 'All notifications marked as read.',
                    icon: 'success',
                    confirmButtonText: 'Great!',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to mark all notifications as read.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while marking notifications as read.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
        });
    }

    function deleteNotification(notificationId) {
        Swal.fire({
            title: 'Delete Notification?',
            text: 'Are you sure you want to delete this notification?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fe fe-trash me-1"></i>Yes, Delete',
            cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`{{ route("user.notifications.delete", ":id") }}`.replace(':id', notificationId), {
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
                updateNotificationStats();
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Notification has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to delete notification.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while deleting the notification.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        });
        });
    }

    function clearAllNotifications() {
        Swal.fire({
            title: 'Clear All Notifications?',
            text: 'This action cannot be undone and will permanently delete all your notifications.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fe fe-trash me-1"></i>Yes, Clear All',
            cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (!result.isConfirmed) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                Swal.fire({
                    title: 'Error!',
                    text: 'CSRF token not found. Please refresh the page.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }

            console.log('Clearing all notifications...');

            // Show processing state
            Swal.fire({
                title: 'Clearing Notifications...',
                text: 'Please wait while we clear all your notifications.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Show loading state
            const clearButton = document.querySelector('button[onclick="clearAllNotifications()"]');
            const originalText = clearButton ? clearButton.innerHTML : '';
            if (clearButton) {
                clearButton.innerHTML = '<i class="fe fe-loader me-1"></i>Clearing...';
                clearButton.disabled = true;
            }

        fetch('{{ route("user.notifications.clear-all") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response URL:', response.url);
            
            if (response.status === 404) {
                throw new Error('Clear all endpoint not found. Please contact support.');
            } else if (response.status === 401) {
                throw new Error('Authentication required. Please log in again.');
            } else if (response.status === 419) {
                throw new Error('Session expired. Please refresh the page and try again.');
            } else if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Clear all response:', data);
            if (data.success) {
                // Clear the notifications container
                document.getElementById('notifications-container').innerHTML = `
                    <div class="text-center p-5">
                        <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications</h5>
                        <p class="text-muted">All notifications have been cleared.</p>
                    </div>
                `;
                
                // Update all stats to 0
                document.querySelector('.text-dark.mb-0').textContent = '0'; // Total notifications
                document.getElementById('unread-count').textContent = '0'; // Unread count  
                document.querySelector('.text-success.mb-0').textContent = '0'; // Read count
                document.querySelector('.text-info.mb-0').textContent = '0'; // This week count
                
                console.log('Notifications cleared successfully');
                
                Swal.fire({
                    title: 'Success!',
                    text: 'All notifications have been cleared successfully.',
                    icon: 'success',
                    confirmButtonText: 'Great!',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    buttonsStyling: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                
                // Update the page content without reloading
                // The notifications container has already been updated above
            } else {
                console.error('Failed to clear notifications:', data.message);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to clear notifications: ' + (data.message || 'Unknown error'),
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        })
        .catch(error => {
            console.error('Error clearing all notifications:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Error clearing notifications: ' + error.message,
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        })
        .finally(() => {
            // Restore button state
            if (clearButton) {
                clearButton.innerHTML = originalText || '<i class="fe fe-trash me-2"></i>Clear All';
                clearButton.disabled = false;
            }
        });
        });
    }

    function filterNotifications() {
        const typeFilter = document.getElementById('filter-type').value;
        const statusFilter = document.getElementById('filter-status').value;
        const notifications = document.querySelectorAll('.notification-item');

        notifications.forEach(notification => {
            const type = notification.getAttribute('data-type');
            const status = notification.getAttribute('data-status');
            
            let showNotification = true;
            
            if (typeFilter && type !== typeFilter) {
                showNotification = false;
            }
            
            if (statusFilter && status !== statusFilter) {
                showNotification = false;
            }
            
            notification.style.display = showNotification ? 'block' : 'none';
        });
    }

    function updateNotificationStats() {
        const totalNotifications = document.querySelectorAll('.notification-item').length;
        const unreadNotifications = document.querySelectorAll('[data-status="unread"]').length;
        const readNotifications = totalNotifications - unreadNotifications;
        
        document.getElementById('unread-count').textContent = unreadNotifications;
    }

    function showToast(message, type = 'info') {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
</script>
@endpush

</x-smart_layout>
