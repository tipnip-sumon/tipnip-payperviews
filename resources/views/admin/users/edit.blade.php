<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->firstname }} {{ $user->lastname }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center my-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ri-user-settings-line me-2"></i>Edit User Information
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" class="form-control @error('firstname') is-invalid @enderror" 
                                           value="{{ old('firstname', $user->firstname) }}" required>
                                    @error('firstname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror" 
                                           value="{{ old('lastname', $user->lastname) }}" required>
                                    @error('lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                           value="{{ old('username', $user->username) }}" required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" 
                                           value="{{ old('mobile', $user->mobile) }}">
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" 
                                           value="{{ old('country', $user->country) }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Wallet Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Deposit Wallet</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="deposit_wallet" class="form-control @error('deposit_wallet') is-invalid @enderror" 
                                               value="{{ old('deposit_wallet', $user->deposit_wallet) }}" step="0.01" min="0">
                                        @error('deposit_wallet')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Interest Wallet</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="interest_wallet" class="form-control @error('interest_wallet') is-invalid @enderror" 
                                               value="{{ old('interest_wallet', $user->interest_wallet) }}" step="0.01" min="0">
                                        @error('interest_wallet')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Settings -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Account Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                        <option value="2" {{ old('status', $user->status) == 2 ? 'selected' : '' }}>Banned</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Verification Settings -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Verification Status</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="ev" value="1" 
                                                       {{ old('ev', $user->ev) ? 'checked' : '' }}>
                                                <label class="form-check-label">Email Verified</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="sv" value="1" 
                                                       {{ old('sv', $user->sv) ? 'checked' : '' }}>
                                                <label class="form-check-label">SMS Verified</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="kv" value="1" 
                                                       {{ old('kv', $user->kv) ? 'checked' : '' }}>
                                                <label class="form-check-label">KYC Verified</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Password Change -->
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fe fe-info me-2"></i>
                                    <strong>Password Change:</strong> Leave password fields empty if you don't want to change the password.
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-1"></i>Back to Details
                            </a>
                            <div>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fe fe-refresh-cw me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-save me-1"></i>Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('styles')
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Real-time validation feedback styles */
        .username-feedback, .email-feedback, .password-feedback {
            transition: all 0.3s ease;
        }
        
        .username-feedback small, .email-feedback small, .password-feedback small {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .validation-loading {
            color: #6c757d !important;
        }
        
        .validation-success {
            color: #198754 !important;
        }
        
        .validation-error {
            color: #dc3545 !important;
        }
        
        /* Spinner animation */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Input state transitions */
        .form-control {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-control.is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Better focus states */
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
    @endpush

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        // Form validation and enhancement
        $(document).ready(function() {
            let usernameTimeout;
            let emailTimeout;
            const userId = parseInt('{{ $user->id ?? 0 }}');
            console.log(userId);
            
            // Real-time username validation
            $('input[name="username"]').on('input', function() {
                let username = $(this).val();
                let $input = $(this);
                let $feedback = $input.siblings('.username-feedback');
                
                // Create feedback element if it doesn't exist
                if ($feedback.length === 0) {
                    $feedback = $('<div class="username-feedback mt-1"></div>');
                    $input.after($feedback);
                }
                
                // Clear previous timeout
                clearTimeout(usernameTimeout);
                
                // Reset input state
                $input.removeClass('is-valid is-invalid');
                $feedback.empty();
                
                if (username.length < 3) {
                    $feedback.html('<small class="text-muted"><i class="fas fa-info-circle"></i> Username must be at least 3 characters long</small>');
                    return;
                }
                
                // Show loading state
                $feedback.html('<small class="validation-loading"><i class="fas fa-spinner fa-spin"></i> Checking availability...</small>');
                
                // Set timeout for API call
                usernameTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("admin.users.check-username") }}',
                        method: 'POST',
                        data: {
                            username: username,
                            user_id: userId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.available) {
                                $input.addClass('is-valid');
                                $feedback.html('<small class="validation-success"><i class="fas fa-check-circle"></i> ' + response.message + '</small>');
                            } else {
                                $input.addClass('is-invalid');
                                $feedback.html('<small class="validation-error"><i class="fas fa-times-circle"></i> ' + response.message + '</small>');
                            }
                        },
                        error: function() {
                            $input.addClass('is-invalid');
                            $feedback.html('<small class="validation-error"><i class="fas fa-exclamation-triangle"></i> Error checking username availability</small>');
                        }
                    });
                }, 500); // Wait 500ms after user stops typing
            });
            
            // Real-time email validation
            $('input[name="email"]').on('input', function() {
                let email = $(this).val();
                let $input = $(this);
                let $feedback = $input.siblings('.email-feedback');
                
                // Create feedback element if it doesn't exist
                if ($feedback.length === 0) {
                    $feedback = $('<div class="email-feedback mt-1"></div>');
                    $input.after($feedback);
                }
                
                // Clear previous timeout
                clearTimeout(emailTimeout);
                
                // Reset input state
                $input.removeClass('is-valid is-invalid');
                $feedback.empty();
                
                if (email.length < 5) {
                    $feedback.html('<small class="text-muted"><i class="fas fa-info-circle"></i> Please enter a valid email address</small>');
                    return;
                }
                
                // Basic email format validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $input.addClass('is-invalid');
                    $feedback.html('<small class="validation-error"><i class="fas fa-times-circle"></i> Please enter a valid email address</small>');
                    return;
                }
                
                // Show loading state
                $feedback.html('<small class="validation-loading"><i class="fas fa-spinner fa-spin"></i> Checking availability...</small>');
                
                // Set timeout for API call
                emailTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("admin.users.check-email") }}',
                        method: 'POST',
                        data: {
                            email: email,
                            user_id: userId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.available) {
                                $input.addClass('is-valid');
                                $feedback.html('<small class="validation-success"><i class="fas fa-check-circle"></i> ' + response.message + '</small>');
                            } else {
                                $input.addClass('is-invalid');
                                $feedback.html('<small class="validation-error"><i class="fas fa-times-circle"></i> ' + response.message + '</small>');
                            }
                        },
                        error: function() {
                            $input.addClass('is-invalid');
                            $feedback.html('<small class="validation-error"><i class="fas fa-exclamation-triangle"></i> Error checking email availability</small>');
                        }
                    });
                }, 500); // Wait 500ms after user stops typing
            });
            
            // Password confirmation validation
            $('input[name="password_confirmation"]').on('input', function() {
                let password = $('input[name="password"]').val();
                let confirmation = $(this).val();
                let $input = $(this);
                let $feedback = $input.siblings('.password-feedback');
                
                // Create feedback element if it doesn't exist
                if ($feedback.length === 0) {
                    $feedback = $('<div class="password-feedback mt-1"></div>');
                    $input.after($feedback);
                }
                
                // Reset input state
                $input.removeClass('is-valid is-invalid');
                $feedback.empty();
                
                if (password && confirmation) {
                    if (password === confirmation) {
                        $input.addClass('is-valid');
                        $feedback.html('<small class="validation-success"><i class="fas fa-check-circle"></i> Passwords match</small>');
                    } else {
                        $input.addClass('is-invalid');
                        $feedback.html('<small class="validation-error"><i class="fas fa-times-circle"></i> Passwords do not match</small>');
                    }
                } else if (confirmation.length > 0) {
                    $feedback.html('<small class="text-muted"><i class="fas fa-info-circle"></i> Please enter the password first</small>');
                }
            });
            
            // Also validate password confirmation when main password changes
            $('input[name="password"]').on('input', function() {
                let confirmation = $('input[name="password_confirmation"]').val();
                if (confirmation) {
                    $('input[name="password_confirmation"]').trigger('input');
                }
            });
            
            // Form submission validation
            $('form').on('submit', function(e) {
                let hasErrors = false;
                
                // Check if any inputs have is-invalid class
                $(this).find('.is-invalid').each(function() {
                    if ($(this).attr('name') === 'username' || $(this).attr('name') === 'email' || $(this).attr('name') === 'password_confirmation') {
                        hasErrors = true;
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fix the validation errors before submitting the form.');
                    return false;
                }
            });
            
            // Initial validation on page load (in case of back button or page refresh)
            setTimeout(function() {
                let username = $('input[name="username"]').val();
                let email = $('input[name="email"]').val();
                
                if (username && username.length >= 3) {
                    $('input[name="username"]').trigger('input');
                }
                
                if (email && email.length >= 5) {
                    $('input[name="email"]').trigger('input');
                }
            }, 500);
        });
    </script>
    @endpush
</x-layout>
