@extends('components.layout')

@section('content')
    <div class="row my-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Permissions Management
                    </h6>
                    <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Sub-Admins
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Permission Categories -->
                        <div class="col-lg-8">
                            <h5 class="mb-3">Available Permissions</h5>
                            
                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-capitalize">
                                            <i class="fas fa-{{ $category == 'users' ? 'users' : ($category == 'deposits' ? 'credit-card' : ($category == 'withdrawals' ? 'money-bill-wave' : ($category == 'support' ? 'headset' : ($category == 'content' ? 'file-alt' : ($category == 'settings' ? 'cogs' : 'chart-bar'))))) }} me-2"></i>
                                            {{ ucfirst($category) }} Management
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($categoryPermissions as $key => $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" disabled id="{{ $key }}">
                                                        <label class="form-check-label" for="{{ $key }}">
                                                            {{ $permission }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Sub-Admins List -->
                        <div class="col-lg-4">
                            <h5 class="mb-3">Sub-Admins & Their Permissions</h5>
                            
                            @if($subAdmins->count() > 0)
                                @foreach($subAdmins as $subAdmin)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($subAdmin->image)
                                                    <img src="{{ asset('storage/' . $subAdmin->image) }}" alt="{{ $subAdmin->name }}" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $subAdmin->name }}</h6>
                                                    <small class="text-muted">{{ ucfirst($subAdmin->role) }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <span class="badge badge--{{ $subAdmin->is_active ? 'success' : 'danger' }}">
                                                    {{ $subAdmin->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>

                                            @php
                                                $userPermissions = $subAdmin->permissions ? json_decode($subAdmin->permissions, true) : [];
                                                $permissionCount = is_array($userPermissions) ? count($userPermissions) : 0;
                                            @endphp

                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-shield-alt me-1"></i>
                                                    {{ $permissionCount }} Permissions
                                                </small>
                                            </div>

                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.sub-admins.edit', $subAdmin->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.sub-admins.show', $subAdmin->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>

                                            @if(is_array($userPermissions) && count($userPermissions) > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted d-block mb-1">Current Permissions:</small>
                                                    <div class="permission-tags">
                                                        @foreach($userPermissions as $permission)
                                                            @php
                                                                $permissionLabel = '';
                                                                foreach($permissions as $cat => $catPerms) {
                                                                    if(isset($catPerms[$permission])) {
                                                                        $permissionLabel = $catPerms[$permission];
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($permissionLabel)
                                                                <span class="badge bg-light text-dark mb-1" style="font-size: 0.7rem;">
                                                                    {{ $permissionLabel }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users-slash text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h6 class="text-muted">No Sub-Admins Found</h6>
                                        <p class="text-muted mb-3">Create sub-admins to manage permissions.</p>
                                        <a href="{{ route('admin.sub-admins.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Create Sub-Admin
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Permissions Guide -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Permission Management Guide
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-shield-alt text-primary me-2"></i>How Permissions Work:</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Sub-admins inherit permissions based on their assigned role</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Individual permissions can be customized per sub-admin</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Permissions control access to specific admin panel sections</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Super admins have access to all permissions</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-users-cog text-info me-2"></i>Managing Sub-Admins:</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-edit text-primary me-2"></i>Edit sub-admin to modify permissions</li>
                                                <li><i class="fas fa-toggle-on text-success me-2"></i>Toggle status to activate/deactivate accounts</li>
                                                <li><i class="fas fa-key text-warning me-2"></i>Reset passwords when needed</li>
                                                <li><i class="fas fa-eye text-info me-2"></i>View detailed sub-admin information</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .permission-tags .badge {
            margin: 1px;
            font-size: 0.7rem;
        }
        
        .card-header h6 {
            font-weight: 600;
        }
        
        .form-check-input:disabled {
            opacity: 0.7;
        }
        
        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection
