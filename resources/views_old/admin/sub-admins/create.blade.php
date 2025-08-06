@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-plus text-primary me-2"></i>
                Create New Sub-Admin
            </h1>
            <p class="text-muted mb-0">Add a new sub-administrator to the system</p>
        </div>
        <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Sub-Admins
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.sub-admins.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum file size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</div>
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
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
                                    <option value="{{ $roleKey }}" {{ old('role') == $roleKey ? 'selected' : '' }}>
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
                            <div class="row">
                                @foreach($permissions as $category => $categoryPermissions)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary text-capitalize">{{ str_replace('_', ' ', $category) }}</h6>
                                        @foreach($categoryPermissions as $permKey => $permName)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_{{ $permKey }}" name="permissions[]" value="{{ $permKey }}"
                                                       {{ is_array(old('permissions')) && in_array($permKey, old('permissions')) ? 'checked' : '' }}>
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
                                      placeholder="Any additional notes about this sub-admin...">{{ old('notes') }}</textarea>
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
                            <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Sub-Admin
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Role Descriptions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Manager</h6>
                        <p class="text-muted small">Full administrative access except super admin functions.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-info">Moderator</h6>
                        <p class="text-muted small">Manages users, content, and handles moderation tasks.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-success">Support Staff</h6>
                        <p class="text-muted small">Handles customer support tickets and user issues.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-warning">Accountant</h6>
                        <p class="text-muted small">Manages financial transactions, deposits, and withdrawals.</p>
                    </div>
                    <div class="mb-0">
                        <h6 class="text-secondary">Content Editor</h6>
                        <p class="text-muted small">Manages website content, videos, and promotional materials.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Username must be unique and cannot be changed later
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Email address will be used for login and notifications
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Permissions can be modified after creation
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Sub-admin will receive login credentials via email
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
});

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    updatePasswordStrength(strength);
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    return strength;
}

function updatePasswordStrength(strength) {
    // Implementation for password strength indicator
    // You can add visual feedback here
}
</script>
@endsection
