<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🎰 Lottery System</h1> 
                        <p class="text-muted">Buy lottery tickets and win amazing prizes!</p>
                    </div>
                    <div>
                        <a href="{{ route('lottery.my.tickets') }}" class="btn btn-info">
                            <i class="fe fe-file-text me-2"></i>My Tickets
                        </a>
                        <a href="{{ route('lottery.results') }}" class="btn btn-success">
                            <i class="fe fe-award me-2"></i>Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="successAlert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 fs-5"></i>
                        <strong>Success!</strong>&nbsp;{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="errorAlert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                        <strong>Error!</strong>&nbsp;{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" id="validationAlert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle me-2 fs-5 mt-1"></i>
                        <div class="flex-grow-1">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($lottery_inactive) && $lottery_inactive)
                <!-- Lottery Inactive State -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-warning">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-pause-circle display-1 text-warning"></i>
                                </div>
                                <h2 class="text-warning mb-3">Lottery Currently Inactive</h2>
                                <p class="lead text-muted mb-4">{{ $message ?? 'The lottery system is temporarily inactive. Please check back later for new draws and exciting prizes!' }}</p>
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Notice:</strong> All existing tickets remain valid and will be processed when the lottery resumes.
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('lottery.results') }}" class="btn btn-outline-primary me-2">
                                        <i class="fe fe-award me-2"></i>View Past Results
                                    </a>
                                    <a href="{{ route('lottery.statistics') }}" class="btn btn-outline-info">
                                        <i class="fe fe-bar-chart-2 me-2"></i>View Statistics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            <!-- Active Lottery Content -->

            <div class="row">
                <!-- Current Draw Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Current Lottery Draw
                            </h4>
                            @if(isset($currentDraw))
                                <span class="badge bg-primary ms-2">{{ $currentDraw->formatted_draw_number }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(isset($currentDraw))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Draw Date & Time</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-calendar text-primary me-2"></i>
                                                <span class="fw-bold">{{ $currentDraw->draw_date->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ticket Price</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-dollar-sign text-success me-2"></i>
                                                <span class="fw-bold text-success">${{ number_format($settings->ticket_price ?? 2, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Total Prize Pool</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-gift text-warning me-2"></i>
                                                @php
                                                    // Use the enhanced calculatePrizePool method
                                                    $calculatedTotalMain = $currentDraw->calculatePrizePool();
                                                @endphp
                                                <span class="fw-bold text-warning">${{ number_format($calculatedTotalMain, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Active Participants</label>
                                            <div class="d-flex align-items-center">
                                                <i class="fe fe-users text-info me-2"></i>
                                                @php
                                                    $totalActiveTickets = \App\Models\LotterySetting::getRealActiveTicketsCount();
                                                    $settingsObject = \App\Models\LotterySetting::getSettings();
                                                    $activeTicketsBoost = $settingsObject->active_tickets_boost ?? 0;
                                                    $displayTickets = $totalActiveTickets + $activeTicketsBoost;
                                                    
                                                @endphp
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold" id="ticketsSoldCounter">{{ number_format($displayTickets) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Countdown Timer with Dynamic Status -->
                                @php
                                    $hasManualWinners = $currentDraw->has_manual_winners;
                                    $isTimeUp = $currentDraw->draw_date->isPast();
                                    $isPending = $currentDraw->status === 'pending';
                                    
                                    // Determine status and styling
                                    if ($hasManualWinners) {
                                        $statusType = 'warning';
                                        $statusIcon = 'fas fa-trophy';
                                        $statusLabel = 'Draw Status';
                                        $statusMessage = 'Winners Selected - Draw Pending';
                                        $statusColor = 'text-warning';
                                    } elseif ($isTimeUp && $isPending) {
                                        $statusType = 'info';
                                        $statusIcon = 'fas fa-hourglass-half';
                                        $statusLabel = 'Draw Status';
                                        $statusMessage = 'Draw Time Reached - Awaiting Results';
                                        $statusColor = 'text-info';
                                    } elseif ($isTimeUp) {
                                        $statusType = 'secondary';
                                        $statusIcon = 'fas fa-clock';
                                        $statusLabel = 'Draw Status';
                                        $statusMessage = 'Draw Completed';
                                        $statusColor = 'text-secondary';
                                    } else {
                                        $statusType = 'primary';
                                        $statusIcon = 'fas fa-clock';
                                        $statusLabel = 'Time Remaining';
                                        $statusMessage = '';
                                        $statusColor = 'text-primary';
                                    }
                                @endphp

                                <div class="alert alert-{{ $statusType }} border-{{ $statusType }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $statusIcon }} me-2 fs-5"></i>
                                            <div>
                                                <strong id="countdownLabel">{{ $statusLabel }}</strong>
                                                @if($hasManualWinners || ($isTimeUp && $isPending))
                                                    <div class="small text-muted">
                                                        @if($hasManualWinners)
                                                            Admin has selected winners for this draw
                                                        @else
                                                            Waiting for admin to process the draw
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div id="countdown" class="fw-bold {{ $statusColor }} fs-5">
                                                {{ $statusMessage }}
                                            </div>
                                            @if(!$hasManualWinners && !$isTimeUp)
                                                <div class="small text-muted" id="drawDateTime">
                                                    Draw: {{ $currentDraw->draw_date->format('M d, Y h:i A') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Progress indicator for active draws -->
                                    @if(!$hasManualWinners && !$isTimeUp)
                                        @php
                                            $totalTime = $currentDraw->created_at->diffInSeconds($currentDraw->draw_date);
                                            $timeElapsed = $currentDraw->created_at->diffInSeconds(now());
                                            $progressPercentage = min(100, ($timeElapsed / $totalTime) * 100);
                                        @endphp
                                        <div class="mt-2">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-{{ $statusType }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $progressPercentage }}%"
                                                     id="timeProgress"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- KYC Verification Notice -->
                                @auth
                                    @if(!isset($userStats['kyc_verified']) || !$userStats['kyc_verified'])
                                        <div class="alert alert-warning border-warning d-flex align-items-center" role="alert">
                                            <i class="fas fa-shield-alt text-warning me-3 fa-2x"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="alert-heading mb-1">KYC Verification Required</h6>
                                                <p class="mb-2">You need to complete KYC verification before purchasing lottery tickets.</p>
                                                <a href="{{ route('user.kyc.index') }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-user-check me-1"></i>Complete KYC Verification
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endauth

                                <!-- Special Ticket Cashback Information -->
                                <div class="alert alert-info border-info" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-info-circle text-info me-3 fa-2x"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-2">
                                                <i class="fas fa-gift me-1"></i>Special Ticket Benefits
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong class="text-primary">
                                                            <i class="fas fa-ticket-alt me-1"></i>Standard Tickets:
                                                        </strong>
                                                        <ul class="small mb-0 mt-1">
                                                            <li>Enter lottery draw</li>
                                                            <li>Share via WhatsApp/Messenger</li>
                                                            <li>Manual sharing options</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong class="text-info">
                                                            <i class="fas fa-gift me-1"></i>Special Tickets:
                                                        </strong>
                                                        <ul class="small mb-0 mt-1">
                                                            <li>All standard ticket features</li>
                                                            <li>Share via WhatsApp/Messenger</li>
                                                            <li>Manual sharing options</li>
                                                            <li><strong class="text-success">
                                                                <i class="fas fa-dollar-sign me-1"></i>$1 Cashback if no win!
                                                            </strong></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <i class="fas fa-star text-warning me-1"></i>
                                                    <strong>Special Ticket Guarantee:</strong> If your special ticket doesn't win any prize in the draw, 
                                                    you'll automatically receive $1 cashback to your account after the draw is completed.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buy Tickets Form -->
                                <div class="border p-4 rounded bg-light {{ (!auth()->check() || (isset($userStats['kyc_verified']) && !$userStats['kyc_verified'])) ? 'opacity-50' : '' }}">
                                    <h5 class="mb-3">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Buy Lottery Tickets
                                    </h5>
                                    
                                    <form action="{{ route('lottery.buy.ticket') }}" method="POST" id="buyTicketForm">
                                        @csrf
                                        <input type="hidden" name="lottery_draw_id" value="{{ $currentDraw->id }}">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="ticket_quantity" class="form-label">Number of Tickets</label>
                                                    <select name="ticket_quantity" id="ticket_quantity" class="form-control" 
                                                            {{ (!auth()->check() || (isset($userStats['kyc_verified']) && !$userStats['kyc_verified'])) ? 'disabled' : '' }} required>
                                                        @for($i = 1; $i <= min(10, $settings->max_tickets_per_user ?? 10); $i++)
                                                            <option value="{{ $i }}">{{ $i }} Ticket{{ $i > 1 ? 's' : '' }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Total Cost</label>
                                                    <div class="form-control bg-light" id="totalCost">
                                                        ${{ number_format($settings->ticket_price ?? 2, 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="agreeTerms" 
                                                       {{ (!auth()->check() || (isset($userStats['kyc_verified']) && !$userStats['kyc_verified'])) ? 'disabled' : '' }} required>
                                                <label class="form-check-label" for="agreeTerms">
                                                    I agree to the <a href="{{ route('policies', 'terms-of-service') }}" target="_blank">Terms & Conditions</a>
                                                </label>
                                            </div>
                                        </div>

                                        @auth
                                            @if(isset($userStats['kyc_verified']) && $userStats['kyc_verified'])
                                                @if($currentDraw->has_manual_winners)
                                                    <button type="button" class="btn btn-warning btn-lg w-100" disabled>
                                                        <i class="fas fa-trophy me-2"></i>
                                                        Winners Already Selected
                                                    </button>
                                                @elseif($currentDraw->draw_date->isPast() && $currentDraw->status === 'pending')
                                                    <button type="button" class="btn btn-info btn-lg w-100" disabled>
                                                        <i class="fas fa-hourglass-half me-2"></i>
                                                        Awaiting Draw Results
                                                    </button>
                                                @elseif($currentDraw->draw_date->isPast())
                                                    <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                                        <i class="fas fa-clock me-2"></i>
                                                        Sales Closed
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="buyTicketBtn">
                                                        <i class="fas fa-ticket-alt me-2"></i>
                                                        Buy Tickets Now
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                                    <i class="fas fa-lock me-2"></i>
                                                    KYC Verification Required
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-warning btn-lg w-100">
                                                <i class="fas fa-sign-in-alt me-2"></i>
                                                Login to Buy Tickets
                                            </a>
                                        @endauth
                                    </form>
                                </div>

                                <!-- Your Current Tickets -->
                                @if($userTickets && $userTickets->count() > 0)
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>
                                                <i class="fas fa-ticket-alt me-2"></i>
                                                Your Tickets for This Draw 
                                            </h5>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" onclick="shareAllTickets('whatsapp')">
                                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="shareAllTickets('messenger')">
                                                    <i class="fab fa-facebook-messenger me-1"></i>Messenger
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="shareAllTickets('manual')">
                                                    <i class="fas fa-share me-1"></i>Manual Share
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach($userTickets as $ticket)
                                                <div class="col-md-3 mb-2" data-ticket-id="{{ $ticket->id }}">
                                                    <div class="card {{ $ticket->is_virtual ? 'bg-info' : 'bg-primary' }} text-white position-relative">
                                                        <div class="card-body text-center">
                                                            <div class="mb-1">
                                                                @if($ticket->is_virtual)
                                                                    <span class="badge bg-light text-info small">
                                                                        <i class="fas fa-gift me-1"></i>Special Ticket
                                                                    </span>
                                                                    <div class="small mt-1 text-light">
                                                                        <i class="fas fa-dollar-sign"></i> $1 Cashback if No Win
                                                                    </div>
                                                                @else
                                                                    <span class="badge bg-light text-primary small">
                                                                        <i class="fas fa-ticket-alt me-1"></i>Standard Ticket
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <h6 class="mb-0">Ticket #{{ $ticket->ticket_number }}</h6>
                                                            <small>{{ $ticket->created_at->format('M d, h:i A') }}</small>
                                                            
                                                            <!-- Individual ticket share dropdown -->
                                                            <div class="dropdown position-absolute top-0 end-0 mt-1 me-1">
                                                                <button class="btn btn-sm btn-light btn-outline-secondary dropdown-toggle" type="button" 
                                                                        data-bs-toggle="dropdown" aria-expanded="false" style="padding: 2px 6px;">
                                                                    <i class="fas fa-share text-dark" style="font-size: 10px;"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="#" onclick="shareTicket({{ $ticket->id }}, 'whatsapp', '{{ $ticket->ticket_number }}')">
                                                                        <i class="fab fa-whatsapp text-success me-2"></i>WhatsApp
                                                                    </a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="shareTicket({{ $ticket->id }}, 'messenger', '{{ $ticket->ticket_number }}')">
                                                                        <i class="fab fa-facebook-messenger text-primary me-2"></i>Messenger
                                                                    </a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="shareTicket({{ $ticket->id }}, 'manual', '{{ $ticket->ticket_number }}')">
                                                                        <i class="fas fa-copy text-info me-2"></i>Copy Ticket Number
                                                                    </a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="text-muted mt-3">No Active Draw</h4>
                                    <p class="text-muted">There's no active lottery draw at the moment. Please check back later!</p>
                                    <a href="{{ route('lottery.results') }}" class="btn btn-info">
                                        <i class="fe fe-award me-2"></i>View Previous Results
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Prize Breakdown -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-trophy me-2"></i>
                                Current Draw Prize List
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($currentDraw))
                                @php
                                    // Use actual prize distribution from database - ensure it's an array
                                    $prizeDistribution = $currentDraw->prize_distribution ?? [];
                                    
                                    // If prize_distribution is a JSON string, decode it
                                    if (is_string($prizeDistribution)) {
                                        $prizeDistribution = json_decode($prizeDistribution, true) ?? [];
                                    }
                                    
                                    // Ensure we have an array
                                    if (!is_array($prizeDistribution)) {
                                        $prizeDistribution = [];
                                    }
                                    
                                    $activeTicketsForDraw = $currentDraw->tickets()->where('status', 'active')->count();
                                    
                                    // Group prizes by position to show consolidated amounts
                                    $prizesByPosition = collect($prizeDistribution)->groupBy('position');
                                    
                                    // Define position names and styling
                                    $positionNames = [
                                        1 => ['name' => '1st Prize', 'icon' => 'fas fa-trophy text-warning', 'color' => 'text-warning'],
                                        2 => ['name' => '2nd Prize', 'icon' => 'fas fa-medal text-info', 'color' => 'text-info'],
                                        3 => ['name' => '3rd Prize', 'icon' => 'fas fa-award text-success', 'color' => 'text-success'],
                                        4 => ['name' => '4th Prize', 'icon' => 'fas fa-star text-primary', 'color' => 'text-primary'],
                                        5 => ['name' => '5th Prize', 'icon' => 'fas fa-gift text-secondary', 'color' => 'text-secondary']
                                    ];
                                @endphp
                                
                                <div class="alert alert-info bg-light border-info">
                                    <h6 class="mb-2">
                                        <i class="fas fa-gift me-2"></i>Prize Distribution for Draw #{{ $currentDraw->formatted_draw_number }}
                                    </h6>
                                    <small class="text-muted">
                                        @php
                                            $totalPrizes = count($prizeDistribution);
                                            $calculatedTotal = $currentDraw->calculatePrizePool();
                                            $activeTicketsCount = $currentDraw->tickets()->where('status', 'active')->count();
                                            $activeTicketsBoost = $settings->active_tickets_boost ?? 0;
                                            $displayTicketsCount = $activeTicketsCount + $activeTicketsBoost;
                                            
                                            if (!empty($prizeDistribution)) {
                                                $prizeSource = 'Fixed Amount Structure';
                                                $displayMessage = "Pre-defined prize distribution with {$totalPrizes} total prizes";
                                            } else {
                                                $prizeSource = 'No Prize Distribution Set';
                                                $displayMessage = "Prize distribution not configured for this draw";
                                            }
                                        @endphp
                                        • {{ $displayMessage }}
                                        • Total prize pool: ${{ number_format($calculatedTotal, 2) }}
                                        @if($totalPrizes > 0)
                                            • Total prizes: {{ $totalPrizes }}
                                        @endif
                                    </small>
                                </div>
                                
                                @if(!empty($prizeDistribution))
                                    @foreach($prizesByPosition as $position => $prizes)
                                        @if(isset($positionNames[$position]))
                                            @php
                                                $totalPrizeForPosition = $prizes->sum('amount');
                                                $prizeCount = $prizes->count();
                                                $individualAmount = $prizes->first()['amount'];
                                            @endphp
                                            <div class="mb-3 p-3 border rounded bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="{{ $positionNames[$position]['icon'] }} me-2 fa-lg"></i>
                                                        <div>
                                                            <h6 class="mb-0 {{ $positionNames[$position]['color'] }}">{{ $positionNames[$position]['name'] }}</h6>
                                                            <small class="text-muted">
                                                                ${{ number_format($individualAmount, 2) }} each
                                                                @if($prizeCount > 1)
                                                                    | Total: ${{ number_format($totalPrizeForPosition, 2) }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-primary fs-6">{{ $prizeCount }} Winner{{ $prizeCount > 1 ? 's' : '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6 class="text-muted">No Prize Distribution Set</h6>
                                        <p class="text-muted small">Prize distribution not configured for this draw.</p>
                                    </div>
                                @endif
                                
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total Prize Pool</span>
                                    @php
                                        // Use the enhanced calculatePrizePool method for consistency
                                        $calculatedTotal = $currentDraw->calculatePrizePool();
                                    @endphp
                                    <span class="fw-bold text-primary">${{ number_format($calculatedTotal, 2) }}</span>
                                </div>
                                
                                <!-- Additional Prize Information -->
                                <div class="mt-3 pt-3 border-top">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="small text-muted">Total Winners</div>
                                            <div class="fw-bold text-info">{{ count($prizeDistribution) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Prize Positions</div>
                                            <div class="fw-bold text-primary">{{ count($prizesByPosition) }}</div>
                                        </div>
                                    </div>
                                    @if(!empty($prizeDistribution))
                                        <div class="row text-center mt-2">
                                            <div class="col-12">
                                                <div class="small text-muted">Prize Type</div>
                                                <div class="fw-bold text-success">
                                                    @php
                                                        $firstPrize = collect($prizeDistribution)->first();
                                                        $prizeType = ($firstPrize['type'] ?? 'fixed_amount') === 'fixed_amount' ? 'Fixed Amount' : 'Percentage';
                                                    @endphp
                                                    {{ $prizeType }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 2rem;"></i>
                                    <h6 class="text-muted">No Active Draw</h6>
                                    <p class="text-muted small">Check back later for new lottery draws and exciting prizes!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-bar me-2"></i>
                                Your Lottery Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h4 class="text-primary" id="userTotalTickets">{{ $userStats['total_tickets'] ?? 0 }}</h4>
                                        <small class="text-muted">Total Tickets</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h4 class="text-success" id="userTotalWins">{{ $userStats['total_wins'] ?? 0 }}</h4>
                                        <small class="text-muted">Total Wins</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h4 class="text-warning" id="userTotalWinnings">${{ number_format($userStats['total_winnings'] ?? 0, 2) }}</h4>
                                        <small class="text-muted">Total Winnings</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <h4 class="text-info" id="userTotalSpent">${{ number_format($userStats['total_spent'] ?? 0, 2) }}</h4>
                                        <small class="text-muted">Total Spent</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Draw Winners (if manually selected) -->
                    @if(isset($currentDrawWinners) && $currentDrawWinners->count() > 0)
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                Current Draw Winners - {{ $currentDraw->formatted_draw_number }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Winners have been selected!</strong> This draw will be finalized soon.
                            </div>
                            @foreach($currentDrawWinners as $winner)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar bg-success text-white me-3">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">
                                            Ticket #{{ $winner->lotteryTicket?->ticket_number ?? 'N/A' }}
                                        </h6>
                                        <small class="text-muted">Prize: ${{ number_format($winner->prize_amount, 2) }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">${{ number_format($winner->prize_amount, 2) }}</div>
                                        <span class="badge bg-success">{{ $winner->prize_position }}{{ $winner->prize_position == 1 ? 'st' : ($winner->prize_position == 2 ? 'nd' : ($winner->prize_position == 3 ? 'rd' : 'th')) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Recent Winners -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-crown me-2"></i>
                                Recent Winners
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($recentWinners) && $recentWinners->count() > 0)
                                @foreach($recentWinners as $winner)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar bg-primary text-white me-3">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">
                                                Ticket #{{ $winner->lotteryTicket?->ticket_number ?? 'N/A' }}
                                            </h6>
                                            <small class="text-muted">Won ${{ number_format($winner->prize_amount, 2) }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning">{{ $winner->prize_position }}{{ $winner->prize_position == 1 ? 'st' : ($winner->prize_position == 2 ? 'nd' : ($winner->prize_position == 3 ? 'rd' : 'th')) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No recent winners</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif 

<!-- Include jQuery if needed -->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

<script> 
document.addEventListener('DOMContentLoaded', function() {
    // Initialize and handle Bootstrap alerts
    const alertElements = document.querySelectorAll('.alert-dismissible');
    
    // Make sure alerts are visible and properly initialized
    alertElements.forEach(function(alert) {
        // Ensure the alert is visible
        alert.style.display = 'block';
        
        // Auto-dismiss success alerts after 8 seconds
        if (alert.classList.contains('alert-success')) {
            setTimeout(function() {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn && alert.offsetParent !== null) {
                    // Use Bootstrap's Alert API to properly dismiss
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 8000);
        }
        
        // Add smooth animation
        setTimeout(function() {
            alert.style.opacity = '1';
        }, 100);
    });

    // Manual close button handling (fallback)
    document.querySelectorAll('.alert .btn-close').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const alert = this.closest('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }
        });
    });

    // Enhanced Countdown Timer with Dynamic UI Updates
    @if(isset($currentDraw))
        const drawDate = new Date('{{ $currentDraw->draw_date->toISOString() }}').getTime();
        const hasManualWinners = {{ $currentDraw->has_manual_winners ? 'true' : 'false' }};
        const drawStatus = '{{ $currentDraw->status }}';
        const isTimeUp = {{ $currentDraw->draw_date->isPast() ? 'true' : 'false' }};
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = drawDate - now;
            const countdownElement = document.getElementById('countdown');
            const labelElement = document.getElementById('countdownLabel');
            const buyBtn = document.getElementById('buyTicketBtn');
            const progressBar = document.getElementById('timeProgress');
            const statusContainer = countdownElement?.closest('.alert');
            
            // Update dynamic status based on current state
            if (hasManualWinners) {
                updateStatusDisplay('warning', 'fas fa-trophy', 'Draw Status', 
                    'Winners Selected - Draw Pending', 'text-warning');
                
                if (buyBtn) {
                    buyBtn.disabled = true;
                    buyBtn.innerHTML = '<i class="fas fa-trophy me-2"></i>Winners Selected';
                    buyBtn.className = 'btn btn-warning btn-lg w-100';
                }
            } else if (distance <= 0) {
                if (drawStatus === 'pending') {
                    updateStatusDisplay('info', 'fas fa-hourglass-half', 'Draw Status', 
                        'Draw Time Reached - Awaiting Results', 'text-info');
                    
                    if (buyBtn) {
                        buyBtn.disabled = true;
                        buyBtn.innerHTML = '<i class="fas fa-hourglass-half me-2"></i>Awaiting Results';
                        buyBtn.className = 'btn btn-info btn-lg w-100';
                    }
                } else {
                    updateStatusDisplay('secondary', 'fas fa-clock', 'Draw Status', 
                        'Draw Completed', 'text-secondary');
                    
                    if (buyBtn) {
                        buyBtn.disabled = true;
                        buyBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Sales Closed';
                        buyBtn.className = 'btn btn-secondary btn-lg w-100';
                    }
                }
            } else {
                // Active countdown
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                let timeString = '';
                if (days > 0) {
                    timeString = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                } else if (hours > 0) {
                    timeString = `${hours}h ${minutes}m ${seconds}s`;
                } else if (minutes > 0) {
                    timeString = `${minutes}m ${seconds}s`;
                } else {
                    timeString = `${seconds}s`;
                }
                
                updateStatusDisplay('primary', 'fas fa-clock', 'Time Remaining', 
                    timeString, 'text-primary');
                
                // Update progress bar
                if (progressBar) {
                    const totalTime = {{ $currentDraw->created_at->diffInSeconds($currentDraw->draw_date) }};
                    const timeElapsed = totalTime - Math.floor(distance / 1000);
                    const progressPercentage = Math.min(100, (timeElapsed / totalTime) * 100);
                    progressBar.style.width = progressPercentage + '%';
                }
                
                // Add urgency styling for last hour
                if (distance < 3600000) { // Less than 1 hour
                    if (countdownElement) {
                        countdownElement.className = 'fw-bold text-danger fs-5 pulse';
                    }
                    if (statusContainer) {
                        statusContainer.className = 'alert alert-danger border-danger';
                    }
                }
            }
        }
        
        function updateStatusDisplay(alertType, iconClass, label, message, textClass) {
            const countdownElement = document.getElementById('countdown');
            const labelElement = document.getElementById('countdownLabel');
            const statusContainer = countdownElement?.closest('.alert');
            const iconElement = statusContainer?.querySelector('i');
            
            if (labelElement) labelElement.textContent = label;
            if (countdownElement) {
                countdownElement.innerHTML = message;
                countdownElement.className = `fw-bold ${textClass} fs-5`;
            }
            if (statusContainer) {
                statusContainer.className = `alert alert-${alertType} border-${alertType}`;
            }
            if (iconElement) {
                iconElement.className = `${iconClass} me-2 fs-5`;
            }
        }
        
        // Real-time status checking (check server status every 5 minutes to avoid frequent reloads)
        function checkDrawStatus() {
            if (!hasManualWinners && drawStatus === 'pending') {
                fetch('{{ route("lottery.status.check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ draw_id: {{ $currentDraw->id }} })
                })
                .then(response => response.json())
                .then(data => {
                    // Only reload if there are significant status changes (winners selected or draw completed)
                    if (data.has_manual_winners && !hasManualWinners) {
                        console.log('Winners have been selected, updating page...');
                        // Update status in place instead of full reload
                        updateDrawStatusDisplay('warning', 'fas fa-trophy', 'Draw Status', 
                            'Winners Selected - Draw Pending', 'text-warning');
                        
                        // Disable buy button
                        const buyBtn = document.getElementById('buyTicketBtn');
                        if (buyBtn) {
                            buyBtn.disabled = true;
                            buyBtn.innerHTML = '<i class="fas fa-trophy me-2"></i>Winners Selected';
                            buyBtn.className = 'btn btn-warning btn-lg w-100';
                        }
                        
                        // Show notification
                        showAlert('info', 'Winners have been selected for this draw!');
                        
                        // Update local variables
                        hasManualWinners = true;
                    } else if (data.status === 'completed' && drawStatus === 'pending') {
                        console.log('Draw completed, updating page...');
                        // Only reload for completed draws to show final results
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000); // Small delay to let user see any current actions
                    }
                    
                    // Update local status
                    drawStatus = data.status;
                })
                .catch(error => {
                    console.log('Status check failed:', error);
                });
            }
        }
        
        // Helper function to update draw status display
        function updateDrawStatusDisplay(alertType, iconClass, label, message, textClass) {
            const countdownElement = document.getElementById('countdown');
            const labelElement = document.getElementById('countdownLabel');
            const statusContainer = countdownElement?.closest('.alert');
            const iconElement = statusContainer?.querySelector('i');
            
            if (labelElement) labelElement.textContent = label;
            if (countdownElement) {
                countdownElement.innerHTML = message;
                countdownElement.className = `fw-bold ${textClass} fs-5`;
            }
            if (statusContainer) {
                statusContainer.className = `alert alert-${alertType} border-${alertType}`;
            }
            if (iconElement) {
                iconElement.className = `${iconClass} me-2 fs-5`;
            }
        }
        
        // Initialize countdown
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Check status every 5 minutes instead of 30 seconds to prevent frequent reloads
        // Only enable if user hasn't disabled auto-checking
        const autoCheckEnabled = localStorage.getItem('lottery_auto_check') !== 'disabled';
        if (autoCheckEnabled) {
            console.log('Auto status checking enabled - checking every 5 minutes');
            setInterval(checkDrawStatus, 300000); // 5 minutes = 300000ms
        } else {
            console.log('Auto status checking disabled by user preference');
        }
    @endif
    
    // Update total cost when quantity changes
    const ticketPrice = {{ $settings->ticket_price ?? 2 }};
    const quantitySelect = document.getElementById('ticket_quantity');
    if (quantitySelect) {
        quantitySelect.addEventListener('change', function() {
            const quantity = parseInt(this.value);
            const total = quantity * ticketPrice;
            const totalCostElement = document.getElementById('totalCost');
            if (totalCostElement) {
                totalCostElement.textContent = '$' + total.toFixed(2);
            }
        });
    }
    
    // AJAX Form submission for instant updates
    const buyTicketForm = document.getElementById('buyTicketForm');
    if (buyTicketForm) {
        buyTicketForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const agreeTerms = document.getElementById('agreeTerms');
            if (agreeTerms && !agreeTerms.checked) {
                alert('Please agree to the terms and conditions');
                return false;
            }
            
            const btn = document.getElementById('buyTicketBtn');
            if (!btn) {
                console.error('Buy ticket button not found');
                return false;
            }
            
            const originalBtnText = btn.innerHTML;
            
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
            
            // Get form data
            const formData = new FormData(buyTicketForm);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const csrfValue = csrfToken ? csrfToken.getAttribute('content') : '';
            
            // Make AJAX request
            fetch('{{ route("lottery.buy.ticket") }}', { 
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfValue,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Always try to parse JSON, regardless of status code
                return response.json().then(data => {
                    return { data, status: response.status, ok: response.ok };
                });
            })
            .then(({ data, status, ok }) => {
                console.log('AJAX Response received:', data, 'Status:', status);
                
                if (ok && data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Add success feedback
                    showSuccessFeedback();
                    
                    // Update page content instantly
                    updatePageContent(data);
                    
                    // Reset button
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                    
                } else {
                    console.log('Purchase failed:', data);
                    // Show error message with proper server response
                    const errorMessage = data.message || 'An error occurred while purchasing tickets.';
                    showAlert('error', errorMessage);
                    
                    // Reset button
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                showAlert('error', 'Network connection error. Please check your internet connection and try again.'); 
                
                // Reset button
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
            });
        });
    }
    
    // Function to show alerts dynamically
    function showAlert(type, message) {
        try {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert-dismissible');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
            alertDiv.id = `${type}Alert`;
            alertDiv.setAttribute('role', 'alert');
            
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            const title = type === 'success' ? 'Success!' : 'Error!';
            
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="${icon} me-2 fs-5"></i>
                    <strong>${title}</strong>&nbsp;${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert after page header
            const pageHeader = document.querySelector('.page-header');
            if (pageHeader) {
                pageHeader.insertAdjacentElement('afterend', alertDiv);
            } else {
                // Fallback to body if page header not found
                document.body.insertBefore(alertDiv, document.body.firstChild);
            }
            
            // Auto-dismiss success alerts after 8 seconds
            if (type === 'success') {
                setTimeout(function() {
                    if (alertDiv.offsetParent !== null && typeof bootstrap !== 'undefined') {
                        try {
                            const bsAlert = new bootstrap.Alert(alertDiv);
                            bsAlert.close();
                        } catch (e) {
                            alertDiv.remove();
                        }
                    }
                }, 8000);
            }
            
            // Scroll to alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } catch (error) {
            console.error('Error showing alert:', error);
            // Fallback to simple alert
            alert(`${type === 'success' ? 'Success' : 'Error'}: ${message}`);
        }
    }
    
    // Function to show success feedback  
    function showSuccessFeedback() {
        try {
            // Create confetti effect
            const buyButton = document.getElementById('buyTicketBtn');
            if (buyButton) {
                // Temporary success styling
                buyButton.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
                buyButton.style.transform = 'scale(1.05)';
                
                setTimeout(() => {
                    buyButton.style.background = '';
                    buyButton.style.transform = '';
                }, 1000);
            }
            
            // Create floating success icon
            const successIcon = document.createElement('div');
            successIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            successIcon.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 4rem;
                color: #28a745;
                z-index: 9999;
                pointer-events: none;
                animation: successPop 1.5s ease-out forwards;
            `;
            
            document.body.appendChild(successIcon);
            
            setTimeout(() => {
                if (successIcon.parentNode) {
                    successIcon.remove();
                }
            }, 1500);
            
            // Try to play a success sound (optional)
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEcBDGH0fPTgjMGHm7A7+OZURE');
                audio.play().catch(() => {}); // Ignore errors if audio fails
            } catch (e) {
                // Ignore audio errors silently
            }
        } catch (error) {
            console.error('Error showing success feedback:', error);
        }
    }
    
    // Function to update page content after successful purchase
    function updatePageContent(data) { 
        try {
            console.log('Updating page content with data:', data);
            
            // Update tickets sold count - preserve boost format
            if (data.tickets_sold !== undefined) {
                // Use the specific ID for tickets sold counter
                const ticketsSoldElement = document.getElementById('ticketsSoldCounter');
                if (ticketsSoldElement) {
                    // Check if we have boost information in the current element
                    const parentDiv = ticketsSoldElement.parentElement;
                    const boostElement = parentDiv.querySelector('small.text-muted');
                    
                    if (boostElement && boostElement.textContent.includes('boost')) {
                        // We have boost format, just update the main number
                        ticketsSoldElement.textContent = data.tickets_sold.toLocaleString();
                        console.log(`Updated boosted tickets to: ${data.tickets_sold}`);
                    } else {
                        // Fallback to old format if no boost detected
                        const maxTickets = '{{ $settings->max_tickets_per_draw ?? 1000 }}';
                        ticketsSoldElement.textContent = `${data.tickets_sold} / ${maxTickets}`;
                        console.log(`Updated tickets sold to: ${data.tickets_sold} / ${maxTickets}`);
                    }
                } else {
                    console.log('Tickets sold element not found');
                }
            }
        
            // Update user stats using specific IDs
            if (data.user_stats) {
                const stats = data.user_stats;
                console.log('Updating user stats:', stats);
                
                // Update total tickets
                const totalTicketsElement = document.getElementById('userTotalTickets');
                if (totalTicketsElement) {
                    totalTicketsElement.textContent = stats.total_tickets || 0;
                    console.log(`Updated total tickets to: ${stats.total_tickets}`);
                }
                
                // Update total wins
                const totalWinsElement = document.getElementById('userTotalWins');
                if (totalWinsElement) {
                    totalWinsElement.textContent = stats.total_wins || 0;
                    console.log(`Updated total wins to: ${stats.total_wins}`);
                }
                
                // Update total winnings
                const totalWinningsElement = document.getElementById('userTotalWinnings');
                if (totalWinningsElement) {
                    totalWinningsElement.textContent = '$' + parseFloat(stats.total_winnings || 0).toFixed(2);
                    console.log(`Updated total winnings to: $${stats.total_winnings}`);
                }
                
                // Update total spent
                const totalSpentElement = document.getElementById('userTotalSpent');
                if (totalSpentElement) {
                    totalSpentElement.textContent = '$' + parseFloat(stats.total_spent || 0).toFixed(2);
                    console.log(`Updated total spent to: $${stats.total_spent}`);
                }
            }            // Add new tickets to "Your Tickets" section
            if (data.new_tickets && data.new_tickets.length > 0) {
                console.log('Adding new tickets:', data.new_tickets);
                
                // Look for existing tickets section
                let ticketsContainer = document.querySelector('.mt-4 .row');
                let ticketsSection = document.querySelector('.mt-4');
                
                // If tickets section doesn't exist, create it
                if (!ticketsSection || !ticketsSection.querySelector('h5')?.textContent.includes('Your Tickets')) {
                    const buyTicketForm = document.getElementById('buyTicketForm').closest('.border');
                    ticketsSection = document.createElement('div');
                    ticketsSection.className = 'mt-4';
                    ticketsSection.innerHTML = `
                        <h5>
                            <i class="fas fa-ticket-alt me-2"></i>
                            Your Tickets for This Draw 
                        </h5>
                        <div class="row" id="userTicketsContainer">
                        </div>
                    `;
                    buyTicketForm.insertAdjacentElement('afterend', ticketsSection);
                    ticketsContainer = ticketsSection.querySelector('.row');
                } else {
                    // Use existing container
                    ticketsContainer = ticketsSection.querySelector('.row') || ticketsSection;
                }
                
                if (ticketsContainer) {
                    // Add new tickets with animation
                    data.new_tickets.forEach((ticket, index) => {
                        const ticketDiv = document.createElement('div');
                        ticketDiv.className = 'col-md-3 mb-2';
                        
                        const cardClass = ticket.is_virtual ? 'bg-info' : 'bg-primary';
                        const badgeClass = ticket.is_virtual ? 'text-info' : 'text-primary';
                        const badgeIcon = ticket.is_virtual ? 'fas fa-gift' : 'fas fa-ticket-alt';
                        const badgeText = ticket.is_virtual ? 'Bonus' : 'Standard';
                        
                        ticketDiv.innerHTML = `
                            <div class="card ${cardClass} text-white">
                                <div class="card-body text-center">
                                    <div class="mb-1">
                                        <span class="badge bg-light ${badgeClass} small">
                                            <i class="${badgeIcon} me-1"></i>${badgeText}
                                        </span>
                                    </div>
                                    <h6 class="mb-0">Ticket #${ticket.ticket_number}</h6>
                                    <small>${ticket.created_at}</small>
                                </div>
                            </div>
                        `;
                        
                        // Add with slight delay for animation effect
                        setTimeout(() => {
                            ticketsContainer.appendChild(ticketDiv);
                            
                            // Add pop animation
                            ticketDiv.style.transform = 'scale(0)';
                            ticketDiv.style.transition = 'transform 0.3s ease-out';
                            
                            setTimeout(() => {
                                ticketDiv.style.transform = 'scale(1.05)';
                                setTimeout(() => {
                                    ticketDiv.style.transform = 'scale(1)';
                                }, 200);
                            }, 50);
                        }, index * 100);
                    });
                    
                    console.log(`Added ${data.new_tickets.length} new tickets`);
                }
            }
            
            // Update total prize pool if provided
            if (data.total_prize_pool !== undefined) {
                // Find prize pool elements more specifically
                const prizePoolElements = document.querySelectorAll('.text-warning');
                prizePoolElements.forEach(element => {
                    if (element.textContent.includes('$') && element.closest('.d-flex')?.textContent.includes('Total Prize Pool')) {
                        element.textContent = '$' + parseFloat(data.total_prize_pool).toFixed(2);
                        console.log(`Updated prize pool to: $${data.total_prize_pool}`);
                    }
                });
                
                // Also update in the sidebar Prize Breakdown
                const totalPrizeElement = document.querySelector('.fw-bold.text-primary');
                if (totalPrizeElement && totalPrizeElement.textContent.includes('$')) {
                    totalPrizeElement.textContent = '$' + parseFloat(data.total_prize_pool).toFixed(2);
                }
            }
            
            console.log('Page content update completed successfully');
            
        } catch (error) {
            console.error('Error updating page content:', error);
        }
    }

    // Debug information and initialization
    @if(session('success'))
        console.log('Success message present:', '{{ addslashes(session('success')) }}');
        
        // Force show success alert if hidden
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            successAlert.style.display = 'block';
            successAlert.style.opacity = '1';
            successAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    @endif
    
    @if(session('error'))
        console.log('Error message present:', '{{ addslashes(session('error')) }}');
        
        // Force show error alert if hidden  
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            errorAlert.style.display = 'block';
            errorAlert.style.opacity = '1';
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    @endif
    
    // Log successful initialization
    console.log('Lottery page JavaScript initialized successfully');
    console.log('Auto-reload status checking:', localStorage.getItem('lottery_auto_check') !== 'disabled' ? 'ENABLED (every 5 minutes)' : 'DISABLED');
    console.log('To disable auto-checking and prevent reloads, run: localStorage.setItem("lottery_auto_check", "disabled")');
    console.log('To re-enable auto-checking, run: localStorage.removeItem("lottery_auto_check")');
});

// Ticket sharing functions
function shareTicket(ticketId, platform, ticketNumber = null) {
    console.log(`Sharing ticket ${ticketId} via ${platform}`);
    
    // Use only the ticket number for sharing
    const textToShare = ticketNumber || ticketId;
    
    switch (platform) {
        case 'whatsapp':
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(textToShare)}`;
            window.open(whatsappUrl, '_blank');
            break;
            
        case 'messenger':
            const messengerUrl = `https://www.messenger.com/t/?text=${encodeURIComponent(textToShare)}`;
            window.open(messengerUrl, '_blank');
            break;
            
        case 'manual':
            // Copy only the ticket number
            copyToClipboard(textToShare, `Ticket number ${textToShare} copied to clipboard!`);
            break;
            
        default:
            console.error('Unknown sharing platform:', platform);
    }
}

function shareAllTickets(platform) {
    console.log(`Sharing all tickets via ${platform}`);
    
    // Get all ticket numbers from the page by looking at the ticket display text
    const ticketElements = document.querySelectorAll('[data-ticket-id]');
    const ticketNumbers = [];
    
    ticketElements.forEach(element => {
        // Find the ticket number in the card (look for "Ticket #" text)
        const ticketNumberElement = element.querySelector('h6');
        if (ticketNumberElement) {
            const ticketText = ticketNumberElement.textContent;
            // Extract ticket number from "Ticket #D620-2037-84EB-75C6" format
            const match = ticketText.match(/Ticket #(.+)/);
            if (match) {
                ticketNumbers.push(match[1]);
            }
        }
    });
    
    // Create simple list of ticket numbers
    const ticketNumbersText = ticketNumbers.join(', ');
    
    switch (platform) {
        case 'whatsapp':
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(ticketNumbersText)}`;
            window.open(whatsappUrl, '_blank');
            break;
            
        case 'messenger':
            const messengerUrl = `https://www.messenger.com/t/?text=${encodeURIComponent(ticketNumbersText)}`;
            window.open(messengerUrl, '_blank');
            break;
            
        case 'manual':
            copyToClipboard(ticketNumbersText, 'All ticket numbers copied to clipboard!');
            break;
            
        default:
            console.error('Unknown sharing platform:', platform);
    }
}

function copyToClipboard(textToCopy, successMessage) {
    // Create a temporary textarea element
    const tempTextArea = document.createElement('textarea');
    tempTextArea.value = textToCopy;
    tempTextArea.style.position = 'fixed';
    tempTextArea.style.left = '-999999px';
    tempTextArea.style.top = '-999999px';
    document.body.appendChild(tempTextArea);
    
    try {
        // Select and copy the text
        tempTextArea.select();
        tempTextArea.setSelectionRange(0, 99999); // For mobile devices
        
        // Try modern clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(tempTextArea.value).then(() => {
                showCopySuccess(successMessage || 'Copied to clipboard!');
            }).catch(() => {
                // Fallback to execCommand
                fallbackCopy(tempTextArea, successMessage);
            });
        } else {
            // Fallback to execCommand
            fallbackCopy(tempTextArea, successMessage);
        }
    } catch (err) {
        console.error('Failed to copy text: ', err);
        showCopyError('Failed to copy. Please copy manually.');
    } finally {
        // Clean up
        document.body.removeChild(tempTextArea);
    }
}

function fallbackCopy(textArea, successMessage) {
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(successMessage || 'Copied to clipboard!');
        } else {
            showCopyError('Failed to copy. Please copy manually.');
        }
    } catch (err) {
        console.error('Fallback copy failed: ', err);
        showCopyError('Failed to copy. Please copy manually.');
    }
}

function showCopySuccess(message) {
    // Create and show a temporary success message
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <strong>${message}</strong>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 3000);
}

function showCopyError(message) {
    // Create and show a temporary error message
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>${message}</strong>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

</script>

<style>
.text-bronze {
    color: #cd7f32;
}
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Enhanced countdown and status styles */
.pulse {
    animation: pulse 1.5s ease-in-out infinite alternate;
}

@keyframes pulse {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.status-transition {
    transition: all 0.3s ease-in-out;
}

.progress {
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 0.25rem;
    overflow: hidden;
}

.progress-bar {
    transition: width 1s ease-in-out;
}

/* Dynamic alert styling */
.alert {
    margin-bottom: 1.5rem !important;
    position: relative;
    z-index: 1050;
    display: block !important;
    opacity: 1 !important;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease-in-out;
}

.alert-primary {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 2px solid #0d6efd;
    color: #084298;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 2px solid #ffc107;
    color: #997404;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
    border: 2px solid #0dcaf0;
    color: #055160;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border: 2px solid #dc3545;
    color: #842029;
    animation: urgentPulse 2s ease-in-out infinite;
}

@keyframes urgentPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
}

.alert-secondary {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    color: #6c757d !important;
}

.alert-success {
    background-color: #d1e7dd !important;
    border: 1px solid #badbcc !important;
    color: #0f5132 !important;
}
    border: 1px solid #f5c2c7 !important;
    color: #842029 !important;
}

.alert-warning {
    background-color: #fff3cd !important;
    border: 1px solid #ffecb5 !important;
    color: #997404 !important;
}

.alert-dismissible {
    padding-right: 3rem;
}

.alert-dismissible .btn-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    z-index: 2;
    padding: 0.375rem;
    color: inherit;
    background: none;
    border: none;
    font-size: 1rem;
    opacity: 0.5;
}

.alert-dismissible .btn-close:hover {
    opacity: 0.75;
}

/* Animation for alerts */
.alert.fade.show {
    opacity: 1 !important;
    transition: opacity 0.15s linear;
}

.alert.fade {
    opacity: 0;
}

/* Ensure success alerts are prominent */
#successAlert {
    background: linear-gradient(135deg, #d1e7dd 0%, #c3e6cb 100%) !important;
    border-left: 4px solid #28a745 !important;
    animation: slideDown 0.5s ease-out;
}

#errorAlert {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%) !important;
    border-left: 4px solid #dc3545 !important;
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes successPop {
    0% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
    }
}

/* Force visibility on page load */
.alert[id*="Alert"] {
    visibility: visible !important;
    display: block !important;
}
</style>
@endsection
</x-smart_layout>
