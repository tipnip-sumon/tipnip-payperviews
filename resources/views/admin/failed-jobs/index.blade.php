@extends('components.layout')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">❌ Failed Jobs Management</h4>
                <div class="d-flex gap-2">
                    @if($stats['total_failed'] > 0)
                        <button class="btn btn-sm btn-warning" onclick="retryAllJobs()">
                            <i class="fe fe-refresh-ccw me-1"></i> Retry All
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="clearAllJobs()">
                            <i class="fe fe-trash-2 me-1"></i> Clear All
                        </button>
                    @endif
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
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="fe fe-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $stats['total_failed'] }}</h4>
                                <p class="text-muted">Total Failed Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fe fe-calendar text-warning" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $stats['today_failed'] }}</h4>
                                <p class="text-muted">Failed Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fe fe-clock text-info" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $stats['week_failed'] }}</h4>
                                <p class="text-muted">Failed This Week</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Failed Jobs List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Failed Jobs</h5>
                            </div>
                            <div class="card-body">
                                @if($failedJobs->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Job Name</th>
                                                    <th>Queue</th>
                                                    <th>Error</th>
                                                    <th>Failed At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($failedJobs as $job)
                                                    <tr>
                                                        <td><code>{{ $job->id }}</code></td>
                                                        <td>
                                                            <strong>{{ $job->job_name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $job->connection }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-secondary">{{ $job->queue ?: 'default' }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="error-message" style="max-width: 300px;">
                                                                <small class="text-danger">{{ Str::limit($job->error_message, 100) }}</small>
                                                                @if(strlen($job->error_message) > 100)
                                                                    <br>
                                                                    <a href="javascript:void(0)" onclick="showFullError({{ $job->id }}, `{{ addslashes($job->error_message) }}`)" class="text-info">
                                                                        <small>Show full error</small>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($job->failed_at)->format('M d, Y H:i') }}
                                                                <br>
                                                                ({{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }})
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-warning" onclick="retryJob({{ $job->id }})" title="Retry Job">
                                                                    <i class="fe fe-refresh-ccw"></i>
                                                                </button>
                                                                <button class="btn btn-outline-info" onclick="showJobDetails({{ $job->id }})" title="View Details">
                                                                    <i class="fe fe-eye"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger" onclick="deleteJob({{ $job->id }})" title="Delete Job">
                                                                    <i class="fe fe-trash-2"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $failedJobs->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fe fe-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3 text-success">No Failed Jobs!</h4>
                                        <p class="text-muted">All jobs are processing successfully. Great work!</p>
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

<!-- Job Details Modal -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="jobDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
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
function retryJob(jobId) {
    Swal.fire({
        title: 'Retry Job?',
        text: `Retry failed job #${jobId}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'Yes, retry!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`{{ url('admin/failed-jobs/retry') }}/${jobId}`, {
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
                Swal.fire('Success!', 'Job retried successfully', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to retry job', 'error');
            }
        }
    });
}

function deleteJob(jobId) {
    Swal.fire({
        title: 'Delete Job?',
        text: `Permanently delete failed job #${jobId}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`{{ url('admin/failed-jobs/delete') }}/${jobId}`, {
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
                Swal.fire('Deleted!', 'Job deleted successfully', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Failed to delete job', 'error');
            }
        }
    });
}

function retryAllJobs() {
    const totalFailed = {{ $stats['total_failed'] }};
    
    if (totalFailed === 0) {
        Swal.fire('No Failed Jobs', 'There are no failed jobs to retry.', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Retry All Failed Jobs?',
        text: `This will retry all ${totalFailed} failed jobs.`,
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

function clearAllJobs() {
    const totalFailed = {{ $stats['total_failed'] }};
    
    if (totalFailed === 0) {
        Swal.fire('No Failed Jobs', 'There are no failed jobs to clear.', 'info');
        return;
    }
    
    Swal.fire({
        title: 'Clear All Failed Jobs?',
        text: `This will permanently delete all ${totalFailed} failed jobs. This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, clear all!',
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

function showJobDetails(jobId) {
    // Find the job data from the current page
    const jobData = @json($failedJobs->items());
    const job = jobData.find(j => j.id == jobId);
    
    if (!job) {
        Swal.fire('Error', 'Job details not found', 'error');
        return;
    }
    
    const content = `
        <div class="row">
            <div class="col-12">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>ID:</strong></td><td>${job.id}</td></tr>
                    <tr><td><strong>UUID:</strong></td><td><code>${job.uuid}</code></td></tr>
                    <tr><td><strong>Job Name:</strong></td><td>${job.job_name}</td></tr>
                    <tr><td><strong>Queue:</strong></td><td>${job.queue || 'default'}</td></tr>
                    <tr><td><strong>Connection:</strong></td><td>${job.connection}</td></tr>
                    <tr><td><strong>Failed At:</strong></td><td>${job.failed_at}</td></tr>
                </table>
                
                <h6 class="mt-3">Error Details</h6>
                <div class="bg-light p-3 rounded">
                    <pre class="text-danger" style="white-space: pre-wrap; font-size: 12px;">${job.error_message}</pre>
                </div>
                
                <h6 class="mt-3">Job Payload</h6>
                <div class="bg-light p-3 rounded">
                    <pre style="white-space: pre-wrap; font-size: 11px;">${JSON.stringify(job.payload, null, 2)}</pre>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('jobDetailsContent').innerHTML = content;
    
    const modal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
    modal.show();
}

function showFullError(jobId, errorMessage) {
    Swal.fire({
        title: `Full Error - Job #${jobId}`,
        html: `<pre class="text-start text-danger" style="white-space: pre-wrap; font-size: 12px; max-height: 400px; overflow-y: auto;">${errorMessage}</pre>`,
        icon: 'error',
        showCloseButton: true,
        showConfirmButton: false,
        width: '80%'
    });
}

function refreshPage() {
    location.reload();
}
</script>
@endpush
