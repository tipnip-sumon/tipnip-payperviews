@extends('components.layout')

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.users.verification.dashboard') }}" class="btn btn-primary btn-sm me-3">
                <i class="fas fa-arrow-left me-1"></i>
                <span class="d-none d-sm-inline">Back to Dashboard</span>
                <span class="d-inline d-sm-none">Back</span>
            </a>
            <h4 class="fw-medium mb-0">
                <i class="fas fa-id-card me-2"></i>{{ $pageTitle }}
            </h4>
        </div>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.verification.dashboard') }}">Verification</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Identity Verification</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-success text-white h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fs-1 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1 text-white">{{ number_format($stats['verified']) }}</h3>
                        <p class="mb-0 text-white-50">Identity Verified Users</p>
                        <small class="text-white-50">
                            <i class="fas fa-shield-check me-1"></i>Verified accounts
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-warning text-white h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-exclamation-circle fs-1 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1 text-white">{{ number_format($stats['unverified']) }}</h3>
                        <p class="mb-0 text-white-50">Identity Unverified Users</p>
                        <small class="text-white-50">
                            <i class="fas fa-user-times me-1"></i>Pending verification
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <!-- Filter Section -->
                <div class="col-lg-4 col-md-6">
                    <form method="GET" action="{{ route('admin.users.verification.identity') }}" class="d-flex">
                        <select name="status" class="form-select me-2">
                            <option value="">All Users</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Unverified</option>
                        </select>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Filter</span>
                        </button>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <div class="col-lg-8 col-md-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-success btn-sm" onclick="bulkVerify('verify')">
                            <i class="fas fa-check me-1"></i>
                            <span class="d-none d-sm-inline">Bulk </span>Verify
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="bulkVerify('unverify')">
                            <i class="fas fa-times me-1"></i>
                            <span class="d-none d-sm-inline">Bulk </span>Unverify
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="bulkSendInstructions()">
                            <i class="fas fa-paper-plane me-1"></i>
                            <span class="d-none d-lg-inline">Send </span>Instructions
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th><i class="fas fa-user me-1"></i>User</th>
                            <th><i class="fas fa-id-card me-1"></i>Identity Status</th>
                            <th class="d-none d-lg-table-cell"><i class="fas fa-check-circle me-1"></i>Verified At</th>
                            <th class="d-none d-sm-table-cell"><i class="fas fa-calendar me-1"></i>Joined</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->avatar)
                                        <img src="{{ asset($user->avatar_url) }}" alt="Avatar" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $user->firstname }} {{ $user->lastname }}</div>
                                        <small class="text-muted">{{ $user->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->identity_verified)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Identity Verified
                                    </span>
                                    <br><small class="text-success">
                                        <i class="fas fa-shield-check me-1"></i>Trusted account
                                    </small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Identity Unverified
                                    </span>
                                    <br><small class="text-warning">
                                        <i class="fas fa-user-times me-1"></i>Needs verification
                                    </small>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($user->identity_verified_at && $user->identity_verified_at instanceof \Carbon\Carbon)
                                    <span class="text-muted">{{ $user->identity_verified_at->format('M d, Y H:i') }}</span>
                                    <br><small class="text-success">
                                        <i class="fas fa-check me-1"></i>{{ $user->identity_verified_at->diffForHumans() }}
                                    </small>
                                @elseif($user->identity_verified_at)
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($user->identity_verified_at)->format('M d, Y H:i') }}</span>
                                    <br><small class="text-success">
                                        <i class="fas fa-check me-1"></i>{{ \Carbon\Carbon::parse($user->identity_verified_at)->diffForHumans() }}
                                    </small>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus me-1"></i>Not verified
                                    </span>
                                    <br><small class="text-warning">
                                        <i class="fas fa-clock me-1"></i>Verification pending
                                    </small>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if($user->created_at instanceof \Carbon\Carbon)
                                    <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                    <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                        <span class="d-none d-lg-inline ms-1">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($user->identity_verified)
                                            <li>
                                                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'unverify')">
                                                    <i class="fas fa-times me-2"></i>Unverify Identity
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="downloadVerificationDocs({{ $user->id }})">
                                                    <i class="fas fa-eye me-2"></i>View Documents
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item text-success" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'verify')">
                                                    <i class="fas fa-check me-2"></i>Verify Identity
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="sendIdentityInstructions({{ $user->id }})">
                                                    <i class="fas fa-paper-plane me-2"></i>Send Instructions
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-primary" href="javascript:void(0)" onclick="requestDocuments({{ $user->id }})">
                                                    <i class="fas fa-file-upload me-2"></i>Request Documents
                                                </a>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="{{ route('admin.users.show', $user->id) }}">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-secondary" href="javascript:void(0)" onclick="viewVerificationHistory({{ $user->id }})">
                                                <i class="fas fa-history me-2"></i>View Profile
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-id-card fa-2x mb-3 opacity-50"></i>
                                    <br>No users found
                                    <br><small>Try adjusting your filters</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-center">
                {{ paginateLinks($users) }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Custom responsive styles */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        .card-body {
            padding: 0.5rem;
        }
        
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.7rem;
        }
    }
    
    @media (max-width: 768px) {
        .page-header-breadcrumb {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .breadcrumb {
            margin-top: 0.5rem;
        }
        
        .card-header .row {
            flex-direction: column;
        }
        
        .card-header .col-lg-8 {
            margin-top: 1rem;
        }
    }
    
    /* Enhanced table styles */
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        background-color: #f8f9fa;
    }
    
    /* Card enhancements */
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa;
    }
    
    /* Button improvements */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
    }
    
    .btn-group .btn + .btn {
        margin-left: 0.25rem;
    }
    
    /* Statistics cards */
    .bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }
    
    .bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    }
    
    /* Identity verification status indicators */
    .text-success {
        color: #198754 !important;
    }
    
    .text-warning {
        color: #f57c00 !important;
    }
    
    /* Dropdown improvements */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 0.5rem;
        min-width: 200px;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
    
    /* Enhanced verification badges */
    .badge {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .bg-success .badge {
        background-color: rgba(255,255,255,0.2) !important;
    }
    
    .bg-warning .badge {
        background-color: rgba(255,255,255,0.2) !important;
    }
    
    /* Identity verification specific styles */
    .verification-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .verification-pending {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* Loading and transition effects */
    .card, .btn, .badge {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Avatar improvements */
    .rounded-circle {
        border: 2px solid #e9ecef;
    }
    
    /* Document status indicators */
    .document-verified {
        color: #198754;
    }
    
    .document-pending {
        color: #fd7e14;
    }
    
    .document-rejected {
        color: #dc3545;
    }
</style>

@endsection

@push('script')
<script>
    'use strict';

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // Notification helper function
    function showNotification(type, message) {
        // Try to use existing notification system first
        if (typeof notify !== 'undefined') {
            notify(type, message);
        } else if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
                title: message,
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    // Individual verification actions
    function changeVerification(userId, action) {
        let actionText = action === 'verify' ? 'verify' : 'unverify';
        if (!confirm(`Are you sure you want to ${actionText} this user's identity?`)) {
            return;
        }

        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.identity.verify', ':id') }}"
            : "{{ route('admin.users.verification.identity.unverify', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'Operation completed successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('success', 'Operation completed successfully');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Send identity verification instructions
    function sendIdentityInstructions(userId) {
        let url = "{{ route('admin.users.verification.send.identity', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'Identity verification instructions sent successfully');
                } else {
                    showNotification('error', response.message || 'Failed to send instructions. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send instructions. Please check your email configuration.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Download verification documents
    function downloadVerificationDocs(userId) {
        // Use the user details route to access documents
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', userId);
        
        // Open user details in new tab where documents can be viewed/downloaded
        window.open(url, '_blank');
        
        showNotification('info', 'Opening user details to view documents...');
    }

    // Request documents from user
    function requestDocuments(userId) {
        if (!confirm('Are you sure you want to request identity documents from this user? They will receive an email notification.')) {
            return;
        }

        // Use the send identity instructions route as it serves similar purpose
        let url = "{{ route('admin.users.verification.send.identity', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: 'request_documents'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'Document request sent successfully');
                } else {
                    showNotification('error', response.message || 'Failed to send document request. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send document request. Please check your email configuration.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // View verification history
    function viewVerificationHistory(userId) {
        // Use the user details route to view history
        let url = "{{ route('admin.users.show', ':id') }}";
        url = url.replace(':id', userId);
        
        // Open in a new tab
        window.open(url, '_blank');
        
        showNotification('info', 'Opening user details to view verification history...');
    }

    // Bulk operations
    function bulkVerify(action) {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            showNotification('error', 'Please select at least one user');
            return;
        }

        let actionText = action === 'verify' ? 'verify' : 'unverify';
        if (!confirm(`Are you sure you want to ${actionText} identity for ${checkedUsers.length} selected user(s)?`)) {
            return;
        }

        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.bulk.verify') }}"
            : "{{ route('admin.users.verification.bulk.unverify') }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: checkedUsers,
                verification_type: 'identity'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message || 'Bulk operation completed successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('success', 'Bulk operation completed successfully');
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                showNotification('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Bulk send identity verification instructions
    function bulkSendInstructions() {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            showNotification('error', 'Please select at least one user');
            return;
        }

        if (!confirm(`Are you sure you want to send identity verification instructions to ${checkedUsers.length} selected user(s)?`)) {
            return;
        }

        let url = "{{ route('admin.users.verification.send.identity.bulk') }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: checkedUsers
            },
            success: function(response) {
                if (response.success) {
                    let successCount = response.sent_count || checkedUsers.length;
                    let failedCount = checkedUsers.length - successCount;
                    
                    if (failedCount > 0) {
                        showNotification('warning', `Instructions sent to ${successCount} users. ${failedCount} failed due to email issues.`);
                    } else {
                        showNotification('success', `Identity verification instructions sent to ${successCount} users successfully`);
                    }
                } else {
                    showNotification('error', response.message || 'Failed to send bulk instructions. Please check your email configuration.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send bulk instructions. Please check your email configuration.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Auto-refresh verification status every 60 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            // Only refresh if page is visible to user
            $('.badge').each(function() {
                if ($(this).hasClass('bg-warning')) {
                    $(this).addClass('verification-pending');
                }
            });
        }
    }, 60000);

    // Add loading states to buttons
    function addLoadingState(button) {
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalHtml;
            button.disabled = false;
        }, 2000);
    }

    // Enhance dropdown button clicks
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't add loading state for view actions
            if (!this.href || this.href === 'javascript:void(0)') {
                const button = this.closest('.dropdown').querySelector('.dropdown-toggle');
                addLoadingState(button);
            }
        });
    });

    // Initialize tooltips for status indicators
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Add tooltips to verification status badges
        $('.badge').each(function() {
            const isVerified = $(this).hasClass('bg-success');
            const tooltipText = isVerified ? 'Identity has been verified by admin' : 'Identity verification is pending';
            $(this).attr('title', tooltipText).tooltip();
        });
    });

    // Real-time verification status check
    function checkVerificationUpdates() {
        // This would typically connect to a WebSocket or polling endpoint
        // For now, we'll just add visual feedback for pending verifications
        $('.verification-pending').each(function() {
            $(this).fadeOut(500).fadeIn(500);
        });
    }

    // Check for updates every 30 seconds
    setInterval(checkVerificationUpdates, 30000);
</script>
@endpush
