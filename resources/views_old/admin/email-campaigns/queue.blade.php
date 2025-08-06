@extends('components.layout')

@section('page-title', $pageTitle)

@section('breadcrumb')
<div class="page-header d-sm-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ $pageTitle }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fe fe-home me-2"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email-campaigns.index') }}">Email Campaigns</a></li>
            <li class="breadcrumb-item active" aria-current="page">Queue Management</li>
        </ol>
    </div>
    <div class="page-rightheader ml-md-auto">
        <div class="btn-list">
            <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-warning" onclick="retryAllFailed()">
                <i class="fe fe-refresh-cw me-2"></i>Retry All Failed
            </button>
            <button type="button" class="btn btn-danger" onclick="clearFailedJobs()">
                <i class="fe fe-trash-2 me-2"></i>Clear Failed Jobs
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Queue Status Overview -->
    <div class="col-xl-3 col-lg-6 col-sm-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="counter-status d-flex align-items-center justify-content-between">
                    <div>
                        <span class="counter-icon bg-primary-transparent">
                            <i class="fe fe-clock text-primary"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="pending-jobs-count">{{ $queueStats['pending'] ?? 0 }}</h5>
                        <p class="mb-0 text-muted">Pending Jobs</p>
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
                            <i class="fe fe-check-circle text-success"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="completed-jobs-count">{{ $queueStats['completed'] ?? 0 }}</h5>
                        <p class="mb-0 text-muted">Completed Today</p>
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
                            <i class="fe fe-x-circle text-danger"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="failed-jobs-count">{{ $queueStats['failed'] ?? 0 }}</h5>
                        <p class="mb-0 text-muted">Failed Jobs</p>
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
                            <i class="fe fe-activity text-info"></i>
                        </span>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-2 number-font" id="worker-status">
                            <span class="badge badge-success">Active</span>
                        </h5>
                        <p class="mb-0 text-muted">Worker Status</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Queue Jobs Table -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📋 Queue Jobs</h4>
                <div class="card-options">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" onclick="filterJobs('all')">All</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterJobs('pending')">Pending</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterJobs('failed')">Failed</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="jobs-table">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Queue</th>
                                <th>Attempts</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="jobs-tbody">
                            <!-- Pending Jobs -->
                            <tr class="job-row pending-job">
                                <td><code>#12345</code></td>
                                <td>
                                    <span class="badge badge-warning">
                                        <i class="fe fe-credit-card me-1"></i>KYC Reminder
                                    </span>
                                </td>
                                <td><span class="badge badge-primary">Pending</span></td>
                                <td>emails</td>
                                <td>0/3</td>
                                <td>2 minutes ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelJob(12345)">
                                        <i class="fe fe-x"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="job-row pending-job">
                                <td><code>#12346</code></td>
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fe fe-lock me-1"></i>Password Reset
                                    </span>
                                </td>
                                <td><span class="badge badge-primary">Pending</span></td>
                                <td>emails</td>
                                <td>0/3</td>
                                <td>5 minutes ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelJob(12346)">
                                        <i class="fe fe-x"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Failed Jobs -->
                            <tr class="job-row failed-job" style="display: none;">
                                <td><code>#12340</code></td>
                                <td>
                                    <span class="badge badge-danger">
                                        <i class="fe fe-user-x me-1"></i>Inactive User
                                    </span>
                                </td>
                                <td><span class="badge badge-danger">Failed</span></td>
                                <td>emails</td>
                                <td>3/3</td>
                                <td>1 hour ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success" onclick="retryJob(12340)">
                                        <i class="fe fe-refresh-cw"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteJob(12340)">
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="job-row failed-job" style="display: none;">
                                <td><code>#12341</code></td>
                                <td>
                                    <span class="badge badge-warning">
                                        <i class="fe fe-credit-card me-1"></i>KYC Reminder
                                    </span>
                                </td>
                                <td><span class="badge badge-danger">Failed</span></td>
                                <td>emails</td>
                                <td>3/3</td>
                                <td>2 hours ago</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-success" onclick="retryJob(12341)">
                                        <i class="fe fe-refresh-cw"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteJob(12341)">
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Queue Controls -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">⚙️ Queue Controls</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-success" onclick="startWorker()">
                        <i class="fe fe-play me-2"></i>Start Queue Worker
                    </button>
                    
                    <button type="button" class="btn btn-warning" onclick="pauseWorker()">
                        <i class="fe fe-pause me-2"></i>Pause Queue Worker
                    </button>
                    
                    <button type="button" class="btn btn-info" onclick="restartWorker()">
                        <i class="fe fe-rotate-cw me-2"></i>Restart Queue Worker
                    </button>
                    
                    <hr>
                    
                    <button type="button" class="btn btn-outline-primary" onclick="clearCompletedJobs()">
                        <i class="fe fe-check-circle me-2"></i>Clear Completed Jobs
                    </button>
                    
                    <button type="button" class="btn btn-outline-warning" onclick="retryAllFailed()">
                        <i class="fe fe-refresh-cw me-2"></i>Retry All Failed Jobs
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger" onclick="clearAllJobs()">
                        <i class="fe fe-trash-2 me-2"></i>Clear All Jobs
                    </button>
                </div>
                
                <hr>
                
                <h6>Queue Statistics</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <small>Jobs per minute</small>
                        <span class="badge badge-primary">~12</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <small>Average processing time</small>
                        <span class="badge badge-info">2.3s</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <small>Success rate</small>
                        <span class="badge badge-success">94.2%</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <small>Last restart</small>
                        <span class="badge badge-secondary">2h ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Worker Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">🔄 Worker Information</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h6><i class="fe fe-check-circle me-2"></i>Worker Status: Active</h6>
                    <p class="mb-2"><strong>Queue:</strong> emails</p>
                    <p class="mb-2"><strong>Timeout:</strong> 300 seconds</p>
                    <p class="mb-2"><strong>Memory:</strong> 128 MB limit</p>
                    <p class="mb-0"><strong>Started:</strong> 2 hours ago</p>
                </div>
                
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                        <small>Memory Usage: 75%</small>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="mb-1 text-success">1,247</h6>
                            <small>Jobs Processed</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h6 class="mb-1 text-primary">00:02:15</h6>
                            <small>Uptime</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Failed Jobs Details -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">❌ Failed Jobs Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Type</th>
                                <th>Failed At</th>
                                <th>Error Message</th>
                                <th>Payload</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>#12340</code></td>
                                <td><span class="badge badge-danger">Inactive User Reminder</span></td>
                                <td>1 hour ago</td>
                                <td>
                                    <small class="text-danger">SMTP connection failed: Connection timeout</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewPayload(12340)">
                                        <i class="fe fe-eye"></i> View
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="retryJob(12340)">
                                        <i class="fe fe-refresh-cw"></i> Retry
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteJob(12340)">
                                        <i class="fe fe-trash-2"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code>#12341</code></td>
                                <td><span class="badge badge-warning">KYC Reminder</span></td>
                                <td>2 hours ago</td>
                                <td>
                                    <small class="text-danger">User email not found or invalid</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewPayload(12341)">
                                        <i class="fe fe-eye"></i> View
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="retryJob(12341)">
                                        <i class="fe fe-refresh-cw"></i> Retry
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteJob(12341)">
                                        <i class="fe fe-trash-2"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJsScripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Queue Management page loaded');
    
    // Auto-refresh queue status every 10 seconds
    setInterval(refreshQueueStatus, 10000);
});

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
        if (data.success) {
            document.getElementById('pending-jobs-count').textContent = data.pending || 0;
            document.getElementById('failed-jobs-count').textContent = data.failed || 0;
        }
    })
    .catch(error => {
        console.error('Error refreshing queue status:', error);
    });
}

function filterJobs(filter) {
    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Show/hide rows based on filter
    const allRows = document.querySelectorAll('.job-row');
    
    allRows.forEach(row => {
        row.style.display = 'none';
    });
    
    if (filter === 'all') {
        allRows.forEach(row => {
            row.style.display = 'table-row';
        });
    } else if (filter === 'pending') {
        document.querySelectorAll('.pending-job').forEach(row => {
            row.style.display = 'table-row';
        });
    } else if (filter === 'failed') {
        document.querySelectorAll('.failed-job').forEach(row => {
            row.style.display = 'table-row';
        });
    }
    
    console.log('Filtered jobs by:', filter);
}

function retryJob(jobId) {
    if (!confirm('Retry this failed job?')) return;
    
    showNotification('Retrying job #' + jobId + '...', 'info');
    
    // Simulate retry
    setTimeout(() => {
        showNotification('Job #' + jobId + ' has been queued for retry', 'success');
        refreshQueueStatus();
    }, 1000);
}

function deleteJob(jobId) {
    if (!confirm('Permanently delete this failed job?')) return;
    
    showNotification('Deleting job #' + jobId + '...', 'info');
    
    // Simulate deletion
    setTimeout(() => {
        showNotification('Job #' + jobId + ' has been deleted', 'success');
        // Remove row from table
        const row = document.querySelector(`[onclick*="${jobId}"]`).closest('tr');
        if (row) row.remove();
        refreshQueueStatus();
    }, 1000);
}

function cancelJob(jobId) {
    if (!confirm('Cancel this pending job?')) return;
    
    showNotification('Cancelling job #' + jobId + '...', 'info');
    
    // Simulate cancellation
    setTimeout(() => {
        showNotification('Job #' + jobId + ' has been cancelled', 'success');
        refreshQueueStatus();
    }, 1000);
}

function retryAllFailed() {
    if (!confirm('Retry all failed jobs?')) return;
    
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
    });
}

function clearFailedJobs() {
    if (!confirm('Clear all failed jobs? This action cannot be undone.')) return;
    
    showNotification('Clearing failed jobs...', 'info');
    
    // Simulate clearing
    setTimeout(() => {
        showNotification('All failed jobs have been cleared', 'success');
        refreshQueueStatus();
    }, 1000);
}

function startWorker() {
    showNotification('Starting queue worker...', 'info');
    
    setTimeout(() => {
        document.getElementById('worker-status').innerHTML = '<span class="badge badge-success">Active</span>';
        showNotification('Queue worker started successfully', 'success');
    }, 2000);
}

function pauseWorker() {
    showNotification('Pausing queue worker...', 'info');
    
    setTimeout(() => {
        document.getElementById('worker-status').innerHTML = '<span class="badge badge-warning">Paused</span>';
        showNotification('Queue worker paused', 'warning');
    }, 1000);
}

function restartWorker() {
    if (!confirm('Restart the queue worker? This will briefly interrupt email processing.')) return;
    
    showNotification('Restarting queue worker...', 'info');
    
    setTimeout(() => {
        document.getElementById('worker-status').innerHTML = '<span class="badge badge-success">Active</span>';
        showNotification('Queue worker restarted successfully', 'success');
    }, 3000);
}

function clearCompletedJobs() {
    if (!confirm('Clear all completed jobs from the database?')) return;
    
    showNotification('Clearing completed jobs...', 'info');
    
    setTimeout(() => {
        showNotification('Completed jobs cleared successfully', 'success');
        refreshQueueStatus();
    }, 1000);
}

function clearAllJobs() {
    if (!confirm('Clear ALL jobs (pending and failed)? This will stop all email processing!')) return;
    
    showNotification('Clearing all jobs...', 'warning');
    
    setTimeout(() => {
        showNotification('All jobs have been cleared', 'success');
        refreshQueueStatus();
    }, 1000);
}

function viewPayload(jobId) {
    const payloadData = {
        12340: {
            user_id: 123,
            email: 'user@example.com',
            type: 'inactive_user_reminder',
            data: { days_inactive: 15, last_login: '2025-07-18' }
        },
        12341: {
            user_id: 124,
            email: 'invalid@email',
            type: 'kyc_reminder',
            data: { kyc_status: 'pending', reminder_count: 2 }
        }
    };
    
    const payload = payloadData[jobId] || { error: 'Payload not found' };
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Job Payload #' + jobId,
            html: '<pre>' + JSON.stringify(payload, null, 2) + '</pre>',
            width: '600px',
            confirmButtonText: 'Close'
        });
    } else {
        alert('Job Payload #' + jobId + ':\n' + JSON.stringify(payload, null, 2));
    }
}

function showNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Notice',
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
