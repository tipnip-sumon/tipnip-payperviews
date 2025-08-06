<!-- Share & Earn System -->
<div class="row g-4">
    
    <!-- Referral Link Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><i class="fe fe-link me-2"></i>Your Referral Link</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="referralLink" 
                                   value="{{ $sharingStats['referral_link'] }}" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                <i class="fe fe-copy me-2"></i>Copy Link
                            </button>
                        </div>
                        <small class="text-muted mt-1">Share this link with friends to earn rewards when they join and invest!</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex flex-column">
                            <h4 class="text-success mb-0">{{ $sharingStats['total_referrals'] }}</h4>
                            <small class="text-muted">Total Referrals</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Sharing Platforms -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-share-2 me-2"></i>Share on Social Platforms</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $platforms = [
                            [
                                'name' => 'Facebook',
                                'icon' => 'fab fa-facebook-f',
                                'color' => 'primary',
                                'platform' => 'facebook',
                                'description' => 'Share with your Facebook friends'
                            ],
                            [
                                'name' => 'Twitter',
                                'icon' => 'fab fa-twitter',
                                'color' => 'info',
                                'platform' => 'twitter',
                                'description' => 'Tweet to your followers'
                            ],
                            [
                                'name' => 'LinkedIn',
                                'icon' => 'fab fa-linkedin-in',
                                'color' => 'primary',
                                'platform' => 'linkedin',
                                'description' => 'Share with professionals'
                            ],
                            [
                                'name' => 'WhatsApp',
                                'icon' => 'fab fa-whatsapp',
                                'color' => 'success',
                                'platform' => 'whatsapp',
                                'description' => 'Send to WhatsApp contacts'
                            ],
                            [
                                'name' => 'Telegram',
                                'icon' => 'fab fa-telegram-plane',
                                'color' => 'info',
                                'platform' => 'telegram',
                                'description' => 'Share in Telegram groups'
                            ],
                            [
                                'name' => 'Email',
                                'icon' => 'fe fe-mail',
                                'color' => 'warning',
                                'platform' => 'email',
                                'description' => 'Send via email'
                            ]
                        ];
                        
                        $shareText = "🎰 Join me on this amazing lottery platform! Win prizes, earn rewards, and get special tokens. Use my link to get started:";
                    @endphp
                    
                    @foreach($platforms as $platform)
                        <div class="col-md-4 col-6">
                            <div class="share-platform card h-100 border-0 shadow-sm" 
                                 onclick="shareOnPlatform('{{ $platform['platform'] }}', '{{ $sharingStats['referral_link'] }}', '{{ $shareText }}')">
                                <div class="card-body text-center p-3">
                                    <i class="{{ $platform['icon'] }} fs-1 text-{{ $platform['color'] }} mb-3"></i>
                                    <h6 class="mb-2">{{ $platform['name'] }}</h6>
                                    <small class="text-muted">{{ $platform['description'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Sharing Rewards -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0"><i class="fe fe-gift me-2"></i>Sharing Rewards</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="text-success mb-1">${{ number_format($sharingStats['potential_earnings'], 2) }}</h3>
                    <p class="text-muted mb-0">Potential Earnings from Referrals</p>
                </div>
                
                <div class="reward-list">
                    <div class="d-flex align-items-center mb-3">
                        <div class="reward-icon me-3">
                            <i class="fe fe-user-plus text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Friend Joins</h6>
                            <small class="text-muted">Earn $2 when someone signs up with your link</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-primary">$2.00</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="reward-icon me-3">
                            <i class="fe fe-dollar-sign text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">First Investment</h6>
                            <small class="text-muted">Earn $5 when they make their first investment</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-success">$5.00</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="reward-icon me-3">
                            <i class="fe fe-star text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Special Token</h6>
                            <small class="text-muted">Get bonus token for qualified referrals</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-warning">Token</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="reward-icon me-3">
                            <i class="fe fe-percent text-info"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Ongoing Commission</h6>
                            <small class="text-muted">Earn 2% from their future investments</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-info">2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Statistics -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-bar-chart-2 me-2"></i>Your Network Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-6 border-end">
                        <h3 class="text-primary mb-1">{{ $sharingStats['total_referrals'] }}</h3>
                        <small class="text-muted">Total Referred</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success mb-1">{{ $sharingStats['active_referrals'] }}</h3>
                        <small class="text-muted">Active Members</small>
                    </div>
                </div>
                
                <!-- Referral Levels -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Level 1 (Direct)</span>
                        <span class="fw-bold">{{ $sharingStats['total_referrals'] }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min(($sharingStats['total_referrals'] / 20) * 100, 100) }}%"></div>
                    </div>
                </div>
                
                <!-- Performance Metrics -->
                <div class="mt-4">
                    <h6 class="mb-3">Performance Metrics</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Conversion Rate</span>
                        <span class="fw-bold text-success">
                            {{ $sharingStats['total_referrals'] > 0 ? number_format(($sharingStats['active_referrals'] / $sharingStats['total_referrals']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Average Earnings per Referral</span>
                        <span class="fw-bold text-primary">
                            ${{ $sharingStats['total_referrals'] > 0 ? number_format($sharingStats['potential_earnings'] / $sharingStats['total_referrals'], 2) : '0.00' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Network Growth Rate</span>
                        <span class="fw-bold text-info">Growing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sharing Tips -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fe fe-lightbulb me-2"></i>Sharing Tips & Best Practices</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="tip-card text-center p-3">
                            <i class="fe fe-target fs-1 text-primary mb-3"></i>
                            <h6 class="mb-2">Target the Right Audience</h6>
                            <p class="text-muted small">Share with people interested in investments and earning opportunities.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="tip-card text-center p-3">
                            <i class="fe fe-message-circle fs-1 text-success mb-3"></i>
                            <h6 class="mb-2">Personal Touch</h6>
                            <p class="text-muted small">Add a personal message explaining why you recommend the platform.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="tip-card text-center p-3">
                            <i class="fe fe-clock fs-1 text-warning mb-3"></i>
                            <h6 class="mb-2">Timing Matters</h6>
                            <p class="text-muted small">Share during peak hours when your audience is most active.</p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">✅ Do's</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Explain the benefits clearly</li>
                            <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Share your personal experience</li>
                            <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Follow up with interested friends</li>
                            <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Use multiple platforms</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">❌ Don'ts</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fe fe-x text-danger me-2"></i>Don't spam or over-share</li>
                            <li class="mb-2"><i class="fe fe-x text-danger me-2"></i>Don't make unrealistic promises</li>
                            <li class="mb-2"><i class="fe fe-x text-danger me-2"></i>Don't share without context</li>
                            <li class="mb-2"><i class="fe fe-x text-danger me-2"></i>Don't ignore platform rules</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
