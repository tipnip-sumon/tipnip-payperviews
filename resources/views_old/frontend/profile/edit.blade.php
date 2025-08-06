<x-smart_layout>
    @section('top_title', $pageTitle ?? 'Edit Profile')
    @section('title', $pageTitle ?? 'Edit Profile')
    @section('content')
        <div class="row mb-4 my-4">
            <div class="col-md-8 col-lg-12 mx-auto">
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit text-primary"></i> Edit Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- @dd($user) --}}
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Avatar Upload -->
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <div class="position-relative d-inline-block">
                                        @if($user->avatar ?? false)
                                            <img src="{{ $user->avatar_url }}" 
                                                 alt="Profile Avatar" 
                                                 class="rounded-circle border"
                                                 style="width: 120px; height: 120px; object-fit: cover;"
                                                 id="avatar-preview">
                                        @else
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                                 style="width: 120px; height: 120px;"
                                                 id="avatar-preview">
                                                <i class="fas fa-user fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="position-absolute bottom-0 end-0">
                                            <label for="avatar" class="btn btn-primary btn-sm rounded-circle">
                                                <i class="fas fa-camera"></i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <input type="file" 
                                           name="avatar" 
                                           id="avatar" 
                                           class="d-none @error('avatar') is-invalid @enderror"
                                           accept="image/*">
                                    
                                    @error('avatar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($user->avatar ?? false)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="delete-avatar-btn">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-8">
                                    <h6>Profile Photo Guidelines:</h6>
                                    <ul class="list-unstyled text-muted small">
                                        <li><i class="fas fa-check text-success"></i> JPG, PNG, or GIF format</li>
                                        <li><i class="fas fa-check text-success"></i> Maximum file size: 2MB</li>
                                        <li><i class="fas fa-check text-success"></i> Recommended: Square image (1:1 ratio)</li>
                                        <li><i class="fas fa-check text-success"></i> Minimum resolution: 200x200 pixels</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="firstname" 
                                               id="firstname" 
                                               class="form-control @error('firstname') is-invalid @enderror" 
                                               value="{{ old('firstname', $user->firstname ?? '') }}" 
                                               required>
                                        @error('firstname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="lastname" 
                                               id="lastname" 
                                               class="form-control @error('lastname') is-invalid @enderror" 
                                               value="{{ old('lastname', $user->lastname ?? '') }}" 
                                               required>
                                        @error('lastname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" 
                                               name="phone" 
                                               id="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $user->mobile ?? '') }}" 
                                               placeholder="+1 (555) 123-4567" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @php  
                                    $c = json_decode(file_get_contents(resource_path('views/country/country.json')));
                                @endphp
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-control @error('country') is-invalid @enderror" id="country" name="country" required>
                                                <option value="">Select Country</option>
                                                @foreach ($c as $k => $country)
                                                    <option value="{{ $country->country }}" {{ old('country', $user->country ?? '') == $country->country ? 'selected' : '' }}>{{$country->country}}</option>
                                                @endforeach
                                            </select>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Read-only Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" 
                                               id="username" 
                                               class="form-control" 
                                               value="{{ $user->username ?? 'N/A' }}" 
                                               readonly>
                                        <div class="form-text">Username cannot be changed</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" 
                                               id="email" 
                                               class="form-control" 
                                               value="{{ $user->email ?? 'N/A' }}" 
                                               readonly>
                                        <div class="form-text">Email cannot be changed</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to Profile
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
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
        
        .alert-success {
            background-color: #d1f2eb;
            border-color: #7dcea0;
            color: #0c5460;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
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
        
        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn {
            border-radius: 6px;
        }
        
        .invalid-feedback {
            display: block;
        }
        
        #avatar-preview {
            transition: all 0.3s ease;
        }
        
        #avatar-preview:hover {
            transform: scale(1.05);
        }
        
        .position-relative .btn {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
    </style>
    @endpush

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').addClass('fade-out');
                setTimeout(function() {
                    $('.alert').remove();
                }, 500);
            }, 5000);
            
            // Avatar preview
            $('#avatar').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, GIF)');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#avatar-preview').html(`
                            <img src="${e.target.result}" 
                                 alt="Avatar Preview" 
                                 class="rounded-circle border"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Handle avatar deletion with AJAX
            $('#delete-avatar-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (confirm('Are you sure you want to delete your avatar?')) {
                    const btn = $(this);
                    const originalText = btn.html();
                    
                    // Show loading state
                    btn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
                    btn.prop('disabled', true);
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        url: '{{ route("profile.avatar.delete") }}',
                        type: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                // Update avatar preview to show default
                                $('#avatar-preview').html(`
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                         style="width: 120px; height: 120px;">
                                        <i class="fas fa-user fa-3x text-muted"></i>
                                    </div>
                                `);
                                
                                // Hide the delete button
                                btn.closest('.mt-2').hide();
                                
                                // Show success message
                                const alertHtml = `
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        ${response.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `;
                                $('.col-md-8.mx-auto').prepend(alertHtml);
                                
                                // Auto-hide after 3 seconds
                                setTimeout(function() {
                                    $('.alert-success').fadeOut();
                                }, 3000);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            const errorMessage = xhr.responseJSON?.message || 'Failed to delete avatar. Please try again.';
                            
                            // Show error message
                            const alertHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${errorMessage}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            $('.col-md-8.mx-auto').prepend(alertHtml);
                            
                            // Restore button state
                            btn.html(originalText);
                            btn.prop('disabled', false);
                        }
                    });
                }
            });
            
            // Remove invalid class on input
            $('input').on('input', function() {
                $(this).removeClass('is-invalid');
            });
            
            // Close alert manually
            $('.alert .btn-close').on('click', function() {
                $(this).closest('.alert').addClass('fade-out');
                setTimeout(() => {
                    $(this).closest('.alert').remove();
                }, 500);
            });
        });
    </script>
    @endpush
</x-smart_layout>
