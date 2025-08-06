<x-smart_layout>

@section('title', $pageTitle ?? 'Special Tickets History')
@section('content')
@push('style')
<style>
    .history-card {
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .history-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-active {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #4caf50;
    }
    
    .status-used {
        background: #e3f2fd;
        color: #1565c0;
        border: 1px solid #2196f3;
    }
    
    .status-expired {
        background: #fce4ec;
        color: #c2185b;
        border: 1px solid #e91e63;
    }
    
    .status-refunded {
        background: #fff3e0;
        color: #ef6c00;
        border: 1px solid #ff9800;
    }
    
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
    }
    
    .stats-summary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 8px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #2196f3;
    }
    
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 14px;
        top: 18px;
        width: 2px;
        height: calc(100% - 10px);
        background: #e0e6ed;
    }
    
    .timeline-item:last-child::after {
        display: none;
    }
    
    .amount-highlight {
        background: linear-gradient(45deg, #4caf50, #8bc34a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
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
                                <i class="fas fa-history me-2"></i>Special Tickets History
                            </h2>
                            <p class="text-muted mb-0">Complete history of your special lottery tickets and usage</p>
                        </div>
                        <div>
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <a href="{{ route('special.tickets.statistics') }}" class="btn btn-info">
                                <i class="fas fa-chart-bar me-1"></i>View Statistics
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtered Statistics Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-summary text-center">
                        <div class="h4 mb-1">{{ $filteredStats['total_tickets'] }}</div>
                        <div class="small">Total Tickets</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-summary text-center">
                        <div class="h4 mb-1">${{ number_format($filteredStats['total_discount_used'], 2) }}</div>
                        <div class="small">Discount Used</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-summary text-center">
                        <div class="h4 mb-1">{{ $filteredStats['active_tokens'] }}</div>
                        <div class="small">Active Tokens</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-summary text-center">
                        <div class="h4 mb-1">${{ number_format($filteredStats['total_refunds'], 2) }}</div>
                        <div class="small">Total Refunds</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-header filter-card">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter & Search
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('special.tickets.history') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="used_as_token" {{ request('status') == 'used_as_token' ? 'selected' : '' }}>Used as Token</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Actions</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('special.tickets.history') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tickets History -->
            @if($tickets->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Tickets History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($tickets as $ticket)
                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="history-card p-4 h-100">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="text-primary mb-1">Ticket #{{ $ticket->id }}</h6>
                                        <small class="text-muted">{{ $ticket->purchased_at->format('M d, Y g:i A') }}</small>
                                    </div>
                                    <span class="status-badge status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </div>

                                <!-- Timeline -->
                                <div class="timeline-item">
                                    <strong>Purchased</strong>
                                    <div class="text-muted small">{{ $ticket->purchased_at->format('M d, Y g:i A') }}</div>
                                    <div class="amount-highlight">${{ number_format($ticket->purchase_amount, 2) }}</div>
                                </div>

                                @if($ticket->lotteryDraw)
                                <div class="timeline-item">
                                    <strong>Lottery Draw</strong>
                                    <div class="text-muted small">{{ $ticket->lotteryDraw->draw_date->format('M d, Y') }}</div>
                                    @if($ticket->is_winner)
                                        <div class="text-success small"><i class="fas fa-trophy me-1"></i>Winner!</div>
                                    @else
                                        <div class="text-muted small">Not winning</div>
                                    @endif
                                </div>
                                @endif

                                @if($ticket->status == 'used_as_token' && $ticket->used_as_token_at)
                                <div class="timeline-item">
                                    <strong>Used as Token</strong>
                                    <div class="text-muted small">{{ $ticket->used_as_token_at->format('M d, Y g:i A') }}</div>
                                    <div class="text-success small">
                                        Discount: ${{ number_format($ticket->token_discount_amount, 2) }}
                                    </div>
                                    @if($ticket->early_usage_bonus > 0)
                                    <div class="text-info small">
                                        Bonus: ${{ number_format($ticket->early_usage_bonus, 2) }}
                                    </div>
                                    @endif
                                </div>
                                @endif

                                @if($ticket->status == 'refunded' && $ticket->refunded_at)
                                <div class="timeline-item">
                                    <strong>Refunded</strong>
                                    <div class="text-muted small">{{ $ticket->refunded_at->format('M d, Y g:i A') }}</div>
                                    <div class="text-warning small">
                                        Amount: ${{ number_format($ticket->refund_amount, 2) }}
                                    </div>
                                </div>
                                @endif

                                <!-- Additional Info -->
                                <div class="mt-3 pt-3 border-top">
                                    <div class="row text-center">
                                        @if($ticket->is_valid_token && $ticket->status == 'active')
                                        <div class="col-6">
                                            <small class="text-muted d-block">Token Value</small>
                                            <span class="fw-bold text-success">${{ number_format($ticket->token_discount_amount, 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Expires</small>
                                            <span class="fw-bold text-warning">{{ $ticket->expires_at->format('M d') }}</span>
                                        </div>
                                        @elseif($ticket->usedForPlan)
                                        <div class="col-12">
                                            <small class="text-muted d-block">Used for Plan</small>
                                            <span class="fw-bold text-primary">{{ $ticket->usedForPlan->name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($tickets->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-history fa-3x"></i>
                    </div>
                    <h5 class="text-muted">No History Found</h5>
                    <p class="text-muted">No tickets found matching your filter criteria.</p>
                    <a href="{{ route('special.tickets.history') }}" class="btn btn-primary">
                        <i class="fas fa-undo me-1"></i>Clear Filters
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
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
    
    document.querySelectorAll('.history-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
    
    // Auto-submit form on date change
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                // Auto-submit if both dates are filled
                const form = this.closest('form');
                const dateFrom = form.querySelector('input[name="date_from"]').value;
                const dateTo = form.querySelector('input[name="date_to"]').value;
                
                if (dateFrom && dateTo) {
                    form.submit();
                }
            }
        });
    });
});
</script>
@endpush
</x-smart_layout>
