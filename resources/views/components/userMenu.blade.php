@auth
    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
        <div class="alert alert-warning border-0 mb-3" style="margin: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div class="flex-grow-1">
                    <strong>Email Verification Required</strong>
                    <p class="mb-0 small">Please verify your email address to access all features.</p>
                </div>
            </div>
            <div class="mt-2">
                <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-envelope me-1"></i>
                    Verify Email
                </a>
            </div>
        </div>
    @endif 
@endauth 

<ul class="main-menu">
    <!-- 🏠 DASHBOARD -->
    <li class="slide">
        <a href="{{route('user.dashboard')}}" class="side-menu__item">
            <i class="fe fe-home side-menu__icon"></i>
            <span class="side-menu__label">Dashboard</span>
        </a>
    </li>

    <!-- 💳 ADD FUND (DEDICATED SECTION) -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-credit-card side-menu__icon"></i>
            <span class="side-menu__label">💳 Add Fund</span>
            @auth
                @php
                    $totalDeposits = auth()->user()->deposits()->where('status', 1)->sum('amount');
                @endphp
                @if($totalDeposits > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalDeposits, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">💳 Add Fund</a>
            </li>
            
            <!-- Deposit Funds -->
            <li class="slide">
                <a href="{{ route('deposit.index') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2 text-success"></i>
                    <span class="side-menu__label">💳 Deposit Funds</span>
                    <span class="badge badge-success ms-auto">Add Money</span>
                </a>
            </li>
            
            <!-- Deposit History -->
            <li class="slide">
                <a href="{{route('deposit.history')}}" class="side-menu__item">
                    <i class="fe fe-credit-card me-2"></i>
                    <span class="side-menu__label">Deposit History</span>
                    @auth
                        @php
                            $depositCount = auth()->user()->deposits()->where('status', 1)->count();
                        @endphp
                        @if($depositCount > 0)
                            <span class="badge badge-info ms-auto">{{ $depositCount }} deposits</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>
    
    <!-- 🎬 MOST IMPORTANT: VIDEO WATCH (TOP PRIORITY) -->
    <li class="slide">
        <a href="{{ route('user.video-views.gallery') }}" class="side-menu__item">
            <i class="fe fe-play-circle side-menu__icon"></i>
            <span class="side-menu__label">🎬 Watch Videos & Earn</span>
            @auth
                @php
                    $totalVideoEarnings = Auth::user()->videoViews()->sum('earned_amount');
                @endphp
                @if($totalVideoEarnings > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalVideoEarnings, 2) }}</span>
                @else
                    <span class="badge badge-primary ms-2">Start Earning</span>
                @endif
            @endauth
        </a>
    </li>

    <!-- 🎟️ BUY LOTTERY TICKETS (2ND PRIORITY) -->
    <li class="slide">
        <a href="{{ route('lottery.index') }}" class="side-menu__item">
            <i class="fas fa-ticket-alt side-menu__icon"></i>
            <span class="side-menu__label">🎟️ Buy Lottery Tickets</span>
            @auth
                @php
                    $currentDraw = \App\Models\LotteryDraw::where('status', 'pending')->first();
                    $userTicketsCount = $currentDraw ? $currentDraw->tickets()->where('user_id', auth()->id())->count() : 0;
                    $lotterySettings = \App\Models\LotterySetting::getSettings();
                @endphp
                @if($userTicketsCount > 0)
                    <span class="badge badge-success ms-2">{{ $userTicketsCount }} tickets</span>
                @else
                    <span class="badge badge-info ms-2">${{ number_format($lotterySettings->ticket_price ?? 2, 2) }}</span>
                @endif
            @endauth
        </a>
    </li>

    <!-- 💰 ESSENTIAL FINANCIAL OPERATIONS -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-dollar-sign side-menu__icon"></i>
            <span class="side-menu__label">💰 Financial Hub</span>
            @auth
                @php
                    $totalBalance = (auth()->user()->deposit_wallet ?? 0) + (auth()->user()->interest_wallet ?? 0);
                @endphp
                @if($totalBalance > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalBalance, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">💰 Financial Hub</a>
            </li>
            
            <!-- Withdraw (4th Priority) -->
            <li class="slide">
                <a href="{{ route('user.withdraw.wallet') }}" class="side-menu__item">
                    <i class="fe fe-minus-circle me-2 text-warning"></i>
                    <span class="side-menu__label">🏧 Withdraw Wallet</span>
                    @auth
                        @if($totalBalance > 0)
                            <span class="badge badge-warning ms-auto">${{ number_format($totalBalance, 2) }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            
            <!-- Transfer (5th Priority) -->
            <li class="slide">
                <a href="{{route('user.transfer_funds')}}" class="side-menu__item">
                    <i class="fe fe-send me-2 text-info"></i>
                    <span class="side-menu__label">💸 Transfer Funds</span>
                    <span class="badge badge-info ms-auto">Send Money</span>
                </a>
            </li>
            
            <!-- Security Package Operations -->
            <li class="slide">
                <a href="{{ route('user.withdraw') }}" class="side-menu__item">
                    <i class="fe fe-shield me-2 text-danger"></i>
                    <span class="side-menu__label">🛡️ Withdraw Security Package</span>
                    @auth
                        @php
                            $activeDeposit = auth()->user()->invests()->where('status', 1)->first();
                        @endphp
                        @if($activeDeposit)
                            <span class="badge badge-warning ms-auto">20% Fee</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>

    <!-- 🛡️ SECURITY PACKAGE -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">🛡️ Security Package</span>
            @auth
                @php
                    $currentDeposit = auth()->user()->invests()->where('status', 1)->first();
                @endphp
                @if($currentDeposit)
                    <span class="badge badge-success ms-2">{{ $currentDeposit->plan->name ?? 'Active' }}</span>
                @else
                    <span class="badge badge-warning ms-2">No Plan</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🛡️ Security Package</a>
            </li>
            <li class="slide">
                <a href="{{route('invest.index')}}" class="side-menu__item">
                    <i class="fe fe-package me-2"></i>
                    <span class="side-menu__label">Security Plans</span>
                    @auth
                        @if(!$currentDeposit)
                            <span class="badge badge-primary ms-auto">Start Now</span>
                        @else
                            <span class="badge badge-info ms-auto">Upgrade</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>

    <!-- 🎰 LOTTERY COMPLETE SYSTEM -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fas fa-dice side-menu__icon"></i>
            <span class="side-menu__label">🎰 Lottery System</span>
            @auth
                @if($userTicketsCount > 0)
                    <span class="badge badge-success ms-2">{{ $userTicketsCount }} tickets</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🎰 Lottery System</a>
            </li>
            <li class="slide">
                <a href="{{ route('lottery.unified.index') }}" class="side-menu__item">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span class="side-menu__label">Lottery Dashboard</span>
                    <span class="badge badge-primary ms-auto">All-in-One</span>
                </a>
            </li>
            @auth
                <li class="slide">
                    <a href="{{ route('lottery.my.tickets') }}" class="side-menu__item">
                        <i class="fas fa-list me-2"></i>
                        <span class="side-menu__label">My Tickets</span>
                        @if($userTicketsCount > 0)
                            <span class="badge badge-success ms-auto">{{ $userTicketsCount }}</span>
                        @endif
                    </a>
                </li>
            @endauth
            <li class="slide">
                <a href="{{ route('lottery.results') }}" class="side-menu__item">
                    <i class="fas fa-trophy me-2"></i>
                    <span class="side-menu__label">Results & Winners</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- 👥 TEAM & REFERRALS -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-users side-menu__icon"></i>
            <span class="side-menu__label">👥 Team & Referrals</span>
            @auth
                @php
                    $totalCommissions = \App\Models\ReferralCommission::where('referrer_user_id', auth()->id())->sum('commission_amount');
                @endphp
                @if($totalCommissions > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalCommissions, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">👥 Team & Referrals</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.sponsor-list') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">Referral List</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.team-tree')}}" class="side-menu__item">
                    <i class="fe fe-git-branch me-2"></i>
                    <span class="side-menu__label">Team Tree</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.generation-history')}}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Commission History</span>
                    @auth
                        @php
                            $todayCommissions = \App\Models\ReferralCommission::where('referrer_user_id', auth()->id())->whereDate('distributed_at', today())->sum('commission_amount');
                        @endphp
                        @if($todayCommissions > 0)
                            <span class="badge badge-primary ms-auto">Today: ${{ number_format($todayCommissions, 4) }}</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>

    <!-- 👤 PROFILE & ACCOUNT -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-user side-menu__icon"></i>
            <span class="side-menu__label">👤 Profile & Account</span>
            @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
                <span class="badge badge-warning ms-2">Unverified</span>
            @elseif(auth()->check())
                <span class="badge badge-success ms-2">Verified</span>
            @endif
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">👤 Profile & Account</a>
            </li>
            <li class="slide">
                <a href="{{ route('profile.index') }}" class="side-menu__item">
                    <i class="fe fe-user me-2"></i>
                    <span class="side-menu__label">My Profile Dashboard</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('profile.edit') }}" class="side-menu__item">
                    <i class="fe fe-edit me-2"></i>
                    <span class="side-menu__label">Edit Profile</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('profile.password') }}" class="side-menu__item">
                    <i class="fe fe-lock me-2"></i>
                    <span class="side-menu__label">Change Password</span>
                </a>
            </li>
            @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
                <li class="slide">
                    <a href="{{ route('verification.notice') }}" class="side-menu__item">
                        <i class="fe fe-mail me-2"></i>
                        <span class="side-menu__label">Verify Email</span>
                        <span class="badge badge-warning ms-auto">Required</span>
                    </a>
                </li>
            @endif
            <li class="slide">
                <a href="{{ route('profile.security') }}" class="side-menu__item">
                    <i class="fe fe-shield me-2"></i>
                    <span class="side-menu__label">Security</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.requirements') }}" class="side-menu__item">
                    <i class="fe fe-check-circle me-2"></i>
                    <span class="side-menu__label">Account Requirements</span>
                    @php
                        $user = auth()->user();
                        $missingRequirements = 0;
                        
                        // Only check requirements if user is authenticated
                        if ($user) {
                            // Check KYC
                            if ($user->kv != 1) $missingRequirements++;
                            
                            // Check email verification
                            if (!$user->hasVerifiedEmail()) $missingRequirements++;
                            
                            // Check profile completion
                            if (empty($user->firstname) || empty($user->lastname) || empty($user->mobile)) $missingRequirements++;
                            
                            // Check referral requirement
                            $hasActiveReferral = \App\Models\User::where('ref_by', $user->id)
                                ->whereHas('invests', function($query) {
                                    $query->where('status', '!=', 'cancelled');
                                })->exists();
                            if (!$hasActiveReferral) $missingRequirements++;
                        }
                    @endphp
                    @if($missingRequirements > 0)
                        <span class="badge badge-warning ms-auto">{{ $missingRequirements }} pending</span>
                    @else
                        <span class="badge badge-success ms-auto">Complete</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>

    <!-- 🔒 KYC VERIFICATION -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">🔒 KYC Verification</span>
            @auth
                @if(Auth::user()->kv == 0)
                    <span class="badge badge-danger ms-2">Not Verified</span>
                @elseif(Auth::user()->kv == 1)
                    <span class="badge badge-success ms-2">Verified</span>
                @elseif(Auth::user()->kv == 2)
                    <span class="badge badge-warning ms-2">Pending</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🔒 KYC Verification</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.kyc.index') }}" class="side-menu__item">
                    <i class="fe fe-file-text me-2"></i>
                    <span class="side-menu__label">KYC Dashboard</span>
                </a>
            </li>
            @auth
                @if(Auth::user()->kv == 0)
                    <li class="slide">
                        <a href="{{ route('user.kyc.create') }}" class="side-menu__item">
                            <i class="fe fe-upload me-2"></i>
                            <span class="side-menu__label">Submit KYC</span>
                            <span class="badge badge-primary ms-auto">New</span>
                        </a>
                    </li>
                @endif
            @endauth
            <li class="slide">
                <a href="{{ route('user.kyc.status') }}" class="side-menu__item">
                    <i class="fe fe-info me-2"></i>
                    <span class="side-menu__label">KYC Status</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- 💬 COMMUNICATION -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-message-circle side-menu__icon"></i>
            <span class="side-menu__label">💬 Communication</span>
            @auth
                @php
                    $unreadNotifications = \App\Models\UserNotification::where('user_id', auth()->id())->unread()->notExpired()->count();
                    $unreadMessages = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', false)->count();
                    $totalUnread = $unreadNotifications + $unreadMessages;
                @endphp
                @if($totalUnread > 0)
                    <span class="badge badge-danger ms-2">{{ $totalUnread > 99 ? '99+' : $totalUnread }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">💬 Communication</a>
            </li>
            <!-- Notifications -->
            <li class="slide">
                <a href="{{ route('user.notifications.index') }}" class="side-menu__item">
                    <i class="fe fe-bell me-2"></i>
                    <span class="side-menu__label">Notifications</span>
                    @auth
                        @if($unreadNotifications > 0)
                            <span class="badge badge-danger ms-auto">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <!-- Messages -->
            <li class="slide">
                <a href="{{ route('user.messages') }}" class="side-menu__item">
                    <i class="fe fe-mail me-2"></i>
                    <span class="side-menu__label">Messages</span>
                    @auth
                        @if($unreadMessages > 0)
                            <span class="badge badge-danger ms-auto">{{ $unreadMessages }} new</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>

    <!-- 📊 CONSOLIDATED REPORTS -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-bar-chart-2 side-menu__icon"></i>
            <span class="side-menu__label">📊 Reports & History</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">📊 Reports & History</a>
            </li>
            
            <!-- Financial Reports -->
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fe fe-dollar-sign me-2"></i>
                    <span class="side-menu__label">Financial Reports</span>
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i>
                </a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('user.withdraw.history') }}" class="side-menu__item">
                            <i class="fe fe-arrow-up me-1"></i>
                            <span class="side-menu__label">Withdrawal History</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('user.transfer_history') }}" class="side-menu__item">
                            <i class="fe fe-send me-1"></i>
                            <span class="side-menu__label">Transfer History</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('invest.history')}}" class="side-menu__item">
                            <i class="fe fe-shield me-1"></i>
                            <span class="side-menu__label">Investment History</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Video & Earnings -->
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fe fe-play-circle me-2"></i>
                    <span class="side-menu__label">Video & Earning Reports</span>
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i>
                </a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('user.video-views.history') }}" class="side-menu__item">
                            <i class="fe fe-clock me-1"></i>
                            <span class="side-menu__label">Viewing History</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('user.video-views.earnings') }}" class="side-menu__item">
                            <i class="fe fe-dollar-sign me-1"></i>
                            <span class="side-menu__label">Video Earnings</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Lottery Reports -->
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fas fa-ticket-alt me-2"></i>
                    <span class="side-menu__label">Lottery Reports</span>
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i>
                </a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('lottery.my.tickets') }}" class="side-menu__item">
                            <i class="fas fa-list me-1"></i>
                            <span class="side-menu__label">My Tickets</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('lottery.my.winnings') }}" class="side-menu__item">
                            <i class="fas fa-medal me-1"></i>
                            <span class="side-menu__label">My Winnings</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a href="{{ route('user.sponsor-tickets.history') }}" class="side-menu__item">
                            <i class="fas fa-exchange-alt me-1"></i>
                            <span class="side-menu__label">Ticket Transfer History</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Support & Contact -->
            <li class="slide">
                <a href="{{ route('user.support.index') }}" class="side-menu__item">
                    <i class="fe fe-help-circle me-2"></i>
                    <span class="side-menu__label">Support Center</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- 🚪 LOGOUT -->
    <li class="slide">
        <a href="javascript:void(0);" class="side-menu__item" onclick="performLogout();">
            <i class="si si-logout side-menu__icon"></i>
            <span class="side-menu__label">🚪 Logout</span>
        </a>
    </li>
</ul>
