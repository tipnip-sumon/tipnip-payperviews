<x-smart_layout>
    <x-slot name="title">Session Management Dashboard</x-slot>
@section('content')
<div class="content-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Session Management</h1>
                        <p class="text-muted">Monitor and control your account sessions across different devices</p>
                    </div>
                    <div>
                        <a href="{{ route('user.sessions.security') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Security Settings
                        </a>
                    </div>
                </div>

                <!-- Session Notifications Alert -->
                @if(session('session_notification'))
                    <div class="alert alert-{{ session('session_notification.type') }} alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div class="flex-grow-1">
                                <strong>{{ session('session_notification.title') }}</strong>
                                <p class="mb-0">{{ session('session_notification.message') }}</p>
                            </div>
                            @if(session('session_notification.action_url'))
                                <a href="{{ session('session_notification.action_url') }}" class="btn btn-sm btn-outline-{{ session('session_notification.type') }}">
                                    {{ session('session_notification.action_text') }}
                                </a>
                            @endif
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Notifications
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $stats['total_notifications'] }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Unread Notifications
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $stats['unread_notifications'] }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Recent Logins (7 days)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $stats['recent_logins'] }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Trusted IPs
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $stats['trusted_ips_count'] }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Active Sessions</h6>
                                <button class="btn btn-sm btn-danger" onclick="terminateOtherSessions()">
                                    <i class="fas fa-sign-out-alt me-1"></i>Terminate Other Sessions
                                </button>
                            </div>
                            <div class="card-body">
                                @forelse($activeSessions as $session)
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 border rounded {{ $session['is_current'] ? 'bg-light border-primary' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-{{ $session['device'] === 'Mobile Device' ? 'mobile-alt' : ($session['device'] === 'Tablet' ? 'tablet-alt' : 'desktop') }} fa-2x text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">
                                                    {{ $session['device'] }}
                                                    @if($session['is_current'])
                                                        <span class="badge bg-primary ms-2">Current Session</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    IP: {{ $session['ip'] }} • Last Activity: {{ $session['last_activity']->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        @if(!$session['is_current'])
                                            <button class="btn btn-sm btn-outline-danger" onclick="terminateSession('{{ $session['id'] }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-laptop fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Only your current session is active</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Recent Notifications -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                                <a href="{{ route('user.sessions.notifications') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @forelse($notifications->take(5) as $notification)
                                    <div class="d-flex justify-content-between align-items-start mb-3 p-2 rounded {{ $notification->color_class }}">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <i class="{{ $notification->icon }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 small">{{ $notification->title }}</h6>
                                                <p class="mb-0 small text-muted">{{ Str::limit($notification->message, 80) }}</p>
                                                <small class="text-muted">{{ $notification->time_ago }}</small>
                                            </div>
                                        </div>
                                        @if(!$notification->is_read)
                                            <span class="badge bg-warning">New</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-3">
                                        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small">No recent notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('user.sessions.notifications', ['mark_as_read' => 1]) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-check me-2"></i>Mark All as Read
                                    </a>
                                    <a href="{{ route('user.sessions.security') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-cog me-2"></i>Security Settings
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm" onclick="clearNotifications()">
                                        <i class="fas fa-trash me-2"></i>Clear Read Notifications
                                    </button>
                                </div>
                            </div>
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
function terminateSession(sessionId) {
    if (confirm('Are you sure you want to terminate this session?')) {
        fetch('{{ route("user.sessions.terminate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ session_id: sessionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to terminate session');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred while terminating the session');
        });
    }
}

function terminateOtherSessions() {
    if (confirm('Are you sure you want to terminate all other sessions? This will log out all other devices.')) {
        fetch('{{ route("user.sessions.terminate-others") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to terminate sessions');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred while terminating sessions');
        });
    }
}

function clearNotifications() {
    if (confirm('Are you sure you want to clear all read notifications?')) {
        fetch('{{ route("user.sessions.notifications.clear") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ type: 'read' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `${data.deleted_count} notifications cleared`);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', 'Failed to clear notifications');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred while clearing notifications');
        });
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.content-area .container-fluid').insertBefore(alertDiv, document.querySelector('.content-area .container-fluid').firstChild);
}
</script>
@endpush
</x-smart_layout>
