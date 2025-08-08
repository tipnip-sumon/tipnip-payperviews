<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title','Withdraw Your Deposit')
    @section('content')
    
    <!-- Withdrawal Status -->
    @if($activeDeposit && $withdrawalDetails)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body text-center py-4">
                    <i class="ri-bank-line fs-40 mb-3"></i>
                    <h4 class="text-white mb-2">Withdraw Your Deposit</h4>
                    <p class="mb-0">You can withdraw your {{ $withdrawalDetails['plan_name'] }} deposit anytime with a 20% processing fee.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Withdrawal Calculation --> 
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-card border-0 shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-calculator-line me-2"></i>Withdrawal Calculation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">${{ number_format($withdrawalDetails['deposit_amount'], 2) }}</h3>
                                <small class="text-muted">Current Deposit</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-warning mb-1">{{ $withdrawalDetails['fee_percentage'] }}%</h3>
                                <small class="text-muted">Processing Fee</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-danger mb-1">${{ number_format($withdrawalDetails['withdrawal_fee'], 2) }}</h3>
                                <small class="text-muted">Fee Amount</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-success mb-1">${{ number_format($withdrawalDetails['net_amount'], 2) }}</h3>
                                <small class="text-muted">You'll Receive</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Withdrawal Statistics -->
    @if($withdrawalStats['total_withdrawals'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-card border-0 shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-bar-chart-line me-2"></i>Your Withdrawal History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h4 class="text-primary mb-1">{{ $withdrawalStats['total_withdrawals'] }}</h4>
                                <small class="text-muted">Total Requests</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h4 class="text-success mb-1">${{ number_format($withdrawalStats['total_withdrawn'], 2) }}</h4>
                                <small class="text-muted">Total Withdrawn</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h4 class="text-warning mb-1">{{ $withdrawalStats['pending_withdrawals'] }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-info mb-1">${{ number_format($withdrawalStats['pending_amount'], 2) }}</h4>
                                <small class="text-muted">Pending Amount</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Main Content -->
    <div class="row my-4">
        <!-- Withdrawal Form -->
        <div class="col-xl-8 col-md-8 col-sm-12">
            @if(!isset($kycVerified) || !$kycVerified)
                <!-- KYC Verification Required Notice -->
                <div class="card border-warning mb-4 my-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt text-warning fa-3x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="text-warning mb-2">KYC Verification Required</h5>
                                <p class="mb-3">You need to complete KYC verification before you can process any withdrawals.</p>
                                <a href="{{ route('user.kyc.index') }}" class="btn btn-warning">
                                    <i class="fas fa-user-check me-2"></i>Complete KYC Verification
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($activeDeposit && $withdrawalDetails)
            <div class="card custom-card border-0 shadow {{ (!isset($kycVerified) || !$kycVerified) ? 'opacity-50' : '' }}">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-hand-coin-line me-2"></i>Request Withdrawal
                    </h5>
                </div>
                <form action="{{ route('user.withdraw.submit') }}" method="post" id="withdrawForm"
                      {{ (!isset($kycVerified) || !$kycVerified) ? 'style=pointer-events:none;' : '' }}>
                    @csrf
                    <div class="card-body p-4">
                        <!-- Withdrawal Method -->
                        <div class="mb-4">
                            <label for="withdraw_method" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-bank-card-line me-2 text-primary"></i>Withdrawal Method:
                            </label>
                            <select class="form-control form-select" name="withdraw_method" id="withdraw_method" required>
                                <option value="">Select withdrawal method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="skrill">Skrill</option>
                                <option value="perfect_money">Perfect Money</option>
                                <option value="usdt">USDT (Crypto)</option>
                            </select>
                            @error('withdraw_method')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Account Details -->
                        <div class="mb-4">
                            <label for="account_details" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-information-line me-2 text-info"></i>Account Details:
                            </label>
                            <textarea name="account_details" id="account_details" class="form-control" rows="4" 
                                      placeholder="Enter your account details (e.g., Bank account number, PayPal email, etc.)" required></textarea>
                            <small class="text-muted">Provide complete and accurate details to avoid delays in processing.</small>
                            @error('account_details')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Withdrawal Summary -->
                        <div class="mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="ri-file-list-line me-2"></i>Withdrawal Summary</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Current Deposit:</strong><br>
                                            <span class="text-primary">${{ number_format($withdrawalDetails['deposit_amount'], 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Processing Fee (20%):</strong><br>
                                            <span class="text-danger">-${{ number_format($withdrawalDetails['withdrawal_fee'], 2) }}</span>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <hr class="my-2">
                                            <strong>Amount You'll Receive:</strong><br>
                                            <span class="text-success fs-18 fw-bold">${{ number_format($withdrawalDetails['net_amount'], 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transaction Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-shield-keyhole-line me-2 text-danger"></i>Transaction Password:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ri-lock-line text-muted"></i></span>
                                <input type="password" name="password" class="form-control" id="password" 
                                       placeholder="Enter your password for security" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Important Notice -->
                        <div class="alert alert-warning border-0">
                            <h6><i class="ri-alert-line me-2"></i>Important Notice:</h6>
                            <ul class="mb-0">
                                <li>Once you submit this request, your deposit will be marked as withdrawn</li>
                                <li>You will lose access to ad viewing until you make a new deposit</li>
                                <li>Processing may take 1-3 business days</li>
                                <li>A 20% processing fee will be deducted from your deposit</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-center p-4">
                        @if(isset($kycVerified) && $kycVerified)
                            <button class="btn btn-danger btn-lg w-100" type="submit" id="withdrawBtn">
                                <i class="ri-money-dollar-circle-line me-2"></i>Request Withdrawal (${{ number_format($withdrawalDetails['net_amount'], 2) }})
                            </button>
                        @else
                            <button class="btn btn-secondary btn-lg w-100" type="button" disabled>
                                <i class="fas fa-lock me-2"></i>KYC Verification Required
                            </button>
                        @endif
                    </div>
                </form>
            </div>
            @else
            <div class="card custom-card border-0 shadow">
                <div class="card-body text-center p-5">
                    <i class="ri-alert-line fs-40 text-warning mb-3"></i>
                    <h4>No Active Deposit Found</h4>
                    <p class="text-muted">You don't have any active deposit to withdraw at the moment.</p>
                    <a href="{{ route('invest.index') }}" class="btn btn-primary">
                        <i class="ri-add-circle-line me-2"></i>Make a Deposit
                    </a>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Recent Withdrawals -->
        <div class="col-xl-4 col-md-4 col-sm-12">
            <div class="card custom-card border-0 shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-history-line me-2"></i>Recent Withdrawals
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($recentWithdrawals as $withdrawal)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-shrink-0 me-3">
                            @if($withdrawal->status == 2)
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                    <i class="ri-time-line"></i>
                                </div>
                            @elseif($withdrawal->status == 1)
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                    <i class="ri-check-line"></i>
                                </div>
                            @elseif($withdrawal->status == 3)
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                                    <i class="ri-close-line"></i>
                                </div>
                            @else
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2">
                                    <i class="ri-question-line"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">${{ number_format($withdrawal->final_amount, 2) }}</span>
                                <small class="text-muted">{{ $withdrawal->created_at->format('M d') }}</small>
                            </div>
                            <div class="small text-muted">
                                {{ $withdrawal->withdrawMethod->name ?? 'Unknown Method' }}
                                @if($withdrawal->status == 2)
                                    <span class="badge bg-warning ms-2">Pending</span>
                                @elseif($withdrawal->status == 1)
                                    <span class="badge bg-success ms-2">Approved</span>
                                @elseif($withdrawal->status == 3)
                                    <span class="badge bg-danger ms-2">Rejected</span>
                                @else
                                    <span class="badge bg-secondary ms-2">Unknown</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-4">
                        <i class="ri-history-line fs-40 text-muted mb-3"></i>
                        <p class="text-muted">No withdrawal history yet</p>
                    </div>
                    @endforelse
                </div>
                @if($recentWithdrawals->count() > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('user.withdraw.history') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-external-link-line me-1"></i>View All History
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger mx-3 mb-3">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mx-3 mb-3">
            <i class="ri-check-line me-2"></i>{{ session('success') }}
        </div>
    @endif
    
    @endsection

    @push('style')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .withdrawal-card {
            transition: all 0.3s ease;
        }
        
        .withdrawal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .amount-highlight {
            font-size: 1.25rem;
            font-weight: 700;
        }
    </style>
    @endpush

    @push('script')
    <script>
        $(document).ready(function() {
            $('#withdrawForm').on('submit', function(e) {
                const password = $('#password').val();
                const method = $('#withdraw_method').val();
                const details = $('#account_details').val();

                if (!password || !method || !details) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                    return false;
                }

                if (!confirm('Are you sure you want to withdraw your deposit? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }

                $('#withdrawBtn').html('<i class="ri-loader-4-line me-2 spin"></i>Processing Withdrawal...').prop('disabled', true);
            });
            
            // Add spinning animation
            $('<style>')
                .prop('type', 'text/css')
                .html('.spin { animation: spin 1s linear infinite; } @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }')
                .appendTo('head');
        });
    </script>
    @endpush
</x-smart_layout>
