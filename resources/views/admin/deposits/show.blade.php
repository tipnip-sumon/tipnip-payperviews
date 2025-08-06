<x-layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

    @push('style')
    <style>
        .detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            transition: all 0.3s ease;
        }
        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-pending {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .status-approved {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: white;
        }
        .status-rejected {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        .info-row {
            padding: 1rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212529;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-details h6 {
            margin: 0;
            font-weight: 600;
            color: #212529;
        }
        .user-details small {
            color: #6c757d;
        }
        .action-buttons .btn {
            margin: 0 0.5rem;
            border-radius: 10px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .action-buttons .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.75rem;
            top: 0.5rem;
            width: 12px;
            height: 12px;
            background: #667eea;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .amount-display {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            text-align: center;
            margin: 1rem 0;
        }
        .charge-display {
            font-size: 1.2rem;
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
        }
        .nav-pills .nav-link {
            border-radius: 10px;
            margin: 0 5px;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
    @endpush
    @section('content')
    <!-- Navigation Pills -->
    <div class="row mb-4 my-4">
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
                    <a class="nav-link" href="{{ route('admin.deposits.rejected') }}">
                        <i class="fas fa-times me-2"></i>Rejected
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Main Deposit Details -->
        <div class="col-lg-8 mb-4">
            <div class="card detail-card">
                <div class="detail-header m-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Deposit Details
                        </h4>
                        <span class="status-badge badge bg-{{ $deposit->status == 1 ? 'success' : ($deposit->status == 2 ? 'primary' : 'danger') }}">
                            {{ $deposit->status == 1 ? 'Approved' : ($deposit->status == 2 ? 'Pending' : 'Rejected') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><strong>User Information</strong></h4>
                                    <p><strong>Full Name:</strong> {{ $deposit->user->fullname ?? 'N/A' }}</p>
                                    <p><strong>Username:</strong> {{ $deposit->user->username ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $deposit->user->email ?? 'N/A' }}</p>
                                    <p><strong>Last Login:</strong> {{ $deposit->user->last_login_human }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h4><strong>Deposit Information</strong></h4>
                                    <p><strong>Order ID:</strong><code> {{ $deposit->trx ?? 'N/A' }}</code></p>
                                    <p><strong>Payment ID:</strong><code> {{ $deposit->payment_id }}</code></p>
                                    <p><div class="info-label"><strong>Amount Details:</strong></div>
                                    <div class="info-value">
                                        <div class="amount-display">
                                            ${{ number_format($deposit->amount, 2) }}
                                        </div>
                                        @if($deposit->amount > 0)
                                            <div class="charge-display">
                                                + ${{ number_format(($deposit->final_amo - $deposit->amount), 2) }} (Charge)
                                            </div>
                                        @endif
                                    </div></p>
                                    <p><strong>Gateway:</strong><span class="badge bg-primary">{{ $deposit->gateway->name ?? 'NOWPayments' }}</span></p>
                                    <p><strong>Wallet Address:</strong>{{ $deposit->btc_wallet }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($deposit->status == 2)
                    <div class="info-row">
                        <div class="info-value">
                            <div class="action-buttons">
                                <!-- <button class="btn btn-success" onclick="approveDeposit({{ $deposit->id }})">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button> -->
                                <button class="btn btn-success approve-btn" 
                                        data-approve-url="{{ route('admin.deposits.approve', $deposit->id) }}"
                                        data-deposit-id="{{ $deposit->id }}">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button>
                                
                                <button class="btn btn-danger reject-btn"
                                    data-reject-url="{{ route('admin.deposits.reject', $deposit->id) }}"
                                    data-deposit-id="{{ $deposit->id }}">
                                    <i class="fas fa-times me-2"></i>Reject
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline & Additional Info -->
        <div class="col-lg-4">
            <div class="card detail-card">
                <div class="detail-header m-4">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="timeline">
                            <div class="timeline-item">
                                <strong>Created</strong><br>
                                <small class="text-muted">{{ showDateTime($deposit->created_at) }}</small>
                            </div>
                            @if($deposit->status == 2)
                            <div class="timeline-item">
                                <strong>Status</strong><br>
                                <strong class="text-warning">Waiting</strong><br>
                            </div>
                            @elseif($deposit->status == 1)
                            <div class="timeline-item">
                                <strong>Status</strong><br>
                                <strong class="text-success">Approved</strong><br>
                                <small class="text-muted">{{ showDateTime($deposit->updated_at) }}</small>
                            </div>
                            @elseif($deposit->status == 3)
                            <div class="timeline-item">
                                <strong>Status</strong><br>
                                <strong class="text-danger">Rejected</strong><br>
                                <small class="text-muted">{{ showDateTime($deposit->updated_at) }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card detail-card">
                <div class="detail-header m-4">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Additional Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-label"><strong>Currency</strong></div>
                        <div class="info-value">{{ strtoupper($deposit->method_currency) ?? 'USD' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><strong>Rate</strong></div>
                        <div class="info-value">{{currencySymbol()}}{{ showAmount($deposit->rate) ?? '1.00' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Final Amount</div>
                        <div class="info-value">
                            <strong>{{currencySymbol()}}{{ showAmount($deposit->final_amo) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @endsection
    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Check if SweetAlert is loaded
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded!');
            }
            $(document).on('click', '.approve-btn', function() {
                const approveUrl = $(this).data('approve-url');
                const depositId = $(this).data('deposit-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to approve this deposit?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: approveUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: depositId
                            },
                            beforeSend: function() {
                                Swal.showLoading();
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Success!', response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error:', xhr);
                                let errorMessage = 'Something went wrong. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire('Error!', errorMessage, 'error');
                            }
                        });
                    }
                });
            });
            $(document).on('click', '.reject-btn', function() {
                const rejectUrl = $(this).data('reject-url');
                const depositId = $(this).data('deposit-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to reject this deposit?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reject it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: rejectUrl,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: depositId
                            },
                            beforeSend: function() {
                                Swal.showLoading();
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Success!', response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error:', xhr);
                                let errorMessage = 'Something went wrong. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire('Error!', errorMessage, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-layout>
