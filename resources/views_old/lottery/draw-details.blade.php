<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🎰 Draw #{{ $draw->id ?? 'N/A' }} Details</h1>
                        <p class="text-muted">Complete information about this lottery draw</p>
                    </div>
                    <div>
                        <a href="{{ route('lottery.results') }}" class="btn btn-secondary">
                            <i class="fe fe-arrow-left me-2"></i>Back to Results
                        </a>
                        @if(isset($draw) && $draw->status == 'pending')
                            <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>Buy Tickets
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(isset($draw))
                <!-- Draw Overview -->
                <div class="card border-{{ $draw->status == 'completed' ? 'success' : ($draw->status == 'pending' ? 'warning' : 'secondary') }}">
                    <div class="card-header bg-{{ $draw->status == 'completed' ? 'success' : ($draw->status == 'pending' ? 'warning' : 'secondary') }} text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-{{ $draw->status == 'completed' ? 'check-circle' : ($draw->status == 'pending' ? 'clock' : 'times-circle') }} me-2"></i>
                                Draw #{{ $draw->id }} Overview
                            </h4>
                            <span class="badge bg-{{ $draw->status == 'completed' ? 'light text-success' : ($draw->status == 'pending' ? 'light text-warning' : 'light text-secondary') }}">
                                {{ ucfirst($draw->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Draw Date & Time</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-calendar text-primary me-2"></i>
                                        <span class="fw-bold">{{ $draw->draw_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Total Prize Pool</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-gift text-warning me-2"></i>
                                        <span class="fw-bold text-success fs-5">
                                            @php
                                                // Calculate total prize pool based on lottery settings and draw data
                                                $totalRevenue = $draw->total_tickets_sold * ($settings->ticket_price ?? 2);
                                                $displayPrizePool = 0;
                                                $calculationMethod = 'fallback';
                                                
                                                // Priority 1: Calculate from lottery settings first (most reliable)
                                                // Force refresh settings to ensure we get the latest data
                                                $freshSettings = \App\Models\LotterySetting::getSettings();
                                                $condition1 = $settings && isset($settings->prize_structure);
                                                $condition1_fresh = $freshSettings && isset($freshSettings->prize_structure);
                                                
                                                // Use fresh settings if the passed settings has null prize_structure
                                                $workingSettings = ($condition1_fresh && !$condition1) ? $freshSettings : $settings;
                                                $workingCondition = $workingSettings && isset($workingSettings->prize_structure);
                                                
                                                // Initialize prize amounts from settings
                                                $settingsFirstPrize = 0;
                                                $settingsSecondPrize = 0;
                                                $settingsThirdPrize = 0;
                                                
                                                if ($workingCondition && $workingSettings->prize_structure) {
                                                    // Handle prize_structure whether it's array or JSON string
                                                    $prizeStructure = is_array($workingSettings->prize_structure) 
                                                        ? $workingSettings->prize_structure 
                                                        : json_decode($workingSettings->prize_structure, true);
                                                    
                                                    if (is_array($prizeStructure)) {
                                                        // Extract individual prize amounts
                                                        if (isset($prizeStructure[1]['amount'])) {
                                                            $settingsFirstPrize = (float)$prizeStructure[1]['amount'];
                                                        }
                                                        if (isset($prizeStructure[2]['amount'])) {
                                                            $settingsSecondPrize = (float)$prizeStructure[2]['amount'];
                                                        }
                                                        if (isset($prizeStructure[3]['amount'])) {
                                                            $settingsThirdPrize = (float)$prizeStructure[3]['amount'];
                                                        }
                                                        
                                                        if ($workingSettings->prize_distribution_type === 'percentage') {
                                                            // Calculate based on percentage of total revenue
                                                            $totalPrizePercentage = 0;
                                                            foreach ($prizeStructure as $prize) {
                                                                $totalPrizePercentage += isset($prize['percentage']) ? (float)$prize['percentage'] : 0;
                                                            }
                                                            $displayPrizePool = ($totalRevenue * $totalPrizePercentage) / 100;
                                                            $calculationMethod = 'percentage_settings';
                                                        } else {
                                                            // Use fixed amounts from settings (default behavior)
                                                            $displayPrizePool = $settingsFirstPrize + $settingsSecondPrize + $settingsThirdPrize;
                                                            $calculationMethod = 'fixed_settings';
                                                        }
                                                    }
                                                }
                                                
                                                // Priority 2: Use actual draw prize data if available and settings didn't work
                                                if ($calculationMethod === 'fallback' && (($draw->first_prize ?? 0) > 0 || ($draw->second_prize ?? 0) > 0 || ($draw->third_prize ?? 0) > 0)) {
                                                    $displayPrizePool = ($draw->first_prize ?? 0) + ($draw->second_prize ?? 0) + ($draw->third_prize ?? 0);
                                                    $calculationMethod = 'draw_prizes';
                                                }
                                                // Priority 3: Use stored total prize pool from draw
                                                if ($calculationMethod === 'fallback' && ($draw->total_prize_pool ?? 0) > 0) {
                                                    $displayPrizePool = $draw->total_prize_pool;
                                                    $calculationMethod = 'stored_total';
                                                }
                                                // Priority 4: Default calculation (80% of revenue)
                                                if ($displayPrizePool <= 0) {
                                                    $displayPrizePool = $totalRevenue * 0.8;
                                                    $calculationMethod = 'default_percentage';
                                                }
                                            @endphp
                                            ${{ number_format($displayPrizePool, 2) }}
                                        </span>
                                        @if($calculationMethod === 'draw_prizes')
                                            <small class="text-muted ms-2">
                                                (From individual prize amounts)
                                            </small>
                                        @elseif($calculationMethod === 'stored_total')
                                            <small class="text-muted ms-2">
                                                (From draw total)
                                            </small>
                                        @elseif($calculationMethod === 'percentage_settings')
                                            <small class="text-muted ms-2">
                                                ({{ number_format(($displayPrizePool / max($totalRevenue, 1)) * 100, 1) }}% of ${{ number_format($totalRevenue, 2) }} revenue)
                                            </small>
                                        @elseif($calculationMethod === 'fixed_settings')
                                            <small class="text-muted ms-2">
                                                (Fixed amounts from settings)
                                            </small>
                                        @else
                                            <small class="text-muted ms-2">
                                                (Default: 80% of revenue)
                                            </small>
                                        @endif
                                        

                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($draw->status == 'pending')
                            <!-- Countdown Timer -->
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Time Remaining:</strong>
                                    </div>
                                    <div id="countdown" class="fw-bold text-warning fs-5"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Prize Breakdown -->
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="card-title text-white mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Prize Breakdown & Distribution
                        </h4>
                        <small class="text-white-50">
                            Total Prize Pool: ${{ number_format($displayPrizePool, 2) }}
                            @if($calculationMethod === 'draw_prizes')
                                | From individual prize amounts
                            @elseif($calculationMethod === 'stored_total')
                                | From draw total
                            @elseif($calculationMethod === 'percentage_settings')
                                | {{ number_format(($displayPrizePool / max($totalRevenue, 1)) * 100, 1) }}% of ${{ number_format($totalRevenue, 2) }} revenue
                            @elseif($calculationMethod === 'fixed_settings')
                                @php
                                    $prizeStructureForDisplay = null;
                                    if (isset($workingSettings) && $workingSettings->prize_structure) {
                                        $prizeStructureForDisplay = is_array($workingSettings->prize_structure) 
                                            ? $workingSettings->prize_structure 
                                            : json_decode($workingSettings->prize_structure, true);
                                    }
                                    $fixedTotal = 0;
                                    if (is_array($prizeStructureForDisplay)) {
                                        $fixedTotal = array_sum(array_column($prizeStructureForDisplay, 'amount'));
                                    }
                                @endphp
                                | Fixed amounts: ${{ number_format($fixedTotal, 2) }}
                            @else
                                | Default calculation (80% of revenue)
                            @endif
                        </small>
                    </div>
                    <div class="card-body">
                        <!-- Enhanced Prize Cards -->
                        <div class="row g-4">
                            <!-- 1st Prize -->
                            <div class="col-md-4">
                                <div class="prize-card gold {{ $draw->first_prize_winner_id ? 'winner-selected' : '' }}">
                                    <div class="prize-header">
                                        <div class="prize-icon">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <div class="prize-rank">
                                            <span class="rank-number">1st</span>
                                            <span class="rank-label">PLACE</span>
                                        </div>
                                    </div>
                                    <div class="prize-amount">
                                        <span class="currency">$</span>
                                        <span class="amount">{{ number_format($settingsFirstPrize, 2) }}</span>
                                    </div>
                                    <div class="prize-percentage">
                                        @php
                                            $firstPrizePercentage = $displayPrizePool > 0 ? ($settingsFirstPrize / $displayPrizePool) * 100 : 0;
                                        @endphp
                                        {{ number_format($firstPrizePercentage, 1) }}% of total pool
                                    </div>
                                    @if($draw->first_prize_winner_id && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                Ticket #{{ $draw->firstPrizeWinner->lotteryTicket->ticket_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="pending-status">
                                            <span class="status-badge">{{ $draw->status == 'pending' ? 'AWAITING DRAW' : 'NO WINNER' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 2nd Prize -->
                            <div class="col-md-4">
                                <div class="prize-card silver {{ $draw->second_prize_winner_id ? 'winner-selected' : '' }}">
                                    <div class="prize-header">
                                        <div class="prize-icon">
                                            <i class="fas fa-medal"></i>
                                        </div>
                                        <div class="prize-rank">
                                            <span class="rank-number">2nd</span>
                                            <span class="rank-label">PLACE</span>
                                        </div>
                                    </div>
                                    <div class="prize-amount">
                                        <span class="currency">$</span>
                                        <span class="amount">{{ number_format($settingsSecondPrize, 2) }}</span>
                                    </div>
                                    <div class="prize-percentage">
                                        @php
                                            $secondPrizePercentage = $displayPrizePool > 0 ? ($settingsSecondPrize / $displayPrizePool) * 100 : 0;
                                        @endphp
                                        {{ number_format($secondPrizePercentage, 1) }}% of total pool
                                    </div>
                                    @if($draw->second_prize_winner_id && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                Ticket #{{ $draw->secondPrizeWinner->lotteryTicket->ticket_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="pending-status">
                                            <span class="status-badge">{{ $draw->status == 'pending' ? 'AWAITING DRAW' : 'NO WINNER' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 3rd Prize -->
                            <div class="col-md-4">
                                <div class="prize-card bronze {{ $draw->third_prize_winner_id ? 'winner-selected' : '' }}">
                                    <div class="prize-header">
                                        <div class="prize-icon">
                                            <i class="fas fa-award"></i>
                                        </div>
                                        <div class="prize-rank">
                                            <span class="rank-number">3rd</span>
                                            <span class="rank-label">PLACE</span>
                                        </div>
                                    </div>
                                    <div class="prize-amount">
                                        <span class="currency">$</span>
                                        <span class="amount">{{ number_format($settingsThirdPrize, 2) }}</span>
                                    </div>
                                    <div class="prize-percentage">
                                        @php
                                            $thirdPrizePercentage = $displayPrizePool > 0 ? ($settingsThirdPrize / $displayPrizePool) * 100 : 0;
                                        @endphp
                                        {{ number_format($thirdPrizePercentage, 1) }}% of total pool
                                    </div>
                                    @if($draw->third_prize_winner_id && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                Ticket #{{ $draw->thirdPrizeWinner->lotteryTicket->ticket_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="pending-status">
                                            <span class="status-badge">{{ $draw->status == 'pending' ? 'AWAITING DRAW' : 'NO WINNER' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Prize Distribution Summary -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="prize-summary bg-light border">
                                    <h6 class="mb-3 text-dark">
                                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                                        Prize Distribution Summary
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="stat-item bg-white rounded p-3 shadow-sm">
                                                <div class="stat-value text-success fw-bold">${{ number_format($displayPrizePool, 2) }}</div>
                                                <div class="stat-label text-dark">Total Prize Pool</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item bg-white rounded p-3 shadow-sm">
                                                <div class="stat-value text-primary fw-bold">${{ number_format($totalRevenue, 2) }}</div>
                                                <div class="stat-label text-dark">Ticket Price</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item bg-white rounded p-3 shadow-sm">
                                                <div class="stat-value text-info fw-bold">
                                                    @php
                                                        $distributedAmount = $settingsFirstPrize + $settingsSecondPrize + $settingsThirdPrize;
                                                    @endphp
                                                    ${{ number_format($distributedAmount, 2) }}
                                                </div>
                                                <div class="stat-label text-dark">Total Distributed</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item bg-white rounded p-3 shadow-sm">
                                                <div class="stat-value text-warning fw-bold">
                                                    @php
                                                        // More realistic prize ratio calculation
                                                        if ($totalRevenue > 0 && $displayPrizePool > 0) {
                                                            $prizeRatio = ($displayPrizePool / $totalRevenue) * 100;
                                                            // Cap the ratio at 100% for display purposes
                                                            $displayRatio = min($prizeRatio, 100);
                                                        } else {
                                                            $displayRatio = 0;
                                                        }
                                                    @endphp
                                                    {{ number_format($displayRatio, 1) }}%
                                                    @if($displayRatio >= 100 && $totalRevenue > 0)
                                                        <small class="d-block text-muted" style="font-size: 10px;">
                                                            (Actual: {{ number_format(($displayPrizePool / $totalRevenue) * 100, 0) }}%)
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="stat-label text-dark">Prize Ratio</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Winners (if draw is completed) -->
                @if($draw->status == 'completed' && $draw->winners && $draw->winners->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-crown me-2"></i>
                                Winners
                            </h4>
                        </div>
                        <div class="card-body">
                            @foreach($draw->winners->sortBy('position') as $winner)
                                <div class="d-flex align-items-center mb-3 p-3 border rounded {{ $winner->position == 1 ? 'border-warning bg-warning-light' : ($winner->position == 2 ? 'border-secondary' : 'border-bronze') }}">
                                    <div class="avatar {{ $winner->position == 1 ? 'bg-warning' : ($winner->position == 2 ? 'bg-secondary' : 'bg-bronze') }} me-3">
                                        <i class="fas fa-{{ $winner->position == 1 ? 'trophy' : 'medal' }}"></i>
                                    </div>>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $winner->position }}{{ $winner->position == 1 ? 'st' : ($winner->position == 2 ? 'nd' : 'rd') }} Prize Winner</h6>
                                        <div class="text-muted">
                                            <span>Ticket #{{ $winner->lotteryTicket->ticket_number }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="text-success mb-0">${{ number_format($winner->prize_amount, 2) }}</h5>
                                        <span class="badge badge-{{ $winner->claim_status == 'claimed' ? 'success' : ($winner->claim_status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($winner->claim_status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- All Tickets -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">
                                <i class="fas fa-ticket-alt me-2"></i>
                                All Tickets ({{ $draw->tickets()->count() }})
                            </h4>
                            <div>
                                <span class="badge badge-info">{{ $draw->tickets()->count() }} tickets</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($draw->tickets && $draw->tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Draw Time</th>
                                            <th>Ticket Price</th>
                                            <th>Status</th>
                                            <th>Winner Prize</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($draw->tickets()->with(['user', 'winner'])->get() as $ticket)
                                            <tr class="{{ $ticket->winner ? 'table-success' : '' }}">
                                                <td>
                                                    <span class="fw-bold {{ $ticket->winner ? 'text-success' : 'text-primary' }}">
                                                        #{{ $ticket->ticket_number }}
                                                        @if($ticket->winner)
                                                            <i class="fas fa-trophy text-warning ms-1"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $draw->draw_date->format('M d, Y h:i A') }}
                                                </td>
                                                <td>
                                                    <span class="text-success">${{ number_format($ticket->ticket_price, 2) }}</span>
                                                </td>
                                                <td>
                                                    @if($ticket->winner)
                                                        <span class="badge bg-success">Winner</span>
                                                    @elseif($draw->status == 'completed')
                                                        <span class="badge bg-secondary">No Prize</span>
                                                    @else
                                                        <span class="badge bg-info">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ticket->winner)
                                                        <span class="text-success fw-bold">
                                                            ${{ number_format($ticket->winner->prize_amount, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-ticket-alt text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No Tickets Sold</h5>
                                <p class="text-muted">No tickets have been sold for this draw yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @auth
                    <!-- Your Tickets for This Draw -->
                    @php
                        $userTickets = $draw->tickets()->where('user_id', auth()->id())->get();
                    @endphp
                    @if($userTickets && $userTickets->count() > 0)
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    Your Tickets for This Draw ({{ $userTickets->count() }})
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($userTickets as $ticket)
                                        <div class="col-md-3 mb-3">
                                            <div class="card {{ $ticket->winner ? 'bg-success text-white' : 'bg-primary text-white' }}">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">Ticket #{{ $ticket->ticket_number }}</h5>
                                                    <p class="mb-1">{{ $ticket->purchased_at->format('M d, h:i A') }}</p>
                                                    @if($ticket->winner)
                                                        <div class="mt-2">
                                                            <i class="fas fa-trophy fs-4"></i>
                                                            <div class="fw-bold">Winner!</div>
                                                            <div>${{ number_format($ticket->winner->prize_amount, 2) }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">Draw Not Found</h4>
                        <p class="text-muted">The requested lottery draw could not be found.</p>
                        <a href="{{ route('lottery.results') }}" class="btn btn-primary">
                            <i class="fe fe-arrow-left me-2"></i>Back to Results
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if(isset($draw) && $draw->status == 'pending')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown Timer
    const drawDate = new Date('{{ $draw->draw_date->toISOString() }}').getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = drawDate - now;
        
        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').innerHTML = 
                days + "d " + hours + "h " + minutes + "m " + seconds + "s";
        } else {
            document.getElementById('countdown').innerHTML = "Draw Time Reached!";
            setTimeout(() => {
                location.reload();
            }, 5000);
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endif

<style>
.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
.bg-bronze {
    background-color: #cd7f32;
}
.text-bronze {
    color: #cd7f32;
}
.border-bronze {
    border-color: #cd7f32 !important;
}
.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
}
</style>
@endsection

@push('styles')
<style>
/* Enhanced Prize Card Styles */
.prize-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

.prize-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.prize-card.winner-selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #f8fff9 100%);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
}

/* Gold Prize (1st Place) */
.prize-card.gold {
    border-color: #ffd700;
    background: linear-gradient(135deg, #fff8dc 0%, #fffbf0 100%);
}
.prize-card.gold .prize-icon {
    color: #ffd700;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
}
.prize-card.gold .rank-number {
    color: #b8860b;
}

/* Silver Prize (2nd Place) */
.prize-card.silver {
    border-color: #c0c0c0;
    background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
}
.prize-card.silver .prize-icon {
    color: #c0c0c0;
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
}
.prize-card.silver .rank-number {
    color: #808080;
}

/* Bronze Prize (3rd Place) */
.prize-card.bronze {
    border-color: #cd7f32;
    background: linear-gradient(135deg, #fdf6f0 0%, #fef9f5 100%);
}
.prize-card.bronze .prize-icon {
    color: #cd7f32;
    background: linear-gradient(135deg, #cd7f32, #e8995a);
}
.prize-card.bronze .rank-number {
    color: #a0522d;
}

.prize-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.prize-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    background: linear-gradient(135deg, #6c757d, #adb5bd);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.prize-rank {
    text-align: right;
}

.rank-number {
    display: block;
    font-size: 24px;
    font-weight: 800;
    color: #495057;
    line-height: 1;
}

.rank-label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 1px;
}

.prize-amount {
    margin: 20px 0;
}

.prize-amount .currency {
    font-size: 18px;
    color: #28a745;
    font-weight: 600;
}

.prize-amount .amount {
    font-size: 32px;
    font-weight: 800;
    color: #28a745;
    display: block;
    line-height: 1;
}

.prize-percentage {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 15px;
    font-weight: 500;
}

.winner-info {
    margin-top: 15px;
}

.winner-badge {
    background: #28a745;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 8px;
}

.ticket-info {
    font-size: 11px;
    color: #6c757d;
    font-family: monospace;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.pending-status .status-badge {
    background: #6c757d;
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.prize-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid #e9ecef;
    border-radius: 15px;
    padding: 25px;
    margin-top: 20px;
}

.stat-item {
    padding: 10px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .prize-card {
        margin-bottom: 20px;
        padding: 20px 15px;
    }
    
    .prize-amount .amount {
        font-size: 28px;
    }
    
    .prize-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .rank-number {
        font-size: 20px;
    }
    
    .stat-value {
        font-size: 20px;
    }
}

/* Animation for winner cards */
.winner-selected {
    animation: winnerGlow 2s ease-in-out infinite alternate;
}

@keyframes winnerGlow {
    from {
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    }
    to {
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4), 0 0 20px rgba(40, 167, 69, 0.1);
    }
}
</style>
@endpush
</x-smart_layout>
