
<x-layout>
@section('title', 'Create Notification')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Create New Notification</h1>
            <div class="">
                <nav>
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}">Notifications</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-wave">
                <i class="fe fe-arrow-left me-2"></i>Back to Notifications
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Create Notification Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Notification Details</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST" id="notification-form">
                        @csrf
                        
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Enter notification title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="4" 
                                      placeholder="Enter notification message" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>Characters: <span id="char-count">0</span> | Recommended: 50-200 characters</small>
                            </div>
                        </div>

                        <!-- Type and Priority Row -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>
                                            <i class="fe fe-info"></i> Info
                                        </option>
                                        <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>
                                            <i class="fe fe-check-circle"></i> Success
                                        </option>
                                        <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>
                                            <i class="fe fe-alert-triangle"></i> Warning
                                        </option>
                                        <option value="danger" {{ old('type') === 'danger' ? 'selected' : '' }}>
                                            <i class="fe fe-alert-circle"></i> Danger
                                        </option>
                                        <option value="primary" {{ old('type') === 'primary' ? 'selected' : '' }}>
                                            <i class="fe fe-bell"></i> Primary
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                            🟢 Low
                                        </option>
                                        <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>
                                            🔵 Normal
                                        </option>
                                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                            🟡 High
                                        </option>
                                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                            🔴 Urgent
                                        </option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action URL and Text Row -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="action_url" class="form-label">Action URL (Optional)</label>
                                    <input type="url" class="form-control @error('action_url') is-invalid @enderror" 
                                           id="action_url" name="action_url" value="{{ old('action_url') }}" 
                                           placeholder="https://example.com/action">
                                    @error('action_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <small>URL to redirect when notification is clicked</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="action_text" class="form-label">Button Text (Optional)</label>
                                    <input type="text" class="form-control @error('action_text') is-invalid @enderror" 
                                           id="action_text" name="action_text" value="{{ old('action_text') }}" 
                                           placeholder="View Details">
                                    @error('action_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Expiry Date -->
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expires At (Optional)</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" name="expires_at" value="{{ old('expires_at') }}" 
                                   min="{{ now()->format('Y-m-d\TH:i') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>Leave empty for permanent notification</small>
                            </div>
                        </div>

                        <!-- Recipients Section -->
                        <div class="mb-4">
                            <label class="form-label">Recipient Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_type_admin" 
                                               value="admin" {{ old('recipient_type', 'admin') === 'admin' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_type_admin">
                                            <i class="fe fe-shield me-2"></i>Send to Admins
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="recipient_type" id="recipient_type_user" 
                                               value="user" {{ old('recipient_type') === 'user' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_type_user">
                                            <i class="fe fe-users me-2"></i>Send to Members/Users
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('recipient_type')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Send To Section -->
                        <div class="mb-4">
                            <label class="form-label">Send To <span class="text-danger">*</span></label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="send_to" id="send_to_all" 
                                       value="all" {{ old('send_to', 'all') === 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_to_all">
                                    <i class="fe fe-users me-2"></i><span id="send_to_all_text">All Admins</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="send_to" id="send_to_specific" 
                                       value="specific" {{ old('send_to') === 'specific' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_to_specific">
                                    <i class="fe fe-user-check me-2"></i><span id="send_to_specific_text">Specific Admins</span>
                                </label>
                            </div>
                            @error('send_to')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Admin Selection (Hidden by default) -->
                        <div class="mb-3" id="admin-selection" style="display: none;">
                            <label for="admin_ids" class="form-label">Select Admins <span class="text-danger">*</span></label>
                            <div class="alert alert-info mb-2" id="admin-selection-help">
                                <i class="fe fe-info-circle me-2"></i>
                                <strong>Required:</strong> Please select at least one admin to send the notification to.
                            </div>
                            <select class="form-select @error('admin_ids') is-invalid @enderror" 
                                    id="admin_ids" name="admin_ids[]" multiple size="6">
                                @php
                                    $admins = \App\Models\Admin::select('id', 'name', 'email')->get();
                                @endphp
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" 
                                            {{ in_array($admin->id, old('admin_ids', [])) ? 'selected' : '' }}>
                                        {{ $admin->name ?? $admin->email }} ({{ $admin->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('admin_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('admin_ids.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>Hold Ctrl/Cmd to select multiple admins</small>
                            </div>
                        </div>

                        <!-- User Selection (Hidden by default) -->
                        <div class="mb-3" id="user-selection" style="display: none;">
                            <label for="user_ids" class="form-label">Select Users <span class="text-danger">*</span></label>
                            <div class="alert alert-info mb-2" id="user-selection-help">
                                <i class="fe fe-info-circle me-2"></i>
                                <strong>Required:</strong> Please select at least one user to send the notification to.
                            </div>
                            
                            <!-- User Search -->
                            <div class="mb-2">
                                <input type="text" class="form-control" id="user-search" 
                                       placeholder="Search users by name, email, or username...">
                            </div>
                            
                            <!-- User Selection with Filters -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <select class="form-select" id="user-filter">
                                        <option value="">All Users</option>
                                        <option value="active">Active Users</option>
                                        <option value="inactive">Inactive Users</option>
                                        <option value="verified">Verified Users</option>
                                        <option value="unverified">Unverified Users</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="select-all-users">
                                        <i class="fe fe-check-square me-1"></i>Select All Visible
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-all-users">
                                        <i class="fe fe-x-square me-1"></i>Clear All
                                    </button>
                                </div>
                            </div>
                            
                            <select class="form-select @error('user_ids') is-invalid @enderror" 
                                    id="user_ids" name="user_ids[]" multiple size="8">
                                @php
                                    $users = \App\Models\User::select('id', 'firstname', 'lastname', 'username', 'email', 'status', 'email_verified_at')
                                        ->orderBy('firstname')
                                        ->orderBy('lastname')
                                        ->limit(500) // Limit for performance
                                        ->get();
                                @endphp
                                @foreach($users as $user)
                                    @php
                                        $fullName = trim($user->firstname . ' ' . $user->lastname) ?: $user->username;
                                    @endphp
                                    <option value="{{ $user->id }}" 
                                            data-name="{{ strtolower($fullName) }}" 
                                            data-email="{{ strtolower($user->email) }}"
                                            data-username="{{ strtolower($user->username) }}"
                                            data-status="{{ $user->status }}"
                                            data-verified="{{ $user->email_verified_at ? 'verified' : 'unverified' }}"
                                            {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                        {{ $fullName }} ({{ $user->email }})
                                        @if($user->status == 0) <span class="text-muted">[Inactive]</span> @endif
                                        @if(!$user->email_verified_at) <span class="text-warning">[Unverified]</span> @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('user_ids.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>Hold Ctrl/Cmd to select multiple users. Showing first 500 users.</small>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                                <i class="fe fe-x me-2"></i>Cancel
                            </a>
                            <button type="button" class="btn btn-outline-info" id="preview-btn">
                                <i class="fe fe-eye me-2"></i>Preview
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-send me-2"></i>Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-eye me-2"></i>Live Preview
                    </div>
                </div>
                <div class="card-body">
                    <div class="notification-preview" id="notification-preview">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <div class="me-3">
                                <span class="avatar avatar-sm bg-info-transparent text-info">
                                    <i class="fe fe-info fs-14" id="preview-icon"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-semibold" id="preview-title">Sample Title</h6>
                                    <span class="badge bg-secondary" id="preview-priority">Normal</span>
                                </div>
                                <p class="text-muted mb-1" id="preview-message">Sample notification message will appear here...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark" id="preview-type">INFO</span>
                                    <button class="btn btn-outline-primary btn-xs" id="preview-action" style="display: none;">
                                        Action
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="mb-2">Recipients Preview</h6>
                            <div class="recipient-count">
                                <span class="badge bg-primary" id="recipient-count">All Admins ({{ \App\Models\Admin::count() }})</span>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted" id="recipient-type-info">
                                    <i class="fe fe-info me-1"></i>Sending to: <span id="recipient-type-text">Admins</span>
                                </small>
                            </div>
                        </div>
                        
                        <div class="mt-3" id="expiry-preview" style="display: none;">
                            <h6 class="mb-2">Expiry</h6>
                            <div class="text-muted">
                                <i class="fe fe-clock me-1"></i>
                                <span id="expiry-text">Never expires</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card custom-card mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-help-circle me-2"></i>Quick Tips
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fe fe-check-circle text-success me-2"></i>
                            <small>Use clear, concise titles (max 255 characters)</small>
                        </li>
                        <li class="mb-2">
                            <i class="fe fe-check-circle text-success me-2"></i>
                            <small>Keep messages informative but brief</small>
                        </li>
                        <li class="mb-2">
                            <i class="fe fe-check-circle text-success me-2"></i>
                            <small>Use appropriate priority levels</small>
                        </li>
                        <li class="mb-2">
                            <i class="fe fe-check-circle text-success me-2"></i>
                            <small>Test with "Send to Specific" first</small>
                        </li>
                        <li>
                            <i class="fe fe-check-circle text-success me-2"></i>
                            <small>Set expiry for time-sensitive notifications</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fe fe-eye me-2"></i>Notification Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="notification-preview-large">
                    <!-- Large preview will be populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="$('#notification-form').submit();">
                    <i class="fe fe-send me-2"></i>Send Notification
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .notification-preview .alert {
        border-left: 4px solid #007bff;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .notification-preview .alert.alert-info { border-left-color: #0dcaf0; }
    .notification-preview .alert.alert-success { border-left-color: #198754; }
    .notification-preview .alert.alert-warning { border-left-color: #ffc107; }
    .notification-preview .alert.alert-danger { border-left-color: #dc3545; }
    .notification-preview .alert.alert-primary { border-left-color: #0d6efd; }
    
    .recipient-count .badge {
        font-size: 0.75rem;
        padding: 0.5em 0.75em;
    }
    
    .form-text small {
        color: #6c757d;
    }
    
    #char-count {
        font-weight: 600;
    }
    
    .quick-tips li {
        padding: 0.25rem 0;
    }
    
    .notification-preview-large .alert {
        font-size: 1.1rem;
        padding: 1.25rem;
    }
    
    .admin-selection-help {
        background: #e7f3ff;
        border: 1px solid #b8daff;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-top: 0.5rem;
    }

    /* Custom form styling */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-wave {
        transition: all 0.3s ease;
    }
    
    .btn-wave:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Character count for message
        $('#message').on('input', function() {
            const charCount = $(this).val().length;
            $('#char-count').text(charCount);
            
            if (charCount < 50) {
                $('#char-count').removeClass('text-success text-warning').addClass('text-danger');
            } else if (charCount > 200) {
                $('#char-count').removeClass('text-success text-danger').addClass('text-warning');
            } else {
                $('#char-count').removeClass('text-danger text-warning').addClass('text-success');
            }
        });
        
        // Show/hide admin/user selection based on recipient type and send_to radio
        $('input[name="recipient_type"], input[name="send_to"]').on('change', function() {
            const recipientType = $('input[name="recipient_type"]:checked').val();
            const sendTo = $('input[name="send_to"]:checked').val();
            
            // Update text labels based on recipient type
            if (recipientType === 'admin') {
                $('#send_to_all_text').text('All Admins');
                $('#send_to_specific_text').text('Specific Admins');
            } else {
                $('#send_to_all_text').text('All Users');
                $('#send_to_specific_text').text('Specific Users');
            }
            
            // Show/hide selection boxes
            if (sendTo === 'specific') {
                if (recipientType === 'admin') {
                    $('#admin-selection').show();
                    $('#user-selection').hide();
                    $('#admin_ids').prop('required', true);
                    $('#user_ids').prop('required', false);
                    $('label[for="admin_ids"]').addClass('text-primary fw-bold');
                } else {
                    $('#admin-selection').hide();
                    $('#user-selection').show();
                    $('#admin_ids').prop('required', false);
                    $('#user_ids').prop('required', true);
                    $('label[for="user_ids"]').addClass('text-primary fw-bold');
                }
            } else {
                $('#admin-selection').hide();
                $('#user-selection').hide();
                $('#admin_ids').prop('required', false);
                $('#user_ids').prop('required', false);
                $('label[for="admin_ids"], label[for="user_ids"]').removeClass('text-primary fw-bold');
            }
            updatePreview();
        });
        
        // User search functionality
        $('#user-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('#user_ids option').each(function() {
                const name = $(this).data('name') || '';
                const email = $(this).data('email') || '';
                const username = $(this).data('username') || '';
                if (name.includes(searchTerm) || email.includes(searchTerm) || username.includes(searchTerm) || searchTerm === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).prop('selected', false);
                }
            });
            updatePreview();
        });
        
        // User filter functionality
        $('#user-filter').on('change', function() {
            const filterValue = $(this).val();
            $('#user_ids option').each(function() {
                let show = true;
                if (filterValue === 'active') {
                    show = $(this).data('status') == 1;
                } else if (filterValue === 'inactive') {
                    show = $(this).data('status') == 0;
                } else if (filterValue === 'verified') {
                    show = $(this).data('verified') === 'verified';
                } else if (filterValue === 'unverified') {
                    show = $(this).data('verified') === 'unverified';
                }
                
                if (show) {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).prop('selected', false);
                }
            });
            updatePreview();
        });
        
        // Select all visible users
        $('#select-all-users').on('click', function() {
            $('#user_ids option:visible').prop('selected', true);
            updatePreview();
        });
        
        // Clear all user selections
        $('#clear-all-users').on('click', function() {
            $('#user_ids option').prop('selected', false);
            updatePreview();
        });
        
        // Trigger change event on page load to set initial state
        $('input[name="recipient_type"]:checked, input[name="send_to"]:checked').trigger('change');
        
        // Real-time preview updates
        $('#title, #message, #type, #priority, #action_url, #action_text, #expires_at').on('input change', updatePreview);
        $('input[name="recipient_type"], input[name="send_to"], #admin_ids, #user_ids').on('change', updatePreview);
        
        // Preview button
        $('#preview-btn').on('click', function() {
            updateLargePreview();
            $('#previewModal').modal('show');
        });
        
        // Initialize preview
        updatePreview();
        
        // Initialize character count
        $('#message').trigger('input');
    });
    
    function updatePreview() {
        const title = $('#title').val() || 'Sample Title';
        const message = $('#message').val() || 'Sample notification message will appear here...';
        const type = $('#type').val() || 'info';
        const priority = $('#priority').val() || 'normal';
        const actionUrl = $('#action_url').val();
        const actionText = $('#action_text').val() || 'Action';
        const expiresAt = $('#expires_at').val();
        const sendTo = $('input[name="send_to"]:checked').val();
        const recipientType = $('input[name="recipient_type"]:checked').val();
        
        // Update recipient type info
        $('#recipient-type-text').text(recipientType === 'admin' ? 'Admins' : 'Members/Users');
        
        // Update preview content
        $('#preview-title').text(title);
        $('#preview-message').text(message);
        $('#preview-type').text(type.toUpperCase());
        
        // Update priority badge
        const priorityColors = {
            'low': 'success',
            'normal': 'secondary', 
            'high': 'warning',
            'urgent': 'danger'
        };
        const priorityEmojis = {
            'low': '🟢',
            'normal': '🔵',
            'high': '🟡', 
            'urgent': '🔴'
        };
        $('#preview-priority').removeClass().addClass(`badge bg-${priorityColors[priority]}`).text(`${priorityEmojis[priority]} ${priority.charAt(0).toUpperCase() + priority.slice(1)}`);
        
        // Update type styling
        const typeColors = {
            'info': 'info',
            'success': 'success',
            'warning': 'warning',
            'danger': 'danger',
            'primary': 'primary'
        };
        const typeIcons = {
            'info': 'fe fe-info',
            'success': 'fe fe-check-circle',
            'warning': 'fe fe-alert-triangle',
            'danger': 'fe fe-alert-circle',
            'primary': 'fe fe-bell'
        };
        
        $('#notification-preview .alert').removeClass().addClass(`alert alert-${typeColors[type]} d-flex align-items-center`);
        $('#preview-icon').removeClass().addClass(typeIcons[type] + ' fs-14');
        $('#notification-preview .avatar').removeClass().addClass(`avatar avatar-sm bg-${typeColors[type]}-transparent text-${typeColors[type]}`);
        
        // Update action button
        if (actionUrl) {
            $('#preview-action').show().text(actionText);
        } else {
            $('#preview-action').hide();
        }
        
        // Update recipients
        if (sendTo === 'all') {
            if (recipientType === 'admin') {
                const adminCount = {{ \App\Models\Admin::count() }};
                $('#recipient-count').text(`All Admins (${adminCount})`);
            } else {
                const userCount = {{ \App\Models\User::count() }};
                $('#recipient-count').text(`All Users (${userCount})`);
            }
        } else {
            if (recipientType === 'admin') {
                const selectedCount = $('#admin_ids option:selected').length;
                $('#recipient-count').text(`${selectedCount} Selected Admin(s)`);
            } else {
                const selectedCount = $('#user_ids option:selected').length;
                $('#recipient-count').text(`${selectedCount} Selected User(s)`);
            }
        }
        
        // Update expiry
        if (expiresAt) {
            const expiryDate = new Date(expiresAt);
            const now = new Date();
            const diffTime = expiryDate - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            $('#expiry-preview').show();
            if (diffDays <= 1) {
                $('#expiry-text').text(`Expires in ${Math.ceil(diffTime / (1000 * 60 * 60))} hours`);
            } else {
                $('#expiry-text').text(`Expires in ${diffDays} days`);
            }
        } else {
            $('#expiry-preview').hide();
        }
    }
    
    function updateLargePreview() {
        const previewHtml = $('#notification-preview').html();
        $('.notification-preview-large').html(previewHtml);
    }
    
    // Form validation before submit
    $('#notification-form').on('submit', function(e) {
        const title = $('#title').val().trim();
        const message = $('#message').val().trim();
        const type = $('#type').val();
        const priority = $('#priority').val();
        const recipientType = $('input[name="recipient_type"]:checked').val();
        const sendTo = $('input[name="send_to"]:checked').val();
        
        // Basic required field validation
        if (!title || !message || !type || !priority || !recipientType || !sendTo) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            
            // Highlight empty required fields
            if (!title) $('#title').addClass('is-invalid');
            if (!message) $('#message').addClass('is-invalid');
            if (!type) $('#type').addClass('is-invalid');
            if (!priority) $('#priority').addClass('is-invalid');
            
            return false;
        }
        
        // Specific validation for recipient selection
        if (sendTo === 'specific') {
            if (recipientType === 'admin') {
                const selectedAdmins = $('#admin_ids option:selected').length;
                if (selectedAdmins === 0) {
                    e.preventDefault();
                    alert('Please select at least one admin when sending to specific admins.');
                    
                    // Highlight the admin selection field
                    $('#admin_ids').addClass('is-invalid');
                    
                    // Scroll to the admin selection field
                    $('html, body').animate({
                        scrollTop: $('#admin-selection').offset().top - 100
                    }, 500);
                    
                    return false;
                }
            } else {
                const selectedUsers = $('#user_ids option:selected').length;
                if (selectedUsers === 0) {
                    e.preventDefault();
                    alert('Please select at least one user when sending to specific users.');
                    
                    // Highlight the user selection field
                    $('#user_ids').addClass('is-invalid');
                    
                    // Scroll to the user selection field
                    $('html, body').animate({
                        scrollTop: $('#user-selection').offset().top - 100
                    }, 500);
                    
                    return false;
                }
            }
        }
        
        // Remove validation classes on successful validation
        $('.form-control, .form-select').removeClass('is-invalid');
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fe fe-loader me-2"></i>Sending...');
    });
    
    // Remove validation classes when user starts typing/selecting
    $('#title, #message, #type, #priority, #admin_ids, #user_ids').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
</script>
@endpush
</x-layout>
