<x-layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

    @push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border-radius: 15px;
            color: white;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
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
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
        .danger-badge {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
    </style>
    @endpush
    @section('content')
    <div class="row mb-4 my-4">
        <!-- Stats Cards -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Rejected</div>
                            <div class="h5 mb-0 font-weight-bold">{{ \App\Models\Deposit::where('status', 2)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold">${{ number_format(\App\Models\Deposit::where('status', 2)->sum('amount'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold">{{ \App\Models\Deposit::where('status', 2)->whereMonth('created_at', now()->month)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x opacity-75"></i>
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
                    <a class="nav-link" href="{{ route('admin.deposits.index') }}">
                        <i class="fas fa-list me-2"></i>All Deposits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.deposits.pending') }}">
                        <i class="fas fa-clock me-2"></i>Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.deposits.approved') }}">
                        <i class="fas fa-check me-2"></i>Approved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.deposits.rejected') }}">
                        <i class="fas fa-times me-2"></i>Rejected
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Rejected Deposits Table -->
    <div class="row">
        <div class="col-12">
            <div class="card table-modern">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2 text-danger"></i>Rejected Deposits
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="rejectedDepositsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Gateway</th>
                                    <th>Amount</th>
                                    <th>Rejected Date</th>
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
    @endsection 
    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#rejectedDepositsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.deposits.rejected") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'user', name: 'user.username' },
                    { data: 'gateway', name: 'gateway.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'rejected_at', name: 'updated_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                }
            });

            // Refresh table every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 30000);
        });
    </script>
    @endpush
</x-layout>
