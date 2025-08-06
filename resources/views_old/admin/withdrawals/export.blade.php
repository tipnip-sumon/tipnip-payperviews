<x-layout>
    <x-slot name="title">Export Withdrawals</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">Export Withdrawals</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdrawals.index') }}">Withdrawals</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Export</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center my-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ri-download-line me-2"></i>Export Withdrawals Data
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.withdrawals.download') }}" id="exportForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="2">Pending</option>
                                        <option value="1">Approved</option>
                                        <option value="3">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Withdrawal Type</label>
                                    <select name="withdraw_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="deposit">Deposit Withdrawals</option>
                                        <option value="wallet">Wallet Withdrawals</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">Export Information</h6>
                            <p class="mb-2">Your CSV export will include the following data:</p>
                            <ul class="mb-0">
                                <li>Withdrawal ID and Transaction ID</li>
                                <li>User information (Username and Email)</li>
                                <li>Withdrawal type (Deposit/Wallet)</li>
                                <li>Amount details (Amount, Charge, Final Amount)</li>
                                <li>Payment method information</li>
                                <li>Status and processing dates</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Back to Withdrawals
                            </a>
                            <button type="submit" class="btn btn-success" id="exportBtn">
                                <i class="ri-download-line me-1"></i>Download CSV Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Export Statistics</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-primary me-2">
                                        <i class="ti ti-wallet fs-14"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ number_format(\App\Models\Withdrawal::count()) }}</h6>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-warning me-2">
                                        <i class="ti ti-clock fs-14"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ number_format(\App\Models\Withdrawal::where('status', 2)->count()) }}</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-success me-2">
                                        <i class="ti ti-check fs-14"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ number_format(\App\Models\Withdrawal::where('status', 1)->count()) }}</h6>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-danger me-2">
                                        <i class="ti ti-x fs-14"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-semibold mb-0">{{ number_format(\App\Models\Withdrawal::where('status', 3)->count()) }}</h6>
                                        <small class="text-muted">Rejected</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Total Amount</label>
                        <h5 class="fw-semibold text-success">${{ number_format(\App\Models\Withdrawal::sum('amount'), 2) }}</h5>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Total Charges</label>
                        <h5 class="fw-semibold text-danger">${{ number_format(\App\Models\Withdrawal::sum('charge'), 2) }}</h5>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Net Final Amount</label>
                        <h5 class="fw-semibold text-primary">${{ number_format(\App\Models\Withdrawal::sum('final_amount'), 2) }}</h5>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Quick Export Links</div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.withdrawals.download', ['status' => 2]) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="ti ti-clock text-warning me-2"></i>Pending Withdrawals</span>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                        <a href="{{ route('admin.withdrawals.download', ['status' => 1]) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="ti ti-check text-success me-2"></i>Approved Withdrawals</span>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                        <a href="{{ route('admin.withdrawals.download', ['status' => 3]) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="ti ti-x text-danger me-2"></i>Rejected Withdrawals</span>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                        <a href="{{ route('admin.withdrawals.download', ['withdraw_type' => 'deposit']) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="ti ti-credit-card text-info me-2"></i>Deposit Withdrawals</span>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                        <a href="{{ route('admin.withdrawals.download', ['withdraw_type' => 'wallet']) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="ti ti-wallet text-secondary me-2"></i>Wallet Withdrawals</span>
                                <i class="ri-download-line"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exportForm = document.getElementById('exportForm');
            const exportBtn = document.getElementById('exportBtn');

            exportForm.addEventListener('submit', function(e) {
                exportBtn.innerHTML = '<i class="ri-loader-4-line me-1 spin"></i>Generating Export...';
                exportBtn.disabled = true;

                // Re-enable button after 3 seconds
                setTimeout(function() {
                    exportBtn.innerHTML = '<i class="ri-download-line me-1"></i>Download CSV Export';
                    exportBtn.disabled = false;
                }, 3000);
            });
        });
    </script>

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</x-layout>
