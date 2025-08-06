@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users-cog text-primary me-2"></i>
                Sub-Admin Management
            </h1>
            <p class="text-muted mb-0">Manage sub-administrators and their permissions</p>
        </div>
        <a href="{{ route('admin.sub-admins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Create New Sub-Admin
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sub-Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Sub-Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inactive Sub-Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>
                Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sub-admins.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, email, or username...">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="moderator" {{ request('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                        <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support Staff</option>
                        <option value="accountant" {{ request('role') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Content Editor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub-Admins Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>
                Sub-Administrators List
            </h6>
        </div>
        <div class="card-body">
            @if($subAdmins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subAdmins as $subAdmin)
                            <tr>
                                <td class="text-center">
                                    @if($subAdmin->image)
                                        <img src="{{ asset('storage/' . $subAdmin->image) }}" 
                                             alt="Avatar" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <span class="text-white fw-bold">
                                                {{ strtoupper(substr($subAdmin->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $subAdmin->name }}</strong>
                                    @if($subAdmin->phone)
                                        <br><small class="text-muted">{{ $subAdmin->phone }}</small>
                                    @endif
                                </td>
                                <td>{{ $subAdmin->username }}</td>
                                <td>{{ $subAdmin->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $subAdmin->role == 'manager' ? 'primary' : ($subAdmin->role == 'moderator' ? 'info' : ($subAdmin->role == 'support' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst($subAdmin->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($subAdmin->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subAdmin->last_login_at)
                                        <small>{{ $subAdmin->last_login_at->format('M j, Y') }}</small><br>
                                        <small class="text-muted">{{ $subAdmin->last_login_at->format('g:i A') }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $subAdmin->created_at->format('M j, Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.sub-admins.show', $subAdmin->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sub-admins.edit', $subAdmin->id) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-{{ $subAdmin->is_active ? 'warning' : 'success' }}" 
                                                onclick="toggleStatus({{ $subAdmin->id }})" 
                                                title="{{ $subAdmin->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $subAdmin->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="resetPassword({{ $subAdmin->id }})" title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteSubAdmin({{ $subAdmin->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $subAdmins->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Sub-Admins Found</h5>
                    <p class="text-muted mb-4">There are no sub-administrators matching your criteria.</p>
                    <a href="{{ route('admin.sub-admins.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Create First Sub-Admin
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        @if(session('new_password'))
            <br><strong>New Password:</strong> <code>{{ session('new_password') }}</code>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<script>
function toggleStatus(id) {
    if (confirm('Are you sure you want to change this sub-admin\'s status?')) {
        window.location.href = '{{ route("admin.sub-admins.toggle-status", ":id") }}'.replace(':id', id);
    }
}

function resetPassword(id) {
    if (confirm('Are you sure you want to reset this sub-admin\'s password? A new password will be generated.')) {
        window.location.href = '{{ route("admin.sub-admins.reset-password", ":id") }}'.replace(':id', id);
    }
}

function deleteSubAdmin(id) {
    if (confirm('Are you sure you want to delete this sub-admin? This action cannot be undone.')) {
        // Create and submit a form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.sub-admins.destroy", ":id") }}'.replace(':id', id);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
