<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🎰 Draw #{{ $draw->display_draw_number ?? 'N/A' }} Details</h1>
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
                                Draw #{{ $draw->display_draw_number }} Overview
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
                                    <label class="form-label text-muted">Ticket Price</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-tag text-info me-2"></i>
                                        <span class="fw-bold text-primary fs-5">
                                            @php
                                                // Get individual ticket price
                                                $ticketPrice = 2.00; // Default ticket price
                                                
                                                // Try to get from a ticket in this draw
                                                $sampleTicket = $draw->tickets()->first();
                                                if ($sampleTicket && $sampleTicket->ticket_price) {
                                                    $ticketPrice = $sampleTicket->ticket_price;
                                                } else {
                                                    // Try to get from lottery settings
                                                    try {
                                                        $settings = \App\Models\LotterySetting::first();
                                                        if ($settings && $settings->ticket_price) {
                                                            $ticketPrice = $settings->ticket_price;
                                                        }
                                                    } catch (Exception $e) {
                                                        // Keep default if there's an error
                                                    }
                                                }
                                            @endphp
                                            ${{ number_format($ticketPrice, 2) }}
                                        </span>
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
                    </div>
                    <div class="card-body">
                        @php
                            // Simplified prize structure - just use default fixed amounts
                            $settingsFirstPrize = 1000;
                            $settingsSecondPrize = 300;
                            $settingsThirdPrize = 100;
                        @endphp
                        
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
                                            // Get actual winners count and prize distribution
                                            if ($draw->status == 'completed') {
                                                $firstWinners = $draw->winners()->where('prize_position', 1)->get();
                                                $firstWinnersCount = $firstWinners->count();
                                            } else {
                                                // Check prize distribution for expected winners
                                                $firstWinnersCount = 0;
                                                if (!empty($prizeDistribution)) {
                                                    $position1Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 1;
                                                    });
                                                    $firstWinnersCount = count($position1Prizes);
                                                }
                                                if ($firstWinnersCount == 0) {
                                                    $firstWinnersCount = 1; // Default to 1 winner
                                                }
                                            }
                                            
                                            // Show prize distribution details
                                            if ($draw->status == 'completed' && $firstWinnersCount > 1) {
                                                // Show actual individual prize amounts from prize_distribution or winners table
                                                if (!empty($prizeDistribution)) {
                                                    // Use prize_distribution data
                                                    $position1Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 1;
                                                    });
                                                    $prizeAmounts = array_column($position1Prizes, 'amount');
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                } else {
                                                    // Use actual winner prize amounts from database
                                                    $prizeAmounts = $firstWinners->pluck('prize_amount')->toArray();
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                }
                                            }
                                        @endphp
                                        
                                        <!-- Winner Count Display -->
                                        <div class="winner-count mb-2">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $firstWinnersCount }} {{ $firstWinnersCount == 1 ? 'Winner' : 'Winners' }}
                                            </span>
                                        </div>
                                        
                                        @if($draw->status == 'completed' && $firstWinnersCount > 1)
                                            <div class="prize-breakdown">{{ $prizeList }}</div>
                                        @else
                                            <div class="prize-info">Fixed Amount: ${{ number_format($settingsFirstPrize, 0) }}</div>
                                        @endif
                                    </div>
                                    @if($draw->firstPrizeWinner && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                @if($draw->firstPrizeWinner->winning_ticket_number)
                                                    Ticket #{{ $draw->firstPrizeWinner->winning_ticket_number }}
                                                @elseif($draw->firstPrizeWinner->lotteryTicket)
                                                    Ticket #{{ $draw->firstPrizeWinner->lotteryTicket->ticket_number }}
                                                @else
                                                    Ticket #N/A (Historical)
                                                @endif
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
                                            // Show actual prize distribution for completed draws
                                            if ($draw->status == 'completed') {
                                                $secondWinners = $draw->winners()->where('prize_position', 2)->get();
                                                $secondWinnersCount = $secondWinners->count();
                                            } else {
                                                // Check prize distribution for expected winners
                                                $secondWinnersCount = 0;
                                                if (!empty($prizeDistribution)) {
                                                    $position2Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 2;
                                                    });
                                                    $secondWinnersCount = count($position2Prizes);
                                                }
                                                if ($secondWinnersCount == 0) {
                                                    $secondWinnersCount = 1; // Default to 1 winner
                                                }
                                            }
                                            
                                            // Show prize distribution details for multiple winners
                                            if ($draw->status == 'completed' && $secondWinnersCount > 1) {
                                                // Show actual individual prize amounts from prize_distribution or winners table
                                                if (!empty($prizeDistribution)) {
                                                    // Use prize_distribution data
                                                    $position2Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 2;
                                                    });
                                                    $prizeAmounts = array_column($position2Prizes, 'amount');
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                } else {
                                                    // Use actual winner prize amounts from database
                                                    $prizeAmounts = $secondWinners->pluck('prize_amount')->toArray();
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                }
                                            }
                                        @endphp
                                        
                                        <!-- Winner Count Display -->
                                        <div class="winner-count mb-2">
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $secondWinnersCount }} {{ $secondWinnersCount == 1 ? 'Winner' : 'Winners' }}
                                            </span>
                                        </div>
                                        
                                        @if($draw->status == 'completed' && $secondWinnersCount > 1)
                                            <div class="prize-breakdown">{{ $prizeList }}</div>
                                        @else
                                            <div class="prize-info">Fixed Amount: ${{ number_format($settingsSecondPrize, 0) }}</div>
                                        @endif
                                    </div>
                                    @if($draw->secondPrizeWinner && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                @if($draw->secondPrizeWinner->winning_ticket_number)
                                                    Ticket #{{ $draw->secondPrizeWinner->winning_ticket_number }}
                                                @elseif($draw->secondPrizeWinner->lotteryTicket)
                                                    Ticket #{{ $draw->secondPrizeWinner->lotteryTicket->ticket_number }}
                                                @else
                                                    Ticket #N/A (Historical)
                                                @endif
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
                                            // Show actual prize distribution for completed draws
                                            if ($draw->status == 'completed') {
                                                $thirdWinners = $draw->winners()->where('prize_position', 3)->get();
                                                $thirdWinnersCount = $thirdWinners->count();
                                            } else {
                                                // Check prize distribution for expected winners
                                                $thirdWinnersCount = 0;
                                                if (!empty($prizeDistribution)) {
                                                    $position3Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 3;
                                                    });
                                                    $thirdWinnersCount = count($position3Prizes);
                                                }
                                                if ($thirdWinnersCount == 0) {
                                                    $thirdWinnersCount = 1; // Default to 1 winner
                                                }
                                            }
                                            
                                            // Show prize distribution details for multiple winners
                                            if ($draw->status == 'completed' && $thirdWinnersCount > 1) {
                                                // Show actual individual prize amounts from prize_distribution or winners table
                                                if (!empty($prizeDistribution)) {
                                                    // Use prize_distribution data
                                                    $position3Prizes = array_filter($prizeDistribution, function($prize) {
                                                        return isset($prize['position']) && $prize['position'] == 3;
                                                    });
                                                    $prizeAmounts = array_column($position3Prizes, 'amount');
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                } else {
                                                    // Use actual winner prize amounts from database
                                                    $prizeAmounts = $thirdWinners->pluck('prize_amount')->toArray();
                                                    $prizeList = '$' . implode(' + $', array_map(function($amount) {
                                                        return number_format($amount, 0);
                                                    }, $prizeAmounts));
                                                }
                                            }
                                        @endphp
                                        
                                        <!-- Winner Count Display -->
                                        <div class="winner-count mb-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $thirdWinnersCount }} {{ $thirdWinnersCount == 1 ? 'Winner' : 'Winners' }}
                                            </span>
                                        </div>
                                        
                                        @if($draw->status == 'completed' && $thirdWinnersCount > 1)
                                            <div class="prize-breakdown">{{ $prizeList }}</div>
                                        @else
                                            <div class="prize-info">Fixed Amount: ${{ number_format($settingsThirdPrize, 0) }}</div>
                                        @endif
                                    </div>
                                    @if($draw->thirdPrizeWinner && $draw->status == 'completed')
                                        <div class="winner-info">
                                            <div class="winner-badge">
                                                <i class="fas fa-check-circle"></i> WON
                                            </div>
                                            <div class="ticket-info">
                                                @if($draw->thirdPrizeWinner->winning_ticket_number)
                                                    Ticket #{{ $draw->thirdPrizeWinner->winning_ticket_number }}
                                                @elseif($draw->thirdPrizeWinner->lotteryTicket)
                                                    Ticket #{{ $draw->thirdPrizeWinner->lotteryTicket->ticket_number }}
                                                @else
                                                    Ticket #N/A (Historical)
                                                @endif
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
                            @foreach($draw->winners->sortBy('prize_position') as $winner)
                                <div class="d-flex align-items-center mb-3 p-3 border rounded {{ $winner->prize_position == 1 ? 'border-warning bg-warning-light' : ($winner->prize_position == 2 ? 'border-secondary' : 'border-bronze') }}">
                                    <div class="avatar {{ $winner->prize_position == 1 ? 'bg-warning' : ($winner->prize_position == 2 ? 'bg-secondary' : 'bg-bronze') }} me-3">
                                        <i class="fas fa-{{ $winner->prize_position == 1 ? 'trophy' : 'medal' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $winner->prize_position }}{{ $winner->prize_position == 1 ? 'st' : ($winner->prize_position == 2 ? 'nd' : 'rd') }} Prize Winner</h6>
                                        <div class="text-muted">
                                            @if($winner->winning_ticket_number)
                                                <span>Ticket #{{ $winner->winning_ticket_number }}</span>
                                            @elseif($winner->lotteryTicket && $winner->lotteryTicket->ticket_number)
                                                <span>Ticket #{{ $winner->lotteryTicket->ticket_number }}</span>
                                            @else
                                                <span class="text-muted">Ticket #N/A (Historical)</span>
                                            @endif
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

.winner-count {
    margin-bottom: 10px;
}

.winner-count .badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.prize-breakdown {
    font-size: 12px;
    color: #28a745;
    font-weight: 600;
    background: rgba(40, 167, 69, 0.1);
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.prize-info {
    font-size: 12px;
    color: #6c757d;
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
