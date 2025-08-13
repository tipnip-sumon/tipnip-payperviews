<x-smart_layout>
    @section('top_title','Transfer Fund')
    @section('title','Transfer Fund')
    
    @push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .transfer-card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
        
        .wallet-balance {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .user-found {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 10px;
            padding: 1rem;
            margin: 0.5rem 0;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-verification {
            display: none;
            animation: slideInUp 0.5s ease;
        }
        
        @keyframes slideInUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .user-details-card {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .smart-user-card {
            position: relative;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        
        .smart-user-card:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.15),
                0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card-glow {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #11998e, #38ef7d);
            border-radius: 18px;
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
        }
        
        .smart-user-card:hover .card-glow {
            opacity: 0.1;
        }
        
        .user-card-content {
            position: relative;
            z-index: 1;
        }
        
        .user-avatar-container {
            position: relative;
        }
        
        .user-avatar-ring {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 3px;
            position: relative;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        
        .user-avatar-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .user-avatar-inner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
            border-radius: 50%;
        }
        
        .verified-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            font-size: 10px;
        }
        
        .user-display-name {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
        }
        
        .user-status-badge {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(21, 87, 36, 0.1);
        }
        
        .user-details-grid {
            display: grid;
            gap: 12px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border-left: 3px solid #667eea;
            transition: all 0.2s ease;
        }
        
        .detail-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(2px);
        }
        
        .detail-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin-right: 12px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            display: block;
            font-size: 0.9rem;
            color: #2c3e50;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .verification-status {
            padding: 16px;
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            border-radius: 12px;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .verification-icon-container {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0 auto;
            box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
        }
        
        .verification-text {
            color: #155724;
        }
        
        .verification-text .fw-bold {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .verification-text small {
            font-size: 0.7rem;
            opacity: 0.8;
        }
        
        .security-indicators {
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        
        .security-item {
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .security-item:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
        }
        
        .security-item i {
            font-size: 1.2rem;
        }
        
        .security-item small {
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: 2px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .transfer-summary {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-transfer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-transfer:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(255, 107, 107, 0.3);
            color: white;
        }
        
        .transfer-fees {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 0.75rem;
            margin: 0.5rem 0;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            z-index: 1000;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .pulse {
            animation: pulse 1s infinite;
        }
        
        .user-verification {
            display: none;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: 1;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            font-weight: bold;
            font-size: 14px;
        }
        
        .step.active .step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .step.completed .step-circle {
            background: #28a745;
            color: white;
        }
        
        .step-title {
            margin-top: 0.5rem;
            font-size: 12px;
            color: #6c757d;
        }
        
        .step.active .step-title {
            color: #667eea;
            font-weight: 600;
        }
    </style>
    @endpush
    
    @section('content')
    <!-- Transfer Fund Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-exchange-alt fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Transfer Funds</h3>
                            <p class="mb-0">Send money to other users instantly and securely</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card transfer-card position-relative">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <div>Processing transfer...</div>
                    </div>
                </div>
                
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-paper-plane me-2 text-primary"></i>
                        Send Money to Another User
                    </h5>
                </div>
                
                <form action="{{route('transfer-balance')}}" method="post" enctype="multipart/form-data" id="transferForm">
                    @csrf
                    <div class="card-body">
                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step active" id="step1">
                                <div class="step-circle">1</div>
                                <div class="step-title">Select Wallet</div>
                            </div>
                            <div class="step" id="step2">
                                <div class="step-circle">2</div>
                                <div class="step-title">Find Recipient</div>
                            </div>
                            <div class="step" id="step3">
                                <div class="step-circle">3</div>
                                <div class="step-title">Enter Amount</div>
                            </div>
                            <div class="step" id="step4">
                                <div class="step-circle">4</div>
                                <div class="step-title">Confirm Transfer</div>
                            </div>
                        </div>

                        <!-- Current Wallet Balance -->
                        <div class="wallet-balance">
                            <h6 class="mb-2"><i class="fas fa-wallet me-2"></i>Your Wallet Balance</h6>
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <div class="bg-white bg-opacity-20 rounded p-2">
                                        <small>Deposit Wallet</small>
                                        <h5 class="mb-0">${{ showAmount(auth()->user()->deposit_wallet) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-white bg-opacity-20 rounded p-2">
                                        <small>Interest Wallet</small>
                                        <h5 class="mb-0">${{ showAmount(auth()->user()->interest_wallet) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Selection -->
                        <div class="mb-4">
                            <label for="wallet" class="form-label fw-bold">
                                <i class="fas fa-credit-card text-primary me-2"></i>Select Transfer Source
                            </label>
                            <select class="form-control form-select" name="wallet" id="wallet" required>
                                <option value="">Choose wallet to transfer from</option>
                                <option value="deposit_wallet">Deposit Wallet - ${{ showAmount(auth()->user()->deposit_wallet) }}</option>
                                <option value="interest_wallet">Interest Wallet - ${{ showAmount(auth()->user()->interest_wallet) }}</option>
                            </select>
                            @error('wallet')
                                <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Member Search -->
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">
                                <i class="fas fa-search text-primary me-2"></i>Find Recipient
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control findUser" value="{{ old('username') }}" 
                                       id="username" placeholder="Enter username or member ID" required>
                                <button type="button" class="btn btn-outline-primary" id="searchUser">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <small class="text-muted">Enter the username of the person you want to send money to</small>
                            <div class="error-message text-danger mt-1"></div>
                            @error('username')
                                <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

        <!-- User Verification Display -->
        <div class="user-verification" id="userVerification">
            <div class="user-found position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 opacity-25">
                    <i class="fas fa-check-circle" style="font-size: 3rem;"></i>
                </div>
                <div class="position-relative">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-check-circle fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1 text-white fw-bold">
                                <i class="fas fa-user-check me-2"></i>Recipient Verified!
                            </h6>
                            <small class="text-white-50">Ready to receive your transfer</small>
                        </div>
                    </div>
                    <div id="userDetails" class="mt-3 p-3 bg-white bg-opacity-10 rounded"></div>
                </div>
            </div>
        </div>                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold">
                                <i class="fas fa-dollar-sign text-primary me-2"></i>Transfer Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" name="amount" value="{{ old('amount') }}" class="form-control" 
                                       id="amount" placeholder="0.00" step="0.01" min="1" required>
                            </div>
                            <small class="text-muted">Minimum transfer amount: $1.00</small>
                            @error('amount')
                                <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transfer Calculation -->
                        <div class="transfer-summary" id="transferSummary" style="display: none;">
                            <h6 class="text-primary mb-3"><i class="fas fa-calculator me-2"></i>Transfer Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Transfer Amount:</span>
                                        <strong id="transferAmount">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Transfer Fee (5%):</span>
                                        <strong id="transferFee" class="text-warning">$0.00</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Deduction:</span>
                                        <strong id="totalDeduction" class="text-danger">$0.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Recipient Receives:</span>
                                        <strong id="recipientReceives" class="text-success">$0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="note" class="form-label fw-bold">
                                <i class="fas fa-sticky-note text-primary me-2"></i>Transfer Note (Optional)
                            </label>
                            <textarea class="form-control" name="note" id="note" rows="3" 
                                      placeholder="Add a note for this transfer (optional)">{{ old('note') }}</textarea>
                            <small class="text-muted">This note will be visible to both you and the recipient</small>
                        </div>

                        <!-- Transaction Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock text-primary me-2"></i>Transaction Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" name="password" class="form-control" id="password" 
                                       placeholder="Enter your transaction password" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Enter your account password to authorize this transfer</small>
                            @error('password')
                                <div class="text-danger mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-0 py-4">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-cancel" id="cancelBtn">
                                <i class="fas fa-times me-2"></i>Cancel Transfer
                            </button>
                            <button type="submit" class="btn btn-transfer" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Money
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Transfers (Optional) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>Transfer Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="fas fa-check me-2"></i>What you can do:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Transfer funds instantly</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Send to any registered user</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Add personal notes</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Track transfer history</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning"><i class="fas fa-info-circle me-2"></i>Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>5% transfer fee applies</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Minimum transfer: $1.00</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Transfers are instant & final</li>
                                <li><i class="fas fa-arrow-right me-2 text-muted"></i>Verify recipient before sending</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    
    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let currentStep = 1;
            let userFound = false;
            let transferData = {};
            
            // Check if CSRF token is available
            if (!$('meta[name="csrf-token"]').length && !$('input[name="_token"]').length) {
                console.warn('CSRF token not found. Adding meta tag.');
                $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
            }
            
            // Set up AJAX defaults
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                }
            });
            
            // Step management
            function updateStep(step) {
                $('.step').removeClass('active completed');
                for (let i = 1; i <= step; i++) {
                    if (i < step) {
                        $('#step' + i).addClass('completed');
                    } else {
                        $('#step' + i).addClass('active');
                    }
                }
                currentStep = step;
            }
            
            // Wallet selection handler
            $('#wallet').on('change', function() {
                const wallet = $(this).val();
                if (wallet) {
                    updateStep(2);
                    transferData.wallet = wallet;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Wallet Selected!',
                        text: `${wallet.replace('_', ' ').toUpperCase()} selected as transfer source`,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
            
            // Enhanced user search functionality
            function searchUser() {
                const username = $('#username').val().trim();
                if (!username) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Username Required',
                        text: 'Please enter a username to search for',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Searching User...',
                    text: 'Please wait while we find the recipient',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const url = '{{ route("findUser") }}';
                const token = '{{ csrf_token() }}';
                const data = {username: username, _token: token};
                
                $.post(url, data, function(response) {
                    Swal.close();
                    
                    if (response.message) {
                        // User not found
                        $('.error-message').html('<i class="fas fa-exclamation-circle me-1"></i>' + response.message);
                        $('#username').addClass('is-invalid').removeClass('is-valid');
                        $('#userVerification').hide();
                        userFound = false;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'User Not Found',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        // User found
                        $('#username').addClass('is-valid').removeClass('is-invalid');
                        $('.error-message').text('');
                        userFound = true;
                        updateStep(3);
                        
                        // Display user details (customize based on your response structure)
                        const userInfo = response.user || {name: username, id: username};
                        const displayName = userInfo.name || `${userInfo.username || username}`;
                        const userEmail = userInfo.email || 'Email not available';
                        const userUsername = userInfo.username || username;
                        
                        $('#userDetails').html(`
                            <div class="smart-user-card">
                                <div class="card-glow"></div>
                                <div class="user-card-content p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-container me-4">
                                            <div class="user-avatar-ring">
                                                <div class="user-avatar-inner">
                                                    <i class="fas fa-user-tie text-white fa-lg"></i>
                                                </div>
                                            </div>
                                            <div class="verified-badge">
                                                <i class="fas fa-check-circle text-white"></i>
                                            </div>
                                        </div>
                                        <div class="user-info flex-grow-1">
                                            <div class="user-name-section mb-3">
                                                <h5 class="user-display-name mb-1">
                                                    <i class="fas fa-crown me-2 text-warning"></i>
                                                    ${displayName}
                                                </h5>
                                                <span class="user-status-badge">
                                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                                    Active Member
                                                </span>
                                            </div>
                                            <div class="user-details-grid">
                                                <div class="detail-item">
                                                    <div class="detail-icon">
                                                        <i class="fas fa-at"></i>
                                                    </div>
                                                    <div class="detail-content">
                                                        <span class="detail-label">Username</span>
                                                        <span class="detail-value">${userUsername}</span>
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-icon">
                                                        <i class="fas fa-envelope"></i>
                                                    </div>
                                                    <div class="detail-content">
                                                        <span class="detail-label">Email</span>
                                                        <span class="detail-value">${userEmail}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="verification-status text-center">
                                            <div class="verification-icon-container mb-2">
                                                <i class="fas fa-shield-check fa-2x"></i>
                                            </div>
                                            <div class="verification-text">
                                                <div class="fw-bold">VERIFIED</div>
                                                <small>Trusted User</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="security-indicators mt-3 pt-3 border-top">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="security-item">
                                                    <i class="fas fa-lock text-success mb-1"></i>
                                                    <small class="d-block text-success">Secure</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="security-item">
                                                    <i class="fas fa-user-check text-primary mb-1"></i>
                                                    <small class="d-block text-primary">Verified</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="security-item">
                                                    <i class="fas fa-flash text-warning mb-1"></i>
                                                    <small class="d-block text-warning">Instant</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        
                        $('#userVerification').show();
                        transferData.recipient = userInfo;
                        
                        // Enhanced SweetAlert2 success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Recipient Verified!',
                            html: `
                                <div class="text-center">
                                    <div class="success-animation mb-3">
                                        <i class="fas fa-user-check fa-3x text-success"></i>
                                    </div>
                                    <h5 class="text-success mb-2">${displayName}</h5>
                                    <p class="text-muted mb-0">Ready to receive your transfer</p>
                                    <div class="mt-3 p-2 bg-light rounded">
                                        <small class="text-success">
                                            <i class="fas fa-shield-check me-1"></i>
                                            Verified and secure recipient
                                        </small>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            timer: 2500,
                            toast: false,
                            position: 'center',
                            background: '#fff',
                            customClass: {
                                popup: 'swal2-success-popup',
                                title: 'swal2-success-title'
                            },
                            didOpen: () => {
                                // Add animation to the success icon
                                const icon = document.querySelector('.success-animation i');
                                if (icon) {
                                    icon.style.animation = 'bounce 0.6s ease-in-out';
                                }
                            }
                        });
                    }
                }).fail(function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Failed',
                        text: 'Unable to search for user. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
            
            // User search events
            $('#searchUser').on('click', searchUser);
            $('#username').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    searchUser();
                }
            });
            
            $('#username').on('focusout', function() {
                const username = $(this).val().trim();
                if (username && !userFound) {
                    searchUser();
                }
            });
            
            // Amount calculation with enhanced feedback
            $('#amount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                
                if (amount > 0) {
                    const fee = amount * 0.05; // 5% fee
                    const totalDeduction = amount + fee;
                    
                    $('#transferAmount').text('$' + amount.toFixed(2));
                    $('#transferFee').text('$' + fee.toFixed(2));
                    $('#totalDeduction').text('$' + totalDeduction.toFixed(2));
                    $('#recipientReceives').text('$' + amount.toFixed(2));
                    
                    $('#transferSummary').show();
                    
                    if (userFound) {
                        updateStep(4);
                    }
                    
                    transferData.amount = amount;
                    transferData.fee = fee;
                    transferData.total = totalDeduction;
                    
                    // Check wallet balance
                    checkWalletBalance();
                } else {
                    $('#transferSummary').hide();
                }
            });
            
            // Real-time balance check
            function checkWalletBalance() {
                const selectedWallet = $('#wallet').val();
                const amount = parseFloat($('#amount').val()) || 0;
                
                if (selectedWallet && amount > 0) {
                    const currentBalance = selectedWallet === 'deposit_wallet' 
                        ? {{ auth()->user()->deposit_wallet }}
                        : {{ auth()->user()->interest_wallet }};
                    
                    const fee = amount * 0.05;
                    const totalRequired = amount + fee;
                    
                    if (totalRequired > currentBalance) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Insufficient Balance',
                            text: `You need $${totalRequired.toFixed(2)} but only have $${currentBalance.toFixed(2)} in your ${selectedWallet.replace('_', ' ')}`,
                            confirmButtonColor: '#ffc107',
                            toast: true,
                            position: 'top-end',
                            timer: 3000
                        });
                        
                        $('#amount').addClass('is-invalid');
                        return false;
                    } else {
                        $('#amount').removeClass('is-invalid');
                        return true;
                    }
                }
                return true;
            }
            
            // Password toggle
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const passwordIcon = $('#passwordIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Cancel button with confirmation
            $('#cancelBtn').on('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Cancel Transfer?',
                    text: 'Are you sure you want to cancel this transfer? All entered data will be lost.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-times me-2"></i>Yes, cancel it!',
                    cancelButtonText: '<i class="fas fa-arrow-left me-2"></i>Continue transfer',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset form
                        $('#transferForm')[0].reset();
                        $('#transferSummary').hide();
                        $('#userVerification').hide();
                        $('.error-message').text('');
                        $('#username').removeClass('is-valid is-invalid');
                        $('#amount').removeClass('is-invalid');
                        updateStep(1);
                        userFound = false;
                        transferData = {};
                        
                        // Clear draft
                        localStorage.removeItem('transfer_draft');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Transfer Cancelled',
                            text: 'Transfer has been cancelled successfully',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });
            
            // Form submission with comprehensive SweetAlert2 validation
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();
                
                if (!userFound) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Recipient Not Verified',
                        text: 'Please find and verify the recipient before proceeding',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }
                
                const formData = {
                    wallet: $('#wallet').val(),
                    username: $('#username').val(),
                    amount: parseFloat($('#amount').val()) || 0,
                    note: $('#note').val(),
                    password: $('#password').val()
                };
                
                // Comprehensive validation
                if (!formData.wallet) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Wallet Required',
                        text: 'Please select a wallet to transfer from',
                        confirmButtonColor: '#dc3545'
                    });
                    $('#wallet').focus();
                    return;
                }
                
                if (formData.amount < 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Minimum transfer amount is $1.00',
                        confirmButtonColor: '#dc3545'
                    });
                    $('#amount').focus();
                    return;
                }
                
                if (!checkWalletBalance()) {
                    return;
                }
                
                if (!formData.password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Required',
                        text: 'Please enter your transaction password',
                        confirmButtonColor: '#dc3545'
                    });
                    $('#password').focus();
                    return;
                }
                
                // Final confirmation with detailed transfer summary
                const recipientName = transferData.recipient?.name || formData.username;
                const fee = formData.amount * 0.05;
                const total = formData.amount + fee;
                
                Swal.fire({
                    title: 'Confirm Money Transfer',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Please review the transfer details carefully
                            </div>
                            <h6 class="text-primary mb-3"><i class="fas fa-receipt me-2"></i>Transfer Details:</h6>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Recipient:</strong></div>
                                <div class="col-6">${recipientName}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Transfer Amount:</strong></div>
                                <div class="col-6 text-success">$${formData.amount.toFixed(2)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Transfer Fee (5%):</strong></div>
                                <div class="col-6 text-warning">$${fee.toFixed(2)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>Total Deduction:</strong></div>
                                <div class="col-6 text-danger">$${total.toFixed(2)}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>From Wallet:</strong></div>
                                <div class="col-6">${formData.wallet.replace('_', ' ').toUpperCase()}</div>
                            </div>
                            ${formData.note ? `
                            <div class="row mb-2">
                                <div class="col-6"><strong>Note:</strong></div>
                                <div class="col-6">${formData.note}</div>
                            </div>
                            ` : ''}
                            <hr>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> This transfer is instant and cannot be reversed!
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-paper-plane me-2"></i>Send Money Now',
                    cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel Transfer',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-popup-large'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show processing overlay
                        $('#loadingOverlay').show();
                        
                        Swal.fire({
                            title: 'Processing Transfer...',
                            html: `
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-3" role="status"></div>
                                    <p>Please wait while we process your transfer</p>
                                    <small class="text-muted">This may take a few moments</small>
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Clear draft before submission
                        localStorage.removeItem('transfer_draft');
                        
                        // Submit the form using AJAX to avoid session issues
                        const formElement = $('#transferForm')[0];
                        const formDataObj = new FormData(formElement);
                        
                        $.ajax({
                            url: formElement.action,
                            type: 'POST',
                            data: formDataObj,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                            },
                            success: function(response) {
                                $('#loadingOverlay').hide();
                                Swal.close();
                                
                                if (response.success || response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Transfer Successful!',
                                        html: `
                                            <div class="text-center">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                <p class="mb-0">${response.message || 'Transfer completed successfully!'}</p>
                                            </div>
                                        `,
                                        confirmButtonColor: '#28a745',
                                        confirmButtonText: '<i class="fas fa-check me-2"></i>Okay'
                                    }).then(() => {
                                        // Reset form after successful transfer
                                        $('#transferForm')[0].reset();
                                        $('#transferSummary').hide();
                                        $('#userVerification').hide();
                                        $('.error-message').text('');
                                        $('#username').removeClass('is-valid is-invalid');
                                        $('#amount').removeClass('is-invalid');
                                        updateStep(1);
                                        userFound = false;
                                        transferData = {};
                                        
                                        // Optionally reload the page to update balances
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Transfer Failed',
                                        html: `
                                            <div class="text-center">
                                                <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                                <p class="mb-0">${response.message || 'Transfer failed. Please try again.'}</p>
                                            </div>
                                        `,
                                        confirmButtonColor: '#dc3545',
                                        confirmButtonText: '<i class="fas fa-retry me-2"></i>Try Again'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#loadingOverlay').hide();
                                Swal.close();
                                
                                let errorMessage = 'Transfer failed. Please try again.';
                                
                                if (xhr.status === 401) {
                                    errorMessage = 'Your session has expired. Please login again.';
                                } else if (xhr.status === 422) {
                                    const errors = xhr.responseJSON?.errors;
                                    if (errors) {
                                        errorMessage = Object.values(errors).flat().join('<br>');
                                    } else {
                                        errorMessage = xhr.responseJSON?.message || errorMessage;
                                    }
                                } else if (xhr.status === 419) {
                                    errorMessage = 'Security token expired. Please refresh the page and try again.';
                                } else if (xhr.responseJSON?.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Transfer Failed',
                                    html: `
                                        <div class="text-center">
                                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                            <p class="mb-0">${errorMessage}</p>
                                        </div>
                                    `,
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: '<i class="fas fa-retry me-2"></i>Try Again'
                                }).then(() => {
                                    if (xhr.status === 401 || xhr.status === 419) {
                                        // Redirect to login if session expired
                                        window.location.href = '/login';
                                    }
                                });
                            }
                        });
                    }
                });
            });
            
            // Session message handling with enhanced SweetAlert2
            @if(session('success'))
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transfer Successful!',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="mb-0">{{ session("success") }}</p>
                            </div>
                        `,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: '<i class="fas fa-check me-2"></i>Okay',
                        timer: 8000,
                        timerProgressBar: true
                    });
                }, 500);
            @endif
            
            @if(session('error'))
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transfer Failed',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                <p class="mb-0">{{ session("error") }}</p>
                            </div>
                        `,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: '<i class="fas fa-retry me-2"></i>Try Again'
                    });
                }, 500);
            @endif
            
            // Enhanced UI interactions
            $('.form-control, .form-select').on('focus', function() {
                $(this).parent().find('.input-group-text').addClass('border-primary');
            }).on('blur', function() {
                $(this).parent().find('.input-group-text').removeClass('border-primary');
            });
            
            // Auto-save draft functionality
            let draftTimer;
            function saveDraft() {
                const draft = {
                    wallet: $('#wallet').val(),
                    username: $('#username').val(),
                    amount: $('#amount').val(),
                    note: $('#note').val(),
                    timestamp: Date.now()
                };
                localStorage.setItem('transfer_draft', JSON.stringify(draft));
            }
            
            $('.form-control, .form-select').on('input change', function() {
                clearTimeout(draftTimer);
                draftTimer = setTimeout(saveDraft, 2000);
            });
            
            // Load draft on page load
            const savedDraft = localStorage.getItem('transfer_draft');
            if (savedDraft) {
                const draft = JSON.parse(savedDraft);
                const draftAge = Date.now() - draft.timestamp;
                
                // Only load draft if it's less than 1 hour old
                if (draftAge < 3600000 && (draft.wallet || draft.username || draft.amount || draft.note)) {
                    Swal.fire({
                        title: 'Resume Previous Transfer?',
                        text: 'We found an unsaved transfer. Would you like to continue where you left off?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#667eea',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-play me-2"></i>Resume',
                        cancelButtonText: '<i class="fas fa-trash me-2"></i>Start Fresh'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (draft.wallet) $('#wallet').val(draft.wallet).trigger('change');
                            if (draft.username) $('#username').val(draft.username);
                            if (draft.amount) $('#amount').val(draft.amount).trigger('input');
                            if (draft.note) $('#note').val(draft.note);
                            
                            if (draft.username) {
                                setTimeout(() => {
                                    searchUser();
                                }, 500);
                            }
                        } else {
                            localStorage.removeItem('transfer_draft');
                        }
                    });
                }
            }
        });
    </script>
    
    <style>
        .swal2-popup-large {
            width: 600px !important;
            max-width: 90vw !important;
        }
        
        .swal2-success-popup {
            border-radius: 20px !important;
            padding: 2rem !important;
        }
        
        .swal2-success-title {
            color: #28a745 !important;
            font-weight: 700 !important;
        }
        
        .swal2-html-container {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            padding: 12px 24px !important;
        }
        
        .swal2-cancel {
            background: #6c757d !important;
            border: none !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            padding: 12px 24px !important;
        }
        
        .swal2-styled:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25) !important;
        }
        
        .swal2-styled:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .swal2-loading {
            border-color: #667eea transparent #667eea transparent !important;
        }
        
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -15px, 0);
            }
            70% {
                transform: translate3d(0, -7px, 0);
            }
            90% {
                transform: translate3d(0, -3px, 0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-animation {
            animation: fadeInUp 0.5s ease-out;
        }
        
        .swal2-icon.swal2-success {
            border-color: #28a745 !important;
        }
        
        .swal2-success-circular-line-left,
        .swal2-success-circular-line-right {
            background-color: #28a745 !important;
        }
        
        .swal2-success-fix {
            background-color: #28a745 !important;
        }
    </style>
    @endpush
</x-smart_layout>