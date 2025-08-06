<x-smart_layout>

@section('title', 'Notification Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Notification Details</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.notifications.index') }}">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary btn-wave">
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
                            <i class="{{ $notification->icon ?? 'fe fe-bell' }} fs-18"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ $notification->title }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $notification->type }}-transparent text-{{ $notification->type }}">
                                    {{ ucfirst($notification->type ?? 'info') }}
                                </span>
                                <span class="badge bg-{{ $notification->priority === 'urgent' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'secondary') }}-transparent">
                                    {{ ucfirst($notification->priority ?? 'normal') }} Priority
                                </span>
                                @if(!$notification->read)
                                    <span class="badge bg-primary">New</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Message Content -->
                    <div class="notification-content mb-4">
                        <h6 class="fw-semibold mb-2">Message:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($notification->message)) !!}
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if($notification->action_url && $notification->action_text)
                        <div class="notification-action mb-4">
                            <h6 class="fw-semibold mb-2">Quick Action:</h6>
                            <a href="{{ $notification->action_url }}" class="btn btn-primary btn-wave">
                                <i class="fe fe-external-link me-2"></i>{{ $notification->action_text }}
                            </a>
                        </div>
                    @endif

                    <!-- Additional Data -->
                    @if($notification->data && is_array($notification->data) && count($notification->data) > 0)
                        <div class="notification-data">
                            <h6 class="fw-semibold mb-2">Additional Information:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    @foreach($notification->data as $key => $value)
                                        <tr>
                                            <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                            <td>
                                                @if(is_numeric($value) && strlen($value) > 10)
                                                    {{ number_format($value, 2) }}
                                                @elseif(is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                                                    <a href="{{ $value }}" target="_blank">{{ $value }}</a>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notification Metadata -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Notification Information</div>
                </div>
                <div class="card-body">
                    <div class="notification-meta">
                        <div class="meta-item mb-3">
                            <label class="fw-semibold text-muted">Status:</label>
                            <div class="mt-1">
                                @if($notification->read)
                                    <span class="badge bg-success-transparent text-success">
                                        <i class="fe fe-check-circle me-1"></i>Read
                                    </span>
                                @else
                                    <span class="badge bg-warning-transparent text-warning">
                                        <i class="fe fe-circle me-1"></i>Unread
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="meta-item mb-3">
                            <label class="fw-semibold text-muted">Received:</label>
                            <div class="mt-1">
                                <small class="text-muted">{{ $notification->created_at->format('F j, Y \a\t g:i A') }}</small>
                                <br>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        @if($notification->read && $notification->read_at)
                            <div class="meta-item mb-3">
                                <label class="fw-semibold text-muted">Read:</label>
                                <div class="mt-1">
                                    <small class="text-muted">{{ $notification->read_at->format('F j, Y \a\t g:i A') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $notification->read_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif

                        @if($notification->expires_at)
                            <div class="meta-item mb-3">
                                <label class="fw-semibold text-muted">Expires:</label>
                                <div class="mt-1">
                                    <small class="text-muted">{{ $notification->expires_at->format('F j, Y \a\t g:i A') }}</small>
                                    <br>
                                    @if($notification->expires_at->isPast())
                                        <span class="badge bg-danger-transparent text-danger">
                                            <i class="fe fe-clock me-1"></i>Expired
                                        </span>
                                    @else
                                        <small class="text-success">{{ $notification->expires_at->diffForHumans() }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="meta-item mb-3">
                            <label class="fw-semibold text-muted">Notification ID:</label>
                            <div class="mt-1">
                                <code class="text-muted">#{{ $notification->id }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="notification-actions mt-4">
                        <h6 class="fw-semibold mb-2">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            @if(!$notification->read)
                                <button class="btn btn-outline-primary btn-sm" onclick="markAsRead()">
                                    <i class="fe fe-check me-2"></i>Mark as Read
                                </button>
                            @endif
                            @if($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="btn btn-outline-info btn-sm">
                                    <i class="fe fe-external-link me-2"></i>{{ $notification->action_text ?: 'Open Link' }}
                                </a>
                            @endif
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteNotification()">
                                <i class="fe fe-trash me-2"></i>Delete Notification
                            </button>
                            <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fe fe-list me-2"></i>All Notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Notifications -->
            @php
                $relatedNotifications = \App\Models\UserNotification::where('user_id', Auth::id())
                    ->where('id', '!=', $notification->id)
                    ->where('type', $notification->type)
                    ->latest()
                    ->limit(3)
                    ->get();
            @endphp

            @if($relatedNotifications->count() > 0)
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Related Notifications</div>
                    </div>
                    <div class="card-body">
                        @foreach($relatedNotifications as $related)
                            <div class="d-flex align-items-start mb-3">
                                <span class="avatar avatar-sm bg-{{ $related->type }}-transparent text-{{ $related->type }} me-2">
                                    <i class="{{ $related->icon ?? 'fe fe-bell' }} fs-12"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('user.notifications.show', $related->id) }}" 
                                       class="text-decoration-none">
                                        <h6 class="mb-1 fs-13">{{ Str::limit($related->title, 40) }}</h6>
                                    </a>
                                    <small class="text-muted">{{ $related->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function markAsRead() {
        fetch('{{ route("user.notifications.read", $notification->id) }}', {
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

    function deleteNotification() {
        if (!confirm('Are you sure you want to delete this notification? This action cannot be undone.')) return;

        fetch('{{ route("user.notifications.delete", $notification->id) }}', {
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
                setTimeout(() => window.location.href = '{{ route("user.notifications.index") }}', 1000);
            } else {
                showToast('Failed to delete notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function showToast(message, type = 'info') {
        // Create toast notification
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fe fe-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush

@endsection
</x-smart_layout>
