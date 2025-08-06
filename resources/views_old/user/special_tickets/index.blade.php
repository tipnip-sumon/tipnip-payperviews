<x-smart_layout>

@section('title', 'Special Token Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">🎫 Special Token Dashboard</h1>
                <p class="page-subtitle">Manage your special lottery tokens and discounts</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Token Summary -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fe fe-star" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $availableTokens->count() }}</h3>
                            <p class="mb-0">Discount Tokens</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Tickets -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fe fe-award" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $commissionTickets->count() ?? 0 }}</h3>
                            <p class="mb-0">Commission Tickets</p>
                            <small class="opacity-75">${{ number_format(($commissionTickets->count() ?? 0) * 2, 0) }} Total Value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Received Tokens -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fe fe-inbox" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $transferStats['total_received'] ?? 0 }}</h3>
                            <p class="mb-0">Tokens Received</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Potential Savings -->
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fe fe-dollar-sign" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            @php
                                $totalDiscount = $availableTokens->sum(function($token) {
                                    return $token->getDiscountPotential(100);
                                });
                            @endphp
                            <h3 class="mb-0">${{ number_format($totalDiscount, 2) }}</h3>
                            <p class="mb-0">Potential Savings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tokens -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Your Special Tokens</h4>
                    <div class="card-options">
                        <a href="{{ route('special.tickets.tokens') }}" class="btn btn-primary btn-sm">
                            <i class="fe fe-eye me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($availableTokens->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Token #</th>
                                        <th>Type</th>
                                        <th>Discount Value</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableTokens->take(5) as $token)
                                        <tr>
                                            <td>
                                                <code>{{ $token->ticket_number }}</code>
                                            </td>
                                            <td>
                                                @if($token->original_owner_id != $token->current_owner_id)
                                                    <span class="badge bg-info">Received</span>
                                                @else
                                                    <span class="badge bg-success">Earned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ${{ number_format($token->getDiscountPotential(100), 2) }}
                                                </strong>
                                                <small class="text-muted">(on $100)</small>
                                            </td>
                                            <td>
                                                @if($token->canBeUsedAsToken())
                                                    <span class="badge bg-success">Ready</span>
                                                @else
                                                    <span class="badge bg-secondary">Used</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($token->canBeTransferred())
                                                    <a href="{{ route('special.tickets.transfer') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fe fe-send"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fe fe-star text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted">No Special Tokens Yet</h4>
                            <p class="text-muted">Special tokens are earned when your referrals make their first plan purchase.</p>
                            <a href="{{ route('user.sponsor-list') }}" class="btn btn-primary">
                                <i class="fe fe-users me-1"></i>Invite Friends
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Commission Lottery Tickets -->
            @if(isset($commissionTickets) && $commissionTickets->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">Your Commission Lottery Tickets</h4>
                    <div class="card-options">
                        <span class="badge bg-success">{{ $commissionTickets->count() }} Tickets</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Value</th>
                                    <th>Lottery Draw</th>
                                    <th>Status</th>
                                    <th>Prize/Refund</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissionTickets->take(5) as $ticket)
                                    <tr>
                                        <td>
                                            <code>{{ $ticket->ticket_number }}</code>
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format($ticket->ticket_price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $ticket->lotteryDraw->draw_date ?? 'Pending' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($ticket->status === 'active')
                                                <span class="badge bg-primary">Active</span>
                                            @elseif($ticket->status === 'winner')
                                                <span class="badge bg-success">Winner</span>
                                            @elseif($ticket->status === 'expired')
                                                <span class="badge bg-warning">Lost ($1 Refunded)</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status === 'winner' && $ticket->prize_amount)
                                                <strong class="text-success">${{ number_format($ticket->prize_amount, 2) }}</strong>
                                            @elseif($ticket->status === 'expired')
                                                <span class="text-info">$1.00 Refunded</span>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($commissionTickets->count() > 5)
                        <div class="text-center mt-3">
                            <p class="text-muted">Showing 5 of {{ $commissionTickets->count() }} commission tickets</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('special.tickets.tokens') }}" class="btn btn-primary">
                            <i class="fe fe-star me-2"></i>View All Tokens
                        </a>
                        
                        @if($availableTokens->count() > 0)
                            <a href="{{ route('special.tickets.transfer') }}" class="btn btn-info">
                                <i class="fe fe-send me-2"></i>Transfer Tokens
                            </a>
                            <a href="{{ route('invest.index') }}" class="btn btn-success">
                                <i class="fe fe-shopping-cart me-2"></i>Use as Discount
                            </a>
                        @endif
                        
                        <a href="{{ route('special.tickets.history') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-clock me-2"></i>Token History
                        </a>
                        <a href="{{ route('special.tickets.statistics') }}" class="btn btn-outline-info">
                            <i class="fe fe-bar-chart me-2"></i>Statistics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Transfers -->
            @if(($transferStats['pending_incoming'] ?? 0) > 0 || ($transferStats['pending_outgoing'] ?? 0) > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title">Pending Transfers</h4>
                    </div>
                    <div class="card-body">
                        @if(($transferStats['pending_incoming'] ?? 0) > 0)
                            <div class="alert alert-warning">
                                <strong>{{ $transferStats['pending_incoming'] }}</strong> incoming transfer(s) waiting for your action
                                <a href="{{ route('special.tickets.incoming') }}" class="btn btn-sm btn-warning ms-2">
                                    Review
                                </a>
                            </div>
                        @endif
                        
                        @if(($transferStats['pending_outgoing'] ?? 0) > 0)
                            <div class="alert alert-info">
                                <strong>{{ $transferStats['pending_outgoing'] }}</strong> outgoing transfer(s) awaiting acceptance
                                <a href="{{ route('special.tickets.outgoing') }}" class="btn btn-sm btn-info ms-2">
                                    Track
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- How it Works -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">How Special Tokens Work</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-users text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>1. Refer Friends</h5>
                            <p class="text-muted">Invite friends to join and share your referral link</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-shopping-cart text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>2. First Purchase</h5>
                            <p class="text-muted">When they make their first plan purchase, you earn both discount tokens and commission lottery tickets (1 per $25)</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-star text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>3. Use as Discount</h5>
                            <p class="text-muted">Apply tokens as discounts on your plan purchases</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-award text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>4. Weekly Lottery</h5>
                            <p class="text-muted">Commission tickets enter weekly lottery draws automatically</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-send text-info" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>5. Transfer & Share</h5>
                            <p class="text-muted">Gift or sell your discount tokens to other users</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="mb-3">
                                <i class="fe fe-dollar-sign text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5>6. Win or Refund</h5>
                            <p class="text-muted">Win lottery prizes or get $1 refund per losing ticket</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</x-smart_layout>
