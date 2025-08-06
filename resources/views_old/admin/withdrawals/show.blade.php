<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.index') }}">Withdrawals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title">Withdrawal Information</h6>
                    <div class="d-flex gap-2">
                        @if($withdrawal->status == 2)
                            <button class="btn btn-sm btn-success" onclick="approveWithdrawal({{ $withdrawal->id }})">
                                <i class="ri-check-line me-1"></i>Approve
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectWithdrawal({{ $withdrawal->id }})">
                                <i class="ri-close-line me-1"></i>Reject
                            </button>
                        @endif
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-sm btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Withdrawal ID</label>
                                <div class="fw-semibold">#{{ $withdrawal->id }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Transaction ID</label>
                                <div class="fw-semibold">
                                    <span class="badge bg-light text-dark">{{ $withdrawal->trx }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Withdrawal Type</label>
                                <div>
                                    <span class="badge bg-{{ $withdrawal->withdraw_type == 'deposit' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($withdrawal->withdraw_type ?? 'deposit') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    @if($withdrawal->status == 2)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($withdrawal->status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Amount</label>
                                <div class="fw-semibold fs-5">${{ number_format($withdrawal->amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Charge</label>
                                <div class="fw-semibold fs-5 text-danger">${{ number_format($withdrawal->charge, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Final Amount</label>
                                <div class="fw-semibold fs-4 text-success">${{ number_format($withdrawal->final_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Request Date</label>
                                <div class="fw-semibold">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Processed Date</label>
                                <div class="fw-semibold">
                                    @if($withdrawal->processed_at)
                                        {{ $withdrawal->processed_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">Not processed yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($withdrawal->admin_feedback)
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-muted">Admin Feedback</label>
                                <div class="alert alert-info">
                                    {{ $withdrawal->admin_feedback }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Withdrawal Information -->
                    @php
                        $info = json_decode($withdrawal->withdraw_information);
                    @endphp
                    @if($info)
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Withdrawal Details</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                @if(isset($info->method))
                                <tr>
                                    <td class="fw-semibold">Method</td>
                                    <td>{{ $info->method }}</td>
                                </tr>
                                @endif
                                @if(isset($info->account_number))
                                <tr>
                                    <td class="fw-semibold">Account Number</td>
                                    <td>{{ $info->account_number }}</td>
                                </tr>
                                @endif
                                @if(isset($info->account_name))
                                <tr>
                                    <td class="fw-semibold">Account Name</td>
                                    <td>{{ $info->account_name }}</td>
                                </tr>
                                @endif
                                @if(isset($info->bank_name))
                                <tr>
                                    <td class="fw-semibold">Bank Name</td>
                                    <td>{{ $info->bank_name }}</td>
                                </tr>
                                @endif
                                @if(isset($info->routing_number))
                                <tr>
                                    <td class="fw-semibold">Routing Number</td>
                                    <td>{{ $info->routing_number }}</td>
                                </tr>
                                @endif
                                @if(isset($info->address))
                                <tr>
                                    <td class="fw-semibold">Address</td>
                                    <td>{{ $info->address }}</td>
                                </tr>
                                @endif
                                @if(isset($info->wallet_breakdown))
                                <tr>
                                    <td class="fw-semibold">Wallet Breakdown</td>
                                    <td>
                                        @foreach($info->wallet_breakdown as $wallet => $amount)
                                            <div>{{ ucfirst(str_replace('_', ' ', $wallet)) }}: ${{ number_format($amount, 2) }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                                @foreach((array)$info as $key => $value)
                                    @if(!in_array($key, ['method', 'account_number', 'account_name', 'bank_name', 'routing_number', 'address', 'wallet_breakdown']) && !is_array($value) && !is_object($value))
                                    <tr>
                                        <td class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- User Information -->
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">User Information</h6>
                </div>
                <div class="card-body">
                    @if($withdrawal->user)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial bg-primary">
                                {{ substr($withdrawal->user->username, 0, 2) }}
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-1">{{ $withdrawal->user->username }}</h6>
                            <p class="text-muted mb-0">{{ $withdrawal->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">User ID</label>
                                <div class="fw-semibold">#{{ $withdrawal->user->id }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Join Date</label>
                                <div class="fw-semibold">{{ $withdrawal->user->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Deposit Wallet</label>
                                <div class="fw-semibold text-success">${{ number_format($withdrawal->user->deposit_wallet ?? 0, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Interest Wallet</label>
                                <div class="fw-semibold text-info">${{ number_format($withdrawal->user->interest_wallet ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <a href="{{ route('admin.users.show', $withdrawal->user->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-user-line me-1"></i>View User Profile
                        </a>
                    </div>
                    @else
                    <div class="text-center text-muted">
                        <i class="ri-user-line fs-2"></i>
                        <p>User information not available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Withdrawal History -->
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">Recent Withdrawals</h6>
                </div>
                <div class="card-body">
                    @if($withdrawal->user)
                        @php
                            $recentWithdrawals = $withdrawal->user->withdrawals()
                                ->where('id', '!=', $withdrawal->id)
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($recentWithdrawals->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentWithdrawals as $recent)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">${{ number_format($recent->final_amount, 2) }}</h6>
                                            <small class="text-muted">{{ $recent->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <div>
                                            @if($recent->status == 2)
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($recent->status == 1)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="ri-wallet-line fs-2"></i>
                                <p>No other withdrawals found</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="ri-wallet-line fs-2"></i>
                            <p>User information not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Approve Withdrawal</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <strong>Confirm Approval:</strong> You are about to approve this withdrawal for ${{ number_format($withdrawal->final_amount, 2) }}.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Feedback (Optional)</label>
                            <textarea name="admin_feedback" class="form-control" rows="3" 
                                      placeholder="Add any feedback for approval...">Withdrawal approved by admin</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Reject Withdrawal</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> Rejecting this withdrawal will restore ${{ number_format($withdrawal->amount, 2) }} to the user's account.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_feedback" class="form-control" rows="3" 
                                      placeholder="Please provide a detailed reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function approveWithdrawal(id) {
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            const form = document.getElementById('approveForm');
            form.action = `/admin/withdrawals/${id}/approve`;
            modal.show();
        }

        function rejectWithdrawal(id) {
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            const form = document.getElementById('rejectForm');
            form.action = `/admin/withdrawals/${id}/reject`;
            modal.show();
        }
    </script>
    @endsection
</x-layout>
