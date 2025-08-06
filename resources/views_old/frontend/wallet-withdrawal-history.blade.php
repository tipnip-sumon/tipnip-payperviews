<x-smart_layout>

@section('title', $pageTitle)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.withdraw.wallet') }}">Wallet Withdrawal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
            <p class="fw-semibold fs-18 mb-0">Your Wallet Withdrawal History</p>
        </div>
        <div>
            <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-primary">
                <i class="fe fe-plus me-2"></i>
                New Withdrawal
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="fe fe-file-text fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Requests</p>
                                    <h4 class="fw-semibold mb-1">{{ $stats['total_requests'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success">
                                <i class="fe fe-check-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Approved</p>
                                    <h4 class="fw-semibold mb-1">{{ $stats['approved_requests'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning">
                                <i class="fe fe-clock fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Pending</p>
                                    <h4 class="fw-semibold mb-1">{{ $stats['pending_requests'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info">
                                <i class="fe fe-dollar-sign fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Withdrawn</p>
                                    <h4 class="fw-semibold mb-1">${{ number_format($stats['total_withdrawn'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal History Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        <i class="fe fe-list me-2"></i>
                        Wallet Withdrawal History
                    </div>
                </div>
                <div class="card-body">
                    @if($withdrawals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr>
                                            <td>
                                                <div>
                                                    <p class="mb-0">{{ $withdrawal->created_at->format('M d, Y') }}</p>
                                                    <small class="text-muted">{{ $withdrawal->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $withdrawal->trx }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>${{ number_format($withdrawal->amount, 2) }}</strong>
                                                    @if($withdrawal->charge > 0)
                                                        <br><small class="text-muted">Fee: ${{ number_format($withdrawal->charge, 2) }}</small>
                                                        <br><small class="text-success">Net: ${{ number_format($withdrawal->final_amount, 2) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $info = json_decode($withdrawal->withdraw_information);
                                                @endphp
                                                <span class="badge bg-info-transparent">
                                                    {{ $info->method ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($withdrawal->status == 0)
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($withdrawal->status == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#withdrawalModal{{ $withdrawal->id }}">
                                                    <i class="fe fe-eye"></i> View
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="withdrawalModal{{ $withdrawal->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Withdrawal Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <table class="table table-borderless">
                                                                            <tr>
                                                                                <td><strong>Transaction ID:</strong></td>
                                                                                <td>{{ $withdrawal->trx }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Date:</strong></td>
                                                                                <td>{{ $withdrawal->created_at->format('M d, Y h:i A') }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Amount:</strong></td>
                                                                                <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Fee:</strong></td>
                                                                                <td>${{ number_format($withdrawal->charge, 2) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Net Amount:</strong></td>
                                                                                <td><strong>${{ number_format($withdrawal->final_amount, 2) }}</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Method:</strong></td>
                                                                                <td>{{ $info->method ?? 'N/A' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Status:</strong></td>
                                                                                <td>
                                                                                    @if($withdrawal->status == 0)
                                                                                        <span class="badge bg-warning">Pending</span>
                                                                                    @elseif($withdrawal->status == 1)
                                                                                        <span class="badge bg-success">Approved</span>
                                                                                    @else
                                                                                        <span class="badge bg-danger">Rejected</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        
                                                                        @if(isset($info->details))
                                                                            <div class="mt-3">
                                                                                <strong>Account Details:</strong>
                                                                                <div class="border p-2 mt-1 bg-light">
                                                                                    {{ $info->details }}
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        @if(isset($info->wallet_breakdown))
                                                                            <div class="mt-3">
                                                                                <strong>Wallet Breakdown:</strong>
                                                                                <div class="border p-2 mt-1 bg-light">
                                                                                    <small>
                                                                                        Deposit Wallet: ${{ number_format($info->wallet_breakdown->deposit_wallet, 2) }}<br>
                                                                                        Interest Wallet: ${{ number_format($info->wallet_breakdown->interest_wallet, 2) }}<br>
                                                                                        Total Balance: ${{ number_format($info->wallet_breakdown->total_balance, 2) }}
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($withdrawals->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $withdrawals->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('assets/images/svgs/empty-folder.svg') }}" alt="No data" class="mb-3" style="width: 120px;">
                            <h5>No Wallet Withdrawals Found</h5>
                            <p class="text-muted">You haven't made any wallet withdrawal requests yet.</p>
                            <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>
                                Make First Withdrawal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</x-smart_layout>
