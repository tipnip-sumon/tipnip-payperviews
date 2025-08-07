<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🎰 Active Lottery Draws</h1> 
                        <p class="text-muted">View all current lottery draws and their details</p>
                    </div>
                    <div>
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-home me-2"></i>Lottery Home
                        </a>
                        @auth
                        <a href="{{ route('lottery.my.tickets') }}" class="btn btn-info">
                            <i class="fe fe-file-text me-2"></i>My Tickets
                        </a>
                        @endauth
                        <a href="{{ route('lottery.results') }}" class="btn btn-success">
                            <i class="fe fe-award me-2"></i>Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 fs-5"></i>
                        <strong>Success!</strong>&nbsp;{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                        <strong>Error!</strong>&nbsp;{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($message))
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <strong>Notice:</strong>&nbsp;{{ $message }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Active Draws Section -->
            @if(isset($lottery_inactive) && $lottery_inactive)
                <div class="card border-warning shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-pause-circle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-warning mb-3">Lottery System Inactive</h3>
                        <p class="text-muted mb-4">The lottery system is currently inactive. Please check back later for active draws.</p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                            <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            @else
                <!-- Active Draws Cards -->
                @if($activeDraws && $activeDraws->count() > 0)
                    <div class="row">
                        @foreach($activeDraws as $draw)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-primary shadow-sm h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-trophy me-2"></i>Draw #{{ $draw['draw_number'] }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Prize Pool:</span>
                                                <span class="h5 text-success mb-0">${{ number_format($draw['prize_pool'], 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Ticket Price:</span>
                                                <span class="fw-bold">${{ number_format($draw['ticket_price'], 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Tickets Sold:</span>
                                                <span>{{ number_format($draw['tickets_sold']) }} / {{ number_format($draw['max_tickets']) }}</span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Progress</small>
                                                <small class="text-muted">{{ number_format($draw['progress_percentage'], 1) }}%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $draw['progress_percentage'] }}%"
                                                     aria-valuenow="{{ $draw['progress_percentage'] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <!-- Draw Date -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Draw Date: {{ $draw['time_remaining'] }}
                                            </small>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="mb-3">
                                            @if($draw['status'] == 'pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-hourglass-half me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($draw['status']) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('lottery.draw.detail', $draw['id']) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                            @auth
                                                @if($draw['tickets_remaining'] > 0)
                                                    <button class="btn btn-success btn-sm" onclick="buyTicket({{ $draw['id'] }})">
                                                        <i class="fas fa-ticket-alt me-2"></i>Buy Ticket
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-times me-2"></i>Sold Out
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Buy
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card border-info shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-ticket-alt text-info" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-info mb-3">No Active Draws</h3>
                            <p class="text-muted mb-4">There are currently no active lottery draws. New draws will be announced soon!</p>
                            <a href="{{ route('lottery.results') }}" class="btn btn-info me-2">
                                <i class="fas fa-history me-2"></i>View Past Results
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
                                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@auth
<script>
function buyTicket(drawId) {
    // Implementation for buying tickets
    // This can be customized based on your existing buy ticket functionality
    window.location.href = "{{ route('lottery.index') }}?draw=" + drawId;
}
</script>
@endauth

@endsection

</x-smart_layout>
