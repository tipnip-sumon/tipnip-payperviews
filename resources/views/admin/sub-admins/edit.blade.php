@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-edit text-primary me-2"></i>
                Edit Sub-Admin
            </h1>
            <p class="text-muted mb-0">Update {{ $subAdmin->name }}'s information</p>
        </div>
        <div>
            <a href="{{ route('admin.sub-admins.show', $subAdmin->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-1"></i>
                View Details
            </a>
            <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Sub-Admins
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.sub-admins.update', $subAdmin->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user me-2"></i>
                            Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $subAdmin->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username', $subAdmin->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $subAdmin->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $subAdmin->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address', $subAdmin->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            @if($subAdmin->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $subAdmin->image) }}" 
                                         alt="Current Image" class="rounded" width="80" height="80">
                                    <small class="text-muted d-block">Current image</small>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty to keep current image. Maximum file size: 2MB.</div>
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-lock me-2"></i>
                            Account Security
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields empty to keep current password.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-shield-alt me-2"></i>
                            Role & Permissions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $roleKey => $roleName)
                                    <option value="{{ $roleKey }}" {{ old('role', $subAdmin->role) == $roleKey ? 'selected' : '' }}>
                                        {{ $roleName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            @php
                                $currentPermissions = json_decode($subAdmin->permissions, true) ?? [];
                            @endphp
                            <div class="row">
                                @foreach($permissions as $category => $categoryPermissions)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary text-capitalize">{{ str_replace('_', ' ', $category) }}</h6>
                                        @foreach($categoryPermissions as $permKey => $permName)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_{{ $permKey }}" name="permissions[]" value="{{ $permKey }}"
                                                       {{ (is_array(old('permissions')) && in_array($permKey, old('permissions'))) || (!old('permissions') && in_array($permKey, $currentPermissions)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permKey }}">
                                                    {{ $permName }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-sticky-note me-2"></i>
                            Additional Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any additional notes about this sub-admin...">{{ old('notes', $subAdmin->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sub-admins.show', $subAdmin->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Sub-Admin
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Current Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Current Information
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($subAdmin->image)
                        <img src="{{ asset('storage/' . $subAdmin->image) }}" 
                             alt="Profile Image" class="rounded-circle mb-3" width="100" height="100">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <span class="text-white" style="font-size: 2rem; font-weight: bold;">
                                {{ strtoupper(substr($subAdmin->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    
                    <h5 class="mb-1">{{ $subAdmin->name }}</h5>
                    <p class="text-muted mb-2">{{ $subAdmin->username }}</p>
                    <p class="text-muted mb-2">{{ $subAdmin->email }}</p>
                    
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-clock me-2"></i>
                        Account Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <small class="text-muted">{{ $subAdmin->created_at->format('F j, Y g:i A') }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        <small class="text-muted">{{ $subAdmin->updated_at->format('F j, Y g:i A') }}</small>
                    </div>
                    
                    @if($subAdmin->last_login_at)
                    <div class="mb-0">
                        <strong>Last Login:</strong><br>
                        <small class="text-muted">{{ $subAdmin->last_login_at->format('F j, Y g:i A') }}</small>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Username changes require careful consideration
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Email changes will affect login credentials
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Permission changes take effect immediately
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Password changes will force re-login
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Role-based permission suggestions
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    
    // Don't auto-change permissions on edit unless specifically requested
    if (confirm('Do you want to automatically set permissions based on the selected role? This will override current permissions.')) {
        // Clear all permissions first
        checkboxes.forEach(cb => cb.checked = false);
        
        // Set permissions based on role
        const rolePermissions = {
            'manager': ['users.view', 'users.edit', 'deposits.view', 'deposits.approve', 'withdrawals.view', 'withdrawals.approve', 'support.view', 'support.reply', 'reports.view'],
            'moderator': ['users.view', 'users.edit', 'users.ban', 'content.videos', 'content.popups', 'support.view', 'support.reply'],
            'support': ['users.view', 'support.view', 'support.reply', 'support.close', 'support.assign'],
            'accountant': ['deposits.view', 'deposits.approve', 'deposits.reject', 'withdrawals.view', 'withdrawals.approve', 'withdrawals.reject', 'reports.view'],
            'editor': ['content.videos', 'content.popups', 'content.notifications', 'users.view']
        };
        
        if (rolePermissions[role]) {
            rolePermissions[role].forEach(permission => {
                const checkbox = document.getElementById('perm_' + permission);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }
});
</script>
@endsection
