<x-smart_layout>
    @section('top_title',$pageTitle)
    
    @push('styles')
    <style>
        /* Compact header styles */
        .card.bg-gradient-primary {
            margin-bottom: 0.5rem;
        }
        .card-body.py-2 {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }
        /* Better spacing throughout */
        .row.mb-2 {
            margin-bottom: 0.5rem !important;
        }
        .card-header.py-2 {
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
        }
        /* Remove any conflicting margin classes */
        .my-4 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        /* Better responsiveness */
        @media (max-width: 767.98px) {
            h2.fs-4 {
                font-size: 1.1rem !important;
            }
            .badge {
                font-size: 0.7rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .step .step-content h6 {
                font-size: 0.9rem;
            }
            .step .step-content p {
                font-size: 0.75rem !important;
                margin-bottom: 0;
            }
        }
        
        /* Page refresh overlay */
        .refresh-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
        }
        
        .refresh-overlay .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
        }
        
        .refresh-overlay .spinner i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
    @endpush
    
    @section('content') 
    
    <!-- Refresh Overlay -->
    <div class="refresh-overlay" id="refreshOverlay">
        <div class="spinner">
            <i class="fas fa-sync-alt fa-spin"></i>
            <div><strong>Refreshing page...</strong></div>
            <small>Please wait while we update your payment status</small>
        </div>
    </div>
    
    <!-- Add Funds Page Header --> 
    <div class="row mb-2 my-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 opacity-10">
                    <i class="fas fa-coins fa-4x mt-n2 me-n2 d-none d-md-block"></i>
                    <i class="fas fa-coins fa-3x mt-n1 me-n1 d-block d-md-none"></i>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <div class="me-md-3 mb-1 mb-md-0 text-center text-md-start">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex align-items-center justify-content-center mx-auto mx-md-0" style="width: 48px; height: 48px;">
                                <i class="fas fa-wallet fa-lg text-white"></i>
                            </div>
                        </div>
                        <div class="text-center text-md-start">
                            <h2 class="mb-0 fw-bold fs-4 fs-md-3">Add Funds to Your Account</h2>
                            <p class="mb-1 fs-6">Deposit money to access premium features and increase your earning potential.</p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start">
                                <span class="badge bg-white text-primary p-1 me-2 mb-1"><i class="fas fa-shield-alt me-1"></i> Secure</span>
                                <span class="badge bg-white text-primary p-1 me-2 mb-1"><i class="fas fa-bolt me-1"></i> Instant</span>
                                <span class="badge bg-white text-primary p-1 mb-1"><i class="fas fa-clock me-1"></i> 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2 my-4">
        <!-- Payment Method Selection -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-2 border-bottom">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="bg-primary bg-opacity-10 p-2 rounded-circle me-2 text-primary">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        Create Payment
                    </h5>
                </div>
                <div class="card-body" id="step-1">
                    @php $pendingDeposit = App\Models\Deposit::Pending()->where('user_id', $user->id)->first(); @endphp
                    @if ($pendingDeposit)
                        <div class="alert alert-info border-start border-info border-4 mb-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                            <i class="fas fa-info-circle fa-lg me-0 me-sm-3 mb-2 mb-sm-0 text-info"></i> 
                            <div>
                                <strong>Pending Payment Detected</strong>
                                <p class="mb-0">You have a pending payment. Please complete it or cancel to create a new one.</p>
                            </div>
                        </div>
                        
                        <div class="payment-details p-3 bg-light rounded-3 border mb-3 position-relative">
                            <div class="ribbon ribbon-top-right d-none d-md-block">
                                <span class="bg-primary">PENDING</span>
                            </div>
                            <span class="badge bg-primary position-absolute top-0 start-0 mt-2 ms-2 d-block d-md-none">PENDING</span>
                            <h5 class="text-primary mb-3 border-bottom pb-2 d-flex align-items-center">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Payment Details
                            </h5>
                            
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="text-muted me-0 me-sm-3 mb-1 mb-sm-0" style="min-width: 100px;">
                                            <strong>Order ID:</strong>
                                        </div>
                                        <div class="bg-white p-2 rounded w-100 border">
                                            <code class="fs-6 text-break">{{ $pendingDeposit->trx }}</code>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="text-muted me-0 me-sm-3 mb-1 mb-sm-0" style="min-width: 100px;">
                                            <strong>Amount:</strong>
                                        </div>
                                        <div class="bg-white p-2 rounded w-100 border">
                                            <span class="text-success fw-bold">${{ showAmount($pendingDeposit->amount) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="text-muted me-0 me-sm-3 mb-1 mb-sm-0" style="min-width: 100px;">
                                            <strong>Currency:</strong>
                                        </div>
                                        <div class="bg-white p-2 rounded w-100 border">
                                            <span class="badge bg-secondary">{{ strtoupper($pendingDeposit->method_currency) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                        <div class="text-muted me-0 me-sm-3 mb-1 mb-sm-0" style="min-width: 100px;">
                                            <strong>Pay Amount:</strong>
                                        </div>
                                        <div class="bg-white p-2 rounded w-100 border">
                                            <span class="fw-bold">{{ $pendingDeposit->final_amo }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-link me-1 text-primary"></i> Payment Link:
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-truncate" id="payment-link" value="{{ $pendingDeposit->admin_feedback }}" readonly>
                                    <button class="btn btn-primary d-flex align-items-center" type="button" id="copy-btn" onclick="copyPaymentLink()">
                                        <i class="fas fa-copy me-1 d-none d-sm-inline"></i> <span>Copy</span>
                                    </button>
                                </div>
                                <small class="text-muted mt-1 d-block">Use this link to complete your payment</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-wallet me-1 text-primary"></i> Payment Wallet:
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-truncate" id="payment-wallet" value="{{ $pendingDeposit->btc_wallet }}" readonly>
                                    <button class="btn btn-primary d-flex align-items-center" type="button" id="copy-wallet" onclick="copyPaymentWallet()">
                                        <i class="fas fa-copy me-1 d-none d-sm-inline"></i> <span>Copy</span>
                                    </button>
                                </div>
                                <small class="text-muted mt-1 d-block">Send your payment to this wallet address</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-info-circle me-1 text-primary"></i> Status:
                                </label>
                                <div>
                                    @if ($pendingDeposit->btc_amo == 'waiting')
                                        <div class="alert alert-warning py-2 d-flex flex-column flex-sm-row align-items-center status-waiting">
                                            <div class="me-0 me-sm-3 mb-2 mb-sm-0 text-center">
                                                <i class="fas fa-clock fa-2x text-warning"></i>
                                            </div>
                                            <div class="text-center text-sm-start">
                                                <strong>Waiting for payment</strong>
                                                <p class="mb-0 small">We're waiting for your payment to be confirmed.</p>
                                            </div>
                                        </div>
                                    @elseif ($pendingDeposit->btc_amo == 'finished')
                                        <div class="alert alert-success py-2 d-flex flex-column flex-sm-row align-items-center status-finished">
                                            <div class="me-0 me-sm-3 mb-2 mb-sm-0 text-center">
                                                <i class="fas fa-check-circle fa-2x text-success"></i>
                                            </div>
                                            <div class="text-center text-sm-start">
                                                <strong>Payment completed</strong>
                                                <p class="mb-0 small">Your payment has been successfully processed.</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-primary py-2 d-flex flex-column flex-sm-row align-items-center status-processing">
                                            <div class="me-0 me-sm-3 mb-2 mb-sm-0 text-center">
                                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                            </div>
                                            <div class="text-center text-sm-start">
                                                <strong>Processing payment</strong>
                                                <p class="mb-0 small">Your payment is being processed. This may take a few minutes.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">
                            <button type="button" class="btn btn-primary btn-lg refresh-btn w-100">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Status
                            </button>
                            <form method="DELETE" enctype="multipart/form-data" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-lg sw-btn-pay w-100" data-id="@auth{{ $user->id }}@endauth">
                                    <i class="fas fa-times-circle me-2"></i> Cancel Payment
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-md-7 col-lg-7">
                                <div class="mb-4">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-money-bill-wave me-2"></i> Create a New Payment
                                    </h5>
                                    <p class="text-muted">Fill in the details below to add funds to your account. Select your preferred payment method and enter the amount.</p>
                                </div>
                                
                                <form action="{{route('pay')}}" method="POST" enctype="multipart/form-data" class="payment-form">
                                    @csrf
                                    <input type="hidden" name="method_code">
                                    <input type="hidden" name="currency">
                                    <input type="hidden" name="user_id" value="@auth{{ $user->id }}@endauth">
                                    {{-- <input type="hidden" name="plan_id" class="form-control" id="plan_id" value="{{ old('pay_amount') }}"> --}}
                                    @error('plan_id')
                                        <span class="text-danger" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-exchange-alt text-primary me-1"></i> Payment Method
                                        </label>
                                        <select class="form-select form-control qursy" name="pay_currency" required>
                                            <option value="">Select Payment Method</option>
                                            <option value="usdtbsc">USDT (BSC Network)</option>
                                            <option value="usdttrc20">USDT (TRC20 Network)</option>
                                            <option value="usdterc20">USDT (ERC20 Network)</option>
                                            <option value="btc">Bitcoin (BTC)</option>
                                            <option value="eth">Ethereum (ETH)</option>
                                            <option value="trx">TRON (TRX)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="pay_amount" class="form-label fw-bold">
                                            <i class="fas fa-dollar-sign text-primary me-1"></i> Payment Amount
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                            <input type="text" class="form-control validAmount" name="pay_amount" id="pay_amount" 
                                                value="{{ old('pay_amount') }}" placeholder="Enter amount (minimum $10)" required>
                                        </div>
                                        <small class="text-muted">Minimum deposit amount is $10</small>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent border-0 px-0 pt-3">
                                        <button class="btn btn-primary btn-lg sw-btn-next w-100" type="submit">
                                            <i class="fas fa-check-circle me-2"></i> Create Payment
                                        </button>
                                    </div>
                                    
                                    <div id='Medds' class="mt-3"></div>
                                </form>
                            </div>
                            
                            <div class="col-md-5 col-lg-5 mt-4 mt-md-0">
                                <div class="bg-light p-4 rounded-3 border h-100">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Payment Information
                                    </h5>
                                    
                                    <div class="mb-4">
                                        <h6 class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i> Secure Transactions</h6>
                                        <p class="text-muted small">All transactions are secured with encryption to protect your financial information.</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="mb-2"><i class="fas fa-clock text-success me-2"></i> Processing Time</h6>
                                        <p class="text-muted small">Most payments are processed instantly, but some may take up to 30 minutes depending on network conditions.</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="mb-2"><i class="fas fa-question-circle text-success me-2"></i> Need Help?</h6>
                                        <p class="text-muted small">If you encounter any issues during payment, please contact our support team for assistance.</p>
                                    </div>
                                    
                                    <div class="alert alert-primary mt-3 mb-0">
                                        <div class="d-flex flex-column flex-sm-row">
                                            <div class="me-0 me-sm-3 mb-2 mb-sm-0 text-center">
                                                <i class="fas fa-lightbulb fa-2x text-primary"></i>
                                            </div>
                                            <div class="text-center text-sm-start">
                                                <strong>Tip:</strong>
                                                <p class="mb-0 small">Adding more funds unlocks higher earning potential and premium features!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <div id='Medds2'></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right sidebar - How it works -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3 position-sticky" style="top: 85px;">
                <div class="card-header bg-white py-2 border-bottom">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="bg-primary bg-opacity-10 p-2 rounded-circle me-2 text-primary">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        How It Works
                    </h5>
                </div>
                <div class="card-body">
                    <div class="steps">
                        <div class="step d-flex mb-3">
                            <div class="step-icon me-3">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span>1</span>
                                </div>
                            </div>
                            <div class="step-content">
                                <h6 class="fw-bold">Select Payment Method</h6>
                                <p class="text-muted small">Choose your preferred cryptocurrency payment option from the dropdown menu.</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex mb-3">
                            <div class="step-icon me-3">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span>2</span>
                                </div>
                            </div>
                            <div class="step-content">
                                <h6 class="fw-bold">Enter Amount</h6>
                                <p class="text-muted small">Specify how much you want to deposit (minimum $10).</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex mb-3">
                            <div class="step-icon me-3">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span>3</span>
                                </div>
                            </div>
                            <div class="step-content">
                                <h6 class="fw-bold">Complete Payment</h6>
                                <p class="text-muted small">Follow the instructions to send the crypto payment to the provided wallet address.</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex">
                            <div class="step-icon me-3">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span>4</span>
                                </div>
                            </div>
                            <div class="step-content">
                                <h6 class="fw-bold">Funds Added</h6>
                                <p class="text-muted small">Once payment is confirmed, funds will be added to your account automatically.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Deposits Section -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center mt-3">
                        <a href="{{ route('deposit.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('style')
    <style>
        /* Custom Styles for Deposit Page */
        .ribbon {
            position: absolute;
            right: -5px;
            top: -5px;
            z-index: 1;
            overflow: hidden;
            width: 110px;
            height: 110px;
            text-align: right;
        }
        
        .ribbon-top-right span {
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            line-height: 24px;
            transform: rotate(45deg);
            width: 130px;
            display: block;
            background: #007bff;
            box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
            position: absolute;
            top: 23px;
            right: -28px;
        }
        
        .ribbon-top-right span::before {
            content: "";
            position: absolute;
            left: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid #007bff;
            border-right: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #007bff;
        }
        
        .ribbon-top-right span::after {
            content: "";
            position: absolute;
            right: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid transparent;
            border-right: 3px solid #007bff;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #007bff;
        }
        
        /* Animation for refresh button */
        .refresh-btn:hover i {
            animation: spin 1s linear;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Payment Form Styling */
        .payment-form label {
            font-size: 0.9rem;
        }
        
        .payment-form .form-control:focus,
        .payment-form .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        /* Copy button animation */
        @keyframes copied {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .copied-animation {
            animation: copied 0.5s ease;
        }
        
        /* Text break for long strings */
        .text-break {
            word-break: break-all;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        /* Responsive Font Sizes */
        @media (max-width: 767px) {
            h2 {
                font-size: 1.75rem !important;
            }
            
            .lead {
                font-size: 1rem !important;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .payment-details {
                padding: 1rem !important;
            }
            
            .badge {
                font-size: 0.7rem !important;
            }
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 575px) {
            .payment-details .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .payment-details .text-muted {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
            
            .alert {
                padding: 0.75rem !important;
            }
            
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            
            .step {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }
            
            .step-icon {
                margin-bottom: 0.5rem;
                margin-right: 0 !important;
            }
            
            .step-content {
                text-align: center;
            }
        }
        
        /* Better input display on small screens */
        @media (max-width: 767px) {
            .form-control, .form-select, .input-group {
                font-size: 16px !important; /* Prevents iOS zoom on focus */
            }
            
            .input-group .btn {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
        
        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            border-radius: 15px !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        .swal2-title {
            color: #333 !important;
            font-weight: 600 !important;
        }
        
        .swal2-content {
            color: #666 !important;
        }
        
        .swal2-confirm.btn {
            margin: 0 0.5rem !important;
            padding: 0.5rem 1.5rem !important;
            border-radius: 8px !important;
        }
        
        .swal2-cancel.btn {
            margin: 0 0.5rem !important;
            padding: 0.5rem 1.5rem !important;
            border-radius: 8px !important;
        }
        
        /* Fade In Animation */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .animated.fadeInDown {
            animation-duration: 0.3s;
            animation-fill-mode: both;
            animation-name: fadeInDown;
        }
    </style>
    @endpush
    @endsection
    @push('script')
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js"></script>
        <script>
            // Add touchstart event for mobile responsiveness
            document.addEventListener('DOMContentLoaded', function() {
                const buttons = document.querySelectorAll('.btn');
                buttons.forEach(function(button) {
                    button.addEventListener('touchstart', function() {
                        this.classList.add('active');
                    });
                    button.addEventListener('touchend', function() {
                        this.classList.remove('active');
                    });
                });
            });
            
            function copyPaymentLink() {
                const paymentLink = document.getElementById('payment-link');
                const copyBtn = document.getElementById('copy-btn');
                
                // Select and copy the text
                paymentLink.select();
                paymentLink.setSelectionRange(0, 99999); // For mobile devices
                
                try {
                    navigator.clipboard.writeText(paymentLink.value).then(function() {
                        // Success feedback
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i> <span>Copied!</span>';
                        copyBtn.classList.remove('btn-primary');
                        copyBtn.classList.add('btn-success');
                        copyBtn.classList.add('copied-animation');
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('btn-success');
                            copyBtn.classList.add('btn-primary');
                            copyBtn.classList.remove('copied-animation');
                        }, 2000);
                    }).catch(function() {
                        // Fallback for older browsers
                        document.execCommand('copy');
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i> <span>Copied!</span>';
                        copyBtn.classList.remove('btn-primary');
                        copyBtn.classList.add('btn-success');
                        copyBtn.classList.add('copied-animation');
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('btn-success');
                            copyBtn.classList.add('btn-primary');
                            copyBtn.classList.remove('copied-animation');
                        }, 2000);
                    });
                } catch (err) {
                    // Fallback for very old browsers
                    document.execCommand('copy');
                    alert('Payment link copied to clipboard!');
                }
            }
            
            function copyPaymentWallet() {
                const paymentLink = document.getElementById('payment-wallet');
                const copyBtn = document.getElementById('copy-wallet');
                
                // Select and copy the text
                paymentLink.select();
                paymentLink.setSelectionRange(0, 99999); // For mobile devices
                
                try {
                    navigator.clipboard.writeText(paymentLink.value).then(function() {
                        // Success feedback
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i> <span>Copied!</span>';
                        copyBtn.classList.remove('btn-primary');
                        copyBtn.classList.add('btn-success');
                        copyBtn.classList.add('copied-animation');
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('btn-success');
                            copyBtn.classList.add('btn-primary');
                            copyBtn.classList.remove('copied-animation');
                        }, 2000);
                    }).catch(function() {
                        // Fallback for older browsers
                        document.execCommand('copy');
                        const originalText = copyBtn.innerHTML;
                        copyBtn.innerHTML = '<i class="fas fa-check"></i> <span>Copied!</span>';
                        copyBtn.classList.remove('btn-primary');
                        copyBtn.classList.add('btn-success');
                        copyBtn.classList.add('copied-animation');
                        
                        setTimeout(function() {
                            copyBtn.innerHTML = originalText;
                            copyBtn.classList.remove('btn-success');
                            copyBtn.classList.add('btn-primary');
                            copyBtn.classList.remove('copied-animation');
                        }, 2000);
                    });
                } catch (err) {
                    // Fallback for very old browsers
                    document.execCommand('copy');
                    alert('Payment Wallet copied to clipboard!');
                }
            }
            
            $(document).ready(function() { 
                // Check screen size and adjust UI
                function adjustForScreenSize() {
                    if (window.innerWidth < 576) {
                        $('.btn-lg').addClass('w-100 mb-2');
                    } else {
                        $('.btn-lg').removeClass('w-100 mb-2');
                    }
                }
                
                // Run on page load and window resize
                adjustForScreenSize();
                $(window).on('resize', adjustForScreenSize);
                
                // Validate amount input
                $('.validAmount').on('input',function(e) {
                    validateAmount($(this));
                });
                
                $('.validAmount').on('focusout',function(e) {
                    validateAmount($(this), true);
                });
                
                function validateAmount($input, showMessage = false) {
                    var value = $input.val();
                    if (!Number.isInteger(Number(value)) || value <= 0 || value === '' || value === null || value === undefined) {
                        $input.removeClass('is-valid');
                        $input.addClass('is-invalid');
                        if (showMessage) {
                            $('#Medds').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i> Pay amount must be a positive integer value.</div>');
                            setTimeout(() => {
                                $('#Medds').html('');
                            }, 5000);
                        }
                        return false;
                    } else if (value < 10 ) {
                        $input.removeClass('is-valid');
                        $input.addClass('is-invalid');
                        if (showMessage) {
                            $('#Medds').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i> Pay amount must be at least 10.</div>');
                            setTimeout(() => {
                                $('#Medds').html('');
                            }, 5000);
                        }
                        return false;
                    } else {
                        $input.removeClass('is-invalid');
                        $input.addClass('is-valid');
                        return true;
                    }
                }
                
                // Form submission handling
                $(".sw-btn-next").on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const currentUrl = window.location.href;
                    var pay_currency = $('.qursy').val();
                    var pay_amount = $('input[name="pay_amount"]').val();
                    var token = '{{ csrf_token() }}';
                    var url = "{{ route('pay') }}";
                    
                    // Validate inputs
                    if (pay_currency == '') {
                        Swal.fire({
                            title: 'Payment Method Required',
                            text: 'Please select a payment method before proceeding.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f0ad4e',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }
                    
                    if (!validateAmount($('.validAmount'), false)) {
                        Swal.fire({
                            title: 'Invalid Amount',
                            text: 'Please enter a valid amount. Minimum deposit is $10.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }
                    
                    // Get payment method name for display
                    const paymentMethodName = $('.qursy option:selected').text();
                    
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Payment Creation',
                        html: `
                            <div class="text-start">
                                <div class="mb-2"><strong>Payment Method:</strong> ${paymentMethodName}</div>
                                <div class="mb-2"><strong>Amount:</strong> $${pay_amount}</div>
                            </div>
                            <hr>
                            <small class="text-muted">You will be redirected to complete the payment after confirmation.</small>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-check-circle me-2"></i>Create Payment',
                        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
                        reverseButtons: true,
                        customClass: {
                            popup: 'animated fadeInDown',
                            confirmButton: 'btn btn-success mx-2',
                            cancelButton: 'btn btn-secondary mx-2'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show processing state
                            Swal.fire({
                                title: 'Creating Payment...',
                                text: 'Please wait while we process your request.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Disable button to prevent multiple submissions
                            $(".sw-btn-next").prop('disabled', true);
                            $(".sw-btn-next").html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
                            
                            // Submit payment
                            var data = {
                                pay_currency: pay_currency,
                                price_amount: pay_amount,
                                _token: token,
                                currentUrl: currentUrl
                            };
                            
                            $.post(url, data, function(response) {
                                redirectUrl = response.data.invoice_url;
                                if (redirectUrl) {
                                    // Try to open in new window/tab
                                    const newWindow = window.open(redirectUrl, '_blank');
                                    
                                    // Check if popup was blocked
                                    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                                        // Popup was blocked, show alternative options
                                        Swal.fire({
                                            title: 'Payment Page Ready!',
                                            html: `
                                                <div class="text-center">
                                                    <p class="mb-3">Your payment page is ready! Click the button below to proceed:</p>
                                                    <div class="alert alert-warning mt-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <small>Popup was blocked by your browser. Please allow popups for this site or use the button below.</small>
                                                    </div>
                                                </div>
                                            `,
                                            icon: 'info',
                                            showCancelButton: true,
                                            confirmButtonColor: '#28a745',
                                            cancelButtonColor: '#6c757d',
                                            confirmButtonText: '<i class="fas fa-external-link-alt me-2"></i>Open Payment Page',
                                            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
                                            customClass: {
                                                confirmButton: 'btn btn-success mx-2',
                                                cancelButton: 'btn btn-secondary mx-2'
                                            },
                                            buttonsStyling: false,
                                            allowOutsideClick: false
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // Direct navigation instead of popup
                                                window.location.href = redirectUrl;
                                            } else {
                                                // User cancelled, reload the page
                                                refreshPageWithOverlay();
                                            }
                                        });
                                    } else {
                                        // Popup opened successfully
                                        Swal.fire({
                                            title: 'Payment Created Successfully!',
                                            text: 'Payment page opened in a new window. Please complete your payment there.',
                                            icon: 'success',
                                            confirmButtonText: 'I understand',
                                            confirmButtonColor: '#28a745',
                                            customClass: {
                                                confirmButton: 'btn btn-success'
                                            },
                                            buttonsStyling: false,
                                            timer: 3000,
                                            timerProgressBar: true,
                                            allowOutsideClick: false
                                        }).then(() => {
                                            // Always reload the page after payment creation
                                            refreshPageWithOverlay();
                                        });
                                        
                                        // Also set up a fallback reload in case the timer doesn't complete
                                        setTimeout(() => {
                                            refreshPageWithOverlay();
                                        }, 4000);
                                    }
                                } else {
                                    Swal.fire({
                                        title: 'Payment Creation Failed',
                                        text: 'Something went wrong. Please try again later.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#dc3545',
                                        customClass: {
                                            confirmButton: 'btn btn-danger'
                                        },
                                        buttonsStyling: false
                                    }).then(() => {
                                        // Reload page even on error to refresh state
                                        refreshPageWithOverlay();
                                    });
                                }
                            }).fail(function(xhr) {
                                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                                
                                Swal.fire({
                                    title: 'Payment Creation Failed',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    // Reload page to refresh state
                                    refreshPageWithOverlay();
                                });
                            });
                        }
                    });
                });
                
                // Refresh button animation and functionality
                $('.refresh-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    $(this).find('i').addClass('fa-spin');
                    $(this).prop('disabled', true);
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
                
                // Delete payment
                $(".sw-btn-pay").on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();  
                    
                    var user_id = $(this).data('id');
                    var token = '{{ csrf_token() }}';
                    var method = $('input[name="_method"]').val();
                    var data = {user_id:user_id, _token:token, _method: method};
                    let url = `/pay/delete/${user_id}`;
                    
                    Swal.fire({
                        title: 'Cancel Payment?',
                        text: 'Are you sure you want to cancel this payment? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, Cancel Payment',
                        cancelButtonText: '<i class="fas fa-times me-2"></i>No, Keep It',
                        reverseButtons: true,
                        customClass: {
                            popup: 'animated fadeInDown',
                            confirmButton: 'btn btn-danger mx-2',
                            cancelButton: 'btn btn-secondary mx-2'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show processing state
                            Swal.fire({
                                title: 'Cancelling Payment...',
                                text: 'Please wait while we process your request.',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Disable button to prevent multiple clicks
                            $(this).prop('disabled', true);
                            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i> Cancelling...');
                            
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: data,
                                cache: false,
                                success: function (response) {
                                    if (response.status == 'success') {
                                        Swal.fire({
                                            title: 'Payment Cancelled!',
                                            text: response.message,
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: '<i class="fas fa-check me-2"></i>OK',
                                            customClass: {
                                                confirmButton: 'btn btn-success'
                                            },
                                            buttonsStyling: false
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    }
                                }
                            }).fail(function(xhr) {
                                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                                
                                Swal.fire({
                                    title: 'Cancellation Failed',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: '<i class="fas fa-times me-2"></i>OK',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    },
                                    buttonsStyling: false
                                });
                                
                                // Re-enable the button
                                $(".sw-btn-pay").prop('disabled', false);
                                $(".sw-btn-pay").html('<i class="fas fa-times-circle me-2"></i> Cancel Payment');
                            });
                        }
                    })
                });
                
                // Highlight selected payment method
                $('.qursy').on('change', function() {
                    if($(this).val() !== '') {
                        $(this).addClass('border-primary');
                    } else {
                        $(this).removeClass('border-primary');
                    }
                });
                
                // Window focus detection for auto-refresh after payment
                let paymentWindowOpened = false;
                let originalFocusHandler = null;
                
                function showRefreshOverlay() {
                    $('#refreshOverlay').fadeIn(300);
                }
                
                function refreshPageWithOverlay() {
                    showRefreshOverlay();
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                }
                
                // Track when payment window is opened
                $(document).on('payment-window-opened', function() {
                    paymentWindowOpened = true;
                    
                    // Set up focus detection
                    originalFocusHandler = function() {
                        if (paymentWindowOpened) {
                            // User returned to the page, refresh to check payment status
                            refreshPageWithOverlay();
                        }
                    };
                    
                    $(window).on('focus', originalFocusHandler);
                });
                
                // Trigger when payment window opens
                $(document).on('click', '.sw-btn-next', function() {
                    setTimeout(() => {
                        $(document).trigger('payment-window-opened');
                    }, 2000);
                });
                
                // Add fast-click for mobile devices
                if (typeof FastClick !== 'undefined') {
                    FastClick.attach(document.body);
                }
                
                // Periodic payment status check for pending deposits
                @if ($pendingDeposit ?? false)
                
                // Function to check and update pending deposit status
                function checkPendingDepositStatus() {
                    $.get('{{ route("user.deposit.status") }}', function(response) {
                        if (response.success) {
                            // Check if there's a recently completed deposit
                            if (response.recently_completed) {
                                showRefreshOverlay();
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                                return;
                            }
                            
                            if (response.has_pending !== {{ $pendingDeposit ? 'true' : 'false' }}) {
                                // Pending status changed, refresh the page to show updated content
                                showRefreshOverlay();
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                                return;
                            }
                            
                            // Update specific fields if deposit exists
                            if (response.deposit) {
                                const deposit = response.deposit;
                                
                                // Update payment status badge
                                if (deposit.btc_amo === 'finished' && $('.status-waiting').is(':visible')) {
                                    showRefreshOverlay();
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                } else if (deposit.btc_amo === 'waiting' && $('.status-finished').is(':visible')) {
                                    showRefreshOverlay();
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }
                                
                                // Update payment link if changed
                                const currentPaymentLink = $('#payment-link').val();
                                if (deposit.admin_feedback && deposit.admin_feedback !== currentPaymentLink) {
                                    $('#payment-link').val(deposit.admin_feedback);
                                }
                                
                                // Update wallet address if changed
                                const currentWallet = $('#payment-wallet').val();
                                if (deposit.btc_wallet && deposit.btc_wallet !== currentWallet) {
                                    $('#payment-wallet').val(deposit.btc_wallet);
                                }
                            }
                        }
                    }).fail(function() {
                        // Silent fail - network issues shouldn't disrupt the experience
                    });
                }
                
                @if ($pendingDeposit)
                // Check deposit status every 15 seconds for pending deposits
                let depositStatusInterval = setInterval(checkPendingDepositStatus, 15000);
                
                // Clear interval when deposit is completed or user navigates away
                $(window).on('beforeunload', function() {
                    if (depositStatusInterval) {
                        clearInterval(depositStatusInterval);
                    }
                });
                @endif
                
                let statusCheckInterval = setInterval(function() {
                    // Check if payment status has changed
                    $.get('{{ route("user.payment_history") }}', function(response) {
                        // This will help detect if payment status changed
                        // The actual refresh will happen through window focus or user action
                    }).fail(function() {
                        // If API fails, we can still rely on other refresh mechanisms
                    });
                }, 30000); // Check every 30 seconds
                
                // Clear interval when user interacts with the page
                $(document).on('click', function() {
                    if (statusCheckInterval) {
                        clearInterval(statusCheckInterval);
                        statusCheckInterval = null;
                    }
                });
                @endif
            });
        </script>
    @endpush
</x-smart_layout>