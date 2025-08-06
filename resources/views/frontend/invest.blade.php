<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title','Deposit for Ads Viewing')
    @section('content')
    
    <!-- Deposit Information -->
    @if(!$userStats || !$userStats['has_plan_one'])
    <div class="row mb-4 my-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body text-center py-4">
                    <i class="ri-play-circle-fill fs-40 mb-3"></i>
                    <h4 class="text-white mb-2">Start Earning by Watching Ads!</h4>
                    <p class="mb-0">Make a deposit to unlock ad viewing opportunities. Choose any plan that suits your budget and start earning immediately!</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body text-center py-4">
                    <i class="ri-medal-fill fs-40 mb-3"></i>
                    <h4 class="text-white mb-2">Upgrade Your Plan!</h4>
                    <p class="mb-0">You're already earning from ads! Upgrade to a higher plan to watch more ads daily and earn more money.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Current Deposit Status -->
    @if($userStats && $userStats['current_deposit'])
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-card border-0 shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-wallet-3-line me-2"></i>Your Current Deposit Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-primary mb-1">{{ $userStats['current_plan'] ?? 'Plan 1' }}</h3>
                                <small class="text-muted">Current Plan</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-success mb-1">${{ number_format($userStats['current_amount'], 0) }}</h3>
                                <small class="text-muted">Deposit Amount</small>
                            </div>
                        </div>
                        <div class="col-md-3 border-end">
                            <div class="p-3">
                                <h3 class="text-info mb-1">
                                    <i class="ri-play-circle-line me-1"></i>Active
                                </h3>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3 class="text-warning mb-1">20%</h3>
                                <small class="text-muted">Withdrawal Fee</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('user.withdraw') }}" class="btn btn-outline-warning">
                            <i class="ri-money-dollar-circle-line me-2"></i>Withdraw Deposit (20% fee)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Investment Form -->
    <div class="row">
        <div class="col-xl-8 col-md-8 col-sm-12">
            <!-- Available Plans -->
            <div class="card custom-card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ri-star-line me-2 text-warning"></i>
                            @if(!$userStats || !$userStats['has_plan_one'])
                                Available Deposit Plans
                            @else
                                Premium Deposit Plans
                            @endif
                        </h5>
                        <span class="badge bg-info">
                            @if(!$userStats || !$userStats['has_plan_one'])
                                Start with Plan 1
                            @else
                                {{ $plans->count() }} Plans Available
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(!$userStats || !$userStats['has_plan_one'])
                        <div class="alert alert-primary border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="ri-information-line fs-20 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Choose Your Plan</h6>
                                    <p class="mb-0">Select any plan that fits your budget to start earning by watching ads daily. Higher plans offer more videos and better earning rates!</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="ri-check-circle-line fs-20 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Congratulations! Upgrade Available</h6>
                                    <p class="mb-0">You can now upgrade to higher deposit plans with more daily ads, higher earning rates, and exclusive features.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        @forelse($plans as $plan)
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card plan-card h-100 border-0 shadow-sm" data-plan-id="{{ $plan->id }}" style="cursor: pointer; transition: all 0.3s ease-in-out;">
                                <!-- Plan Badge -->
                                <div class="plan-badge-container">
                                    @if($plan->id == 1)
                                        <div class="plan-badge bg-primary">
                                            <i class="ri-star-fill me-1"></i>Starter Plan
                                        </div>
                                    @elseif($plan->id <= 3)
                                        <div class="plan-badge bg-success">
                                            <i class="ri-fire-fill me-1"></i>Popular Choice
                                        </div>
                                    @else
                                        <div class="plan-badge bg-warning">
                                            <i class="ri-vip-crown-fill me-1"></i>Premium Plan
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body text-center p-4">
                                    <!-- Plan Title & Price -->
                                    <div class="plan-header mb-4">
                                        <h4 class="plan-title text-dark mb-2">{{ $plan->name }}</h4>
                                        <div class="plan-price mb-2">
                                            <span class="price-amount">${{ number_format($plan->fixed_amount, 0) }}</span>
                                            <small class="text-muted d-block">One-time Deposit</small>
                                        </div>
                                        <div class="return-highlight bg-light rounded p-2">
                                            <span class="text-info fw-bold">Ad Viewing Access</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Plan Features -->
                                    <div class="plan-features text-start">
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <div class="feature-icon me-3">
                                                <i class="ri-play-circle-fill text-warning"></i>
                                            </div>
                                            <div>
                                                <strong>Daily Ad Limit:</strong> {{ $plan->daily_video_limit }} ads/day
                                            </div>
                                        </div>
                                        
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <div class="feature-icon me-3">
                                                <i class="ri-money-dollar-circle-fill text-success"></i>
                                            </div>
                                            <div>
                                                <strong>Earning Rate:</strong> ${{ number_format($plan->video_earning_rate, 4) }} per ad
                                            </div>
                                        </div>
                                        
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <div class="feature-icon me-3">
                                                <i class="ri-calendar-line text-info"></i>
                                            </div>
                                            <div>
                                                <strong>Max Daily Earning:</strong> ${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}
                                            </div>
                                        </div>
                                        
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <div class="feature-icon me-3">
                                                <i class="ri-shield-check-fill text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>Withdrawal:</strong> Available (20% fee)
                                            </div>
                                        </div>
                                        
                                        <!-- Expected Daily Earnings -->
                                        <div class="returns-calculation bg-info bg-opacity-10 rounded p-3 mt-3">
                                            <div class="text-center">
                                                <small class="text-muted">Potential Daily Earnings</small>
                                                <h5 class="text-info mb-0">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}</h5>
                                                <small class="text-muted">By watching all daily ads</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent border-0 p-3">
                                    <button class="btn btn-outline-primary w-100 select-plan-btn">
                                        <i class="ri-hand-coin-line me-2"></i>Select This Plan
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-warning text-center">
                                @if($userStats && $userStats['has_plan_one'])
                                    <i class="ri-award-line me-2"></i>
                                    Congratulations! You've reached the highest available plan tier.
                                @else
                                    <i class="ri-information-line me-2"></i>
                                    No investment plans available at the moment.
                                @endif
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 col-sm-12">
            <!-- Deposit Form -->
            <div class="card custom-card border-0 shadow investment-form">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-secure-payment-line me-2"></i>Complete Your Deposit
                    </h5>
                </div>
                <form action="{{route('invest.submit')}}" method="post" id="investForm">
                    @csrf
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="wallet_type" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-wallet-3-line me-2 text-primary"></i>Payment Wallet:
                            </label>
                            <select class="form-control form-select" name="wallet_type" id="wallet_type">
                                <option value="deposit_wallet">
                                    <i class="ri-bank-card-line"></i> Deposit Wallet 
                                    <span class="wallet-balance">(${{ number_format(auth()->user()->deposit_wallet ?? 0, 2) }} available)</span>
                                </option>
                                <option value="interest_wallet">
                                    <i class="ri-coins-line"></i> Interest Wallet 
                                    <span class="wallet-balance">(${{ number_format(auth()->user()->interest_wallet ?? 0, 2) }} available)</span>
                                </option>
                            </select>
                            @error('wallet_type')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="plan_id" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-vip-crown-line me-2 text-warning"></i>Selected Plan:
                            </label>
                            <select class="form-control form-select plan_id" name="plan_id" id="plan_id">
                                <option value="">Choose a plan from the cards above</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->fixed_amount, 0) }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="plan_amount" id="plan_amount" value="">
                            @error('plan_id')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-calculator-line me-2 text-info"></i>Deposit Summary:
                            </label>
                            <div class="calculation-display rounded border">
                                <div class="calculation text-center">
                                    <div class="text-muted">
                                        <i class="ri-information-line fs-20 mb-2"></i>
                                        <p class="mb-0">Select a plan to see deposit details</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Token Discount Section -->
                        @if($specialTicketStats && $specialTicketStats['usable_tokens_count'] > 0)
                        <div class="mb-4">
                            <div class="card border-warning bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ri-ticket-2-line text-warning fs-24 me-2"></i>
                                        <div>
                                            <h6 class="mb-1 text-warning">Special Lottery Tokens Available!</h6>
                                            <small class="text-muted">
                                                You have {{ $specialTicketStats['usable_tokens_count'] }} special token(s) that can be used as discount
                                                @if(isset($preSelectedToken) && $preSelectedToken)
                                                <br><strong class="text-success">Token #{{ $preSelectedToken->id }} is pre-selected for use!</strong>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="use_special_tokens" value="yes" id="useSpecialTokens">
                                        <label class="form-check-label" for="useSpecialTokens">
                                            <strong>Use special tokens for discount</strong>
                                            <br><small class="text-muted">Get time-based discount (5% max, decreases over time until draw)</small>
                                        </label>
                                    </div>
                                    
                                    <div id="tokenDetails" class="mt-3" style="display: none;">
                                        <div id="tokenDiscountInfo" class="mb-3">
                                            <!-- Time-based discount info will appear here -->
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bg-light rounded p-2">
                                                    <small class="text-muted">Discount Applied</small>
                                                    <div id="tokenDiscount" class="fw-bold text-success">$0.00</div>
                                                    <small class="text-muted">On upgrade amount only</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg-light rounded p-2">
                                                    <small class="text-muted">Final Payment</small>
                                                    <div id="finalPayment" class="fw-bold text-primary">$0.00</div>
                                                    <small class="text-muted">Amount to pay</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="ri-information-line me-1"></i>
                                                <strong>Note:</strong> Your sponsor will still receive full lottery tickets based on the original investment amount, regardless of your discount. Discount applies only to your wallet deduction amount.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Ticket Apply Option -->
                        <div class="mb-4">
                            <div class="card border-info bg-info bg-opacity-10">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ri-ticket-line text-info fs-24 me-2"></i>
                                        <div>
                                            <h6 class="mb-1 text-info">Apply Ticket</h6>
                                            <small class="text-muted">Apply your lottery ticket for this investment</small>
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="applyTicket" name="apply_ticket" value="yes">
                                        <label class="form-check-label" for="applyTicket">
                                            <strong>Apply lottery ticket for this investment</strong>
                                            <br><small class="text-muted">Get time-based discount (5% max, decreases over time until draw)</small>
                                        </label>
                                    </div>

                                    <div id="ticketApplySection" class="mt-3" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="ticket_number" class="form-label small fw-semibold">
                                                    <i class="ri-hashtag me-1"></i>Ticket Number (Optional):
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           class="form-control form-control-sm" 
                                                           name="ticket_number" 
                                                           id="ticket_number" 
                                                           placeholder="e.g., TKT123456, LT789012, or 6106-AC74-D736-1771_LT1" 
                                                           maxlength="30">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="clearTicketBtn" title="Clear ticket number">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="ri-information-line me-1"></i>
                                                    Enter valid ticket format (TKT/LT prefix, 6+ digits, or format like 6106-AC74-D736-1771_LT1) or leave blank for auto-assignment
                                                </small>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-info btn-sm w-100" id="checkTicketBtn">
                                                    <i class="ri-search-line me-1"></i>Check Ticket
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Ticket Status Result -->
                                        <div id="ticketStatusResult" class="mt-3" style="display: none;">
                                            <div class="alert alert-info border-0 py-2 mb-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-information-line me-2"></i>
                                                    <span id="ticketStatusMessage">Ticket status will appear here</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Regular Ticket Discount Info -->
                                        <div id="regularTicketDiscountInfo" class="mt-3">
                                            <!-- Time-based discount info for regular tickets will appear here -->
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <small class="text-info">
                                            <i class="ri-gift-line me-1"></i>
                                            <strong>Benefits:</strong> Time-based discount (up to 5%), lottery entry, sharing options, and transfer capabilities.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="form-password" class="form-label fs-14 text-dark fw-semibold">
                                <i class="ri-shield-keyhole-line me-2 text-danger"></i>Transaction Password:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ri-lock-line text-muted"></i></span>
                                <input type="password" name="password" class="form-control" id="form-password" 
                                       placeholder="Enter your password for security" autocomplete="current-password" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-light text-center p-4">
                        <button class="btn btn-primary btn-lg w-100 mb-3" type="submit" id="investBtn">
                            <i class="ri-wallet-3-line me-2"></i>Make Deposit
                        </button>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="ri-shield-check-line me-1 text-success"></i>
                                Secure & instant processing
                            </small>
                        </div>
                    </div>
                </form>

                <!-- Messages -->
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
            </div>
        </div>
    </div>
    @endsection

    @push('style')
    <style>
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .plan-badge-container {
            position: relative;
            overflow: hidden;
        }
        
        .plan-badge {
            position: absolute;
            top: 15px;
            right: -25px;
            color: white;
            padding: 5px 30px;
            font-size: 12px;
            font-weight: 600;
            transform: rotate(45deg);
            z-index: 10;
        }
        
        .plan-card {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        }
        
        .plan-card:hover {
            border-color: #007bff;
            box-shadow: 0 1rem 2rem rgba(0, 123, 255, 0.2);
            transform: translateY(-5px);
        }
        
        .plan-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f7ea 100%);
            box-shadow: 0 1rem 2rem rgba(40, 167, 69, 0.2);
            transform: translateY(-5px);
        }
        
        .plan-card.selected .select-plan-btn {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .plan-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .price-amount {
            font-size: 2.5rem;
            font-weight: 800;
            color: #28a745;
        }
        
        .feature-icon {
            width: 35px;
            height: 35px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .feature-item {
            font-size: 14px;
            color: #495057;
        }
        
        .return-highlight {
            border: 2px dashed #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        
        .returns-calculation {
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .calculation-display {
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
        }
        
        .select-plan-btn {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .select-plan-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15);
        }
        
        /* Investment Form Enhancements */
        .investment-form .card {
            position: sticky;
            top: 20px;
        }
        
        .wallet-balance {
            font-size: 12px;
            color: #6c757d;
        }
        
        /* Alert Enhancements */
        .alert {
            border-radius: 10px;
        }
        
        /* Animation for plan selection */
        @keyframes planSelect {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        .plan-card.selecting {
            animation: planSelect 0.3s ease-in-out;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .price-amount {
                font-size: 2rem;
            }
            
            .plan-badge {
                padding: 3px 20px;
                font-size: 10px;
            }
        }
        
        /* Ticket validation styles */
        .form-control.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.32.27.4-.74-.4-.27z'/%3e%3cpath fill='%2328a745' d='M6.564 2.564 6.1 2.1l-3.1 3.1-1.1-1.1-.464.464L2.9 6.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 5.8 2.4 2.4M8.2 5.8l-2.4 2.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
        }
    </style>
    @endpush

    @push('script')
    <script>
        $(document).ready(function() {
            // Plan card selection
            $('.plan-card').on('click', function() {
                const planId = $(this).data('plan-id');
                const planName = $(this).find('h4').text();
                const planAmount = $(this).find('.price-amount').text().replace('$', '').replace(',', '');
                
                // Remove active class from all cards
                $('.plan-card').removeClass('border-primary bg-primary bg-opacity-10 selected');
                
                // Add active class to clicked card
                $(this).addClass('border-primary bg-primary bg-opacity-10 selected');
                
                // Update form elements
                $('#plan_id').val(planId);
                $('#plan_amount').val(planAmount);
                
                // Update calculation display
                updateCalculationDisplay(planAmount, planName);
                
                // Enable invest button regardless of other conditions
                enableInvestButton();
            });

            // Plan select dropdown change
            $('#plan_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                if ($(this).val()) {
                    const planAmount = selectedOption.text().split('$')[1].replace(',', '');
                    const planName = selectedOption.text().split(' - ')[0];
                    $('#plan_amount').val(planAmount);
                    updateCalculationDisplay(planAmount, planName);
                    $('#investBtn').prop('disabled', false);
                    
                    // Update card selection
                    $('.plan-card').removeClass('border-primary bg-primary bg-opacity-10 selected');
                    $('.plan-card[data-plan-id="' + $(this).val() + '"]').addClass('border-primary bg-primary bg-opacity-10 selected');
                } else {
                    $('#plan_amount').val('');
                    $('.calculation-display .calculation').html(`
                        <div class="text-muted">
                            <i class="ri-information-line fs-20 mb-2"></i>
                            <p class="mb-0">Select a plan to see deposit details</p>
                        </div>
                    `);
                    $('#investBtn').prop('disabled', true);
                }
            });

            // Special tokens functionality - ensure only one ticket type at a time
            $('#useSpecialTokens').on('change', function() {
                if ($(this).is(':checked')) {
                    // Uncheck regular ticket if special token is selected
                    $('#applyTicket').prop('checked', false);
                    $('#ticketApplySection').slideUp();
                    $('#ticketStatusResult').hide();
                    
                    $('#tokenDetails').slideDown();
                    updateTokenCalculation();
                } else {
                    $('#tokenDetails').slideUp();
                }
                
                // Update calculation if plan is selected
                const planAmount = $('#plan_amount').val();
                if (planAmount) {
                    updateCalculationDisplay(planAmount, 'Selected Plan');
                }
            });

            // Ticket apply functionality - ensure only one ticket type at a time
            $('#applyTicket').on('change', function() {
                if ($(this).is(':checked')) {
                    // Uncheck special tokens if regular ticket is selected
                    $('#useSpecialTokens').prop('checked', false);
                    $('#tokenDetails').slideUp();
                    
                    $('#ticketApplySection').slideDown();
                } else {
                    $('#ticketApplySection').slideUp();
                    $('#ticketStatusResult').hide();
                }
                
                // Update calculation to include/exclude ticket discount
                const planAmount = $('#plan_amount').val();
                if (planAmount) {
                    updateCalculationDisplay(planAmount, 'Selected Plan');
                }
            });

            // Ticket usage tracking
            let usedTickets = new Set(); // Track used tickets in this session
            let validatedTickets = new Map(); // Track validation status

            // Real-time ticket number validation
            $('#ticket_number').on('input', function() {
                const ticketNumber = $(this).val().trim();
                const $checkBtn = $('#checkTicketBtn');
                
                // Clear previous status when user starts typing
                if (ticketNumber.length > 0) {
                    $('#ticketStatusResult').hide();
                }
                
                // Enable/disable check button based on input
                if (ticketNumber.length >= 3) {
                    $checkBtn.prop('disabled', false);
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else if (ticketNumber.length > 0) {
                    $checkBtn.prop('disabled', true);
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $checkBtn.prop('disabled', false);
                    $(this).removeClass('is-valid is-invalid');
                }
            });

            // Check ticket functionality
            $('#checkTicketBtn').on('click', function() {
                const ticketNumber = $('#ticket_number').val().trim();
                const $btn = $(this);
                const originalText = $btn.html();
                
                // Show loading state
                $btn.html('<i class="ri-loader-4-line spin me-1"></i>Checking...');
                $btn.prop('disabled', true);
                
                if (ticketNumber) {
                    // Show checking status
                    $('#ticketStatusResult').show();
                    $('#ticketStatusMessage').html('<i class="ri-loader-4-line spin me-2"></i>Verifying ticket number...');
                    $('#ticketStatusResult .alert').removeClass().addClass('alert alert-info border-0 py-2 mb-0');
                    
                    // Make API call to validate ticket
                    $.ajax({
                        url: '/tickets/validate',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        data: JSON.stringify({
                            ticket_number: ticketNumber,
                            usage_type: 'investment'
                        }),
                        success: function(response) {
                            let message = '';
                            let alertClass = 'alert-warning';
                            
                            if (response.success) {
                                message = `<i class="ri-check-line me-2"></i>${response.message}`;
                                alertClass = 'alert-success';
                                
                                // Show discount information
                                if (response.discount_percentage > 0) {
                                    message += `<br><small class="text-success">Time-based discount: ${response.discount_percentage}% (${response.time_remaining_hours}h remaining)</small>`;
                                }
                                
                                // Update discount calculation
                                updateTokenCalculation();
                            } else {
                                message = `<i class="ri-close-line me-2"></i>${response.message}`;
                                alertClass = response.is_reuse ? 'alert-danger' : 'alert-warning';
                                
                                if (response.used_at) {
                                    message += `<br><small class="text-muted">Previously used on: ${response.used_at}</small>`;
                                }
                            }
                            
                            $('#ticketStatusMessage').html(message);
                            $('#ticketStatusResult .alert').removeClass().addClass(`alert ${alertClass} border-0 py-2 mb-0`);
                            
                            // Only add to frontend tracking after server validation
                            const upperTicketNumber = ticketNumber.toUpperCase();
                            
                            // For successful validation, only track in session for form submission validation
                            if (response.success) {
                                validatedTickets.set(upperTicketNumber, {
                                    isValid: true,
                                    timestamp: new Date(),
                                    status: 'valid',
                                    serverValidated: true
                                });
                            } else {
                                // For failed validation, track both for session management
                                usedTickets.add(upperTicketNumber);
                                validatedTickets.set(upperTicketNumber, {
                                    isValid: false,
                                    timestamp: new Date(),
                                    status: response.is_reuse ? 'reused' : 'invalid',
                                    serverValidated: true
                                });
                            }
                            
                            // Show ticket usage history
                            showTicketUsageHistory();
                        },
                        error: function(xhr, status, error) {
                            let message = '<i class="ri-close-line me-2"></i>Error validating ticket. Please try again.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = `<i class="ri-close-line me-2"></i>${xhr.responseJSON.message}`;
                            }
                            
                            $('#ticketStatusMessage').html(message);
                            $('#ticketStatusResult .alert').removeClass().addClass('alert alert-danger border-0 py-2 mb-0');
                            
                            console.error('Ticket validation error:', error);
                        },
                        complete: function() {
                            // Reset button
                            $btn.html(originalText);
                            $btn.prop('disabled', false);
                        }
                    });
                } else {
                    $('#ticketStatusResult').show();
                    $('#ticketStatusMessage').html('<i class="ri-information-line me-2"></i>Auto-assignment will be used if no ticket number is provided');
                    $('#ticketStatusResult .alert').removeClass().addClass('alert alert-info border-0 py-2 mb-0');
                    
                    // Reset button
                    setTimeout(() => {
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                    }, 500);
                    
                    // Update calculation for auto-assignment
                    updateTokenCalculation();
                }
            });

            // Clear ticket button functionality
            $('#clearTicketBtn').on('click', function() {
                $('#ticket_number').val('').removeClass('is-valid is-invalid');
                $('#ticketStatusResult').hide();
                $('#checkTicketBtn').prop('disabled', false);
                
                // Clear any validation displays
                $('#regularTicketDiscountInfo').html('');
                
                // Clear session tracking for this ticket
                const ticketNumber = $('#ticket_number').attr('data-last-checked');
                if (ticketNumber) {
                    const upperTicketNumber = ticketNumber.toUpperCase();
                    usedTickets.delete(upperTicketNumber);
                    validatedTickets.delete(upperTicketNumber);
                    $('#ticket_number').removeAttr('data-last-checked');
                }
                
                // Update calculations
                updateTokenCalculation();
            });

            // Add function to reset all session tracking (for debugging)
            window.resetTicketTracking = function() {
                usedTickets.clear();
                validatedTickets.clear();
                $('#ticket_number').val('').removeClass('is-valid is-invalid').removeAttr('data-last-checked');
                $('#ticketStatusResult').hide();
                console.log('Ticket tracking reset');
            };

            // Form submission validation
            $('#investForm').on('submit', function(e) {
                const ticketNumber = $('#ticket_number').val().trim();
                const applyTicketChecked = $('#applyTicket').is(':checked');
                
                // Only validate tickets if user is trying to apply a ticket
                if (applyTicketChecked && ticketNumber) {
                    const upperTicketNumber = ticketNumber.toUpperCase();
                    const ticketInfo = validatedTickets.get(upperTicketNumber);
                    
                    // Prevent submission if ticket was validated as invalid or reused
                    if (ticketInfo && (ticketInfo.status === 'invalid' || ticketInfo.status === 'reused')) {
                        e.preventDefault();
                        
                        let message = 'This ticket cannot be used.';
                        
                        if (ticketInfo.status === 'reused') {
                            message = 'This ticket has already been used and cannot be reused.';
                        } else if (ticketInfo.status === 'invalid') {
                            message = 'This ticket was validated as invalid and cannot be used. Please use a different ticket or remove the ticket number for auto-assignment.';
                        }
                        
                        // Show error message
                        $('#ticketStatusResult').show();
                        $('#ticketStatusMessage').html(`<i class="ri-close-line me-2"></i>${message}`);
                        $('#ticketStatusResult .alert').removeClass().addClass('alert alert-danger border-0 py-2 mb-0');
                        
                        // Scroll to error message
                        $('#ticketStatusResult')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        return false;
                    }
                    
                    // If ticket hasn't been validated yet, require validation first
                    if (!ticketInfo || !ticketInfo.serverValidated) {
                        e.preventDefault();
                        
                        $('#ticketStatusResult').show();
                        $('#ticketStatusMessage').html('<i class="ri-information-line me-2"></i>Please validate the ticket number before submitting.');
                        $('#ticketStatusResult .alert').removeClass().addClass('alert alert-warning border-0 py-2 mb-0');
                        
                        // Highlight the check button
                        $('#checkTicketBtn').addClass('btn-warning').removeClass('btn-outline-info');
                        setTimeout(() => {
                            $('#checkTicketBtn').removeClass('btn-warning').addClass('btn-outline-info');
                        }, 3000);
                        
                        $('#ticketStatusResult')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                }
            });

            // Add ticket usage history display
            function showTicketUsageHistory() {
                if (usedTickets.size > 0) {
                    let historyHTML = '<div class="mt-3"><small class="text-muted"><strong>Ticket Usage History:</strong></small><ul class="list-unstyled mt-2">';
                    
                    for (let ticket of usedTickets) {
                        const info = validatedTickets.get(ticket);
                        const statusClass = info.status === 'valid' ? 'text-success' : 'text-danger';
                        const statusIcon = info.status === 'valid' ? 'ri-check-line' : 'ri-close-line';
                        
                        historyHTML += `
                            <li class="small ${statusClass}">
                                <i class="${statusIcon} me-1"></i>
                                ${ticket} - ${info.status} (${info.timestamp.toLocaleTimeString()})
                            </li>
                        `;
                    }
                    
                    historyHTML += '</ul></div>';
                    
                    // Add to ticket section if not already present
                    if (!$('#ticketUsageHistory').length) {
                        $('#ticketApplySection').append(`<div id="ticketUsageHistory">${historyHTML}</div>`);
                    } else {
                        $('#ticketUsageHistory').html(historyHTML);
                    }
                }
            }

            // Wallet type change handler
            $('#wallet_type').on('change', function() {
                const planAmount = $('#plan_amount').val();
                if (planAmount) {
                    updateCalculationDisplay(planAmount, 'Selected Plan');
                }
            });

            function updateCalculationDisplay(amount, planName) {
                const depositWallet = {{ auth()->user()->deposit_wallet ?? 0 }};
                const interestWallet = {{ auth()->user()->interest_wallet ?? 0 }};
                const selectedWallet = $('#wallet_type').val();
                const availableBalance = selectedWallet === 'interest_wallet' ? interestWallet : depositWallet;
                
                const planAmount = parseFloat(amount);
                const currentUserDeposit = {{ $userStats['current_amount'] ?? 0 }}; // User's current deposit amount
                
                // Calculate upgrade amount (what user actually needs to pay)
                const upgradeAmount = planAmount > currentUserDeposit ? planAmount - currentUserDeposit : planAmount;
                
                let finalAmount = upgradeAmount; // Start with upgrade amount
                let totalDiscount = 0;
                let discountDetails = [];
                
                // Calculate time-based discount for any ticket type
                const timeDiscount = calculateTimeBasedDiscount();
                const discountPercentage = timeDiscount.discount / 100;
                
                // Apply discount to the upgrade amount (not full plan amount)
                if ($('#useSpecialTokens').is(':checked')) {
                    const specialTokenDiscount = upgradeAmount * discountPercentage;
                    totalDiscount += specialTokenDiscount;
                    discountDetails.push({
                        type: 'Special Token',
                        amount: specialTokenDiscount,
                        icon: 'ri-ticket-2-line',
                        percentage: timeDiscount.discount
                    });
                } else if ($('#applyTicket').is(':checked')) {
                    const ticketDiscount = upgradeAmount * discountPercentage;
                    totalDiscount += ticketDiscount;
                    discountDetails.push({
                        type: 'Regular Ticket',
                        amount: ticketDiscount,
                        icon: 'ri-ticket-line',
                        percentage: timeDiscount.discount
                    });
                }
                
                finalAmount = upgradeAmount - totalDiscount; // Discount applied to upgrade amount
                const canInvest = availableBalance >= finalAmount;
                
                // Build discount display
                let discountHTML = '';
                if (discountDetails.length > 0) {
                    discountHTML = '<div class="row">';
                    discountDetails.forEach(discount => {
                        discountHTML += `
                            <div class="col-md-12">
                                <div class="bg-success bg-opacity-10 rounded p-2 mt-2">
                                    <small class="text-success">
                                        <i class="${discount.icon} me-1"></i>
                                        ${discount.type} Discount (${discount.percentage}%): -$${discount.amount.toFixed(2)}
                                    </small>
                                    <br>
                                    <small class="text-muted">Applied to upgrade amount only</small>
                                </div>
                            </div>
                        `;
                    });
                    discountHTML += '</div>';
                }
                
                // Determine display text based on whether it's an upgrade or new deposit
                const isUpgrade = currentUserDeposit > 0 && planAmount > currentUserDeposit;
                const displayText = isUpgrade ? 'Upgrade Amount' : 'Plan Amount';
                
                $('.calculation-display .calculation').html(`
                    <div class="row text-center">
                        <div class="col-md-4 border-end">
                            <div class="p-2">
                                <small class="text-muted d-block">Full Plan Value</small>
                                <h6 class="text-info mb-0">$${planAmount.toFixed(2)}</h6>
                            </div>
                        </div>
                        <div class="col-md-4 border-end">
                            <div class="p-2">
                                <small class="text-muted d-block">${displayText}</small>
                                <h6 class="text-primary mb-0">$${upgradeAmount.toFixed(2)}</h6>
                                ${isUpgrade ? '<small class="text-muted">($' + currentUserDeposit.toFixed(2) + ' already deposited)</small>' : ''}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2">
                                <small class="text-muted d-block">You Pay</small>
                                <h6 class="${canInvest ? 'text-success' : 'text-danger'} mb-0">$${finalAmount.toFixed(2)}</h6>
                                ${totalDiscount > 0 ? '<small class="text-success">After discount</small>' : ''}
                            </div>
                        </div>
                    </div>
                    ${discountHTML}
                    <div class="row">
                        <div class="col-12">
                            <div class="mt-2 text-center">
                                <small class="text-muted">
                                    <i class="ri-wallet-line me-1"></i>
                                    Available Balance: $${availableBalance.toFixed(2)}
                                    ${!canInvest ? '<span class="text-danger ms-2">(Insufficient funds)</span>' : ''}
                                </small>
                                ${isUpgrade ? '<br><small class="text-info"><i class="ri-information-line me-1"></i>This is an upgrade - you only pay the difference</small>' : ''}
                            </div>
                        </div>
                    </div>
                `);
                
                // Update invest button state
                $('#investBtn').prop('disabled', !canInvest);
            }
            
            // Function to enable invest button when plan is selected
            function enableInvestButton() {
                const planId = $('#plan_id').val();
                if (planId) {
                    $('#investBtn').prop('disabled', false);
                }
            }

            // Calculate time-based discount
            function calculateTimeBasedDiscount() {
                const purchaseTime = new Date(); // Current time as purchase/receipt time
                const drawTime = new Date();
                
                // Add 24 hours to current time as example draw time
                // In real implementation, this should come from your lottery system
                drawTime.setHours(drawTime.getHours() + 24);
                
                const totalTime = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
                const remainingTime = drawTime.getTime() - purchaseTime.getTime();
                
                // Calculate discount percentage (max 5%, decreases over time)
                const maxDiscount = 5;
                const discountPercentage = Math.max(0, (remainingTime / totalTime) * maxDiscount);
                
                return {
                    discount: Math.round(discountPercentage * 100) / 100, // Round to 2 decimal places
                    timeRemaining: Math.max(0, Math.floor(remainingTime / (1000 * 60 * 60))) // Hours remaining
                };
            }

            function updateTokenCalculation() {
                const planAmount = parseFloat($('#plan_amount').val()) || 0;
                if (planAmount > 0) {
                    const currentUserDeposit = {{ $userStats['current_amount'] ?? 0 }};
                    const upgradeAmount = planAmount > currentUserDeposit ? planAmount - currentUserDeposit : planAmount;
                    
                    let totalDiscount = 0;
                    let discountType = '';
                    
                    // Calculate time-based discount for any ticket type
                    const timeDiscount = calculateTimeBasedDiscount();
                    const discountPercentage = timeDiscount.discount / 100;
                    
                    // Apply discount based on which ticket type is selected (to upgrade amount only)
                    if ($('#useSpecialTokens').is(':checked')) {
                        totalDiscount = upgradeAmount * discountPercentage;
                        discountType = 'Special Token';
                        
                        // Update special token discount info
                        $('#tokenDiscountInfo').html(`
                            <div class="alert alert-info">
                                <i class="las la-clock text-info"></i>
                                <strong>${discountType} - Time-based discount: ${timeDiscount.discount}%</strong><br>
                                <small class="text-muted">
                                    Discount decreases over time. ${timeDiscount.timeRemaining} hours until draw.
                                    <br>Applied to upgrade amount: $${upgradeAmount.toFixed(2)}
                                </small>
                            </div>
                        `);
                        $('#regularTicketDiscountInfo').html('');
                        
                    } else if ($('#applyTicket').is(':checked')) {
                        totalDiscount = upgradeAmount * discountPercentage;
                        discountType = 'Regular Ticket';
                        
                        // Update regular ticket discount info
                        $('#regularTicketDiscountInfo').html(`
                            <div class="alert alert-info">
                                <i class="las la-clock text-info"></i>
                                <strong>${discountType} - Time-based discount: ${timeDiscount.discount}%</strong><br>
                                <small class="text-muted">
                                    Discount decreases over time. ${timeDiscount.timeRemaining} hours until draw.
                                    <br>Applied to upgrade amount: $${upgradeAmount.toFixed(2)}
                                </small>
                            </div>
                        `);
                        $('#tokenDiscountInfo').html('');
                    } else {
                        // Clear both discount info displays when no ticket is selected
                        $('#tokenDiscountInfo').html('');
                        $('#regularTicketDiscountInfo').html('');
                    }
                    
                    const finalPayment = upgradeAmount - totalDiscount;
                    
                    $('#tokenDiscount').text(`$${totalDiscount.toFixed(2)}`);
                    $('#finalPayment').text(`$${finalPayment.toFixed(2)}`);
                }
            }

            // Add CSS for animations
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .spin { animation: spin 1s linear infinite; }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                `)
                .appendTo('head');

            // Handle pre-selected token from URL parameter
            @if(isset($preSelectedToken) && $preSelectedToken)
            // Auto-check the token checkbox if user came from token page
            $('#useSpecialTokens').prop('checked', true);
            $('#tokenDetails').show();
            
            // Show notification about pre-selected token
            const tokenNotification = `
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ri-ticket-2-line me-2"></i>
                    <strong>Token Pre-selected!</strong> Token #{{ $preSelectedToken->id }} (Value: ${{ number_format($preSelectedToken->token_discount_amount, 2) }}) is ready to use.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#investForm').before(tokenNotification);
            
            // Auto-dismiss notification after 5 seconds
            setTimeout(() => {
                $('.alert-info').fadeOut();
            }, 5000);
            @endif
        });
    </script>
    @endpush
</x-smart_layout>
