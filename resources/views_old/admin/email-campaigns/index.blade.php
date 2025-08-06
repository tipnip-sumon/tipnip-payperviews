@extends('components.layout')

@section('page-title', $pageTitle)

@section('breadcrumb')
<div class="page-header d-sm-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ $pageTitle }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fe fe-home me-2"></i>Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Email Campaigns</li>
        </ol>
    </div>
    <div class="page-rightheader ml-md-auto">
        <div class="btn-list">
            <a href="{{ route('admin.email-campaigns.analytics') }}" class="btn btn-outline-info">
                <i class="fe fe-bar-chart-2 me-2"></i>Analytics
            </a>
            <a href="{{ route('admin.email-campaigns.templates') }}" class="btn btn-outline-secondary">
                <i class="fe fe-file-text me-2"></i>Templates
            </a>
            <a href="{{ route('admin.email-campaigns.queue') }}" class="btn btn-outline-warning">
                <i class="fe fe-list me-2"></i>Queue
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="refreshStats(true)">
                <i class="fe fe-refresh-cw me-2"></i>Refresh Stats
            </button>
            <a href="{{ route('admin.settings.test-email') }}" class="btn btn-primary" onclick="openTestEmailModal(); return false;">
                <i class="fe fe-send me-2"></i>Test Email
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="counter-status d-flex align-items-center justify-content-between">
                    <div>
                        <span class="counter-icon bg-warning-transparent">
                            <i class="fe fe-credit-card text-warning"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="kyc-pending-count">{{ $stats['kyc_pending'] }}</h5>
                        <p class="mb-0 text-muted">KYC Pending</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="counter-status d-flex align-items-center justify-content-between">
                    <div>
                        <span class="counter-icon bg-danger-transparent">
                            <i class="fe fe-user-x text-danger"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="inactive-users-count">{{ $stats['inactive_users'] }}</h5>
                        <p class="mb-0 text-muted">Inactive Users</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="counter-status d-flex align-items-center justify-content-between">
                    <div>
                        <span class="counter-icon bg-info-transparent">
                            <i class="fe fe-lock text-info"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="password-reset-count">{{ $stats['password_reset_due'] }}</h5>
                        <p class="mb-0 text-muted">Password Reset Due</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="counter-status d-flex align-items-center justify-content-between">
                    <div>
                        <span class="counter-icon bg-success-transparent">
                            <i class="fe fe-users text-success"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="total-users-count">{{ $stats['total_active_users'] }}</h5>
                        <p class="mb-0 text-muted">Total Active Users</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Queue Status -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Email Queue Status</h4>
                <div class="card-options">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshQueueStatus()">
                        <i class="fe fe-refresh-cw"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="counter-icon bg-primary-transparent">
                                    <i class="fe fe-clock text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">Pending Jobs</h6>
                                <h4 class="mb-0" id="queue-pending-count">{{ $stats['queue_pending'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="counter-icon bg-danger-transparent">
                                    <i class="fe fe-x-circle text-danger"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">Failed Jobs</h6>
                                <h4 class="mb-0" id="queue-failed-count">{{ $stats['queue_failed'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($stats['queue_failed'] > 0)
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-warning" onclick="retryFailedJobs()">
                        <i class="fe fe-refresh-cw me-2"></i>Retry Failed Jobs
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Campaign Actions -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Campaign Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-outline-warning" onclick="sendKycReminders()" {{ $stats['kyc_pending'] == 0 ? 'disabled' : '' }}>
                        <i class="fe fe-credit-card me-2"></i>
                        Send KYC Reminders ({{ $stats['kyc_pending'] }})
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger" onclick="sendInactiveReminders()" {{ $stats['inactive_users'] == 0 ? 'disabled' : '' }}>
                        <i class="fe fe-user-x me-2"></i>
                        Send Inactive User Reminders ({{ $stats['inactive_users'] }})
                    </button>
                    
                    <button type="button" class="btn btn-outline-info" onclick="sendPasswordResets()" {{ $stats['password_reset_due'] == 0 ? 'disabled' : '' }}>
                        <i class="fe fe-lock me-2"></i>
                        Send Password Reset Reminders ({{ $stats['password_reset_due'] }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Email Campaign Instructions</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fe fe-info me-2"></i>How Email Campaigns Work:</h6>
                    <ul class="mb-0">
                        <li><strong>KYC Pending Reminders:</strong> Sends emails to users who haven't completed KYC verification</li>
                        <li><strong>Inactive User Reminders:</strong> Targets users who deposited but haven't invested in 15+ days</li>
                        <li><strong>Password Reset Reminders:</strong> Prompts users to change passwords older than 30 days</li>
                        <li><strong>Queue System:</strong> All emails are processed through a queue for reliable delivery</li>
                        <li><strong>Failed Job Recovery:</strong> Automatically retry failed email jobs with one click</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <h6><i class="fe fe-check-circle me-2"></i>Email Configuration Status:</h6>
                    <p class="mb-2">✅ SMTP Configuration: Active (Mailtrap)</p>
                    <p class="mb-2">✅ Queue Driver: Database</p>
                    <p class="mb-2">✅ Email Templates: 4 Professional Templates Ready</p>
                    <p class="mb-0">✅ Auto-Processing: Investment congratulations sent automatically</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJsScripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Email Campaign Dashboard loaded');
    
    // Auto-refresh stats every 30 seconds (without notification)
    setInterval(() => refreshStats(false), 30000);
});

function refreshStats(showMessage = false) {
    console.log('Refreshing email campaign stats...');
    
    fetch('{{ route("admin.email-campaigns.index") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.stats) {
            document.getElementById('kyc-pending-count').textContent = data.stats.kyc_pending;
            document.getElementById('inactive-users-count').textContent = data.stats.inactive_users;
            document.getElementById('password-reset-count').textContent = data.stats.password_reset_due;
            document.getElementById('total-users-count').textContent = data.stats.total_active_users;
            
            refreshQueueStatus();
            
            // Only show notification for manual refresh
            if (showMessage) {
                showNotification('Stats refreshed successfully!', 'success');
            }
        }
    })
    .catch(error => {
        console.error('Error refreshing stats:', error);
        if (showMessage) {
            showNotification('Failed to refresh stats', 'error');
        }
    });
}

function refreshQueueStatus() {
    fetch('{{ route("admin.email-campaigns.queue-status") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('queue-pending-count').textContent = data.pending || 0;
        document.getElementById('queue-failed-count').textContent = data.failed || 0;
    })
    .catch(error => {
        console.error('Error refreshing queue status:', error);
    });
}

function sendKycReminders() {
    if (!confirm('Send KYC reminder emails to all pending users?')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader fa-spin me-2"></i>Sending...';
    button.disabled = true;
    
    fetch('{{ route("admin.email-campaigns.send-kyc-reminders") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            refreshStats();
        } else {
            showNotification(data.message || 'Failed to send emails', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to send KYC reminders', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function sendInactiveReminders() {
    if (!confirm('Send reminder emails to all inactive users?')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader fa-spin me-2"></i>Sending...';
    button.disabled = true;
    
    fetch('{{ route("admin.email-campaigns.send-inactive-reminders") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            refreshStats();
        } else {
            showNotification(data.message || 'Failed to send emails', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to send inactive user reminders', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function sendPasswordResets() {
    if (!confirm('Send password reset reminders to all due users?')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader fa-spin me-2"></i>Sending...';
    button.disabled = true;
    
    fetch('{{ route("admin.email-campaigns.send-password-resets") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            refreshStats();
        } else {
            showNotification(data.message || 'Failed to send emails', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to send password reset reminders', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function retryFailedJobs() {
    if (!confirm('Retry all failed email jobs?')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-loader fa-spin me-2"></i>Retrying...';
    button.disabled = true;
    
    fetch('{{ route("admin.email-campaigns.retry-failed") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            refreshQueueStatus();
        } else {
            showNotification(data.message || 'Failed to retry jobs', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to retry failed jobs', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            icon: type,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}
</script>
@endsection
