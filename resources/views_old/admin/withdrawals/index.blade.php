<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
@section('content')   
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 my-4">
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-primary">
                                <i class="ti ti-wallet fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['total']) }}</h6>
                            <span class="fs-12 text-muted">Total Withdrawals</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-warning">
                                <i class="ti ti-clock fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['pending']) }}</h6>
                            <span class="fs-12 text-muted">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-success">
                                <i class="ti ti-check fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['approved']) }}</h6>
                            <span class="fs-12 text-muted">Approved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-danger">
                                <i class="ti ti-x fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['rejected']) }}</h6>
                            <span class="fs-12 text-muted">Rejected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-info">
                                <i class="ti ti-credit-card fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['deposit_withdrawals']) }}</h6>
                            <span class="fs-12 text-muted">Deposit Type</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-secondary">
                                <i class="ti ti-wallet fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($stats['wallet_withdrawals']) }}</h6>
                            <span class="fs-12 text-muted">Wallet Type</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        {{ $pageTitle }}
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="ri-filter-line me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.withdrawals.export') }}" class="btn btn-sm btn-success">
                            <i class="ri-download-line me-1"></i>Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap w-100" id="withdrawalsTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Charge</th>
                                    <th>Final Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input withdrawal-checkbox" value="{{ $withdrawal->id }}">
                                    </td>
                                    <td>{{ $withdrawal->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial bg-light text-dark">
                                                    {{ substr($withdrawal->user->username ?? 'N/A', 0, 2) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $withdrawal->user->username ?? 'N/A' }}</span>
                                                <br>
                                                <small class="text-muted">{{ $withdrawal->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $withdrawal->trx }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $withdrawal->withdraw_type == 'deposit' ? 'info' : 'secondary' }}">
                                            {{ ucfirst($withdrawal->withdraw_type ?? 'deposit') }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                    <td>${{ number_format($withdrawal->charge, 2) }}</td>
                                    <td class="fw-semibold">${{ number_format($withdrawal->final_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $info = json_decode($withdrawal->withdraw_information);
                                        @endphp
                                        {{ $info->method ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if($withdrawal->status == 2)
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($withdrawal->status == 1)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $withdrawal->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @if($withdrawal->status == 2)
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="approveWithdrawal({{ $withdrawal->id }})" 
                                                        title="Approve">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="rejectWithdrawal({{ $withdrawal->id }})" 
                                                        title="Reject">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $withdrawals->firstItem() ?? 0 }} to {{ $withdrawals->lastItem() ?? 0 }} of {{ $withdrawals->total() ?? 0 }} results
                        </div>
                        <div>
                            {{ $withdrawals->links() }}
                        </div>
                    </div>
                    
                    <!-- Bulk Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-3" id="bulkActions" style="display: none !important;">
                        <div>
                            <span id="selectedCount">0</span> items selected
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-success" onclick="bulkApprove()">
                                <i class="ri-check-line me-1"></i>Approve Selected
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="bulkReject()">
                                <i class="ri-close-line me-1"></i>Reject Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Filter Withdrawals</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="GET" action="{{ route('admin.withdrawals.index') }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Withdrawal Type</label>
                                    <select name="withdraw_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="deposit" {{ request('withdraw_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                        <option value="wallet" {{ request('withdraw_type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search by transaction ID, username, or email..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-warning">Clear</a>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </form>
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
                            <strong>Warning:</strong> Rejecting this withdrawal will restore the funds to the user's account.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="admin_feedback" class="form-control" rows="3" 
                                      placeholder="Please provide a reason for rejection..." required></textarea>
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

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="bulkActionTitle">Bulk Action</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkActionForm" method="POST" action="{{ route('admin.withdrawals.bulk-action') }}">
                    @csrf
                    <input type="hidden" name="action" id="bulkActionType">
                    <input type="hidden" name="withdrawals" id="bulkWithdrawals">
                    <div class="modal-body">
                        <div id="bulkApproveContent" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Admin Feedback (Optional)</label>
                                <textarea name="admin_feedback" class="form-control" rows="3" 
                                          placeholder="Add feedback for bulk approval...">Bulk approved by admin</textarea>
                            </div>
                        </div>
                        <div id="bulkRejectContent" style="display: none;">
                            <div class="alert alert-warning">
                                <strong>Warning:</strong> Rejecting these withdrawals will restore the funds to the users' accounts.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="admin_feedback" class="form-control" rows="3" 
                                          placeholder="Please provide a reason for rejection..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="bulkActionBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const withdrawalCheckboxes = document.querySelectorAll('.withdrawal-checkbox');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            selectAllCheckbox.addEventListener('change', function() {
                withdrawalCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            withdrawalCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.withdrawal-checkbox:checked');
                const count = checkedBoxes.length;
                
                selectedCount.textContent = count;
                
                if (count > 0) {
                    bulkActions.style.display = 'flex';
                } else {
                    bulkActions.style.display = 'none';
                }
                
                // Update select all checkbox state
                selectAllCheckbox.indeterminate = count > 0 && count < withdrawalCheckboxes.length;
                selectAllCheckbox.checked = count === withdrawalCheckboxes.length;
            }
        });

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

        function bulkApprove() {
            const checkedBoxes = document.querySelectorAll('.withdrawal-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one withdrawal to approve.');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            document.getElementById('bulkActionType').value = 'approve';
            document.getElementById('bulkWithdrawals').value = JSON.stringify(ids);
            document.getElementById('bulkActionTitle').textContent = 'Bulk Approve Withdrawals';
            document.getElementById('bulkApproveContent').style.display = 'block';
            document.getElementById('bulkRejectContent').style.display = 'none';
            document.getElementById('bulkActionBtn').className = 'btn btn-success';
            document.getElementById('bulkActionBtn').textContent = 'Approve Selected';

            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            modal.show();
        }

        function bulkReject() {
            const checkedBoxes = document.querySelectorAll('.withdrawal-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one withdrawal to reject.');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            document.getElementById('bulkActionType').value = 'reject';
            document.getElementById('bulkWithdrawals').value = JSON.stringify(ids);
            document.getElementById('bulkActionTitle').textContent = 'Bulk Reject Withdrawals';
            document.getElementById('bulkApproveContent').style.display = 'none';
            document.getElementById('bulkRejectContent').style.display = 'block';
            document.getElementById('bulkActionBtn').className = 'btn btn-danger';
            document.getElementById('bulkActionBtn').textContent = 'Reject Selected';

            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            modal.show();
        }
    </script>
    @endsection
</x-layout>
