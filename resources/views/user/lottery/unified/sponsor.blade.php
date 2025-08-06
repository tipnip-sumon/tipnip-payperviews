<!-- Sponsor Tickets Tab Content -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fe fe-users me-2"></i>🎟️ Sponsor Tickets</h4>
            @if($sponsorTicketsCount > 0)
                <div class="badge badge-primary fs-6">{{ $sponsorTicketsCount }} Available</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Sponsor Tickets Overview -->
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fe fe-users me-2"></i>My Sponsor Tickets</h5>
            </div>
            <div class="card-body">
                @if($sponsorTicketsCount > 0)
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $sponsorTicketsCount }}</h3>
                        <p class="mb-0">Available Sponsor Tickets</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        <strong>Sponsor Tickets</strong> are earned when you qualify as a sponsor. They can be transferred to team members or used as special tokens.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-primary">
                            <i class="fe fe-eye me-2"></i>View All Tickets
                        </a>
                        <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-outline-primary">
                            <i class="fe fe-send me-2"></i>Transfer Tickets
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-users display-4 text-muted"></i>
                        <h5 class="mt-3">No Sponsor Tickets</h5>
                        <p class="text-muted">Become a sponsor to earn tickets. Invest at least $25 to qualify!</p>
                        <a href="{{ route('invest.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Qualify as Sponsor
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sponsor Qualification Status -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-award me-2"></i>Sponsor Status</h5>
            </div>
            <div class="card-body">
                @php
                    $user = auth()->user();
                    $currentInvestment = $user->invests()->where('status', 1)->first();
                    $totalInvestment = $user->invests()->where('status', 1)->sum('amount');
                    $isSponsor = $totalInvestment >= 25;
                    $requiredAmount = 25;
                    $progressPercentage = min(($totalInvestment / $requiredAmount) * 100, 100);
                @endphp
                
                <div class="text-center mb-3">
                    @if($isSponsor)
                        <div class="badge badge-success fs-6 p-3">
                            <i class="fe fe-check-circle me-2"></i>Qualified Sponsor
                        </div>
                        <p class="text-success mt-2">You meet the sponsor requirements!</p>
                    @else
                        <div class="badge badge-warning fs-6 p-3">
                            <i class="fe fe-clock me-2"></i>Sponsor Candidate
                        </div>
                        <p class="text-warning mt-2">Invest more to become a sponsor</p>
                    @endif
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Investment Progress</span>
                        <span>${{ number_format($totalInvestment, 2) }} / ${{ number_format($requiredAmount, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar {{ $isSponsor ? 'bg-success' : 'bg-warning' }}" 
                             role="progressbar" 
                             style="width: {{ $progressPercentage }}%">
                        </div>
                    </div>
                </div>
                
                @if(!$isSponsor)
                    <div class="alert alert-info">
                        <i class="fe fe-info-circle me-2"></i>
                        Invest ${{ number_format($requiredAmount - $totalInvestment, 2) }} more to qualify as a sponsor and start earning tickets!
                    </div>
                @endif
                
                <div class="d-grid">
                    @if($isSponsor)
                        <a href="{{ route('user.sponsor-list') }}" class="btn btn-success">
                            <i class="fe fe-users me-2"></i>Manage Team
                        </a>
                    @else
                        <a href="{{ route('invest.index') }}" class="btn btn-warning">
                            <i class="fe fe-trending-up me-2"></i>Invest More
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Sponsor Tickets -->
@if($sponsorTicketsCount > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fe fe-list me-2"></i>Available Sponsor Tickets</h5>
                <div>
                    <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-sm btn-primary me-2">
                        <i class="fe fe-send me-1"></i>Transfer
                    </a>
                    <a href="{{ route('invest.index') }}" class="btn btn-sm btn-success">
                        <i class="fe fe-star me-1"></i>Use as Token
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $sponsorTickets = auth()->user()->specialTickets()
                        ->where('status', 'active')
                        ->whereNull('used_as_token_at')
                        ->latest()
                        ->limit(10)
                        ->get();
                @endphp
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Earned From</th>
                                <th>Date Received</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sponsorTickets as $ticket)
                                <tr>
                                    <td><strong>#{{ $ticket->id }}</strong></td>
                                    <td>
                                        @if($ticket->source_investment_id)
                                            Investment #{{ $ticket->source_investment_id }}
                                        @else
                                            Sponsor Qualification
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M j, Y') }}</td>
                                    <td>
                                        @if($ticket->discount_percentage)
                                            <span class="text-success">{{ $ticket->discount_percentage }}% Discount</span>
                                        @else
                                            <span class="text-primary">Special Privilege</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Available</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-sm btn-outline-primary" title="Transfer">
                                                <i class="fe fe-send"></i>
                                            </a>
                                            <a href="{{ route('invest.index') }}" class="btn btn-sm btn-outline-success" title="Use as Token">
                                                <i class="fe fe-star"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($sponsorTickets->count() >= 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-outline-primary">
                            View All Sponsor Tickets
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Transfer History -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fe fe-activity me-2"></i>Recent Transfer Activity</h5>
                <a href="{{ route('user.sponsor-tickets.history') }}" class="btn btn-sm btn-outline-primary">View Full History</a>
            </div>
            <div class="card-body">
                @php
                    $recentTransfers = \App\Models\SpecialTicketTransfer::where(function($query) {
                        $query->where('from_user_id', auth()->id())
                              ->orWhere('to_user_id', auth()->id());
                    })
                    ->with(['fromUser', 'toUser', 'specialTicket'])
                    ->latest()
                    ->limit(5)
                    ->get();
                @endphp
                
                @if($recentTransfers->count() > 0)
                    <div class="timeline">
                        @foreach($recentTransfers as $transfer)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $transfer->from_user_id == auth()->id() ? 'bg-info' : 'bg-success' }}">
                                    <i class="fe fe-{{ $transfer->from_user_id == auth()->id() ? 'arrow-up' : 'arrow-down' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="timeline-title">
                                                @if($transfer->from_user_id == auth()->id())
                                                    Ticket Sent
                                                @else
                                                    Ticket Received
                                                @endif
                                            </h6>
                                            <p class="timeline-text">
                                                @if($transfer->from_user_id == auth()->id())
                                                    Sent sponsor ticket to {{ $transfer->toUser->username ?? 'Unknown' }}
                                                @else
                                                    Received sponsor ticket from {{ $transfer->fromUser->username ?? 'Unknown' }}
                                                @endif
                                            </p>
                                        </div>
                                        <small class="text-muted">{{ $transfer->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge badge-{{ $transfer->status == 'completed' ? 'success' : ($transfer->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($transfer->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-clock display-4 text-muted"></i>
                        <h5 class="mt-3">No Transfer Activity</h5>
                        <p class="text-muted">Your sponsor ticket transfers will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- How to Earn Sponsor Tickets -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-help-circle me-2"></i>How to Earn Sponsor Tickets</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">1</span>
                        </div>
                        <h6>Invest $25+</h6>
                        <small class="text-muted">Make an investment of at least $25 to qualify as a sponsor</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">2</span>
                        </div>
                        <h6>Maintain Status</h6>
                        <small class="text-muted">Keep your investment active to maintain sponsor status</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">3</span>
                        </div>
                        <h6>Earn Tickets</h6>
                        <small class="text-muted">Automatically receive sponsor tickets based on your activity</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">4</span>
                        </div>
                        <h6>Transfer or Use</h6>
                        <small class="text-muted">Transfer to team members or use as investment tokens</small>
                    </div>
                </div>
                
                <div class="alert alert-success mt-3">
                    <i class="fe fe-lightbulb me-2"></i>
                    <strong>Pro Tip:</strong> Sponsor tickets are valuable! They can be transferred to help your team members or used to get discounts on your own investments.
                </div>
            </div>
        </div>
    </div>
</div>
