<x-smart_layout>
    @section('title', 'Referral Dashboard')
    @section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-share-alt me-2"></i>
                                Referral Dashboard
                            </h4>
                            <p class="mb-0 opacity-75">Invite friends and earn commissions on their activities!</p>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">${{ number_format($referralEarnings, 2) }}</h3>
                            <small class="opacity-75">Total Referral Earnings</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 text-warning mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Total Referrals</h5>
                    <h3 class="text-warning">{{ $totalReferrals }}</h3>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 text-success mb-2">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="card-title">Active Referrals</h5>
                    <h3 class="text-success">{{ $activeReferrals }}</h3>
                    <small class="text-muted">Currently active</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 text-info mb-2">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h5 class="card-title">Commission Rate</h5>
                    <h3 class="text-info">10%</h3>
                    <small class="text-muted">Per referral activity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Link Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>Your Referral Link
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-link"></i>
                                </span>
                                <input type="text" class="form-control" id="referralLink" value="{{ $referralLink }}" readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()" id="copyBtn">
                                    <i class="fas fa-copy me-1"></i>Copy
                                </button>
                            </div>
                            <small class="text-muted">Share this link with friends to earn commissions when they join and start earning!</small>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-success" onclick="shareReferralLink()">
                                <i class="fas fa-share-alt me-2"></i>Share Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Referrals and Actions -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Recent Referrals
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentReferrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Joined Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReferrals as $referral)
                                    <tr>
                                        <td>
                                            <strong>{{ $referral->username }}</strong>
                                        </td>
                                        <td>
                                            {{ $referral->firstname }} {{ $referral->lastname }}
                                        </td>
                                        <td>
                                            @if($referral->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-warning">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            {{ $referral->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('user.sponsor-list') }}" class="btn btn-outline-primary">
                                View All Referrals
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="display-6 text-muted mb-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h6 class="text-muted">No referrals yet</h6>
                            <p class="text-muted">Start sharing your referral link to earn commissions!</p>
                            <button class="btn btn-primary" onclick="copyReferralLink()">
                                <i class="fas fa-copy me-2"></i>Copy Referral Link
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-gift me-2"></i>Referral Benefits
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Commission Earnings</h6>
                                    <small class="text-muted">Earn 10% on referral activities</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Bonus Rewards</h6>
                                    <small class="text-muted">Special bonuses for active referrers</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Performance Tracking</h6>
                                    <small class="text-muted">Monitor your referral progress</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('user.refferral-history') }}" class="btn btn-outline-success">
                            <i class="fas fa-chart-bar me-2"></i>View Earnings History
                        </a>
                        <a href="{{ route('user.team-tree') }}" class="btn btn-outline-info">
                            <i class="fas fa-sitemap me-2"></i>View Team Tree
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #007bff);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

@media (max-width: 768px) {
    .display-6 {
        font-size: 1.5rem;
    }
}
</style>

<script>
function copyReferralLink() {
    const linkInput = document.getElementById('referralLink');
    const copyBtn = document.getElementById('copyBtn');
    
    // Copy to clipboard
    linkInput.select();
    document.execCommand('copy');
    
    // Update button text
    const originalText = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
    copyBtn.classList.remove('btn-outline-primary');
    copyBtn.classList.add('btn-success');
    
    // Reset button after 2 seconds
    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-outline-primary');
    }, 2000);
    
    // Show toast notification if available
    if (typeof showToast === 'function') {
        showToast('Referral link copied to clipboard!', 'success');
    }
}

function shareReferralLink() {
    const referralLink = document.getElementById('referralLink').value;
    const shareText = `Join PayPerViews and start earning money by watching videos! Use my referral link: ${referralLink}`;
    
    // Check if Web Share API is available
    if (navigator.share) {
        navigator.share({
            title: 'Join PayPerViews - Earn Money Watching Videos',
            text: shareText,
            url: referralLink
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: Copy to clipboard and show share options
        copyReferralLink();
        
        // Create share modal or show options
        const shareOptions = `
            <div class="alert alert-info mt-3">
                <h6><i class="fas fa-share-alt me-2"></i>Share your referral link:</h6>
                <div class="d-flex gap-2 mt-2">
                    <a href="https://wa.me/?text=${encodeURIComponent(shareText)}" target="_blank" class="btn btn-success btn-sm">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </a>
                    <a href="https://t.me/share/url?url=${encodeURIComponent(referralLink)}&text=${encodeURIComponent('Join PayPerViews and earn money!')}" target="_blank" class="btn btn-info btn-sm">
                        <i class="fab fa-telegram me-1"></i>Telegram
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralLink)}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fab fa-facebook me-1"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}" target="_blank" class="btn btn-dark btn-sm">
                        <i class="fab fa-twitter me-1"></i>Twitter
                    </a>
                </div>
            </div>
        `;
        
        // Add share options after the referral link section
        const cardBody = document.querySelector('.card-body');
        if (!document.querySelector('.share-options')) {
            const shareDiv = document.createElement('div');
            shareDiv.className = 'share-options';
            shareDiv.innerHTML = shareOptions;
            cardBody.appendChild(shareDiv);
            
            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (shareDiv.parentNode) {
                    shareDiv.remove();
                }
            }, 10000);
        }
    }
}

// Add card hover effects
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
        });
    });
});
</script>
    @endsection
</x-smart_layout>
