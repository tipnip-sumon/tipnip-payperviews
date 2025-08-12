<!-- Dashboard Overview -->
<div class="row g-4">
    
    <!-- Current Status Cards -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><i class="fe fe-activity me-2"></i>Current Status</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="p-3">
                            <h3 class="text-success mb-1">{{ $lotteryTicketsCount }}</h3>
                            <p class="text-muted mb-0 small">Active Lottery Tickets</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3">
                            <h3 class="text-warning mb-1">{{ $specialTokensCount }}</h3>
                            <p class="text-muted mb-0 small">Available Tokens</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="p-3">
                            <h3 class="text-info mb-1">{{ $sponsorTicketsCount }}</h3>
                            <p class="text-muted mb-0 small">Sponsor Tickets</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3">
                            <h3 class="text-danger mb-1">${{ number_format($totalWinnings, 2) }}</h3>
                            <p class="text-muted mb-0 small">Total Winnings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Next Lottery Draw -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0"><i class="fe fe-clock me-2"></i>Next Lottery Draw</h5>
            </div>
            <div class="card-body text-center">
                @if($nextDraw['exists'])
                    <div class="mb-3">
                        <h2 class="text-primary mb-1" id="lotteryCountdown">{{ $nextDraw['time_remaining'] }}</h2>
                        <p class="text-muted mb-0">Time Remaining</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="p-2">
                                <h4 class="text-success mb-1">${{ number_format($nextDraw['total_prize'], 2) }}</h4>
                                <small class="text-muted">Prize Pool</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2">
                                <h4 class="text-info mb-1">{{ $nextDraw['participants'] }}</h4>
                                <small class="text-muted">Active Tickets</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus-circle me-2"></i>Buy More Tickets
                        </a>
                    </div>
                @else
                    <div class="py-4">
                        <i class="fe fe-clock fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No Active Draw</h5>
                        <p class="text-muted">Check back later for the next lottery draw.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-zap me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('lottery.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                            <i class="fe fe-shopping-cart fs-2 mb-2"></i>
                            <span>Buy Lottery Tickets</span>
                            <small class="text-muted">${{ number_format($lotterySettings->ticket_price ?? 2, 2) }} each</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('special.tickets.transfer') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                            <i class="fe fe-send fs-2 mb-2"></i>
                            <span>Transfer Tokens</span>
                            <small class="text-muted">Share with friends</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <button type="button" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fe fe-share-2 fs-2 mb-2"></i>
                            <span>Share & Earn</span>
                            <small class="text-muted">Invite friends</small>
                        </button>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('invest.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                            <i class="fe fe-percent fs-2 mb-2"></i>
                            <span>Use as Discount</span>
                            <small class="text-muted">Save on investments</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-bar-chart-2 me-2"></i>Your Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Lottery Participation</span>
                        <span class="fw-bold">{{ $lotteryTicketsCount }}/10</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ min(($lotteryTicketsCount / 10) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Token Collection</span>
                        <span class="fw-bold">{{ $specialTokensCount }}/5</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ min(($specialTokensCount / 5) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Sponsor Activity</span>
                        <span class="fw-bold">{{ $sponsorTicketsCount }}/3</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ min(($sponsorTicketsCount / 3) * 100, 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Sharing Score</span>
                        <span class="fw-bold">{{ $sharingStats['total_referrals'] }}/10</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ min(($sharingStats['total_referrals'] / 10) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sharing Summary -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-users me-2"></i>Referral Network</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary mb-1">{{ $sharingStats['total_referrals'] }}</h3>
                    <p class="text-muted mb-0">Total Referrals</p>
                </div>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="p-2">
                            <h4 class="text-success mb-1">{{ $sharingStats['active_referrals'] }}</h4>
                            <small class="text-muted">Active Members</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2">
                            <h4 class="text-warning mb-1">${{ number_format($sharingStats['potential_earnings'], 2) }}</h4>
                            <small class="text-muted">Potential Earnings</small>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fe fe-share-2 me-2"></i>Share Your Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievement Badges -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-award me-2"></i>Achievements & Milestones</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $achievements = [
                            [
                                'name' => 'First Ticket',
                                'description' => 'Purchase your first lottery ticket',
                                'achieved' => $lotteryTicketsCount > 0,
                                'icon' => 'fe fe-ticket',
                                'color' => 'success'
                            ],
                            [
                                'name' => 'Token Collector',
                                'description' => 'Collect 5 special tokens',
                                'achieved' => $specialTokensCount >= 5,
                                'icon' => 'fe fe-star',
                                'color' => 'warning'
                            ],
                            [
                                'name' => 'Sponsor Master',
                                'description' => 'Earn 3 sponsor tickets',
                                'achieved' => $sponsorTicketsCount >= 3,
                                'icon' => 'fe fe-users',
                                'color' => 'info'
                            ],
                            [
                                'name' => 'Social Butterfly',
                                'description' => 'Refer 10 friends',
                                'achieved' => $sharingStats['total_referrals'] >= 10,
                                'icon' => 'fe fe-share-2',
                                'color' => 'danger'
                            ],
                            [
                                'name' => 'Lucky Winner',
                                'description' => 'Win your first lottery prize',
                                'achieved' => $totalWinnings > 0,
                                'icon' => 'fe fe-trophy',
                                'color' => 'primary'
                            ]
                        ];
                    @endphp
                    
                    @foreach($achievements as $achievement)
                        <div class="col-md-2 col-4">
                            <div class="text-center p-3 {{ $achievement['achieved'] ? 'bg-light' : 'opacity-50' }}">
                                <div class="achievement-badge mb-2 {{ $achievement['achieved'] ? 'pulse-animation' : '' }}">
                                    <i class="{{ $achievement['icon'] }} fs-2 text-{{ $achievement['color'] }}"></i>
                                </div>
                                <h6 class="mb-1 {{ $achievement['achieved'] ? 'text-dark' : 'text-muted' }}">{{ $achievement['name'] }}</h6>
                                <small class="text-muted">{{ $achievement['description'] }}</small>
                                @if($achievement['achieved'])
                                    <div class="mt-2">
                                        <span class="badge bg-{{ $achievement['color'] }}">Achieved!</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
