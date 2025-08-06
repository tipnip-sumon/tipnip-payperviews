<x-smart_layout>

@section('title', 'Outgoing Token Transfers')
@section('content')
@push('style')
<style>
    .transfer-card {
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: white;
        margin-bottom: 20px;
    }
    
    .transfer-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .transfer-header {
        background: linear-gradient(135deg, #fff3e0 0%, #e8f5e8 100%);
        border-radius: 12px 12px 0 0;
        padding: 20px;
        border-bottom: 1px solid #e0e6ed;
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-accepted {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-cancelled {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }
    
    .token-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }
    
    .btn-cancel {
        background: linear-gradient(45deg, #6c757d, #5a6268);
        border: none;
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .recipient-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .recipient-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .transfer-amount {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
        font-size: 1.4rem;
    }
    
    .progress-tracker {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px 0;
        position: relative;
    }
    
    .progress-step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        position: relative;
        z-index: 2;
    }
    
    .progress-step.completed {
        background: #28a745;
    }
    
    .progress-step.current {
        background: #ffc107;
        color: #212529;
    }
    
    .progress-step.pending {
        background: #6c757d;
    }
    
    .progress-line {
        position: absolute;
        top: 50%;
        left: 40px;
        right: 40px;
        height: 3px;
        background: #e9ecef;
        z-index: 1;
    }
    
    .progress-line.completed {
        background: #28a745;
    }
</style>
@endpush


<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary mb-1">
                                <i class="fas fa-arrow-up me-2"></i>Outgoing Token Transfers
                            </h2>
                            <p class="text-muted mb-0">Track token transfers you've sent to other users</p>
                        </div>
                        <div>
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <a href="{{ route('special.tickets.incoming') }}" class="btn btn-success me-2">
                                <i class="fas fa-arrow-down me-1"></i>Incoming Transfers
                            </a>
                            <a href="{{ route('special.tickets.transfer') }}" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>New Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $outgoingTransfers->where('status', 'pending')->count() }}</div>
                            <div class="small">Pending Transfers</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $outgoingTransfers->where('status', 'accepted')->count() }}</div>
                            <div class="small">Accepted Transfers</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">${{ number_format($outgoingTransfers->where('status', 'accepted')->sum('transfer_fee'), 2) }}</div>
                            <div class="small">Total Value Sent</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $outgoingTransfers->whereIn('status', ['rejected', 'cancelled'])->count() }}</div>
                            <div class="small">Rejected/Cancelled</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outgoing Transfers -->
            @if($outgoingTransfers->count() > 0)
                @foreach($outgoingTransfers as $transfer)
                <div class="transfer-card">
                    <div class="transfer-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="recipient-info">
                                <div class="recipient-avatar">
                                    {{ strtoupper(substr($transfer->recipient->firstname ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $transfer->recipient->firstname }} {{ $transfer->recipient->lastname }}</div>
                                    <small class="text-muted">{{ $transfer->recipient->email }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-{{ $transfer->status }}">
                                    {{ ucfirst($transfer->status) }}
                                </span>
                                <div class="small text-muted mt-1">
                                    {{ $transfer->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Progress Tracker -->
                        <div class="progress-tracker">
                            <div class="progress-line {{ in_array($transfer->status, ['accepted', 'rejected']) ? 'completed' : '' }}"></div>
                            
                            <div class="progress-step completed">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            
                            <div class="progress-step {{ $transfer->status === 'pending' ? 'current' : ($transfer->status === 'accepted' ? 'completed' : 'pending') }}">
                                <i class="fas fa-clock"></i>
                            </div>
                            
                            <div class="progress-step {{ $transfer->status === 'accepted' ? 'completed' : 'pending' }}">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="text-center mb-3">
                            <small class="text-muted">
                                @if($transfer->status === 'pending')
                                    Waiting for recipient to accept
                                @elseif($transfer->status === 'accepted')
                                    Transfer completed successfully
                                @elseif($transfer->status === 'rejected')
                                    Transfer was rejected by recipient
                                @elseif($transfer->status === 'cancelled')
                                    Transfer was cancelled by you
                                @endif
                            </small>
                        </div>

                        <!-- Transfer Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="token-info">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-ticket-alt me-2"></i>Token Details
                                    </h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Token ID</small>
                                            <span class="fw-bold">#{{ $transfer->specialTicket->id }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Token Value</small>
                                            <span class="fw-bold text-success">${{ number_format($transfer->specialTicket->token_discount_amount, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Draw Date</small>
                                            <span class="fw-bold">{{ $transfer->specialTicket->lotteryDraw->draw_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Expires</small>
                                            <span class="fw-bold text-warning">{{ $transfer->specialTicket->expires_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="token-info">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-exchange-alt me-2"></i>Transfer Details
                                    </h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Transfer Type</small>
                                            <span class="fw-bold text-capitalize">{{ str_replace('_', ' ', $transfer->transfer_type) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Transfer Fee</small>
                                            <span class="transfer-amount">${{ number_format($transfer->transfer_fee, 2) }}</span>
                                        </div>
                                    </div>
                                    @if($transfer->notes)
                                    <div class="mt-2">
                                        <small class="text-muted d-block">Notes</small>
                                        <span class="fw-bold">{{ $transfer->notes }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons for Pending Transfers -->
                        @if($transfer->status === 'pending')
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('special.tickets.cancel-transfer', $transfer->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-cancel" onclick="return confirm('Are you sure you want to cancel this transfer?')">
                                    <i class="fas fa-times me-1"></i>Cancel Transfer
                                </button>
                            </form>
                        </div>
                        @endif

                        <!-- Status Information -->
                        @if($transfer->status === 'accepted')
                        <div class="text-center mt-3">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Transfer accepted on {{ $transfer->processed_at ? $transfer->processed_at->format('M d, Y g:i A') : 'N/A' }}
                            </div>
                        </div>
                        @elseif($transfer->status === 'rejected')
                        <div class="text-center mt-3">
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-times-circle me-2"></i>
                                Transfer rejected on {{ $transfer->processed_at ? $transfer->processed_at->format('M d, Y g:i A') : 'N/A' }}
                            </div>
                        </div>
                        @elseif($transfer->status === 'cancelled')
                        <div class="text-center mt-3">
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-ban me-2"></i>
                                Transfer cancelled on {{ $transfer->processed_at ? $transfer->processed_at->format('M d, Y g:i A') : 'N/A' }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($outgoingTransfers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $outgoingTransfers->links() }}
                </div>
                @endif
            @else
            <!-- Empty State -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="mb-4">
                            <i class="fas fa-paper-plane fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Outgoing Transfers</h4>
                        <p class="text-muted mb-4">You haven't sent any token transfers yet.</p>
                        <div>
                            <a href="{{ route('special.tickets.transfer') }}" class="btn btn-primary me-2">
                                <i class="fas fa-paper-plane me-1"></i>Send First Transfer
                            </a>
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-ticket-alt me-1"></i>View My Tokens
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Help Section -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Transfer Management Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Track Your Transfers:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-eye text-info me-2"></i>
                                    Monitor transfer status in real-time
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    Cancel pending transfers if needed
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-history text-secondary me-2"></i>
                                    View complete transfer history
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-bell text-primary me-2"></i>
                                    Receive notifications on status changes
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Transfer Status Guide:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <span class="status-badge status-pending me-2">Pending</span>
                                    Waiting for recipient acceptance
                                </li>
                                <li class="mb-2">
                                    <span class="status-badge status-accepted me-2">Accepted</span>
                                    Transfer completed successfully
                                </li>
                                <li class="mb-2">
                                    <span class="status-badge status-rejected me-2">Rejected</span>
                                    Recipient declined the transfer
                                </li>
                                <li class="mb-2">
                                    <span class="status-badge status-cancelled me-2">Cancelled</span>
                                    Transfer cancelled by sender
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate transfer cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.transfer-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
    
    // Add confirmation dialog for cancellation
    document.querySelectorAll('form[action*="cancel-transfer"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to cancel this transfer? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Animate progress trackers
    setTimeout(() => {
        document.querySelectorAll('.progress-tracker').forEach(tracker => {
            tracker.style.opacity = '0';
            tracker.style.transform = 'scale(0.8)';
            tracker.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                tracker.style.opacity = '1';
                tracker.style.transform = 'scale(1)';
            }, 100);
        });
    }, 500);
});
</script>
@endpush
@endsection
</x-smart_layout>

{{-- The above code is a complete Blade template for displaying outgoing token transfers in a user interface. It includes sections for statistics, individual transfer cards, and helpful tips for managing transfers. The design is responsive and visually appealing, with animations and interactive elements to enhance user experience. --}}
