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
                <i class="fas fa-shield-alt me-2"></i>{{ $pageTitle }}
            </h4>
        </div>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.verification.dashboard') }}">Verification</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Two Factor Authentication</li>
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
                            <i class="fas fa-shield-alt fs-1 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1 text-white">{{ number_format($stats['enabled']) }}</h3>
                        <p class="mb-0 text-white-50">2FA Enabled Users</p>
                        <small class="text-white-50">
                            <i class="fas fa-lock me-1"></i>Secure accounts
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
                            <i class="fas fa-unlock-alt fs-1 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="mb-1 text-white">{{ number_format($stats['disabled']) }}</h3>
                        <p class="mb-0 text-white-50">2FA Disabled Users</p>
                        <small class="text-white-50">
                            <i class="fas fa-exclamation-triangle me-1"></i>Needs attention
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
                    <form method="GET" action="{{ route('admin.users.verification.2fa') }}" class="d-flex">
                        <select name="status" class="form-select me-2">
                            <option value="">All Users</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>2FA Enabled</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>2FA Disabled</option>
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
                        <button type="button" class="btn btn-success btn-sm" onclick="bulkManage2FA('enable')">
                            <i class="fas fa-lock me-1"></i>
                            <span class="d-none d-sm-inline">Bulk </span>Enable 2FA
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="bulkManage2FA('disable')">
                            <i class="fas fa-unlock me-1"></i>
                            <span class="d-none d-sm-inline">Bulk </span>Disable 2FA
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
                            <th><i class="fas fa-shield-alt me-1"></i>2FA Status</th>
                            <th class="d-none d-lg-table-cell"><i class="fas fa-clock me-1"></i>Enabled At</th>
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
                                @if($user->two_fa_status)
                                    <span class="badge bg-success">
                                        <i class="fas fa-lock me-1"></i>Enabled
                                    </span>
                                    <br><small class="text-success">
                                        <i class="fas fa-shield-check me-1"></i>Secure
                                    </small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-unlock me-1"></i>Disabled
                                    </span>
                                    <br><small class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Vulnerable
                                    </small>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($user->two_fa_enabled_at)
                                    <span class="text-muted">{{ $user->two_fa_enabled_at->format('M d, Y H:i') }}</span>
                                    <br><small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $user->two_fa_enabled_at->diffForHumans() }}
                                    </small>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus me-1"></i>Not enabled
                                    </span>
                                    <br><small class="text-warning">
                                        <i class="fas fa-exclamation me-1"></i>Security risk
                                    </small>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                        <span class="d-none d-lg-inline ms-1">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($user->two_fa_status)
                                            <li>
                                                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="manage2FA({{ $user->id }}, 'disable')">
                                                    <i class="fas fa-unlock me-2"></i>Disable 2FA
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="reset2FA({{ $user->id }})">
                                                    <i class="fas fa-sync-alt me-2"></i>Reset 2FA
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item text-success" href="javascript:void(0)" onclick="manage2FA({{ $user->id }}, 'enable')">
                                                    <i class="fas fa-lock me-2"></i>Enable 2FA
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-primary" href="javascript:void(0)" onclick="force2FA({{ $user->id }})">
                                                    <i class="fas fa-shield-alt me-2"></i>Force 2FA Setup
                                                </a>
                                            </li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="{{ route('admin.users.show', $user->id) }}">
                                                <i class="fas fa-eye me-2"></i>View Details
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
                                    <i class="fas fa-shield-alt fa-2x mb-3 opacity-50"></i>
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
    
    /* Security status indicators */
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
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }
    
    /* Enhanced security badges */
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
    
    /* Security status colors */
    .text-security-high {
        color: #198754;
    }
    
    .text-security-low {
        color: #dc3545;
    }
    
    .text-security-medium {
        color: #fd7e14;
    }
</style>

@endsection

@push('script')
<script>
    'use strict';

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

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // Individual 2FA management
    function manage2FA(userId, action) {
        let url = action === 'enable' 
            ? "{{ route('admin.users.verification.2fa.enable', ':id') }}"
            : "{{ route('admin.users.verification.2fa.disable', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('error', response.message || 'Operation failed. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Reset 2FA for user
    function reset2FA(userId) {
        if (!confirm('Are you sure you want to reset 2FA for this user? They will need to set it up again.')) {
            return;
        }

        let url = "{{ route('admin.users.verification.2fa.reset', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('error', response.message || 'Reset failed. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to reset 2FA. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Force 2FA setup for user
    function force2FA(userId) {
        if (!confirm('Are you sure you want to force 2FA setup for this user? They will be required to set up 2FA on their next login.')) {
            return;
        }

        let url = "{{ route('admin.users.verification.2fa.force', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('error', response.message || 'Force 2FA failed. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to force 2FA setup. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Bulk 2FA management
    function bulkManage2FA(action) {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            showNotification('error', 'Please select at least one user');
            return;
        }

        let actionText = action === 'enable' ? 'enable' : 'disable';
        if (!confirm(`Are you sure you want to ${actionText} 2FA for ${checkedUsers.length} selected user(s)?`)) {
            return;
        }

        let url = action === 'enable' 
            ? "{{ route('admin.users.verification.bulk.verify') }}"
            : "{{ route('admin.users.verification.bulk.unverify') }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: checkedUsers,
                verification_type: '2fa'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('error', response.message || 'Bulk operation failed. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Bulk operation failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showNotification('error', errorMessage);
            }
        });
    }

    // Auto-refresh security status every 30 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            // Only refresh if page is visible to user
            $('.badge').each(function() {
                $(this).addClass('animate__animated animate__pulse');
                setTimeout(() => {
                    $(this).removeClass('animate__animated animate__pulse');
                }, 1000);
            });
        }
    }, 30000);

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
        item.addEventListener('click', function() {
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
        
        // Add tooltips to 2FA status badges
        $('.badge').each(function() {
            const isEnabled = $(this).hasClass('bg-success');
            const tooltipText = isEnabled ? '2FA is enabled for enhanced security' : '2FA is disabled - security risk';
            $(this).attr('title', tooltipText).tooltip();
        });
    });

    // Real-time security status check
    function checkSecurityUpdates() {
        // This would typically connect to a WebSocket or polling endpoint
        // For now, we'll just add visual feedback for security status
        $('.badge.bg-warning').each(function() {
            $(this).fadeOut(500).fadeIn(500);
        });
    }

    // Check for updates every 30 seconds
    setInterval(checkSecurityUpdates, 30000);
</script>
@endpush
