<x-smart_layout>
    @section('title', $pageTitle)
    @section('content')
        <style>
            /* COMPREHENSIVE THEME TEXT SYSTEM - 100% VISIBILITY GUARANTEE */
            
            /* Base theme classes - Black text for light theme */
            .theme-text {
                color: #000 !important;
                font-weight: 500 !important;
            }
            .theme-text-header {
                color: #000 !important;
                font-weight: 700 !important;
            }
            .theme-text-content {
                color: #000 !important;
                font-weight: 600 !important;
            }
            .theme-text-muted {
                color: #444 !important;
                font-weight: 500 !important;
            }
            
            /* Balance card specific text - FORCE WHITE on gradient backgrounds */
            .balance-card-text,
            .balance-card .card-body,
            .balance-card .card-body * {
                color: #ffffff !important;
                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            }
            .balance-card-text-header,
            .balance-card h6,
            .balance-card .card-title {
                color: #ffffff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            }
            .balance-card-text-amount,
            .balance-card h3,
            .balance-card .balance-value {
                color: #ffffff !important;
                font-weight: 700 !important;
                text-shadow: 0 2px 4px rgba(0,0,0,0.4);
            }
            .balance-card-text-small,
            .balance-card small {
                color: rgba(255,255,255,0.9) !important;
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            }
            
            /* FORCE all text in balance cards to be white */
            .balance-card,
            .balance-card *,
            .balance-card .btn,
            .balance-card .fa,
            .balance-card .fas,
            .balance-card i {
                color: #ffffff !important;
            }
            
            /* Override for circle icons - force dark color */
            .balance-card .bg-white .text-dark,
            .balance-card .bg-opacity-30 .text-dark,
            .balance-card .rounded-circle .text-dark,
            .welcome-banner .bg-white .text-dark,
            .welcome-banner .bg-opacity-30 .text-dark,
            .welcome-banner .rounded-circle .text-dark,
            .bg-white.bg-opacity-30 .text-dark,
            .rounded-circle .text-dark {
                color: #000000 !important;
            }
            
            /* Theme-adaptive table headers */
            .theme-table-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
                font-weight: 700 !important;
            }
            
            /* DARK THEME OVERRIDES - White text for dark theme */
            [data-theme="dark"] .theme-text,
            .dark-theme .theme-text,
            body.dark .theme-text,
            html[data-theme-mode="dark"] .theme-text {
                color: #ffffff !important;
            }
            [data-theme="dark"] .theme-text-header,
            .dark-theme .theme-text-header,
            body.dark .theme-text-header,
            html[data-theme-mode="dark"] .theme-text-header {
                color: #ffffff !important;
                font-weight: 700 !important;
            }
            [data-theme="dark"] .theme-text-content,
            .dark-theme .theme-text-content,
            body.dark .theme-text-content,
            html[data-theme-mode="dark"] .theme-text-content {
                color: #ffffff !important;
                font-weight: 600 !important;
            }
            [data-theme="dark"] .theme-text-muted,
            .dark-theme .theme-text-muted,
            body.dark .theme-text-muted,
            html[data-theme-mode="dark"] .theme-text-muted {
                color: #cccccc !important;
            }
            
            /* Dark theme table headers - White background for dark theme */
            [data-theme="dark"] .theme-table-header,
            .dark-theme .theme-table-header,
            body.dark .theme-table-header,
            html[data-theme-mode="dark"] .theme-table-header {
                background-color: #ffffff !important;
                color: #000 !important;
                font-weight: 700 !important;
            }
            
            /* ABSOLUTE WHITE TEXT ENFORCEMENT - No theme dependence */
            .force-white-text,
            .force-white-text *,
            .welcome-banner,
            .welcome-banner *,
            .earnings-banner,
            .earnings-banner *,
            .gradient-card .text-white,
            .gradient-card .text-white * {
                color: #ffffff !important;
                text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            }
            
            /* Balance cards maintain white text in all themes (on gradient backgrounds) */
            [data-theme="dark"] .balance-card-text,
            .dark-theme .balance-card-text,
            body.dark .balance-card-text,
            html[data-theme-mode="dark"] .balance-card-text {
                color: #ffffff !important;
            }
            
            /* Global text color enforcement for better coverage */
            [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, 
            [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6,
            .dark-theme h1, .dark-theme h2, .dark-theme h3, 
            .dark-theme h4, .dark-theme h5, .dark-theme h6,
            body.dark h1, body.dark h2, body.dark h3, 
            body.dark h4, body.dark h5, body.dark h6,
            html[data-theme-mode="dark"] h1, html[data-theme-mode="dark"] h2, html[data-theme-mode="dark"] h3,
            html[data-theme-mode="dark"] h4, html[data-theme-mode="dark"] h5, html[data-theme-mode="dark"] h6 {
                color: #ffffff !important;
            }
            
            [data-theme="dark"] p, [data-theme="dark"] span, [data-theme="dark"] div:not(.balance-card):not(.card-gradient),
            .dark-theme p, .dark-theme span, .dark-theme div:not(.balance-card):not(.card-gradient),
            body.dark p, body.dark span, body.dark div:not(.balance-card):not(.card-gradient),
            html[data-theme-mode="dark"] p, html[data-theme-mode="dark"] span, html[data-theme-mode="dark"] div:not(.balance-card):not(.card-gradient) {
                color: #ffffff !important;
            }
        </style>
        <!-- Main Statistics Cards -->
        <div class="row mb-4 my-4">
            <!-- New Dashing Welcome Banner -->
            <div class="col-12">
                <div class="card border-0 shadow-lg welcome-banner force-white-text" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-0 force-white-text">
                        <!-- Desktop Version -->
                        <div class="row align-items-center g-0 d-none d-lg-flex">
                            <div class="col-lg-8 p-5">
                                <div class="text-white force-white-text">
                                    <h2 class="mb-3 fw-bold text-white force-white-text" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-weight: 800;">
                                        <i class="fas fa-rocket me-3 text-white force-white-text" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></i>Welcome to Your Earnings Dashboard!
                                    </h2>
                                    <p class="lead mb-4 text-white force-white-text" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); opacity: 1; font-weight: 600;">
                                        Track your investments, manage your portfolio, and watch your earnings grow with our advanced platform.
                                    </p>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white bg-opacity-30 rounded-circle p-2 me-3">
                                                    <i class="fas fa-chart-line text-dark"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-white mb-0 fw-bold" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-weight: 700;">Smart Investing</h6>
                                                    <small class="text-white-50" style="color: rgba(255,255,255,0.9) !important; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">AI-powered strategies</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white bg-opacity-30 rounded-circle p-2 me-3">
                                                    <i class="fas fa-shield-alt text-dark"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-white mb-0 fw-bold" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-weight: 700;">Secure Platform</h6>
                                                    <small class="text-white-50" style="color: rgba(255,255,255,0.9) !important; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">Bank-level security</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white bg-opacity-30 rounded-circle p-2 me-3">
                                                    <i class="fas fa-clock text-dark"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-white mb-0 fw-bold" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-weight: 700;">24/7 Access</h6>
                                                    <small class="text-white-50" style="color: rgba(255,255,255,0.9) !important; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">Always available</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 p-4 text-center">
                                <div class="text-white">
                                    <div class="mb-4">
                                        <i class="fas fa-wallet fa-4x mb-3" style="opacity: 0.8; color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></i>
                                        <h5 class="fw-bold" style="color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-weight: 700;">Ready to Start?</h5>
                                    </div>
                                    <div class="d-grid gap-3">
                                        <a href="{{ route('deposit.index') }}" class="btn btn-light btn-lg fw-bold shadow-sm" style="color: #000000 !important;">
                                            <i class="fas fa-plus-circle me-2" style="color: #000000 !important;"></i>Add Funds
                                        </a>
                                        <a href="{{ route('invest.index') }}" class="btn btn-outline-light btn-lg fw-bold">
                                            <i class="fas fa-chart-line me-2"></i>Start Investing
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Version - Simple Banner -->
                        <div class="d-block d-lg-none p-4 text-center text-white">
                            <div class="mb-3">
                                <i class="fas fa-rocket fa-2x mb-2" style="opacity: 0.9; color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></i>
                                <h4 class="fw-bold mb-2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8); color: #ffffff !important; font-weight: 800;">Welcome Back!</h4>
                                <p class="mb-3" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8); opacity: 1; color: #ffffff !important; font-weight: 600;">
                                    Track your earnings and grow your portfolio
                                </p>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('deposit.index') }}" class="btn btn-outline-light btn-lg fw-bold">
                                    <i class="fas fa-plus-circle me-2"></i>Add Funds
                                </a>
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-light fw-bold">
                                    <i class="fas fa-chart-line me-2"></i>Start Investing
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm balance-card" data-stat="current-balance" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="balance-card-text-header mb-0">💙 Account Wallet</h6>
                                </div>
                                <h3 class="mb-0 balance-card-text-amount d-flex align-items-center" id="current-balance-amount">
                                    <span class="balance-value" data-realtime-update="dashboard-total-balance">${{ showAmount($currentBalance) }}</span>
                                    <span class="balance-hidden" style="display: none;">••••••••</span>
                                    <button class="btn btn-sm btn-link balance-card-text ms-2 p-0 balance-quick-toggle" onclick="toggleBalanceVisibility('current-balance')" style="font-size: 0.8rem; opacity: 0.7;">
                                        <i class="fas fa-eye balance-quick-icon"></i>
                                    </button>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="balance-card-text-small">Updated: <span id="current-balance-time">{{ now()->format('H:i') }}</span></small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin balance-card-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-30 rounded-circle p-3">
                                <i class="fas fa-wallet fa-lg text-dark"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar" role="progressbar" style="width: 100%; background: white;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm balance-card" data-stat="team-bonus" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="balance-card-text-header mb-0">💛 Team Bonus</h6>
                                </div>
                                <h3 class="mb-0 balance-card-text-amount d-flex align-items-center" id="team-bonus-amount">
                                    <span class="balance-value">${{ showAmount($referral_earnings) }}</span>
                                    <span class="balance-hidden" style="display: none;">••••••••</span>
                                    <button class="btn btn-sm btn-link balance-card-text ms-2 p-0 balance-quick-toggle" onclick="toggleBalanceVisibility('team-bonus')" style="font-size: 0.8rem; opacity: 0.7;">
                                        <i class="fas fa-eye balance-quick-icon"></i>
                                    </button>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="balance-card-text-small">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        Monthly: ${{ showAmount($monthly_referral_earnings ?? 0) }}
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin balance-card-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-30 rounded-circle p-3">
                                <i class="fas fa-users fa-lg text-dark"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar" role="progressbar" style="width: {{ $total_referrals ? min(100, ($total_referrals / 10) * 100) : 0 }}%; background: white;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm balance-card" data-stat="total-earnings" style="background: linear-gradient(135deg, #0f7b0f 0%, #155d27 50%, #1e7e34 100%); border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="balance-card-text-header mb-0">💰 Total Earnings Hub</h6>
                                </div>
                                <h3 class="mb-0 balance-card-text-amount d-flex align-items-center" id="total-earnings-amount">
                                    <span class="balance-value" data-realtime-update="dashboard-interest-balance">${{ showAmount(auth()->user()->interest_wallet) }}</span>
                                    <span class="balance-hidden" style="display: none;">••••••••</span>
                                    <button class="btn btn-sm btn-link balance-card-text ms-2 p-0 balance-quick-toggle" onclick="toggleBalanceVisibility('total-earnings')" style="font-size: 0.8rem; opacity: 0.7;">
                                        <i class="fas fa-eye balance-quick-icon"></i>
                                    </button>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="balance-card-text-small">
                                        <i class="fas fa-coins me-1"></i>
                                        All Income: {{ $growth_percentage ?? 0 }}%
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin balance-card-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-30 rounded-circle p-3">
                                <i class="fas fa-coins fa-lg text-dark"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar" role="progressbar" style="width: {{ min(100, ($interests ?? 0) / max(1, $totalInvest ?? 1) * 100) }}%; background: white;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm balance-card" data-stat="video-access-vault" style="background: linear-gradient(135deg, #6f2c91 0%, #9b339b 50%, #b5179e 100%); border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="balance-card-text-header mb-0">💎 Video Access Vault</h6>
                                </div>
                                <h3 class="mb-0 balance-card-text-amount d-flex align-items-center" id="video-access-amount">
                                    <span class="balance-value" data-realtime-update="dashboard-deposit-balance">${{ showAmount($totalInvest) }}</span>
                                    <span class="balance-hidden" style="display: none;">••••••••</span>
                                    <button class="btn btn-sm btn-link balance-card-text ms-2 p-0 balance-quick-toggle" onclick="toggleBalanceVisibility('video-access-vault')" style="font-size: 0.8rem; opacity: 0.7;">
                                        <i class="fas fa-eye balance-quick-icon"></i>
                                    </button>
                                </h3>
                                <div class="d-flex align-items-center mt-1">
                                    <small class="balance-card-text-small">
                                        <i class="fas fa-play-circle me-1"></i>
                                        Active Plans: ${{ showAmount($runningInvests ?? 0) }}
                                    </small>
                                    <div class="loading-indicator ms-2" style="display: none;">
                                        <i class="fas fa-spinner fa-spin balance-card-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-30 rounded-circle p-3">
                                <i class="fas fa-video fa-lg text-dark"></i>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar" role="progressbar" style="width: {{ $totalInvest ? min(100, ($runningInvests ?? 0) / max(1, $totalInvest) * 100) : 0 }}%; background: white;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lottery System Highlight -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%); border-radius: 15px;">
                    <div class="card-body text-white p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-30 rounded-circle p-3 me-3">
                                        <i class="fas fa-ticket-alt fa-2x text-dark"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-white mb-1" style="text-shadow: 0 3px 6px rgba(0,0,0,0.9), 0 1px 3px rgba(0,0,0,0.8); font-weight: 800; color: #ffffff !important;">🎰 Try Your Luck with Our Lottery!</h3>
                                        <p class="text-white mb-0" style="text-shadow: 0 2px 4px rgba(0,0,0,0.9), 0 1px 2px rgba(0,0,0,0.8); color: #ffffff !important; font-weight: 500;">Buy lottery tickets and win amazing prizes. Weekly draws with guaranteed winners!</p>
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
                                            <h4 class="text-white mb-0" style="text-shadow: 0 3px 6px rgba(0,0,0,0.9), 0 1px 3px rgba(0,0,0,0.8); font-weight: 800; color: #ffffff !important;">${{ $settings ? number_format($settings->ticket_price, 2) : '2.00' }}</h4>
                                            <small class="text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.9); color: #ffffff !important; font-weight: 600;">Per Ticket</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-2">
                                        <div class="text-center">
                                            <h4 class="text-white mb-0" style="text-shadow: 0 3px 6px rgba(0,0,0,0.9), 0 1px 3px rgba(0,0,0,0.8); font-weight: 800; color: #ffffff !important;">{{ $userTickets }}</h4>
                                            <small class="text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.9); color: #ffffff !important; font-weight: 600;">Your Tickets</small>
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
                                            <h4 class="text-white mb-0" style="text-shadow: 0 3px 6px rgba(0,0,0,0.9), 0 1px 3px rgba(0,0,0,0.8); font-weight: 800; color: #ffffff !important;">${{ number_format($actualPrizePool, 2) }}</h4>
                                            <small class="text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.9); color: #ffffff !important; font-weight: 600;">Prize Pool</small>
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
                                    <a href="{{ route('lottery.my.tickets') }}" class="btn btn-light btn-sm fw-bold shadow">
                                        <i class="fas fa-list me-2"></i>My Tickets
                                    </a>
                                    <a href="{{ route('lottery.results') }}" class="btn btn-light btn-sm fw-bold shadow">
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
                        <h5 class="card-title mb-0 theme-text-header">
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
                                            <h6 class="mb-1 text-primary" style="font-weight: 700; color: #0056b3 !important;">
                                                <i class="fas fa-play-circle me-1"></i>
                                                Video Earnings Dashboard
                                            </h6>
                                            <p class="mb-0 small theme-text-muted">
                                                📺 Track your daily video progress • 💰 Monitor earnings • 📊 View statistics
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
                                                <h6 class="mb-1 theme-text-header"><i class="fas fa-film text-primary"></i> Your Plan: <span class="text-primary" style="font-weight: 700;">{{ $activePlan->name }}</span></h6>
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
                                                <small class="d-block text-center mt-1 theme-text-content">
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
                                    <h4 class="mb-1 fw-bold" style="color: #dc3545; font-weight: 800;">
                                        {{ $totalVideosWatched ?? 0 }}
                                    </h4>
                                    <p class="mb-0 small theme-text-content">Videos Watched</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-dollar-sign fa-2x" style="color: #28a745;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #28a745; font-weight: 800;">
                                        ${{ showAmount($videoEarnings ?? 0) }}
                                    </h4>
                                    <p class="mb-0 small theme-text-content">Video Earnings</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-day fa-2x" style="color: #17a2b8;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #17a2b8; font-weight: 800;">
                                        {{ $todayViews }}
                                        @if($dailyLimit > 0)
                                            <small class="theme-text-muted">/{{ $dailyLimit }}</small>
                                        @endif
                                    </h4>
                                    <p class="mb-0 small theme-text-content">Today's Views</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="text-center p-3 border rounded bg-light">
                                    <div class="mb-2">
                                        <i class="fas fa-clock fa-2x" style="color: #6f42c1;"></i>
                                    </div>
                                    <h4 class="mb-1 fw-bold" style="color: #6f42c1; font-weight: 800;">
                                        ${{ number_format($todayEarnings ?? 0, 4) }}
                                    </h4>
                                    <p class="mb-0 small theme-text-content">Today's Earnings</p>
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
                        <h5 class="card-title mb-0 theme-text-header">Investment Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #198754; font-weight: 800;">${{ showAmount($runningInvests) }}</h4>
                                    <p class="mb-0 small theme-text-content">Running Investments</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #6c757d; font-weight: 800;">${{ showAmount($completedInvests) }}</h4>
                                    <p class="mb-0 small theme-text-content">Completed Investments</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1 fw-bold" style="color: #0d6efd; font-weight: 800;">${{ showAmount($interests) }}</h4>
                                    <p class="mb-0 small theme-text-content">Total Interest Earned</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card custom-card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0 theme-text-header">Transaction Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #dc3545; font-weight: 800;">${{ showAmount($balance_transfer) }}</h4>
                                    <p class="mb-0 small theme-text-content">Total Transferred</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3">
                                    <h4 class="mb-1 fw-bold" style="color: #198754; font-weight: 800;">${{ showAmount($balance_received) }}</h4>
                                    <p class="mb-0 small theme-text-content">Total Received</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1 fw-bold" style="color: #0dcaf0; font-weight: 800;">${{ showAmount($referral_earnings) }}</h4>
                                    <p class="mb-0 small theme-text-content">Referral Earnings</p>
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
                        <h5 class="card-title mb-0 theme-text-header">Quick Actions</h5>
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
                        <h5 class="card-title mb-0 theme-text-header">
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
                                    <h6 class="mb-2 theme-text-header">Your Referral Link:</h6>
                                    <div class="input-group">
                                        <input type="text" id="referralLink" class="form-control bg-light" 
                                               value="{{ auth()->user()->getReferralLink() }}" 
                                               readonly>
                                        <button class="btn btn-outline-primary" type="button" id="copyReferralLink">
                                            <i class="fas fa-copy me-1"></i>Copy
                                        </button>
                                    </div>
                                    <small class="theme-text-content">Share this link with your friends to earn referral commissions</small>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="mb-1 fw-bold" style="color: #0d6efd;">
                                                {{ auth()->user()->referrals()->count() }}
                                            </h4>
                                            <p class="mb-0 small theme-text-content">Total Referrals</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="mb-1 fw-bold" style="color: #198754;">
                                                ${{ showAmount($referral_earnings) }}
                                            </h4>
                                            <p class="mb-0 small theme-text-content">Total Referral Earnings</p>
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
                        <h5 class="card-title mb-0 theme-text-header">Recent Transactions</h5>
                        <a href="{{ route('user.transfer_history') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($transactions && count($transactions) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="theme-table-header">
                                        <tr>
                                            <th class="theme-table-header">Transaction ID</th>
                                            <th class="theme-table-header">Type</th>
                                            <th class="theme-table-header">Amount</th>
                                            <th class="theme-table-header">Status</th>
                                            <th class="theme-table-header">Date</th>
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
                                            <td class="theme-text-content">{{ $transaction->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <h5 class="theme-text-header">No Recent Transactions</h5>
                                <p class="theme-text-content">Your transaction history will appear here.</p>
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
            
            /* Simplified animations for better performance */
            .metric-item {
                padding: 8px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .metric-item:hover {
                background: rgba(255, 255, 255, 0.2);
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
            
            /* Simplified animations for better performance - removed heavy keyframes */
            
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
                /* Simplified card styling for better performance */
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

            /* Simplified balance cards for better performance */
            .balance-card {
                overflow: hidden;
                position: relative;
            }
            
            .balance-hidden {
                font-size: 1.5rem;
                letter-spacing: 3px;
                color: white !important;
                font-weight: bold;
            }
            
            .balance-amount {
                transition: all 0.3s ease;
            }
            
            /* Simplified balance toggle styles for better performance */
            .balance-quick-toggle {
                border: none !important;
                background: none !important;
                text-decoration: none !important;
            }
            
            /* Optimized styles - removed heavy animations for better performance */
        </style>
        
        <script>
            // Enhanced Balance Visibility Toggle Function
            function toggleBalanceVisibility(cardId) {
                const card = document.querySelector(`[data-stat="${cardId}"]`);
                if (!card) return;
                
                const valueSpan = card.querySelector('.balance-value');
                const hiddenSpan = card.querySelector('.balance-hidden');
                const quickToggleIcon = card.querySelector('.balance-quick-icon');
                
                if (!valueSpan || !hiddenSpan) return;
                
                // Determine current state
                const isHidden = valueSpan.style.display === 'none';
                
                if (isHidden) {
                    // Show balance
                    valueSpan.style.display = 'inline';
                    hiddenSpan.style.display = 'none';
                    
                    // Update close toggle icon
                    if (quickToggleIcon) {
                        quickToggleIcon.className = 'fas fa-eye balance-quick-icon';
                        quickToggleIcon.style.transform = 'scale(1.3)';
                        setTimeout(() => quickToggleIcon.style.transform = 'scale(1)', 200);
                    }
                    
                    // Store preference
                    localStorage.setItem(`balance-visible-${cardId}`, 'true');
                    
                    // Update tooltip
                    const quickToggleBtn = card.querySelector('.balance-quick-toggle');
                    if (quickToggleBtn) quickToggleBtn.title = 'Hide Balance';
                    
                } else {
                    // Hide balance
                    valueSpan.style.display = 'none';
                    hiddenSpan.style.display = 'inline';
                    
                    // Update close toggle icon
                    if (quickToggleIcon) {
                        quickToggleIcon.className = 'fas fa-eye-slash balance-quick-icon';
                        quickToggleIcon.style.transform = 'scale(1.3)';
                        setTimeout(() => quickToggleIcon.style.transform = 'scale(1)', 200);
                    }
                    
                    // Store preference
                    localStorage.setItem(`balance-visible-${cardId}`, 'false');
                    
                    // Update tooltip
                    const quickToggleBtn = card.querySelector('.balance-quick-toggle');
                    if (quickToggleBtn) quickToggleBtn.title = 'Show Balance';
                }
            }
            
            // Load balance visibility preferences on page load
            document.addEventListener('DOMContentLoaded', function() {
                const balanceCards = document.querySelectorAll('.balance-card');
                
                balanceCards.forEach(card => {
                    const cardId = card.getAttribute('data-stat');
                    const isVisible = localStorage.getItem(`balance-visible-${cardId}`);
                    
                    if (isVisible === 'false') {
                        const valueSpan = card.querySelector('.balance-value');
                        const hiddenSpan = card.querySelector('.balance-hidden');
                        const quickToggleIcon = card.querySelector('.balance-quick-icon');
                        const quickToggleBtn = card.querySelector('.balance-quick-toggle');
                        
                        if (valueSpan && hiddenSpan) {
                            valueSpan.style.display = 'none';
                            hiddenSpan.style.display = 'inline';
                            
                            if (quickToggleIcon) quickToggleIcon.className = 'fas fa-eye-slash balance-quick-icon';
                            if (quickToggleBtn) quickToggleBtn.title = 'Show Balance';
                        }
                    }
                });
                
                // Add hover effects to balance amounts
                const balanceAmounts = document.querySelectorAll('.balance-amount');
                balanceAmounts.forEach(amount => {
                    amount.addEventListener('mouseenter', function() {
                        const quickToggle = this.querySelector('.balance-quick-toggle');
                        if (quickToggle) {
                            quickToggle.style.opacity = '1';
                        }
                    });
                    
                    amount.addEventListener('mouseleave', function() {
                        const quickToggle = this.querySelector('.balance-quick-toggle');
                        if (quickToggle) {
                            quickToggle.style.opacity = '0.7';
                        }
                    });
                });
                
                // Copy referral link functionality
                const copyButton = document.getElementById('copyReferralLink');
                if (copyButton) {
                    copyButton.addEventListener('click', function() {
                        const referralInput = document.getElementById('referralLink');
                        if (referralInput) {
                            referralInput.select();
                            referralInput.setSelectionRange(0, 99999);
                            
                            try {
                                document.execCommand('copy');
                                this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-success');
                                
                                setTimeout(() => {
                                    this.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                                    this.classList.remove('btn-success');
                                    this.classList.add('btn-outline-primary');
                                }, 2000);
                            } catch (err) {
                                console.error('Copy failed:', err);
                                // Fallback for modern browsers
                                if (navigator.clipboard) {
                                    navigator.clipboard.writeText(referralInput.value).then(() => {
                                        this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                                        this.classList.remove('btn-outline-primary');
                                        this.classList.add('btn-success');
                                        
                                        setTimeout(() => {
                                            this.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                                            this.classList.remove('btn-success');
                                            this.classList.add('btn-outline-primary');
                                        }, 2000);
                                    });
                                }
                            }
                        }
                    });
                }
            });
            
            // Share referral link function
            function shareReferralLink() {
                const referralInput = document.getElementById('referralLink');
                if (!referralInput) return;
                
                const referralLink = referralInput.value;
                
                if (navigator.share) {
                    navigator.share({
                        title: 'Join our platform',
                        text: 'Join me on this amazing platform and start earning!',
                        url: referralLink
                    }).catch(err => console.log('Share failed:', err));
                } else {
                    // Fallback: copy to clipboard
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(referralLink).then(() => {
                            alert('Referral link copied to clipboard!');
                        }).catch(() => {
                            // Final fallback
                            referralInput.select();
                            document.execCommand('copy');
                            alert('Referral link copied to clipboard!');
                        });
                    } else {
                        // Very old browser fallback
                        referralInput.select();
                        document.execCommand('copy');
                        alert('Referral link copied to clipboard!');
                    }
                }
            }
        </script>
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
            
            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem;
                }
                
                .h3 {
                    font-size: 1.5rem;
                }
                
                .btn-sm {
                    font-size: 0.8rem;
                    padding: 0.2rem 0.4rem;
                }
            }
        </style>
    @endpush

    @push('script')
    
    <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Chart.js removed to improve page loading speed -->
    
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
    
    <!-- Performance monitoring script removed to improve page loading speed -->
    
        <script>
            // Simplified dashboard initialization - heavy real-time updates removed for better performance
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize basic balance toggle functionality
                loadBalanceVisibility();
                
                // Copy referral link functionality
                const copyButton = document.getElementById('copyReferralLink');
                if (copyButton) {
                    copyButton.addEventListener('click', function() {
                        const referralInput = document.getElementById('referralLink');
                        if (referralInput) {
                            referralInput.select();
                            referralInput.setSelectionRange(0, 99999); // For mobile devices
                            
                            // Use modern clipboard API if available
                            if (navigator.clipboard) {
                                navigator.clipboard.writeText(referralInput.value).then(function() {
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
                            
                            this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                            this.classList.add('btn-success');
                            setTimeout(() => {
                                this.innerHTML = '<i class="fas fa-copy me-1"></i>Copy';
                                this.classList.remove('btn-success');
                                this.classList.add('btn-outline-primary');
                            }, 2000);
                        }
                    });
                }
            });

            // Simple share referral link function - lightweight version
            window.shareReferralLink = function() {
                const referralInput = document.getElementById('referralLink');
                if (referralInput) {
                    // Copy to clipboard
                    referralInput.select();
                    referralInput.setSelectionRange(0, 99999);
                    
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(referralInput.value).then(function() {
                            alert('Referral link copied to clipboard!');
                        }).catch(function(err) {
                            document.execCommand('copy');
                            alert('Referral link copied to clipboard!');
                        });
                    } else {
                        document.execCommand('copy');
                        alert('Referral link copied to clipboard!');
                    }
                }
            }
        </script>
    @endpush

@push('script')
    
    <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Chart.js removed to improve page loading speed -->
    
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
    @endpush
</x-smart_layout>