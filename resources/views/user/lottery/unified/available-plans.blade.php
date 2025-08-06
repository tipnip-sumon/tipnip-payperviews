<x-smart_layout>

@push('title', 'Available Plans')

<div class="container-fluid py-4">
    
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-info text-white border-0 shadow-lg">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="h3 mb-2">💰 Available Investment Plans</h2>
                            <p class="mb-0 opacity-75">Use your special tokens to get discounts on investment plans</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column align-items-end">
                                <h4 class="mb-1">{{ $availableTokensCount }}</h4>
                                <small class="opacity-75">Available Tokens</small>
                                <small class="opacity-75">${{ number_format($totalTokenValue, 2) }} Value</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Tokens Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🎫 Your Available Tokens</h5>
                </div>
                <div class="card-body">
                    @if($availableTokensCount > 0)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fe fe-star fs-1 text-warning"></i>
                                    <h4 class="mt-2">{{ $availableTokensCount }}</h4>
                                    <p class="text-muted">Available Tokens</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fe fe-dollar-sign fs-1 text-success"></i>
                                    <h4 class="mt-2">${{ number_format($totalTokenValue, 2) }}</h4>
                                    <p class="text-muted">Total Discount Value</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="fe fe-percent fs-1 text-info"></i>
                                    <h4 class="mt-2">Up to 100%</h4>
                                    <p class="text-muted">Maximum Discount</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Token Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Token Details:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Token Number</th>
                                                <th>Discount Value</th>
                                                <th>Expires</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($availableTokens as $token)
                                                <tr>
                                                    <td>{{ $token->ticket_number }}</td>
                                                    <td>${{ number_format($token->token_discount_amount ?: 2.00, 2) }}</td>
                                                    <td>
                                                        @if($token->token_expires_at)
                                                            {{ $token->token_expires_at->format('M d, Y') }}
                                                        @else
                                                            Never
                                                        @endif
                                                    </td>
                                                    <td><span class="badge bg-success">Active</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fe fe-alert-circle fs-1 text-warning"></i>
                            <h5 class="mt-3">No Available Tokens</h5>
                            <p class="text-muted">You don't have any special tokens available for use.</p>
                            <a href="{{ route('lottery.unified.index') }}" class="btn btn-primary">
                                <i class="fe fe-arrow-left me-2"></i>Back to Lottery Center
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Available Plans -->
    @if($availableTokensCount > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📈 Investment Plans</h5>
                </div>
                <div class="card-body">
                    @if($plans->count() > 0)
                        <div class="row">
                            @foreach($plans as $plan)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fe fe-trending-up fs-1 text-primary"></i>
                                            </div>
                                            <h5 class="card-title">{{ $plan->name }}</h5>
                                            <h4 class="text-primary mb-3">${{ number_format($plan->minimum, 2) }}</h4>
                                            
                                            <ul class="list-unstyled text-start mb-4">
                                                <li class="mb-2">
                                                    <i class="fe fe-check text-success me-2"></i>
                                                    {{ $plan->interest_rate }}% Daily Return
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fe fe-check text-success me-2"></i>
                                                    {{ $plan->duration }} Days Duration
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fe fe-check text-success me-2"></i>
                                                    Capital {{ $plan->capital_return ? 'Returned' : 'Not Returned' }}
                                                </li>
                                                @if($plan->daily_video_limit)
                                                <li class="mb-2">
                                                    <i class="fe fe-video text-info me-2"></i>
                                                    {{ $plan->daily_video_limit }} Videos/Day
                                                </li>
                                                @endif
                                            </ul>
                                            
                                            <!-- Token Usage Calculator -->
                                            <div class="border rounded p-3 mb-3 bg-light">
                                                <h6 class="text-success">Token Discount Available</h6>
                                                @php
                                                    $maxDiscount = min($plan->minimum, $totalTokenValue);
                                                    $finalAmount = $plan->minimum - $maxDiscount;
                                                    $tokensNeeded = ceil($maxDiscount / 2.00); // Assuming $2 per token
                                                @endphp
                                                <p class="mb-1">
                                                    <strong>Max Discount:</strong> ${{ number_format($maxDiscount, 2) }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Tokens Needed:</strong> {{ min($tokensNeeded, $availableTokensCount) }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Final Cost:</strong> ${{ number_format($finalAmount, 2) }}
                                                </p>
                                            </div>
                                            
                                            <a href="{{ route('invest.index') }}?plan={{ $plan->id }}&use_tokens=1" 
                                               class="btn btn-primary w-100">
                                                <i class="fe fe-credit-card me-2"></i>
                                                Invest with Tokens
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fe fe-alert-circle fs-1 text-warning"></i>
                            <h5 class="mt-3">No Plans Available</h5>
                            <p class="text-muted">There are no investment plans available at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('lottery.unified.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left me-2"></i>Back to Lottery Center
            </a>
            <a href="{{ route('plans') }}" class="btn btn-outline-secondary ms-2">
                <i class="fe fe-trending-up me-2"></i>View All Plans
            </a>
        </div>
    </div>

</div>

@push('script')
<script>
$(document).ready(function() {
    // Add any specific JavaScript for the available plans page
    
    // Example: Calculate token usage dynamically
    $('.token-calculator').on('input', function() {
        // Implement dynamic calculation if needed
    });
});
</script>
@endpush

</x-smart_layout>
