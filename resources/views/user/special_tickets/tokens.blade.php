<x-smart_layout>

@section('title', $pageTitle ?? 'Special Discount Tokens')
@section('content')
@push('style')
<style>
    .token-card {
        border: 2px solid #e3f2fd;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
    }
    
    .token-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,123,255,0.15);
        border-color: #2196f3;
    }
    
    .token-badge {
        background: linear-gradient(45deg, #2196f3, #21cbf3);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
    }
    
    .token-usage-item {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 0 8px 8px 0;
    }
    
    .discount-amount {
        background: linear-gradient(45deg, #28a745, #20c997);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .use-token-btn {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .use-token-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(238, 90, 36, 0.3);
        color: white;
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
                                <i class="fas fa-ticket-alt me-2"></i>Special Discount Tokens
                            </h2>
                            <p class="text-muted mb-0">Use your special tickets as discount tokens for plan purchases</p>
                        </div>
                        <div>
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h3 mb-1">{{ $availableTokens->count() }}</div>
                        <div class="small">Available Tokens</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h3 mb-1">{{ $usedTokens->count() }}</div>
                        <div class="small">Used Tokens</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h3 mb-1">${{ number_format($availableTokens->sum('token_discount_amount'), 2) }}</div>
                        <div class="small">Total Discount Value</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="h3 mb-1">${{ number_format($usedTokens->sum('token_discount_amount'), 2) }}</div>
                        <div class="small">Total Used Value</div>
                    </div>
                </div>
            </div>

            <!-- Available Tokens -->
            @if($availableTokens->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-coins me-2"></i>Available Discount Tokens
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($availableTokens as $token)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="token-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="token-badge">
                                        Token #{{ $token->id }}
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Valid until</small>
                                        <div class="fw-bold">{{ $token->expires_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-3">
                                    <div class="discount-amount">
                                        ${{ number_format($token->token_discount_amount, 2) }}
                                    </div>
                                    <small class="text-muted">Discount Value</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Draw Date</small>
                                            <span class="fw-bold">{{ $token->lotteryDraw->draw_date->format('M d') }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Bonus</small>
                                            <span class="fw-bold text-success">${{ number_format($token->early_usage_bonus, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button class="btn use-token-btn btn-sm w-100" onclick="useToken({{ $token->id }})">
                                        <i class="fas fa-shopping-cart me-1"></i>Use for Purchase
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-ticket-alt fa-3x"></i>
                    </div>
                    <h5 class="text-muted">No Available Tokens</h5>
                    <p class="text-muted">You don't have any special tickets that can be used as discount tokens.</p>
                    <a href="{{ route('special.tickets.index') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i>View My Tickets
                    </a>
                </div>
            </div>
            @endif

            <!-- Token Usage History -->
            @if($usedTokens->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Recent Token Usage
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($usedTokens as $token)
                    <div class="token-usage-item">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="fw-bold">Token #{{ $token->id }}</div>
                                <small class="text-muted">{{ $token->used_as_token_at->format('M d, Y g:i A') }}</small>
                            </div>
                            <div class="col-md-3">
                                <div class="fw-bold text-primary">{{ $token->usedForPlan->name ?? 'Unknown Plan' }}</div>
                                <small class="text-muted">Plan Purchase</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-success">${{ number_format($token->token_discount_amount, 2) }}</div>
                                <small class="text-muted">Discount Applied</small>
                            </div>
                            <div class="col-md-2">
                                <div class="fw-bold text-info">${{ number_format($token->early_usage_bonus, 2) }}</div>
                                <small class="text-muted">Bonus Earned</small>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="badge bg-success">Used</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('special.tickets.history') }}" class="btn btn-outline-success">
                            <i class="fas fa-list me-1"></i>View Full History
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- How to Use Tokens -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>How to Use Discount Tokens
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Steps to Use:</h6>
                            <ol class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Click "Use for Purchase" on any available token
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Select the plan you want to purchase
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Token discount will be automatically applied
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Earn additional early usage bonus
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    Each token can only be used once
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    Tokens expire after the lottery draw date
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-gift text-success me-2"></i>
                                    Early usage earns bonus rewards
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exchange-alt text-primary me-2"></i>
                                    Tokens can be transferred to other users
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
function useToken(tokenId) {
    // Redirect to investment page where user can use the token for purchase
    window.location.href = `/invest?use_token=${tokenId}`;
}

// Add some interactive animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate token cards on scroll
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
    
    document.querySelectorAll('.token-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
@endpush
@endsection
</x-smart_layout>

{{-- The above code is a complete Blade template for displaying special discount tokens in a user interface. It includes sections for statistics, available tokens, token usage history, and instructions on how to use the tokens. The design is responsive and visually appealing, with animations and interactive elements to enhance user experience. --}}
