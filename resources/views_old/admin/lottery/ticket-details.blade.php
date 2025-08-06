<x-layout>
    @section('top_title', 'Ticket Details - ' . $ticket->ticket_number)
    
    @section('content')
        <div class="row">
            @section('title', 'Ticket Details - ' . $ticket->ticket_number)
            
            <!-- Back Button -->
            <div class="col-12 mb-3">
                <a href="{{ route('admin.lottery.tickets') }}" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Back to Tickets
                </a>
            </div>

            <!-- Ticket Information Card -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-ticket me-2"></i>Ticket Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Ticket Number:</strong><br>
                                <code class="fs-5">{{ $ticket->ticket_number }}</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Ticket Price:</strong><br>
                                <span class="text-success fs-5">${{ number_format($ticket->ticket_price, 2) }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Purchase Date:</strong><br>
                                <span class="text-muted">{{ $ticketStats['purchase_date']->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong><br>
                                @if($ticketStats['is_winner'])
                                    <span class="badge bg-success fs-6">
                                        <i class="fe fe-trophy"></i> Winner
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">Regular Ticket</span>
                                @endif
                            </div>
                            @if($ticketStats['is_winner'])
                                <div class="col-md-6">
                                    <strong>Prize Amount:</strong><br>
                                    <span class="text-warning fs-5">${{ number_format($ticketStats['prize_amount'], 2) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Claim Status:</strong><br>
                                    @switch($ticketStats['claim_status'])
                                        @case('claimed')
                                            <span class="badge bg-success">Claimed</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-danger">Expired</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($ticketStats['claim_status'] ?? 'Unknown') }}</span>
                                    @endswitch
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
                        <h5 class="card-title mb-0"><i class="fe fe-user me-2"></i>Ticket Holder</h5>
                    </div>
                    <div class="card-body">
                        @if($ticket->user)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3">
                                    <img src="{{ $ticket->user->profile_photo_url ?? asset('assets/images/users/default.png') }}" 
                                         class="avatar-img rounded-circle" alt="User Avatar">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $ticket->user->name }}</h6>
                                    <small class="text-muted">{{ $ticket->user->email }}</small>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <strong>User ID:</strong><br>
                                    <span class="text-muted">#{{ $ticket->user->id }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Phone:</strong><br>
                                    <span class="text-muted">{{ $ticket->user->phone ?? 'Not provided' }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Member Since:</strong><br>
                                    <span class="text-muted">{{ $ticket->user->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.users.details', $ticket->user->id) }}" class="btn btn-sm btn-outline-primary">
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

            <!-- Draw Information Card -->
            @if($ticket->draw)
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fe fe-calendar me-2"></i>Draw Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Draw Number:</strong><br>
                                    <span class="text-muted">{{ $ticket->draw->draw_number ?? '#' . $ticket->draw->id }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Draw Date:</strong><br>
                                    <span class="text-muted">
                                        {{ $ticketStats['draw_date'] ? $ticketStats['draw_date']->format('M d, Y') : 'Not scheduled' }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Max Tickets:</strong><br>
                                    <span class="text-muted">{{ number_format($ticket->draw->max_tickets) }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Draw Status:</strong><br>
                                    @switch($ticket->draw->status)
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
                                            <span class="badge bg-secondary">{{ ucfirst($ticket->draw->status) }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.lottery.draws.details', $ticket->draw->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fe fe-external-link"></i> View Draw Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Winning Details Card (if winner) -->
            @if($ticketStats['is_winner'] && $ticket->winners->count() > 0)
                <div class="col-lg-6 mb-4">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i class="fe fe-trophy me-2"></i>Winning Details</h5>
                        </div>
                        <div class="card-body">
                            @foreach($ticket->winners as $winner)
                                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <strong>Position:</strong><br>
                                            @if($winner->position == 1)
                                                <span class="badge bg-warning">🥇 1st Place</span>
                                            @elseif($winner->position == 2)
                                                <span class="badge bg-secondary">🥈 2nd Place</span>
                                            @elseif($winner->position == 3)
                                                <span class="badge bg-primary">🥉 3rd Place</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $winner->position }}th Place</span>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <strong>Prize Amount:</strong><br>
                                            <span class="text-success fs-6">${{ number_format($winner->prize_amount, 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Prize Percentage:</strong><br>
                                            <span class="text-muted">{{ $winner->prize_percentage }}%</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Claim Status:</strong><br>
                                            @switch($winner->claim_status)
                                                @case('claimed')
                                                    <span class="badge bg-success">Claimed</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('expired')
                                                    <span class="badge bg-danger">Expired</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($winner->claim_status) }}</span>
                                            @endswitch
                                        </div>
                                        @if($winner->claim_deadline)
                                            <div class="col-12">
                                                <strong>Claim Deadline:</strong><br>
                                                <span class="text-muted">{{ $winner->claim_deadline->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-settings me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.lottery.tickets') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to Tickets
                            </a>
                            @if($ticket->draw)
                                <a href="{{ route('admin.lottery.draws.details', $ticket->draw->id) }}" class="btn btn-primary">
                                    <i class="fe fe-eye"></i> View Draw
                                </a>
                            @endif
                            @if($ticket->user)
                                <a href="{{ route('admin.users.details', $ticket->user->id) }}" class="btn btn-info">
                                    <i class="fe fe-user"></i> View User
                                </a>
                            @endif
                            @if($ticketStats['is_winner'])
                                <button class="btn btn-success" onclick="alert('Prize management features coming soon!')">
                                    <i class="fe fe-trophy"></i> Manage Prize
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-layout>
