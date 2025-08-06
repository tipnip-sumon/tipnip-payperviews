@extends('components.layout')
@section('title', 'Referral Benefits System') 

@section('content')
<div class="container-fluid my-4">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Referral Benefits System</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Settings</a></li>
                        <li class="breadcrumb-item active">Referral Benefits</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Qualified Users</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['total_qualified_users'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-user-check text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Bonuses Given</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value">{{ number_format($stats['total_bonuses_given'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-gift text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Bonus Transactions</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['total_bonus_transactions'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-transfer text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Users with Benefits</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value">{{ $stats['total_users_with_benefits'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-group text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Referral Benefits Configuration
                    </h5>
                </div>
                <form action="{{ route('admin.referral-benefits.update-settings') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Enable/Disable System -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                   {{ $referralBenefitsSettings['enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enabled">
                                <strong>Enable Referral Benefits System</strong>
                                <br><small class="text-muted">Master switch for the entire referral benefits system</small>
                            </label>
                        </div>

                        <!-- Basic Requirements -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_referrals" class="form-label">
                                        <strong>Minimum Referrals Required</strong>
                                    </label>
                                    <input type="number" class="form-control" id="minimum_referrals" 
                                           name="minimum_referrals" min="1" max="100"
                                           value="{{ $referralBenefitsSettings['minimum_referrals'] }}">
                                    <small class="text-muted">Number of qualifying referrals needed for benefits</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_investment_per_referral" class="form-label">
                                        <strong>Minimum Investment per Referral ($)</strong>
                                    </label>
                                    <input type="number" class="form-control" id="minimum_investment_per_referral" 
                                           name="minimum_investment_per_referral" min="1" step="0.01"
                                           value="{{ $referralBenefitsSettings['minimum_investment_per_referral'] }}">
                                    <small class="text-muted">Each referral must invest at least this amount</small>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Bonus Settings -->
                        <div class="border rounded p-3 mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-exchange-alt me-2"></i>Transfer Bonus Settings
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transfer_bonus_min" class="form-label">Minimum Bonus (%)</label>
                                        <input type="number" class="form-control" id="transfer_bonus_min" 
                                               name="transfer_bonus_min" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['transfer_bonus_min'] }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transfer_bonus_max" class="form-label">Maximum Bonus (%)</label>
                                        <input type="number" class="form-control" id="transfer_bonus_max" 
                                               name="transfer_bonus_max" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['transfer_bonus_max'] }}">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Qualified users get bonus when they transfer money to others</small>
                        </div>

                        <!-- Balance Receive Bonus Settings -->
                        <div class="border rounded p-3 mb-4">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-wallet me-2"></i>Balance Receive Bonus Settings
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receive_bonus_min" class="form-label">Minimum Bonus (%)</label>
                                        <input type="number" class="form-control" id="receive_bonus_min" 
                                               name="receive_bonus_min" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['receive_bonus_min'] }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receive_bonus_max" class="form-label">Maximum Bonus (%)</label>
                                        <input type="number" class="form-control" id="receive_bonus_max" 
                                               name="receive_bonus_max" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['receive_bonus_max'] }}">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Qualified users get bonus when they receive money from others</small>
                        </div>

                        <!-- Withdraw Charge Reduction Settings -->
                        <div class="border rounded p-3 mb-4">
                            <h6 class="text-warning mb-3">
                                <i class="fas fa-money-bill-wave me-2"></i>Withdraw Charge Reduction Settings
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="withdraw_reduction_min" class="form-label">Minimum Reduction (%)</label>
                                        <input type="number" class="form-control" id="withdraw_reduction_min" 
                                               name="withdraw_reduction_min" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['withdraw_reduction_min'] }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="withdraw_reduction_max" class="form-label">Maximum Reduction (%)</label>
                                        <input type="number" class="form-control" id="withdraw_reduction_max" 
                                               name="withdraw_reduction_max" min="0" max="10" step="0.1"
                                               value="{{ $referralBenefitsSettings['withdraw_reduction_max'] }}">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Qualified users get reduced withdrawal charges</small>
                        </div>

                        <!-- Information Panel -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>How It Works</h6>
                            <ul class="mb-0">
                                <li><strong>Qualification:</strong> Users need {{ $referralBenefitsSettings['minimum_referrals'] }} referrals, each investing ${{ $referralBenefitsSettings['minimum_investment_per_referral'] }}+ for video access</li>
                                <li><strong>Dynamic Bonuses:</strong> Each qualified user gets random bonus percentages within your set ranges</li>
                                <li><strong>Transfer Bonus:</strong> Extra money when transferring to others</li>
                                <li><strong>Receive Bonus:</strong> Extra money when receiving from others</li>
                                <li><strong>Withdraw Reduction:</strong> Lower withdrawal fees</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-warning" onclick="recalculateQualifications()">
                                    <i class="fas fa-sync me-2"></i>Recalculate All Users
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Qualified Users -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Recent Qualified Users
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentQualified->count() > 0)
                        @foreach($recentQualified as $benefit)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-16">
                                            <i class="bx bx-user-check"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $benefit->user->username ?? 'Unknown' }}</h6>
                                    <p class="text-muted mb-0">
                                        {{ $benefit->qualified_referrals_count }} referrals
                                        <br>
                                        <small>Qualified: {{ $benefit->qualified_at->format('M d, Y') }}</small>
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.referral-benefits.user-details', $benefit->user_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="btn btn-link">
                                View All Qualified Users <i class="bx bx-right-arrow-alt"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="bx bx-user-x fs-48"></i>
                            <p class="mt-2">No qualified users yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="btn btn-outline-primary">
                            <i class="fas fa-users me-2"></i>View Qualified Users
                        </a>
                        <a href="{{ route('admin.referral-benefits.bonus-transactions') }}" class="btn btn-outline-info">
                            <i class="fas fa-history me-2"></i>Bonus Transactions
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="exportReport()">
                            <i name="fas fa-download me-2"></i>Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
function recalculateQualifications() {
    Swal.fire({
        title: 'Recalculate User Qualifications?',
        text: 'This will check all users and update their qualification status based on current settings.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, recalculate!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Recalculating...',
                text: 'Please wait while we update user qualifications.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            fetch('{{ route("admin.referral-benefits.recalculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire('Error!', 'An error occurred while recalculating.', 'error');
            });
        }
    });
}

function exportReport() {
    // Implementation for exporting report
    Swal.fire('Info', 'Export functionality will be implemented soon.', 'info');
}
</script>
@endpush
@endsection
