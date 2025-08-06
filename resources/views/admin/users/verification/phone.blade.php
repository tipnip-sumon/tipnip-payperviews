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
                <i class="fas fa-phone me-2"></i>{{ $pageTitle }}
            </h4>
        </div>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.verification.dashboard') }}">Verification</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Phone Verification</li>
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
                        <p class="mb-0 text-white-50">Phone Verified Users</p>
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
                        <p class="mb-0 text-white-50">Phone Unverified Users</p>
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
                    <form method="GET" action="{{ route('admin.users.verification.phone') }}" class="d-flex">
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
                        <button type="button" class="btn btn-info btn-sm" onclick="bulkCallPhone()">
                            <i class="fas fa-phone me-1"></i>
                            <span class="d-none d-lg-inline">Initiate </span>Verification
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
                            <th class="d-none d-md-table-cell"><i class="fas fa-phone me-1"></i>Mobile Number</th>
                            <th><i class="fas fa-shield-alt me-1"></i>Status</th>
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
                            <td class="d-none d-md-table-cell">
                                @if($user->mobile)
                                    <span class="text-dark">{{ $user->mobile }}</span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus me-1"></i>No mobile number
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->phone_verified)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Verified
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($user->phone_verified_at)
                                    <span class="text-muted">{{ $user->phone_verified_at->format('M d, Y H:i') }}</span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus me-1"></i>Not verified
                                    </span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                        <span class="d-none d-lg-inline ms-1">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($user->phone_verified)
                                            <li>
                                                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'unverify')">
                                                    <i class="fas fa-times me-2"></i>Unverify Phone
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item text-success" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'verify')">
                                                    <i class="fas fa-check me-2"></i>Verify Phone
                                                </a>
                                            </li>
                                            @if($user->mobile)
                                            <li>
                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="sendPhoneVerification({{ $user->id }})">
                                                    <i class="fas fa-phone me-2"></i>Call for Verification
                                                </a>
                                            </li>
                                            @endif
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
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3"></i>
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
</style>

@endsection

@push('script')
<script>
    'use strict';

    // Define notify function using SweetAlert2
    function notify(type, message) {
        if (typeof Swal !== 'undefined') {
            let iconType = type === 'error' ? 'error' : 'success';
            let title = type === 'error' ? 'Error!' : 'Success!';
            
            Swal.fire({
                title: title,
                text: message,
                icon: iconType,
                timer: 3000,
                showConfirmButton: false,
                allowOutsideClick: true,
                allowEscapeKey: true,
                toast: true,
                position: 'top-end',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        } else {
            // Fallback to browser alert if SweetAlert2 is not available
            alert(message);
        }
    }

    // Select All functionality
    $('#selectAll').on('change', function() {
        $('.user-checkbox').prop('checked', this.checked);
    });

    // Individual verification actions
    function changeVerification(userId, action) {
        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.phone.verify', ':id') }}"
            : "{{ route('admin.users.verification.phone.unverify', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Send phone verification
    function sendPhoneVerification(userId) {
        let url = "{{ route('admin.users.verification.send.phone', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Bulk operations
    function bulkVerify(action) {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            notify('error', 'Please select at least one user');
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
                verification_type: 'phone'
            },
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                notify('error', 'An error occurred. Please try again.');
            }
        });
    }

    // Bulk phone verification
    function bulkCallPhone() {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            notify('error', 'Please select at least one user');
            return;
        }

        // Initiate phone calls one by one
        let initiated = 0;
        checkedUsers.forEach(function(userId) {
            sendPhoneVerification(userId);
            initiated++;
        });

        notify('success', `Phone verification initiated for ${initiated} users`);
    }
</script>
@endpush
