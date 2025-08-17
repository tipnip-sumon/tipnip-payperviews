<x-smart_layout>
    <x-slot name="title">Active Sessions</x-slot>

@section('content')

<div class="content-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Active Sessions</h1>
                        <p class="text-muted">Manage your active sessions across different devices and browsers</p>
                    </div>
                    <div>
                        <a href="{{ route('user.sessions.dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                        <button class="btn btn-warning" onclick="terminateOtherSessions()">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout Other Sessions
                        </button>
                    </div>
                </div>

                <!-- Current Session Alert -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Current Session</strong>
                            <p class="mb-0">You are currently logged in from this device. This session cannot be terminated from here.</p>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions List -->
                @if(count($activeSessions) > 0)
                    <div class="row">
                        @foreach($activeSessions as $index => $session)
                            <div class="col-md-6 mb-4">
                                <div class="card {{ $session['is_current'] ? 'border-primary' : 'border-secondary' }} h-100">
                                    <div class="card-header {{ $session['is_current'] ? 'bg-primary text-white' : 'bg-light' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                @if($session['is_current'])
                                                    <i class="fas fa-desktop me-2"></i>Current Session
                                                @else
                                                    <i class="fas fa-laptop me-2"></i>Session #{{ $index + 1 }}
                                                @endif
                                            </h6>
                                            @if($session['is_current'])
                                                <span class="badge badge-light">Active Now</span>
                                            @else
                                                <span class="badge badge-{{ $session['is_active'] ? 'success' : 'secondary' }}">
                                                    {{ $session['is_active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <strong class="text-muted">Device:</strong>
                                                <br>{{ $session['device'] ?? 'Unknown Device' }}
                                            </div>
                                            <div class="col-6">
                                                <strong class="text-muted">Browser:</strong>
                                                <br>{{ $session['browser'] ?? 'Unknown Browser' }}
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <strong class="text-muted">IP Address:</strong>
                                                <br>
                                                <code>{{ $session['ip_address'] ?? 'Unknown' }}</code>
                                            </div>
                                            <div class="col-6">
                                                <strong class="text-muted">Location:</strong>
                                                <br>{{ $session['location'] ?? 'Unknown Location' }}
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <strong class="text-muted">Last Activity:</strong>
                                                <br>{{ $session['last_activity'] ?? 'Unknown' }}
                                            </div>
                                            <div class="col-6">
                                                <strong class="text-muted">Login Time:</strong>
                                                <br>{{ $session['login_time'] ?? 'Unknown' }}
                                            </div>
                                        </div>
                                        
                                        @if(!$session['is_current'])
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-danger" onclick="terminateSession('{{ $session['session_id'] ?? '' }}')">
                                                    <i class="fas fa-sign-out-alt me-1"></i>Terminate Session
                                                </button>
                                                @if($session['is_trusted'] ?? false)
                                                    <span class="badge badge-success ms-2">
                                                        <i class="fas fa-shield-alt me-1"></i>Trusted
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Active Sessions -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-laptop fa-3x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Active Sessions</h4>
                        <p class="text-muted mb-4">You currently have no active sessions from other devices.</p>
                        <a href="{{ route('user.sessions.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                @endif

                <!-- Recent Login Activities -->
                @if($recentLogins->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Login Activities</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Device</th>
                                            <th>IP Address</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentLogins->take(10) as $login)
                                            <tr>
                                                <td>{{ $login->created_at->format('M d, Y H:i:s') }}</td>
                                                <td>{{ $login->device_info ?? 'Unknown' }}</td>
                                                <td><code>{{ $login->ip_address ?? 'Unknown' }}</code></td>
                                                <td>{{ $login->location ?? 'Unknown' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $login->type === 'success' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($login->type) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.border-primary {
    border-color: #4e73df !important;
    border-width: 2px !important;
}

.badge {
    font-size: 0.75rem;
}

code {
    font-size: 0.875rem;
    background-color: #f8f9fc;
    padding: 2px 6px;
    border-radius: 3px;
}
</style>
@endpush
@push('script')
<script>
function terminateSession(sessionId) {
    if (confirm('Are you sure you want to terminate this session? The user will be logged out from that device.')) {
        fetch(`{{ route('user.sessions.terminate') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                session_id: sessionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Session terminated successfully');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to terminate session');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while terminating the session');
        });
    }
}

function terminateOtherSessions() {
    if (confirm('Are you sure you want to logout from all other devices? This will terminate all sessions except your current one.')) {
        fetch(`{{ route('user.sessions.terminate-others') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'All other sessions terminated successfully');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to terminate other sessions');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while terminating other sessions');
        });
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.content-area .container-fluid').insertBefore(alertDiv, document.querySelector('.content-area .container-fluid').firstChild);
}
</script>
@endpush
</x-smart_layout>
