<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title','Withdrawal History')
    @section('content')
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="ri-file-list-line fs-30 mb-2"></i>
                    <h4 class="mb-1">{{ $stats['total_requests'] }}</h4>
                    <small>Total Requests</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="ri-check-double-line fs-30 mb-2"></i>
                    <h4 class="mb-1">{{ $stats['approved_requests'] }}</h4>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="ri-time-line fs-30 mb-2"></i>
                    <h4 class="mb-1">{{ $stats['pending_requests'] }}</h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="ri-close-circle-line fs-30 mb-2"></i>
                    <h4 class="mb-1">{{ $stats['rejected_requests'] }}</h4>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Withdrawn Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body text-center">
                    <i class="ri-money-dollar-circle-line fs-40 mb-3"></i>
                    <h3 class="text-white mb-1">${{ number_format($stats['total_withdrawn'], 2) }}</h3>
                    <p class="mb-0">Total Amount Withdrawn</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-gradient-warning text-white">
                <div class="card-body text-center">
                    <i class="ri-percent-line fs-40 mb-3"></i>
                    <h3 class="text-white mb-1">${{ number_format($stats['total_fees_paid'], 2) }}</h3>
                    <p class="mb-0">Total Fees Paid</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Withdrawal History Table -->
    <div class="card custom-card border-0 shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-history-line me-2"></i>Withdrawal History
                </h5>
                <a href="{{ route('user.withdraw') }}" class="btn btn-light btn-sm">
                    <i class="ri-add-circle-line me-1"></i>New Withdrawal
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($withdrawals->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Method</th>
                            <th>Amount Requested</th>
                            <th>Fee</th>
                            <th>Final Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <span class="fw-semibold text-primary">#{{ $withdrawal->trx }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="ri-bank-card-line me-2 text-info"></i>
                                    {{ $withdrawal->withdrawMethod->name ?? 'Unknown' }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold">${{ number_format($withdrawal->amount + $withdrawal->charge, 2) }}</span>
                            </td>
                            <td>
                                <span class="text-danger">${{ number_format($withdrawal->charge, 2) }}</span>
                                <small class="text-muted d-block">20%</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">${{ number_format($withdrawal->final_amount, 2) }}</span>
                            </td>
                            <td>
                                @if($withdrawal->status == 2)
                                    <span class="badge bg-warning">
                                        <i class="ri-time-line me-1"></i>Pending
                                    </span>
                                @elseif($withdrawal->status == 1)
                                    <span class="badge bg-success">
                                        <i class="ri-check-line me-1"></i>Approved
                                    </span>
                                @elseif($withdrawal->status == 3)
                                    <span class="badge bg-danger">
                                        <i class="ri-close-line me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="ri-question-line me-1"></i>Unknown
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $withdrawal->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $withdrawal->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm" 
                                        onclick="showDetails('{{ $withdrawal->id }}')" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#withdrawalModal">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                {{ $withdrawals->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="ri-history-line fs-40 text-muted mb-3"></i>
                <h5>No Withdrawal History</h5>
                <p class="text-muted">You haven't made any withdrawal requests yet.</p>
                <a href="{{ route('user.withdraw') }}" class="btn btn-primary">
                    <i class="ri-add-circle-line me-2"></i>Make Your First Withdrawal
                </a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Withdrawal Details Modal -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Withdrawal Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="withdrawalDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    @endsection

    @push('style')
    <style>
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .card {
            transition: all 0.3s ease;
        }
    </style>
    @endpush

    @push('script')
    <script>
        function showDetails(withdrawalId) {
            // Here you can load withdrawal details via AJAX
            // For now, we'll just show a placeholder
            $('#withdrawalDetails').html(`
                <div class="text-center">
                    <i class="ri-loader-4-line fs-24 spin text-primary"></i>
                    <p class="mt-2">Loading withdrawal details...</p>
                </div>
            `);
            
            // You can implement AJAX call here to fetch details
            setTimeout(() => {
                $('#withdrawalDetails').html(`
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Detailed view will be implemented with additional withdrawal information.
                    </div>
                `);
            }, 1000);
        }
        
        // Add spinning animation
        $('<style>')
            .prop('type', 'text/css')
            .html('.spin { animation: spin 1s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }')
            .appendTo('head');
    </script>
    @endpush
</x-smart_layout>
