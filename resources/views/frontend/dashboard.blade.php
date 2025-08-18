<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title',$pageTitle)
    @section('content')
        <!-- Welcome Section -->
        <div class="row mb-4 my-4">
            <div class="col-12">
                <div class="card custom-card bg-gradient-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h4 class="text-primary mb-1">Welcome back, {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}!</h4> 
                                <p class="text-primary-50 mb-0">Here's what's happening with your account today.</p>
                                <!-- Session Status -->
                                <div class="mt-2" id="session-status-container">
                                    <small class="badge bg-success">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Secure Session Active - Only one login allowed
                                    </small>
                                    @if($user->session_created_at)
                                        <small class="text-white-50 ms-2">
                                            Session started: {{ $user->session_created_at->format('M d, h:i A') }}
                                        </small>
                                    @endif
                                    
                                    <!-- Session Notifications Alert - Dynamic Content -->
                                    <div id="session-notifications-area">
                                        @php
                                            $unreadNotifications = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                                                ->where('user_id', $user->id)
                                                ->where('is_read', false)
                                                ->count();
                                        @endphp
                                        
                                        @if($unreadNotifications > 0)
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark" onclick="showSessionNotifications()" style="cursor: pointer;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    {{ $unreadNotifications }} Security Alert{{ $unreadNotifications > 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('deposit.index') }}" class="btn btn-warning btn-lg me-3 shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i>Add Funds
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Earnings Hub - Completely Redesigned -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="new-earnings-card">
                    <div class="earnings-card-header">
                        <div class="earnings-title-section">
                            <div class="earnings-icon-new">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="earnings-title-text">
                                <h3>💰 Total Earnings Hub</h3>
                                <p>Your complete income overview</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="earnings-content-area">
                        <div class="earnings-main-section">
                            <div class="earnings-controls">
                                <span class="income-label">All Income</span>
                                <button class="visibility-toggle" id="toggleEarnings" onclick="toggleEarningsVisibility()">
                                    <i class="fas fa-eye" id="earningsIcon"></i>
                                </button>
                            </div>
                            <div class="earnings-amount-display">
                                <h1 class="amount-visible" id="earningsAmount" style="display: none;">
                                    ${{ showAmount($investments['total_interest_earned'] + $referrals['referral_earnings'] + $videoSystem['video_earnings']) }}
                                </h1>
                                <h1 class="amount-hidden" id="hiddenEarnings">
                                    ••••••••
                                </h1>
                            </div>
                        </div>
                        
                        <div class="earnings-breakdown-section">
                            <h5 class="breakdown-heading">Earnings Breakdown</h5>
                            
                            <div class="breakdown-item-new">
                                <div class="breakdown-icon-container interest-bg">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div class="breakdown-details">
                                    <span class="breakdown-type">Interest</span>
                                    <span class="breakdown-amount">${{ showAmount($investments['total_interest_earned']) }}</span>
                                </div>
                            </div>
                            
                            <div class="breakdown-item-new">
                                <div class="breakdown-icon-container referral-bg">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="breakdown-details">
                                    <span class="breakdown-type">Referrals</span>
                                    <span class="breakdown-amount">${{ showAmount($referrals['referral_earnings']) }}</span>
                                </div>
                            </div>
                            
                            <div class="breakdown-item-new">
                                <div class="breakdown-icon-container video-bg">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                                <div class="breakdown-details">
                                    <span class="breakdown-type">Videos</span>
                                    <span class="breakdown-amount">${{ showAmount($videoSystem['video_earnings']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Statistics Cards -->
        <div class="row mb-4">
            <!-- Add Funds Call-to-Action -->
            <div class="col-12 mb-3">
                <div class="card border-0 bg-gradient-success text-white shadow">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h4 class="mb-2"><i class="fas fa-rocket me-2"></i>Ready to Boost Your Earnings?</h4>
                                <p class="mb-0">Add funds to your account to unlock premium investment plans with higher daily video limits and earning rates!</p>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                <a href="{{ route('invest.index') }}" class="btn btn-light btn-lg fw-bold px-4 shadow-sm">
                                    <i class="fas fa-credit-card me-2"></i>Deposit Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Wallets - Fresh Design -->
            <div class="col-lg-6 mb-3">
                <div class="fresh-wallet-card">
                    <div class="wallet-card-header">
                        <h5 class="wallet-title"><i class="fas fa-wallet"></i>Account Wallets</h5>
                    </div>
                    <div class="wallet-card-body">
                        <!-- Account Wallet - Total -->
                        <div class="wallet-item-fresh">
                            <div class="wallet-icon-section">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="wallet-info-section">
                                <h6 class="wallet-label">💙 Account Wallet - Total</h6>
                                <div class="wallet-amount-controls">
                                    <button class="wallet-toggle-btn" onclick="toggleWalletVisibility('total')" title="Toggle visibility">
                                        <i class="fas fa-eye" id="totalWalletIcon"></i>
                                    </button>
                                    <h4 class="wallet-amount-value" id="totalWalletAmount" style="display: none;">
                                        ${{ showAmount($currentBalance) }}
                                    </h4>
                                    <h4 class="wallet-amount-hidden" id="totalWalletHidden">
                                        ****
                                    </h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Wallet - Purchase -->
                        <div class="wallet-item-fresh">
                            <div class="wallet-icon-section">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="wallet-info-section">
                                <h6 class="wallet-label">💛 Account Wallet - Purchase</h6>
                                <div class="wallet-amount-controls">
                                    <button class="wallet-toggle-btn" onclick="toggleWalletVisibility('purchase')" title="Toggle visibility">
                                        <i class="fas fa-eye" id="purchaseWalletIcon"></i>
                                    </button>
                                    <h4 class="wallet-amount-value" id="purchaseWalletAmount" style="display: none;">
                                        ${{ showAmount(auth()->user()->deposit_wallet) }}
                                    </h4>
                                    <h4 class="wallet-amount-hidden" id="purchaseWalletHidden">
                                        ****
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="fresh-earnings-card">
                    <div class="earnings-card-header">
                        <h5 class="earnings-title"><i class="fas fa-chart-bar"></i>Team Bonus & Investments</h5>
                    </div>
                    <div class="earnings-card-body">
                        <!-- Interest Wallet -->
                        <div class="earnings-item-fresh">
                            <div class="earnings-icon-section">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="earnings-info-section">
                                <h6 class="earnings-label">💚 Interest Wallet</h6>
                                <div class="earnings-amount-controls">
                                    <button class="earnings-toggle-btn" onclick="toggleWalletVisibility('interest')" title="Toggle visibility">
                                        <i class="fas fa-eye" id="interestWalletIcon"></i>
                                    </button>
                                    <h4 class="earnings-amount-value" id="interestWalletAmount" style="display: none;">
                                        ${{ showAmount(auth()->user()->interest_wallet) }}
                                    </h4>
                                    <h4 class="earnings-amount-hidden" id="interestWalletHidden">
                                        ****
                                    </h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Investment -->
                        <div class="earnings-item-fresh">
                            <div class="earnings-icon-section">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            <div class="earnings-info-section">
                                <h6 class="earnings-label">💎 Total Investment</h6>
                                <div class="earnings-amount-controls">
                                    <button class="earnings-toggle-btn" onclick="toggleWalletVisibility('investment')" title="Toggle visibility">
                                        <i class="fas fa-eye" id="investmentWalletIcon"></i>
                                    </button>
                                    <h4 class="earnings-amount-value" id="investmentWalletAmount" style="display: none;">
                                        ${{ showAmount($totalInvest) }}
                                    </h4>
                                    <h4 class="earnings-amount-hidden" id="investmentWalletHidden">
                                        ****
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lottery System Highlight -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                        <i class="fas fa-ticket-alt fa-2x text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-white mb-1">🎰 Try Your Luck with Our Lottery!</h3>
                                        <p class="text-white-50 mb-0">Buy lottery tickets and win amazing prizes. Weekly draws with guaranteed winners!</p>
                                    </div>
                                </div>
                                <div class="row">
                                    @php
                                        $settings = App\Models\LotterySetting::getSettings();
                                        $currentDraw = App\Models\LotteryDraw::where('status', 'pending')->first();
                                        $userTickets = $currentDraw ? App\Models\LotteryTicket::where('user_id', auth()->id())
                                                                        ->where('lottery_draw_id', $currentDraw->id)
                                                                        ->count() : 0;
                                    @endphp
                                    <div class="col-sm-4 mb-2">
                                        <div class="text-center">
                                            <h4 class="text-white mb-0">${{ $settings ? number_format($settings->ticket_price, 2) : '2.00' }}</h4>
                                            <small class="text-white-50">Per Ticket</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-2">
                                        <div class="text-center">
                                            <h4 class="text-white mb-0">{{ $userTickets }}</h4>
                                            <small class="text-white-50">Your Tickets</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-2">
                                        <div class="text-center">
                                            <h4 class="text-white mb-0">${{ $currentDraw ? number_format($currentDraw->total_prize_pool, 2) : '0.00' }}</h4>
                                            <small class="text-white-50">Prize Pool</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                @if(auth()->user()->kv != 1)
                                    <div class="alert alert-warning text-dark p-2 mb-3">
                                        <small><i class="fas fa-shield-alt me-1"></i>KYC verification required for lottery</small>
                                    </div>
                                @endif
                                <div class="d-grid gap-2"> 
                                    @if(auth()->user()->kv == 1)
                                        <a href="{{ route('lottery.index') }}" class="btn btn-light btn-lg fw-bold shadow">
                                            <i class="fas fa-ticket-alt me-2"></i>Buy Lottery Tickets
                                        </a>
                                    @else
                                        <a href="{{ route('user.kyc.index') }}" class="btn btn-warning btn-lg fw-bold shadow">
                                            <i class="fas fa-user-check me-2"></i>Complete KYC First
                                        </a>
                                    @endif
                                    <a href="{{ route('lottery.my.tickets') }}" class="btn btn-outline-light">
                                        <i class="fas fa-list me-2"></i>My Tickets
                                    </a>
                                    <a href="{{ route('lottery.results') }}" class="btn btn-outline-light">
                                        <i class="fas fa-trophy me-2"></i>View Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Access Vault - Completely Redesigned -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="fresh-video-vault">
                    <div class="video-vault-header">
                        <div class="video-vault-title-section">
                            <div class="video-vault-icon">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <div class="video-vault-title-text">
                                <h5>💎 Video Access Vault</h5>
                                <p>Your video earnings dashboard</p>
                            </div>
                        </div>
                        @php
                            $activePlan = auth()->user()->getHighestActivePlan();
                            $dailyLimit = auth()->user()->getDailyVideoLimit();
                            $todayViews = \App\Models\VideoView::where('user_id', auth()->id())
                                    ->whereDate('viewed_at', today())
                                    ->count();
                            $remainingViews = max(0, $dailyLimit - $todayViews);
                            $videoRate = $activePlan ? $activePlan->video_earning_rate : 0;
                        @endphp
                        <div class="video-vault-badges">
                            @if($activePlan)
                                <div class="vault-badge vault-badge-plan">
                                    <i class="fas fa-award"></i> {{ $activePlan->name }}
                                </div>
                            @endif
                            <div class="vault-badge vault-badge-limit">
                                <i class="fas fa-play-circle"></i> Limit: {{ $todayViews }}/{{ $dailyLimit }} videos
                            </div>
                        </div>
                    </div>
                    
                    <div class="video-vault-content">
                        @if($activePlan)
                            <div class="video-plan-info">
                                <div class="plan-info-content">
                                    <div class="plan-details">
                                        <h6><i class="fas fa-film"></i> Your Plan: <span>{{ $activePlan->name }}</span></h6>
                                        <div class="plan-badges">
                                            <span class="plan-badge plan-badge-success">Daily Limit: {{ $dailyLimit }} videos</span>
                                            <span class="plan-badge plan-badge-info">Rate: ${{ number_format($videoRate, 4) }}/video</span>
                                            <span class="plan-badge plan-badge-warning">Max Daily: ${{ number_format($dailyLimit * $videoRate, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="plan-progress">
                                        <div class="progress-container">
                                            <div class="progress-bar-new {{ $remainingViews == 0 ? 'progress-danger' : 'progress-success' }}" 
                                                style="width: {{ min(100, ($todayViews / max(1, $dailyLimit)) * 100) }}%"></div>
                                        </div>
                                        <small class="progress-text">
                                            {{ $remainingViews }} videos remaining today
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="video-stats-grid">
                            <div class="video-stat-item">
                                <div class="video-stat-icon">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                                <div class="video-stat-content">
                                    <h4>
                                        @php
                                            $totalVideosWatched = \App\Models\VideoView::where('user_id', auth()->id())->count();
                                        @endphp
                                        {{ $totalVideosWatched }}
                                    </h4>
                                    <p>Videos Watched</p>
                                </div>
                            </div>
                            
                            <div class="video-stat-item">
                                <div class="video-stat-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="video-stat-content">
                                    <h4>
                                        @php
                                            $videoEarnings = \App\Models\Transaction::where('user_id', auth()->id())
                                                ->where('remark', 'video_view_earning')
                                                ->sum('amount');
                                        @endphp
                                        ${{ showAmount($videoEarnings) }}
                                    </h4>
                                    <p>Video Earnings</p>
                                </div>
                            </div>
                            
                            <div class="video-stat-item">
                                <div class="video-stat-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="video-stat-content">
                                    <h4>{{ $todayViews }}@if($dailyLimit > 0)<small>/{{ $dailyLimit }}</small>@endif</h4>
                                    <p>Today's Views</p>
                                </div>
                            </div>
                            
                            <div class="video-stat-item">
                                <div class="video-stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="video-stat-content">
                                    <h4>
                                        @php
                                            $pendingEarnings = $todayViews * ($videoRate ?: 0.01);
                                        @endphp
                                        ${{ number_format($pendingEarnings, 2) }}
                                    </h4>
                                    <p>Pending Earnings</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="video-actions">
                            <div class="video-action-buttons">
                                <!-- Dropdown approach as backup -->
                                <div class="btn-group">
                                    <button type="button" class="video-action-btn video-btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-play"></i>Watch Videos
                                        @if($remainingViews > 0)
                                            <span class="action-badge">{{ $remainingViews }} left</span>
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('user.video-views.index') }}">
                                            <i class="fas fa-th-large me-2"></i>Gallery-1 (Standard)
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('user.video-views.simple') }}">
                                            <i class="fas fa-list me-2"></i>Gallery-2 (Simple)
                                        </a></li>
                                    </ul>
                                </div>
                                
                                <!-- Alternative: Modal button (hidden by default, you can show it if dropdown doesn't work) -->
                                <button onclick="showVideoGalleryChoice()" class="video-action-btn video-btn-primary d-none" id="modalVideoBtn">
                                    <i class="fas fa-play"></i>Watch Videos (Modal)
                                    @if($remainingViews > 0)
                                        <span class="action-badge">{{ $remainingViews }} left</span>
                                    @endif
                                </button>
                                
                                <a href="{{ route('user.video-views.history') }}" class="video-action-btn video-btn-secondary">
                                    <i class="fas fa-history"></i>View History
                                </a>
                                <a href="{{ route('user.video-views.earnings') }}" class="video-action-btn video-btn-success">
                                    <i class="fas fa-chart-bar"></i>Earnings Report
                                </a>
                            </div>
                            
                            @if($remainingViews == 0 && $dailyLimit > 0)
                                <div class="video-alert video-alert-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>You've reached your daily limit of {{ $dailyLimit }} videos. New videos will be available tomorrow!</span>
                                </div>
                            @elseif(!$activePlan)
                                <div class="video-alert video-alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Upgrade your plan to increase your daily video limit and earning rate!</span>
                                    <a href="{{ route('invest.index') }}" class="alert-action-btn">View Plans</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment & Transaction Overview -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="color: #495057;">Investment Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #198754;">${{ showAmount($runningInvests) }}</h4>
                                    <p class="text-muted mb-0 small">Running Investments</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #6c757d;">${{ showAmount($completedInvests) }}</h4>
                                    <p class="text-muted mb-0 small">Completed Investments</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1 fw-bold" style="color: #0d6efd;">${{ showAmount($interests) }}</h4>
                                    <p class="text-muted mb-0 small">Total Interest Earned</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="color: #495057;">Transaction Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #dc3545;">${{ showAmount($balance_transfer) }}</h4>
                                    <p class="text-muted mb-0 small">Total Transferred</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #198754;">${{ showAmount($balance_received) }}</h4>
                                    <p class="text-muted mb-0 small">Total Received</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1 fw-bold" style="color: #0dcaf0;">${{ showAmount($referral_earnings) }}</h4>
                                    <p class="text-muted mb-0 small">Referral Earnings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="color: #495057;">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <a href="{{ route('deposit.index') }}" class="text-decoration-none">
                                    <div class="card text-white text-center h-100 quick-action-card" style="background: linear-gradient(135deg, #ff9d00 0%, #ff6a00 100%);">
                                        <div class="card-body d-flex flex-column justify-content-center py-4">
                                            <i class="fas fa-wallet fa-3x mb-3"></i>
                                            <h5 class="mb-0">Add Funds to Your Account</h5>
                                            <p class="mt-2 mb-0">Deposit money to invest and increase your earnings</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('invest.index') }}" class="text-decoration-none">
                                    <div class="card text-white text-center h-100 quick-action-card" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                                            <h6 class="mb-0">Invest</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ auth()->user()->kv == 1 ? route('lottery.index') : route('user.kyc.index') }}" class="text-decoration-none">
                                    <div class="card text-white text-center h-100 quick-action-card" style="background: linear-gradient(135deg, #e91e63 0%, #ad1457 100%);">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            @if(auth()->user()->kv == 1)
                                                <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                                <h6 class="mb-0">Lottery</h6>
                                            @else
                                                <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                                <h6 class="mb-0 small">KYC Required</h6>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('user.transfer_funds') }}" class="text-decoration-none">
                                    <div class="card text-white text-center h-100 quick-action-card" style="background: linear-gradient(135deg, #ffc107 0%, #ffb30a 100%);">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                                            <h6 class="mb-0">Transfer</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <a href="{{ route('user.team-tree') }}" class="text-decoration-none">
                                    <div class="card text-white text-center h-100 quick-action-card" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <h6 class="mb-0">My Team</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="color: #495057;">
                            <i class="fas fa-users me-2" style="color: #0d6efd;"></i>
                            Referral Program
                        </h5>
                        <a href="{{ route('user.refferral-history') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-history me-1"></i>View History
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Your Referral Link:</h6>
                                    <div class="input-group">
                                        <input type="text" id="referralLink" class="form-control bg-light" 
                                               value="{{ auth()->user()->getReferralLink() }}" 
                                               readonly>
                                        <button class="btn btn-outline-primary" type="button" id="copyReferralLink">
                                            <i class="fas fa-copy me-1"></i>Copy
                                        </button>
                                    </div>
                                    <small class="text-muted">Share this link with your friends to earn referral commissions</small>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="mb-1 fw-bold" style="color: #0d6efd;">
                                                {{ auth()->user()->referrals()->count() }}
                                            </h4>
                                            <p class="text-muted mb-0 small">Total Referrals</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="mb-1 fw-bold" style="color: #198754;">
                                                ${{ showAmount($referral_earnings) }}
                                            </h4>
                                            <p class="text-muted mb-0 small">Total Referral Earnings</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center p-3 border rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-handshake fa-3x text-white mb-2"></i>
                                    <h6 class="text-white mb-2">Earn Commission</h6>
                                    <p class="text-white-50 mb-0 small">
                                        Get rewarded for every friend you invite to join our platform
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('user.sponsor-list') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-list me-1"></i>My Referrals
                                    </a>
                                    <a href="{{ route('user.team-tree') }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-sitemap me-1"></i>Team Tree
                                    </a>
                                    <button class="btn btn-outline-success btn-sm" onclick="shareReferralLink()">
                                        <i class="fas fa-share-alt me-1"></i>Share Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="color: #495057;">Recent Transactions</h5>
                        <a href="{{ route('user.transfer_history') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($transactions && count($transactions) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="color: #495057;">Transaction ID</th>
                                            <th style="color: #495057;">Type</th>
                                            <th style="color: #495057;">Amount</th>
                                            <th style="color: #495057;">Status</th>
                                            <th style="color: #495057;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions->take(5) as $transaction)
                                        <tr>
                                            <td><code>{{ $transaction->trx }}</code></td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $transaction->trx_type == '+' ? '#198754' : '#dc3545' }}; color: white;">
                                                    {{ $transaction->trx_type == '+' ? 'Credit' : 'Debit' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold" style="color: {{ $transaction->trx_type == '+' ? '#198754' : '#dc3545' }};">
                                                {{ $transaction->trx_type }}${{ showAmount($transaction->amount) }}
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: #198754; color: white;">Completed</span>
                                            </td>
                                            <td style="color: #6c757d;">{{ $transaction->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Recent Transactions</h5>
                                <p class="text-muted">Your transaction history will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Gallery Choice Modal -->
        <div class="modal fade" id="videoGalleryModal" tabindex="-1" aria-labelledby="videoGalleryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title" id="videoGalleryModalLabel">
                            <i class="fas fa-play-circle me-2"></i>Choose Video Gallery
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">Select which video gallery you'd like to access:</p>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.video-views.index') }}" class="text-decoration-none">
                                    <div class="gallery-choice-card">
                                        <div class="gallery-choice-icon">
                                            <i class="fas fa-th-large"></i>
                                        </div>
                                        <div class="gallery-choice-content">
                                            <h6 class="mb-1">Gallery-1</h6>
                                            <p class="mb-0 text-muted">Standard video gallery view</p>
                                        </div>
                                        <div class="gallery-choice-arrow">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-12">
                                <a href="{{ route('user.video-views.simple') }}" class="text-decoration-none">
                                    <div class="gallery-choice-card">
                                        <div class="gallery-choice-icon">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <div class="gallery-choice-content">
                                            <h6 class="mb-1">Gallery-2</h6>
                                            <p class="mb-0 text-muted">Simple video gallery view</p>
                                        </div>
                                        <div class="gallery-choice-arrow">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .bg-primary-light {
                background-color: rgba(13, 110, 253, 0.1);
            }
            
            .bg-warning-light {
                background-color: rgba(255, 193, 7, 0.1);
            }
            
            .bg-success-light {
                background-color: rgba(25, 135, 84, 0.1);
            }
            
            .bg-info-light {
                background-color: rgba(13, 202, 240, 0.1);
            }
            
            /* Enhanced Earnings Card Styles */
            .earnings-icon-container {
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                border-radius: 50%;
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
            }
            
            .earnings-display {
                transition: all 0.3s ease;
                border: 1px solid #e9ecef !important;
            }
            
            .earnings-display:hover {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
                transform: translateY(-2px);
            }
            
            .earnings-amount h2 {
                font-size: 2.5rem;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            
            .breakdown-item {
                transition: all 0.3s ease;
                border: 1px solid #f8f9fa !important;
            }
            
            .breakdown-item:hover {
                transform: translateX(5px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            
            .breakdown-item .fas {
                width: 20px;
                text-align: center;
            }
            
            /* Improved Badge Styling */
            .badge.bg-success {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
                color: white;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                border-radius: 50px;
                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            }
            
            /* Enhanced Border Colors */
            .border-primary {
                border-color: #0d6efd !important;
            }
            
            .border-info {
                border-color: #0dcaf0 !important;
            }
            
            .border-warning {
                border-color: #ffc107 !important;
            }
            
            .border-3 {
                border-width: 3px !important;
            }
            
            .card {
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }
            
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            }
            
            .quick-action-card {
                transition: all 0.3s ease;
                cursor: pointer;
                border: none;
            }
            
            .quick-action-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            }
            
            .table-hover tbody tr:hover {
                background-color: rgba(0,0,0,0.025);
            }
            
            .badge {
                font-size: 0.75em;
                padding: 0.35em 0.65em;
                border-radius: 0.375rem;
            }
            
            .opacity-75 {
                opacity: 0.75;
            }
            
            .shadow-sm {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            
            .fw-bold {
                font-weight: 700 !important;
            }
            
            .fw-medium {
                font-weight: 500 !important;
            }
            
            .text-muted {
                color: #6c757d !important;
            }
            
            /* Ensure all text colors are visible */
            .card-title {
                color: #495057 !important;
            }
            
            h3, h4, h5, h6 {
                color: inherit !important;
            }
            
            .btn {
                border-radius: 0.375rem;
                font-weight: 500;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            
            .gap-2 {
                gap: 0.5rem !important;
            }
            
            .me-1 {
                margin-right: 0.25rem !important;
            }
            
            /* Session Notifications Animation */
            #session-notifications-area {
                transition: all 0.3s ease-in-out;
            }
            
            .badge.bg-warning {
                animation: pulse-warning 2s infinite;
                transition: all 0.3s ease;
            }
            
            @keyframes pulse-warning {
                0% {
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
                }
            }
            
            .badge.bg-warning:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
            }
            
            /* Session container updates */
            #session-status-container {
                position: relative;
            }
            
            .session-update-indicator {
                position: absolute;
                top: -5px;
                right: -5px;
                width: 10px;
                height: 10px;
                background: #28a745;
                border-radius: 50%;
                animation: blink 1s infinite;
            }
            
            @keyframes blink {
                0%, 50% { opacity: 1; }
                51%, 100% { opacity: 0; }
            }
            
            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem;
                }
                
                .h3 {
                    font-size: 1.5rem;
                }
                
                .quick-action-card .card-body {
                    padding: 0.75rem;
                }
                
                .btn-sm {
                    font-size: 0.8rem;
                    padding: 0.2rem 0.4rem;
                }
                
                .earnings-amount h2 {
                    font-size: 2rem;
                }
                
                .breakdown-item {
                    margin-bottom: 0.75rem !important;
                }
            }
            
            /* Video Gallery Choice Modal Styles */
            .gallery-choice-card {
                display: flex;
                align-items: center;
                padding: 1rem;
                border: 2px solid #e9ecef;
                border-radius: 0.5rem;
                transition: all 0.3s ease;
                background: #fff;
            }
            
            .gallery-choice-card:hover {
                border-color: #0d6efd;
                background: #f8f9ff;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
            }
            
            .gallery-choice-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
                color: white;
                font-size: 1.2rem;
            }
            
            .gallery-choice-content {
                flex-grow: 1;
            }
            
            .gallery-choice-content h6 {
                color: #495057;
                font-weight: 600;
            }
            
            .gallery-choice-arrow {
                color: #6c757d;
                font-size: 1.1rem;
                transition: all 0.3s ease;
            }
            
            .gallery-choice-card:hover .gallery-choice-arrow {
                color: #0d6efd;
                transform: translateX(5px);
            }
            
            .modal-content {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            }
            
            .modal-header.bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 1rem 1rem 0 0;
            }
            
            /* SweetAlert2 custom styles */
            .swal-wide {
                width: 600px !important;
            }
            
            /* Dropdown styling */
            .btn-group .video-action-btn.dropdown-toggle::after {
                margin-left: 8px;
            }
            
            .dropdown-menu {
                border-radius: 10px;
                border: none;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                padding: 10px 0;
            }
            
            .dropdown-item {
                padding: 10px 20px;
                display: flex;
                align-items: center;
                transition: all 0.3s ease;
            }
            
            .dropdown-item:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transform: translateX(5px);
            }
        </style>
    @endpush

    @push('script')
        <script>
            // Auto-refresh session notifications every 10 seconds
            let sessionCheckInterval;
            let isPageActive = true;

            // Page visibility API to pause updates when tab is not active
            document.addEventListener('visibilitychange', function() {
                isPageActive = !document.hidden;
                if (isPageActive) {
                    // Resume checking when tab becomes active
                    checkSessionNotifications();
                    startSessionMonitoring();
                } else {
                    // Pause checking when tab is inactive
                    stopSessionMonitoring();
                }
            });

            // Start session monitoring on page load
            document.addEventListener('DOMContentLoaded', function() {
                startSessionMonitoring();
                
                // Check if there are existing notifications on page load
                const existingNotifications = document.querySelector('#session-notifications-area .badge.bg-warning');
                if (existingNotifications) {
                    const text = existingNotifications.textContent || '';
                    const match = text.match(/(\d+)/);
                    const count = match ? parseInt(match[1]) : 0;
                    
                    if (count > 0) {
                        // Show a welcome notification about existing alerts
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'info',
                                title: 'Security Alerts Pending',
                                text: `You have ${count} unread security alert${count > 1 ? 's' : ''} waiting for your attention.`,
                                showCancelButton: true,
                                confirmButtonText: 'Review Now',
                                cancelButtonText: 'Later',
                                confirmButtonColor: '#007bff',
                                cancelButtonColor: '#6c757d',
                                toast: true,
                                position: 'top-end',
                                timer: 6000,
                                timerProgressBar: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    showSessionNotifications();
                                }
                            });
                        }, 2000); // Show after 2 seconds to let page fully load
                    }
                }
            });

            function startSessionMonitoring() {
                // Session monitoring disabled for better performance
                // Will only check on page load instead of continuous polling
            }

            function stopSessionMonitoring() {
                if (sessionCheckInterval) {
                    clearInterval(sessionCheckInterval);
                    sessionCheckInterval = null;
                }
            }

            // Toggle earnings visibility function
            function toggleEarningsVisibility() {
                const earningsAmount = document.getElementById('earningsAmount');
                const hiddenEarnings = document.getElementById('hiddenEarnings');
                const earningsIcon = document.getElementById('earningsIcon');
                
                if (earningsAmount.style.display === 'none') {
                    // Show earnings
                    earningsAmount.style.display = 'block';
                    hiddenEarnings.style.display = 'none';
                    earningsIcon.className = 'fas fa-eye-slash';
                } else {
                    // Hide earnings
                    earningsAmount.style.display = 'none';
                    hiddenEarnings.style.display = 'block';
                    earningsIcon.className = 'fas fa-eye';
                }
            }

            // Toggle wallet visibility function
            function toggleWalletVisibility(walletType) {
                const amountElement = document.getElementById(walletType + 'WalletAmount');
                const hiddenElement = document.getElementById(walletType + 'WalletHidden');
                const iconElement = document.getElementById(walletType + 'WalletIcon');
                
                if (amountElement.style.display === 'none') {
                    // Show amount
                    amountElement.style.display = 'block';
                    hiddenElement.style.display = 'none';
                    iconElement.className = 'fas fa-eye-slash';
                } else {
                    // Hide amount
                    amountElement.style.display = 'none';
                    hiddenElement.style.display = 'block';
                    iconElement.className = 'fas fa-eye';
                }
            }

            // Function to check for session notifications (DISABLED)
            function checkSessionNotifications() {
                // Temporarily disabled to prevent 500 errors
                // This functionality will be restored once the session notifications system is properly configured
                console.log('Session notifications check is temporarily disabled');
                return;
                
                /* DISABLED CODE:
                fetch('/user/session-notifications/check', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    updateSessionNotificationsArea(data);
                })
                .catch(error => {
                    console.error('Error checking session notifications:', error);
                });
                */
            }

            // Function to update the session notifications area
            function updateSessionNotificationsArea(data) {
                const notificationsArea = document.getElementById('session-notifications-area');
                if (!notificationsArea) return;

                const unreadCount = data.unread_count || 0;
                const currentCount = getCurrentNotificationCount();
                
                // Only update if count has changed
                if (unreadCount !== currentCount) {
                    // Add update indicator
                    showUpdateIndicator();
                    
                    if (unreadCount > 0) {
                        notificationsArea.innerHTML = `
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark" onclick="showSessionNotifications()" style="cursor: pointer;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    ${unreadCount} Security Alert${unreadCount > 1 ? 's' : ''}
                                </span>
                            </div>
                        `;
                        
                        // Show toast notification for new alerts (only if count increased)
                        if (unreadCount > currentCount && currentCount >= 0) {
                            // Show both toast and Swal for important security alerts
                            showToast(`New security alert detected! You have ${unreadCount} unread alert${unreadCount > 1 ? 's' : ''}`, 'warning');
                            
                            // Also show a more prominent Swal notification
                            Swal.fire({
                                icon: 'warning',
                                title: 'Security Alert Detected!',
                                text: `You have ${unreadCount} new security notification${unreadCount > 1 ? 's' : ''}. Please review immediately.`,
                                showCancelButton: true,
                                confirmButtonText: 'View Alerts',
                                cancelButtonText: 'Later',
                                confirmButtonColor: '#ffc107',
                                cancelButtonColor: '#6c757d',
                                timer: 8000,
                                timerProgressBar: true,
                                toast: false,
                                position: 'center'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    showSessionNotifications();
                                }
                            });
                        }
                    } else {
                        notificationsArea.innerHTML = '';
                    }
                }
            }
            
            // Helper function to get current notification count
            function getCurrentNotificationCount() {
                const badge = document.querySelector('#session-notifications-area .badge.bg-warning');
                if (!badge) return 0;
                
                const text = badge.textContent || '';
                const match = text.match(/(\d+)/);
                return match ? parseInt(match[1]) : 0;
            }
            
            // Show visual update indicator
            function showUpdateIndicator() {
                const container = document.getElementById('session-status-container');
                if (!container) return;
                
                // Remove existing indicator
                const existingIndicator = container.querySelector('.session-update-indicator');
                if (existingIndicator) {
                    existingIndicator.remove();
                }
                
                // Add new indicator
                const indicator = document.createElement('div');
                indicator.className = 'session-update-indicator';
                container.appendChild(indicator);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    if (indicator.parentNode) {
                        indicator.remove();
                    }
                }, 3000);
            }

            // Session Notifications Functions
            window.showSessionNotifications = function() {
                fetch('/user/session-notifications', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    displayNotificationsModal(data.notifications);
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to load security notifications. Please check your internet connection.',
                        showCancelButton: true,
                        confirmButtonText: 'Refresh Page',
                        cancelButtonText: 'Try Again',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Try to fetch notifications again
                            showSessionNotifications();
                        }
                    });
                });
            };
            
            function displayNotificationsModal(notifications) {
                const modalHtml = `
                    <div class="modal fade" id="sessionNotificationsModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title">
                                        <i class="fas fa-shield-alt me-2"></i>Security Alerts
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ${notifications.length > 0 ? 
                                        notifications.map(notification => `
                                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                                <div class="me-3">
                                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="alert-heading">${notification.title}</h6>
                                                    <p class="mb-2">${notification.message}</p>
                                                    <div class="small text-muted">
                                                        <div><strong>New Login From:</strong> ${notification.new_login_device || 'Unknown Device'}</div>
                                                        <div><strong>IP Address:</strong> ${notification.new_login_ip}</div>
                                                        <div><strong>Location:</strong> ${notification.new_login_location || 'Unknown'}</div>
                                                        <div><strong>Time:</strong> ${new Date(notification.created_at).toLocaleString()}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('') 
                                        : '<div class="alert alert-info">No security alerts found.</div>'
                                    }
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="markNotificationsAsRead()">Mark All as Read</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                const existingModal = document.getElementById('sessionNotificationsModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('sessionNotificationsModal'));
                modal.show();
            }
            
            window.markNotificationsAsRead = function() {
                Swal.fire({
                    icon: 'question',
                    title: 'Mark All as Read?',
                    text: 'This will mark all security alerts as read. Are you sure?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Mark as Read',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Marking notifications as read',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        fetch('/user/session-notifications/mark-read', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the notifications area to remove the badge
                                const notificationsArea = document.getElementById('session-notifications-area');
                                if (notificationsArea) {
                                    notificationsArea.innerHTML = '';
                                }
                                
                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('sessionNotificationsModal'));
                                if (modal) {
                                    modal.hide();
                                }
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Security alerts marked as read successfully',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error marking notifications as read:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: 'Unable to mark notifications as read. Please try again.',
                                showCancelButton: true,
                                confirmButtonText: 'Retry',
                                cancelButtonText: 'Close',
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#6c757d'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    markNotificationsAsRead();
                                }
                            });
                        });
                    }
                });
            };


            // Copy referral link functionality
            document.getElementById('copyReferralLink').addEventListener('click', function() {
                const referralLink = document.getElementById('referralLink');
                referralLink.select();
                referralLink.setSelectionRange(0, 99999); // For mobile devices
                
                // Use modern clipboard API if available
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(referralLink.value).then(function() {
                        showToast('Referral link copied to clipboard!', 'success');
                    }).catch(function(err) {
                        // Fallback to execCommand
                        document.execCommand('copy');
                        showToast('Referral link copied to clipboard!', 'success');
                    });
                } else {
                    // Fallback for older browsers
                    document.execCommand('copy');
                    showToast('Referral link copied to clipboard!', 'success');
                }
            });

            // Share referral link functionality
            function shareReferralLink() {
                const referralLink = document.getElementById('referralLink').value;
                const shareData = {
                    title: 'Join {{ config("app.name") }} and Start Earning!',
                    text: 'Join me on {{ config("app.name") }} and start earning with our amazing investment platform!',
                    url: referralLink
                };

                // Check if Web Share API is supported
                if (navigator.share) {
                    navigator.share(shareData).then(() => {
                        showToast('Referral link shared successfully!', 'success');
                    }).catch((err) => {
                        // Fallback to manual sharing
                        fallbackShare(referralLink);
                    });
                } else {
                    // Fallback for browsers that don't support Web Share API
                    fallbackShare(referralLink);
                }
            }

            // Fallback share function
            function fallbackShare(link) {
                // Create a temporary modal with social sharing options
                const shareModal = `
                    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="shareModalLabel">Share Your Referral Link</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="shareLink" class="form-label">Your Referral Link:</label>
                                        <div class="input-group">
                                            <input type="text" id="shareLink" class="form-control" value="${link}" readonly>
                                            <button class="btn btn-outline-primary" type="button" onclick="copyFromModal()">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="https://wa.me/?text=Join me on {{ config("app.name") }} and start earning! ${encodeURIComponent(link)}" 
                                           class="btn btn-success" target="_blank">
                                            <i class="fab fa-whatsapp me-2"></i>Share on WhatsApp
                                        </a>
                                        <a href="https://t.me/share/url?url=${encodeURIComponent(link)}&text=Join me on {{ config("app.name") }} and start earning!" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fab fa-telegram me-2"></i>Share on Telegram
                                        </a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(link)}" 
                                           class="btn btn-primary" target="_blank">
                                            <i class="fab fa-facebook me-2"></i>Share on Facebook
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?text=Join me on {{ config('app.name') }} and start earning!&url=${encodeURIComponent(link)}" 
                                           class="btn btn-info" target="_blank">
                                            <i class="fab fa-twitter me-2"></i>Share on Twitter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add modal to page if it doesn't exist
                if (!document.getElementById('shareModal')) {
                    document.body.insertAdjacentHTML('beforeend', shareModal);
                }
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('shareModal'));
                modal.show();
            }

            // Copy function for the modal
            function copyFromModal() {
                const shareLink = document.getElementById('shareLink');
                shareLink.select();
                shareLink.setSelectionRange(0, 99999);
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(shareLink.value).then(function() {
                        showToast('Link copied to clipboard!', 'success');
                    }).catch(function(err) {
                        document.execCommand('copy');
                        showToast('Link copied to clipboard!', 'success');
                    });
                } else {
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!', 'success');
                }
            }

            // Toast notification function
            function showToast(message, type = 'info') {
                // Check if a toast container exists, if not create one
                let toastContainer = document.getElementById('toastContainer');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toastContainer';
                    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                    toastContainer.style.zIndex = '1055';
                    document.body.appendChild(toastContainer);
                }

                const toastId = 'toast-' + Date.now();
                let bgClass, iconClass;
                
                switch(type) {
                    case 'success':
                        bgClass = 'bg-success';
                        iconClass = 'fa-check-circle';
                        break;
                    case 'warning':
                        bgClass = 'bg-warning text-dark';
                        iconClass = 'fa-exclamation-triangle';
                        break;
                    case 'error':
                        bgClass = 'bg-danger';
                        iconClass = 'fa-exclamation-circle';
                        break;
                    default:
                        bgClass = 'bg-primary';
                        iconClass = 'fa-info-circle';
                }
                
                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${iconClass} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, {
                    autohide: true,
                    delay: type === 'warning' ? 5000 : 3000 // Warning toasts stay longer
                });
                
                toast.show();
                
                // Remove the toast element after it's hidden
                toastElement.addEventListener('hidden.bs.toast', function() {
                    toastElement.remove();
                });
            }
            
            // Fresh Login Cache Clearing - Light version to prevent white screen
            if (sessionStorage.getItem('fresh_login_cache_cleared') !== 'true') {
                // This is the first access to dashboard after login
                console.log('Performing light fresh login cache clearing...');
                
                try {
                    // Only clear specific browser storage items (not all)
                    if (typeof(Storage) !== 'undefined') {
                        // Clear only dashboard-related items, not everything
                        const itemsToClear = ['dashboard_cache', 'user_cache', 'temp_data', 'old_session'];
                        itemsToClear.forEach(item => {
                            localStorage.removeItem(item);
                            sessionStorage.removeItem(item);
                        });
                        console.log('Specific storage items cleared');
                    }
                    
                    // Set flag that cache has been cleared
                    sessionStorage.setItem('fresh_login_cache_cleared', 'true');
                    sessionStorage.setItem('cache_cleared_timestamp', Date.now().toString());
                    
                    console.log('Light fresh login cache clearing completed');
                } catch (error) {
                    console.error('Fresh login cache clearing error:', error);
                }
            }
            
            // Video Gallery Choice Modal Function
            function showVideoGalleryChoice() {
                console.log('showVideoGalleryChoice function called');
                
                // Check if Bootstrap is available
                if (typeof bootstrap === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    // Fallback: show a simple JavaScript confirm dialog
                    const choice = confirm('Choose Video Gallery:\n\nOK for Gallery-1 (Standard View)\nCancel for Gallery-2 (Simple View)');
                    if (choice) {
                        window.location.href = "{{ route('user.video-views.index') }}";
                    } else {
                        window.location.href = "{{ route('user.video-views.simple') }}";
                    }
                    return;
                }
                
                // Check if modal element exists
                const modalElement = document.getElementById('videoGalleryModal');
                if (!modalElement) {
                    console.error('Modal element not found');
                    // Fallback to confirm dialog
                    const choice = confirm('Choose Video Gallery:\n\nOK for Gallery-1 (Standard View)\nCancel for Gallery-2 (Simple View)');
                    if (choice) {
                        window.location.href = "{{ route('user.video-views.index') }}";
                    } else {
                        window.location.href = "{{ route('user.video-views.simple') }}";
                    }
                    return;
                }
                
                console.log('Creating Bootstrap modal');
                try {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    console.log('Modal shown successfully');
                } catch (error) {
                    console.error('Error showing modal:', error);
                    // Fallback to confirm dialog
                    const choice = confirm('Choose Video Gallery:\n\nOK for Gallery-1 (Standard View)\nCancel for Gallery-2 (Simple View)');
                    if (choice) {
                        window.location.href = "{{ route('user.video-views.index') }}";
                    } else {
                        window.location.href = "{{ route('user.video-views.simple') }}";
                    }
                }
            }
            
            // Alternative function using jQuery if Bootstrap doesn't work
            function showVideoGalleryChoiceAlt() {
                // Using SweetAlert2 if available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Choose Video Gallery',
                        html: `
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg" onclick="window.location.href='{{ route('user.video-views.index') }}'">
                                    <i class="fas fa-th-large me-2"></i>Gallery-1 (Standard)
                                </button>
                                <button class="btn btn-outline-primary btn-lg" onclick="window.location.href='{{ route('user.video-views.simple') }}'">
                                    <i class="fas fa-list me-2"></i>Gallery-2 (Simple)
                                </button>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'swal-wide'
                        }
                    });
                } else {
                    // Final fallback
                    const choice = confirm('Choose Video Gallery:\n\nOK for Gallery-1 (Standard View)\nCancel for Gallery-2 (Simple View)');
                    if (choice) {
                        window.location.href = "{{ route('user.video-views.index') }}";
                    } else {
                        window.location.href = "{{ route('user.video-views.simple') }}";
                    }
                }
            }
        </script>
        
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            /* ===========================================
               TOTAL EARNINGS HUB - FRESH DESIGN
               =========================================== */
            .new-earnings-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 20px;
                padding: 30px;
                margin-bottom: 20px;
                color: white;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            }
            
            .earnings-card-header {
                margin-bottom: 25px;
            }
            
            .earnings-title-section {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            
            .earnings-icon-new {
                background: rgba(255,255,255,0.2);
                width: 70px;
                height: 70px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
            }
            
            .earnings-title-text h3 {
                margin: 0;
                font-size: 28px;
                font-weight: bold;
            }
            
            .earnings-title-text p {
                margin: 5px 0 0 0;
                opacity: 0.8;
                font-size: 16px;
            }
            
            .earnings-content-area {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 30px;
                align-items: start;
            }
            
            .earnings-main-section {
                background: rgba(255,255,255,0.1);
                padding: 25px;
                border-radius: 15px;
            }
            
            .earnings-controls {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
            }
            
            .income-label {
                background: rgba(255,255,255,0.9);
                color: #667eea;
                padding: 8px 20px;
                border-radius: 25px;
                font-weight: bold;
                font-size: 14px;
            }
            
            .visibility-toggle {
                background: transparent;
                border: 2px solid rgba(255,255,255,0.5);
                color: white;
                padding: 8px 15px;
                border-radius: 25px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .visibility-toggle:hover {
                background: rgba(255,255,255,0.2);
                border-color: white;
            }
            
            .earnings-amount-display h1 {
                font-size: 48px;
                font-weight: bold;
                margin: 0;
                text-align: center;
            }
            
            .earnings-breakdown-section {
                background: rgba(255,255,255,0.1);
                padding: 25px;
                border-radius: 15px;
            }
            
            .breakdown-heading {
                margin-bottom: 20px;
                font-size: 18px;
                font-weight: bold;
            }
            
            .breakdown-item-new {
                display: flex;
                align-items: center;
                gap: 15px;
                background: rgba(255,255,255,0.1);
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 15px;
                transition: all 0.3s ease;
            }
            
            .breakdown-item-new:hover {
                background: rgba(255,255,255,0.2);
                transform: translateX(5px);
            }
            
            .breakdown-icon-container {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
            }
            
            .interest-bg { background: #28a745; }
            .referral-bg { background: #17a2b8; }
            .video-bg { background: #dc3545; }
            
            .breakdown-details {
                flex: 1;
            }
            
            .breakdown-type {
                display: block;
                font-size: 14px;
                opacity: 0.8;
            }
            
            .breakdown-amount {
                display: block;
                font-size: 18px;
                font-weight: bold;
                margin-top: 2px;
            }
            
            /* ===========================================
               ACCOUNT WALLETS - FRESH DESIGN
               =========================================== */
            .fresh-wallet-card {
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                border-radius: 20px;
                padding: 25px;
                color: white;
                box-shadow: 0 10px 30px rgba(30, 60, 114, 0.3);
                height: 100%;
            }
            
            .wallet-card-header {
                margin-bottom: 25px;
            }
            
            .wallet-title {
                font-size: 20px;
                font-weight: bold;
                margin: 0;
            }
            
            .wallet-title i {
                margin-right: 10px;
                color: rgba(255,255,255,0.8);
            }
            
            .wallet-item-fresh {
                background: rgba(255,255,255,0.1);
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 20px;
                transition: all 0.3s ease;
            }
            
            .wallet-item-fresh:hover {
                background: rgba(255,255,255,0.2);
                transform: translateY(-2px);
            }
            
            .wallet-icon-section {
                background: rgba(255,255,255,0.2);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }
            
            .wallet-info-section {
                flex: 1;
            }
            
            .wallet-label {
                font-size: 16px;
                margin-bottom: 10px;
                opacity: 0.9;
            }
            
            .wallet-amount-controls {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .wallet-toggle-btn {
                background: transparent;
                border: 2px solid rgba(255,255,255,0.5);
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .wallet-toggle-btn:hover {
                background: rgba(255,255,255,0.2);
                border-color: white;
            }
            
            .wallet-amount-value, .wallet-amount-hidden {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
            }
            
            /* ===========================================
               TEAM BONUS & INVESTMENTS - FRESH DESIGN
               =========================================== */
            .fresh-earnings-card {
                background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 50%, #a6ffcb 100%);
                border-radius: 20px;
                padding: 25px;
                color: white;
                box-shadow: 0 10px 30px rgba(31, 162, 255, 0.3);
                height: 100%;
            }
            
            .earnings-title {
                font-size: 20px;
                font-weight: bold;
                margin: 0 0 25px 0;
            }
            
            .earnings-title i {
                margin-right: 10px;
                color: rgba(255,255,255,0.8);
            }
            
            .earnings-item-fresh {
                background: rgba(255,255,255,0.1);
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 20px;
                transition: all 0.3s ease;
            }
            
            .earnings-item-fresh:hover {
                background: rgba(255,255,255,0.2);
                transform: translateY(-2px);
            }
            
            .earnings-icon-section {
                background: rgba(255,255,255,0.2);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }
            
            .earnings-info-section {
                flex: 1;
            }
            
            .earnings-label {
                font-size: 16px;
                margin-bottom: 10px;
                opacity: 0.9;
            }
            
            .earnings-amount-controls {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .earnings-toggle-btn {
                background: transparent;
                border: 2px solid rgba(255,255,255,0.5);
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .earnings-toggle-btn:hover {
                background: rgba(255,255,255,0.2);
                border-color: white;
            }
            
            .earnings-amount-value, .earnings-amount-hidden {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
            }
            
            /* ===========================================
               VIDEO ACCESS VAULT - FRESH DESIGN
               =========================================== */
            .fresh-video-vault {
                background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
                border-radius: 20px;
                padding: 30px;
                color: white;
                box-shadow: 0 10px 30px rgba(255, 154, 158, 0.3);
            }
            
            .video-vault-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .video-vault-title-section {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            
            .video-vault-icon {
                background: rgba(255,255,255,0.2);
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
            }
            
            .video-vault-title-text h5 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }
            
            .video-vault-title-text p {
                margin: 5px 0 0 0;
                opacity: 0.8;
                font-size: 14px;
            }
            
            .video-vault-badges {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            
            .vault-badge {
                background: rgba(255,255,255,0.9);
                color: #333;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: bold;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .vault-badge-plan {
                background: #007bff;
                color: white;
            }
            
            .vault-badge-limit {
                background: #17a2b8;
                color: white;
            }
            
            .video-plan-info {
                background: rgba(255,255,255,0.1);
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 25px;
            }
            
            .plan-info-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .plan-details h6 {
                margin-bottom: 10px;
                font-size: 16px;
            }
            
            .plan-badges {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
            
            .plan-badge {
                background: rgba(255,255,255,0.9);
                color: #333;
                padding: 4px 12px;
                border-radius: 15px;
                font-size: 12px;
                font-weight: bold;
            }
            
            .plan-badge-success { background: #28a745; color: white; }
            .plan-badge-info { background: #17a2b8; color: white; }
            .plan-badge-warning { background: #ffc107; color: #333; }
            
            .plan-progress {
                text-align: center;
            }
            
            .progress-container {
                background: rgba(255,255,255,0.3);
                height: 8px;
                border-radius: 4px;
                width: 150px;
                margin: 0 auto 5px auto;
                overflow: hidden;
            }
            
            .progress-bar-new {
                height: 100%;
                border-radius: 4px;
                transition: all 0.3s ease;
            }
            
            .progress-success { background: #28a745; }
            .progress-danger { background: #dc3545; }
            
            .progress-text {
                font-size: 12px;
                opacity: 0.9;
            }
            
            .video-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 25px;
            }
            
            .video-stat-item {
                background: rgba(255,255,255,0.1);
                padding: 20px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                gap: 15px;
                transition: all 0.3s ease;
            }
            
            .video-stat-item:hover {
                background: rgba(255,255,255,0.2);
                transform: translateY(-2px);
            }
            
            .video-stat-icon {
                background: rgba(255,255,255,0.2);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }
            
            .video-stat-content h4 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }
            
            .video-stat-content p {
                margin: 5px 0 0 0;
                font-size: 14px;
                opacity: 0.8;
            }
            
            .video-actions {
                text-align: center;
            }
            
            .video-action-buttons {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }
            
            .video-action-btn {
                background: rgba(255,255,255,0.9);
                color: #333;
                padding: 12px 20px;
                border-radius: 25px;
                text-decoration: none;
                font-weight: bold;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
            }
            
            .video-action-btn:hover {
                background: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                color: #333;
                text-decoration: none;
            }
            
            .video-btn-primary { background: #007bff; color: white; }
            .video-btn-secondary { background: #6c757d; color: white; }
            .video-btn-success { background: #28a745; color: white; }
            
            .action-badge {
                background: rgba(255,255,255,0.2);
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 11px;
            }
            
            .video-alert {
                background: rgba(255,255,255,0.1);
                padding: 15px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            
            .video-alert-warning {
                border-left: 4px solid #ffc107;
            }
            
            .video-alert-info {
                border-left: 4px solid #17a2b8;
            }
            
            .alert-action-btn {
                background: rgba(255,255,255,0.2);
                color: white;
                padding: 5px 15px;
                border-radius: 15px;
                text-decoration: none;
                font-size: 12px;
                margin-left: 10px;
            }
            
            .alert-action-btn:hover {
                background: rgba(255,255,255,0.3);
                color: white;
                text-decoration: none;
            }
            
            /* ===========================================
               RESPONSIVE DESIGN
               =========================================== */
            @media (max-width: 992px) {
                .earnings-content-area {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }
                
                .video-vault-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .plan-info-content {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .video-stats-grid {
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                }
                
                .video-action-buttons {
                    flex-direction: column;
                    align-items: center;
                    gap: 10px;
                }
                
                .video-action-btn {
                    width: 200px;
                    justify-content: center;
                }
            }
            
            @media (max-width: 768px) {
                .earnings-title-section {
                    flex-direction: column;
                    text-align: center;
                    gap: 15px;
                }
                
                .video-vault-title-section {
                    flex-direction: column;
                    text-align: center;
                    gap: 15px;
                }
                
                .video-stats-grid {
                    grid-template-columns: 1fr;
                }
                
                .wallet-item-fresh, .earnings-item-fresh {
                    flex-direction: column;
                    text-align: center;
                    gap: 15px;
                }
                
                .wallet-amount-controls, .earnings-amount-controls {
                    flex-direction: column;
                    gap: 10px;
                }
            }
            
            /* Legacy styles for other sections */
            .card {
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }
            
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            }
            
            .quick-action-card {
                transition: all 0.3s ease;
                cursor: pointer;
                border: none;
            }
            
            .quick-action-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            }
        </style>
    @endpush
</x-smart_layout>