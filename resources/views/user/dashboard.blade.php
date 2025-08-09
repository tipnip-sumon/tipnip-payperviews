<x-smart_layout>
    @section('title', $pageTitle)
    @section('content')
        <!-- Welcome Section -->
        <div class="row mb-4 my-4">
            <div class="col-12">
                <div class="card custom-card bg-gradient-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h4 class="text-white mb-1 fw-bold">Welcome back, {{ auth()->user()->firstname }} {{ auth()->user()->lastname }}!</h4> 
                                <p class="text-white-75 mb-0">Here's what's happening with your account today.</p>
                                <!-- Session Status -->
                                <div class="mt-2" id="session-status-container">
                                    <small class="badge bg-success">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Secure Session Active - Only one login allowed
                                    </small>
                                    @if(isset($user->session_created_at) && $user->session_created_at)
                                        <small class="text-white-50 ms-2">
                                            Session started: {{ $user->session_created_at->format('M d, h:i A') }}
                                        </small>
                                    @endif
                                    
                                    <!-- Session Notifications Alert - Dynamic Content -->
                                    {{--
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
                                    --}}
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
            
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card custom-card border-0 shadow-sm balance-card" data-stat="current-balance">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="text-muted mb-0">Account Wallet</h6>
                                    <button class="btn btn-sm btn-outline-secondary balance-toggle" onclick="toggleBalanceVisibility('current-balance')" title="Toggle Balance Visibility">
                                        <i class="fas fa-eye balance-toggle-icon"></i>
                                    </button>
                                </div>
                                <h3 class="mb-0 fw-bold balance-amount" style="color: #0d6efd;" id="current-balance-amount">
                                    <span class="balance-value">${{ showAmount($currentBalance) }}</span>
                                    <span class="balance-hidden" style="display: none;">****</span>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-muted">Updated: <span id="current-balance-time">{{ now()->format('H:i') }}</span></small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary-light rounded-circle p-3">
                                <i class="fas fa-wallet fa-lg" style="color: #0d6efd;"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card custom-card border-0 shadow-sm balance-card" data-stat="team-bonus">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="text-muted mb-0">Team Bonus</h6>
                                    <button class="btn btn-sm btn-outline-secondary balance-toggle" onclick="toggleBalanceVisibility('team-bonus')" title="Toggle Balance Visibility">
                                        <i class="fas fa-eye balance-toggle-icon"></i>
                                    </button>
                                </div>
                                <h3 class="mb-0 fw-bold balance-amount" style="color: #ffc107;" id="team-bonus-amount">
                                    <span class="balance-value">${{ showAmount($referral_earnings) }}</span>
                                    <span class="balance-hidden" style="display: none;">****</span>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        Monthly: ${{ showAmount($monthly_referral_earnings ?? 0) }}
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin text-warning"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-warning-light rounded-circle p-3">
                                <i class="fas fa-users fa-lg" style="color: #ffc107;"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $total_referrals ? min(100, ($total_referrals / 10) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card custom-card border-0 shadow-sm balance-card" data-stat="total-earnings">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="text-muted mb-0">Total Earnings Hub</h6>
                                    <button class="btn btn-sm btn-outline-secondary balance-toggle" onclick="toggleBalanceVisibility('total-earnings')" title="Toggle Balance Visibility">
                                        <i class="fas fa-eye balance-toggle-icon"></i>
                                    </button>
                                </div>
                                <h3 class="mb-0 fw-bold balance-amount" style="color: #198754;" id="total-earnings-amount">
                                    <span class="balance-value">${{ showAmount(auth()->user()->interest_wallet) }}</span>
                                    <span class="balance-hidden" style="display: none;">****</span>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-success">
                                        <i class="fas fa-coins me-1"></i>
                                        All Income: {{ $growth_percentage ?? 0 }}%
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin text-success"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-success-light rounded-circle p-3">
                                <i class="fas fa-coins fa-lg" style="color: #198754;"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, ($interests ?? 0) / max(1, $totalInvest ?? 1) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card custom-card border-0 shadow-sm balance-card" data-stat="video-access-vault">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="text-muted mb-0">Video Access Vault</h6>
                                    <button class="btn btn-sm btn-outline-secondary balance-toggle" onclick="toggleBalanceVisibility('video-access-vault')" title="Toggle Balance Visibility">
                                        <i class="fas fa-eye balance-toggle-icon"></i>
                                    </button>
                                </div>
                                <h3 class="mb-0 fw-bold balance-amount" style="color: #0dcaf0;" id="video-access-amount">
                                    <span class="balance-value">${{ showAmount($totalInvest) }}</span>
                                    <span class="balance-hidden" style="display: none;">****</span>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="text-info">
                                        <i class="fas fa-play-circle me-1"></i>
                                        Active Plans: ${{ showAmount($runningInvests ?? 0) }}
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin text-info"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-info-light rounded-circle p-3">
                                <i class="fas fa-video fa-lg" style="color: #0dcaf0;"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalInvest ? min(100, ($runningInvests ?? 0) / max(1, $totalInvest) * 100) : 0 }}%"></div>
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
                                            @php
                                                $actualPrizePool = 0;
                                                if ($currentDraw && $currentDraw->prize_distribution) {
                                                    $actualPrizePool = collect($currentDraw->prize_distribution)->sum('amount');
                                                } elseif ($currentDraw) {
                                                    $actualPrizePool = $currentDraw->total_prize_pool;
                                                }
                                            @endphp
                                            <h4 class="text-white mb-0">${{ number_format($actualPrizePool, 2) }}</h4>
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

        <!-- Video View System Income Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="color: #495057;">
                            Video View System Income 
                            <span class="badge bg-info ms-2">
                                <i class="fas fa-play me-1"></i>Active
                            </span>
                        </h5>
                        @php
                            $activePlan = $activePlan ?? auth()->user()->getHighestActivePlan();
                            $dailyLimit = $dailyLimit ?? auth()->user()->getDailyVideoLimit();
                            $todayViews = $todayViews ?? 0; // From optimized dashboard data
                            $remainingViews = $remainingViews ?? max(0, $dailyLimit - $todayViews);
                            $videoRate = $activePlan ? $activePlan->video_earning_rate : 0;
                        @endphp
                        <div class="d-flex align-items-center">
                            @if($activePlan)
                                <div class="badge bg-primary me-2 p-2">
                                    <i class="fas fa-award me-1"></i> {{ $activePlan->name }}
                                </div>
                            @endif
                            <div class="badge bg-info p-2">
                                <i class="fas fa-play-circle me-1"></i> Limit: {{ $todayViews }}/{{ $dailyLimit }} videos
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Optimization Info Banner -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-success border-success bg-light">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-video fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-primary">
                                                <i class="fas fa-play-circle me-1"></i>
                                                Video Earnings Dashboard
                                            </h6>
                                            <p class="mb-0 small text-muted">
                                                📺 Track your daily video progress • � Monitor earnings • � View statistics
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($activePlan)
                                <div class="col-12 mb-3">
                                    <div class="alert alert-light border border-primary shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <div>
                                                <h6 class="mb-1"><i class="fas fa-film text-primary"></i> Your Plan: <span class="text-primary">{{ $activePlan->name }}</span></h6>
                                                <p class="mb-0 small">
                                                    <span class="badge bg-success me-1">Daily Limit: {{ $dailyLimit }} videos</span>
                                                    <span class="badge bg-info me-1">Rate: ${{ number_format($videoRate, 4) }}/video</span>
                                                    <span class="badge bg-warning">Max Daily: ${{ number_format($dailyLimit * $videoRate, 2) }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <div class="progress" style="height: 8px; width: 150px;">
                                                    <div class="progress-bar {{ $remainingViews == 0 ? 'bg-danger' : 'bg-success' }}" role="progressbar" 
                                                        style="width: {{ min(100, ($todayViews / max(1, $dailyLimit)) * 100) }}%"></div>
                                                </div>
                                                <small class="d-block text-center mt-1">
                                                    {{ $remainingViews }} videos remaining today
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-play-circle fa-2x" style="color: #dc3545;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #dc3545;">
                                        {{ $totalVideosWatched ?? 0 }}
                                    </h4>
                                    <p class="text-muted mb-0 small">Videos Watched</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-dollar-sign fa-2x" style="color: #28a745;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #28a745;">
                                        ${{ showAmount($videoEarnings ?? 0) }}
                                    </h4>
                                    <p class="text-muted mb-0 small">Video Earnings</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-day fa-2x" style="color: #17a2b8;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #17a2b8;">
                                        {{ $todayViews }}
                                        @if($dailyLimit > 0)
                                            <small class="text-muted">/{{ $dailyLimit }}</small>
                                        @endif
                                    </h4>
                                    <p class="text-muted mb-0 small">Today's Views</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-clock fa-2x" style="color: #6f42c1;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #6f42c1;">
                                        ${{ number_format($todayEarnings ?? 0, 4) }}
                                    </h4>
                                    <p class="text-muted mb-0 small">Today's Earnings</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('user.video-views.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-play me-1"></i>Watch Videos
                                        @if($remainingViews > 0)
                                            <span class="badge bg-light text-dark ms-1">{{ $remainingViews }} left</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-history me-1"></i>View History
                                    </a>
                                    <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-chart-bar me-1"></i>Earnings Report
                                    </a>
                                </div>
                                @if($remainingViews == 0 && $dailyLimit > 0)
                                    <div class="text-center mt-3">
                                        <div class="alert alert-warning border-warning py-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            You've reached your daily limit of {{ $dailyLimit }} videos. New videos will be available tomorrow!
                                        </div>
                                    </div>
                                @elseif(!$activePlan)
                                    <div class="text-center mt-3">
                                        <div class="alert alert-info border-info py-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Upgrade your plan to increase your daily video limit and earning rate!
                                            <a href="{{ route('invest.index') }}" class="btn btn-sm btn-outline-primary ms-2">View Plans</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
    @endsection

    @push('style')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .bg-gradient-info {
                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            }
            
            /* Performance Metrics Styles */
            .metric-item {
                padding: 8px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.3s ease;
            }
            
            .metric-item:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }
            
            .metric-item i {
                font-size: 1.2rem;
                margin-bottom: 4px;
                display: block;
            }
            
            .metric-item h6 {
                font-size: 1.1rem;
                font-weight: 700;
                margin: 2px 0;
            }
            
            .metric-item small {
                font-size: 0.75rem;
                opacity: 0.9;
            }
            
            /* Animated performance indicators */
            @keyframes pulse-metric {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .metric-item.active {
                animation: pulse-metric 2s infinite;
            }
            
            /* Performance status colors */
            .performance-excellent { border-left: 4px solid #28a745; }
            .performance-good { border-left: 4px solid #ffc107; }
            .performance-poor { border-left: 4px solid #dc3545; }
            
            /* Text contrast improvements */
            .performance-banner {
                /* Remove any blur effects */
                backdrop-filter: none !important;
                filter: none !important;
                /* Ensure visibility */
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            .performance-banner .text-white {
                color: #ffffff !important;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
                font-weight: 600 !important;
                visibility: visible !important;
                opacity: 1 !important;
                display: inline !important;
            }
            
            .performance-banner h6 {
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
                font-weight: 700 !important;
                color: #ffffff !important;
                visibility: visible !important;
                opacity: 1 !important;
                display: block !important;
            }
            
            .performance-banner small {
                text-shadow: 0 2px 3px rgba(0, 0, 0, 0.8);
                color: #ffffff !important;
                visibility: visible !important;
                opacity: 1 !important;
                display: inline !important;
            }
            
            .performance-banner i {
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
                filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.6));
                color: #ffffff !important;
                visibility: visible !important;
                opacity: 1 !important;
                display: inline-block !important;
            }
            
            /* Ensure metric items have no blur and are fully visible */
            .performance-banner .metric-item {
                backdrop-filter: none !important;
                filter: none !important;
                background: rgba(255, 255, 255, 0.25) !important;
                border: 1px solid rgba(255, 255, 255, 0.4);
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Force visibility on all text elements in performance banner */
            .performance-banner * {
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            .text-white-75 {
                color: rgba(255, 255, 255, 0.85) !important;
            }
            
            .text-white-50 {
                color: rgba(255, 255, 255, 0.7) !important;
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

            /* Enhanced Balance Cards */
            .balance-card {
                position: relative;
                overflow: hidden;
            }
            
            .balance-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .balance-card:hover::before {
                left: 100%;
            }
            
            .balance-amount {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-weight: 700;
                transition: all 0.3s ease;
            }
            
            .loading-indicator {
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .loading-indicator.show {
                display: inline-block;
                opacity: 1;
            }
            
            /* Balance Toggle Styles */
            .balance-toggle {
                border: none !important;
                background: transparent !important;
                padding: 4px 8px !important;
                font-size: 12px !important;
                opacity: 0.7 !important;
                transition: all 0.3s ease !important;
            }
            
            .balance-toggle:hover {
                opacity: 1 !important;
                background: rgba(0,0,0,0.05) !important;
                transform: scale(1.1) !important;
            }
            
            .balance-toggle i {
                transition: all 0.3s ease !important;
            }
            
            .balance-value, .balance-hidden {
                transition: all 0.3s ease !important;
            }
            
            .balance-hidden {
                color: #6c757d !important;
                font-size: 1.5rem !important;
                letter-spacing: 2px !important;
            }
            
            /* Smooth fade transition for balance visibility */
            .balance-fade-out {
                opacity: 0 !important;
                transform: scale(0.95) !important;
            }
            
            .balance-fade-in {
                opacity: 1 !important;
                transform: scale(1) !important;
            }
            
            /* Progress bars */
            .progress {
                background-color: rgba(0,0,0,0.1);
                border-radius: 2px;
            }
            
            .progress-bar {
                transition: width 0.6s ease;
            }
            
            /* Performance Chart Container */
            #performanceChart {
                height: 250px !important;
            }
            
            /* Quick Stats Animation */
            .quick-stat-item {
                padding: 12px;
                border-radius: 8px;
                background: rgba(0,0,0,0.02);
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }
            
            .quick-stat-item:hover {
                background: rgba(0,0,0,0.05);
                border-left-color: #0d6efd;
                transform: translateX(5px);
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

            /* Toast Notification Styles */
            .toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            }

            .toast-notification {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                padding: 16px 20px;
                margin-bottom: 10px;
                border-left: 4px solid;
                min-width: 300px;
                animation: slideIn 0.3s ease-out;
            }

            .toast-notification.success {
                border-left-color: #28a745;
            }

            .toast-notification.error {
                border-left-color: #dc3545;
            }

            .toast-notification.info {
                border-left-color: #17a2b8;
            }

            .toast-notification.warning {
                border-left-color: #ffc107;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }

            /* Update animation for balance cards */
            .balance-card.updating .balance-amount {
                animation: balanceUpdate 0.6s ease-in-out;
            }
            
            .balance-card.updating {
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
                transition: box-shadow 0.6s ease;
            }

            @keyframes balanceUpdate {
                0% { 
                    transform: scale(1); 
                    filter: brightness(1);
                }
                50% { 
                    transform: scale(1.05); 
                    color: #28a745;
                    filter: brightness(1.1);
                    text-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
                }
                100% { 
                    transform: scale(1); 
                    filter: brightness(1);
                    text-shadow: none;
                }
            }
            
            /* Card update indicator pulse */
            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                }
            }

            /* Real-time indicator */
            .realtime-indicator {
                display: inline-block;
                width: 8px;
                height: 8px;
                background: #28a745;
                border-radius: 50%;
                margin-left: 8px;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
                }
                70% {
                    box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
                }
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

                .toast-notification {
                    min-width: 250px;
                    margin: 5px 10px;
                }

                #performanceChart {
                    height: 200px !important;
                }
            }
        </style>
    @endpush

    @push('script')
    
    <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Balance Toggle Function - Defined in separate script tag to ensure immediate availability -->
    <script>
        // Balance Visibility Toggle Function - Updated for individual card control
        // Check if balanceVisibility is already defined to prevent redeclaration
        if (typeof balanceVisibility === 'undefined') {
            var balanceVisibility = {
                'current-balance': true,
                'team-bonus': true,
                'total-earnings': true,
                'video-access-vault': true
            };
        }

        // Define function with card-specific parameter
        function toggleBalanceVisibility(cardType) {
            // If no cardType specified, toggle all (backward compatibility)
            if (!cardType) {
                const allVisible = Object.values(balanceVisibility).every(visible => visible);
                const newState = !allVisible;
                Object.keys(balanceVisibility).forEach(key => {
                    balanceVisibility[key] = newState;
                });
                updateAllBalanceCards();
            } else {
                // Toggle specific card
                balanceVisibility[cardType] = !balanceVisibility[cardType];
                updateBalanceCard(cardType);
            }
            
            // Save preference to localStorage
            localStorage.setItem('balanceVisibility', JSON.stringify(balanceVisibility));
        }

        function updateBalanceCard(cardType) {
            const card = document.querySelector(`[data-stat="${cardType}"]`);
            if (!card) return;

            const valueElement = card.querySelector('.balance-value');
            const hiddenElement = card.querySelector('.balance-hidden');
            const toggleIcon = card.querySelector('.balance-toggle-icon');
            
            if (valueElement && hiddenElement) {
                if (balanceVisibility[cardType]) {
                    // Show balance
                    valueElement.style.display = 'inline';
                    hiddenElement.style.display = 'none';
                } else {
                    // Hide balance
                    valueElement.style.display = 'none';
                    hiddenElement.style.display = 'inline';
                }
            }
            
            // Update toggle icon
            if (toggleIcon) {
                if (balanceVisibility[cardType]) {
                    toggleIcon.className = 'fas fa-eye';
                    toggleIcon.parentElement.title = 'Hide Balance';
                } else {
                    toggleIcon.className = 'fas fa-eye-slash';
                    toggleIcon.parentElement.title = 'Show Balance';
                }
            }
        }

        function updateAllBalanceCards() {
            Object.keys(balanceVisibility).forEach(cardType => {
                updateBalanceCard(cardType);
            });
        }
        
        // Also assign to window object for global access
        window.toggleBalanceVisibility = toggleBalanceVisibility;
        
        // Load saved balance visibility preference
        function loadBalanceVisibility() {
            const saved = localStorage.getItem('balanceVisibility');
            if (saved) {
                try {
                    const savedVisibility = JSON.parse(saved);
                    balanceVisibility = { ...balanceVisibility, ...savedVisibility };
                } catch (e) {
                    // Use default settings if parsing fails
                }
            }
            
            // Apply saved states with slight delay to ensure DOM is ready
            setTimeout(() => {
                updateAllBalanceCards();
            }, 100);
        }
        
        // Initialize balance visibility when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadBalanceVisibility();
        });
    </script>
    
    <script>
        // High-Traffic Performance Metrics System
        // Prevent redeclaration errors
        if (typeof performanceRefreshInterval === 'undefined') {
            var performanceRefreshInterval;
        }
        if (typeof isHighTrafficMode === 'undefined') {
            var isHighTrafficMode = false;
        }
        if (typeof concurrentUsers === 'undefined') {
            var concurrentUsers = 0;
        }
        if (typeof lastRefreshTime === 'undefined') {
            var lastRefreshTime = Date.now();
        }
        
        // Detect high traffic and adjust refresh intervals
        function detectTrafficLoad() {
            const userAgent = navigator.userAgent;
            const connectionType = navigator.connection ? navigator.connection.effectiveType : '4g';
            
            // Adjust intervals based on load
            if (concurrentUsers > 1000) {
                isHighTrafficMode = true;
                return 60000; // 1 minute for high traffic
            } else if (concurrentUsers > 500) {
                return 45000; // 45 seconds for medium traffic
            } else {
                return 30000; // 30 seconds for normal traffic
            }
        }
        
        // Throttled performance metrics refresh
        function refreshPerformanceMetrics() {
            const now = Date.now();
            const timeSinceLastRefresh = now - lastRefreshTime;
            const minInterval = detectTrafficLoad();
            
            // Skip if too frequent
            if (timeSinceLastRefresh < minInterval) {
                return;
            }
            
            // Use fetch with timeout and error handling
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
            
            fetch('{{ route("dashboard.performance") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                signal: controller.signal,
                cache: 'no-cache'
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Performance metrics response is not JSON');
                }
                
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updatePerformanceDisplay(data.metrics);
                    concurrentUsers = data.metrics.concurrent_users || 0;
                    lastRefreshTime = now;
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                if (error.name === 'AbortError') {
                    console.warn('Performance metrics request timed out');
                } else {
                    console.warn('Performance metrics update failed:', error);
                }
                // Exponential backoff on errors
                setTimeout(() => {
                    lastRefreshTime = now - (minInterval * 0.8); // Allow retry sooner
                }, 10000);
            });
        }
        
        function updatePerformanceDisplay(metrics) {
            // Efficient DOM updates with requestAnimationFrame
            requestAnimationFrame(() => {
                const metricElements = document.querySelectorAll('.metric-item h6');
                if (metricElements.length >= 3) {
                    // Update with performance indicators
                    if (metrics.loading_time !== undefined) {
                        metricElements[0].textContent = metrics.loading_time + 'ms';
                        
                        // Dynamic performance status based on load
                        const parentCard = document.querySelector('.performance-banner');
                        if (parentCard) {
                            parentCard.classList.remove('performance-excellent', 'performance-good', 'performance-poor');
                            
                            // Adjusted thresholds for high traffic
                            const loadThreshold = concurrentUsers > 1000 ? 500 : 300;
                            const excellentThreshold = concurrentUsers > 1000 ? 200 : 100;
                            
                            if (metrics.loading_time < excellentThreshold) {
                                parentCard.classList.add('performance-excellent');
                            } else if (metrics.loading_time < loadThreshold) {
                                parentCard.classList.add('performance-good');
                            } else {
                                parentCard.classList.add('performance-poor');
                            }
                        }
                    }
                    
                    if (metrics.query_count !== undefined) {
                        metricElements[1].textContent = metrics.query_count;
                    }
                    
                    if (metrics.concurrent_users !== undefined) {
                        metricElements[2].textContent = metrics.concurrent_users;
                        
                        // Show warning for high load
                        if (metrics.concurrent_users > 5000) {
                            metricElements[2].style.color = '#dc3545';
                            metricElements[2].title = 'High server load detected';
                        } else if (metrics.concurrent_users > 1000) {
                            metricElements[2].style.color = '#ffc107';
                            metricElements[2].title = 'Medium server load';
                        } else {
                            metricElements[2].style.color = '#ffffff';
                            metricElements[2].title = '';
                        }
                    }
                }
                
                // Update timestamp with cache-busting
                const timestampElements = document.querySelectorAll('.performance-banner small');
                timestampElements.forEach(element => {
                    if (element.innerHTML.includes('fa-sync-alt') && metrics.load_timestamp) {
                        const memoryText = element.innerHTML.split('|')[0];
                        const loadIndicator = isHighTrafficMode ? ' 🔥' : '';
                        element.innerHTML = memoryText + '<span class="mx-2">|</span><i class="fas fa-sync-alt me-1"></i>' + metrics.load_timestamp + loadIndicator;
                    }
                });
                
                // Pulse animation for updates (less frequent in high traffic)
                if (!isHighTrafficMode) {
                    document.querySelectorAll('.metric-item').forEach(item => {
                        item.classList.add('active');
                        setTimeout(() => item.classList.remove('active'), 1000);
                    });
                }
            });
        }
        
        // Intelligent refresh system
        function startPerformanceMonitoring() {
            // Clear any existing interval
            if (performanceRefreshInterval) {
                clearInterval(performanceRefreshInterval);
            }
            
            // Start with immediate refresh
            refreshPerformanceMetrics();
            
            // Dynamic interval based on traffic
            function scheduleNextRefresh() {
                const interval = detectTrafficLoad();
                performanceRefreshInterval = setTimeout(() => {
                    refreshPerformanceMetrics();
                    scheduleNextRefresh(); // Schedule next refresh
                }, interval);
            }
            
            scheduleNextRefresh();
        }
        
        // Page visibility optimization
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Pause monitoring when tab is hidden
                if (performanceRefreshInterval) {
                    clearTimeout(performanceRefreshInterval);
                }
            } else {
                // Resume monitoring when tab becomes visible
                startPerformanceMonitoring();
            }
        });
        
        // Initialize monitoring
        document.addEventListener('DOMContentLoaded', function() {
            // Delay initial load to prevent stampede
            const randomDelay = Math.random() * 5000; // 0-5 second random delay
            setTimeout(() => {
                startPerformanceMonitoring();
            }, randomDelay);
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (performanceRefreshInterval) {
                clearTimeout(performanceRefreshInterval);
            }
        });
    </script>
    
        <script>
            // Dashboard Real-time Update System
            // Prevent redeclaration errors
            if (typeof dashboardUpdateInterval === 'undefined') {
                var dashboardUpdateInterval;
            }
            if (typeof isPageActive === 'undefined') {
                var isPageActive = true;
            }
            if (typeof lastUpdateTime === 'undefined') {
                var lastUpdateTime = null;
            }
            if (typeof performanceChart === 'undefined') {
                var performanceChart = null;
            }

            // Prevent external systems from interfering
            window.dashboardInitialized = false;

            // Page visibility API to pause updates when tab is not active
            document.addEventListener('visibilitychange', function() {
                isPageActive = !document.hidden;
                if (isPageActive) {
                    // Resume checking when tab becomes active
                    updateDashboardData();
                    startDashboardUpdates();
                } else {
                    // Pause checking when tab is inactive
                    stopDashboardUpdates();
                }
            });

            // Start dashboard updates on page load
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    initializePerformanceChart();
                    startDashboardUpdates();
                    updateDashboardData(); // Initial load
                    
                    // Initialize session monitoring
                    checkSessionNotifications(); // Initial check
                    startSessionMonitoring(); // Start monitoring
                    
                    // Mark dashboard as initialized
                    window.dashboardInitialized = true;
                } catch (error) {
                    // Handle initialization errors silently
                }
            });

            function startDashboardUpdates() {
                // Clear any existing interval
                if (dashboardUpdateInterval) {
                    clearInterval(dashboardUpdateInterval);
                }
                
                // Start new interval - update every 30 seconds
                dashboardUpdateInterval = setInterval(function() {
                    if (isPageActive) {
                        updateDashboardData();
                    }
                }, 30000);
            }

            function stopDashboardUpdates() {
                if (dashboardUpdateInterval) {
                    clearInterval(dashboardUpdateInterval);
                    dashboardUpdateInterval = null;
                }
            }

            // Function to update dashboard data
            function updateDashboardData() {
                // Check if CSRF token exists
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    return;
                }
                
                showLoadingIndicators();
                
                fetch('/user/api/dashboard/quick-stats', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Quick stats response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    if (data && data.stats) {
                        updateBalanceCards(data.stats);
                        updateTimestamp();
                        hideLoadingIndicators();
                        showUpdateSuccess();
                    }
                })
                .catch(error => {
                    hideLoadingIndicators();
                    showUpdateError();
                });
            }

            // Function to update balance cards
            function updateBalanceCards(stats) {
                // Update Current Balance
                const currentBalanceElement = document.getElementById('current-balance-amount');
                if (currentBalanceElement && stats.current_balance !== undefined) {
                    animateNumberChange(currentBalanceElement, parseFloat(stats.current_balance), true);
                }

                // Update Team Bonus
                const teamBonusElement = document.getElementById('team-bonus-amount');
                if (teamBonusElement && stats.team_bonus !== undefined) {
                    animateNumberChange(teamBonusElement, parseFloat(stats.team_bonus), true);
                }

                // Update Total Earnings Hub
                const totalEarningsElement = document.getElementById('total-earnings-amount');
                if (totalEarningsElement && stats.interest_wallet !== undefined) {
                    animateNumberChange(totalEarningsElement, parseFloat(stats.interest_wallet), true);
                }

                // Update Video Access Vault - with special attention to zero values
                const videoAccessElement = document.getElementById('video-access-amount');
                if (videoAccessElement && stats.total_investment !== undefined) {
                    const newValue = parseFloat(stats.total_investment);
                    animateNumberChange(videoAccessElement, newValue, true); // Force animation even for $0.00
                }

                // Update Today's Earnings
                const todayEarningsElement = document.querySelector('#today-earnings h4');
                if (todayEarningsElement && stats.today_earnings !== undefined) {
                    animateNumberChange(todayEarningsElement, parseFloat(stats.today_earnings), true);
                }

                // Update Monthly Earnings
                const monthlyEarningsElement = document.querySelector('#monthly-earnings h4');
                if (monthlyEarningsElement && stats.monthly_earnings !== undefined) {
                    animateNumberChange(monthlyEarningsElement, parseFloat(stats.monthly_earnings), true);
                }
            }

            // Function to animate number changes - No animation for zero values
            function animateNumberChange(element, newValue, forceAnimation = false) {
                if (!element) return;
                
                // Check if this element is inside a balance card that's currently hidden
                const parentCard = element.closest('.balance-card');
                if (parentCard) {
                    const cardType = parentCard.getAttribute('data-stat');
                    if (cardType && balanceVisibility && !balanceVisibility[cardType]) {
                        // Update the hidden value without animation, but don't show it
                        const valueElement = parentCard.querySelector('.balance-value');
                        if (valueElement) {
                            valueElement.textContent = '$' + newValue.toFixed(2);
                        }
                        return; // Don't animate hidden balances
                    }
                }
                
                const currentText = element.textContent || element.querySelector('.balance-value')?.textContent || '$0.00';
                const currentValue = parseFloat(currentText.replace(/[$,]/g, '')) || 0;
                const hasChanged = Math.abs(currentValue - newValue) > 0.01;
                
                // Skip animation for zero values - no need to animate $0.00
                if (newValue === 0) {
                    // Just update the value without animation
                    if (element.querySelector('.balance-value')) {
                        element.querySelector('.balance-value').textContent = '$' + newValue.toFixed(2);
                    } else {
                        element.textContent = '$' + newValue.toFixed(2);
                    }
                    return;
                }
                
                // Animate if value changed OR if forced (like on refresh)
                if (hasChanged || forceAnimation) {
                    // Add updating class to parent card for additional styling
                    if (parentCard) {
                        parentCard.classList.add('updating');
                        setTimeout(() => parentCard.classList.remove('updating'), 600);
                    }
                    
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(1.05)';
                    
                    // Color based on value change
                    if (hasChanged) {
                        element.style.color = newValue > currentValue ? '#28a745' : (newValue < currentValue ? '#dc3545' : '#17a2b8');
                    } else {
                        element.style.color = '#17a2b8'; // Blue for refresh without change
                    }
                    
                    setTimeout(() => {
                        // Update the text content
                        if (element.querySelector('.balance-value')) {
                            element.querySelector('.balance-value').textContent = '$' + newValue.toFixed(2);
                        } else {
                            element.textContent = '$' + newValue.toFixed(2);
                        }
                        
                        element.style.transform = 'scale(1)';
                        element.style.boxShadow = '';
                        
                        setTimeout(() => {
                            element.style.color = '';
                        }, 300);
                    }, 150);
                    
                    // Show update indicator for the specific card
                    if (parentCard) {
                        showCardUpdateIndicator(parentCard);
                    }
                } else {
                    // Even if no animation, still update the value
                    if (element.querySelector('.balance-value')) {
                        element.querySelector('.balance-value').textContent = '$' + newValue.toFixed(2);
                    } else {
                        element.textContent = '$' + newValue.toFixed(2);
                    }
                }
            }
            
            // Function to show update indicator for individual cards
            function showCardUpdateIndicator(card) {
                const existingIndicator = card.querySelector('.card-update-indicator');
                if (existingIndicator) {
                    existingIndicator.remove();
                }
                
                const indicator = document.createElement('div');
                indicator.className = 'card-update-indicator';
                indicator.style.cssText = `
                    position: absolute;
                    top: 10px;
                    right: 40px;
                    width: 8px;
                    height: 8px;
                    background: #28a745;
                    border-radius: 50%;
                    animation: pulse 1s ease-in-out;
                    z-index: 10;
                `;
                
                card.style.position = 'relative';
                card.appendChild(indicator);
                
                setTimeout(() => {
                    if (indicator.parentNode) {
                        indicator.remove();
                    }
                }, 2000);
            }

            // Function to show loading indicators
            function showLoadingIndicators() {
                try {
                    document.querySelectorAll('.loading-indicator').forEach(indicator => {
                        if (indicator) {
                            indicator.style.display = 'inline-block';
                        }
                    });
                } catch (error) {
                    // Handle errors silently
                }
            }

            // Function to hide loading indicators
            function hideLoadingIndicators() {
                try {
                    document.querySelectorAll('.loading-indicator').forEach(indicator => {
                        if (indicator) {
                            indicator.style.display = 'none';
                        }
                    });
                } catch (error) {
                    // Handle errors silently
                }
            }

            // Function to update timestamp
            function updateTimestamp() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { 
                    hour12: false, 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
                
                document.querySelectorAll('[id$="-time"]').forEach(timeElement => {
                    timeElement.textContent = timeString;
                });
                
                lastUpdateTime = now;
            }

            // Function to show update success
            function showUpdateSuccess() {
                //showToast('Dashboard updated successfully', 'success');
            }

            // Function to show update error
            function showUpdateError() {
                //showToast('Failed to update dashboard data', 'error');
            }

            // Performance Chart Initialization
            function initializePerformanceChart() {
                const ctx = document.getElementById('performanceChart');
                if (!ctx) return;

                // Check if chartData is available
                const chartLabels = @json(isset($chartData) ? $chartData->pluck('date') : []);
                const chartAmounts = @json(isset($chartData) ? $chartData->pluck('amount') : []);
                
                // If no data available, use sample data
                const fallbackLabels = chartLabels.length > 0 ? chartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
                const fallbackData = chartAmounts.length > 0 ? chartAmounts : [0, 0, 0, 0, 0];

                performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: fallbackLabels,
                        datasets: [{
                            label: 'Daily Earnings',
                            data: fallbackData,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#0d6efd',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#0d6efd',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Earnings: $' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                }
                            }
                        },
                        elements: {
                            point: {
                                hoverRadius: 10
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }

            // Function to refresh performance data
            function refreshPerformanceData() {
                fetch('/user/api/dashboard/performance-metrics', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Performance metrics response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    if (performanceChart && data.data.daily_earnings) {
                        performanceChart.data.labels = data.data.daily_earnings.map(item => item.date);
                        performanceChart.data.datasets[0].data = data.data.daily_earnings.map(item => item.amount);
                        performanceChart.update();
                        showToast('Performance chart updated', 'success');
                    }
                })
                .catch(error => {
                    showToast('Failed to refresh performance data', 'error');
                });
            }

            // Function to update chart period
            function updateChartPeriod(period) {
                // This would typically make an API call with the period parameter
                showToast(`Switching to ${period} view`, 'info');
                refreshPerformanceData();
            }

            // Auto-refresh session notifications every 10 seconds
            if (typeof sessionCheckInterval === 'undefined') {
                var sessionCheckInterval;
            }

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

            function startSessionMonitoring() {
                // Clear any existing interval
                if (sessionCheckInterval) {
                    clearInterval(sessionCheckInterval);
                }
                
                // Start new interval - check every 10 seconds
                sessionCheckInterval = setInterval(function() {
                    if (isPageActive) {
                        checkSessionNotifications();
                    }
                }, 10000);
            }

            function stopSessionMonitoring() {
                if (sessionCheckInterval) {
                    clearInterval(sessionCheckInterval);
                    sessionCheckInterval = null;
                }
            }

            // Function to check for session notifications
            function checkSessionNotifications() {
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
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    updateSessionNotificationsArea(data);
                })
                .catch(error => {
                    console.warn('Notification update error:', error);
                    
                    if (error.message && error.message.includes('401') || error.message.includes('403')) {
                        const timestamp = Math.floor(Date.now() / 1000);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Session Expired',
                            text: 'Your session has expired. Please log in again.',
                            confirmButtonText: 'Go to Login',
                            confirmButtonColor: '#007bff',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.location.href = `/login?from_logout=1&t=${timestamp}`;
                        });
                    } else if (error.message && error.message.includes('Response is not JSON')) {
                        // Server returned HTML/text instead of JSON (possibly error page)
                        console.warn('Server returned non-JSON response for notifications - retrying in 30 seconds');
                        setTimeout(() => {
                            checkSessionNotifications();
                        }, 30000);
                    }
                    // For other errors, fail silently to avoid spam
                });
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Session notifications response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    displayNotificationsModal(data.notifications);
                })
                .catch(error => {
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
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            
                            // Check if response is actually JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Mark read response is not JSON');
                            }
                            
                            return response.json();
                        })
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
            window.shareReferralLink = function() {
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
            window.fallbackShare = function(link) {
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
            window.copyFromModal = function() {
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

            // Prevent external systems from overriding dashboard functions
            Object.freeze(window.toggleBalanceVisibility);
            Object.freeze(window.showSessionNotifications);
            Object.freeze(window.markNotificationsAsRead);
            Object.freeze(window.shareReferralLink);
            Object.freeze(window.fallbackShare);
            Object.freeze(window.copyFromModal);
        </script>
    @endpush
</x-smart_layout>