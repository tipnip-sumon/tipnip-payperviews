<x-smart_layout>
    <x-slot name="title">Session Notifications</x-slot>


@section('content')
<div class="content-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Session Notifications</h1>
                        <p class="text-muted">View your recent login activities and security alerts</p>
                    </div>
                    <div>
                        <a href="{{ route('user.sessions.dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <button class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check me-2"></i>Mark All Read
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                @if($notifications->count() > 0)
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Session Activities</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($notifications as $notification)
                                    <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="icon-circle {{ $notification->type === 'warning' ? 'bg-warning' : 'bg-info' }}">
                                                    <i class="fas {{ $notification->type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle' }} text-white"></i>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 {{ !$notification->is_read ? 'font-weight-bold' : '' }}">
                                                            {{ $notification->title }}
                                                        </h6>
                                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                        @if($notification->ip_address)
                                                            <small class="text-muted ms-3">
                                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                                IP: {{ $notification->ip_address }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if(!$notification->is_read)
                                                                <li>
                                                                    <a class="dropdown-item" href="#" onclick="markAsRead({{ $notification->id }})">
                                                                        <i class="fas fa-check me-2"></i>Mark as Read
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification({{ $notification->id }})">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        @if($notifications->hasPages())
                            <div class="card-footer">
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-bell fa-3x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Notifications</h4>
                        <p class="text-muted mb-4">You don't have any session notifications at the moment.</p>
                        <a href="{{ route('user.sessions.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-group-item:hover {
    background-color: #f8f9fc !important;
}

.bg-light {
    background-color: #f8f9fc !important;
    border-left: 4px solid #4e73df;
}
</style>
@endpush
@push('script')
<script>
function markAsRead(notificationId) {
    fetch(`{{ url('user/sessions/notifications') }}/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to mark notification as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function markAllAsRead() {
    fetch(`{{ route('user.sessions.notifications.mark-all-read') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to mark all notifications as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`{{ url('user/sessions/notifications') }}/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete notification');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
@endpush
</x-smart_layout>
