<x-smart_layout>

@section('title', $pageTitle)

@section('content')
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745',
                    timer: 5000,
                    timerProgressBar: true,
                    showCloseButton: true
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545',
                    showCloseButton: true
                });
            });
        </script>
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
                                <button type="button" class="btn btn-warning" onclick="showKycAlert()">
                                    <i class="fas fa-user-check me-2"></i>Complete KYC Verification
                                </button>
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
                                        <label for="withdraw_method" class="form-label">Withdrawal Method <span class="text-danger">*</span></label>
                                        <select class="form-select @error('method_id') is-invalid @enderror" 
                                                id="withdraw_method" name="method_id" required>
                                            <option value="">Select Method</option>
                                            @forelse($withdrawMethods as $method)
                                                <option value="{{ $method->id }}" 
                                                        data-min="{{ $method->min_amount }}"
                                                        data-max="{{ $method->max_amount }}"
                                                        data-fixed-charge="{{ $method->fixed_charge ?? 0 }}"
                                                        data-percent-charge="{{ $method->percent_charge ?? 0 }}"
                                                        data-daily-limit="{{ $method->daily_limit ?? 0 }}"
                                                        {{ old('method_id') == $method->id ? 'selected' : '' }}>
                                                    {{ $method->name }} 
                                                    (Min: ${{ number_format($method->min_amount, 2) }}, Max: ${{ number_format($method->max_amount, 2) }})
                                                    @if($method->fixed_charge > 0 || $method->percent_charge > 0)
                                                        - Charges: 
                                                        @if($method->fixed_charge > 0)${{ number_format($method->fixed_charge, 2) }}@endif
                                                        @if($method->fixed_charge > 0 && $method->percent_charge > 0) + @endif
                                                        @if($method->percent_charge > 0){{ $method->percent_charge }}%@endif
                                                    @endif
                                                </option>
                                            @empty
                                                <option value="" disabled>No withdrawal methods available</option>
                                            @endforelse
                                        </select>
                                        @error('method_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="method-info" class="mt-2" style="display: none;">
                                            <div class="alert alert-info">
                                                <div id="method-details"></div>
                                                <div id="charge-calculation" class="mt-2"></div>
                                            </div>
                                        </div>
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

                            <div class="mb-3">
                                <label for="password" class="form-label">Transaction Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fe fe-info me-2"></i>
                                <strong>Important:</strong> Withdrawal charges depend on the selected method. Please check the charge calculation below before submitting your request.
                            </div>

                            <button type="submit" class="btn btn-primary" 
                                    {{ (!isset($kycVerified) || !$kycVerified) ? 'disabled' : '' }}>
                                <i class="fe fe-send me-2"></i>
                                Submit Withdrawal Request
                            </button>
                            
                            @if(!isset($kycVerified) || !$kycVerified)
                                <div class="mt-3">
                                    <button type="button" class="btn btn-warning" onclick="showKycAlert()">
                                        <i class="fas fa-user-check me-2"></i>Complete KYC to Withdraw
                                    </button>
                                </div>
                            @endif
                        </form>
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
                                @if($withdrawal->status == 2)
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($withdrawal->status == 1)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($withdrawal->status == 3)
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
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

<script>
// KYC Alert Function
function showKycAlert() {
    Swal.fire({
        title: 'KYC Verification Required',
        html: `
            <div class="text-start">
                <p><i class="fas fa-shield-alt text-warning me-2"></i>To ensure the security of your withdrawals, you need to complete KYC verification.</p>
                <p><strong>Required for:</strong></p>
                <ul class="text-start">
                    <li>Wallet withdrawals</li>
                    <li>Higher transaction limits</li>
                    <li>Account security</li>
                </ul>
                <p class="text-muted small mt-3">This process typically takes 24-48 hours for approval.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Complete KYC Now',
        cancelButtonText: 'Later',
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route('user.kyc.index') }}';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const methodSelect = document.getElementById('withdraw_method');
    const methodInfo = document.getElementById('method-info');
    const methodDetails = document.getElementById('method-details');
    const chargeCalculation = document.getElementById('charge-calculation');
    const maxAmount = {{ $totalWalletBalance }};
    
    // Show insufficient balance alert
    @if($totalWalletBalance <= 0)
        Swal.fire({
            title: 'Insufficient Balance',
            text: 'You don\'t have sufficient wallet balance to make a withdrawal.',
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
    @endif
    
    // Amount input validation
    amountInput.addEventListener('input', function() {
        if (parseFloat(this.value) > maxAmount) {
            this.value = maxAmount;
            Swal.fire({
                title: 'Amount Adjusted',
                text: `Amount has been adjusted to your maximum available balance: $${maxAmount.toFixed(2)}`,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
        updateChargeCalculation();
    });

    // Method selection handler
    methodSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const minAmount = parseFloat(selectedOption.dataset.min);
            const maxAmount = parseFloat(selectedOption.dataset.max);
            const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge || 0);
            const percentCharge = parseFloat(selectedOption.dataset.percentCharge || 0);
            const dailyLimit = parseFloat(selectedOption.dataset.dailyLimit || 0);
            
            // Update amount input constraints
            amountInput.min = minAmount;
            amountInput.max = Math.min(maxAmount, {{ $totalWalletBalance }});
            
            // Show method information
            let chargeInfo = '';
            if (fixedCharge > 0 && percentCharge > 0) {
                chargeInfo = `$${fixedCharge.toFixed(2)} + ${percentCharge}%`;
            } else if (fixedCharge > 0) {
                chargeInfo = `$${fixedCharge.toFixed(2)} fixed`;
            } else if (percentCharge > 0) {
                chargeInfo = `${percentCharge}% of amount`;
            } else {
                chargeInfo = 'No charges';
            }
            
            methodDetails.innerHTML = `
                <strong>Selected Method:</strong> ${selectedOption.text.split('(')[0].trim()}<br>
                <strong>Amount Limits:</strong> $${minAmount.toFixed(2)} - $${Math.min(maxAmount, {{ $totalWalletBalance }}).toFixed(2)}<br>
                <strong>Charges:</strong> ${chargeInfo}${dailyLimit > 0 ? '<br><strong>Daily Limit:</strong> $' + dailyLimit.toFixed(2) : ''}
            `;
            
            methodInfo.style.display = 'block';
            updateChargeCalculation();
            
            // Show method selection success toast
            Swal.fire({
                title: 'Method Selected',
                text: `${selectedOption.text.split('(')[0].trim()} selected successfully`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            methodInfo.style.display = 'none';
            amountInput.min = 1;
            amountInput.max = {{ $totalWalletBalance }};
        }
    });

    function updateChargeCalculation() {
        const selectedOption = methodSelect.options[methodSelect.selectedIndex];
        const amount = parseFloat(amountInput.value);
        
        if (methodSelect.value && amount > 0) {
            const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge || 0);
            const percentCharge = parseFloat(selectedOption.dataset.percentCharge || 0);
            
            // Calculate total charges
            const percentageFee = (amount * percentCharge) / 100;
            const totalCharge = fixedCharge + percentageFee;
            const finalAmount = amount - totalCharge;
            
            chargeCalculation.innerHTML = `
                <div class="row text-center">
                    <div class="col-4">
                        <strong>Requested:</strong><br>
                        <span class="text-primary">$${amount.toFixed(2)}</span>
                    </div>
                    <div class="col-4">
                        <strong>Total Charge:</strong><br>
                        <span class="text-warning">$${totalCharge.toFixed(2)}</span>
                        ${(fixedCharge > 0 && percentCharge > 0) ? '<br><small>($' + fixedCharge.toFixed(2) + ' + ' + percentageFee.toFixed(2) + ')</small>' : ''}
                    </div>
                    <div class="col-4">
                        <strong>You'll Receive:</strong><br>
                        <span class="text-success">$${finalAmount.toFixed(2)}</span>
                    </div>
                </div>
            `;
        } else {
            chargeCalculation.innerHTML = '';
        }
    }

    // Validate amount against method limits on form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedOption = methodSelect.options[methodSelect.selectedIndex];
        const amount = parseFloat(amountInput.value);
        
        if (methodSelect.value && amount > 0) {
            const minAmount = parseFloat(selectedOption.dataset.min);
            const maxAmount = parseFloat(selectedOption.dataset.max);
            const availableBalance = {{ $totalWalletBalance }};
            
            if (amount < minAmount) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Amount!',
                    text: `Minimum withdrawal amount for this method is $${minAmount.toFixed(2)}`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                }).then(() => {
                    amountInput.focus();
                });
                return;
            }
            
            if (amount > maxAmount) {
                e.preventDefault();
                Swal.fire({
                    title: 'Amount Exceeds Limit!',
                    text: `Maximum withdrawal amount for this method is $${maxAmount.toFixed(2)}`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                }).then(() => {
                    amountInput.focus();
                });
                return;
            }
            
            if (amount > availableBalance) {
                e.preventDefault();
                Swal.fire({
                    title: 'Insufficient Balance!',
                    text: `Insufficient wallet balance. Available: $${availableBalance.toFixed(2)}`,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                }).then(() => {
                    amountInput.focus();
                });
                return;
            }
            
            // Show confirmation dialog before submitting
            e.preventDefault();
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge || 0);
            const percentCharge = parseFloat(selectedOption.dataset.percentCharge || 0);
            const percentageFee = (amount * percentCharge) / 100;
            const totalCharge = fixedCharge + percentageFee;
            const finalAmount = amount - totalCharge;
            
            Swal.fire({
                title: 'Confirm Withdrawal',
                html: `
                    <div class="text-start">
                        <p><strong>Method:</strong> ${selectedOption.text.split('(')[0].trim()}</p>
                        <p><strong>Requested Amount:</strong> $${amount.toFixed(2)}</p>
                        <p><strong>Total Charges:</strong> $${totalCharge.toFixed(2)}</p>
                        <p><strong>You'll Receive:</strong> <span class="text-success">$${finalAmount.toFixed(2)}</span></p>
                        <hr>
                        <p class="text-muted small">Are you sure you want to proceed with this withdrawal?</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Withdraw',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show processing message
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your withdrawal request.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form
                    e.target.submit();
                }
            });
        }
    });
});
</script>
@endsection
</x-smart_layout>
