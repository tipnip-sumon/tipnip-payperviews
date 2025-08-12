<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🏆 Lottery Results</h1>
                        <p class="text-muted">View lottery draw results and winners</p>
                    </div>
                    <div>
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
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Draws</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-filter me-2"></i>Filter
                                </button>
                                <a href="{{ route('lottery.results') }}" class="btn btn-secondary">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Draw Date</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-calendar text-primary me-2"></i>
                                        <span class="fw-bold">{{ $latestDraw->draw_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total Tickets Sold</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-ticket text-info me-2"></i>
                                        @php
                                            $latestTotalTickets = $latestDraw->tickets ? $latestDraw->tickets->count() : $latestDraw->total_tickets_sold;
                                            $latestRealTickets = $latestDraw->tickets ? $latestDraw->tickets->where('is_virtual', false)->count() : 0;
                                            $latestVirtualTickets = $latestDraw->tickets ? $latestDraw->tickets->where('is_virtual', true)->count() : 0;
                                        @endphp
                                        <span class="fw-bold">{{ $latestTotalTickets }}</span>
                                        @if($latestDraw->tickets && $latestDraw->tickets->count() > 0)
                                            <small class="text-muted ms-2">
                                                ({{ $latestRealTickets }} real + {{ $latestVirtualTickets }} virtual)
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Prize Pool</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-gift text-warning me-2"></i>
                                        @php
                                            // Use the new helper method for consistent calculation
                                            $latestCalculatedPrizePool = $latestDraw->actual_prize_pool;
                                        @endphp
                                        <span class="fw-bold text-success">${{ number_format($latestCalculatedPrizePool, 2) }}</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-success">{{ ucfirst($latestDraw->status) }}</span>
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
                                <div class="row">
                                    @foreach($latestDraw->winners->sortBy('position') as $winner)
                                        @if($winner->position)
                                            <div class="col-md-4 mb-3">
                                            <div class="card {{ ($winner->position ?? 0) == 1 ? 'border-warning bg-light' : (($winner->position ?? 0) == 2 ? 'border-secondary' : 'border-bronze') }}">
                                                <div class="card-body text-center">
                                                    <div class="mb-2">
                                                        @if(($winner->position ?? 0) == 1)
                                                            <i class="fas fa-trophy text-warning fs-2"></i>
                                                        @elseif(($winner->position ?? 0) == 2)
                                                            <i class="fas fa-medal text-secondary fs-2"></i>
                                                        @else
                                                            <i class="fas fa-medal text-bronze fs-2"></i>
                                                        @endif
                                                    </div>
                                                    <h5 class="mb-1">{{ $winner->position }} Prize</h5>
                                                    <h6 class="mb-2 text-muted">W-{{ $winner->position }}</h6>
                                                    <h4 class="text-success mb-2">${{ number_format($winner->prize_amount, 2) }}</h4>
                                                    <p class="mb-1 font-monospace">
                                                        #{{ $winner->lotteryTicket ? $winner->lotteryTicket->ticket_number : 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-3">
                            <a href="{{ route('lottery.draw.details', $latestDraw->id) }}" class="btn btn-primary">
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
                        <div class="row">
                            @foreach($draws as $draw)
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card h-100 {{ $draw->status == 'completed' ? 'border-success' : ($draw->status == 'pending' ? 'border-warning' : 'border-secondary') }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Draw #{{ $draw->display_draw_number }}</h6>
                                            <span class="badge badge-{{ $draw->status == 'completed' ? 'success' : ($draw->status == 'pending' ? 'warning' : 'secondary') }}">
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
                                                    $totalTickets = $draw->tickets ? $draw->tickets->count() : $draw->total_tickets_sold;
                                                    $realTickets = $draw->tickets ? $draw->tickets->where('is_virtual', false)->count() : 0;
                                                    $virtualTickets = $draw->tickets ? $draw->tickets->where('is_virtual', true)->count() : 0;
                                                @endphp
                                                <div>
                                                    <span class="fw-bold">{{ $totalTickets }}</span>
                                                </div>
                                            </div>

                                            @if($draw->status == 'completed' && $draw->winners && $draw->winners->count() > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted">Winners</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($draw->winners->sortBy('position') as $winner)
                                                            @if($winner->position)
                                                                <span class="badge badge-sm {{ ($winner->position ?? 0) == 1 ? 'badge-warning' : (($winner->position ?? 0) == 2 ? 'badge-secondary' : 'badge-info') }}">
                                                                    {{ $winner->position }} Prize: ${{ number_format($winner->prize_amount, 2) }}
                                                                </span>
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
                                            <a href="{{ route('lottery.draw.details', $draw->id) }}" class="btn btn-outline-primary btn-sm w-100">
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
</style>
@endsection
</x-smart_layout>
