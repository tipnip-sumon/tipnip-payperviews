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
    <li class="slide">
        <a href="{{route('user.dashboard')}}" class="side-menu__item">
            <i class="fe fe-home side-menu__icon"></i>
            <span class="side-menu__label">Dashboards</span>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="{{route('user.dashboard')}}">Dashboards</a>
            </li>
        </ul>
    </li>
    
    <!-- Unified Lottery & Ticket Center Start - MOVED TO TOP -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fas fa-dice side-menu__icon"></i>
            <span class="side-menu__label">🎰 Lottery & Ticket Center</span>
            @auth
                @php
                    // Lottery data
                    $currentDraw = \App\Models\LotteryDraw::where('status', 'pending')->first();
                    $userTicketsCount = $currentDraw ? $currentDraw->tickets()->where('user_id', auth()->id())->count() : 0;
                    $totalWinnings = auth()->user()->lotteryWinners()->where('claim_status', 'claimed')->sum('prize_amount');
                    $lotterySettings = \App\Models\LotterySetting::getSettings();
                    
                    // Special tokens data
                    $specialTicketService = new \App\Services\SpecialTicketService();
                    $availableTokens = $specialTicketService->getAvailableTokens(auth()->id());
                    $transferStats = $specialTicketService->getUserTransferStats(auth()->id());
                    $tokenCount = $availableTokens->count();
                    
                    // Sponsor tickets data
                    $availableSponsorTickets = auth()->user()->specialTickets()->where('status', 'active')->whereNull('used_as_token_at')->count();
                    
                    // Combined activity indicator
                    $totalActivity = $userTicketsCount + $tokenCount + $availableSponsorTickets;
                @endphp
                @if($totalActivity > 0)
                    <span class="badge badge-success ms-2">{{ $totalActivity }} active</span>
                @elseif($totalWinnings > 0)
                    <span class="badge badge-warning ms-2">${{ number_format($totalWinnings, 2) }} won</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">🎰 Lottery & Ticket Center</a> 
            </li>
            
            <!-- Main Dashboard -->
            <li class="slide">
                <a href="{{ route('lottery.unified.index') }}" class="side-menu__item">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span class="side-menu__label">Dashboard</span>
                    <span class="badge badge-primary ms-auto">All-in-One</span>
                </a>
            </li>
            
            <!-- Lottery Section -->
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fas fa-ticket-alt me-2"></i>
                    <span class="side-menu__label">🎰 Lottery System</span>
                    @auth
                        @if($userTicketsCount > 0)
                            <span class="badge badge-success ms-auto">{{ $userTicketsCount }} tickets</span>
                        @endif
                    @endauth
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i> 
                </a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('lottery.index') }}" class="side-menu__item">
                            <i class="fas fa-shopping-cart me-1"></i>
                            <span class="side-menu__label">Buy Lottery Tickets</span>
                            @auth
                                <span class="badge badge-info ms-auto">${{ number_format($lotterySettings->ticket_price ?? 2, 2) }}</span>
                            @else
                                <span class="badge badge-info ms-auto">$2.00</span>
                            @endauth
                        </a>
                    </li>
                    @auth
                        <li class="slide">
                            <a href="{{ route('lottery.my.tickets') }}" class="side-menu__item">
                                <i class="fas fa-list me-1"></i>
                                <span class="side-menu__label">My Lottery Tickets</span>
                                @if($userTicketsCount > 0)
                                    <span class="badge badge-success ms-auto">{{ $userTicketsCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endauth
                    <li class="slide">
                        <a href="{{ route('lottery.results') }}" class="side-menu__item">
                            <i class="fas fa-trophy me-1"></i>
                            <span class="side-menu__label">Lottery Results</span>
                        </a>
                    </li>
                    @auth
                        @if($totalWinnings > 0)
                            <li class="slide">
                                <a href="{{ route('lottery.my.winnings') }}" class="side-menu__item">
                                    <i class="fas fa-medal me-1"></i>
                                    <span class="side-menu__label">My Winnings</span>
                                    <span class="badge badge-warning ms-auto">${{ number_format($totalWinnings, 2) }}</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </li>
            
            <!-- Special Tokens Section -->
            <li class="slide has-sub" style="display:none;">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fas fa-star me-2"></i>
                    <span class="side-menu__label">🎫 Special Tokens</span>
                    @auth
                        @if($tokenCount > 0)
                            <span class="badge badge-warning ms-auto">{{ $tokenCount }}</span>
                        @endif
                    @endauth
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i>
                </a>
                <ul class="slide-menu child2">
                    @auth
                        <li class="slide">
                            <a href="{{ route('special.tickets.tokens') }}" class="side-menu__item">
                                <i class="fas fa-coins me-1"></i>
                                <span class="side-menu__label">My Special Tokens</span>
                                @if($tokenCount > 0)
                                    <span class="badge badge-success ms-auto">{{ $tokenCount }}</span>
                                @endif
                            </a>
                        </li>
                        @if($tokenCount > 0)
                            <li class="slide">
                                <a href="{{ route('special.tickets.transfer') }}" class="side-menu__item">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    <span class="side-menu__label">Transfer Tokens</span>
                                    <span class="badge badge-info ms-auto">Share</span>
                                </a>
                            </li>
                        @endif
                        @if($transferStats['pending_incoming'] > 0)
                            <li class="slide">
                                <a href="{{ route('special.tickets.incoming') }}" class="side-menu__item">
                                    <i class="fas fa-inbox me-1"></i>
                                    <span class="side-menu__label">Incoming Transfers</span>
                                    <span class="badge badge-warning ms-auto">{{ $transferStats['pending_incoming'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                    <li class="slide">
                        <a href="{{ route('special.tickets.history') }}" class="side-menu__item">
                            <i class="fas fa-history me-1"></i>
                            <span class="side-menu__label">Token History</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Sponsor Tickets Section -->
            <li class="slide has-sub" style="display:none;">
                <a href="javascript:void(0);" class="side-menu__item">
                    <i class="fas fa-users me-2"></i>
                    <span class="side-menu__label">🎟️ Sponsor Tickets</span>
                    @auth
                        @if($availableSponsorTickets > 0)
                            <span class="badge badge-primary ms-auto">{{ $availableSponsorTickets }}</span>
                        @endif
                    @endauth
                    <i class="fe fe-chevron-right side-menu__angle oph-5"></i>
                </a>
                <ul class="slide-menu child2">
                    @auth
                        <li class="slide">
                            <a href="{{ route('user.sponsor-tickets.index') }}" class="side-menu__item">
                                <i class="fas fa-gift me-1"></i>
                                <span class="side-menu__label">My Sponsor Tickets</span>
                                @if($availableSponsorTickets > 0)
                                    <span class="badge badge-success ms-auto">{{ $availableSponsorTickets }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('user.sponsor-tickets.index') }}" class="side-menu__item">
                                <i class="fas fa-share-alt me-1"></i>
                                <span class="side-menu__label">Transfer Sponsor Tickets</span>
                            </a>
                        </li>
                    @endauth
                    <li class="slide">
                        <a href="{{ route('user.sponsor-tickets.history') }}" class="side-menu__item">
                            <i class="fas fa-exchange-alt me-1"></i>
                            <span class="side-menu__label">Transfer History</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Sharing & Social -->
            <li class="slide">
                <a href="{{ route('lottery.share') }}" class="side-menu__item">
                    <i class="fas fa-share-nodes me-2"></i>
                    <span class="side-menu__label">Share & Earn</span>
                    <span class="badge badge-success ms-auto">Social</span>
                </a>
            </li>
            
            <!-- Statistics & Analytics -->
            <li class="slide">
                <a href="{{ route('lottery.statistics') }}" class="side-menu__item">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="side-menu__label">Statistics & Analytics</span>
                    <span class="badge badge-info ms-auto">Data</span>
                </a>
            </li>
            
            <!-- Quick Actions -->
            <li class="slide">
                <a href="{{ route('invest.index') }}" class="side-menu__item">
                    <i class="fas fa-percentage me-2"></i>
                    <span class="side-menu__label">Use as Investment Discount</span>
                    <span class="badge badge-success ms-auto">Save Money</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Unified Lottery & Ticket Center End -->
    
    <!-- Notifications Menu -->
    <li class="slide">
        <a href="{{ route('user.notifications.index') }}" class="side-menu__item">
            <i class="fe fe-bell side-menu__icon"></i>
            <span class="side-menu__label">System Notifications</span>
            @php
                $unreadCount = \App\Models\UserNotification::where('user_id', auth()->id())->unread()->notExpired()->count();
            @endphp
            @if($unreadCount > 0)
                <span class="badge badge-danger ms-auto">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>
    </li>
    
    <!-- <li class="slide">
        <a href="" class="side-menu__item">
            <i class="fe fe-message-square side-menu__icon"></i>
            <span class="side-menu__label">Transfer</span>
        </a>
    </li> -->
    <!-- End::slide -->
     <!-- Add Fund Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-sliders side-menu__icon"></i>
            <span class="side-menu__label">Wallet Management</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Wallet Management</a>
            </li>
            <li class="slide">
                <a href="{{route('deposit.index')}}" class="side-menu__item">
                    <span class="side-menu__label">Add Funds</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.payment_history')}}" class="side-menu__item">
                    <span class="side-menu__label">Deposit History</span>
                </a>
            </li>
        </ul>
    </li>
     <!-- Profile Menu Start -->
    <li class="slide has-sub">
    <a href="javascript:void(0);" class="side-menu__item">
        <i class="fe fe-sliders side-menu__icon"></i>
                    <span class="side-menu__label">Profile</span>
        @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
            <span class="badge badge-warning ms-2">Unverified</span>
        @elseif(auth()->check())
            <span class="badge badge-success ms-2">Verified</span>
        @endif
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide side-menu__label1">
            <a href="javascript:void(0)">Profile</a>
        </li>
        <li class="slide">
            <a href="{{ route('profile.index') }}" class="side-menu__item">
                                    <span class="side-menu__label">My Profile Dashboard</span>
            </a>
        </li>
        <li class="slide">
            <a href="{{ route('profile.edit') }}" class="side-menu__item">
                <span class="side-menu__label">Edit Profile</span>
            </a>
        </li>
        <li class="slide">
            <a href="{{ route('profile.password') }}" class="side-menu__item">
                <span class="side-menu__label">Change Password</span>
            </a>
        </li>
        @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
            <li class="slide">
                <a href="{{ route('verification.notice') }}" class="side-menu__item">
                    <span class="side-menu__label">Verify Email</span>
                    <span class="badge badge-warning ms-auto">Required</span>
                </a>
            </li>
        @endif
        <li class="slide">
            <a href="{{ route('profile.security') }}" class="side-menu__item">
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
    <!-- KYC Verification Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-shield side-menu__icon"></i>
            <span class="side-menu__label">KYC Verification</span>
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
                <a href="javascript:void(0)">KYC Verification</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.kyc.index') }}" class="side-menu__item">
                    <span class="side-menu__label">KYC Dashboard</span>
                </a>
            </li>
            @auth
                @if(Auth::user()->kv == 0)
                    <li class="slide">
                        <a href="{{ route('user.kyc.create') }}" class="side-menu__item">
                            <span class="side-menu__label">Submit KYC</span>
                            <span class="badge badge-primary ms-auto">New</span>
                        </a>
                    </li>
                @endif
            @endauth
            <li class="slide">
                <a href="{{ route('user.kyc.status') }}" class="side-menu__item">
                    <span class="side-menu__label">KYC Status</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- KYC Verification Menu End -->
     <!-- Video Gallery Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-video side-menu__icon"></i>
            <span class="side-menu__label">Earning Gallery</span>
            @auth
                @php
                    $totalVideoEarnings = Auth::user()->videoViews()->sum('earned_amount');
                    $todayVideoEarnings = Auth::user()->videoViews()->whereDate('viewed_at', today())->sum('earned_amount');
                @endphp
                @if($totalVideoEarnings > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalVideoEarnings, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Earning Gallery</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.video-views.gallery') }}" class="side-menu__item">
                    <i class="fe fe-play-circle me-2"></i>
                    <span class="side-menu__label">Earn From Videos</span>
                    <span class="badge badge-primary ms-auto">Earn Money</span>
                </a>
            </li>
            @auth
                <li class="slide">
                    <a href="{{ route('user.video-views.history') }}" class="side-menu__item">
                        <i class="fe fe-clock me-2"></i>
                        <span class="side-menu__label">Viewing History</span>
                        @if($totalVideoEarnings > 0)
                            <span class="badge badge-info ms-auto">{{ Auth::user()->videoViews()->count() }} videos</span>
                        @endif
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('user.video-views.earnings') }}" class="side-menu__item">
                        <i class="fe fe-dollar-sign me-2"></i>
                        <span class="side-menu__label">Video Earnings</span>
                        @if($totalVideoEarnings > 0)
                            <span class="badge badge-success ms-auto">${{ number_format($totalVideoEarnings, 4) }}</span>
                        @endif
                    </a>
                </li>
                @if($todayVideoEarnings > 0)
                    <li class="slide">
                        <a href="{{ route('video.daily-report') }}" class="side-menu__item">
                            <i class="fe fe-calendar me-2"></i>
                            <span class="side-menu__label">Daily Report</span>
                            <span class="badge badge-warning ms-auto">Today: ${{ number_format($todayVideoEarnings, 4) }}</span>
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </li>
    <!-- Video Gallery Menu End -->
     
     
    
    <!-- Profile Menu End -->
    <!-- Task Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-layers side-menu__icon"></i>
            <span class="side-menu__label">Security Package</span>
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
                <a href="javascript:void(0)">Security Package</a>
            </li>
            <li class="slide">
                <a href="{{route('invest.index')}}" class="side-menu__item">
                    <i class="fe fe-shield me-2"></i>
                    <span class="side-menu__label">Security Package Plan</span>
                    @auth
                        @if(!$currentDeposit)
                            <span class="badge badge-primary ms-auto">Start</span>
                        @else
                            <span class="badge badge-info ms-auto">Upgrade</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('invest.history')}}" class="side-menu__item">
                    <i class="fe fe-file-text me-2"></i>
                    <span class="side-menu__label">Security Package Report</span>
                </a>
            </li>
            @auth
                @if($currentDeposit)
                    <li class="slide">
                        <a href="{{ route('user.withdraw') }}" class="side-menu__item">
                            <i class="fe fe-download me-2"></i>
                            <span class="side-menu__label">Withdraw Security Package</span>
                            <span class="badge badge-warning ms-auto">20% Fee</span>
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </li>
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-layers side-menu__icon"></i>
            <span class="side-menu__label">Team Views</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">My Team</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.sponsor-list') }}" class="side-menu__item">
                    <span class="side-menu__label">Referral List</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.team-tree')}}" class="side-menu__item">
                    <span class="side-menu__label">Team Tree</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Task Menu End -->
    <!-- My Team Start -->
    <li class="slide has-sub" style="display: none;">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fa-solid fa-people-group side-menu__icon"></i>
            <span class="side-menu__label">My Team</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">My Team</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.sponsor-list') }}" class="side-menu__item">
                    <span class="side-menu__label">Sponsor List</span>
                </a>
            </li>
            <li class="slide">
                <a href="" class="side-menu__item">
                    <span class="side-menu__label">Generation Report</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- My Team End -->
    
    <!-- Messages Menu Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-mail side-menu__icon"></i>
            <span class="side-menu__label">Messages</span>
            @auth
                @php
                    $unreadMessages = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', false)->count();
                    $totalMessages = \App\Models\Message::where(function($query) {
                        $query->where('from_user_id', auth()->id())
                              ->orWhere('to_user_id', auth()->id());
                    })->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="badge badge-danger ms-2">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>
                @elseif($totalMessages > 0)
                    <span class="badge badge-info ms-2">{{ $totalMessages }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Messages</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.messages') }}" class="side-menu__item">
                    <i class="fe fe-message-circle me-2"></i>
                    <span class="side-menu__label">Messages Dashboard</span>
                    @auth
                        @if($totalMessages > 0)
                            <span class="badge badge-primary ms-auto">{{ $totalMessages }} total</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.messages.inbox') }}" class="side-menu__item">
                    <i class="fe fe-inbox me-2"></i>
                    <span class="side-menu__label">Inbox</span>
                    @auth
                        @if($unreadMessages > 0)
                            <span class="badge badge-danger ms-auto">{{ $unreadMessages }} new</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.messages.sent') }}" class="side-menu__item">
                    <i class="fe fe-send me-2"></i>
                    <span class="side-menu__label">Sent Messages</span>
                    @auth
                        @php
                            $sentMessages = \App\Models\Message::where('from_user_id', auth()->id())->count();
                        @endphp
                        @if($sentMessages > 0)
                            <span class="badge badge-success ms-auto">{{ $sentMessages }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.sponsor-list') }}" class="side-menu__item">
                    <i class="fe fe-edit me-2"></i>
                    <span class="side-menu__label">Compose Message</span>
                    <span class="badge badge-primary ms-auto">New</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Messages Menu End -->
    
    <!-- Team Bonus Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-headphones side-menu__icon"></i>
            <span class="side-menu__label">Commission Center</span>
            @auth
                @php
                    $totalCommissions = \App\Models\ReferralCommission::where('referrer_user_id', auth()->id())->sum('commission_amount');
                    $todayCommissions = \App\Models\ReferralCommission::where('referrer_user_id', auth()->id())->whereDate('distributed_at', today())->sum('commission_amount');
                @endphp
                @if($totalCommissions > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalCommissions, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Commission Center</a>
            </li>
            <li class="slide">
                <a href="{{route('user.generation-history')}}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Commission History</span>
                    @auth
                        @if($todayCommissions > 0)
                            <span class="badge badge-primary ms-auto">Today: ${{ number_format($todayCommissions, 4) }}</span>
                        @endif
                    @endauth
                </a>
            </li>
        </ul>
    </li>
    <!-- Team Bonus End -->
    <!-- Sponsor Tickets Start -->
        <!-- Team Bonus Start -->
    <!-- Sponsor Tickets End -->
    <!-- Transaction Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fa-solid fa-tent-arrow-left-right side-menu__icon"></i>
            <span class="side-menu__label">Financial Operations</span>
            @auth
                @php
                    $totalBalance = (auth()->user()->deposit_wallet ?? 0) + (auth()->user()->interest_wallet ?? 0);
                    $activeDeposit = auth()->user()->invests()->where('status', 1)->first();
                @endphp
                @if($totalBalance > 0)
                    <span class="badge badge-success ms-2">${{ number_format($totalBalance, 2) }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Financial Operations</a>
            </li>
            <li class="slide">
                <a href="{{ route('deposit.index') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2"></i>
                    <span class="side-menu__label">Deposit</span>
                    <span class="badge badge-primary ms-auto">Add Funds</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.withdraw') }}" class="side-menu__item">
                    <i class="fe fe-minus-circle me-2"></i>
                    <span class="side-menu__label">Withdraw Deposit</span>
                    @auth
                        @if($activeDeposit)
                            <span class="badge badge-warning ms-auto">20% Fee</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.withdraw.wallet') }}" class="side-menu__item">
                    <i class="fe fe-credit-card me-2"></i>
                    <span class="side-menu__label">Withdraw Wallet</span> 
                    @auth
                        @php
                            $walletBalance = (auth()->user()->deposit_wallet ?? 0) + (auth()->user()->interest_wallet ?? 0);
                        @endphp
                        @if($walletBalance > 0)
                            <span class="badge badge-success ms-auto">${{ number_format($walletBalance, 2) }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.transfer_funds')}}" class="side-menu__item">
                    <span class="side-menu__label">Transfer</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Transaction End -->
    <!-- Transaction Report Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="bi bi-box side-menu__icon"></i>
            <span class="side-menu__label">Financial Reports</span>
            @auth
                @php
                    $totalDeposits = auth()->user()->deposits()->where('status', 1)->count();
                    $totalWithdrawals = auth()->user()->withdrawals()->count();
                @endphp
                @if($totalDeposits > 0 || $totalWithdrawals > 0)
                    <span class="badge badge-info ms-2">{{ $totalDeposits + $totalWithdrawals }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Transaction Report</a>
            </li>
            <li class="slide">
                <a href="{{ route('deposit.history') }}" class="side-menu__item">
                    <i class="fe fe-trending-up me-2"></i>
                    <span class="side-menu__label">Deposit Report</span>
                    @auth
                        @if($totalDeposits > 0)
                            <span class="badge badge-success ms-auto">{{ $totalDeposits }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.withdraw.history') }}" class="side-menu__item">
                    <i class="fe fe-trending-down me-2"></i>
                    <span class="side-menu__label">Deposit Withdraw Report</span>
                    @auth
                        @php
                            $depositWithdrawals = auth()->user()->withdrawals()->where('withdraw_type', 'deposit')->count();
                        @endphp
                        @if($depositWithdrawals > 0)
                            <span class="badge badge-warning ms-auto">{{ $depositWithdrawals }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.withdraw.wallet.history') }}" class="side-menu__item">
                    <i class="fe fe-credit-card me-2"></i>
                    <span class="side-menu__label">Wallet Withdraw Report</span>
                    @auth
                        @php
                            $walletWithdrawals = auth()->user()->withdrawals()->where('withdraw_type', 'wallet')->count();
                        @endphp
                        @if($walletWithdrawals > 0)
                            <span class="badge badge-info ms-auto">{{ $walletWithdrawals }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{route('user.transfer_history')}}" class="side-menu__item">
                    <span class="side-menu__label">Transection History</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Transaction Report End -->
    <!-- Exchange Report Start -->
    <li class="slide has-sub" style="display: none;">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fa-solid fa-right-left side-menu__icon"></i>
            <span class="side-menu__label">Exchange Report</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Exchange Report</a>
            </li>
            <li class="slide">
                <a href="" class="side-menu__item">
                    <span class="side-menu__label">Purchase In</span>
                </a>
            </li>
            <li class="slide">
                <a href="" class="side-menu__item">
                    <span class="side-menu__label">Purchase Out</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Transaction Report End -->
    
    <!-- Support Center Start -->
    <li class="slide has-sub">
        <a href="javascript:void(0);" class="side-menu__item">
            <i class="fe fe-headset side-menu__icon"></i>
            <span class="side-menu__label">Support Center</span>
            @auth
                @php
                    $unreadMessages = Auth::user()->receivedMessages()->unread()->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="badge badge-danger ms-2">{{ $unreadMessages }}</span>
                @endif
            @endauth
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide side-menu__label1">
                <a href="javascript:void(0)">Support Center</a>
            </li>
            <li class="slide">
                <a href="{{ route('user.support.index') }}" class="side-menu__item">
                    <i class="fe fe-home me-2"></i>
                    <span class="side-menu__label">Support Dashboard</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.support.create') }}" class="side-menu__item">
                    <i class="fe fe-plus-circle me-2"></i>
                    <span class="side-menu__label">Create Ticket</span>
                    <span class="badge badge-primary ms-auto">New</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.support.tickets') }}" class="side-menu__item">
                    <i class="fe fe-list me-2"></i>
                    <span class="side-menu__label">My Tickets</span>
                    @auth
                        @if($unreadMessages > 0)
                            <span class="badge badge-danger ms-auto">{{ $unreadMessages }}</span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.support.knowledge') }}" class="side-menu__item">
                    <i class="fe fe-book me-2"></i>
                    <span class="side-menu__label">Knowledge Base</span>
                    <span class="badge badge-info ms-auto">FAQ</span>
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('user.support.contact') }}" class="side-menu__item">
                    <i class="fe fe-mail me-2"></i>
                    <span class="side-menu__label">Contact Us</span>
                </a>
            </li>
        </ul>
    </li>
    <!-- Support Center End -->
    

    <!-- Start::slide -->
    <li class="slide">
        <!-- Primary logout using enhanced JavaScript method -->
        <a href="javascript:void(0);" class="side-menu__item" onclick="performLogout();">
            <i class="si si-logout side-menu__icon"></i>
            <span class="side-menu__label">Logout</span>
        </a>
    </li>

    <!-- Cache Management Tools (for development/admin) -->
    {{-- @if(auth()->check() && auth()->user()->isAdmin())
    <li class="slide">
        <a href="javascript:void(0);" class="side-menu__item" onclick="showCacheManagementModal();">
            <i class="fe fe-trash-2 side-menu__icon"></i>
            <span class="side-menu__label">Cache Manager</span>
        </a>
    </li>
    @endif --}}
</ul>