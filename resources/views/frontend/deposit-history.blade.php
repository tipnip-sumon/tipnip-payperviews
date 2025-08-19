<x-smart_layout>

@section('title', $pageTitle)

@section('content')
<div class="container-fluid my-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
            <p class="fw-semibold fs-18 mb-0">Your Deposit Transaction History</p>
        </div>
        <div>
            <a href="{{ route('deposit.index') }}" class="btn btn-primary">
                <i class="fe fe-plus me-2"></i>
                New Deposit
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
                                    <p class="text-muted mb-0">Total Deposits</p>
                                    <h4 class="fw-semibold mb-1">{{ $stats['total_deposits'] }}</h4>
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
                                    <p class="text-muted mb-0">Successful</p>
                                    <h4 class="fw-semibold mb-1">{{ $stats['successful_deposits'] }}</h4>
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
                                    <h4 class="fw-semibold mb-1">{{ $stats['pending_deposits'] }}</h4>
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
                                    <p class="text-muted mb-0">Total Amount</p>
                                    <h4 class="fw-semibold mb-1">${{ number_format($stats['total_amount'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-filter me-2"></i>
                        Filters & Search
                    </div>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Successful</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pending</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Transaction ID, Amount..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-search"></i>
                                    </button>
                                    <a href="{{ route('deposit.history') }}" class="btn btn-outline-secondary">
                                        <i class="fe fe-refresh-cw"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit History Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        <i class="fe fe-list me-2"></i>
                        Deposit History
                    </div>
                </div>
                <div class="card-body">
                    @if($deposits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                        <th>Gateway</th>
                                        <th>Amount</th>
                                        <th>Charge</th>
                                        <th>Payable</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
                                        <tr>
                                            <td>
                                                <div>
                                                    <p class="mb-0">{{ $deposit->created_at->format('M d, Y') }}</p>
                                                    <small class="text-muted">{{ $deposit->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $deposit->trx }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($deposit->gateway && $deposit->gateway->image)
                                                        <img src="{{ asset('assets/images/gateway/' . $deposit->gateway->image) }}" 
                                                             alt="{{ $deposit->gateway->name }}" class="me-2" style="width: 24px; height: 24px;">
                                                    @endif
                                                    <span>{{ $deposit->gateway->name ?? 'NowPayments' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($deposit->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-danger">${{ number_format($deposit->charge, 2) }}</span>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($deposit->final_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-transparent">
                                                    1 {{ $deposit->method_currency }} = {{ $deposit->rate }} {{ $deposit->currency }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($deposit->status == 1)
                                                    <span class="badge bg-success">Successful</span>
                                                @elseif($deposit->status == 2)
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($deposit->status == 3)
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">Initiated</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#depositModal{{ $deposit->id }}">
                                                    <i class="fe fe-eye"></i> View
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="depositModal{{ $deposit->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Deposit Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <table class="table table-borderless">
                                                                            <tr>
                                                                                <td><strong>Transaction ID:</strong></td>
                                                                                <td>{{ $deposit->trx }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Date:</strong></td>
                                                                                <td>{{ $deposit->created_at->format('M d, Y h:i A') }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Gateway:</strong></td>
                                                                                <td>{{ $deposit->gateway->name ?? 'NowPayments' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Amount:</strong></td>
                                                                                <td>${{ number_format($deposit->amount, 2) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Charge:</strong></td>
                                                                                <td>${{ number_format($deposit->charge, 2) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Payable Amount:</strong></td>
                                                                                <td><strong>${{ number_format($deposit->final_amount, 2) }}</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Rate:</strong></td>
                                                                                <td>1 {{ $deposit->method_currency }} = {{ $deposit->rate }} {{ $deposit->currency }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Status:</strong></td>
                                                                                <td>
                                                                                    @if($deposit->status == 1)
                                                                                        <span class="badge bg-success">Successful</span>
                                                                                    @elseif($deposit->status == 2)
                                                                                        <span class="badge bg-warning">Pending</span>
                                                                                    @elseif($deposit->status == 3)
                                                                                        <span class="badge bg-danger">Rejected</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">Initiated</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        </table>

                                                                        @if($deposit->admin_feedback)
                                                                            <div class="mt-3">
                                                                                <strong>Payment Link:</strong>
                                                                                <div class="border p-2 mt-1 bg-light">
                                                                                    {{ $deposit->admin_feedback }}
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        @if($deposit->detail && $deposit->status != 1)
                                                                            <div class="mt-3" style="display: none;">
                                                                                <strong>Payment Details:</strong>
                                                                                <div class="border p-3 mt-1 bg-light rounded">
                                                                                    @php
                                                                                        $details = json_decode($deposit->detail, true);
                                                                                    @endphp
                                                                                    
                                                                                    @if($details && is_array($details))
                                                                                        <div class="row g-2">
                                                                                            @foreach($details as $key => $value)
                                                                                                <div class="col-md-6">
                                                                                                    <div class="d-flex flex-column">
                                                                                                        <small class="text-muted fw-bold">{{ ucwords(str_replace('_', ' ', $key)) }}:</small>
                                                                                                        <span class="text-dark">
                                                                                                            @if($key === 'payment_status')
                                                                                                                <span class="badge bg-{{ $value === 'waiting' ? 'warning' : ($value === 'finished' ? 'success' : 'info') }}">
                                                                                                                    {{ ucfirst($value) }}
                                                                                                                </span>
                                                                                                            @elseif(str_contains($key, 'amount'))
                                                                                                                <strong class="text-primary">{{ $value }}</strong>
                                                                                                            @elseif($key === 'pay_address')
                                                                                                                <code class="small text-break">{{ $value }}</code>
                                                                                                            @else
                                                                                                                {{ $value }}
                                                                                                            @endif
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        <small class="text-muted">{{ $deposit->detail }}</small>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                @if($deposit->status == 2 && $deposit->gateway && $deposit->gateway->name != 'Manual')
                                                                    <a href="{{ route('deposit.confirm') }}?trx={{ $deposit->trx }}" class="btn btn-primary">
                                                                        Complete Payment
                                                                    </a>
                                                                @endif
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
                        @if($deposits->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $deposits->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('assets/images/svgs/empty-folder.svg') }}" alt="No data" class="mb-3" style="width: 120px;">
                            <h5>No Deposits Found</h5>
                            <p class="text-muted">You haven't made any deposits yet or no deposits match your search criteria.</p>
                            <a href="{{ route('deposit.index') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>
                                Make First Deposit
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.type !== 'text') { // Don't auto-submit for text search
                filterForm.submit();
            }
        });
    });
});
</script>
@endsection
</x-smart_layout>
