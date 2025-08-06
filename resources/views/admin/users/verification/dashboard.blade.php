@extends('components.layout')

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card rounded-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm me-3">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Verification Statistics Overview -->
                <div class="row g-3 mb-4">
                    <!-- Total Users -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['total_users'] }}</div>
                                        <div class="small">Total Users</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Verification -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-envelope-open fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['email_verified'] }}</div>
                                        <div class="small">{{ $stats['email_unverified'] }} unverified</div>
                                        <div class="small">Email Verification</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.email') }}" class="btn btn-sm btn-light">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Verification -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-sms fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['sms_verified'] }}</div>
                                        <div class="small">{{ $stats['sms_unverified'] }} unverified</div>
                                        <div class="small">SMS Verification</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.sms') }}" class="btn btn-sm btn-light">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KYC Verification -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-id-card fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['kyc_verified'] }}</div>
                                        <div class="small">{{ $stats['kyc_unverified'] }} unverified</div>
                                        <div class="small">KYC Verification</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.kyc') }}" class="btn btn-sm btn-dark">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Verification -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-secondary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-phone fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['phone_verified'] ?? 0 }}</div>
                                        <div class="small">{{ $stats['phone_unverified'] ?? 0 }} unverified</div>
                                        <div class="small">Phone Verification</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.phone') }}" class="btn btn-sm btn-light">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identity Verification -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-user-shield fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['identity_verified'] ?? 0 }}</div>
                                        <div class="small">{{ $stats['identity_unverified'] ?? 0 }} unverified</div>
                                        <div class="small">Identity Verification</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.identity') }}" class="btn btn-sm btn-light">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Two Factor Authentication -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-lock fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold h4 mb-1">{{ $stats['two_fa_enabled'] ?? 0 }}</div>
                                        <div class="small">{{ $stats['two_fa_disabled'] ?? 0 }} disabled</div>
                                        <div class="small">Two Factor Auth</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.verification.2fa') }}" class="btn btn-sm btn-light">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Verification Status -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card bg-gradient-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-shield-alt fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        @php
                                            $totalVerified = ($stats['email_verified'] ?? 0) + ($stats['sms_verified'] ?? 0) + ($stats['kyc_verified'] ?? 0);
                                            $verificationRate = $stats['total_users'] > 0 ? round(($totalVerified / ($stats['total_users'] * 3)) * 100, 1) : 0;
                                        @endphp
                                        <div class="fw-bold h4 mb-1">{{ $verificationRate }}%</div>
                                        <div class="small">Overall Verification</div>
                                        <div class="small">{{ $totalVerified }} total verifications</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-light" role="progressbar" style="width: {{ $verificationRate }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <a href="{{ route('admin.users.verification.settings') }}" class="btn btn-primary w-100 py-3">
                                            <i class="fas fa-cog me-2"></i>
                                            <span class="d-block">Verification Settings</span>
                                            <small class="d-block text-light">Configure verification rules</small>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <a href="{{ route('admin.users.verification.reports') }}" class="btn btn-info w-100 py-3">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            <span class="d-block">View Reports</span>
                                            <small class="d-block text-light">Detailed analytics</small>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <a href="{{ route('admin.users.verification.reports.export') }}" class="btn btn-success w-100 py-3">
                                            <i class="fas fa-download me-2"></i>
                                            <span class="d-block">Export Data</span>
                                            <small class="d-block text-light">Download reports</small>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <button onclick="showBulkVerificationActions()" class="btn btn-warning w-100 py-3">
                                            <i class="fas fa-tasks me-2"></i>
                                            <span class="d-block">Bulk Actions</span>
                                            <small class="d-block text-dark">Mass verification tools</small>
                                        </button>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <a href="{{ route('admin.users.index', ['filter' => 'unverified']) }}" class="btn btn-danger w-100 py-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <span class="d-block">Unverified Users</span>
                                            <small class="d-block text-light">View pending verifications</small>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                        <button onclick="refreshDashboard()" class="btn btn-secondary w-100 py-3">
                                            <i class="fas fa-sync-alt me-2"></i>
                                            <span class="d-block">Refresh Data</span>
                                            <small class="d-block text-light">Update statistics</small>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Verification Activities -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>Recent Verification Activities
                                </h5>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" onclick="filterTable('all')">All</button>
                                    <button type="button" class="btn btn-outline-success" onclick="filterTable('verified')">Verified</button>
                                    <button type="button" class="btn btn-outline-warning" onclick="filterTable('pending')">Pending</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-nowrap">User</th>
                                                <th class="text-center text-nowrap">Email</th>
                                                <th class="text-center text-nowrap">SMS</th>
                                                <th class="text-center text-nowrap">KYC</th>
                                                <th class="text-center text-nowrap d-none d-md-table-cell">Phone</th>
                                                <th class="text-center text-nowrap d-none d-lg-table-cell">2FA</th>
                                                <th class="text-nowrap d-none d-md-table-cell">Last Updated</th>
                                                <th class="text-center text-nowrap">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentVerifications as $user)
                                            <tr>
                                                <td class="text-nowrap">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 d-none d-sm-flex">
                                                            {{ strtoupper(substr($user->firstname ?? $user->username, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold small">{{ $user->firstname }} {{ $user->lastname }}</div>
                                                            <div class="text-muted small">{{ $user->username }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($user->ev)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-sm-inline ms-1">Verified</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock"></i>
                                                            <span class="d-none d-sm-inline ms-1">Pending</span>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($user->sv)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-sm-inline ms-1">Verified</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock"></i>
                                                            <span class="d-none d-sm-inline ms-1">Pending</span>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($user->kv)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-sm-inline ms-1">Verified</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock"></i>
                                                            <span class="d-none d-sm-inline ms-1">Pending</span>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center d-none d-md-table-cell">
                                                    @if($user->pv ?? false)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-lg-inline ms-1">Verified</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-minus"></i>
                                                            <span class="d-none d-lg-inline ms-1">N/A</span>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center d-none d-lg-table-cell">
                                                    @if($user->ts ?? false)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-shield-alt"></i>
                                                            <span class="d-none d-xl-inline ms-1">Enabled</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-shield"></i>
                                                            <span class="d-none d-xl-inline ms-1">Disabled</span>
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-nowrap small d-none d-md-table-cell">
                                                    {{ $user->updated_at->format('M d, Y') }}
                                                    <div class="text-muted smaller">{{ $user->updated_at->format('H:i') }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                                           class="btn btn-outline-primary" 
                                                           title="View User">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-success" 
                                                                onclick="quickVerify({{ $user->id }})"
                                                                title="Quick Verify">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                        No recent verification activities found
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[title]').tooltip();
        
        // Auto refresh dashboard every 5 minutes (less aggressive)
        setInterval(function() {
            refreshDashboard();
        }, 300000); // 5 minutes
    });

    // Filter table function
    function filterTable(status) {
        $('.btn-group button').removeClass('active');
        $(`button[onclick="filterTable('${status}')"]`).addClass('active');
        
        const table = $('.table tbody');
        const rows = table.find('tr');
        
        rows.show();
        
        if (status !== 'all') {
            rows.each(function() {
                const row = $(this);
                const badges = row.find('.badge');
                let hasStatus = false;
                
                badges.each(function() {
                    const badge = $(this);
                    if (status === 'verified' && badge.hasClass('bg-success')) {
                        hasStatus = true;
                    } else if (status === 'pending' && (badge.hasClass('bg-warning') || badge.hasClass('bg-secondary'))) {
                        hasStatus = true;
                    }
                });
                
                if (!hasStatus && !row.find('td[colspan]').length) {
                    row.hide();
                }
            });
        }
    }

    // Quick verify function
    function quickVerify(userId) {
        if (confirm('Are you sure you want to quickly verify all pending verifications for this user?')) {
            // Add AJAX call here to verify user
            $.ajax({
                url: `/admin/users/${userId}/quick-verify`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('User verified successfully!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Failed to verify user: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('An error occurred while verifying the user.', 'error');
                }
            });
        }
    }

    // Refresh dashboard function
    function refreshDashboard() {
        // Show loading indicator
        const refreshBtn = $('button[onclick="refreshDashboard()"]');
        const originalHtml = refreshBtn.html();
        refreshBtn.html('<i class="fas fa-spinner fa-spin me-2"></i><span class="d-block">Refreshing...</span><small class="d-block text-light">Please wait</small>');
        refreshBtn.prop('disabled', true);
        
        // Reload page after a short delay
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Bulk verification actions
    function showBulkVerificationActions() {
        const actions = [
            { text: 'Verify All Email Pending', action: 'email' },
            { text: 'Verify All SMS Pending', action: 'sms' },
            { text: 'Mark All KYC Reviewed', action: 'kyc' },
            { text: 'Enable 2FA for All', action: '2fa' },
            { text: 'Send Verification Reminders', action: 'reminder' }
        ];
        
        let html = '<div class="list-group">';
        actions.forEach(item => {
            html += `<button class="list-group-item list-group-item-action" onclick="executeBulkAction('${item.action}')">${item.text}</button>`;
        });
        html += '</div>';
        
        showModal('Bulk Verification Actions', html);
    }

    // Execute bulk action
    function executeBulkAction(action) {
        if (confirm(`Are you sure you want to execute this bulk action: ${action}?`)) {
            // Add AJAX call here for bulk actions
            $.ajax({
                url: '/admin/users/verification/bulk-action',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    action: action
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(`Bulk action completed: ${response.message}`, 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification('Failed to execute bulk action: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('An error occurred while executing the bulk action.', 'error');
                }
            });
        }
    }

    // Utility functions
    function showModal(title, content) {
        const modalHtml = `
            <div class="modal fade" id="dynamicModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">${content}</div>
                    </div>
                </div>
            </div>
        `;
        
        $('#dynamicModal').remove();
        $('body').append(modalHtml);
        $('#dynamicModal').modal('show');
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
</script>

<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .smaller {
        font-size: 0.75rem;
    }
    
    @media (max-width: 576px) {
        .card-header .btn-group {
            margin-top: 10px;
            width: 100%;
        }
        
        .card-header .btn-group .btn {
            flex: 1;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.4rem;
        }
    }
    
    @media (max-width: 768px) {
        .card-body .row .col-lg-4 {
            margin-bottom: 1rem;
        }
        
        .table td {
            padding: 0.5rem 0.25rem;
        }
    }
    
    /* Custom responsive utilities */
    @media (max-width: 991px) {
        .d-lg-table-cell {
            display: none !important;
        }
    }
    
    @media (max-width: 767px) {
        .d-md-table-cell {
            display: none !important;
        }
    }
    
    /* Improved card spacing on mobile */
    @media (max-width: 576px) {
        .row.g-3 > * {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush
