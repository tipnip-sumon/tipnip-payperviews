@extends('components.layout')

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">🕒 Schedule Management</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-primary" onclick="runScheduleNow()">
                        <i class="fe fe-play me-1"></i> Run Schedule Now
                    </button>
                    <button class="btn btn-sm btn-info" onclick="refreshPage()">
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
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fe fe-clock text-primary" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $pendingJobs }}</h4>
                                <p class="text-muted">Pending Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="fe fe-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $failedJobs }}</h4>
                                <p class="text-muted">Failed Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fe fe-activity text-info" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">
                                    @if($cronStatus === 'active')
                                        <span class="text-success">Active</span>
                                    @elseif($cronStatus === 'delayed')
                                        <span class="text-warning">Delayed</span>
                                    @elseif($cronStatus === 'inactive')
                                        <span class="text-danger">Inactive</span>
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </h4>
                                <p class="text-muted">Cron Status</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fe fe-check-circle text-success" style="font-size: 2rem;"></i>
                                <h4 class="mt-2" id="last-run-time">
                                    {{ cache('last_schedule_run') ? cache('last_schedule_run')->diffForHumans() : 'Never' }}
                                </h4>
                                <p class="text-muted">Last Run</p>
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
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="runSpecificCommand('schedule:run')">
                                            <i class="fe fe-play-circle me-2"></i>
                                            Run Schedule
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-info w-100 mb-2" onclick="runSpecificCommand('queue:work --stop-when-empty')">
                                            <i class="fe fe-list me-2"></i>
                                            Process Queue
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-warning w-100 mb-2" onclick="runSpecificCommand('queue:retry all')">
                                            <i class="fe fe-refresh-ccw me-2"></i>
                                            Retry Failed Jobs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Scheduled Tasks -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Scheduled Tasks</h5>
                            </div>
                            <div class="card-body">
                                @if(count($recentTasks) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Timestamp</th>
                                                    <th>Status</th>
                                                    <th>Message</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentTasks as $task)
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted">{{ $task['timestamp'] }}</small>
                                                        </td>
                                                        <td>
                                                            @if($task['status'] === 'completed')
                                                                <span class="badge badge-success">Completed</span>
                                                            @else
                                                                <span class="badge badge-danger">Failed</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>{{ $task['message'] }}</small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fe fe-clock text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No recent scheduled tasks found.</p>
                                        <small class="text-muted">Run the scheduler to see task history here.</small>
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
function runScheduleNow() {
    Swal.fire({
        title: 'Run Schedule Now?',
        text: "This will manually trigger the Laravel scheduler to run all scheduled tasks.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, run schedule!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("admin.schedule.run") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const response = result.value;
            if (response.success) {
                Swal.fire({
                    title: 'Schedule Executed!',
                    html: `
                        <div class="text-start">
                            <p><strong>Tasks Run:</strong> ${response.tasks_run || 0}</p>
                            <p><strong>Duration:</strong> ${response.duration || 'N/A'}</p>
                            <p><strong>Memory Used:</strong> ${response.memory_used || 'N/A'}</p>
                            <p><strong>Exit Code:</strong> ${response.exit_code || 0}</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Great!'
                }).then(() => {
                    // Refresh the page to show updated data
                    location.reload();
                });
            } else {
                Swal.fire('Error!', response.message || 'Failed to run schedule', 'error');
            }
        }
    });
}

function runSpecificCommand(command) {
    Swal.fire({
        title: 'Run Command?',
        text: `Execute: ${command}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, run it!',
        cancelButtonText: 'Cancel',
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
                    command: command,
                    confirm: 1
                })
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
                Swal.fire('Success!', response.message || 'Command executed successfully', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', response.message || 'Command execution failed', 'error');
            }
        }
    });
}

function refreshPage() {
    location.reload();
}

// Auto-refresh every 30 seconds
setInterval(() => {
    // Update last run time
    fetch('{{ route("admin.schedule.index") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Update specific elements without full page reload
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update statistics if elements exist
        const lastRunElement = document.getElementById('last-run-time');
        const newLastRun = doc.getElementById('last-run-time');
        if (lastRunElement && newLastRun) {
            lastRunElement.innerHTML = newLastRun.innerHTML;
        }
    })
    .catch(console.error);
}, 30000);
</script>
@endpush
