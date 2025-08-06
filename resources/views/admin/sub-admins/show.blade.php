@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user text-primary me-2"></i>
                Sub-Admin Details
            </h1>
            <p class="text-muted mb-0">{{ $subAdmin->name }} ({{ $subAdmin->username }})</p>
        </div>
        <div>
            <a href="{{ route('admin.sub-admins.edit', $subAdmin->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i>
                Edit Sub-Admin
            </a>
            <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Sub-Admins
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    @if($subAdmin->image)
                        <img src="{{ asset('storage/' . $subAdmin->image) }}" 
                             alt="Profile Image" class="rounded-circle mb-3" width="120" height="120">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 120px; height: 120px;">
                            <span class="text-white" style="font-size: 3rem; font-weight: bold;">
                                {{ strtoupper(substr($subAdmin->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h4 class="mb-1">{{ $subAdmin->name }}</h4>
                    <p class="text-muted mb-2">{{ $subAdmin->username }}</p>
                    
                    <span class="badge bg-{{ $subAdmin->role == 'manager' ? 'primary' : ($subAdmin->role == 'moderator' ? 'info' : ($subAdmin->role == 'support' ? 'success' : 'secondary')) }} mb-2">
                        {{ ucfirst($subAdmin->role) }}
                    </span>
                    
                    <br>
                    
                    @if($subAdmin->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Account Age:</span>
                            <strong>{{ $stats['account_age'] }}</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Last Login:</span>
                            <strong>
                                @if($stats['last_login'])
                                    {{ $stats['last_login']->format('M j, Y g:i A') }}
                                @else
                                    Never
                                @endif
                            </strong>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Login Attempts:</span>
                            <strong>{{ $stats['login_attempts'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-{{ $subAdmin->is_active ? 'warning' : 'success' }}" 
                                onclick="toggleStatus({{ $subAdmin->id }})">
                            <i class="fas fa-{{ $subAdmin->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $subAdmin->is_active ? 'Deactivate' : 'Activate' }} Account
                        </button>
                        
                        <button type="button" class="btn btn-info" onclick="resetPassword({{ $subAdmin->id }})">
                            <i class="fas fa-key me-1"></i>
                            Reset Password
                        </button>
                        
                        <button type="button" class="btn btn-danger" onclick="deleteSubAdmin({{ $subAdmin->id }})">
                            <i class="fas fa-trash me-1"></i>
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Basic Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Full Name:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Username:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->username }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Email:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->email }}</div>
                    </div>
                    
                    @if($subAdmin->phone)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Phone:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->phone }}</div>
                    </div>
                    @endif
                    
                    @if($subAdmin->address)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Address:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->address }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Role:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $subAdmin->role == 'manager' ? 'primary' : ($subAdmin->role == 'moderator' ? 'info' : ($subAdmin->role == 'support' ? 'success' : 'secondary')) }}">
                                {{ ucfirst($subAdmin->role) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-0">
                        <div class="col-sm-3"><strong>Status:</strong></div>
                        <div class="col-sm-9">
                            @if($subAdmin->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-2"></i>
                        Permissions
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $permissions = json_decode($subAdmin->permissions, true) ?? [];
                        $allPermissions = [
                            'users' => [
                                'users.view' => 'View Users',
                                'users.create' => 'Create Users',
                                'users.edit' => 'Edit Users',
                                'users.delete' => 'Delete Users',
                                'users.ban' => 'Ban/Unban Users',
                            ],
                            'deposits' => [
                                'deposits.view' => 'View Deposits',
                                'deposits.approve' => 'Approve Deposits',
                                'deposits.reject' => 'Reject Deposits',
                                'deposits.export' => 'Export Deposits',
                            ],
                            'withdrawals' => [
                                'withdrawals.view' => 'View Withdrawals',
                                'withdrawals.approve' => 'Approve Withdrawals',
                                'withdrawals.reject' => 'Reject Withdrawals',
                                'withdrawals.export' => 'Export Withdrawals',
                            ],
                            'support' => [
                                'support.view' => 'View Support Tickets',
                                'support.reply' => 'Reply to Tickets',
                                'support.close' => 'Close Tickets',
                                'support.assign' => 'Assign Tickets',
                            ],
                            'content' => [
                                'content.videos' => 'Manage Videos',
                                'content.popups' => 'Manage Popups',
                                'content.notifications' => 'Manage Notifications',
                            ],
                            'settings' => [
                                'settings.general' => 'General Settings',
                                'settings.security' => 'Security Settings',
                                'settings.mail' => 'Mail Configuration',
                            ],
                            'reports' => [
                                'reports.view' => 'View Reports',
                                'reports.export' => 'Export Reports',
                                'reports.analytics' => 'View Analytics',
                            ],
                        ];
                    @endphp
                    
                    @if(count($permissions) > 0)
                        <div class="row">
                            @foreach($allPermissions as $category => $categoryPermissions)
                                @php
                                    $categoryHasPermissions = false;
                                    foreach($categoryPermissions as $permKey => $permName) {
                                        if(in_array($permKey, $permissions)) {
                                            $categoryHasPermissions = true;
                                            break;
                                        }
                                    }
                                @endphp
                                
                                @if($categoryHasPermissions)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary text-capitalize">{{ str_replace('_', ' ', $category) }}</h6>
                                        @foreach($categoryPermissions as $permKey => $permName)
                                            @if(in_array($permKey, $permissions))
                                                <div class="mb-1">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <span>{{ $permName }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>No specific permissions assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar me-2"></i>
                        Account Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Created:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->created_at->format('F j, Y g:i A') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Last Updated:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->updated_at->format('F j, Y g:i A') }}</div>
                    </div>
                    
                    @if($subAdmin->last_login_at)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Last Login:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->last_login_at->format('F j, Y g:i A') }}</div>
                    </div>
                    @endif
                    
                    @if($subAdmin->last_login_ip)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Last Login IP:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->last_login_ip }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-0">
                        <div class="col-sm-3"><strong>Login Attempts:</strong></div>
                        <div class="col-sm-9">{{ $subAdmin->login_attempts ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($subAdmin->notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sticky-note me-2"></i>
                        Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $subAdmin->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

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
