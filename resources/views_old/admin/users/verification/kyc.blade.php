@extends('components.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.users.verification.dashboard') }}" class="btn btn-light btn-sm me-3">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-id-card me-2"></i>{{ $pageTitle }}
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Statistics Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="card bg-success text-white border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-light bg-opacity-25 rounded-circle p-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fs-2 fw-bold">{{ $stats['verified'] }}</div>
                                        <div class="small">KYC Verified Users</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-light bg-opacity-25 rounded-circle p-3">
                                            <i class="fas fa-exclamation-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fs-2 fw-bold">{{ $stats['unverified'] }}</div>
                                        <div class="small">KYC Unverified Users</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Actions -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('admin.users.verification.kyc') }}">
                                <div class="input-group">
                                    <select name="status" class="form-select">
                                        <option value="">All Users</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Verified</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Unverified</option>
                                    </select>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <button type="button" class="btn btn-success" onclick="bulkVerify('verify')">
                                    <i class="fas fa-check"></i> Bulk Verify
                                </button>
                                <button type="button" class="btn btn-warning" onclick="bulkVerify('unverify')">
                                    <i class="fas fa-times"></i> Bulk Unverify
                                </button>
                                <a href="{{ route('admin.kyc.index') }}" class="btn btn-info">
                                    <i class="fas fa-list"></i> View KYC Requests
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table with Horizontal Scroll for Mobile -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <!-- Mobile Scroll Hint -->
                                <div class="d-block d-lg-none bg-light p-2 text-center small text-muted">
                                    <i class="fas fa-hand-point-right me-1"></i>
                                    Swipe left/right to view all columns
                                </div>
                                
                                <div class="card-body p-0">
                                    <div class="table-responsive" id="kycTableContainer">
                                        <table class="table table-hover table-striped mb-0" id="kycTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="min-width: 80px;">
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </th>
                                                    <th style="min-width: 200px;">User</th>
                                                    <th style="min-width: 120px;">Status</th>
                                                    <th style="min-width: 160px;">Verified At</th>
                                                    <th style="min-width: 120px;">Joined</th>
                                                    <th style="min-width: 140px;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($users as $user)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2">
                                                                {{ strtoupper(substr($user->firstname, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold">{{ $user->firstname }} {{ $user->lastname }}</div>
                                                                <small class="text-muted">{{ $user->username }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($user->kv)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle me-1"></i>KYC Verified
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-exclamation-circle me-1"></i>KYC Unverified
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($user->kv && isset($user->kyc_verified_at) && $user->kyc_verified_at)
                                                            @php
                                                                $kycDate = is_string($user->kyc_verified_at) ? \Carbon\Carbon::parse($user->kyc_verified_at) : $user->kyc_verified_at;
                                                            @endphp
                                                            @if($kycDate)
                                                                <small>{{ $kycDate->format('M d, Y') }}</small><br>
                                                                <small class="text-muted">{{ $kycDate->format('H:i') }}</small>
                                                            @else
                                                                <span class="text-muted">Not verified</span>
                                                            @endif
                                                        @elseif($user->kv && $user->updated_at)
                                                            <small>{{ $user->updated_at->format('M d, Y') }}</small><br>
                                                            <small class="text-muted">{{ $user->updated_at->format('H:i') }}</small>
                                                        @else
                                                            <span class="text-muted">Not verified</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $user->created_at->format('M d, Y') }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                @if($user->kv)
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'unverify')">
                                                                            <i class="fas fa-times text-warning me-2"></i>Unverify KYC
                                                                        </a>
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="changeVerification({{ $user->id }}, 'verify')">
                                                                            <i class="fas fa-check text-success me-2"></i>Verify KYC
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                                        <i class="fas fa-eye text-primary me-2"></i>View Details
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.kyc.index') }}?user={{ $user->id }}">
                                                                        <i class="fas fa-file-alt text-info me-2"></i>View KYC Documents
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
                                                            <i class="fas fa-users fa-3x mb-3"></i>
                                                            <div class="h5">No users found</div>
                                                            <p class="mb-0">Try adjusting your filters or check back later.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Mobile Scroll Indicator -->
                                    <div class="d-block d-lg-none" id="scrollIndicator">
                                        <div class="scroll-indicator-container p-2 bg-light border-top">
                                            <div class="scroll-indicator-track">
                                                <div class="scroll-indicator-thumb" id="scrollThumb"></div>
                                            </div>
                                            <small class="text-muted text-center d-block mt-1">
                                                <span id="scrollText">Scroll to see more →</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($users->hasPages())
                                <div class="card-footer bg-light border-top">
                                    <div class="d-flex justify-content-center">
                                        {{ paginateLinks($users) }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for Horizontal Scrolling Table -->
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-bottom: 2px solid #dee2e6;
}

.card {
    border-radius: 0.5rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Horizontal Scroll Table Styles */
@media (max-width: 991.98px) {
    .table-responsive {
        position: relative;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #007bff #f8f9fa;
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #0056b3;
    }

    /* Simple table layout for mobile */
    #kycTable {
        min-width: 800px;
        width: auto;
        table-layout: auto;
    }

    /* Column minimum widths */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        min-width: 80px;
        text-align: center;
    }

    .table th:nth-child(2),
    .table td:nth-child(2) {
        min-width: 200px;
    }

    .table th:nth-child(3),
    .table td:nth-child(3) {
        min-width: 120px;
    }

    .table th:nth-child(4),
    .table td:nth-child(4) {
        min-width: 160px;
    }

    .table th:nth-child(5),
    .table td:nth-child(5) {
        min-width: 120px;
    }

    .table th:nth-child(6),
    .table td:nth-child(6) {
        min-width: 140px;
        text-align: center;
    }

    /* Scroll indicator styles */
    .scroll-indicator-container {
        background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .scroll-indicator-track {
        height: 4px;
        background: #dee2e6;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
    }

    .scroll-indicator-thumb {
        height: 100%;
        background: #007bff;
        border-radius: 2px;
        transition: all 0.3s ease;
        width: 30%;
    }
}

/* Desktop styles */
@media (min-width: 992px) {
    #kycTable {
        table-layout: auto;
        min-width: auto;
        width: auto;
    }
    
    .table th,
    .table td {
        width: auto;
        min-width: auto;
    }
}

/* Mobile specific improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .d-flex.flex-wrap.gap-2 {
        justify-content: center !important;
        gap: 0.5rem !important;
    }
    
    .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .fs-2 {
        font-size: 1.5rem !important;
    }
    
    .avatar-sm {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .dropdown-menu {
        font-size: 0.875rem;
    }
    
    /* Adjust table for smaller screens */
    #kycTable {
        min-width: 700px;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }
    
    .card-body {
        padding: 0.75rem !important;
    }
    
    .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .input-group .btn {
        padding: 0.375rem 0.75rem;
    }
    
    .fs-2 {
        font-size: 1.25rem !important;
    }
    
    .card-title {
        font-size: 0.9rem;
    }
    
    .avatar-sm {
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
    
    /* Further adjust table for very small screens */
    #kycTable {
        min-width: 650px;
    }
}

/* Smooth scrolling animation */
.table-responsive {
    scroll-behavior: smooth;
}

/* Loading animation */
.table tbody tr {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

    // Individual verification actions
    function changeVerification(userId, action) {
        let url = action === 'verify' 
            ? "{{ route('admin.users.verification.kyc.verify', ':id') }}"
            : "{{ route('admin.users.verification.kyc.unverify', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    }

    // Bulk operations
    function bulkVerify(action) {
        let checkedUsers = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (checkedUsers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection!',
                text: 'Please select at least one user'
            });
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
                verification_type: 'kyc'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    }

    // Horizontal scroll indicator for mobile
    $(document).ready(function() {
        const tableContainer = $('#kycTableContainer');
        const scrollThumb = $('#scrollThumb');
        const scrollText = $('#scrollText');
        
        if (window.innerWidth <= 991) {
            // Update scroll indicator
            function updateScrollIndicator() {
                const scrollLeft = tableContainer.scrollLeft();
                const scrollWidth = tableContainer[0].scrollWidth;
                const clientWidth = tableContainer[0].clientWidth;
                const maxScroll = scrollWidth - clientWidth;
                
                if (maxScroll > 0) {
                    const percentage = (scrollLeft / maxScroll) * 100;
                    const thumbWidth = Math.max((clientWidth / scrollWidth) * 100, 15);
                    
                    scrollThumb.css({
                        'width': thumbWidth + '%',
                        'transform': `translateX(${percentage * (100 - thumbWidth) / 100}%)`
                    });
                    
                    // Update text based on scroll position
                    if (percentage === 0) {
                        scrollText.text('Scroll to see more →');
                    } else if (percentage >= 95) {
                        scrollText.text('← Scroll back to start');
                    } else {
                        scrollText.text(`${Math.round(percentage)}% scrolled`);
                    }
                }
            }
            
            // Listen to scroll events
            tableContainer.on('scroll', updateScrollIndicator);
            
            // Initial call
            updateScrollIndicator();
            
            // Touch scroll enhancement
            let isScrolling = false;
            
            tableContainer.on('touchstart', function() {
                isScrolling = true;
                $(this).addClass('scrolling');
            });
            
            tableContainer.on('touchend', function() {
                isScrolling = false;
                setTimeout(() => {
                    $(this).removeClass('scrolling');
                }, 150);
            });
        }
        
        // Add smooth scrolling for dropdown menus near edges
        $('.dropdown-toggle').on('click', function() {
            const dropdown = $(this);
            const tableContainer = $('#kycTableContainer');
            const dropdownOffset = dropdown.offset().left;
            const containerOffset = tableContainer.offset().left;
            const containerWidth = tableContainer.width();
            
            // If dropdown is near the right edge, scroll to show it
            if (dropdownOffset + 200 > containerOffset + containerWidth) {
                const scrollAmount = tableContainer.scrollLeft() + 200;
                tableContainer.animate({ scrollLeft: scrollAmount }, 300);
            }
        });
        
        // Row animation delay
        $('.table tbody tr').each(function(index) {
            $(this).css('animation-delay', (index * 0.05) + 's');
        });
    });
</script>
@endpush
