<x-layout>

@section('title', 'Notification Details')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Notification Details</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-wave">
                <i class="fe fe-arrow-left me-2"></i>Back to Notifications
            </a>
            @if(!$notification->read)
                <button class="btn btn-primary btn-wave ms-2" onclick="markAsRead()">
                    <i class="fe fe-check me-2"></i>Mark as Read
                </button>
            @endif
            <button class="btn btn-outline-danger btn-wave ms-2" onclick="deleteNotification()">
                <i class="fe fe-trash me-2"></i>Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Main Notification Details -->
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-lg bg-{{ $notification->type }}-transparent text-{{ $notification->type }} me-3">
                            <i class="{{ $notification->icon }} fs-18"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ $notification->title }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $notification->type }}">{{ ucfirst($notification->type) }}</span>
                                @if($notification->priority === 'urgent')
                                    <span class="badge bg-danger">Urgent</span>
                                @elseif($notification->priority === 'high')
                                    <span class="badge bg-warning">High Priority</span>
                                @elseif($notification->priority === 'normal')
                                    <span class="badge bg-info">Normal</span>
                                @else
                                    <span class="badge bg-secondary">Low Priority</span>
                                @endif
                                @if(!$notification->read)
                                    <span class="badge bg-primary">Unread</span>
                                @else
                                    <span class="badge bg-success">Read</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="notification-content">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">Message:</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $notification->message }}</p>
                            </div>
                        </div>

                        @if($notification->action_text && $notification->action_url)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Recommended Action:</h6>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted">{{ $notification->action_text }}</span>
                                    <a href="{{ route('admin.notifications.redirect', $notification->id) }}" 
                                       class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fe fe-external-link me-1"></i>{{ $notification->action_text }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if($notification->metadata && count($notification->metadata) > 0)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Additional Information:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            @foreach($notification->metadata as $key => $value)
                                                <tr>
                                                    <td class="fw-semibold" style="width: 30%;">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                                    <td>
                                                        @if(is_array($value) || is_object($value))
                                                            <pre class="mb-0 text-wrap">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @elseif(filter_var($value, FILTER_VALIDATE_URL))
                                                            <a href="{{ $value }}" target="_blank" class="text-decoration-none">{{ $value }}</a>
                                                        @elseif(is_numeric($value) && strlen($value) > 10)
                                                            {{ number_format($value, 2) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if($notification->expires_at)
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Expiration:</h6>
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="fe fe-clock me-2"></i>
                                    <div>
                                        This notification will expire on <strong>{{ $notification->expires_at->format('M j, Y \a\t g:i A') }}</strong>
                                        @if($notification->expires_at->isPast())
                                            <span class="badge bg-danger ms-2">Expired</span>
                                        @elseif($notification->expires_at->diffInDays() <= 1)
                                            <span class="badge bg-warning ms-2">Expires Soon</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Notifications -->
            @if($relatedNotifications && $relatedNotifications->count() > 0)
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fe fe-link me-2"></i>Related Notifications
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($relatedNotifications as $related)
                                <div class="list-group-item d-flex align-items-center {{ !$related->read ? 'bg-light' : '' }}">
                                    <span class="avatar avatar-sm bg-{{ $related->type }}-transparent text-{{ $related->type }} me-3">
                                        <i class="{{ $related->icon }} fs-14"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $related->title }}</h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($related->message, 100) }}</p>
                                        <small class="text-muted">{{ $related->time_ago }}</small>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="{{ route('admin.notifications.show', $related->id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fe fe-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="col-xl-4">
            <!-- Notification Information -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-info me-2"></i>Notification Information
                    </div>
                </div>
                <div class="card-body">
                    <div class="notification-info">
                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold mb-1">Created:</label>
                            <p class="mb-0">{{ $notification->created_at->format('M j, Y \a\t g:i A') }}</p>
                            <small class="text-muted">({{ $notification->time_ago }})</small>
                        </div>

                        @if($notification->read_at)
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold mb-1">Read At:</label>
                                <p class="mb-0">{{ $notification->read_at->format('M j, Y \a\t g:i A') }}</p>
                                <small class="text-muted">({{ $notification->read_at->diffForHumans() }})</small>
                            </div>
                        @endif

                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold mb-1">Priority Level:</label>
                            <div class="priority-indicator">
                                @switch($notification->priority)
                                    @case('urgent')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fe fe-alert-triangle me-1"></i>Urgent
                                        </span>
                                        <p class="text-muted small mb-0 mt-1">Requires immediate attention</p>
                                        @break
                                    @case('high')
                                        <span class="badge bg-warning fs-6">
                                            <i class="fe fe-alert-circle me-1"></i>High
                                        </span>
                                        <p class="text-muted small mb-0 mt-1">Should be addressed soon</p>
                                        @break
                                    @case('normal')
                                        <span class="badge bg-info fs-6">
                                            <i class="fe fe-info me-1"></i>Normal
                                        </span>
                                        <p class="text-muted small mb-0 mt-1">Standard notification</p>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fe fe-minus-circle me-1"></i>Low
                                        </span>
                                        <p class="text-muted small mb-0 mt-1">For information only</p>
                                @endswitch
                            </div>
                        </div>

                        <div class="info-item mb-3">
                            <label class="form-label fw-semibold mb-1">Notification ID:</label>
                            <p class="mb-0 font-monospace">#{{ $notification->id }}</p>
                        </div>

                        @if($notification->expires_at)
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold mb-1">Expires:</label>
                                <p class="mb-0">{{ $notification->expires_at->format('M j, Y \a\t g:i A') }}</p>
                                <small class="text-muted">({{ $notification->expires_at->diffForHumans() }})</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-zap me-2"></i>Quick Actions
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$notification->read)
                            <button class="btn btn-primary" onclick="markAsRead()">
                                <i class="fe fe-check me-2"></i>Mark as Read
                            </button>
                        @else
                            <button class="btn btn-outline-secondary" onclick="markAsUnread()">
                                <i class="fe fe-eye-off me-2"></i>Mark as Unread
                            </button>
                        @endif

                        @if($notification->action_url)
                            <a href="{{ route('admin.notifications.redirect', $notification->id) }}" 
                               class="btn btn-info" target="_blank">
                                <i class="fe fe-external-link me-2"></i>View Related Item
                            </a>
                        @endif

                        <button class="btn btn-outline-warning" onclick="duplicateNotification()">
                            <i class="fe fe-copy me-2"></i>Duplicate Notification
                        </button>

                        <button class="btn btn-outline-info" onclick="shareNotification()">
                            <i class="fe fe-share me-2"></i>Share Notification
                        </button>

                        <hr class="my-2">

                        <button class="btn btn-outline-danger" onclick="deleteNotification()">
                            <i class="fe fe-trash me-2"></i>Delete Notification
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notification Statistics -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-bar-chart me-2"></i>Statistics
                    </div>
                </div>
                <div class="card-body">
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Similar Notifications Today:</span>
                            <span class="fw-semibold">{{ $stats['similar_today'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total {{ ucfirst($notification->type) }}:</span>
                            <span class="fw-semibold">{{ $stats['type_total'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Response Time:</span>
                            <span class="fw-semibold">
                                @if($notification->read_at)
                                    {{ $notification->created_at->diffInMinutes($notification->read_at) }} min
                                @else
                                    <span class="text-warning">Pending</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function markAsRead() {
        fetch('{{ route("admin.notifications.read", $notification->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notification marked as read', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast('Failed to mark notification as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function markAsUnread() {
        fetch('{{ route("admin.notifications.unread", $notification->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notification marked as unread', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast('Failed to mark notification as unread', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function deleteNotification() {
        if (!confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
            return;
        }

        fetch('{{ route("admin.notifications.delete", $notification->id) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notification deleted successfully', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.notifications.index") }}';
                }, 1500);
            } else {
                showToast('Failed to delete notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function duplicateNotification() {
        if (!confirm('Create a duplicate of this notification?')) {
            return;
        }

        fetch('{{ route("admin.notifications.duplicate", $notification->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Notification duplicated successfully', 'success');
                setTimeout(() => {
                    window.location.href = `{{ route("admin.notifications.show", ":id") }}`.replace(':id', data.notification_id);
                }, 1500);
            } else {
                showToast('Failed to duplicate notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function shareNotification() {
        const url = window.location.href;
        const title = '{{ addslashes($notification->title) }}';
        const text = '{{ addslashes(Str::limit($notification->message, 100)) }}';

        if (navigator.share) {
            navigator.share({
                title: title,
                text: text,
                url: url
            }).then(() => {
                showToast('Notification shared successfully', 'success');
            }).catch(error => {
                console.log('Error sharing:', error);
                fallbackShare(url, title, text);
            });
        } else {
            fallbackShare(url, title, text);
        }
    }

    function fallbackShare(url, title, text) {
        const shareData = `${title}\n\n${text}\n\n${url}`;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareData).then(() => {
                showToast('Notification details copied to clipboard', 'success');
            }).catch(error => {
                console.error('Error copying to clipboard:', error);
                showToast('Could not copy to clipboard', 'error');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = shareData;
            document.body.appendChild(textArea);
            textArea.select();
            
            try {
                document.execCommand('copy');
                showToast('Notification details copied to clipboard', 'success');
            } catch (error) {
                console.error('Error copying to clipboard:', error);
                showToast('Could not copy to clipboard', 'error');
            }
            
            document.body.removeChild(textArea);
        }
    }

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
