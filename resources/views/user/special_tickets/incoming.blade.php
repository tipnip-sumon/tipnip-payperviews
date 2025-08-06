<x-smart_layout>

@section('title', 'Incoming Token Transfers')
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
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
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
    
    .btn-accept {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        color: white;
    }
    
    .btn-reject {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: none;
        color: white;
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .sender-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .sender-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(45deg, #007bff, #6f42c1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .transfer-amount {
        background: linear-gradient(45deg, #28a745, #20c997);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
        font-size: 1.4rem;
    }
</style>
@endpush
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary mb-1">
                                <i class="fas fa-arrow-down me-2"></i>Incoming Token Transfers
                            </h2>
                            <p class="text-muted mb-0">Manage token transfers sent to you by other users</p>
                        </div>
                        <div>
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <a href="{{ route('special.tickets.outgoing') }}" class="btn btn-info">
                                <i class="fas fa-arrow-up me-1"></i>Outgoing Transfers
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
                            <div class="h3 mb-1">{{ $incomingTransfers->where('status', 'pending')->count() }}</div>
                            <div class="small">Pending Transfers</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $incomingTransfers->where('status', 'accepted')->count() }}</div>
                            <div class="small">Accepted Transfers</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">${{ number_format($incomingTransfers->where('status', 'accepted')->sum('transfer_fee'), 2) }}</div>
                            <div class="small">Total Value Received</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="card-body text-center">
                            <div class="h3 mb-1">{{ $incomingTransfers->where('status', 'rejected')->count() }}</div>
                            <div class="small">Rejected Transfers</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incoming Transfers -->
            @if($incomingTransfers->count() > 0)
                @foreach($incomingTransfers as $transfer)
                <div class="transfer-card">
                    <div class="transfer-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="sender-info">
                                <div class="sender-avatar">
                                    {{ strtoupper(substr($transfer->sender->firstname ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $transfer->sender->firstname }} {{ $transfer->sender->lastname }}</div>
                                    <small class="text-muted">{{ $transfer->sender->email }}</small>
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
                            <form method="POST" action="{{ route('special.tickets.accept-transfer', $transfer->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-accept" onclick="return confirm('Are you sure you want to accept this token transfer?')">
                                    <i class="fas fa-check me-1"></i>Accept Transfer
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('special.tickets.reject-transfer', $transfer->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this token transfer?')">
                                    <i class="fas fa-times me-1"></i>Reject Transfer
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
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                @if($incomingTransfers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $incomingTransfers->links() }}
                </div>
                @endif
            @else
            <!-- Empty State -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="mb-4">
                            <i class="fas fa-inbox fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Incoming Transfers</h4>
                        <p class="text-muted mb-4">You haven't received any token transfers yet.</p>
                        <div>
                            <a href="{{ route('special.tickets.transfer') }}" class="btn btn-primary me-2">
                                <i class="fas fa-paper-plane me-1"></i>Send Transfer
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
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>How Incoming Transfers Work
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">What You Can Do:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Review incoming token transfers
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Accept transfers you want to receive
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Reject transfers you don't want
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    View complete transfer history
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    You must accept transfers to receive tokens
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    Pending transfers will expire if not processed
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    All transfers are secure and tracked
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-bell text-primary me-2"></i>
                                    You'll receive notifications for new transfers
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
    
    // Add confirmation dialogs for actions
    document.querySelectorAll('form[action*="accept-transfer"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to accept this token transfer?')) {
                e.preventDefault();
            }
        });
    });
    
    document.querySelectorAll('form[action*="reject-transfer"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to reject this token transfer? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
</x-smart_layout>
