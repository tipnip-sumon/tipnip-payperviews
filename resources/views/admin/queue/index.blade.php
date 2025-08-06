@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">📋 Queue Management</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" onclick="startQueueWorker()">
                        <i class="fe fe-play me-1"></i> Start Worker
                    </button>
                    <button class="btn btn-sm btn-info" onclick="checkWorkerStatus()">
                        <i class="fe fe-activity me-1"></i> Check Status
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="refreshPage()">
                        <i class="fe fe-refresh-cw me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(isset($error))
                    <div class="alert alert-danger">
                        <i class="fe fe-alert-triangle me-2"></i>
                        {{ $error }}
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fe fe-clock text-info" style="font-size: 2rem;"></i>
                                <h4 class="mt-2" id="pending-count">{{ $pendingJobs }}</h4>
                                <p class="text-muted">Pending Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="fe fe-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h4 class="mt-2" id="failed-count">{{ $failedJobs }}</h4>
                                <p class="text-muted">Failed Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fe fe-check-circle text-success" style="font-size: 2rem;"></i>
                                <h4 class="mt-2" id="processed-count">{{ $processedToday }}</h4>
                                <p class="text-muted">Processed Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fe fe-cpu text-primary" style="font-size: 2rem;"></i>
                                <h4 class="mt-2" id="worker-count">
                                    @if($workerStatus['active'])
                                        <span class="text-success">{{ count($workerStatus['workers']) }}</span>
                                    @else
                                        <span class="text-danger">0</span>
                                    @endif
                                </h4>
                                <p class="text-muted">Active Workers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Worker Status -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Worker Status</h5>
                            </div>
                            <div class="card-body">
                                @if($workerStatus['active'] && count($workerStatus['workers']) > 0)
                                    <div class="alert alert-success">
                                        <i class="fe fe-check-circle me-2"></i>
                                        <strong>{{ count($workerStatus['workers']) }} worker(s) are currently running</strong>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>PID</th>
                                                    @if(PHP_OS_FAMILY !== 'Windows')
                                                        <th>User</th>
                                                    @endif
                                                    <th>Command</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($workerStatus['workers'] as $worker)
                                                    <tr>
                                                        <td><code>{{ $worker['pid'] }}</code></td>
                                                        @if(PHP_OS_FAMILY !== 'Windows')
                                                            <td>{{ $worker['user'] ?? 'N/A' }}</td>
                                                        @endif
                                                        <td><small>{{ $worker['command'] ?? 'queue:work' }}</small></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fe fe-alert-triangle me-2"></i>
                                        <strong>No queue workers are currently running</strong>
                                        <p class="mb-0 mt-2">Queue jobs will not be processed automatically. Consider starting a worker.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-success w-100 mb-2" onclick="startQueueWorker()">
                                            <i class="fe fe-play me-2"></i>
                                            Start Worker
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="processQueueOnce()">
                                            <i class="fe fe-skip-forward me-2"></i>
                                            Process Once
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-warning w-100 mb-2" onclick="retryFailedJobs()">
                                            <i class="fe fe-refresh-ccw me-2"></i>
                                            Retry Failed
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-danger w-100 mb-2" onclick="clearFailedJobs()">
                                            <i class="fe fe-trash-2 me-2"></i>
                                            Clear Failed
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Pending Jobs</h5>
                            </div>
                            <div class="card-body">
                                @if(count($recentJobs) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Job Name</th>
                                                    <th>Queue</th>
                                                    <th>Attempts</th>
                                                    <th>Created</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentJobs as $job)
                                                    <tr>
                                                        <td><code>{{ $job['id'] }}</code></td>
                                                        <td>{{ $job['name'] }}</td>
                                                        <td>
                                                            <span class="badge badge-info">{{ $job['queue'] }}</span>
                                                        </td>
                                                        <td>
                                                            @if($job['attempts'] > 0)
                                                                <span class="badge badge-warning">{{ $job['attempts'] }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">0</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">{{ $job['created_at'] }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fe fe-inbox text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No pending jobs in the queue.</p>
                                        <small class="text-muted">Jobs will appear here when added to the queue.</small>
                                    </div>
                                @endif
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
function startQueueWorker() {
    Swal.fire({
        title: 'Start Queue Worker?',
        text: "This will start a new queue worker process in the background.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, start worker!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.queue.start-worker") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire('Success!', response.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to start worker', 'error');
            }
        }
    });
}

function checkWorkerStatus() {
    fetch('{{ route("admin.queue.worker-status") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.workers && data.workers.length > 0) {
            Swal.fire({
                title: 'Queue Worker Status',
                html: `
                    <div class="text-start">
                        <p><strong>Active Workers:</strong> ${data.workers.length}</p>
                        <p><strong>Pending Jobs:</strong> ${data.pending_jobs || 0}</p>
                        <p><strong>Failed Jobs:</strong> ${data.failed_jobs || 0}</p>
                        <p><strong>Processed Today:</strong> ${data.processed_today || 0}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
            
            // Update counts
            updateCounts(data);
        } else {
            Swal.fire({
                title: 'No Active Workers',
                text: 'Queue workers are not running. Consider starting them manually.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Start Worker',
                cancelButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    startQueueWorker();
                }
            });
        }
    })
    .catch(error => {
        console.error('Error checking worker status:', error);
        Swal.fire('Error!', 'Failed to check queue worker status', 'error');
    });
}

function processQueueOnce() {
    Swal.fire({
        title: 'Process Queue Once?',
        text: "This will process all pending jobs once and then stop.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        confirmButtonText: 'Yes, process jobs!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.system-commands.execute") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    command: 'queue:work --stop-when-empty',
                    confirm: 1
                })
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire('Success!', 'Queue processed successfully', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to process queue', 'error');
            }
        }
    });
}

function retryFailedJobs() {
    const failedCount = parseInt(document.getElementById('failed-count').textContent);
    
    if (failedCount === 0) {
        Swal.fire('No Failed Jobs', 'There are no failed jobs to retry.', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Retry Failed Jobs?',
        text: `This will retry all ${failedCount} failed jobs.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'Yes, retry all!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.failed-jobs.retry-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire('Success!', response.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to retry jobs', 'error');
            }
        }
    });
}

function clearFailedJobs() {
    const failedCount = parseInt(document.getElementById('failed-count').textContent);
    
    if (failedCount === 0) {
        Swal.fire('No Failed Jobs', 'There are no failed jobs to clear.', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Clear All Failed Jobs?',
        text: `This will permanently delete all ${failedCount} failed jobs. This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, clear all!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.failed-jobs.clear-all") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire('Cleared!', response.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to clear jobs', 'error');
            }
        }
    });
}

function updateCounts(data) {
    document.getElementById('pending-count').textContent = data.pending_jobs || 0;
    document.getElementById('failed-count').textContent = data.failed_jobs || 0;
    document.getElementById('processed-count').textContent = data.processed_today || 0;
    document.getElementById('worker-count').innerHTML = data.workers.length > 0 
        ? `<span class="text-success">${data.workers.length}</span>`
        : `<span class="text-danger">0</span>`;
}

function refreshPage() {
    location.reload();
}

// Auto-refresh every 30 seconds
setInterval(() => {
    fetch('{{ route("admin.queue.counts") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateCounts(data);
    })
    .catch(console.error);
}, 30000);
</script>
@endpush
