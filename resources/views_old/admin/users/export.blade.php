<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
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
                        <i class="ri-download-line me-2"></i>Export Users Data
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.download') }}" id="exportForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Filter</label>
                                    <select name="status" class="form-select">
                                        <option value="all">All Status</option>
                                        <option value="1">Active Users</option>
                                        <option value="0">Inactive Users</option>
                                        <option value="2">Banned Users</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Verification Status</label>
                                    <select name="verification_status" class="form-select">
                                        <option value="">All Verification Status</option>
                                        <option value="email_verified">Email Verified</option>
                                        <option value="email_unverified">Email Not Verified</option>
                                        <option value="kyc_verified">KYC Verified</option>
                                        <option value="kyc_unverified">KYC Not Verified</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fe fe-info me-2"></i>
                            <strong>Export Information:</strong>
                            <ul class="mb-0 mt-2">
                                <li>The export will include user ID, name, username, email, mobile, country, status, verification details, wallet balances, and registration information.</li>
                                <li>Large exports may take some time to process.</li>
                                <li>The file will be downloaded as a CSV format.</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-1"></i>Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-download-line me-1"></i>Download Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Export Options -->
    <div class="row justify-content-center mt-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ri-flashlight-line me-2"></i>Quick Export Options
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.users.download', ['status' => '1']) }}" class="btn btn-success">
                                    <i class="fe fe-users me-2"></i>Export All Active Users
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.users.download', ['verification_status' => 'kyc_verified']) }}" class="btn btn-info">
                                    <i class="fe fe-shield me-2"></i>Export KYC Verified Users
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.users.download', ['date_from' => date('Y-m-d', strtotime('-30 days'))]) }}" class="btn btn-warning">
                                    <i class="fe fe-calendar me-2"></i>Export Last 30 Days
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('admin.users.download') }}" class="btn btn-primary">
                                    <i class="fe fe-download me-2"></i>Export All Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        // Export form enhancement
        $(document).ready(function() {
            $('#exportForm').on('submit', function(e) {
                const button = $(this).find('button[type="submit"]');
                button.prop('disabled', true);
                button.html('<i class="fe fe-loader me-1 spin"></i>Preparing Export...');
                
                // Re-enable button after 5 seconds in case something goes wrong
                setTimeout(() => {
                    button.prop('disabled', false);
                    button.html('<i class="ri-download-line me-1"></i>Download Export');
                }, 5000);
            });
            
            // Set default date range to current month
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            if (!$('input[name="date_from"]').val()) {
                $('input[name="date_from"]').val(firstDay.toISOString().split('T')[0]);
            }
            if (!$('input[name="date_to"]').val()) {
                $('input[name="date_to"]').val(today.toISOString().split('T')[0]);
            }
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
    @endpush
</x-layout>
