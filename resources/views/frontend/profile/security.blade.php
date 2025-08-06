<x-smart_layout>
    @section('top_title', $pageTitle ?? 'Security Settings')
    @section('title', $pageTitle ?? 'Security Settings')
    @section('content')
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Error Messages -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Security Overview -->
        <div class="row mb-4 my-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-shield-alt"></i> Security Settings
                                </h4>
                                <p class="mb-0">Manage your account security and privacy settings</p>
                                <small class="text-light">Keep your account secure with these settings</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <h5 class="mb-1">Account Status</h5>
                                    @if($user->hasVerifiedEmail())
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-exclamation-triangle"></i> Unverified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Status Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card {{ $user->hasVerifiedEmail() ? 'bg-success' : 'bg-warning' }} text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Email Status</p>
                                <h5 class="text-white mb-0">
                                    {{ $user->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}
                                </h5>
                            </div>
                            <i class="fas fa-{{ $user->hasVerifiedEmail() ? 'check-circle' : 'exclamation-triangle' }} fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Account Age</p>
                                <h5 class="text-white mb-0">
                                    {{ $user->created_at ? $user->created_at->diffForHumans() : 'Unknown' }}
                                </h5>
                            </div>
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Last Login</p>
                                <h5 class="text-white mb-0">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </h5>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Security Score</p>
                                <h5 class="text-white mb-0">
                                    @php
                                        $score = 0;
                                        if($user->hasVerifiedEmail()) $score += 50;
                                        if($user->phone || $user->mobile) $score += 25;
                                        if($user->avatar) $score += 25;
                                    @endphp
                                    {{ $score }}%
                                </h5>
                            </div>
                            <i class="fas fa-shield-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="row">
            <!-- Change Password -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-key text-warning"></i> Change Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Password must be at least 8 characters long
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="form-control" 
                                       required>
                            </div>
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Email Verification -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-envelope text-info"></i> Email Verification
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($user->hasVerifiedEmail())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Your email address is verified!
                            </div>
                            <p class="text-muted">
                                Your email <strong>{{ $user->email }}</strong> has been verified on 
                                {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'N/A' }}
                            </p>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Your email address is not verified!
                            </div>
                            <p class="text-muted mb-3">
                                Please verify your email address <strong>{{ $user->email }}</strong> to secure your account.
                            </p>
                            <a href="{{ route('verification.notice') }}" class="btn btn-info">
                                <i class="fas fa-envelope"></i> Verify Email
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-primary"></i> Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Username:</td>
                                        <td>{{ $user->username ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Email:</td>
                                        <td>
                                            {{ $user->email ?? 'Not set' }}
                                            @if($user->hasVerifiedEmail())
                                                <span class="badge bg-success ms-2">Verified</span>
                                            @else
                                                <span class="badge bg-warning ms-2">Unverified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Phone:</td>
                                        <td>{{ $user->phone ?? $user->mobile ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Country:</td>
                                        <td>{{ $user->country ?? 'Not set' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Account Created:</td>
                                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Last Updated:</td>
                                        <td>{{ $user->updated_at ? $user->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Last Login:</td>
                                        <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status:</td>
                                        <td>
                                            @if($user->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb"></i> Security Tips
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Password Security</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i> Use a strong, unique password</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Include uppercase and lowercase letters</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Add numbers and special characters</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Avoid personal information</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Account Security</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i> Verify your email address</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Add a phone number</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Keep your profile updated</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Log out from shared devices</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog text-primary"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-grid">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit Profile
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-grid">
                                    <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                        <i class="fas fa-key"></i> Change Password
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-grid">
                                    @if(!$user->hasVerifiedEmail())
                                        <a href="{{ route('verification.notice') }}" class="btn btn-outline-info">
                                            <i class="fas fa-envelope"></i> Verify Email
                                        </a>
                                    @else
                                        <a href="{{ route('profile.index') }}" class="btn btn-outline-success">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-grid">
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-home"></i> Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
    <style>
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-bottom: 1px solid #eee;
            background: transparent;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn {
            border-radius: 6px;
        }
        
        .table td {
            padding: 0.5rem 0;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
        
        .list-unstyled li {
            margin-bottom: 0.5rem;
        }
        
        .card.border-info {
            border: 2px solid #17a2b8 !important;
        }
        
        .card-header.bg-info {
            background-color: #17a2b8 !important;
        }
    </style>
    @endpush

    @push('script')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').addClass('fade-out');
                setTimeout(function() {
                    $('.alert').remove();
                }, 500);
            }, 5000);
            
            // Close alert manually
            $('.alert .btn-close').on('click', function() {
                $(this).closest('.alert').addClass('fade-out');
                setTimeout(() => {
                    $(this).closest('.alert').remove();
                }, 500);
            });
            
            // Password strength indicator
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                updatePasswordStrength(strength);
            });
            
            // Password confirmation validation
            $('#password_confirmation').on('input', function() {
                const password = $('#password').val();
                const confirmation = $(this).val();
                
                if (password !== confirmation && confirmation !== '') {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
        });
        
        function calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score += 25;
            if (password.match(/[a-z]/)) score += 25;
            if (password.match(/[A-Z]/)) score += 25;
            if (password.match(/[0-9]/)) score += 25;
            if (password.match(/[^a-zA-Z0-9]/)) score += 25;
            
            return Math.min(score, 100);
        }
        
        function updatePasswordStrength(strength) {
            // You can add password strength indicator here
            console.log('Password strength:', strength + '%');
        }
    </script>
    @endpush
</x-smart_layout>