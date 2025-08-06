<x-layout>
    @section('top_title', 'Winner Details - ' . ($winner->user->name ?? 'Winner #' . $winner->id))
    
    @section('content')
        <div class="row">
            @section('title', 'Winner Details - ' . ($winner->user->name ?? 'Winner #' . $winner->id))
            
            <!-- Back Button -->
            <div class="col-12 mb-3">
                <a href="{{ route('admin.lottery.winners') }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Winners
                </a>
            </div>

            <!-- Winner Information Card -->
            <div class="col-lg-8 mb-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="fe fe-trophy me-2"></i>Winner Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Position:</strong><br>
                                @if($winner->prize_position == 1)
                                    <span class="badge bg-warning fs-6">🥇 1st Place</span>
                                @elseif($winner->prize_position == 2)
                                    <span class="badge bg-secondary fs-6">🥈 2nd Place</span>
                                @elseif($winner->prize_position == 3)
                                    <span class="badge bg-primary fs-6">🥉 3rd Place</span>
                                @else
                                    <span class="badge bg-light text-dark fs-6">{{ $winner->prize_position }}th Place</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Prize Amount:</strong><br>
                                <span class="text-success fs-4">${{ number_format($winner->prize_amount, 2) }}</span>
                            </div>
                            @if($winner->claim_status)
                                <div class="col-md-6">
                                    <strong>Claim Status:</strong><br>
                                    @switch($winner->claim_status)
                                        @case('claimed')
                                            <span class="badge bg-success fs-6">
                                                <i class="fe fe-check"></i> Claimed
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning fs-6">
                                                <i class="fe fe-clock"></i> Pending
                                            </span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-danger fs-6">
                                                <i class="fe fe-x"></i> Expired
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary fs-6">{{ ucfirst($winner->claim_status) }}</span>
                                    @endswitch
                                </div>
                            @endif
                            @if($winner->claimed_at)
                                <div class="col-md-6">
                                    <strong>Claimed At:</strong><br>
                                    <span class="text-muted">{{ $winner->claimed_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($winner->transaction_id)
                                <div class="col-md-6">
                                    <strong>Transaction ID:</strong><br>
                                    <code>{{ $winner->transaction_id }}</code>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-user me-2"></i>Winner Profile</h5>
                    </div>
                    <div class="card-body">
                        @if($winner->user)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3">
                                    <img src="{{ $winner->user->profile_photo_url ?? asset('assets/images/users/default.png') }}" 
                                         class="avatar-img rounded-circle" alt="User Avatar">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $winner->user->name }}</h6>
                                    <small class="text-muted">{{ $winner->user->email }}</small>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <strong>User ID:</strong><br>
                                    <span class="text-muted">#{{ $winner->user->id }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Phone:</strong><br>
                                    <span class="text-muted">{{ $winner->user->phone ?? 'Not provided' }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Verification Status:</strong><br>
                                    @if($winner->user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <strong>Member Since:</strong><br>
                                    <span class="text-muted">{{ $winner->user->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.users.details', $winner->user->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-external-link"></i> View User Profile
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fe fe-user-x display-4 text-muted"></i>
                                <p class="text-muted mt-2">User information not available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ticket Information Card -->
            @if($winner->ticket)
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fe fe-ticket me-2"></i>Winning Ticket</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Ticket Number:</strong><br>
                                    <code class="fs-6">{{ $winner->ticket->ticket_number }}</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Ticket Price:</strong><br>
                                    <span class="text-success">${{ number_format($winner->ticket->ticket_price, 2) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Purchase Date:</strong><br>
                                    <span class="text-muted">{{ $winner->ticket->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>ROI:</strong><br>
                                    @php
                                        $roi = $winner->ticket->ticket_price > 0 ? 
                                            (($winner->prize_amount - $winner->ticket->ticket_price) / $winner->ticket->ticket_price) * 100 : 0;
                                    @endphp
                                    <span class="text-{{ $roi > 0 ? 'success' : 'danger' }}">
                                        {{ number_format($roi, 1) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.lottery.tickets.details', $winner->ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-external-link"></i> View Ticket Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Draw Information Card -->
            @if($winner->draw)
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fe fe-calendar me-2"></i>Draw Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Draw Number:</strong><br>
                                    <span class="text-muted">{{ $winner->draw->draw_number ?? '#' . $winner->draw->id }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Draw Date:</strong><br>
                                    <span class="text-muted">
                                        {{ $winnerStats['draw_date'] ? $winnerStats['draw_date']->format('M d, Y') : 'Not scheduled' }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Total Participants:</strong><br>
                                    <span class="text-muted">{{ number_format($winnerStats['total_participants']) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Draw Status:</strong><br>
                                    @switch($winner->draw->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('drawn')
                                            <span class="badge bg-info">Drawn</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Completed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($winner->draw->status) }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.lottery.draws.details', $winner->draw->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-external-link"></i> View Draw Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Prize Distribution Timeline -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-clock me-2"></i>Prize Distribution Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Prize Won</h6>
                                    <p class="text-muted mb-1">
                                        {{ $winner->created_at->format('M d, Y H:i') }}
                                    </p>
                                    <small class="text-success">
                                        Won ${{ number_format($winner->prize_amount, 2) }} ({{ $winner->prize_percentage }}% of prize pool)
                                    </small>
                                </div>
                            </div>
                            
                            @if($winner->claim_status === 'claimed' && $winner->claimed_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6>Prize Claimed</h6>
                                        <p class="text-muted mb-1">
                                            {{ $winner->claimed_at->format('M d, Y H:i') }}
                                        </p>
                                        <small class="text-info">
                                            Prize successfully claimed by winner
                                        </small>
                                    </div>
                                </div>
                            @elseif($winner->claim_status === 'pending')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6>Awaiting Claim</h6>
                                        <p class="text-muted mb-1">Current Status</p>
                                        <small class="text-warning">
                                            Prize awaiting claim
                                        </small>
                                    </div>
                                </div>
                            @elseif($winner->claim_status === 'expired')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6>Claim Expired</h6>
                                        <p class="text-muted mb-1">
                                            Claim period expired
                                        </p>
                                        <small class="text-danger">
                                            Prize claim period has expired
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-settings me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.lottery.winners') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to Winners
                            </a>
                            @if($winner->draw)
                                <a href="{{ route('admin.lottery.draws.details', $winner->draw->id) }}" class="btn btn-primary">
                                    <i class="fe fe-eye"></i> View Draw
                                </a>
                            @endif
                            @if($winner->ticket)
                                <a href="{{ route('admin.lottery.tickets.details', $winner->ticket->id) }}" class="btn btn-info">
                                    <i class="fe fe-ticket"></i> View Ticket
                                </a>
                            @endif
                            @if($winner->user)
                                <a href="{{ route('admin.users.details', $winner->user->id) }}" class="btn btn-warning">
                                    <i class="fe fe-user"></i> View User
                                </a>
                            @endif
                            @if($winner->claim_status === 'pending')
                                <form method="POST" action="{{ route('admin.lottery.winners.force-claim', $winner->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Are you sure you want to manually distribute this prize?')">
                                        <i class="fe fe-check"></i> Force Claim
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

@push('styles')
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
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 3px solid #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}
</style>
@endpush
</x-layout>
