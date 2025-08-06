@extends('components.layout')
@section('title', 'Bonus Transactions - Referral Benefits')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Bonus Transactions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.referral-benefits.index') }}">Referral Benefits</a></li>
                        <li class="breadcrumb-item active">Bonus Transactions</li>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Bonuses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value">{{ number_format($stats['total_bonuses'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-dollar text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Transfer Bonuses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value">{{ number_format($stats['transfer_bonuses'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-transfer text-primary"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Receive Bonuses</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value">{{ number_format($stats['receive_bonuses'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-wallet text-info"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Withdraw Reductions</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value">{{ number_format($stats['withdraw_reductions'], 2) }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-minus-circle text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.referral-benefits.bonus-transactions') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" 
                                       value="{{ request('username') }}" placeholder="Search username...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Transaction Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="transfer_bonus" {{ request('type') == 'transfer_bonus' ? 'selected' : '' }}>Transfer Bonus</option>
                                    <option value="receive_bonus" {{ request('type') == 'receive_bonus' ? 'selected' : '' }}>Receive Bonus</option>
                                    <option value="withdraw_reduction" {{ request('type') == 'withdraw_reduction' ? 'selected' : '' }}>Withdraw Reduction</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Min Amount</label>
                                <input type="number" name="min_amount" class="form-control" step="0.01"
                                       value="{{ request('min_amount') }}" placeholder="0.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" 
                                       value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" 
                                       value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search"></i>
                                    </button>
                                    <a href="{{ route('admin.referral-benefits.bonus-transactions') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Bonus Transactions ({{ $transactions->total() }})
                        </h5>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportData()">
                                <i class="fas fa-download me-1"></i>Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Percentage</th>
                                        <th>Original Amount</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <span class="font-monospace">#{{ $transaction->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs flex-shrink-0 me-2">
                                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-12">
                                                            {{ strtoupper(substr($transaction->userBenefit->user->username ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $transaction->userBenefit->user->username ?? 'Unknown' }}</h6>
                                                        <small class="text-muted">{{ $transaction->userBenefit->user->email ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($transaction->type == 'transfer_bonus')
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        <i class="fas fa-exchange-alt me-1"></i>Transfer Bonus
                                                    </span>
                                                @elseif($transaction->type == 'receive_bonus')
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="fas fa-wallet me-1"></i>Receive Bonus
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        <i class="fas fa-minus-circle me-1"></i>Withdraw Reduction
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type == 'withdraw_reduction')
                                                    <span class="text-warning fw-semibold">
                                                        -${{ number_format($transaction->amount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-success fw-semibold">
                                                        +${{ number_format($transaction->amount, 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    {{ $transaction->percentage_used }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    ${{ number_format($transaction->original_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $transaction->description }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $transaction->created_at->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item" onclick="viewTransaction({{ $transaction->id }})">
                                                                <i class="bx bx-show me-2"></i>View Details
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="{{ route('admin.referral-benefits.user-details', $transaction->userBenefit->user_id) }}">
                                                                <i class="bx bx-user me-2"></i>View User
                                                            </a>
                                                        </li>
                                                        @if($transaction->related_transaction_id)
                                                            <li>
                                                                <button class="dropdown-item" onclick="findRelated('{{ $transaction->related_transaction_id }}')">
                                                                    <i class="bx bx-link me-2"></i>Related Transaction
                                                                </button>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <p class="text-muted">
                                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} 
                                    of {{ $transactions->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $transactions->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="avatar-xl mx-auto mb-4">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-36">
                                    <i class="bx bx-receipt"></i>
                                </div>
                            </div>
                            <h5>No Transactions Found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['username', 'type', 'min_amount', 'from_date', 'to_date']))
                                    No transactions match your current filters. Try adjusting your search criteria.
                                @else
                                    No bonus transactions have been recorded yet.
                                @endif
                            </p>
                            @if(request()->hasAny(['username', 'type', 'min_amount', 'from_date', 'to_date']))
                                <a href="{{ route('admin.referral-benefits.bonus-transactions') }}" class="btn btn-outline-primary">
                                    <i class="bx bx-refresh me-2"></i>Clear Filters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
function viewTransaction(transactionId) {
    // Show loading
    document.getElementById('transactionDetails').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
    modal.show();

    // Load transaction details
    fetch(`/admin/referral-benefits/transaction-details/${transactionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('transactionDetails').innerHTML = data.html;
            } else {
                document.getElementById('transactionDetails').innerHTML = '<div class="alert alert-danger">Error loading transaction details.</div>';
            }
        })
        .catch(error => {
            document.getElementById('transactionDetails').innerHTML = '<div class="alert alert-danger">Error loading transaction details.</div>';
        });
}

function findRelated(transactionId) {
    // Highlight and scroll to related transaction if on same page
    const row = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
    if (row) {
        row.classList.add('table-warning');
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        setTimeout(() => {
            row.classList.remove('table-warning');
        }, 3000);
    } else {
        // Search for the transaction
        Swal.fire('Info', 'Searching for related transaction...', 'info');
        // Could implement search functionality here
    }
}

function exportData() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    
    // Create download link
    const downloadUrl = `{{ route('admin.referral-benefits.bonus-transactions') }}?${params.toString()}`;
    window.open(downloadUrl, '_blank');
}

// Add transaction ID to rows for easy finding
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const idCell = row.querySelector('td:first-child span');
        if (idCell) {
            const id = idCell.textContent.replace('#', '');
            row.setAttribute('data-transaction-id', id);
        }
    });
});
</script>
@endpush
@endsection
