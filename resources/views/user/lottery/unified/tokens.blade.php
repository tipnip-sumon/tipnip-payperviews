<!-- Special Tokens Tab Content -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fe fe-star me-2"></i>🎫 Special Tokens</h4>
            @if($specialTokensCount > 0)
                <div class="badge badge-warning fs-6">{{ $specialTokensCount }} Available</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Token Overview -->
    <div class="col-md-6">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fe fe-star me-2"></i>My Special Tokens</h5>
            </div>
            <div class="card-body">
                @if($specialTokensCount > 0)
                    <div class="text-center mb-3">
                        <h3 class="text-warning">{{ $specialTokensCount }}</h3>
                        <p class="mb-0">Available Special Tokens</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fe fe-info me-2"></i>
                        <strong>Special Tokens</strong> can be used to get discounts on investments or transferred to other users.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('special.tickets.tokens') }}" class="btn btn-warning">
                            <i class="fe fe-eye me-2"></i>View All Tokens
                        </a>
                        <a href="{{ route('special.tickets.transfer') }}" class="btn btn-outline-warning">
                            <i class="fe fe-send me-2"></i>Transfer Tokens
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-star display-4 text-muted"></i>
                        <h5 class="mt-3">No Special Tokens</h5>
                        <p class="text-muted">You don't have any special tokens yet. Complete investments to earn them!</p>
                        <a href="{{ route('invest.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Start Investing
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Transfer Statistics -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-trending-up me-2"></i>Transfer Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">{{ $transferStats['total_sent'] ?? 0 }}</h4>
                        <small class="text-muted">Tokens Sent</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ $transferStats['total_received'] ?? 0 }}</h4>
                        <small class="text-muted">Tokens Received</small>
                    </div>
                </div>
                
                @if(($transferStats['pending_incoming'] ?? 0) > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fe fe-clock me-2"></i>
                        You have {{ $transferStats['pending_incoming'] }} pending incoming transfer{{ $transferStats['pending_incoming'] > 1 ? 's' : '' }}.
                        <div class="mt-2">
                            <a href="{{ route('special.tickets.incoming') }}" class="btn btn-sm btn-warning">
                                Review Transfers
                            </a>
                        </div>
                    </div>
                @endif
                
                @if(($transferStats['pending_outgoing'] ?? 0) > 0)
                    <div class="alert alert-info mt-3">
                        <i class="fe fe-send me-2"></i>
                        You have {{ $transferStats['pending_outgoing'] }} pending outgoing transfer{{ $transferStats['pending_outgoing'] > 1 ? 's' : '' }}.
                        <div class="mt-2">
                            <a href="{{ route('special.tickets.outgoing') }}" class="btn btn-sm btn-info">
                                View Sent Transfers
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Available Tokens List -->
@if($specialTokensCount > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fe fe-list me-2"></i>Available Tokens</h5>
                <div>
                    <a href="{{ route('special.tickets.transfer') }}" class="btn btn-sm btn-warning me-2">
                        <i class="fe fe-send me-1"></i>Transfer
                    </a>
                    <a href="{{ route('invest.index') }}" class="btn btn-sm btn-success">
                        <i class="fe fe-percent me-1"></i>Use for Discount
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Token ID</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Received Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableTokens->take(10) as $token)
                                <tr>
                                    <td><strong>#{{ $token->id }}</strong></td>
                                    <td>
                                        <span class="badge badge-primary">{{ ucfirst($token->type ?? 'Special') }}</span>
                                    </td>
                                    <td>
                                        @if($token->discount_percentage)
                                            <span class="text-success">{{ $token->discount_percentage }}% Off</span>
                                        @else
                                            <span class="text-info">Special Privilege</span>
                                        @endif
                                    </td>
                                    <td>{{ $token->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('special.tickets.transfer') }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fe fe-send"></i>
                                            </a>
                                            <a href="{{ route('invest.index') }}" class="btn btn-sm btn-outline-success">
                                                <i class="fe fe-percent"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($availableTokens->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('special.tickets.tokens') }}" class="btn btn-outline-primary">
                            View All {{ $availableTokens->count() }} Tokens
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Token Usage Guide -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-help-circle me-2"></i>How to Use Special Tokens</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fe fe-percent"></i>
                        </div>
                        <h6>Investment Discount</h6>
                        <small class="text-muted">Use tokens to get discounts when making new investments</small>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fe fe-send"></i>
                        </div>
                        <h6>Transfer to Others</h6>
                        <small class="text-muted">Send tokens to other users in your network</small>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fe fe-gift"></i>
                        </div>
                        <h6>Special Privileges</h6>
                        <small class="text-muted">Access exclusive features and benefits</small>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fe fe-lightbulb me-2"></i>
                    <strong>Pro Tip:</strong> Special tokens are earned through successful investments and referral activities. The more you invest and refer, the more tokens you earn!
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Token Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fe fe-activity me-2"></i>Recent Token Activity</h5>
                <a href="{{ route('special.tickets.history') }}" class="btn btn-sm btn-outline-primary">View Full History</a>
            </div>
            <div class="card-body">
                @php
                    $recentActivity = auth()->user()->specialTickets()
                        ->with(['transfers'])
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($recentActivity->count() > 0)
                    <div class="timeline">
                        @foreach($recentActivity as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning">
                                    <i class="fe fe-star"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="timeline-title">Token Received</h6>
                                            <p class="timeline-text">Special token #{{ $activity->id }} added to your account</p>
                                        </div>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($activity->used_as_token_at)
                                        <span class="badge badge-secondary">Used</span>
                                    @else
                                        <span class="badge badge-success">Available</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-clock display-4 text-muted"></i>
                        <h5 class="mt-3">No Recent Activity</h5>
                        <p class="text-muted">Your token activity will appear here once you start earning and using tokens.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
