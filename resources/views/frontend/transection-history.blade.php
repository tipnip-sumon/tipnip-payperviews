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
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
            <p class="fw-semibold fs-18 mb-0">Your Transfer Transaction History</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.transfer_funds') }}" class="btn btn-primary">
                <i class="fe fe-send me-2"></i>
                New Transfer
            </a>
            <a href="{{ route('user.all_transaction_history') }}" class="btn btn-outline-success">
                <i class="fe fe-list me-2"></i>
                All Transactions
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row" id="statistics-cards">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="fe fe-arrow-right-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Transfers</p>
                                    <h4 class="fw-semibold mb-1" id="total-transfers">0</h4>
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
                                <i class="fe fe-arrow-down-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Money Sent</p>
                                    <h4 class="fw-semibold mb-1" id="money-sent">$0.00</h4>
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
                                <i class="fe fe-arrow-up-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Money Received</p>
                                    <h4 class="fw-semibold mb-1" id="money-received">$0.00</h4>
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
                                <i class="fe fe-dollar-sign fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Charges</p>
                                    <h4 class="fw-semibold mb-1" id="total-charges">$0.00</h4>
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
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Transaction Type</label>
                            <select id="type-filter" class="form-select">
                                <option value="">All Transactions</option>
                                <option value="+">Money Received</option>
                                <option value="-">Money Sent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" id="from-date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" id="to-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" id="search-input" class="form-control" placeholder="Transaction ID, Details...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" id="apply-filters" class="btn btn-primary">
                                    <i class="fe fe-search"></i> Filter
                                </button>
                                <button type="button" id="reset-filters" class="btn btn-outline-secondary">
                                    <i class="fe fe-refresh-cw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer History Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title">
                        <i class="fe fe-list me-2"></i>
                        Transfer History
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fe fe-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportData('csv')"><i class="fe fe-file-text me-2"></i>CSV</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fe fe-file me-2"></i>Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fe fe-file-pdf me-2"></i>PDF</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transfer-history-table" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Charge</th>
                                    <th>Net Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Details</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('pageJsScripts')
<!-- DataTables -->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/js/responsive.bootstrap4.min.js')}}"></script>

<script type="text/javascript">
$(document).ready(function() {
    var table = $("#transfer-history-table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        dom: 'Bfrtip',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25,
        ajax: {
            url: "{{route('user.transfer_history')}}",
            data: function(d) {
                d.type_filter = $('#type-filter').val();
                d.from_date = $('#from-date').val();
                d.to_date = $('#to-date').val();
                d.search_input = $('#search-input').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {
                data: 'trx_type', 
                name: 'trx_type',
                render: function(data, type, row) {
                    if (data.includes('Credit')) {
                        return '<span class="badge bg-success"><i class="fe fe-arrow-down me-1"></i>' + data + '</span>';
                    } else {
                        return '<span class="badge bg-danger"><i class="fe fe-arrow-up me-1"></i>' + data + '</span>';
                    }
                }
            },
            {
                data: 'amount', 
                name: 'amount',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            {
                data: 'charge', 
                name: 'charge',
                render: function(data, type, row) {
                    return '<span class="text-danger">' + data + '</span>';
                }
            },
            {
                data: 'post_balance', 
                name: 'post_balance',
                render: function(data, type, row) {
                    return '<strong class="text-primary">' + data + '</strong>';
                }
            },
            {
                data: 'trx', 
                name: 'trx',
                render: function(data, type, row) {
                    return '<span class="badge bg-light text-dark">' + data + '</span>';
                }
            },
            {data: 'remark', name: 'remark'},
            {
                data: 'note', 
                name: 'note',
                render: function(data, type, row) {
                    if (data === 'N/A') {
                        return '<span class="text-muted">N/A</span>';
                    }
                    return data;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-info view-details" data-row="' + 
                           btoa(JSON.stringify(row)) + '"><i class="fe fe-eye"></i></button>';
                }
            }
        ],
        drawCallback: function(settings) {
            updateStatistics();
        },
        language: {
            emptyTable: "No transfer transactions found",
            zeroRecords: "No matching transfer transactions found",
            info: "Showing _START_ to _END_ of _TOTAL_ transfer transactions",
            infoEmpty: "Showing 0 to 0 of 0 transfer transactions"
        }
    });

    // Filter functionality
    $('#apply-filters').click(function() {
        table.draw();
    });

    $('#reset-filters').click(function() {
        $('#type-filter').val('');
        $('#from-date').val('');
        $('#to-date').val('');
        $('#search-input').val('');
        table.draw();
    });

    // Search on Enter key
    $('#search-input').keypress(function(e) {
        if (e.which === 13) {
            table.draw();
        }
    });

    // View transaction details
    $(document).on('click', '.view-details', function() {
        var rowData = JSON.parse(atob($(this).data('row')));
        showTransactionDetails(rowData);
    });

    function showTransactionDetails(data) {
        var modalContent = `
            <table class="table table-borderless">
                <tr>
                    <td><strong>Transaction ID:</strong></td>
                    <td>${data.trx}</td>
                </tr>
                <tr>
                    <td><strong>Date & Time:</strong></td>
                    <td>${data.created_at}</td>
                </tr>
                <tr>
                    <td><strong>Type:</strong></td>
                    <td>${data.trx_type}</td>
                </tr>
                <tr>
                    <td><strong>Amount:</strong></td>
                    <td><strong>${data.amount}</strong></td>
                </tr>
                <tr>
                    <td><strong>Charge:</strong></td>
                    <td><span class="text-danger">${data.charge}</span></td>
                </tr>
                <tr>
                    <td><strong>Net Amount:</strong></td>
                    <td><strong class="text-primary">${data.post_balance}</strong></td>
                </tr>
                <tr>
                    <td><strong>Details:</strong></td>
                    <td>${data.remark}</td>
                </tr>
                <tr>
                    <td><strong>Note:</strong></td>
                    <td>${data.note}</td>
                </tr>
            </table>
        `;
        
        $('#modal-content').html(modalContent);
        $('#transactionModal').modal('show');
    }

    function updateStatistics() {
        var tableData = table.data().toArray();
        var totalTransfers = tableData.length;
        var moneySent = 0;
        var moneyReceived = 0;
        var totalCharges = 0;

        tableData.forEach(function(row) {
            var amount = parseFloat(row.amount.replace(/[$,]/g, ''));
            var charge = parseFloat(row.charge.replace(/[$,]/g, ''));
            
            if (row.trx_type.includes('Credit')) {
                moneyReceived += amount;
            } else {
                moneySent += amount;
            }
            totalCharges += charge;
        });

        $('#total-transfers').text(totalTransfers);
        $('#money-sent').text('$' + moneySent.toFixed(2));
        $('#money-received').text('$' + moneyReceived.toFixed(2));
        $('#total-charges').text('$' + totalCharges.toFixed(2));
    }

    // Export functionality
    window.exportData = function(format) {
        // You can implement export functionality here
        alert('Export to ' + format.toUpperCase() + ' - Feature coming soon!');
    };
});
</script>
@endsection
</x-smart_layout>