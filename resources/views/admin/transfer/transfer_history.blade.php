<x-layout>
    @section('top_title','Admin Transfer History')
    @section('title','Transfer History')
    
    @push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    @section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h5 class="card-title text-white mb-0">
                    <i class="fas fa-history me-2"></i>
                    Transfer History
                </h5>
                <a href="{{ route('admin.transfer_member') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    New Transfer
                </a>
            </div>
            <div class="card-body">
                <!-- Advanced Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-light">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 d-flex align-items-center">
                                    <i class="fas fa-filter me-2"></i>
                                    Advanced Filters & Export
                                    <button class="btn btn-sm btn-outline-primary ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </h6>
                            </div>
                            <div class="collapse show" id="filterCollapse">
                                <div class="card-body">
                                    <form id="filterForm" method="GET">
                                        <div class="row g-3">
                                            <!-- Quick Filters -->
                                            <div class="col-md-3">
                                                <label class="form-label">Quick Filter</label>
                                                <select name="filter" class="form-select" id="quickFilter">
                                                    <option value="">All Time</option>
                                                    <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                                                    <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>This Week</option>
                                                    <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>This Month</option>
                                                    <option value="yearly" {{ request('filter') == 'yearly' ? 'selected' : '' }}>This Year</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Date Range -->
                                            <div class="col-md-3">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">End Date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                            </div>
                                            
                                            <!-- Search -->
                                            <div class="col-md-3">
                                                <label class="form-label">Search</label>
                                                <input type="text" name="search" class="form-control" placeholder="User, amount, note..." value="{{ request('search') }}">
                                            </div>
                                            
                                            <!-- Amount Range -->
                                            <div class="col-md-3">
                                                <label class="form-label">Min Amount</label>
                                                <input type="number" name="min_amount" class="form-control" placeholder="0.00" step="0.01" value="{{ request('min_amount') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Max Amount</label>
                                                <input type="number" name="max_amount" class="form-control" placeholder="0.00" step="0.01" value="{{ request('max_amount') }}">
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="col-md-6">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search me-1"></i>Filter
                                                    </button>
                                                    <a href="{{ route('admin.transfer_history') }}" class="btn btn-secondary">
                                                        <i class="fas fa-times me-1"></i>Clear
                                                    </a>
                                                    <button type="button" class="btn btn-success" onclick="exportTransfers('excel')">
                                                        <i class="fas fa-file-excel me-1"></i>Excel
                                                    </button>
                                                    <button type="button" class="btn btn-info" onclick="exportTransfers('csv')">
                                                        <i class="fas fa-file-csv me-1"></i>CSV
                                                    </button>
                                                    <a href="{{ route('admin.transfer_reports') }}" class="btn btn-warning">
                                                        <i class="fas fa-chart-bar me-1"></i>Reports
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Summary -->
                @if(isset($stats))
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary text-center">
                            <div class="card-body">
                                <h4 class="text-primary">{{ number_format($stats['filtered_count']) }}</h4>
                                <small class="text-muted">Total Transfers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success text-center">
                            <div class="card-body">
                                <h4 class="text-success">${{ number_format($stats['filtered_amount'], 2) }}</h4>
                                <small class="text-muted">Total Amount</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info text-center">
                            <div class="card-body">
                                <h4 class="text-info">{{ number_format($stats['today_transfers']) }}</h4>
                                <small class="text-muted">Today's Transfers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning text-center">
                            <div class="card-body">
                                <h4 class="text-warning">${{ number_format($stats['today_amount'], 2) }}</h4>
                                <small class="text-muted">Today's Amount</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($transfers->count() > 0)
                    <!-- Transfers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#ID</th>
                                    <th>Date & Time</th>
                                    <th>From Admin</th>
                                    <th>To User</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#{{ $transfer->id }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $transfer->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $transfer->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 35px; height: 35px; font-size: 12px; font-weight: bold;">
                                                    {{ strtoupper(substr($transfer->user_transfer ?? 'A', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $transfer->user_transfer ?? 'Admin' }}</div>
                                                    <small class="text-muted">Admin</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 35px; height: 35px; font-size: 12px; font-weight: bold;">
                                                    {{ strtoupper(substr($transfer->user_receive, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $transfer->user_receive }}</div>
                                                    <small class="text-muted">User</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-success fs-5">
                                                ${{ number_format($transfer->amount, 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($transfer->note)
                                                <span class="text-wrap" style="max-width: 200px; display: inline-block;">
                                                    {{ Str::limit($transfer->note, 50) }}
                                                </span>
                                                @if(strlen($transfer->note) > 50)
                                                    <button type="button" class="btn btn-link btn-sm p-0" 
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $transfer->note }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-muted">No note</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transfer->status)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="showTransferDetails({{ $transfer->id }})"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($transfer->note)
                                                    <button type="button" class="btn btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#noteModal{{ $transfer->id }}"
                                                            title="View Note">
                                                        <i class="fas fa-sticky-note"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Note Modal -->
                                    @if($transfer->note)
                                        <div class="modal fade" id="noteModal{{ $transfer->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Transfer Note #{{ $transfer->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-0">{{ $transfer->note }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of {{ $transfers->total() }} transfers
                        </div>
                        <div>
                            {{ $transfers->links() }}
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exchange-alt fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Transfer History</h4>
                        <p class="text-muted mb-4">No transfers have been made yet.</p>
                        <a href="{{ route('admin.transfer_member') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Make First Transfer
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Transfers</h6>
                                <h3 class="mb-0">{{ $transfers->total() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exchange-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Amount</h6>
                                <h3 class="mb-0">${{ number_format($transfers->sum('amount'), 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">This Month</h6>
                                <h3 class="mb-0">{{ $transfers->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Today</h6>
                                <h3 class="mb-0">{{ $transfers->where('created_at', '>=', now()->startOfDay())->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Details Modal -->
<div class="modal fade" id="transferDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transferDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Show transfer details
function showTransferDetails(transferId) {
    const modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'));
    const content = document.getElementById('transferDetailsContent');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading transfer details...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch transfer details via AJAX
    fetch(`/admin/transfer_details/${transferId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const transfer = data.transfer;
            const transactions = data.transactions || [];
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Transfer Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-bold">Transfer ID:</td>
                                        <td><span class="badge bg-primary">#${transfer.id}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Date & Time:</td>
                                        <td>${new Date(transfer.created_at).toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">From Admin:</td>
                                        <td>${transfer.user_transfer || 'Admin'}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">To User:</td>
                                        <td>${transfer.user_receive}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Amount:</td>
                                        <td><span class="fw-bold text-success fs-5">$${parseFloat(transfer.amount).toFixed(2)}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status:</td>
                                        <td>
                                            <span class="badge ${transfer.status ? 'bg-success' : 'bg-warning'}">
                                                <i class="fas ${transfer.status ? 'fa-check' : 'fa-clock'} me-1"></i>
                                                ${transfer.status ? 'Completed' : 'Pending'}
                                            </span>
                                        </td>
                                    </tr>
                                    ${transfer.note ? `
                                    <tr>
                                        <td class="fw-bold">Note:</td>
                                        <td class="text-wrap">${transfer.note}</td>
                                    </tr>
                                    ` : ''}
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Related Transactions</h6>
                            </div>
                            <div class="card-body">
                                ${transactions.length > 0 ? `
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Amount</th>
                                                    <th>Balance After</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${transactions.map(tx => `
                                                    <tr>
                                                        <td>
                                                            <span class="badge ${tx.trx_type === '+' ? 'bg-success' : 'bg-danger'}">
                                                                ${tx.remark}
                                                            </span>
                                                        </td>
                                                        <td class="${tx.trx_type === '+' ? 'text-success' : 'text-danger'}">
                                                            ${tx.trx_type}$${Math.abs(parseFloat(tx.amount)).toFixed(2)}
                                                        </td>
                                                        <td>$${parseFloat(tx.post_balance).toFixed(2)}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                ` : `
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle mb-2"></i>
                                        <p class="mb-0">No transaction records found</p>
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Transfer Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h5 class="text-primary mb-1">$${parseFloat(transfer.amount).toFixed(2)}</h5>
                                            <small class="text-muted">Transfer Amount</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h5 class="text-success mb-1">${transfer.status ? 'Success' : 'Pending'}</h5>
                                            <small class="text-muted">Status</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h5 class="text-info mb-1">${transactions.length}</h5>
                                            <small class="text-muted">Transactions</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-warning mb-1">${new Date(transfer.created_at).toLocaleDateString()}</h5>
                                        <small class="text-muted">Transfer Date</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Error Loading Details</h6>
                    <p class="mb-0">${data.message || 'Unable to load transfer details. Please try again.'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error fetching transfer details:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Network Error</h6>
                <p class="mb-0">Unable to fetch transfer details. Please check your connection and try again.</p>
            </div>
        `;
    });
}

// Export transfers function
function exportTransfers(format) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    // Add export parameter
    formData.append('export', format);
    
    // Create URL with parameters
    const params = new URLSearchParams(formData);
    const exportUrl = `{{ route('admin.transfer_history') }}?${params.toString()}`;
    
    // Show loading
    const loadingToast = showToast('info', 'Preparing export...', 'Please wait while we generate your file.');
    
    // Create a temporary link and trigger download
    window.location.href = exportUrl;
    
    // Hide loading after a delay
    setTimeout(() => {
        hideToast(loadingToast);
        showToast('success', 'Export Started', 'Your download should begin shortly.');
    }, 2000);
}

// Toast notification function
function showToast(type, title, message) {
    const toastId = 'toast-' + Date.now();
    const toastColors = {
        'success': 'bg-success',
        'error': 'bg-danger', 
        'info': 'bg-info',
        'warning': 'bg-warning'
    };
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white ${toastColors[type]} border-0 position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="hideToast('${toastId}')"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    return toastId;
}

function hideToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        const bsToast = bootstrap.Toast.getInstance(toast);
        if (bsToast) {
            bsToast.hide();
        }
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 500);
    }
}

// Auto-submit form on quick filter change
document.addEventListener('DOMContentLoaded', function() {
    const quickFilter = document.getElementById('quickFilter');
    if (quickFilter) {
        quickFilter.addEventListener('change', function() {
            if (this.value) {
                // Clear date inputs when using quick filter
                document.querySelector('input[name="start_date"]').value = '';
                document.querySelector('input[name="end_date"]').value = '';
                document.getElementById('filterForm').submit();
            }
        });
    }
});
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.btn-group-sm > .btn {
    padding: 0.375rem 0.5rem;
    font-size: 0.75rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.opacity-75 {
    opacity: 0.75;
}

.rounded-circle {
    font-size: 0.75rem;
}
</style>
@endpush
</x-layout>
