<x-smart_layout>

@section('title', 'All Activity - Lottery Center')

@section('content')
<div class="container-fluid my-4">
    
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-info text-white border-0 shadow-lg">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="h3 mb-2">📊 Complete Activity History</h2>
                            <p class="mb-0 opacity-75">Your comprehensive lottery and ticket activity timeline</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('lottery.unified.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-activity me-2"></i>Activity Timeline
                    </h5>
                </div>
                <div class="card-body">
                    @if($activities && $activities->count() > 0)
                        <div class="timeline">
                            @foreach($activities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker {{ $activity['type_class'] ?? 'bg-primary' }}">
                                        @if(isset($activity['icon']))
                                            <i class="{{ $activity['icon'] }}"></i>
                                        @elseif($activity['type'] === 'lottery_purchase')
                                            <i class="fe fe-shopping-cart"></i>
                                        @elseif($activity['type'] === 'token_received')
                                            <i class="fe fe-star"></i>
                                        @elseif($activity['type'] === 'sponsor_ticket')
                                            <i class="fe fe-users"></i>
                                        @else
                                            <i class="fe fe-activity"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">{{ $activity['title'] }}</div>
                                        <div class="timeline-text">{{ $activity['description'] }}</div>
                                        <div class="timeline-time">
                                            <small class="text-muted">
                                                <i class="fe fe-clock me-1"></i>
                                                @if(isset($activity['time_ago']))
                                                    {{ $activity['time_ago'] }}
                                                @elseif(isset($activity['created_at']))
                                                    {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                                @else
                                                    Recently
                                                @endif
                                            </small>
                                            @if(isset($activity['amount']) && $activity['amount'] > 0)
                                                <span class="badge badge-success ms-2">
                                                    ${{ number_format($activity['amount'], 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fe fe-activity display-4 text-muted mb-3"></i>
                                <h4>No Activity Yet</h4>
                                <p class="text-muted">Your lottery and ticket activities will appear here.</p>
                                <a href="{{ route('lottery.unified.index') }}" class="btn btn-primary">
                                    <i class="fe fe-play-circle me-2"></i>Start Your Lottery Journey
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fe fe-shopping-cart fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $user->lotteryTickets()->count() }}</h3>
                    <small>Lottery Tickets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fe fe-star fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $user->specialTickets()->count() }}</h3>
                    <small>Special Tokens</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fe fe-users fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $user->getCompletedTransfersCount() }}</h3>
                    <small>Transfers</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fe fe-trophy fs-1 mb-2"></i>
                    <h3 class="mb-1">${{ number_format($user->lotteryWinners()->sum('prize_amount'), 2) }}</h3>
                    <small>Total Winnings</small>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-marker.bg-success { background-color: #28a745; }
.timeline-marker.bg-warning { background-color: #ffc107; }
.timeline-marker.bg-info { background-color: #17a2b8; }
.timeline-marker.bg-primary { background-color: #007bff; }

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 8px;
    color: #6c757d;
}

.timeline-time {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}
</style>
@endsection
</x-smart_layout>
