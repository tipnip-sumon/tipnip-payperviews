<x-smart_layout>

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Share & Earn</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lottery.unified.index') }}">Lottery</a></li>
                        <li class="breadcrumb-item active">Share & Earn</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Share & Earn Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-primary">{{ $sharingStats['total_referrals'] }}</h2>
                        <p class="text-muted mb-1">Total Referrals</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-success">{{ $sharingStats['active_referrals'] }}</h2>
                        <p class="text-muted mb-1">Active Referrals</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-warning">${{ number_format($sharingStats['potential_earnings'], 2) }}</h2>
                        <p class="text-muted mb-1">Potential Earnings</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-info">{{ $sharingStats['share_count'] }}</h2>
                        <p class="text-muted mb-1">Times Shared</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Link Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-link me-2"></i>Your Referral Link</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control" id="referralLink" 
                                       value="{{ $sharingStats['referral_link'] }}" readonly>
                                <button class="btn btn-primary" type="button" onclick="copyReferralLink()">
                                    <i class="fas fa-copy me-1"></i>Copy Link
                                </button>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Share this link with friends to earn rewards when they join and make their first investment!
                            </small>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-success" onclick="openShareModal()">
                                <i class="fas fa-share-alt me-1"></i>Share Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Sharing Platforms -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-share-alt me-2"></i>Share on Social Platforms</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $shareText = "Join me on " . config('app.name') . " and start earning! Use my referral link to get started.";
                            $platforms = [
                                [
                                    'name' => 'Facebook',
                                    'icon' => 'fab fa-facebook-f',
                                    'color' => 'primary',
                                    'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($sharingStats['referral_link'])
                                ],
                                [
                                    'name' => 'Twitter',
                                    'icon' => 'fab fa-twitter',
                                    'color' => 'info',
                                    'url' => 'https://twitter.com/intent/tweet?text=' . urlencode($shareText) . '&url=' . urlencode($sharingStats['referral_link'])
                                ],
                                [
                                    'name' => 'WhatsApp',
                                    'icon' => 'fab fa-whatsapp',
                                    'color' => 'success',
                                    'url' => 'https://wa.me/?text=' . urlencode($shareText . ' ' . $sharingStats['referral_link'])
                                ],
                                [
                                    'name' => 'Telegram',
                                    'icon' => 'fab fa-telegram-plane',
                                    'color' => 'primary',
                                    'url' => 'https://t.me/share/url?url=' . urlencode($sharingStats['referral_link']) . '&text=' . urlencode($shareText)
                                ],
                                [
                                    'name' => 'LinkedIn',
                                    'icon' => 'fab fa-linkedin-in',
                                    'color' => 'dark',
                                    'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($sharingStats['referral_link'])
                                ],
                                [
                                    'name' => 'Email',
                                    'icon' => 'fas fa-envelope',
                                    'color' => 'secondary',
                                    'url' => 'mailto:?subject=' . urlencode('Join me on ' . config('app.name')) . '&body=' . urlencode($shareText . ' ' . $sharingStats['referral_link'])
                                ]
                            ];
                        @endphp

                        @foreach($platforms as $platform)
                        <div class="col-md-4 col-lg-2">
                            <a href="{{ $platform['url'] }}" target="_blank" 
                               class="btn btn-{{ $platform['color'] }} w-100 mb-2 share-button"
                               onclick="return trackShare('{{ strtolower($platform['name']) }}')">
                                <i class="{{ $platform['icon'] }} me-2"></i>
                                {{ $platform['name'] }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-question-circle me-2"></i>How Referral System Works</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-share text-white fs-4"></i>
                                </div>
                                <h6 class="mt-3">1. Share Your Link</h6>
                                <p class="text-muted small">Copy and share your unique referral link with friends, family, or on social media.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-plus text-white fs-4"></i>
                                </div>
                                <h6 class="mt-3">2. Friends Join</h6>
                                <p class="text-muted small">When someone uses your link to register, they become your referral.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-coins text-white fs-4"></i>
                                </div>
                                <h6 class="mt-3">3. Earn Rewards</h6>
                                <p class="text-muted small">Get special lottery tickets and bonuses when your referrals make investments.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Referrals -->
    @if($sharingStats['total_referrals'] > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Recent Referrals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Join Date</th>
                                    <th>Status</th>
                                    <th>Total Investment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->referrals()->latest()->take(10)->get() as $referral)
                                <tr>
                                    <td>{{ $referral->username }}</td>
                                    <td>{{ $referral->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($referral->invests()->where('status', 1)->exists())
                                            <span class="badge bg-success">Active Investor</span>
                                        @else
                                            <span class="badge bg-secondary">Registered</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($referral->invests()->where('status', 1)->sum('amount'), 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No referrals yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('script')
<script>
function copyReferralLink() {
    const linkInput = document.getElementById('referralLink');
    const referralLink = linkInput.value;
    
    // Modern clipboard API with fallback
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(referralLink).then(function() {
            // Success with modern API
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'Your referral link has been copied to clipboard',
                html: `
                    <div class="mb-3">
                        <p class="mb-2">Your referral link has been copied to clipboard!</p>
                        <div class="alert alert-light border">
                            <small class="text-muted">${referralLink}</small>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Great!',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }).catch(function(err) {
            // Fallback if modern API fails
            fallbackCopyTextToClipboard(referralLink);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(referralLink);
    }
}

function fallbackCopyTextToClipboard(text) {
    const linkInput = document.getElementById('referralLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'Your referral link has been copied to clipboard',
                html: `
                    <div class="mb-3">
                        <p class="mb-2">Your referral link has been copied to clipboard!</p>
                        <div class="alert alert-light border">
                            <small class="text-muted">${text}</small>
                        </div>
                        <p class="small text-muted mt-2">Share this link with friends to earn rewards!</p>
                    </div>
                `,
                confirmButtonText: 'Awesome!',
                confirmButtonColor: '#28a745',
                timer: 4000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__bounceIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        } else {
            throw new Error('Copy command failed');
        }
    } catch (err) {
        // Manual copy prompt
        Swal.fire({
            icon: 'info',
            title: 'Copy Manually',
            html: `
                <div class="mb-3">
                    <p class="mb-3">Please copy the link manually:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" value="${text}" id="manualCopyInput" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="selectAllText()">
                            <i class="fas fa-mouse-pointer"></i> Select All
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">Press Ctrl+C (or Cmd+C on Mac) to copy</small>
                </div>
            `,
            confirmButtonText: 'Got it!',
            confirmButtonColor: '#007bff',
            showCancelButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                // Auto-select the text when dialog opens
                const input = document.getElementById('manualCopyInput');
                input.focus();
                input.select();
            }
        });
    }
}

function selectAllText() {
    const input = document.getElementById('manualCopyInput');
    input.focus();
    input.select();
}

function trackShare(platform) {
    // Show sharing confirmation
    Swal.fire({
        icon: 'question',
        title: `Share on ${platform.charAt(0).toUpperCase() + platform.slice(1)}?`,
        text: 'You will be redirected to share your referral link',
        showCancelButton: true,
        confirmButtonText: `Yes, share on ${platform.charAt(0).toUpperCase() + platform.slice(1)}!`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        showClass: {
            popup: 'animate__animated animate__zoomIn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Track sharing analytics
            fetch(`{{ route('lottery.track.share') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    platform: platform,
                    action: 'share',
                    referral_link: document.getElementById('referralLink').value
                })
            }).then(response => {
                if (response.ok) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Sharing Tracked!',
                        text: `Your ${platform} share has been recorded. Keep sharing to earn more!`,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            }).catch(console.error);
            
            // Allow the default link action to proceed
            return true;
        } else {
            // Prevent the link from opening
            event.preventDefault();
            return false;
        }
    });
    
    // Prevent immediate navigation
    event.preventDefault();
    return false;
}

function openShareModal() {
    const referralLink = document.getElementById('referralLink').value;
    
    Swal.fire({
        title: 'Share Your Referral Link',
        html: `
            <div class="text-start">
                <div class="mb-4">
                    <label class="form-label fw-bold">Your Referral Link:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="${referralLink}" id="modalReferralLink" readonly>
                        <button class="btn btn-primary" type="button" onclick="copyFromModal()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralLink)}" 
                           target="_blank" class="btn btn-primary w-100 btn-sm">
                            <i class="fab fa-facebook-f me-1"></i> Facebook
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://wa.me/?text=${encodeURIComponent('Join me on {{ config("app.name") }} and start earning! ' + referralLink)}" 
                           target="_blank" class="btn btn-success w-100 btn-sm">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent('Join me on {{ config("app.name") }} and start earning!')}&url=${encodeURIComponent(referralLink)}" 
                           target="_blank" class="btn btn-info w-100 btn-sm">
                            <i class="fab fa-twitter me-1"></i> Twitter
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://t.me/share/url?url=${encodeURIComponent(referralLink)}&text=${encodeURIComponent('Join me on {{ config("app.name") }} and start earning!')}" 
                           target="_blank" class="btn btn-primary w-100 btn-sm">
                            <i class="fab fa-telegram-plane me-1"></i> Telegram
                        </a>
                    </div>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-1"></i> Sharing Tips:</h6>
                    <ul class="small mb-0">
                        <li>Add a personal message when sharing</li>
                        <li>Explain the benefits of joining</li>
                        <li>Share in relevant groups or communities</li>
                        <li>Follow up with interested friends</li>
                    </ul>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#6c757d',
        showClass: {
            popup: 'animate__animated animate__fadeInUp'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown'
        }
    });
}

function copyFromModal() {
    const modalInput = document.getElementById('modalReferralLink');
    const referralLink = modalInput.value;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(referralLink).then(function() {
            Swal.showValidationMessage('✅ Link copied to clipboard!');
            setTimeout(() => {
                Swal.resetValidationMessage();
            }, 2000);
        });
    } else {
        modalInput.select();
        modalInput.setSelectionRange(0, 99999);
        try {
            document.execCommand('copy');
            Swal.showValidationMessage('✅ Link copied to clipboard!');
            setTimeout(() => {
                Swal.resetValidationMessage();
            }, 2000);
        } catch (err) {
            Swal.showValidationMessage('❌ Please copy manually');
        }
    }
}
</script>
@endpush

@endsection

</x-smart_layout>
