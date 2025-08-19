<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="page-title">🏆 Lottery Results</h1>
                        <p class="text-muted">View lottery draw results and winners</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Buy Tickets
                        </a>
                        <a href="{{ route('lottery.my.tickets') }}" class="btn btn-info">
                            <i class="fe fe-file-text me-2"></i>My Tickets
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('lottery.results') }}" class="row g-3">
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Draws</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-xl-3 col-lg-12 col-md-6 col-12">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fe fe-filter me-2"></i>Filter
                                </button>
                                <a href="{{ route('lottery.results') }}" class="btn btn-secondary flex-fill">
                                    <i class="fe fe-refresh-cw me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Latest Draw Results -->
            @if(isset($latestDraw) && $latestDraw->status == 'completed')
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i>
                            Latest Draw Results - {{ $latestDraw->formatted_draw_number }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex flex-column">
                                            <label class="form-label small text-muted mb-1">Draw Date</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-calendar text-primary me-2"></i>
                                                <span class="fw-bold">{{ $latestDraw->draw_date->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column">
                                            <label class="form-label small text-muted mb-1">Total Tickets Sold</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-ticket text-info me-2"></i>
                                                @php
                                                    // Show actual historical ticket counts when available
                                                    $latestTotalTickets = $latestDraw->display_tickets_sold > 0 ? $latestDraw->display_tickets_sold : 
                                                                         ($latestDraw->tickets ? $latestDraw->tickets->count() : $latestDraw->total_tickets_sold);
                                                @endphp
                                                <span class="fw-bold">{{ number_format($latestTotalTickets) }}</span>
                                                @if($latestDraw->cleanup_performed)
                                                    <small class="text-muted ms-2">
                                                        <span class="badge badge-info badge-sm ms-1">Historical</span>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex flex-column">
                                            <label class="form-label small text-muted mb-1">Total Prize Pool</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-gift text-warning me-2"></i>
                                                @php
                                                    // Use the new helper method for consistent calculation
                                                    $latestCalculatedPrizePool = $latestDraw->actual_prize_pool;
                                                @endphp
                                                <span class="fw-bold text-success">${{ number_format($latestCalculatedPrizePool, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column">
                                            <label class="form-label small text-muted mb-1">Status</label>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-success">{{ ucfirst($latestDraw->status) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Winners Display -->
                        @if($latestDraw->winners && $latestDraw->winners->count() > 0)
                            <div class="mt-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-crown me-2"></i>
                                    Winners
                                </h5>
                                <div class="row g-3">
                                    @foreach($latestDraw->winners->sortBy('prize_position') as $winner)
                                        @if($winner->prize_position)
                                            <div class="col-lg-4 col-md-6 col-12">
                                                <div class="card h-100 {{ $winner->prize_position == 1 ? 'border-warning bg-light' : ($winner->prize_position == 2 ? 'border-secondary' : 'border-bronze') }}">
                                                    <div class="card-body text-center">
                                                        <div class="mb-2">
                                                            @if($winner->prize_position == 1)
                                                                <i class="fas fa-trophy text-warning" style="font-size: 2rem;"></i>
                                                            @elseif($winner->prize_position == 2)
                                                                <i class="fas fa-medal text-secondary" style="font-size: 2rem;"></i>
                                                            @else
                                                                <i class="fas fa-medal text-bronze" style="font-size: 2rem;"></i>
                                                            @endif
                                                        </div>
                                                        <h6 class="mb-1">{{ $winner->prize_position }}{{ $winner->prize_position == 1 ? 'st' : ($winner->prize_position == 2 ? 'nd' : 'rd') }} Prize</h6>
                                                        <h5 class="text-success mb-2">${{ number_format($winner->prize_amount, 2) }}</h5>
                                                        <div class="mb-2">
                                                            <small class="text-muted d-block">Winning Ticket</small>
                                                            <code class="fs-6">
                                                                @if($winner->winning_ticket_number)
                                                                    {{ $winner->winning_ticket_number }}
                                                                @elseif($winner->lotteryTicket)
                                                                    {{ $winner->lotteryTicket->ticket_number }}
                                                                @else
                                                                    <span class="text-muted">Ticket Deleted</span>
                                                                @endif
                                                            </code>
                                                        </div>
                                                        <span class="badge bg-{{ $winner->claim_status == 'claimed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($winner->claim_status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-3">
                            <a href="{{ route('lottery.draw.detail', $latestDraw->id) }}" class="btn btn-primary">
                                <i class="fe fe-eye me-2"></i>View Full Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Draw Results -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Recent Draw Results
                    </h4>
                    <div class="ms-auto">
                        @if(request('show_all') || request()->hasAny(['status', 'date_from', 'date_to']))
                            <span class="badge badge-primary">
                                @if(method_exists($draws, 'total'))
                                    {{ $draws->total() }} draws found
                                @else
                                    {{ $draws->count() }} draws found
                                @endif
                            </span>
                        @else
                            <span class="badge badge-primary">Last 4 draws</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($draws) && $draws->count() > 0)
                        <div class="row g-4">
                            @foreach($draws as $draw)
                                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                                    <div class="card h-100 {{ $draw->status == 'completed' ? 'border-success' : ($draw->status == 'pending' ? 'border-warning' : 'border-secondary') }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Draw #{{ $draw->display_draw_number }}</h6>
                                            <span class="badge bg-{{ $draw->status == 'completed' ? 'success' : ($draw->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($draw->status) }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <small class="text-muted">Draw Date</small>
                                                <div>{{ $draw->draw_date->format('M d, Y h:i A') }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Prize Pool</small>
                                                @php
                                                    // Use the new helper method for consistent calculation
                                                    $calculatedPrizePool = $draw->actual_prize_pool;
                                                @endphp
                                                <div class="fw-bold text-success">${{ number_format($calculatedPrizePool, 2) }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Tickets Sold</small>
                                                @php
                                                    $totalTickets = $draw->display_tickets_sold > 0 ? $draw->display_tickets_sold : 
                                                                   ($draw->tickets ? $draw->tickets->count() : $draw->total_tickets_sold);
                                                @endphp
                                                <div>
                                                    <span class="fw-bold">{{ number_format($totalTickets) }}</span>
                                                </div>
                                            </div>

                                            @if($draw->status == 'completed' && $draw->winners && $draw->winners->count() > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted">Winners & Tickets</small>
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($draw->winners->sortBy('prize_position') as $winner)
                                                            @if($winner->prize_position)
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span class="badge bg-{{ ($winner->prize_position ?? 0) == 1 ? 'warning' : (($winner->prize_position ?? 0) == 2 ? 'secondary' : 'info') }}">
                                                                        {{ $winner->prize_position }}{{ $winner->prize_position == 1 ? 'st' : ($winner->prize_position == 2 ? 'nd' : 'rd') }} Prize: ${{ number_format($winner->prize_amount, 2) }}
                                                                    </span>
                                                                    <small class="text-muted">
                                                                        @if($winner->winning_ticket_number)
                                                                            <code class="fs-7">{{ $winner->winning_ticket_number }}</code>
                                                                        @else
                                                                            <span class="text-muted">No Ticket</span>
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @elseif($draw->status == 'pending')
                                                <div class="mb-3">
                                                    <small class="text-muted">Time Remaining</small>
                                                    <div class="countdown" data-target="{{ $draw->draw_date->toISOString() }}">
                                                        Calculating...
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('lottery.draw.detail', $draw->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fe fe-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination for all results -->
                        @if(method_exists($draws, 'hasPages') && $draws->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $draws->withQueryString()->links() }}
                            </div>
                        @endif

                        <!-- View All Draws Link for limited view -->
                        @if(!request('show_all') && !request()->hasAny(['status', 'date_from', 'date_to']))
                            <div class="text-center mt-3">
                                <a href="{{ route('lottery.results') }}?show_all=1" class="btn btn-outline-primary">
                                    <i class="fe fe-list me-2"></i>View All Draw Results
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No Recent Draw Results</h4>
                            <p class="text-muted">No recent lottery draws found.</p>
                            <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>Buy Tickets for Next Draw
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="card" style="display:none;">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Lottery Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $statistics['total_draws'] ?? 0 }}</h4>
                                <small class="text-muted">Total Draws</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">${{ number_format($statistics['total_prizes'] ?? 0, 2) }}</h4>
                                <small class="text-muted">Total Prizes Awarded</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $statistics['total_winners'] ?? 0 }}</h4>
                                <small class="text-muted">Total Winners</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">{{ $statistics['total_tickets'] ?? 0 }}</h4>
                                <small class="text-muted">Tickets Sold</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timers for pending draws
    const countdowns = document.querySelectorAll('.countdown');
    
    countdowns.forEach(countdown => {
        const target = new Date(countdown.getAttribute('data-target')).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = target - now;
            
            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                countdown.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s";
            } else {
                countdown.innerHTML = "Draw time reached!";
            }
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
});
</script>

<style>
.text-bronze {
    color: #cd7f32;
}
.border-bronze {
    border-color: #cd7f32 !important;
}
.badge-sm {
    font-size: 0.75em;
}
.fs-7 {
    font-size: 0.8rem;
}

/* Mobile responsive improvements */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .fs-6 {
        font-size: 0.875rem !important;
    }
    
    .d-flex.gap-2 {
        gap: 0.5rem !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0.5rem;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .card-header h4 {
        font-size: 1.1rem;
    }
    
    .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
    
    /* Stack buttons vertically on very small screens */
    .d-flex.gap-2.flex-wrap {
        flex-direction: column;
    }
    
    .d-flex.gap-2.flex-wrap .btn {
        width: 100%;
    }
}
</style>
@endsection
</x-smart_layout>
