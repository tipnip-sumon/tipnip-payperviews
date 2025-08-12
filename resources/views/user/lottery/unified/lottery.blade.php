<!-- Lottery System Tab Content -->
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fe fe-play-circle me-2"></i>🎰 Lottery System</h4>
            @if($currentDraw)
                <div class="text-end">
                    <small class="text-muted">Next Draw:</small>
                    <div class="badge badge-primary" id="lottery-countdown">
                        {{ $currentDraw->draw_date->format('M j, Y H:i') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Current Lottery Information -->
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fe fe-gift me-2"></i>Current Lottery Draw</h5>
            </div>
            <div class="card-body">
                @if($currentDraw)
                    <div class="row">
                        <div class="col-6">
                            <strong>{{ $currentDraw->formatted_draw_number }}</strong>
                            <p class="mb-1">Total Prize Pool</p>
                            <h4 class="text-success">${{ number_format($currentDraw->calculatePrizePool(), 2) }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">Tickets Sold</p>
                            <h4 class="text-info">{{ $currentDraw->tickets()->count() }}</h4>
                            <small class="text-muted">Your tickets: {{ $lotteryTicketsCount }}</small>
                        </div>
                    </div>
                    
                    @if($lotteryTicketsCount > 0)
                        <div class="alert alert-success mt-3">
                            <i class="fe fe-check-circle me-2"></i>
                            You have {{ $lotteryTicketsCount }} ticket{{ $lotteryTicketsCount > 1 ? 's' : '' }} in this draw!
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-shopping-cart me-2"></i>Buy More Tickets
                        </a>
                        <a href="{{ route('lottery.my.tickets') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list me-2"></i>View My Tickets
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-clock display-4 text-muted"></i>
                        <h5 class="mt-3">No Active Draw</h5>
                        <p class="text-muted">The next lottery draw will be announced soon.</p>
                        <a href="{{ route('lottery.results') }}" class="btn btn-outline-primary">
                            <i class="fe fe-bar-chart me-2"></i>View Past Results
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Lottery Stats -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-bar-chart-2 me-2"></i>My Lottery Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary">{{ auth()->user()->lotteryTickets()->count() }}</h4>
                        <small class="text-muted">Total Tickets</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">${{ number_format($totalWinnings, 2) }}</h4>
                        <small class="text-muted">Total Winnings</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ auth()->user()->lotteryWinners()->count() }}</h4>
                        <small class="text-muted">Times Won</small>
                    </div>
                </div>

                @if($totalWinnings > 0)
                    <div class="mt-3">
                        <div class="d-grid">
                            <a href="{{ route('lottery.my.winnings') }}" class="btn btn-success">
                                <i class="fe fe-trophy me-2"></i>View My Winnings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Lottery Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fe fe-activity me-2"></i>Recent Lottery Activity</h5>
                <a href="{{ route('lottery.results') }}" class="btn btn-sm btn-outline-primary">View All Results</a>
            </div>
            <div class="card-body">
                @php
                    $recentTickets = auth()->user()->lotteryTickets()->with('draw')->latest()->limit(5)->get();
                @endphp
                
                @if($recentTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Draw</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                    <tr>
                                        <td><strong>#{{ $ticket->ticket_number }}</strong></td>
                                        <td>{{ $ticket->draw->formatted_draw_number ?? 'N/A' }}</td>
                                        <td>{{ $ticket->created_at->format('M j, Y') }}</td>
                                        <td>
                                            @if($ticket->draw && $ticket->draw->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($ticket->draw && $ticket->draw->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $isWinner = $ticket->draw && $ticket->draw->winners()->where('lottery_ticket_id', $ticket->id)->exists();
                                            @endphp
                                            @if($isWinner)
                                                <span class="badge bg-warning"><i class="fe fe-trophy"></i> Winner!</span>
                                            @elseif($ticket->draw && $ticket->draw->status == 'completed')
                                                <span class="text-muted">No prize</span>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fe fe-ticket display-4 text-muted"></i>
                        <h5 class="mt-3">No Lottery Tickets Yet</h5>
                        <p class="text-muted">Start playing the lottery to see your activity here.</p>
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Buy Your First Ticket
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- How to Play Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-help-circle me-2"></i>How to Play</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">1</span>
                        </div>
                        <h6>Buy Tickets</h6>
                        <small class="text-muted">Purchase lottery tickets for ${{ number_format($lotterySettings->ticket_price ?? 2, 2) }} each</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">2</span>
                        </div>
                        <h6>Wait for Draw</h6>
                        <small class="text-muted">Drawings happen automatically on scheduled dates</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">3</span>
                        </div>
                        <h6>Check Results</h6>
                        <small class="text-muted">Winners are automatically selected and notified</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <span class="fw-bold">4</span>
                        </div>
                        <h6>Claim Prizes</h6>
                        <small class="text-muted">Winnings are automatically added to your wallet</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
