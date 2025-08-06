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

    <!-- Quick Stats -->
    <div class="row mb-4 my-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-success">
                                <i class="ti ti-check fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($withdrawals->total()) }}</h6>
                            <span class="fs-12 text-muted">Approved Withdrawals</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-info">
                                <i class="ti ti-wallet fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">${{ number_format($withdrawals->sum('amount'), 2) }}</h6>
                            <span class="fs-12 text-muted">Total Amount</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-primary">
                                <i class="ti ti-credit-card fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($withdrawals->where('withdraw_type', 'deposit')->count()) }}</h6>
                            <span class="fs-12 text-muted">Deposit Type</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-secondary">
                                <i class="ti ti-wallet fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">{{ number_format($withdrawals->where('withdraw_type', 'wallet')->count()) }}</h6>
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
                        <a href="{{ route('admin.withdrawals.export', ['status' => 1]) }}" class="btn btn-sm btn-success">
                            <i class="ri-download-line me-1"></i>Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($withdrawals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Transaction ID</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Charge</th>
                                        <th>Final Amount</th>
                                        <th>Method</th>
                                        <th>Processed Date</th>
                                        <th>Admin Feedback</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                    <tr>
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
                                            @if($withdrawal->processed_at)
                                                {{ $withdrawal->processed_at->format('M d, Y h:i A') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($withdrawal->admin_feedback)
                                                <span class="text-truncate" style="max-width: 150px;" title="{{ $withdrawal->admin_feedback }}">
                                                    {{ Str::limit($withdrawal->admin_feedback, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="ri-eye-line"></i>
                                            </a>
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
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial bg-light text-muted">
                                    <i class="ri-checkbox-circle-line fs-2"></i>
                                </span>
                            </div>
                            <h5 class="text-muted">No Approved Withdrawals</h5>
                            <p class="text-muted">There are no approved withdrawals to display.</p>
                            <a href="{{ route('admin.withdrawals.pending') }}" class="btn btn-primary">
                                <i class="ri-time-line me-1"></i>View Pending Withdrawals
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Filter Approved Withdrawals</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="GET" action="{{ route('admin.withdrawals.approved') }}">
                    <div class="modal-body">
                        <div class="row">
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
                                    <label class="form-label">Date Range</label>
                                    <input type="date" name="from_date" class="form-control mb-2" 
                                           placeholder="From Date" value="{{ request('from_date') }}">
                                    <input type="date" name="to_date" class="form-control" 
                                           placeholder="To Date" value="{{ request('to_date') }}">
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
                        <a href="{{ route('admin.withdrawals.approved') }}" class="btn btn-warning">Clear</a>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
</x-layout>
