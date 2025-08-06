<x-layout>
    @section('title', $pageTitle)
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb my-4">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4 my-4">
        <!-- User Information -->
        <div class="col-xl-4 col-lg-5 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit User
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar_url) }}" alt="User Avatar" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mb-3" style="width: 100px; height: 100px; font-size: 2rem;">
                                {{ strtoupper(substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1)) }}
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h5>
                        <p class="text-muted mb-2">{{ $user->username }}</p>
                        @if($user->status == 1)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Active
                            </span>
                        @elseif($user->status == 0)
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Inactive
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="fas fa-ban me-1"></i>Banned
                            </span>
                        @endif
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-envelope me-2"></i>Email</span>
                            <span class="fw-medium text-end">{{ $user->email }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-phone me-2"></i>Mobile</span>
                            <span class="fw-medium text-end">{{ $user->mobile ?: 'Not provided' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-globe me-2"></i>Country</span>
                            <span class="fw-medium text-end">{{ $user->country ?: 'Not provided' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-user-plus me-2"></i>Referrals</span>
                            <span class="fw-medium text-end">{{ $userStats['referrals_count'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-handshake me-2"></i>Referred By</span>
                            <span class="fw-medium text-end">{{ $sponsor ? $sponsor : 'Direct' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-calendar-plus me-2"></i>Joined</span>
                            <span class="fw-medium text-end">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i>Last Login</span>
                            <span class="fw-medium text-end">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Status -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Verification Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-envelope me-2 text-primary"></i>Email Verification</span>
                            @if($user->ev)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Not Verified
                                </span>
                            @endif
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-sms me-2 text-info"></i>SMS Verification</span>
                            @if($user->sv)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Not Verified
                                </span>
                            @endif
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-id-card me-2 text-warning"></i>KYC Verification</span>
                            @if($user->kv)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Not Verified
                                </span>
                            @endif
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-lock me-2 text-secondary"></i>Two Factor Auth</span>
                            @if($user->ts ?? false)
                                <span class="badge bg-info">
                                    <i class="fas fa-shield-alt me-1"></i>Enabled
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-shield me-1"></i>Disabled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Activity -->
        <div class="col-xl-8 col-lg-7 col-md-12">
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-wallet fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Deposit Wallet</h6>
                            <h4 class="mb-0">${{ number_format($user->deposit_wallet ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-chart-line fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Interest Wallet</h6>
                            <h4 class="mb-0">${{ number_format($user->interest_wallet ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-arrow-down fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Total Deposits</h6>
                            <h4 class="mb-0">${{ number_format($userStats['total_deposits'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-arrow-up fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Total Withdrawals</h6>
                            <h4 class="mb-0">${{ number_format($userStats['total_withdrawals'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-hourglass-half fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Pending Withdrawals</h6>
                            <h4 class="mb-0">${{ number_format($userStats['pending_withdrawals'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card bg-purple text-white h-100" style="background-color: #6f42c1 !important;">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-coins fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Total Investments</h6>
                            <h4 class="mb-0">${{ number_format($userStats['total_investments'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card bg-orange text-white h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-users fs-1 mb-2 opacity-75"></i>
                            <h6 class="mb-1 text-white-50">Total Referrals</h6>
                            <h4 class="mb-0">{{ number_format($userStats['referrals_count']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Tabs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-justified mb-3" id="activityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="deposits-tab" data-bs-toggle="pill" data-bs-target="#deposits" type="button" role="tab">
                                <i class="fas fa-arrow-down me-1"></i>
                                <span class="d-none d-sm-inline">Recent </span>Deposits
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="withdrawals-tab" data-bs-toggle="pill" data-bs-target="#withdrawals" type="button" role="tab">
                                <i class="fas fa-arrow-up me-1"></i>
                                <span class="d-none d-sm-inline">Recent </span>Withdrawals
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="transactions-tab" data-bs-toggle="pill" data-bs-target="#transactions" type="button" role="tab">
                                <i class="fas fa-exchange-alt me-1"></i>
                                <span class="d-none d-sm-inline">Recent </span>Transactions
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="activityTabContent">
                        <!-- Deposits Tab -->
                        <div class="tab-pane fade show active" id="deposits" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-dollar-sign me-1"></i>Amount</th>
                                            <th class="d-none d-md-table-cell"><i class="fas fa-credit-card me-1"></i>Gateway</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                            <th class="d-none d-sm-table-cell"><i class="fas fa-calendar me-1"></i>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentDeposits as $deposit)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-success">${{ number_format($deposit->amount, 2) }}</span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="badge bg-light text-dark">{{ $deposit->gateway->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($deposit->status == 1)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                @elseif($deposit->status == 2)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="d-none d-sm-table-cell text-muted">
                                                {{ $deposit->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>No deposits found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Withdrawals Tab -->
                        <div class="tab-pane fade" id="withdrawals" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-dollar-sign me-1"></i>Amount</th>
                                            <th class="d-none d-md-table-cell"><i class="fas fa-university me-1"></i>Method</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                            <th class="d-none d-sm-table-cell"><i class="fas fa-calendar me-1"></i>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentWithdrawals as $withdrawal)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-warning">${{ number_format($withdrawal->amount, 2) }}</span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="badge bg-light text-dark">{{ $withdrawal->method->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($withdrawal->status == 1)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                @elseif($withdrawal->status == 2)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="d-none d-sm-table-cell text-muted">
                                                {{ $withdrawal->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>No withdrawals found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade" id="transactions" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-exchange-alt me-1"></i>Type</th>
                                            <th><i class="fas fa-dollar-sign me-1"></i>Amount</th>
                                            <th class="d-none d-md-table-cell"><i class="fas fa-file-alt me-1"></i>Description</th>
                                            <th class="d-none d-sm-table-cell"><i class="fas fa-calendar me-1"></i>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                @if($transaction->trx_type == '+')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-plus me-1"></i>Credit
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-minus me-1"></i>Debit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold {{ $transaction->trx_type == '+' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->trx_type }}${{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="text-truncate" style="max-width: 200px;" title="{{ $transaction->details ?? 'N/A' }}">
                                                    {{ Str::limit($transaction->details ?? 'N/A', 30) }}
                                                </span>
                                            </td>
                                            <td class="d-none d-sm-table-cell text-muted">
                                                {{ $transaction->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <br>No transactions found
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

    <style>
        /* Custom responsive styles */
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }
            
            .badge {
                font-size: 0.7rem;
            }
            
            .fs-1 {
                font-size: 2rem !important;
            }
            
            .list-group-item {
                padding: 0.5rem 0;
            }
            
            .nav-pills .nav-link {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
        }
        
        /* Ensure equal height cards */
        .row.g-3 .card,
        .row.g-4 .card {
            height: 100%;
        }
        
        /* Smooth transitions */
        .card, .badge, .btn {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Table improvements */
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Tab improvements */
        .nav-pills .nav-link {
            border-radius: 0.375rem;
            margin: 0 2px;
        }
        
        .nav-pills .nav-link.active {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>

    @endsection
</x-layout>
