<x-smart_layout>

@section('title', $pageTitle)

@push('styles')
<!-- DataTables CSS from assets_custom -->
<link rel="stylesheet" href="{{asset('assets_custom/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('assets_custom/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11055;"></div>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2 my-4">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
            <p class="fw-semibold fs-18 mb-0">Complete Transaction History</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.transfer_funds') }}" class="btn btn-primary">
                <i class="fe fe-send me-2"></i>
                New Transfer
            </a>
            <a href="{{ route('user.transfer_history') }}" class="btn btn-outline-info">
                <i class="fe fe-repeat me-2"></i>
                Transfer History
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 my-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="fe fe-list fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Transactions</p>
                                    <h4 class="fw-semibold mb-1">{{ number_format($transactionStats['total_transactions']) }}</h4>
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
                                <i class="fe fe-plus-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Credits</p>
                                    <h4 class="fw-semibold mb-1 text-success">${{ number_format($transactionStats['total_credits'], 2) }}</h4>
                                    <small class="text-muted">{{ $transactionStats['credit_count'] }} transactions</small>
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
                            <span class="avatar avatar-md avatar-rounded bg-danger">
                                <i class="fe fe-minus-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Debits</p>
                                    <h4 class="fw-semibold mb-1 text-danger">${{ number_format($transactionStats['total_debits'], 2) }}</h4>
                                    <small class="text-muted">{{ $transactionStats['debit_count'] }} transactions</small>
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
                            <span class="avatar avatar-md avatar-rounded {{ $transactionStats['net_amount'] >= 0 ? 'bg-success' : 'bg-warning' }}">
                                <i class="fe fe-trending-{{ $transactionStats['net_amount'] >= 0 ? 'up' : 'down' }} fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Net Amount</p>
                                    <h4 class="fw-semibold mb-1 {{ $transactionStats['net_amount'] >= 0 ? 'text-success' : 'text-warning' }}">
                                        ${{ number_format($transactionStats['net_amount'], 2) }}
                                    </h4>
                                    <small class="text-muted">Total charges: ${{ number_format($transactionStats['total_charges'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">This Month's Activity</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-success">${{ number_format($transactionStats['monthly_credits'], 2) }}</h5>
                                <p class="text-muted mb-0">Credits</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h5 class="text-danger">${{ number_format($transactionStats['monthly_debits'], 2) }}</h5>
                                <p class="text-muted mb-0">Debits</p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h6 class="{{ $transactionStats['monthly_net'] >= 0 ? 'text-success' : 'text-warning' }}">
                            Net: ${{ number_format($transactionStats['monthly_net'], 2) }}
                        </h6>
                        <small class="text-muted">{{ $transactionStats['monthly_transactions'] }} transactions this month</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Transaction Types</div>
                </div>
                <div class="card-body">
                    @if($transactionStats['type_breakdown']->count() > 0)
                        @foreach($transactionStats['type_breakdown']->take(5) as $type)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark">{{ ucwords(str_replace('_', ' ', $type->remark)) }}</span>
                                <div class="text-end">
                                    <small class="text-muted">{{ $type->count }} transactions</small>
                                    <br>
                                    <span class="fw-bold">${{ number_format($type->total_amount, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No transaction data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Transaction Table -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">All Transaction History</div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportTransactions()">
                            <i class="fe fe-download me-1"></i>Export
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                            <i class="fe fe-refresh-cw me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select" id="remark_filter">
                                <option value="all">All Types</option>
                                @foreach($transactionTypes as $type)
                                    <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label">Credit/Debit</label>
                            <select class="form-select" id="type_filter">
                                <option value="">All</option>
                                <option value="+">Credit (+)</option>
                                <option value="-">Debit (-)</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date">
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date">
                        </div>
                        <div class="col-lg-3 col-md-12 mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="search_input" placeholder="Search by transaction ID, details, or note...">
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover nowrap" id="all-transactions-table" style="width:100%">
                            <thead class="table-primary">
                                <tr>
                                    <th data-priority="1">Date</th>
                                    <th data-priority="2">ID</th>
                                    <th data-priority="3">Type</th>
                                    <th data-priority="4">Category</th>
                                    <th data-priority="5">Amount</th>
                                    <th data-priority="6">Charge</th>
                                    <th data-priority="7">Balance</th>
                                    <th data-priority="8">Wallet</th>
                                    <th data-priority="9">Details</th>
                                    <th data-priority="10">Note</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<!-- DataTables JavaScript - Using assets_custom for better organization -->
<script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{asset('assets_custom/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets_custom/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets_custom/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets_custom/js/responsive.bootstrap4.min.js')}}"></script>

<script type="text/javascript">
    let transactionsTable;

$(document).ready(function() {
    // Initialize DataTable with enhanced responsiveness
    transactionsTable = $('#all-transactions-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: {
            details: {
                type: 'inline',
                target: 'tr'
            }
        },
        columnDefs: [
            {
                targets: [7, 8, 9], // Wallet, Details, Note columns
                responsivePriority: 10001,
                className: "none"
            },
            {
                targets: [5, 6], // Charge and Balance columns  
                responsivePriority: 9999,
                className: "desktop"
            }
        ],
        ajax: {
            url: "{{ route('user.all_transaction_history') }}",
            data: function(d) {
                d.remark_filter = $('#remark_filter').val();
                d.type_filter = $('#type_filter').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.search_input = $('#search_input').val();
            }
        },
        columns: [
            { 
                data: 'created_at', 
                name: 'created_at', 
                orderable: true,
                responsivePriority: 1,
                className: "all"
            },
            { 
                data: 'trx', 
                name: 'trx', 
                orderable: false,
                responsivePriority: 2,
                className: "all"
            },
            { 
                data: 'trx_type', 
                name: 'trx_type', 
                orderable: false,
                responsivePriority: 3,
                className: "all"
            },
            { 
                data: 'remark', 
                name: 'remark', 
                orderable: false,
                responsivePriority: 4,
                className: "min-tablet-l"
            },
            { 
                data: 'amount', 
                name: 'amount', 
                orderable: true,
                responsivePriority: 5,
                className: "all"
            },
            { 
                data: 'charge', 
                name: 'charge', 
                orderable: false,
                responsivePriority: 9999,
                className: "desktop"
            },
            { 
                data: 'post_balance', 
                name: 'post_balance', 
                orderable: false,
                responsivePriority: 9998,
                className: "desktop"
            },
            { 
                data: 'wallet_type', 
                name: 'wallet_type', 
                orderable: false,
                responsivePriority: 10001,
                className: "none"
            },
            { 
                data: 'details', 
                name: 'details', 
                orderable: false,
                responsivePriority: 10002,
                className: "none"
            },
            { 
                data: 'note', 
                name: 'note', 
                orderable: false,
                responsivePriority: 10003,
                className: "none"
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        scrollX: false,
        autoWidth: false,
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
            emptyTable: '<div class="text-center"><i class="fe fe-inbox fs-2 text-muted"></i><br><h6 class="text-muted mt-2">No transactions found</h6></div>',
            zeroRecords: '<div class="text-center"><i class="fe fe-search fs-2 text-muted"></i><br><h6 class="text-muted mt-2">No matching transactions found</h6></div>'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });

    // Filter change handlers
    $('#remark_filter, #type_filter, #from_date, #to_date').on('change', function() {
        transactionsTable.ajax.reload();
    });

    // Search input handler with debounce
    let searchTimeout;
    $('#search_input').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            transactionsTable.ajax.reload();
        }, 500);
    });

    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    $('#from_date, #to_date').attr('max', today);
});

function refreshTable() {
    transactionsTable.ajax.reload();
    showToast('Table refreshed', 'success');
}

function exportTransactions() {
    // Get current filters
    const filters = {
        remark_filter: $('#remark_filter').val(),
        type_filter: $('#type_filter').val(),
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
        search_input: $('#search_input').val(),
        export: 'true'
    };

    // Create download URL with filters
    const params = new URLSearchParams(filters);
    const exportUrl = `{{ route('user.all_transaction_history') }}?${params.toString()}`;
    
    // Show loading state
    showToast('Generating export file...', 'info');
    
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'transactions_export.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    setTimeout(() => {
        showToast('Export file generated successfully!', 'success');
    }, 1000);
}

function showToast(message, type = 'info') {
    // Get the toast container
    const toastContainer = document.querySelector('.toast-container');
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fe fe-${getToastIcon(type)} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    
    bsToast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'danger': 
        case 'error': return 'alert-circle';
        case 'warning': return 'alert-triangle';
        case 'info': 
        default: return 'info';
    }
}

// Add tooltip initialization for truncated text
$(document).on('mouseenter', '[title]', function() {
    $(this).tooltip();
});
</script>
@endpush

@push('styles')
<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
    white-space: nowrap;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

/* Toast container */
.toast-container {
    position: fixed !important;
    top: 20px !important;
    right: 20px !important;
    z-index: 11055 !important;
    max-width: 350px;
}

.toast {
    min-width: 300px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}

.toast .toast-body {
    padding: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.toast .btn-close {
    filter: brightness(0) invert(1);
}

/* Responsive toast positioning */
@media screen and (max-width: 768px) {
    .toast-container {
        top: 10px !important;
        right: 10px !important;
        left: 10px !important;
        right: 10px !important;
        max-width: calc(100vw - 20px);
    }
    
    .toast {
        min-width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* DataTables responsive improvements */
.table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

/* DataTables specific responsive styles */
@media screen and (max-width: 767px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
        margin-top: 1rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.5rem;
        margin: 0 0.125rem;
    }
    
    /* Responsive child row styling */
    .dtr-details {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        margin: 0.5rem 0;
    }
    
    .dtr-details .dtr-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .dtr-details .dtr-data {
        margin-bottom: 0.25rem;
    }
}

/* Improve mobile filter layout */
@media screen and (max-width: 768px) {
    .row.mb-4 .col-lg-3,
    .row.mb-4 .col-lg-2,
    .row.mb-4 .col-md-6 {
        margin-bottom: 1rem !important;
    }
    
    .card-header .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .card-header .d-flex .d-flex {
        justify-content: center;
    }
}

/* Better button spacing on mobile */
@media screen and (max-width: 576px) {
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
    }
}

/* Improve statistics cards on mobile */
@media screen and (max-width: 768px) {
    .col-xl-3.col-lg-6.col-md-6 {
        margin-bottom: 1rem;
    }
}

/* Fix table overflow issues */
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

/* Style the responsive control icon */
.dtr-control:before {
    content: '⊞';
    font-size: 1.2em;
    color: #007bff;
    cursor: pointer;
}

.dtr-control.collapsed:before {
    content: '⊞';
}

.dtr-control.expanded:before {
    content: '⊟';
}

/* Ensure proper table layout */
#all-transactions-table {
    width: 100% !important;
    table-layout: fixed;
}

#all-transactions-table th,
#all-transactions-table td {
    text-overflow: ellipsis;
    overflow: hidden;
}

/* Improve pagination on mobile */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    min-width: 2.5rem;
    text-align: center;
}

@media screen and (max-width: 576px) {
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.375rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@endsection
</x-smart_layout>
