@extends('components.layout')
@section('title', 'User Referral Benefits Details')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $user->username }}'s Referral Benefits</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.referral-benefits.index') }}">Referral Benefits</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.referral-benefits.qualified-users') }}">Qualified Users</a></li>
                        <li class="breadcrumb-item active">{{ $user->username }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- User Overview -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-4">
                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                            {{ strtoupper(substr($user->username, 0, 2)) }}
                        </span>
                    </div>
                    <h5 class="card-title">{{ $user->username }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    @if($userBenefit)
                        <span class="badge bg-success fs-12 mb-2">Qualified User</span>
                        <p class="text-muted mb-0">
                            <small>Qualified on {{ $userBenefit->qualified_at->format('M d, Y') }}</small>
                        </p>
                    @else
                        <span class="badge bg-warning fs-12 mb-2">Not Qualified</span>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-2">
                                <h5 class="mb-1">{{ $referralStats['total_referrals'] }}</h5>
                                <p class="text-muted mb-0">Total Referrals</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h5 class="mb-1">{{ $referralStats['qualified_referrals'] }}</h5>
                                <p class="text-muted mb-0">Qualified</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border-top">
                                <h5 class="mb-1">${{ number_format($referralStats['total_bonuses'], 2) }}</h5>
                                <p class="text-muted mb-0">Total Bonuses</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border-top">
                                <h5 class="mb-1">{{ $referralStats['bonus_transactions'] }}</h5>
                                <p class="text-muted mb-0">Transactions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($userBenefit)
                <!-- Current Benefits -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-gift me-2"></i>Current Benefits
                            </h6>
                            <div>
                                @if($userBenefit->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-1">{{ $userBenefit->transfer_bonus_percentage }}%</h5>
                                    <p class="text-muted mb-0">Transfer Bonus</p>
                                    <small class="text-muted">Extra when sending money</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                            <i class="fas fa-wallet"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-1">{{ $userBenefit->receive_bonus_percentage }}%</h5>
                                    <p class="text-muted mb-0">Receive Bonus</p>
                                    <small class="text-muted">Extra when receiving money</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                            <i class="fas fa-minus-circle"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-1">{{ $userBenefit->withdraw_reduction_percentage }}%</h5>
                                    <p class="text-muted mb-0">Withdraw Reduction</p>
                                    <small class="text-muted">Lower withdrawal fees</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex gap-2">
                                @if($userBenefit->is_active)
                                    <button class="btn btn-outline-warning btn-sm" onclick="toggleUserStatus({{ $user->id }}, false)">
                                        <i class="fas fa-pause me-1"></i>Deactivate Benefits
                                    </button>
                                @else
                                    <button class="btn btn-outline-success btn-sm" onclick="toggleUserStatus({{ $user->id }}, true)">
                                        <i class="fas fa-play me-1"></i>Activate Benefits
                                    </button>
                                @endif
                                <button class="btn btn-outline-primary btn-sm" onclick="recalculateUser({{ $user->id }})">
                                    <i class="fas fa-sync me-1"></i>Recalculate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Not Qualified Info -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-4">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-24">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                        </div>
                        <h5>User Not Qualified</h5>
                        <p class="text-muted">
                            This user has not met the requirements for referral benefits yet.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="border rounded p-3">
                                    <h6>Requirements:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-flex justify-content-between">
                                            <span>Minimum Referrals:</span>
                                            <span>
                                                {{ $referralStats['qualified_referrals'] }}/{{ $settings['minimum_referrals'] }}
                                                @if($referralStats['qualified_referrals'] >= $settings['minimum_referrals'])
                                                    <i class="fas fa-check text-success ms-1"></i>
                                                @else
                                                    <i class="fas fa-times text-danger ms-1"></i>
                                                @endif
                                            </span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>Min Investment per Referral:</span>
                                            <span>${{ $settings['minimum_investment_per_referral'] }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary mt-3" onclick="recalculateUser({{ $user->id }})">
                            <i class="fas fa-sync me-1"></i>Check Qualification
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Referrals List -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Referrals ({{ $referrals->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($referrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Total Investment</th>
                                        <th>Video Access</th>
                                        <th>Joined</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                        <tr>
                                            <td>{{ $referral->username }}</td>
                                            <td>{{ $referral->email }}</td>
                                            <td>
                                                ${{ number_format($referral->total_investment, 2) }}
                                            </td>
                                            <td>
                                                @if($referral->total_investment >= $settings['minimum_investment_per_referral'])
                                                    <span class="badge bg-success-subtle text-success">Qualified</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Not Qualified</span>
                                                @endif
                                            </td>
                                            <td>{{ $referral->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($referral->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-users fs-48"></i>
                            <p class="mt-2">No referrals yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bonus Transactions -->
    @if($userBenefit)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Recent Bonus Transactions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($bonusTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Percentage</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bonusTransactions as $transaction)
                                            <tr>
                                                <td>
                                                    @if($transaction->type == 'transfer_bonus')
                                                        <span class="badge bg-primary-subtle text-primary">Transfer Bonus</span>
                                                    @elseif($transaction->type == 'receive_bonus')
                                                        <span class="badge bg-success-subtle text-success">Receive Bonus</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning">Withdraw Reduction</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($transaction->type == 'withdraw_reduction')
                                                        <span class="text-warning">-${{ number_format($transaction->amount, 2) }}</span>
                                                    @else
                                                        <span class="text-success">+${{ number_format($transaction->amount, 2) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->percentage_used }}%</td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($bonusTransactions->hasPages())
                                <div class="mt-3">
                                    {{ $bonusTransactions->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-receipt fs-48"></i>
                                <p class="mt-2">No bonus transactions yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('script')
<script>
function toggleUserStatus(userId, isActive) {
    const action = isActive ? 'activate' : 'deactivate';
    const title = isActive ? 'Activate Benefits?' : 'Deactivate Benefits?';
    const text = isActive 
        ? 'This user will start receiving referral benefits again.' 
        : 'This user will temporarily lose referral benefits.';

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/referral-benefits/toggle-user-status/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'An error occurred while updating status.', 'error');
            });
        }
    });
}

function recalculateUser(userId) {
    Swal.fire({
        title: 'Recalculate User Benefits?',
        text: 'This will refresh the user\'s qualification status and benefit percentages.',
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
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/referral-benefits/recalculate-user/${userId}`, {
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
</script>
@endpush
@endsection
