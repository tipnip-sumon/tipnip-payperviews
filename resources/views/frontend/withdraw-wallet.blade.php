<x-smart_layout>

@section('title', $pageTitle)

@section('content')

@php
    // Safety check for required variables
    $totalWalletBalance = $totalWalletBalance ?? 0;
    $depositWallet = $depositWallet ?? 0;
    $interestWallet = $interestWallet ?? 0;
    $withdrawMethods = $withdrawMethods ?? collect([]);
    $isWalletOtpSession = $isWalletOtpSession ?? false;
    $walletStoredData = $walletStoredData ?? null;
    $kycVerified = $kycVerified ?? false;
    $withdrawalStats = $withdrawalStats ?? [
        'total_wallet_withdrawals' => 0,
        'total_wallet_withdrawn' => 0,
        'pending_wallet_withdrawals' => 0,
        'pending_wallet_amount' => 0
    ];
    $recentWithdrawals = $recentWithdrawals ?? collect([]);
@endphp
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
            <p class="fw-semibold fs-18 mb-0">Withdraw Your Wallet Balance</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fe fe-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Wallet Balance Overview -->
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="fe fe-credit-card fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Wallet Balance</p>
                                    <h4 class="fw-semibold mb-1">${{ number_format($totalWalletBalance, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposit Wallet -->
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success">
                                <i class="fe fe-dollar-sign fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Deposit Wallet</p>
                                    <h4 class="fw-semibold mb-1">${{ number_format($depositWallet, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interest Wallet -->
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info">
                                <i class="fe fe-trending-up fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Interest Wallet</p>
                                    <h4 class="fw-semibold mb-1">${{ number_format($interestWallet, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Withdrawal Form -->
        <div class="col-xl-8">
            @if(!isset($kycVerified) || !$kycVerified)
                <!-- KYC Verification Required Notice -->
                <div class="card border-warning mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shield-alt text-warning fa-3x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="text-warning mb-2">KYC Verification Required</h5>
                                <p class="mb-3">You need to complete KYC verification before you can process wallet withdrawals.</p>
                                <a href="{{ route('user.kyc.index') }}" class="btn btn-warning">
                                    <i class="fas fa-user-check me-2"></i>Complete KYC Verification
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="card custom-card {{ (!isset($kycVerified) || !$kycVerified) ? 'opacity-50' : '' }}">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-download me-2"></i>
                        Wallet Withdrawal Request
                    </div>
                </div>
                <div class="card-body">
                    @if($totalWalletBalance <= 0)
                        <div class="alert alert-warning">
                            <i class="fe fe-alert-triangle me-2"></i>
                            You don't have sufficient wallet balance to make a withdrawal.
                        </div>
                    @else
                        @if(!$isWalletOtpSession)
                            <!-- Step 1: Initial Withdrawal Form (NO PASSWORD) -->
                            <div class="alert alert-info mb-3">
                                <strong>Current Status:</strong> Showing initial withdrawal form (isWalletOtpSession = {{ $isWalletOtpSession ? 'true' : 'false' }})
                            </div>
                            <form action="{{ route('user.withdraw.wallet.submit') }}" method="POST" 
                                  style="{{ (!isset($kycVerified) || !$kycVerified) ? 'pointer-events: none;' : '' }}">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Withdrawal Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" min="1" max="{{ $totalWalletBalance }}" 
                                                       step="0.01" value="{{ old('amount') }}" required>
                                            </div>
                                            <small class="text-muted">Available Balance: ${{ number_format($totalWalletBalance, 2) }}</small>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="method_id" class="form-label">Withdrawal Method <span class="text-danger">*</span></label>
                                            <select class="form-select @error('method_id') is-invalid @enderror" 
                                                    id="method_id" name="method_id" required>
                                                <option value="">Select Method</option>
                                                @forelse($withdrawMethods as $method)
                                                    <option value="{{ $method->id }}" 
                                                            data-min="{{ $method->min_amount }}"
                                                            data-max="{{ $method->max_amount }}"
                                                            data-fixed-charge="{{ $method->fixed_charge ?? 0 }}"
                                                            data-percent-charge="{{ $method->percent_charge ?? 0 }}"
                                                            {{ old('method_id') == $method->id ? 'selected' : '' }}>
                                                        {{ $method->name }} 
                                                        @if($method->fixed_charge > 0 || $method->percent_charge > 0)
                                                            ({{ $method->fixed_charge > 0 ? '$'.number_format($method->fixed_charge, 2) : '' }}{{ ($method->fixed_charge > 0 && $method->percent_charge > 0) ? ' + ' : '' }}{{ $method->percent_charge > 0 ? $method->percent_charge.'%' : '' }} fee)
                                                        @else
                                                            (No fee)
                                                        @endif
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No withdrawal methods available</option>
                                                @endforelse
                                            </select>
                                            @error('method_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="account_details" class="form-label">Account Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('account_details') is-invalid @enderror" 
                                              id="account_details" name="account_details" rows="4" required
                                              placeholder="Please provide your account details (account number, email, wallet address, etc.)">{{ old('account_details') }}</textarea>
                                    @error('account_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Method Information Display -->
                                <div id="method-info" class="card bg-light mb-3" style="display: none;">
                                    <div class="card-body">
                                        <div id="method-details"></div>
                                    </div>
                                </div>

                                <!-- Charge Calculation Display -->
                                <div id="charge-calculation" class="card bg-info text-white mb-3" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="text-white mb-3">Withdrawal Calculation</h6>
                                        <div id="calculation-details"></div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fe fe-info me-2"></i>
                                    <strong>Step 1:</strong> Fill out the withdrawal details above and click "Send Verification Code" to receive an OTP via email.
                                </div>

                                <button type="submit" class="btn btn-primary" 
                                        {{ (!isset($kycVerified) || !$kycVerified) ? 'disabled' : '' }}>
                                    <i class="fe fe-mail me-2"></i>
                                    Send Verification Code
                                </button>
                                
                                @if(!isset($kycVerified) || !$kycVerified)
                                    <div class="mt-3">
                                        <a href="{{ route('user.kyc.index') }}" class="btn btn-warning">
                                            <i class="fas fa-user-check me-2"></i>Complete KYC to Withdraw
                                        </a>
                                    </div>
                                @endif
                            </form>
                        @else
                            <!-- Step 2: OTP Verification Form -->
                            <div class="alert alert-warning mb-3">
                                <strong>Current Status:</strong> Showing OTP form (isWalletOtpSession = {{ $isWalletOtpSession ? 'true' : 'false' }})
                            </div>
                            
                            <div class="alert alert-success">
                                <i class="fe fe-check-circle me-2"></i>
                                <strong>Step 2:</strong> We've sent a verification code to your email. Enter the code below along with your transaction password to complete the withdrawal.
                            </div>
                            
                            <form action="{{ route('user.withdraw.wallet.submit') }}" method="POST">
                                @csrf
                                
                                <!-- Hidden OTP indicator -->
                                <input type="hidden" name="otp_code" value="verify">
                                
                                <!-- Display stored form data -->
                                @if(isset($walletStoredData))
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Amount:</strong> ${{ number_format($walletStoredData['amount'] ?? 0, 2) }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Method:</strong> {{ \App\Models\WithdrawMethod::find($walletStoredData['method_id'] ?? 0)->name ?? 'Unknown' }}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Account Details:</strong> {{ $walletStoredData['account_details'] ?? 'N/A' }}
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="otp_code" class="form-label">6-Digit Verification Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('otp_code') is-invalid @enderror" 
                                                   id="otp_code" name="otp_code" maxlength="6" pattern="[0-9]{6}" 
                                                   placeholder="Enter 6-digit code" required autocomplete="off">
                                            @error('otp_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Transaction Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required autocomplete="current-password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fe fe-clock me-2"></i>
                                    <strong>Important:</strong> The verification code expires in 10 minutes. If you don't receive the code, you can request a new one.
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fe fe-check me-2"></i>
                                        Verify & Complete Withdrawal
                                    </button>
                                    
                                    <form action="{{ route('user.withdraw.wallet.submit') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="clear_otp" value="1">
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="fe fe-refresh-cw me-2"></i>
                                            Request New Code
                                        </button>
                                    </form>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Withdrawal Statistics -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-bar-chart me-2"></i>
                        Withdrawal Statistics
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h5 class="mb-1">{{ $withdrawalStats['total_wallet_withdrawals'] }}</h5>
                                <p class="text-muted mb-0 small">Total Requests</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h5 class="mb-1">${{ number_format($withdrawalStats['total_wallet_withdrawn'], 2) }}</h5>
                                <p class="text-muted mb-0 small">Total Withdrawn</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h5 class="mb-1">{{ $withdrawalStats['pending_wallet_withdrawals'] }}</h5>
                                <p class="text-muted mb-0 small">Pending</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h5 class="mb-1">${{ number_format($withdrawalStats['pending_wallet_amount'], 2) }}</h5>
                                <p class="text-muted mb-0 small">Pending Amount</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('user.withdraw.wallet.history') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fe fe-clock me-2"></i>
                            View History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Withdrawals -->
            @if($recentWithdrawals->count() > 0)
            <div class="card custom-card mt-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-clock me-2"></i>
                        Recent Withdrawals
                    </div>
                </div>
                <div class="card-body">
                    @foreach($recentWithdrawals as $withdrawal)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="mb-0 fw-semibold">${{ number_format($withdrawal->final_amount, 2) }}</p>
                                <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y') }}</small>
                            </div>
                            <div>
                                @if($withdrawal->status == 0)
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($withdrawal->status == 1)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const methodSelect = document.getElementById('method_id'); // Fixed selector ID
    const methodInfo = document.getElementById('method-info');
    const methodDetails = document.getElementById('method-details');
    const chargeCalculation = document.getElementById('charge-calculation');
    const calculationDetails = document.getElementById('calculation-details');
    const maxAmount = {{ $totalWalletBalance }};
    
    // Amount input validation
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (parseFloat(this.value) > maxAmount) {
                this.value = maxAmount;
            }
            updateChargeCalculation();
        });
    }

    // Method selection handler
    if (methodSelect) {
        methodSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value && selectedOption) {
                const minAmount = parseFloat(selectedOption.dataset.min) || 0;
                const maxAmount = parseFloat(selectedOption.dataset.max) || 999999;
                const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge) || 0;
                const percentCharge = parseFloat(selectedOption.dataset.percentCharge) || 0;
                
                // Update amount input constraints
                if (amountInput) {
                    amountInput.min = minAmount;
                    amountInput.max = Math.min(maxAmount, {{ $totalWalletBalance }});
                }
                
                // Show method information
                let chargeText = 'No fee';
                if (fixedCharge > 0 || percentCharge > 0) {
                    chargeText = '';
                    if (fixedCharge > 0) {
                        chargeText += '$' + fixedCharge.toFixed(2);
                    }
                    if (fixedCharge > 0 && percentCharge > 0) {
                        chargeText += ' + ';
                    }
                    if (percentCharge > 0) {
                        chargeText += percentCharge + '%';
                    }
                    chargeText += ' fee';
                }
                
                methodDetails.innerHTML = `
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <strong><i class="fe fe-credit-card me-2"></i>Selected Method:</strong> ${selectedOption.text.split('(')[0].trim()}
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fe fe-dollar-sign me-2"></i>Limits:</strong><br>
                            <span class="text-muted">Min: $${minAmount.toFixed(2)} - Max: $${Math.min(maxAmount, {{ $totalWalletBalance }}).toFixed(2)}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fe fe-percent me-2"></i>Charge:</strong><br>
                            <span class="text-muted">${chargeText}</span>
                        </div>
                    </div>
                `;
                
                methodInfo.style.display = 'block';
                updateChargeCalculation();
            } else {
                methodInfo.style.display = 'none';
                chargeCalculation.style.display = 'none';
                if (amountInput) {
                    amountInput.min = 1;
                    amountInput.max = {{ $totalWalletBalance }};
                }
            }
        });
    }

    function updateChargeCalculation() {
        if (!methodSelect || !amountInput) return;
        
        const selectedOption = methodSelect.options[methodSelect.selectedIndex];
        const amount = parseFloat(amountInput.value);
        
        if (methodSelect.value && amount > 0 && selectedOption) {
            const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge) || 0;
            const percentCharge = parseFloat(selectedOption.dataset.percentCharge) || 0;
            
            let chargeAmount = fixedCharge;
            if (percentCharge > 0) {
                chargeAmount += (amount * percentCharge) / 100;
            }
            
            const finalAmount = amount - chargeAmount;
            
            calculationDetails.innerHTML = `
                <div class="row text-center">
                    <div class="col-4">
                        <div class="text-center">
                            <strong>Requested Amount</strong><br>
                            <span class="fs-5 text-white">$${amount.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <strong>Processing Fee</strong><br>
                            <span class="fs-5 text-warning">$${chargeAmount.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <strong>You'll Receive</strong><br>
                            <span class="fs-5 text-success fw-bold">$${finalAmount.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
            `;
            chargeCalculation.style.display = 'block';
        } else {
            chargeCalculation.style.display = 'none';
        }
    }

    // Validate amount against method limits on form submission
    const withdrawForm = document.querySelector('form[action*="withdraw.wallet.submit"]');
    const submitButton = withdrawForm?.querySelector('button[type="submit"]');
    
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', function(e) {
            if (!methodSelect || !amountInput) return;
            
            // Show immediate loading feedback
            if (submitButton) {
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processing...';
                
                // Re-enable button after 10 seconds in case of network issues
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }, 10000);
            }
            
            // Show processing notification
            Swal.fire({
                title: 'Processing Withdrawal',
                text: 'Please wait while we process your request...',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 3000,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const amount = parseFloat(amountInput.value);
            
            if (methodSelect.value && amount > 0 && selectedOption) {
                const minAmount = parseFloat(selectedOption.dataset.min) || 0;
                const maxAmount = parseFloat(selectedOption.dataset.max) || 999999;
                
                if (amount < minAmount) {
                    e.preventDefault();
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                    Swal.close();
                    Swal.fire({
                        title: 'Invalid Amount',
                        text: `Minimum withdrawal amount for this method is $${minAmount.toFixed(2)}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                if (amount > Math.min(maxAmount, {{ $totalWalletBalance }})) {
                    e.preventDefault();
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                    Swal.close();
                    Swal.fire({
                        title: 'Amount Too High',
                        text: `Maximum withdrawal amount for this method is $${Math.min(maxAmount, {{ $totalWalletBalance }}).toFixed(2)}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }
        });
    }

    // Handle SweetAlert messages from backend
    @if(session('swal_success'))
        Swal.fire({
            title: '{{ session("swal_success.title") }}',
            text: '{{ session("swal_success.text") }}',
            icon: '{{ session("swal_success.icon") }}',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true
        });
    @endif

    @if(session('swal_error'))
        Swal.fire({
            title: '{{ session("swal_error.title") }}',
            text: '{{ session("swal_error.text") }}',
            icon: '{{ session("swal_error.icon") }}',
            confirmButtonText: 'OK'
        });
    @endif

    // Handle regular session messages
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session("success") }}',
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session("error") }}',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif

    // Auto-hide Bootstrap alerts after showing SweetAlert
    @if(session('success') || session('error'))
        // Hide the Bootstrap alert since we're showing SweetAlert
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    @endif

    // Add loading overlay for better UX
    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
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
        `;
        overlay.innerHTML = `
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 mb-0">Processing your withdrawal...</p>
            </div>
        `;
        document.body.appendChild(overlay);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            const existingOverlay = document.getElementById('loadingOverlay');
            if (existingOverlay) {
                existingOverlay.remove();
            }
        }, 10000);
    }

    // Enhanced form submission with better feedback
    if (withdrawForm) {
        const originalSubmitHandler = withdrawForm.onsubmit;
        withdrawForm.addEventListener('submit', function(e) {
            // Run original validation first
            if (originalSubmitHandler && !originalSubmitHandler.call(this, e)) {
                return;
            }
            
            // If validation passes, show loading
            if (!e.defaultPrevented) {
                showLoadingOverlay();
            }
        });
    }
});
</script>
@endpush
@endsection
</x-smart_layout>
