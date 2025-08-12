<x-smart_layout>

@section('title', 'Lottery & Ticket Center')

@section('content')
<div class="container-fluid my-4">
    
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow-lg">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="h3 mb-2">🎰 Lottery & Ticket Center</h2>
                            <p class="mb-0 opacity-75">Your complete hub for lottery tickets, special tokens, sponsor rewards, and sharing opportunities</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column align-items-end">
                                <h4 class="mb-1">${{ number_format($totalBalance, 2) }}</h4>
                                <small class="opacity-75">Total Wallet Balance</small>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="fe fe-ticket fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $lotteryTicketsCount }}</h3>
                    <small>Lottery Tickets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="fe fe-star fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $specialTokensCount + $sponsorTicketsCount }}</h3>
                    <small>Reward Tokens</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="fe fe-share-2 fs-1 mb-2"></i>
                    <h3 class="mb-1">{{ $sharesCount ?? 0 }}</h3>
                    <small>Shares Made</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-danger text-white h-100">
                <div class="card-body text-center">
                    <i class="fe fe-trophy fs-1 mb-2"></i>
                    <h3 class="mb-1">${{ number_format($totalWinnings, 2) }}</h3>
                    <small>Total Winnings</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="lotteryTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                                <i class="fe fe-home me-2"></i>Dashboard
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lottery-tab" data-bs-toggle="tab" data-bs-target="#lottery" type="button" role="tab">
                                <i class="fe fe-play-circle me-2"></i>Lottery System
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tokens-tab" data-bs-toggle="tab" data-bs-target="#tokens" type="button" role="tab">
                                <i class="fe fe-star me-2"></i>Reward Tokens
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sharing-tab" data-bs-toggle="tab" data-bs-target="#sharing" type="button" role="tab">
                                <i class="fe fe-share-2 me-2"></i>Share & Earn
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="lotteryTabsContent">
                        
                        <!-- Dashboard Tab -->
                        <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                            @include('user.lottery.unified.dashboard')
                        </div>
                        
                        <!-- Lottery Tab -->
                        <div class="tab-pane fade" id="lottery" role="tabpanel">
                            @include('user.lottery.unified.lottery')
                        </div>
                        
                        <!-- Reward Tokens Tab (Combined Special + Sponsor) -->
                        <div class="tab-pane fade" id="tokens" role="tabpanel">
                            @include('user.lottery.unified.reward-tokens')
                        </div>
                        
                        <!-- Sharing Tab -->
                        <div class="tab-pane fade" id="sharing" role="tabpanel">
                            @include('user.lottery.unified.sharing')
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🕒 Recent Activity</h5>
                    <a href="{{ route('lottery.unified.activity.all') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="timeline">
                            @foreach($recentActivity as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker {{ $activity['type_class'] }}">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $activity['title'] }}</h6>
                                        <p class="timeline-text">{{ $activity['description'] }}</p>
                                        @if(isset($activity['amount']) && $activity['amount'] > 0)
                                            <div class="badge bg-success">Prize: ${{ number_format($activity['amount'], 2) }}</div>
                                        @endif
                                        @if(isset($activity['claim_status']))
                                            <div class="badge bg-{{ $activity['claim_status'] === 'claimed' ? 'success' : 'warning' }} ms-2">
                                                {{ ucfirst($activity['claim_status']) }}
                                            </div>
                                        @endif
                                        <small class="text-muted d-block mt-1">{{ $activity['time_ago'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fe fe-activity fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Recent Activity</h5>
                            <p class="text-muted">Start participating in lottery, tokens, or sharing to see activity here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Sharing Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">🚀 Share & Earn Rewards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('user.lottery.unified.share-modal')
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
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-marker.bg-success { background-color: #28a745; }
.timeline-marker.bg-warning { background-color: #ffc107; }
.timeline-marker.bg-info { background-color: #17a2b8; }
.timeline-marker.bg-danger { background-color: #dc3545; }

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 8px;
    color: #6c757d;
}

.share-platform {
    transition: all 0.3s ease;
    cursor: pointer;
}

.share-platform:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.gradient-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse-animation {
    animation: pulse 2s infinite;
}
</style>
@endpush

@push('script')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Sharing functionality
function shareOnPlatform(platform, url, text) {
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(text)}&body=${encodeURIComponent(url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
        
        // Track share event
        fetch('{{ route("lottery.unified.track.share") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                platform: platform,
                content_type: 'lottery_center'
            })
        });
    }
}

// Copy referral link
function copyReferralLink() {
    const referralLink = document.getElementById('referralLink');
    referralLink.select();
    referralLink.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show success message
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fe fe-check me-2"></i>Copied!';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

// Auto-refresh lottery countdown
setInterval(function() {
    fetch('{{ route("lottery.unified.countdown") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('lotteryCountdown').innerHTML = data.countdown;
        });
}, 1000);

// Real-time notifications
if ('WebSocket' in window) {
    // WebSocket connection for real-time updates
    // Implementation depends on your WebSocket setup
}
</script>
@endpush

</x-smart_layout>
