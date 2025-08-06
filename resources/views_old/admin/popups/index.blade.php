@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-window-restore text-primary me-2"></i>
                Popup Management
            </h1>
            <p class="text-muted mb-0">Create and manage beautiful popups for your users</p>
        </div>
        <a href="{{ route('admin.popups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Create New Popup
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Popups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $popups->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-window-restore fa-2x text-gray-300"></i>
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
                                Active Popups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $popups->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Total Views</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $popups->sum('view_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                Total Clicks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $popups->sum('click_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popups Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Popups</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?filter=active">Active Only</a></li>
                    <li><a class="dropdown-item" href="?filter=inactive">Inactive Only</a></li>
                    <li><a class="dropdown-item" href="?">All Popups</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if($popups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Clicks</th>
                                <th>CTR</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($popups as $popup)
                            <tr>
                                <td>{{ $popup->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($popup->image)
                                            <img src="{{ $popup->image_url }}" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ Str::limit($popup->title, 30) }}</strong>
                                            @if($popup->content)
                                                <br><small class="text-muted">{{ Str::limit(strip_tags($popup->content), 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $popup->type === 'promotion' ? 'success' : ($popup->type === 'warning' ? 'warning' : ($popup->type === 'announcement' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($popup->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst($popup->size) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $popup->priority }}</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               data-id="{{ $popup->id }}" {{ $popup->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label text-{{ $popup->is_active ? 'success' : 'danger' }}">
                                            {{ $popup->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-eye text-info me-1"></i>
                                    {{ number_format($popup->view_count) }}
                                </td>
                                <td>
                                    <i class="fas fa-mouse-pointer text-warning me-1"></i>
                                    {{ number_format($popup->click_count) }}
                                </td>
                                <td>
                                    @php
                                        $ctr = $popup->view_count > 0 ? round(($popup->click_count / $popup->view_count) * 100, 2) : 0;
                                    @endphp
                                    <span class="text-{{ $ctr > 5 ? 'success' : ($ctr > 2 ? 'warning' : 'danger') }}">
                                        {{ $ctr }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.popups.show', $popup) }}" class="btn btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.popups.preview', $popup) }}" class="btn btn-outline-success" title="Preview" target="_blank">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="{{ route('admin.popups.edit', $popup) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.popups.duplicate', $popup) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary" title="Duplicate">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.popups.destroy', $popup) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this popup?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $popups->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-window-restore fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Popups Found</h5>
                    <p class="text-muted">Create your first popup to engage with your users!</p>
                    <a href="{{ route('admin.popups.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Create Your First Popup
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('script')
<script>
$(document).ready(function() {
    // Handle status toggle
    $('.status-toggle').change(function() {
        const popupId = $(this).data('id');
        const isActive = $(this).is(':checked');
        const label = $(this).next('label');
        
        $.ajax({
            url: `/admin/popups/${popupId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    label.removeClass('text-success text-danger')
                         .addClass(response.status ? 'text-success' : 'text-danger')
                         .text(response.status ? 'Active' : 'Inactive');
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                // Revert toggle
                $(this).prop('checked', !isActive);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update popup status',
                });
            }
        });
    });
});
</script>
@endpush
@endsection
