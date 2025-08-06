<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @push('style')
        <style>
            .stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 15px;
                padding: 25px;
                color: white;
                margin-bottom: 20px;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
                transition: transform 0.3s ease;
            }
            .stats-card:hover {
                transform: translateY(-5px);
            }
            .stats-icon {
                font-size: 3rem;
                opacity: 0.8;
                margin-bottom: 15px;
            }
            .stats-value {
                font-size: 2.5rem;
                font-weight: 700;
                margin: 0;
            }
            .stats-label {
                font-size: 0.95rem;
                opacity: 0.9;
                margin: 5px 0 0 0;
            }
            .filter-card {
                background: #fff;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 25px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            .table-card {
                background: #fff;
                border-radius: 15px;
                padding: 25px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            .btn-filter {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 10px;
                padding: 10px 25px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-filter:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                color: white;
            }
            .form-control {
                border-radius: 10px;
                border: 2px solid #e1e5e9;
                padding: 12px 15px;
                transition: all 0.3s ease;
            }
            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            .badge-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%); }
            .badge-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
            .badge-danger { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
            .badge-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
            .page-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px 0;
                margin-bottom: 30px;
                border-radius: 0 0 30px 30px;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border-color: #667eea !important;
            }
            .alert {
                border-radius: 10px;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .modal-content {
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            }
            .modal-header {
                border-radius: 15px 15px 0 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                display: none;
            }
            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #667eea;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* Responsive improvements */
            @media (max-width: 768px) {
                .stats-card {
                    margin-bottom: 15px;
                    padding: 20px;
                }
                
                .stats-value {
                    font-size: 2rem;
                }
                
                .filter-card, .table-card {
                    padding: 20px;
                    margin-bottom: 20px;
                }
                
                .page-header {
                    padding: 20px 0;
                    margin-bottom: 20px;
                }
                
                .page-header h2 {
                    font-size: 1.75rem;
                }
                
                .btn-sm {
                    padding: 0.375rem 0.75rem;
                    font-size: 0.875rem;
                }
                
                .table-responsive {
                    border-radius: 8px;
                }
                
                .d-flex.gap-2 {
                    gap: 0.5rem !important;
                }
            }
            
            @media (max-width: 576px) {
                .container {
                    padding-left: 15px;
                    padding-right: 15px;
                }
                
                .stats-value {
                    font-size: 1.75rem;
                }
                
                .filter-card, .table-card {
                    padding: 15px;
                }
                
                .btn-sm {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
                }
                
                .page-header h2 {
                    font-size: 1.5rem;
                }
                
                .modal-dialog {
                    margin: 1rem;
                }
                
                .d-flex.flex-wrap {
                    justify-content: center !important;
                }
            }
        </style>
    @endpush

    @section('content')
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>

        <div class="page-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-0"><i class="fas fa-credit-card me-3"></i>{{ $pageTitle }}</h2>
                        <p class="mb-0 mt-2 opacity-75">Track all your payment transactions and history</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-light btn-sm" onclick="refreshStats()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3 class="stats-value" id="total-payments">{{ $statistics['total_payments'] ?? 0 }}</h3>
                        <p class="stats-label">Total Payments</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3 class="stats-value" id="total-amount">${{ number_format($statistics['total_amount'] ?? 0, 2) }}</h3>
                        <p class="stats-label">Total Amount</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="stats-value" id="successful-payments">{{ $statistics['successful_payments'] ?? 0 }}</h3>
                        <p class="stats-label">Successful</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card text-center">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="stats-value" id="pending-payments">{{ $statistics['pending_payments'] ?? 0 }}</h3>
                        <p class="stats-label">Pending</p>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="filter-card">
                <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Advanced Filters</h5>
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-control" id="filter_status" name="status">
                                <option value="">All Status</option>
                                <option value="1">Successful</option>
                                <option value="0">Pending</option>
                                <option value="2">Processing</option>
                                <option value="3">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="">All Currencies</option>
                                @foreach($currencies ?? [] as $currency)
                                    <option value="{{ $currency }}">{{ $currency }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Min Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="min_amount" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Max Amount</label>
                            <input type="number" class="form-control" id="max_amount" name="max_amount" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Payment ID, TRX...">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="clearFilters()">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </button>
                        <button type="button" class="btn btn-filter" onclick="applyFilters()">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="table-card">
                <div class="row align-items-center mb-4">
                    <div class="col-md-6 col-12 mb-3 mb-md-0">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Payment History</h5>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-start">
                            <button class="btn btn-success btn-sm" onclick="openCreateModal()" style="display: none;">
                                <i class="fas fa-plus me-2"></i><span class="d-none d-sm-inline">New Payment</span><span class="d-inline d-sm-none">New</span>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="exportData('excel')">
                                <i class="fas fa-file-excel me-2"></i><span class="d-none d-sm-inline">Excel</span>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="exportData('pdf')">
                                <i class="fas fa-file-pdf me-2"></i><span class="d-none d-sm-inline">PDF</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="payment-history-table">
                        <thead class="table-light">
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Payment ID</th>
                                <th>Gateway</th>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Charge</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Details Modal -->
        <div class="modal fade" id="paymentDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Payment Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="payment-details-content">
                        <!-- Details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalTitle">
                            <i class="fas fa-plus me-2"></i>Create Payment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="paymentForm">
                            <input type="hidden" id="paymentId" name="payment_id">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="1" required>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="method_currency" class="form-label fw-bold">Currency <span class="text-danger">*</span></label>
                                    <select class="form-control" id="method_currency" name="method_currency" required>
                                        <option value="">Select Currency</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                        <option value="BTC">BTC</option>
                                        <option value="ETH">ETH</option>
                                        <option value="USDT">USDT</option>
                                        <option value="USDC">USDC</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="charge" class="form-label fw-bold">Charge</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="charge" name="charge" 
                                               step="0.01" min="0" value="0">
                                    </div>
                                    <small class="text-muted">Processing fee (optional)</small>
                                </div>
                                
                                <div class="col-md-6 mb-3" id="statusGroup" style="display: none;">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="0">Pending</option>
                                        <option value="1">Successful</option>
                                        <option value="2">Processing</option>
                                        <option value="3">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" placeholder="Optional payment description"></textarea>
                                <small class="text-muted">Additional details about this payment</small>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Manual payment entries will be marked as pending by default. 
                                Contact support for verification and approval.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" onclick="savePayment()">
                            <i class="fas fa-save me-2"></i><span id="saveButtonText">Create Payment</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- Include jQuery first -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Include DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        
        <script>
            let table;
            
            $(document).ready(function() {
                console.log('Document ready, initializing...');
                initializeDataTable();
                setDateDefaults();
            });

            function showLoading() {
                $('#loadingOverlay').show();
            }

            function hideLoading() {
                $('#loadingOverlay').hide();
            }

            function initializeDataTable() {
                console.log('Initializing DataTable...');
                table = $('#payment-history-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('user.payment_history') }}",
                        data: function(d) {
                            d.date_from = $('#date_from').val();
                            d.date_to = $('#date_to').val();
                            d.status = $('#filter_status').val();
                            d.currency = $('#currency').val();
                            d.min_amount = $('#min_amount').val();
                            d.max_amount = $('#max_amount').val();
                            d.search_query = $('#search').val();
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'payment_id', name: 'payment_id'},
                        {data: 'gateway', name: 'gateway'},
                        {data: 'method_currency', name: 'method_currency'},
                        {data: 'amount', name: 'amount'},
                        {data: 'charge', name: 'charge'},
                        {data: 'total_amount', name: 'total_amount'},
                        {data: 'status', name: 'status', orderable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    order: [[1, 'desc']],
                    pageLength: 25,
                    language: {
                        processing: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br>Loading...</div>',
                        emptyTable: '<div class="text-center"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br>No payment records found</div>',
                        zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching records found</div>'
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });
            }

            function setDateDefaults() {
                const today = new Date();
                const lastMonth = new Date();
                lastMonth.setMonth(today.getMonth() - 1);
                
                $('#date_to').val(today.toISOString().split('T')[0]);
                $('#date_from').val(lastMonth.toISOString().split('T')[0]);
            }

            function applyFilters() {
                showLoading();
                table.draw();
                updateStatistics();
                hideLoading();
            }

            function clearFilters() {
                $('#filter-form')[0].reset();
                setDateDefaults();
                table.draw();
                updateStatistics();
            }

            function updateStatistics() {
                const filters = {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    status: $('#filter_status').val(),
                    currency: $('#currency').val(),
                    min_amount: $('#min_amount').val(),
                    max_amount: $('#max_amount').val(),
                    search_query: $('#search').val()
                };

                $.get("{{ route('user.payment_history') }}", {...filters, get_stats: true})
                    .done(function(data) {
                        if (data.statistics) {
                            $('#total-payments').text(data.statistics.total_payments || 0);
                            $('#total-amount').text('$' + (data.statistics.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
                            $('#successful-payments').text(data.statistics.successful_payments || 0);
                            $('#pending-payments').text(data.statistics.pending_payments || 0);
                        }
                    });
            }

            function refreshStats() {
                showLoading();
                updateStatistics();
                table.draw();
                hideLoading();
            }

            function viewPaymentDetails(paymentId) {
                showLoading();
                $.get("{{ route('user.payment_history') }}", {payment_id: paymentId, get_details: true})
                    .done(function(data) {
                        $('#payment-details-content').html(data.html);
                        $('#paymentDetailsModal').modal('show');
                    })
                    .fail(function() {
                        showAlert('error', 'Error loading payment details');
                    })
                    .always(function() {
                        hideLoading();
                    });
            }

            function exportData(format) {
                const filters = {
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    status: $('#filter_status').val(),
                    currency: $('#currency').val(),
                    min_amount: $('#min_amount').val(),
                    max_amount: $('#max_amount').val(),
                    search_query: $('#search').val(),
                    export: format
                };

                const queryString = Object.keys(filters)
                    .filter(key => filters[key] !== '')
                    .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(filters[key]))
                    .join('&');

                window.open("{{ route('user.payment_history') }}?" + queryString, '_blank');
            }

            // CRUD Operations
            function openCreateModal() {
                resetPaymentForm();
                $('#paymentModalTitle').html('<i class="fas fa-plus me-2"></i>Create Payment');
                $('#saveButtonText').text('Create Payment');
                $('#statusGroup').hide();
                $('#paymentModal').modal('show');
            }

            function editPayment(id) {
                resetPaymentForm();
                showLoading();
                
                // Construct the URL properly with the ID parameter
                const url = "{{ route('user.payment.get', ':id') }}".replace(':id', id);
                
                $.get(url)
                    .done(function(response) {
                        if (response.status === 'success') {
                            const data = response.data;
                            
                            $('#paymentId').val(data.id);
                            $('#amount').val(data.amount);
                            $('#method_currency').val(data.method_currency);
                            $('#charge').val(data.charge || 0);
                            $('#status').val(data.status);
                            $('#description').val(data.description || '');
                            
                            $('#paymentModalTitle').html('<i class="fas fa-edit me-2"></i>Edit Payment');
                            $('#saveButtonText').text('Update Payment');
                            $('#statusGroup').show();
                            $('#paymentModal').modal('show');
                        } else {
                            showAlert('error', response.message);
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Error loading payment:', xhr);
                        showAlert('error', 'Failed to load payment details');
                    })
                    .always(function() {
                        hideLoading();
                    });
            }

            function savePayment() {
                const form = $('#paymentForm')[0];
                const isEdit = $('#paymentId').val() !== '';
                
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                // Prepare data
                const data = {
                    amount: $('#amount').val(),
                    method_currency: $('#method_currency').val(),
                    charge: $('#charge').val() || 0,
                    description: $('#description').val(),
                    _token: '{{ csrf_token() }}'
                };
                
                if (isEdit) {
                    data.status = $('#status').val();
                    data._method = 'PUT';
                }
                
                const url = isEdit ? 
                    "{{ route('user.payment.update', ':id') }}".replace(':id', $('#paymentId').val()) :
                    "{{ route('user.payment.create') }}";
                
                // Show loading state
                const saveBtn = $('[onclick="savePayment()"]');
                const originalText = saveBtn.html();
                saveBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#paymentModal').modal('hide');
                            showAlert('success', response.message);
                            table.draw();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Save error:', xhr);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                const input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(errors[field][0]);
                            });
                        } else {
                            const message = xhr.responseJSON?.message || 'An error occurred';
                            showAlert('error', message);
                        }
                    },
                    complete: function() {
                        saveBtn.html(originalText).prop('disabled', false);
                    }
                });
            }

            function deletePayment(id) {
                if (!confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
                    return;
                }
                
                showLoading();
                const url = "{{ route('user.payment.delete', ':id') }}".replace(':id', id);
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                            table.draw();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        const message = xhr.responseJSON?.message || 'Failed to delete payment';
                        showAlert('error', message);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            function resetPaymentForm() {
                $('#paymentForm')[0].reset();
                $('#paymentId').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function showAlert(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="fas ${iconClass} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                $('.container').first().prepend(alertHtml);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }

            // Auto-refresh every 5 minutes
            setInterval(function() {
                refreshStats();
            }, 300000);

            // Make functions global for onclick handlers
            window.openCreateModal = openCreateModal;
            window.editPayment = editPayment;
            window.deletePayment = deletePayment;
            window.savePayment = savePayment;
            window.viewPaymentDetails = viewPaymentDetails;
            window.exportData = exportData;
            window.applyFilters = applyFilters;
            window.clearFilters = clearFilters;
            window.refreshStats = refreshStats;
        </script>
    @endpush
</x-smart_layout>
