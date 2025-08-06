<x-layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

    @push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .table-modern {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .deposit-card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .deposit-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        .action-buttons .btn {
            margin: 0 2px;
            border-radius: 8px;
        }
        .user-info img {
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .amount-display {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
    @endpush

    @section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div>
                                    <span class="fw-semibold opacity-75">Pending Count</span>
                                    <h3 class="mb-0 mt-1">{{ $stats['pending_count'] }}</h3>
                                    <small class="opacity-75">Awaiting Approval</small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card warning border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div>
                                    <span class="fw-semibold opacity-75">Pending Amount</span>
                                    <h3 class="mb-0 mt-1">${{ number_format($stats['pending_amount'], 2) }}</h3>
                                    <small class="opacity-75">Total Value</small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card info border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div>
                                    <span class="fw-semibold opacity-75">Today's Pending</span>
                                    <h3 class="mb-0 mt-1">{{ $stats['today_pending'] }}</h3>
                                    <small class="opacity-75">New Today</small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card stats-card border-0" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div>
                                    <span class="fw-semibold opacity-75">Today's Amount</span>
                                    <h3 class="mb-0 mt-1">${{ number_format($stats['today_amount'], 2) }}</h3>
                                    <small class="opacity-75">Today's Value</small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-3 mb-md-0">
                        <i class="fas fa-filter me-2"></i>Filter Pending Deposits
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by username or email...">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary w-100" onclick="refreshTable()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All
                                </label>
                            </div>
                            <span id="selectedCount" class="text-muted">0 selected</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="bulkApprove()" disabled id="bulkApproveBtn">
                                <i class="fas fa-check me-1"></i>Bulk Approve
                            </button>
                            <button class="btn btn-danger" onclick="bulkReject()" disabled id="bulkRejectBtn">
                                <i class="fas fa-times me-1"></i>Bulk Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposits Table -->
        <div class="card table-modern">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Pending Deposits
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                        <table class="table table-hover" id="depositsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Gateway</th>
                                    <th>Amount</th>
                                    <th>Total Amount</th>
                                    <th>Request Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this deposit? This action will:</p>
                    <ul>
                        <li>Credit the user's account</li>
                        <li>Mark the deposit as approved</li>
                        <li>Create a transaction record</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmApprove()">
                        <i class="fas fa-check me-1"></i>Approve Deposit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectReason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times me-1"></i>Reject Deposit
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with proper configuration
            const table = $('#depositsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.deposits.pending') }}",
                    type: 'GET',
                },
                columns: [
                    { data: 'user', name: 'user.username' },
                    { data: 'gateway', name: 'gateway.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'total_amount', name: 'amount' },
                    { data: 'date', name: 'updated_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                }
            });
            // Search functionality
            $('#searchInput').on('keyup', function() {
                console.log('Search input changed:', this.value);
                table.draw();
            });

            // Select all functionality
            $('#selectAll, #selectAllTable').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.row-checkbox').prop('checked', isChecked);
                $('#selectAll, #selectAllTable').prop('checked', isChecked);
                updateBulkActions();
            });

            // Individual checkbox change
            $(document).on('change', '.row-checkbox', function() {
                updateBulkActions();
                
                const totalCheckboxes = $('.row-checkbox').length;
                const checkedCheckboxes = $('.row-checkbox:checked').length;
                
                $('#selectAll, #selectAllTable').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            function updateBulkActions() {
                const selectedCount = $('.row-checkbox:checked').length;
                $('#selectedCount').text(selectedCount + ' selected');
                
                if (selectedCount > 0) {
                    $('#bulkApproveBtn, #bulkRejectBtn').prop('disabled', false);
                } else {
                    $('#bulkApproveBtn, #bulkRejectBtn').prop('disabled', true);
                }
            }
            // Refresh table every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 30000);
        });

        let currentDepositId = null;

        function approveDeposit(id) {
            currentDepositId = id;
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        }

        function rejectDeposit(id) {
            currentDepositId = id;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        function confirmApprove() {
            if (!currentDepositId) return;

            $.post("/admin/deposits/" + currentDepositId + "/approve", {
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                    modal.hide();
                    showAlert('success', response.message);
                    $('#depositsTable').DataTable().ajax.reload();
                } else {
                    showAlert('error', response.message);
                }
            })
            .fail(function() {
                showAlert('error', 'Failed to approve deposit. Please try again.');
            });
        }

        function confirmReject() {
            if (!currentDepositId) return;

            const reason = $('#rejectReason').val();

            $.post("/admin/deposits/" + currentDepositId + "/reject", {
                _token: '{{ csrf_token() }}',
                reason: reason
            })
            .done(function(response) {
                if (response.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                    modal.hide();
                    $('#rejectReason').val('');
                    showAlert('success', response.message);
                    $('#depositsTable').DataTable().ajax.reload();
                } else {
                    showAlert('error', response.message);
                }
            })
            .fail(function() {
                showAlert('error', 'Failed to reject deposit. Please try again.');
            });
        }

        function bulkApprove() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                showAlert('warning', 'Please select deposits to approve.');
                return;
            }

            if (confirm('Are you sure you want to approve ' + selectedIds.length + ' deposits?')) {
                $.post("/admin/deposits/bulk-action", {
                    _token: '{{ csrf_token() }}',
                    action: 'approve',
                    deposits: selectedIds
                })
                .done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#depositsTable').DataTable().ajax.reload();
                        $('.row-checkbox').prop('checked', false);
                        $('#selectAll, #selectAllTable').prop('checked', false);
                        updateBulkActions();
                    } else {
                        showAlert('error', response.message);
                    }
                })
                .fail(function() {
                    showAlert('error', 'Bulk approval failed. Please try again.');
                });
            }
        }

        function bulkReject() {
            const selectedIds = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                showAlert('warning', 'Please select deposits to reject.');
                return;
            }

            if (confirm('Are you sure you want to reject ' + selectedIds.length + ' deposits?')) {
                $.post("/admin/deposits/bulk-action", {
                    _token: '{{ csrf_token() }}',
                    action: 'reject',
                    deposits: selectedIds
                })
                .done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#depositsTable').DataTable().ajax.reload();
                        $('.row-checkbox').prop('checked', false);
                        $('#selectAll, #selectAllTable').prop('checked', false);
                        updateBulkActions();
                    } else {
                        showAlert('error', response.message);
                    }
                })
                .fail(function() {
                    showAlert('error', 'Bulk rejection failed. Please try again.');
                });
            }
        }

        function refreshTable() {
            $('#depositsTable').DataTable().ajax.reload();
            showAlert('info', 'Table refreshed successfully.');
        }

        function showAlert(type, message) {
            // You can use your preferred alert system here
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            
            const alert = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Show alert at top of page
            $('body').prepend(alert);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
    @endpush
</x-layout>
