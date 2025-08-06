@extends('components.layout')

@section('page-title', 'Modal Management')

@section('style')
<style>
    .modal-card {
        transition: transform 0.2s;
        border-left: 4px solid #dee2e6;
    }
    .modal-card.active {
        border-left-color: #28a745;
    }
    .modal-card.inactive {
        border-left-color: #dc3545;
    }
    .modal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .frequency-badge {
        background: #e9ecef;
        color: #495057;
    }
    .target-badge {
        background: #f8f9fa;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Modal Management</h2>
                    <p class="text-muted">Manage website modals and popup settings</p>
                </div>
                <div>
                    <a href="{{ route('admin.modal.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Modal
                    </a>
                    <a href="{{ route('admin.modal.analytics') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Total Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-window-restore fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Active Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->where('is_active', 1)->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Inactive Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->where('is_active', 0)->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-pause-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">PWA Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->where('modal_name', 'like', '%install%')->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-mobile-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Modal Settings</h5>
        </div>
        <div class="card-body">
            @if($modalSettings->count() > 0)
                <div class="row">
                    @foreach($modalSettings as $modal)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card modal-card {{ $modal->is_active ? 'active' : 'inactive' }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $modal->title }}</h6>
                                    <div class="d-flex gap-2">
                                        <span class="badge status-badge {{ $modal->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $modal->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-2">{{ $modal->subtitle }}</p>
                                    <p class="card-text small">{{ Str::limit($modal->description, 100) }}</p>
                                    
                                    <div class="mb-3">
                                        <span class="badge frequency-badge me-1">{{ ucfirst($modal->show_frequency) }}</span>
                                        <span class="badge target-badge">{{ ucfirst(str_replace('_', ' ', $modal->target_users)) }}</span>
                                    </div>
                                    
                                    <div class="small text-muted mb-3">
                                        <div><strong>Max Shows:</strong> {{ $modal->max_shows }}</div>
                                        <div><strong>Delay:</strong> {{ $modal->delay_seconds }}s</div>
                                        <div><strong>Modal Name:</strong> <code>{{ $modal->modal_name }}</code></div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('admin.modal.show', $modal->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.modal.edit', $modal->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-sm btn-outline-{{ $modal->is_active ? 'warning' : 'success' }}" 
                                                onclick="toggleModalStatus({{ $modal->id }})">
                                            <i class="fas fa-{{ $modal->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteModal({{ $modal->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-window-restore fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Modal Settings Found</h5>
                    <p class="text-muted">Create your first modal to get started.</p>
                    <a href="{{ route('admin.modal.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Modal
                    </a>
                </div>
            @endif
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
                <form id="deleteForm" method="POST" style="display: inline;">
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
function toggleModalStatus(modalId) {
    fetch(`/admin/modals/${modalId}/toggle-status`, {
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

function deleteModal(modalId) {
    document.getElementById('deleteForm').action = `/admin/modals/${modalId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Fix for missing admin menu functions
function showBrowserCacheManager() {
    Swal.fire({
        title: '🌐 Browser Cache Manager',
        html: `
            <div class="row g-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>ℹ️ Info:</strong> Clear browser cache for better performance.
                    </div>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-primary w-100" onclick="clearDomainCache()">
                        <i class="fas fa-globe"></i><br>Clear Domain Cache
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-warning w-100" onclick="clearAdvancedCache()">
                        <i class="fas fa-cogs"></i><br>Advanced Clear
                    </button>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-outline-danger w-100" onclick="clearAllCaches()">
                    <i class="fas fa-trash"></i> Clear All Caches
                </button>
            </div>
        `,
        showConfirmButton: false,
        showCancelButton: true,
        cancelButtonText: 'Close',
        width: '500px'
    });
}

function clearDomainCache() {
    window.location.href = '/browser_cache_clear/only_this_domain';
}

function clearAdvancedCache() {
    window.location.href = '/browser_cache_clear/advanced';
}

function clearAllCaches() {
    Swal.fire({
        title: 'Clearing All Caches...',
        html: 'Please wait while we clear all system caches.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        window.location.href = '/browser_cache_clear/advanced';
    }, 2000);
}
</script>
@endpush
