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
        .stats-card.success {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
        .filter-card {
            background: #f8f9fa;
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .nav-pills .nav-link {
            border-radius: 10px;
            margin: 0 5px;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.5rem;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
        }
    </style>
    @endpush
    @section('content')
    <div class="row mb-4 my-4">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Deposits</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['pending'] }}</div>
                            <div class="text-xs">${{ number_format($stats['pending_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['approved'] }}</div>
                            <div class="text-xs">${{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Rejected</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Pills -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.deposits.index') }}">
                        <i class="fas fa-list me-2"></i>All Deposits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.deposits.pending') }}">
                        <i class="fas fa-clock me-2"></i>Pending ({{ $stats['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.deposits.approved') }}">
                        <i class="fas fa-check me-2"></i>Approved ({{ $stats['approved'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.deposits.rejected') }}">
                        <i class="fas fa-times me-2"></i>Rejected ({{ $stats['rejected'] }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Deposits Table -->
    <div class="row">
        <div class="col-12">
            <div class="card table-modern">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-coins me-2"></i>All Deposits
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="depositsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Gateway</th>
                                    <th>Amount</th>
                                    <th>Charge</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
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
    <script>
        $(document).ready(function() {
            console.log('Document ready, jQuery version:', $.fn.jquery);
            console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
            
            // Check if DataTables is available
            if (typeof $.fn.DataTable === 'undefined') {
                console.error('DataTables is not loaded!');
                return;
            }
            
            console.log('Initializing All Deposits DataTable...');
            
            // Initialize DataTable
            var table = $('#depositsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.deposits.index") }}',
                    type: 'GET',
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX error:', error, code);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'user', name: 'user.username' },
                    { data: 'gateway', name: 'gateway.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'charge', name: 'charge' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'status', name: 'status' },
                    { data: 'date', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[6, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                },
                drawCallback: function(settings) {
                    console.log('DataTable draw completed, rows:', settings.json ? settings.json.recordsTotal : 'unknown');
                }
            });

            console.log('DataTable initialized:', table);

            // Refresh table every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 30000);
        });

        // Approve deposit
        function approveDeposit(id) {
            if (confirm('Are you sure you want to approve this deposit?')) {
                $.ajax({
                    url: `/admin/deposits/${id}/approve`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Success: ' + response.message);
                            $('#depositsTable').DataTable().ajax.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error: Something went wrong. Please try again.');
                    }
                });
            }
        }

        // Reject deposit
        // function rejectDeposit(id) {
        //     if (confirm('Are you sure you want to reject this deposit?')) {
        //         $.ajax({
        //             url: `/admin/deposits/${id}/reject`,
        //             type: 'POST',
        //             data: {
        //                 _token: '{{ csrf_token() }}'
        //             },
        //             success: function(response) {
        //                 if (response.success) {
        //                     alert('Success: ' + response.message);
        //                     $('#depositsTable').DataTable().ajax.reload();
        //                 } else {
        //                     alert('Error: ' + response.message);
        //                 }
        //             },
        //             error: function() {
        //                 alert('Error: Something went wrong. Please try again.');
        //             }
        //         });
        //     }
        // }
        function rejectDeposit(id) {
            currentDepositId = id;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
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
    </script>
    @endpush
</x-layout>
