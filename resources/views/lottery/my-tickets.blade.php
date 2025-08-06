<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">🎫 My Lottery Tickets</h1>
                        <p class="text-muted">View and manage your lottery tickets</p>
                    </div>
                    <div>
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Buy More Tickets
                        </a>
                        <a href="{{ route('lottery.results') }}" class="btn btn-info">
                            <i class="fe fe-award me-2"></i>View Results
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Options -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('lottery.my.tickets') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Tickets</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="drawn" {{ request('status') == 'drawn' ? 'selected' : '' }}>Drawn</option>
                                <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="draw_id" class="form-label">Draw</label>
                            <select name="draw_id" id="draw_id" class="form-control">
                                <option value="">All Draws</option>
                                @if(isset($draws))
                                    @foreach($draws as $draw)
                                        <option value="{{ $draw->id }}" {{ request('draw_id') == $draw->id ? 'selected' : '' }}>
                                            Draw #{{ $draw->display_draw_number }} - {{ $draw->draw_date->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                @endif
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
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fe fe-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('lottery.my.tickets') }}" class="btn btn-secondary">
                                <i class="fe fe-refresh-cw me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['total_tickets'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Total Tickets</p>
                                </div>
                                <div class="avatar bg-primary">
                                    <i class="fe fe-ticket"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['active_tickets'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Active Tickets</p>
                                </div>
                                <div class="avatar bg-success">
                                    <i class="fe fe-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['winning_tickets'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Winning Tickets</p>
                                </div>
                                <div class="avatar bg-warning">
                                    <i class="fe fe-trophy"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">${{ number_format($stats['total_winnings'] ?? 0, 2) }}</h4>
                                    <p class="text-muted mb-0">Total Winnings</p>
                                </div>
                                <div class="avatar bg-info">
                                    <i class="fe fe-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Your Lottery Tickets
                    </h4>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if(isset($tickets) && $tickets->count() > 0)
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fe fe-share-2 me-1"></i>Share All Tickets
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" id="shareAllWhatsApp">
                                            <i class="fab fa-whatsapp text-success me-2"></i>Share via WhatsApp
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" id="shareAllFacebook">
                                            <i class="fab fa-facebook text-primary me-2"></i>Share via Facebook
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" id="shareAllTelegram">
                                            <i class="fab fa-telegram text-info me-2"></i>Share via Telegram
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" id="copyAllTickets">
                                            <i class="fe fe-copy text-secondary me-2"></i>Copy All Numbers
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <span class="badge badge-primary">{{ $tickets->total() ?? 0 }} tickets found</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($tickets) && $tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Draw</th>
                                        <th>Draw Date</th>
                                        <th>Purchase Date</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Prize</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $ticket->ticket_number }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('lottery.draw.details', $ticket->lottery_draw_id) }}" 
                                                   class="text-decoration-none">
                                                    Draw #{{ $ticket->lotteryDraw->display_draw_number }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $ticket->lotteryDraw->draw_date->format('M d, Y h:i A') }}
                                            </td>
                                            <td>
                                                {{ $ticket->purchased_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">${{ number_format($ticket->ticket_price, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($ticket->lotteryDraw->status == 'pending')
                                                    <span class="badge bg-warning">
                                                        <i class="fe fe-clock me-1"></i>Draw Pending
                                                    </span>
                                                @elseif($ticket->status == 'active')
                                                    <span class="badge bg-info">Active</span>
                                                @elseif($ticket->status == 'expired')
                                                    <span class="badge bg-danger">Expired</span>
                                                @elseif($ticket->status == 'refunded')
                                                    <span class="badge bg-danger">Refunded</span>
                                                @elseif($ticket->status == 'winner')
                                                    <span class="badge bg-success">Won</span>
                                                @elseif($ticket->status == 'claimed')
                                                    <span class="badge bg-warning">Claimed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($ticket->lotteryDraw->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->lotteryDraw->status == 'completed' && $ticket->winner)
                                                    <span class="text-success fw-bold">
                                                        ${{ number_format($ticket->winner->prize_amount, 2) }}
                                                    </span>
                                                @elseif($ticket->lotteryDraw->status == 'pending')
                                                    <span class="text-info">
                                                        <i class="fe fe-clock me-1"></i>Draw Pending
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->lotteryDraw->status == 'completed' && $ticket->winner && $ticket->winner->claim_status == 'pending')
                                                    @php
                                                        $position = $ticket->winner->prize_position ?? 1; // Default to 1 if null
                                                        $suffix = 'th';
                                                        if ($position == 1) $suffix = 'st';
                                                        elseif ($position == 2) $suffix = 'nd';
                                                        elseif ($position == 3) $suffix = 'rd';
                                                        $positionText = $position . $suffix;
                                                    @endphp
                                                    <form action="{{ route('lottery.claim.prize', $ticket->winner->id) }}" method="POST" class="d-inline claim-form">
                                                        @csrf
                                                        <button type="button" class="btn btn-sm btn-success claim-prize-btn" 
                                                                data-amount="${{ number_format($ticket->winner->prize_amount, 2) }}"
                                                                data-position="{{ $positionText }}">
                                                            <i class="fe fe-gift me-1"></i>Claim Prize
                                                        </button>
                                                    </form>
                                                @elseif($ticket->lotteryDraw->status == 'completed' && $ticket->winner && $ticket->winner->claim_status == 'claimed')
                                                    <span class="badge badge-success">Claimed</span>
                                                @elseif($ticket->lotteryDraw->status == 'pending')
                                                    <span class="badge bg-info">
                                                        <i class="fe fe-clock me-1"></i>Awaiting Draw
                                                    </span>
                                                @else
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('lottery.draw.details', $ticket->lottery_draw_id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fe fe-eye me-1"></i>View
                                                        </a>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fe fe-share-2 me-1"></i>Share
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item share-whatsapp" href="#" 
                                                                       data-ticket="{{ $ticket->ticket_number }}"
                                                                       data-draw="{{ $ticket->lotteryDraw->display_draw_number }}"
                                                                       data-date="{{ $ticket->lotteryDraw->draw_date->format('M d, Y') }}"
                                                                       data-price="${{ number_format($ticket->ticket_price, 2) }}">
                                                                        <i class="fab fa-whatsapp text-success me-2"></i>WhatsApp
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item share-facebook" href="#" 
                                                                       data-ticket="{{ $ticket->ticket_number }}"
                                                                       data-draw="{{ $ticket->lotteryDraw->display_draw_number }}"
                                                                       data-date="{{ $ticket->lotteryDraw->draw_date->format('M d, Y') }}"
                                                                       data-price="${{ number_format($ticket->ticket_price, 2) }}">
                                                                        <i class="fab fa-facebook text-primary me-2"></i>Facebook
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item share-telegram" href="#" 
                                                                       data-ticket="{{ $ticket->ticket_number }}"
                                                                       data-draw="{{ $ticket->lotteryDraw->display_draw_number }}"
                                                                       data-date="{{ $ticket->lotteryDraw->draw_date->format('M d, Y') }}"
                                                                       data-price="${{ number_format($ticket->ticket_price, 2) }}">
                                                                        <i class="fab fa-telegram text-info me-2"></i>Telegram
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item copy-link" href="#" 
                                                                       data-ticket="{{ $ticket->ticket_number }}"
                                                                       data-draw="{{ $ticket->lotteryDraw->display_draw_number }}"
                                                                       data-date="{{ $ticket->lotteryDraw->draw_date->format('M d, Y') }}"
                                                                       data-price="${{ number_format($ticket->ticket_price, 2) }}">
                                                                        <i class="fe fe-copy text-secondary me-2"></i>Copy Number
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(isset($tickets) && $tickets->hasPages())
                            <div class="mt-4">
                                <div class="d-flex justify-content-center">
                                    <nav aria-label="Tickets pagination">
                                        {{ $tickets->links() }}
                                    </nav>
                                </div>
                                
                                <!-- Pagination Info -->
                                <div class="d-flex justify-content-center mt-3">
                                    <div class="pagination-info">
                                        <small class="text-muted px-3 py-2 bg-light rounded">
                                            <i class="fe fe-info me-1"></i>
                                            Showing <strong>{{ $tickets->firstItem() }}</strong> to <strong>{{ $tickets->lastItem() }}</strong> 
                                            of <strong>{{ $tickets->total() }}</strong> results
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">No Tickets Found</h4>
                            <p class="text-muted">You haven't purchased any lottery tickets yet.</p>
                            <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                                <i class="fe fe-plus me-2"></i>Buy Your First Ticket
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Winning History -->
            @if(isset($winningHistory) && $winningHistory->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-trophy me-2"></i>
                            Your Winning History
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($winningHistory as $win)
                                @if($win->lotteryDraw && $win->lotteryDraw->status == 'completed')
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-gradient-success text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="mb-0">${{ number_format($win->prize_amount, 2) }}</h5>
                                                        <small>Prize</small>
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-medal fs-2"></i>
                                                    </div>
                                                </div>
                                                <hr class="my-2">
                                                <small>
                                                    Draw #{{ $win->lotteryDraw->display_draw_number }} - {{ $win->created_at->format('M d, Y') }}
                                                    <br>
                                                    Status: {{ ucfirst($win->claim_status) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #20c997);
}

/* Enhanced Pagination Styling */
.pagination {
    margin-bottom: 0;
    gap: 0.25rem;
}

.pagination .page-link {
    color: #6c757d;
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
    text-decoration: none;
    font-weight: 500;
}

.pagination .page-link:hover:not(.disabled) {
    color: #0d6efd;
    background-color: #e3f2fd;
    border-color: #90caf9;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.pagination-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Responsive pagination */
@media (max-width: 768px) {
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.125rem;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .pagination-info {
        font-size: 0.8rem;
        padding: 0.5rem 1rem !important;
    }
}

@media (max-width: 576px) {
    .pagination .page-link {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
    }
}

.swal-wide {
    width: 500px !important;
}
</style>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Awesome CDN for social media icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle claim prize buttons
    const claimButtons = document.querySelectorAll('.claim-prize-btn');
    
    claimButtons.forEach(function(button, index) {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            const position = this.getAttribute('data-position');
            const form = this.closest('.claim-form');
            const parentForm = this.closest('form');
            
            // Use parentForm if .claim-form is not found
            const targetForm = form || parentForm;
            
            if (!amount || !position || !targetForm) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Unable to process claim request. Please refresh and try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            Swal.fire({
                title: '🎉 Claim Your Prize!',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-trophy text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-success mb-2">${amount}</h4>
                        <p class="text-muted mb-3">${position} Prize Winner</p>
                        <p>Are you sure you want to claim this prize?<br>
                        <small class="text-muted">The amount will be added to your account balance.</small></p>
                    </div>
                `,
                icon: null,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-gift me-2"></i>Yes, Claim Prize!',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
                customClass: {
                    popup: 'swal-wide',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                reverseButtons: true,
                focusConfirm: false,
                allowEscapeKey: true,
                allowOutsideClick: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading with timeout
                    const loadingAlert = Swal.fire({
                        title: 'Processing...',
                        html: 'Claiming your prize, please wait.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Set a timeout in case the form submission doesn't redirect properly
                    setTimeout(() => {
                        loadingAlert.close();
                        Swal.fire({
                            title: 'Taking longer than expected...',
                            text: 'Please check if the prize was claimed successfully.',
                            icon: 'warning',
                            confirmButtonText: 'Refresh Page',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    }, 10000); // 10 second timeout
                    
                    // Submit the form
                    try {
                        targetForm.submit();
                    } catch (error) {
                        console.error('Form submission error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to submit claim form. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });

    // Ticket Sharing Functions
    function generateTicketText(ticket, draw, date, price) {
        // Only share ticket number for security purposes
        return ticket;
    }

    function generateAllTicketsText() {
        const tickets = [];
        document.querySelectorAll('tbody tr').forEach(row => {
            const ticketNumber = row.querySelector('td:first-child .fw-bold')?.textContent?.replace('#', '') || '';
            
            if (ticketNumber) {
                tickets.push(ticketNumber);
            }
        });

        return tickets.join('\n');
    }

    // Individual ticket sharing
    document.querySelectorAll('.share-whatsapp').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const ticket = this.dataset.ticket;
            const draw = this.dataset.draw;
            const date = this.dataset.date;
            const price = this.dataset.price;
            
            const text = generateTicketText(ticket, draw, date, price);
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(whatsappUrl, '_blank');
        });
    });

    document.querySelectorAll('.share-facebook').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const ticket = this.dataset.ticket;
            const draw = this.dataset.draw;
            const date = this.dataset.date;
            const price = this.dataset.price;
            
            const text = generateTicketText(ticket, draw, date, price);
            const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(text)}`;
            window.open(facebookUrl, '_blank', 'width=600,height=400');
        });
    });

    document.querySelectorAll('.share-telegram').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const ticket = this.dataset.ticket;
            const draw = this.dataset.draw;
            const date = this.dataset.date;
            const price = this.dataset.price;
            
            const text = generateTicketText(ticket, draw, date, price);
            const telegramUrl = `https://t.me/share/url?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(text)}`;
            window.open(telegramUrl, '_blank');
        });
    });

    document.querySelectorAll('.copy-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const ticket = this.dataset.ticket;
            const draw = this.dataset.draw;
            const date = this.dataset.date;
            const price = this.dataset.price;
            
            const text = generateTicketText(ticket, draw, date, price);
            
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Ticket details copied to clipboard',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                Swal.fire({
                    title: 'Copied!',
                    text: 'Ticket details copied to clipboard',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    });

    // Share all tickets functionality
    document.getElementById('shareAllWhatsApp')?.addEventListener('click', function(e) {
        e.preventDefault();
        const text = generateAllTicketsText();
        const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
        window.open(whatsappUrl, '_blank');
    });

    document.getElementById('shareAllFacebook')?.addEventListener('click', function(e) {
        e.preventDefault();
        const text = generateAllTicketsText();
        const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(text)}`;
        window.open(facebookUrl, '_blank', 'width=600,height=400');
    });

    document.getElementById('shareAllTelegram')?.addEventListener('click', function(e) {
        e.preventDefault();
        const text = generateAllTicketsText();
        const telegramUrl = `https://t.me/share/url?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(text)}`;
        window.open(telegramUrl, '_blank');
    });

    document.getElementById('copyAllTickets')?.addEventListener('click', function(e) {
        e.preventDefault();
        const text = generateAllTicketsText();
        
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                title: 'Copied!',
                text: 'All ticket details copied to clipboard',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            Swal.fire({
                title: 'Copied!',
                text: 'All ticket details copied to clipboard',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
    });
});
</script>

<style>
.swal-wide {
    width: 500px !important;
}

/* Sharing Button Styles */
.btn-group .dropdown-menu {
    min-width: 200px;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}

/* Social media brand colors */
.fab.fa-whatsapp { color: #25D366 !important; }
.fab.fa-facebook { color: #1877F2 !important; }
.fab.fa-telegram { color: #0088CC !important; }

/* Action buttons styling */
.btn-group .btn {
    border-radius: 0.375rem;
}

.btn-group .dropdown-toggle::after {
    margin-left: 0.5em;
}

/* Table action column width */
td:last-child {
    min-width: 120px;
}

/* Ensure draw details links are clickable */
.draw-details-link {
    pointer-events: auto !important;
    position: relative;
    z-index: 10;
}

.draw-details-link:hover {
    text-decoration: underline !important;
    color: #0066cc !important;
}
</style>
@endsection
</x-smart_layout>
