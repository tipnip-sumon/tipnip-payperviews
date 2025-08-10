<x-smart_layout>

@section('title', $pageTitle)

@section('content')
<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

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
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="session-success">
            <i class="fe fe-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="session-error">
            <i class="fe fe-alert-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert" id="session-warning">
            <i class="fe fe-alert-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert" id="session-info">
            <i class="fe fe-info-circle me-2"></i>
            {{ session('info') }}
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
                        <form action="{{ route('user.withdraw.wallet.submit') }}" method="POST" id="withdrawForm"
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
                                                   step="0.01" value="{{ session('wallet_withdrawal_form_data.amount') ?? old('amount') }}" 
                                                   {{ session('wallet_otp_required') ? 'readonly' : '' }} required>
                                            @if(session('wallet_otp_required'))
                                                <span class="input-group-text bg-success text-white">
                                                    <i class="ri-check-line"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @if(session('wallet_otp_required'))
                                            <small class="text-success">Available Balance: ${{ number_format($totalWalletBalance, 2) }} - Amount saved</small>
                                        @else
                                            <small class="text-muted">Available Balance: ${{ number_format($totalWalletBalance, 2) }}</small>
                                        @endif
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="withdraw_method" class="form-label">Withdrawal Method <span class="text-danger">*</span></label>
                                        <select class="form-select @error('method_id') is-invalid @enderror" 
                                                id="withdraw_method" name="method_id" 
                                                {{ session('wallet_otp_required') ? 'readonly disabled' : '' }} required>
                                            <option value="">Select Method</option>
                                            @forelse($withdrawMethods as $method)
                                                <option value="{{ $method->id }}" 
                                                        data-min="{{ $method->min_amount }}"
                                                        data-max="{{ $method->max_amount }}"
                                                        data-fixed-charge="{{ $method->fixed_charge ?? 0 }}"
                                                        data-percent-charge="{{ $method->percent_charge ?? 0 }}"
                                                        data-daily-limit="{{ $method->daily_limit ?? 0 }}"
                                                        data-currency="{{ $method->currency ?? 'USD' }}"
                                                        data-icon="{{ $method->icon ?? 'fe fe-credit-card' }}"
                                                        {{ (old('method_id') == $method->id || (session('wallet_withdrawal_form_data.method_id') == $method->id)) ? 'selected' : '' }}>
                                                    @if($method->icon)
                                                        <i class="{{ $method->icon }} me-2"></i>
                                                    @endif
                                                    {{ $method->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No withdrawal methods available</option>
                                            @endforelse
                                        </select>
                                        @if(session('wallet_otp_required'))
                                            <small class="text-success">
                                                <i class="ri-check-line me-1"></i>
                                                Method selected and saved
                                            </small>
                                        @endif
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
                                @if(session('wallet_otp_required'))
                                    <div class="input-group">
                                        <textarea class="form-control @error('account_details') is-invalid @enderror" 
                                                  id="account_details" name="account_details" rows="4" readonly required>{{ session('wallet_withdrawal_form_data.account_details') ?? old('account_details') }}</textarea>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ri-check-line"></i>
                                        </span>
                                    </div>
                                    <small class="text-success">Account details saved</small>
                                @else
                                    <textarea class="form-control @error('account_details') is-invalid @enderror" 
                                              id="account_details" name="account_details" rows="4" required
                                              placeholder="Please provide your account details (account number, email, wallet address, etc.)">{{ old('account_details') }}</textarea>
                                @endif
                                @error('account_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Transaction Password <span class="text-danger">*</span></label>
                                @if(session('wallet_otp_required'))
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" value="••••••••" readonly required>
                                        <input type="hidden" name="password" value="{{ session('wallet_withdrawal_form_data.password') ?? '' }}">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ri-check-line"></i>
                                        </span>
                                    </div>
                                    <small class="text-success">
                                        <i class="ri-check-line me-1"></i>
                                        Password verified. Ready for OTP verification.
                                    </small>
                                @else
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                @endif
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fe fe-info me-2"></i>
                                <strong>Important:</strong> Withdrawal charges depend on the selected method. Please check the charge calculation below before submitting your request.
                            </div>

                            @if(!isset($kycVerified) || !$kycVerified)
                                <div class="mt-3">
                                    <button type="button" class="btn btn-warning" onclick="showKycAlert()">
                                        <i class="fas fa-user-check me-2"></i>Complete KYC to Withdraw
                                    </button>
                                </div>
                            @else
                                <!-- Submit Button Section -->
                                <div class="col-12">
                                    <div class="mt-4">
                                        @if($walletOtpRequired && !session('wallet_otp_required'))
                                            <!-- Send OTP Button -->
                                            <div class="text-center">
                                                <button class="btn btn-info btn-lg w-100" type="button" id="send-wallet-otp-btn">
                                                    <span id="send-wallet-otp-spinner" class="spinner-border spinner-border-sm me-2" style="display: none;"></span>
                                                    <span id="send-wallet-otp-text">Send Verification Code</span>
                                                </button>
                                                <small class="text-muted d-block mt-2">A 6-digit verification code will be sent to your email</small>
                                            </div>
                                        @elseif($walletOtpRequired && session('wallet_otp_required'))
                                            <!-- OTP Verification Section -->
                                            <div class="text-center">
                                                <div class="mb-3">
                                                    <label for="otp" class="form-label">Verification Code <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('otp') is-invalid @enderror text-center" 
                                                           id="otp" name="otp" maxlength="6" placeholder="Enter 6-digit code" required>
                                                    <small class="text-success">
                                                        <i class="ri-mail-check-line me-1"></i>
                                                        Verification code sent to your email. Please check your inbox.
                                                    </small>
                                                    @error('otp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <button class="btn btn-primary btn-lg w-100" type="button" id="walletWithdrawBtn">
                                                    <i class="ri-shield-check-line me-2"></i>Verify Code & Withdraw
                                                </button>
                                            </div>
                                        @else
                                            <!-- Direct Submit (No OTP Required) -->
                                            <div class="text-center">
                                                <button class="btn btn-success btn-lg w-100" type="button" id="walletWithdrawBtn">
                                                    <i class="ri-send-plane-line me-2"></i>Submit Withdrawal Request
                                                </button>
                                            </div>
                                        @endif
                                    </div>
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
@endsection

@push('script')
<script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>

<script>
$(document).ready(function() {
    console.log('jQuery loaded and ready!'); // Debug log
    console.log('Send OTP button exists:', $('#send-wallet-otp-btn').length > 0); // Debug log
    
    // Flag to control form submission
    let allowFormSubmission = false;
    
    // Prevent unwanted form submissions (only allow via AJAX or explicit submit handlers)
    $('#withdrawForm').on('submit', function(e) {
        if (!allowFormSubmission) {
            e.preventDefault();
            console.log('Form submit intercepted'); // Debug log
            return false;
        }
        console.log('Form submission allowed'); // Debug log
    });

    // Function to allow form submission
    window.allowWithdrawalFormSubmission = function() {
        allowFormSubmission = true;
    };
    
    // Initialize withdrawal page functionality
    initializeWithdrawalPage();
    
    // Handle Send OTP Button Click (AJAX)
    $(document).on('click', '#send-wallet-otp-btn', function(e) {
        e.preventDefault();
        console.log('Send OTP button clicked!'); // Debug log
        
        const $btn = $(this);
        const $form = $('#withdrawForm');
        const $spinner = $('#send-wallet-otp-spinner');
        const $text = $('#send-wallet-otp-text');
        
        console.log('Button found:', $btn.length > 0); // Debug log
        console.log('Form found:', $form.length > 0); // Debug log
        
        // Collect form data manually to handle readonly/disabled fields
        const formData = {
            amount: $('#amount').val(),
            method_id: $('#withdraw_method').val(),
            account_details: $('#account_details').val(),
            password: $('input[name="password"][type="hidden"]').val() || $('#password').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Debug: Log form data to console
        console.log('Form data being sent:', formData);

        // Validate form data
        if (!formData.amount || !formData.method_id || !formData.account_details || !formData.password) {
            console.log('Validation failed - missing fields:', {
                amount: formData.amount,
                method_id: formData.method_id,
                account_details: formData.account_details,
                password: formData.password ? '***masked***' : 'empty'
            });
            
            Swal.fire({
                title: 'Missing Information!',
                text: 'Please fill in all required fields before sending OTP',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Show loading state
        $btn.prop('disabled', true);
        $spinner.show();
        $text.text('Sending...');
        
        console.log('=== About to send AJAX request ===');
        console.log('Button disabled:', $btn.prop('disabled'));
        console.log('Spinner visible:', $spinner.is(':visible'));

        // Send AJAX request
        console.log('=== AJAX Request Debug ===');
        console.log('URL:', '{{ route("user.withdraw.wallet.send-otp") }}');
        console.log('Form data:', formData);
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: '{{ route("user.withdraw.wallet.send-otp") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('=== AJAX Success Debug ===');
                console.log('Response:', response);
                
                if (response.success) {
                    // Store expiry time for countdown
                    if (response.expires_at) {
                        sessionStorage.setItem('otp_expires_at', response.expires_at);
                    }
                    
                    Swal.fire({
                        title: 'Code Sent!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload the page to show OTP form
                        location.reload();
                    });
                } else {
                    throw new Error(response.message || 'Failed to send OTP');
                }
            },
            error: function(xhr) {
                console.error('=== AJAX Error Debug ===');
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Response Text:', xhr.responseText);
                console.error('Response JSON:', xhr.responseJSON);
                console.error('Full XHR object:', xhr);
                
                let message = 'Failed to send verification code. Please try again.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        message = 'Validation errors: ' + errors.join(', ');
                    }
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Reset button state
                $btn.prop('disabled', false);
                $spinner.hide();
                $text.text('Send Verification Code');
            }
        });
    });
    
    // Handle Withdrawal Button Click
    $(document).on('click', '#walletWithdrawBtn', function(e) {
        e.preventDefault();
        console.log('Withdrawal button clicked!'); // Debug log
        submitWithdrawalForm();
    });

    // Handle actual withdrawal submission (called from within the app)
    function submitWithdrawalForm() {
        const $form = $('#withdrawForm');
        const walletOtpRequired = {{ $walletOtpRequired ? 'true' : 'false' }};
        const otpSession = {{ session('wallet_otp_required') ? 'true' : 'false' }};
        const otpCode = $('#otp').val();
        const methodName = $('#withdraw_method option:selected').text();
        const amount = $('#amount').val();

        // If OTP is required and we're in OTP session, validate OTP
        if (walletOtpRequired && otpSession) {
            if (!otpCode || otpCode.length !== 6) {
                Swal.fire({
                    title: 'Invalid OTP!',
                    text: 'Please enter the 6-digit verification code sent to your email',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                $('#otp').focus();
                return false;
            }
            
            // Confirm OTP verification
            Swal.fire({
                title: 'Verify OTP & Complete Withdrawal',
                html: `Enter verification code: <strong>${otpCode}</strong><br><br>
                       Withdrawing $${amount} via <strong>${methodName}</strong><br>
                       <small class="text-muted">This will complete your wallet withdrawal request</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Verify & Withdraw',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#walletWithdrawBtn').html('<i class="ri-loader-4-line me-2 spin"></i>Verifying OTP...').prop('disabled', true);
                    
                    // Get fresh CSRF token from dedicated endpoint
                    $.get('{{ route("user.withdraw.wallet.csrf-token") }}', function(response) {
                        if (response.csrf_token) {
                            // Update both meta tag and form token
                            $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                            $('#withdrawForm').find('input[name="_token"]').val(response.csrf_token);
                            console.log('Fresh CSRF token obtained:', response.csrf_token.substr(0, 10) + '...');
                        }
                        
                        // Allow form submission and submit
                        console.log('=== Form Submission Debug ===');
                        console.log('Form action URL:', $('#withdrawForm').attr('action'));
                        console.log('Form method:', $('#withdrawForm').attr('method'));
                        console.log('CSRF token:', $('#withdrawForm').find('input[name="_token"]').val());
                        
                        window.allowWithdrawalFormSubmission();
                        $('#withdrawForm')[0].submit();
                        
                    }).fail(function() {
                        console.log('Failed to get fresh CSRF token, proceeding with existing token');
                        console.log('=== Fallback Form Submission Debug ===');
                        console.log('Form action URL:', $('#withdrawForm').attr('action'));
                        console.log('Form method:', $('#withdrawForm').attr('method'));
                        
                        // Fallback: try with existing token
                        window.allowWithdrawalFormSubmission();
                        $('#withdrawForm')[0].submit();
                    });
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Please use the Send Verification Code button first.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
});

// Wait for DOM to be loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if this is a redirect after form submission
    const isRedirectAfterSubmission = sessionStorage.getItem('withdrawalSubmitted') === 'true';
    
    // Clear submission flags on fresh load
    if (!isRedirectAfterSubmission) {
        sessionStorage.removeItem('withdrawalSubmitting');
        sessionStorage.removeItem('withdrawalSubmitted');
    } else {
        sessionStorage.removeItem('withdrawalSubmitted');
    }
    
    // Wait for SweetAlert to be fully loaded
    function waitForSweetAlert() {
        if (typeof Swal !== 'undefined') {
            initializeWithdrawalPage();
        } else {
            setTimeout(waitForSweetAlert, 100);
        }
    }
    
    waitForSweetAlert();
});

// KYC Alert Function
function showKycAlert() {
    if (typeof Swal === 'undefined') {
        if (confirm('KYC Verification Required - Please complete KYC verification to withdraw funds. Click OK to proceed to KYC verification.')) {
            window.location.href = '{{ route('user.kyc.index') }}';
        }
        return;
    }
    
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
        reverseButtons: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route('user.kyc.index') }}';
        }
    }).catch((error) => {
        if (confirm('KYC Verification Required - Please complete KYC verification to withdraw funds. Click OK to proceed.')) {
            window.location.href = '{{ route('user.kyc.index') }}';
        }
    });
}

// Function to show SweetAlert messages
function showSweetAlertMessage(type, title, message) {
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        return false; // SweetAlert not available, keep HTML alert
    }
    
    const config = {
        title: title,
        text: message,
        confirmButtonText: 'OK',
        showCloseButton: true,
        allowOutsideClick: true,
        allowEscapeKey: true
    };
    
    switch(type) {
        case 'success':
            config.icon = 'success';
            config.confirmButtonColor = '#28a745';
            config.timer = 5000;
            config.timerProgressBar = true;
            break;
        case 'error':
            config.icon = 'error';
            config.confirmButtonColor = '#dc3545';
            break;
        case 'warning':
            config.icon = 'warning';
            config.confirmButtonColor = '#ffc107';
            break;
        case 'info':
            config.icon = 'info';
            config.confirmButtonColor = '#17a2b8';
            break;
        default:
            config.icon = 'info';
            config.confirmButtonColor = '#6c757d';
    }
    
    try {
        Swal.fire(config);
        return true; // SweetAlert shown successfully
    } catch (error) {
        return false; // Failed to show SweetAlert, keep HTML alert
    }
}

// Main initialization function
function initializeWithdrawalPage() {
    // Add flag to prevent multiple confirmations
    let isSubmitting = false;
    
    // Check for session messages and show SweetAlert
    // Only show messages if this isn't a rapid succession of page loads
    const lastMessageTime = sessionStorage.getItem('lastMessageTime');
    const currentTime = Date.now();
    const shouldShowMessage = !lastMessageTime || (currentTime - parseInt(lastMessageTime)) > 2000; // 2 second cooldown
    
    if (shouldShowMessage) {
        @if(session('success'))
            if (showSweetAlertMessage('success', 'Success!', '{{ addslashes(session('success')) }}')) {
                // Hide HTML alert if SweetAlert was shown successfully
                const htmlAlert = document.getElementById('session-success');
                if (htmlAlert) htmlAlert.style.display = 'none';
            }
            sessionStorage.setItem('lastMessageTime', currentTime.toString());
        @endif

        @if(session('error'))
            if (showSweetAlertMessage('error', 'Error!', '{{ addslashes(session('error')) }}')) {
                // Hide HTML alert if SweetAlert was shown successfully
                const htmlAlert = document.getElementById('session-error');
                if (htmlAlert) htmlAlert.style.display = 'none';
            }
            sessionStorage.setItem('lastMessageTime', currentTime.toString());
        @endif

        @if(session('warning'))
            if (showSweetAlertMessage('warning', 'Warning!', '{{ addslashes(session('warning')) }}')) {
                // Hide HTML alert if SweetAlert was shown successfully
                const htmlAlert = document.getElementById('session-warning');
                if (htmlAlert) htmlAlert.style.display = 'none';
            }
            sessionStorage.setItem('lastMessageTime', currentTime.toString());
        @endif

        @if(session('info'))
            if (showSweetAlertMessage('info', 'Information', '{{ addslashes(session('info')) }}')) {
                // Hide HTML alert if SweetAlert was shown successfully
                const htmlAlert = document.getElementById('session-info');
                if (htmlAlert) htmlAlert.style.display = 'none';
            }
            sessionStorage.setItem('lastMessageTime', currentTime.toString());
        @endif
    }
    
    // Get DOM elements
    const amountInput = document.getElementById('amount');
    const methodSelect = document.getElementById('withdraw_method');
    const methodInfo = document.getElementById('method-info');
    const methodDetails = document.getElementById('method-details');
    const chargeCalculation = document.getElementById('charge-calculation');
    const maxAmount = {{ $totalWalletBalance }};
    
    // Show insufficient balance alert
    @if($totalWalletBalance <= 0)
        setTimeout(() => {
            showSweetAlertMessage('warning', 'Insufficient Balance', 'You don\'t have sufficient wallet balance to make a withdrawal.');
        }, 500);
    @endif
    
    // Amount input validation
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (parseFloat(this.value) > maxAmount) {
                this.value = maxAmount;
                if (typeof Swal !== 'undefined') {
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
            }
            updateChargeCalculation();
        });
    }

    // Method selection handler
    if (methodSelect) {
        methodSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value) {
                const minAmount = parseFloat(selectedOption.dataset.min);
                const maxAmount = parseFloat(selectedOption.dataset.max);
                const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge || 0);
                const percentCharge = parseFloat(selectedOption.dataset.percentCharge || 0);
                const dailyLimit = parseFloat(selectedOption.dataset.dailyLimit || 0);
                const currency = selectedOption.dataset.currency || 'USD';
                const icon = selectedOption.dataset.icon || 'fe fe-credit-card';
                
                // Update amount input constraints
                if (amountInput) {
                    amountInput.min = minAmount;
                    amountInput.max = Math.min(maxAmount, {{ $totalWalletBalance }});
                }
                
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
                
                if (methodDetails) {
                    methodDetails.innerHTML = `
                        <div class="d-flex align-items-center mb-2">
                            <i class="${icon} me-2 text-primary"></i>
                            <strong>Selected Method:</strong> <span class="ms-1">${currency} - ${selectedOption.text.split('(')[0].trim().replace(/^[A-Z]{3,4}\s-\s/, '')}</span>
                        </div>
                        <strong>Amount Limits:</strong> $${minAmount.toFixed(2)} - $${Math.min(maxAmount, {{ $totalWalletBalance }}).toFixed(2)}<br>
                        <strong>Currency:</strong> ${currency}<br>
                        <strong>Charges:</strong> ${chargeInfo}${dailyLimit > 0 ? '<br><strong>Daily Limit:</strong> $' + dailyLimit.toFixed(2) : ''}
                    `;
                }
                
                if (methodInfo) {
                    methodInfo.style.display = 'block';
                }
                updateChargeCalculation();
                
                // Show method selection success toast
                if (typeof Swal !== 'undefined') {
                    const currency = selectedOption.dataset.currency || 'USD';
                    const methodName = selectedOption.text.split('(')[0].trim().replace(/^[A-Z]{3,4}\s-\s/, '');
                    Swal.fire({
                        title: 'Method Selected',
                        html: `<i class="${icon} me-2 text-primary"></i>${currency} - ${methodName} selected successfully`,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            } else {
                if (methodInfo) {
                    methodInfo.style.display = 'none';
                }
                if (amountInput) {
                    amountInput.min = 1;
                    amountInput.max = {{ $totalWalletBalance }};
                }
            }
        });
    }

    
    // Charge calculation function
    function updateChargeCalculation() {
        if (!methodSelect || !chargeCalculation || !amountInput) return;
        
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
    const withdrawForm = document.querySelector('form');
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', function(e) {
            // Check if we're already in the middle of submitting
            const alreadySubmitting = sessionStorage.getItem('withdrawalSubmitting') === 'true' || isSubmitting;
            
            // If already submitting, allow the form to go through normally
            if (alreadySubmitting) {
                return true;
            }
            
            if (!methodSelect || !amountInput) {
                return;
            }
            
            const selectedOption = methodSelect.options[methodSelect.selectedIndex];
            const amount = parseFloat(amountInput.value);
            
            if (methodSelect.value && amount > 0) {
                const minAmount = parseFloat(selectedOption.dataset.min);
                const maxAmount = parseFloat(selectedOption.dataset.max);
                const availableBalance = {{ $totalWalletBalance }};
                
                if (amount < minAmount) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Invalid Amount!',
                            text: `Minimum withdrawal amount for this method is $${minAmount.toFixed(2)}`,
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107'
                        }).then(() => {
                            amountInput.focus();
                        });
                    } else {
                        alert(`Minimum withdrawal amount for this method is $${minAmount.toFixed(2)}`);
                        amountInput.focus();
                    }
                    return;
                }
                
                if (amount > maxAmount) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Amount Exceeds Limit!',
                            text: `Maximum withdrawal amount for this method is $${maxAmount.toFixed(2)}`,
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107'
                        }).then(() => {
                            amountInput.focus();
                        });
                    } else {
                        alert(`Maximum withdrawal amount for this method is $${maxAmount.toFixed(2)}`);
                        amountInput.focus();
                    }
                    return;
                }
                
                if (amount > availableBalance) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Insufficient Balance!',
                            text: `Insufficient wallet balance. Available: $${availableBalance.toFixed(2)}`,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        }).then(() => {
                            amountInput.focus();
                        });
                    } else {
                        alert(`Insufficient wallet balance. Available: $${availableBalance.toFixed(2)}`);
                        amountInput.focus();
                    }
                    return;
                }
                
                // Show confirmation dialog before submitting
                e.preventDefault();
                
                const fixedCharge = parseFloat(selectedOption.dataset.fixedCharge || 0);
                const percentCharge = parseFloat(selectedOption.dataset.percentCharge || 0);
                const percentageFee = (amount * percentCharge) / 100;
                const totalCharge = fixedCharge + percentageFee;
                const finalAmount = amount - totalCharge;
                
                if (typeof Swal !== 'undefined') {
                    const currency = selectedOption.dataset.currency || 'USD';
                    const icon = selectedOption.dataset.icon || 'fe fe-credit-card';
                    const methodName = selectedOption.text.split('(')[0].trim().replace(/^[A-Z]{3,4}\s-\s/, '');
                    
                    Swal.fire({
                        title: 'Confirm Withdrawal',
                        html: `
                            <div class="text-start">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="${icon} me-2 text-primary fs-18"></i>
                                    <strong>Method:</strong> <span class="ms-1">${currency} - ${methodName}</span>
                                </div>
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
                            // Set flags to prevent multiple confirmations
                            isSubmitting = true;
                            sessionStorage.setItem('withdrawalSubmitting', 'true');
                            sessionStorage.setItem('withdrawalSubmitted', 'true');
                            
                            // Show loading on submit button
                            const submitButton = withdrawForm.querySelector('button[type="submit"]');
                            if (submitButton) {
                                submitButton.disabled = true;
                                submitButton.innerHTML = '<i class="fe fe-loader me-2 spin"></i>Processing...';
                            }
                            
                            // Refresh CSRF token from meta tag
                            const csrfToken = withdrawForm.querySelector('input[name="_token"]');
                            const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                            if (metaCsrf && csrfToken) {
                                csrfToken.value = metaCsrf.getAttribute('content');
                            }
                            
                            // Submit the form
                            withdrawForm.submit();
                        }
                    });
                } else {
                    // Fallback confirmation
                    const confirmed = confirm(`Confirm Withdrawal?\n\nMethod: ${selectedOption.text.split('(')[0].trim()}\nAmount: $${amount.toFixed(2)}\nCharges: $${totalCharge.toFixed(2)}\nYou'll Receive: $${finalAmount.toFixed(2)}\n\nProceed?`);
                    if (confirmed) {
                        isSubmitting = true;
                        sessionStorage.setItem('withdrawalSubmitting', 'true');
                        sessionStorage.setItem('withdrawalSubmitted', 'true');
                        
                        // Show loading on submit button
                        const submitButton = withdrawForm.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fe fe-loader me-2 spin"></i>Processing...';
                        }
                        
                        // Refresh CSRF token from meta tag
                        const csrfToken = withdrawForm.querySelector('input[name="_token"]');
                        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                        if (metaCsrf && csrfToken) {
                            csrfToken.value = metaCsrf.getAttribute('content');
                        }
                        
                        withdrawForm.submit();
                    }
                }
            }
        });
    }
    
    // OTP Timer and Auto-Resend Functionality
    @if($walletOtpRequired && session('wallet_otp_required'))
    $(document).ready(function() {
        // Check for stored expiry time
        const expiryTime = sessionStorage.getItem('otp_expires_at');
        if (expiryTime) {
            startOtpCountdown(expiryTime);
        }
        
        // Add resend button after OTP input
        const otpInput = $('#otp');
        if (otpInput.length && !$('#resend-otp-btn').length) {
            const resendButton = `
                <div class="mt-2 text-center">
                    <span id="otp-timer" class="text-muted small"></span>
                    <button type="button" id="resend-otp-btn" class="btn btn-link btn-sm p-0" style="display: none;">
                        <span id="resend-otp-spinner" class="spinner-border spinner-border-sm me-1" style="display: none;"></span>
                        Resend Code
                    </button>
                </div>
            `;
            otpInput.closest('.mb-3').append(resendButton);
        }
        
        // Handle resend button click
        $(document).on('click', '#resend-otp-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $spinner = $('#resend-otp-spinner');
            
            $btn.prop('disabled', true);
            $spinner.show();
            
            // Get form data for resending
            const formData = {
                amount: $('#amount').val(),
                method_id: $('#withdraw_method').val(),
                account_details: $('#account_details').val(),
                password: $('input[name="password"][type="hidden"]').val() || $('#password').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: '{{ route("user.withdraw.wallet.send-otp") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Store new expiry time
                        if (response.expires_at) {
                            sessionStorage.setItem('otp_expires_at', response.expires_at);
                            startOtpCountdown(response.expires_at);
                        }
                        
                        // Show success message
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'New verification code sent!'
                        });
                    } else {
                        throw new Error(response.message || 'Failed to resend OTP');
                    }
                },
                error: function(xhr) {
                    console.error('Resend OTP Error:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to resend verification code. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                }
            });
        });
    });
    
    function startOtpCountdown(expiryTime) {
        const targetTime = new Date(expiryTime).getTime();
        const $timer = $('#otp-timer');
        const $resendBtn = $('#resend-otp-btn');
        
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetTime - now;
            
            if (distance > 0) {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                $timer.text(`Code expires in ${minutes}:${seconds.toString().padStart(2, '0')}`);
                $resendBtn.hide();
            } else {
                // Code expired
                clearInterval(countdown);
                $timer.text('Code expired').addClass('text-danger');
                $resendBtn.show();
                sessionStorage.removeItem('otp_expires_at');
                
                // Show expiry notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'warning',
                    title: 'Verification code expired. Please request a new one.'
                });
            }
        }, 1000);
    }
    @endif
}
</script>
@endpush
</x-smart_layout>