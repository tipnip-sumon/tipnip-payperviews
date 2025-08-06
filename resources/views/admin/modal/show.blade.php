@extends('components.layout')

@section('page-title', 'Modal Details')

@section('style')
<style>
    .detail-card {
        border-left: 4px solid #007bff;
    }
    .json-viewer {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
@php
    $additionalSettings = json_decode($modalSetting->additional_settings ?? '{}', true);
@endphp

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $modalSetting->title }}</h2>
                    <p class="text-muted">Modal Details and Configuration</p>
                </div>
                <div>
                    <a href="{{ route('admin.modal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.modal.edit', $modalSetting->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Modal Name</h6>
                            <p class="text-muted"><code>{{ $modalSetting->modal_name }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Title</h6>
                            <p class="text-muted">{{ $modalSetting->title }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Subtitle</h6>
                            <p class="text-muted">{{ $modalSetting->subtitle ?: 'Not set' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Heading</h6>
                            <p class="text-muted">{{ $modalSetting->heading ?: 'Not set' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6>Description</h6>
                            <p class="text-muted">{{ $modalSetting->description ?: 'No description provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Targeting & Frequency -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bullseye"></i> Targeting & Frequency</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h6>Target Users</h6>
                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $modalSetting->target_users)) }}</span>
                        </div>
                        <div class="col-md-3">
                            <h6>Show Frequency</h6>
                            <span class="badge bg-info">{{ ucfirst($modalSetting->show_frequency) }}</span>
                        </div>
                        <div class="col-md-3">
                            <h6>Maximum Shows</h6>
                            <p class="text-muted">{{ $modalSetting->max_shows }} times</p>
                        </div>
                        <div class="col-md-3">
                            <h6>Delay</h6>
                            <p class="text-muted">{{ $modalSetting->delay_seconds }} seconds</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Settings -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Advanced Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Device Targeting</h6>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-{{ ($additionalSettings['show_on_mobile_only'] ?? false) ? 'check text-success' : 'times text-danger' }}"></i>
                                    Mobile Only
                                </li>
                                <li>
                                    <i class="fas fa-{{ ($additionalSettings['show_on_desktop_only'] ?? false) ? 'check text-success' : 'times text-danger' }}"></i>
                                    Desktop Only
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Session Settings</h6>
                            <p class="text-muted">
                                <strong>Minimum Session Time:</strong> {{ $additionalSettings['minimum_session_time'] ?? 30 }} seconds
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Exclude Routes</h6>
                            @if(!empty($additionalSettings['exclude_routes']))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($additionalSettings['exclude_routes'] as $route)
                                        <span class="badge bg-danger">{{ $route }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">None specified</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Include Routes</h6>
                            @if(!empty($additionalSettings['include_routes']))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($additionalSettings['include_routes'] as $route)
                                        <span class="badge bg-success">{{ $route }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">All routes (default)</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Code -->
            @if(!empty($additionalSettings['custom_css']) || !empty($additionalSettings['custom_js']))
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-code"></i> Custom Code</h5>
                </div>
                <div class="card-body">
                    @if(!empty($additionalSettings['custom_css']))
                    <div class="mb-3">
                        <h6>Custom CSS</h6>
                        <pre class="json-viewer"><code>{{ $additionalSettings['custom_css'] }}</code></pre>
                    </div>
                    @endif
                    
                    @if(!empty($additionalSettings['custom_js']))
                    <div class="mb-3">
                        <h6>Custom JavaScript</h6>
                        <pre class="json-viewer"><code>{{ $additionalSettings['custom_js'] }}</code></pre>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Raw JSON Configuration -->
            <div class="card detail-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-code"></i> Raw Configuration</h5>
                </div>
                <div class="card-body">
                    <pre class="json-viewer"><code>{{ json_encode($additionalSettings, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Active Status</span>
                        <span class="badge {{ $modalSetting->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ $modalSetting->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <div class="mb-2">
                            <strong>ID:</strong> {{ $modalSetting->id }}
                        </div>
                        <div class="mb-2">
                            <strong>Created:</strong> {{ $modalSetting->created_at }}
                        </div>
                        <div class="mb-2">
                            <strong>Updated:</strong> {{ $modalSetting->updated_at }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-{{ $modalSetting->is_active ? 'warning' : 'success' }}" 
                                onclick="toggleStatus()">
                            <i class="fas fa-{{ $modalSetting->is_active ? 'pause' : 'play' }}"></i>
                            {{ $modalSetting->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <a href="{{ route('admin.modal.edit', $modalSetting->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Modal
                        </a>
                        <button class="btn btn-outline-info" onclick="testModal()">
                            <i class="fas fa-vial"></i> Test Modal
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteModal()">
                            <i class="fas fa-trash"></i> Delete Modal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Preview</h6>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3" style="background: #f8f9fa;">
                        <h6 class="text-primary">{{ $modalSetting->title }}</h6>
                        @if($modalSetting->subtitle)
                        <p class="text-muted small mb-2">{{ $modalSetting->subtitle }}</p>
                        @endif
                        @if($modalSetting->description)
                        <p class="small">{{ Str::limit($modalSetting->description, 100) }}</p>
                        @endif
                        <div class="d-flex gap-1 mt-2">
                            <span class="badge bg-primary small">{{ $modalSetting->show_frequency }}</span>
                            <span class="badge bg-secondary small">{{ $modalSetting->target_users }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this modal? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.modal.destroy', $modalSetting->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function toggleStatus() {
    fetch(`/admin/modals/{{ $modalSetting->id }}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function testModal() {
    // This would trigger a test display of the modal
    alert('Test modal functionality - This would show the modal with current settings on the frontend.');
}

function deleteModal() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
