<!-- Share Modal Content -->
<div class="text-center mb-4">
    <h4 class="text-primary mb-2">🚀 Invite Friends & Earn Rewards!</h4>
    <p class="text-muted">Share your referral link and earn money when your friends join and invest.</p>
</div>

<!-- Referral Link -->
<div class="mb-4">
    <label class="form-label fw-bold">Your Referral Link</label>
    <div class="input-group">
        <input type="text" class="form-control" id="modalReferralLink" 
               value="{{ route('register') }}?ref={{ auth()->user()->username }}" readonly>
        <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
            <i class="fe fe-copy me-1"></i>Copy
        </button>
    </div>
    <small class="text-muted">Share this link to earn $2 when someone joins + $5 when they invest!</small>
</div>

<!-- Social Share Buttons -->
<div class="row g-2">
    @php
        $shareText = "🎰 Join me on this amazing lottery platform! Win prizes, earn rewards, and get special tokens. Use my link to get started:";
        $referralLink = route('register') . '?ref=' . auth()->user()->username;
    @endphp
    
    <div class="col-4">
        <button type="button" class="btn btn-primary w-100" 
                onclick="shareOnPlatform('facebook', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fab fa-facebook-f me-1"></i>Facebook
        </button>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-info w-100" 
                onclick="shareOnPlatform('twitter', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fab fa-twitter me-1"></i>Twitter
        </button>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-success w-100" 
                onclick="shareOnPlatform('whatsapp', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fab fa-whatsapp me-1"></i>WhatsApp
        </button>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-info w-100" 
                onclick="shareOnPlatform('telegram', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fab fa-telegram-plane me-1"></i>Telegram
        </button>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-primary w-100" 
                onclick="shareOnPlatform('linkedin', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fab fa-linkedin-in me-1"></i>LinkedIn
        </button>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-warning w-100" 
                onclick="shareOnPlatform('email', '{{ $referralLink }}', '{{ $shareText }}')">
            <i class="fe fe-mail me-1"></i>Email
        </button>
    </div>
</div>

<!-- Rewards Summary -->
<div class="mt-4 p-3 bg-light rounded">
    <h6 class="mb-2">💰 Earn with Every Referral:</h6>
    <div class="row text-center">
        <div class="col-4">
            <strong class="text-primary">$2</strong>
            <br><small class="text-muted">Join Bonus</small>
        </div>
        <div class="col-4">
            <strong class="text-success">$5</strong>
            <br><small class="text-muted">First Investment</small>
        </div>
        <div class="col-4">
            <strong class="text-warning">2%</strong>
            <br><small class="text-muted">Ongoing Commission</small>
        </div>
    </div>
</div>
