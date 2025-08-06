<!-- Unified Reward Tokens Tab -->
<div class="row">
    <!-- Token Summary Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body text-center">
                        <i class="fe fe-star fs-2 mb-2"></i>
                        <h4 class="mb-1">{{ $specialTokensCount }}</h4>
                        <small>Special Tokens</small>
                        <div class="mt-2">
                            <small class="opacity-75">Referral Rewards</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body text-center">
                        <i class="fe fe-users fs-2 mb-2"></i>
                        <h4 class="mb-1">{{ $sponsorTicketsCount }}</h4>
                        <small>Sponsor Tickets</small>
                        <div class="mt-2">
                            <small class="opacity-75">Sponsor Rewards</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-success text-white">
                    <div class="card-body text-center">
                        <i class="fe fe-check-circle fs-2 mb-2"></i>
                        <h4 class="mb-1">{{ $availableTokensCount ?? ($specialTokensCount + $sponsorTicketsCount) }}</h4>
                        <small>Available to Use</small>
                        <div class="mt-2">
                            <small class="opacity-75">Valid & Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Management Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">💎 Your Reward Tokens</h5>
                    <small class="text-muted">Manage your special tokens and sponsor tickets</small>
                </div>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="tokenFilter" id="filterAll" value="all" checked>
                    <label class="btn btn-outline-primary btn-sm" for="filterAll">All Tokens</label>
                    
                    <input type="radio" class="btn-check" name="tokenFilter" id="filterSpecial" value="special">
                    <label class="btn btn-outline-warning btn-sm" for="filterSpecial">Special Tokens</label>
                    
                    <input type="radio" class="btn-check" name="tokenFilter" id="filterSponsor" value="sponsor">
                    <label class="btn btn-outline-info btn-sm" for="filterSponsor">Sponsor Tickets</label>
                </div>
            </div>
            <div class="card-body">
                @if($allTokens->count() > 0)
                    <div class="row g-3" id="tokensContainer">
                        @foreach($allTokens as $token)
                            <div class="col-md-6 col-lg-4 token-item" data-type="{{ $token->token_type }}">
                                <div class="card border-0 shadow-sm h-100 token-card {{ $token->token_type === 'special' ? 'border-warning' : 'border-info' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <span class="badge bg-{{ $token->token_type === 'special' ? 'warning' : 'info' }} mb-2">
                                                    {{ $token->token_type === 'special' ? '⭐ Special Token' : '👥 Sponsor Ticket' }}
                                                </span>
                                                <h6 class="card-title mb-1">{{ $token->ticket_number }}</h6>
                                                <small class="text-muted">
                                                    Value: ${{ number_format($token->ticket_price, 2) }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                @if($token->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($token->status) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Token Details -->
                                        <div class="token-details mb-3">
                                            @if($token->token_type === 'special')
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Sponsor</small>
                                                        <strong>User #{{ $token->sponsor_user_id }}</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Referral</small>
                                                        <strong>User #{{ $token->referral_user_id }}</strong>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($token->token_expires_at)
                                                <div class="mt-2 text-center">
                                                    <small class="text-muted">Expires:</small>
                                                    <div class="text-{{ $token->token_expires_at->isPast() ? 'danger' : 'success' }}">
                                                        {{ $token->token_expires_at->format('M d, Y') }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Token Actions -->
                                        <div class="token-actions">
                                            @if($token->status === 'active' && $token->is_valid_token && !$token->used_as_token_at)
                                                <div class="row g-2">
                                                    @if($token->is_transferable)
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                                                    onclick="transferToken('{{ $token->id }}')">
                                                                <i class="fe fe-send me-1"></i>Transfer
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <div class="col-{{ $token->is_transferable ? '6' : '12' }}">
                                                        <button type="button" class="btn btn-success btn-sm w-100" 
                                                                onclick="useToken('{{ $token->id }}')">
                                                            <i class="fe fe-shopping-cart me-1"></i>Use Token
                                                        </button>
                                                    </div>
                                                </div>

                                                @if($token->transfer_count > 0)
                                                    <div class="mt-2 text-center">
                                                        <small class="text-muted">
                                                            <i class="fe fe-repeat me-1"></i>
                                                            Transferred {{ $token->transfer_count }} time(s)
                                                        </small>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center">
                                                    @if($token->used_as_token_at)
                                                        <span class="badge bg-secondary">
                                                            <i class="fe fe-check me-1"></i>Used on {{ $token->used_as_token_at->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">Not Available</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Token Usage Instructions -->
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fe fe-info me-2"></i>How to Use Your Tokens
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Special Tokens:</strong> Earned through referrals, can be transferred to other users or used for plan purchases</li>
                                <li><strong>Sponsor Tickets:</strong> Rewards from sponsors, can be used for premium plan upgrades</li>
                                <li><strong>Transfer:</strong> Send tokens to other users (if transferable)</li>
                                <li><strong>Use Token:</strong> Apply token value towards plan purchases</li>
                            </ul>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="fe fe-star fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No Reward Tokens Yet</h5>
                        <p class="text-muted mb-4">
                            Start earning tokens by referring friends or participating in sponsor programs!
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-warning" onclick="showTab('sharing')">
                                            <i class="fe fe-share-2 me-2"></i>Start Referring
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn btn-outline-info" onclick="showTab('lottery')">
                                            <i class="fe fe-play-circle me-2"></i>Join Lottery
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Token Transfer Modal -->
<div class="modal fade" id="transferTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-send me-2"></i>Transfer Token
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transferTokenForm">
                <div class="modal-body">
                    <input type="hidden" id="transferTokenId" name="token_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Recipient Username</label>
                        <input type="text" class="form-control" id="recipientUsername" name="recipient_username" required>
                        <small class="form-text text-muted">Enter the username of the person you want to transfer to</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="transferMessage" name="message" rows="2" placeholder="Add a message for the recipient..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fe fe-send me-2"></i>Transfer Token
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Token Usage Modal -->
<div class="modal fade" id="useTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-shopping-cart me-2"></i>Use Token
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="useTokenForm">
                <div class="modal-body">
                    <input type="hidden" id="useTokenId" name="token_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Select Plan</label>
                        <select class="form-select" id="planSelect" name="plan_id" required>
                            <option value="">Choose a plan...</option>
                            <!-- Plans will be loaded dynamically -->
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fe fe-info me-1"></i>
                            Token value will be applied as discount to the selected plan
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-check me-2"></i>Use Token
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter tokens by type
document.querySelectorAll('input[name="tokenFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const filterValue = this.value;
        const tokenItems = document.querySelectorAll('.token-item');
        
        tokenItems.forEach(item => {
            if (filterValue === 'all' || item.dataset.type === filterValue) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Transfer token function
function transferToken(tokenId) {
    document.getElementById('transferTokenId').value = tokenId;
    new bootstrap.Modal(document.getElementById('transferTokenModal')).show();
}

// Use token function
function useToken(tokenId) {
    document.getElementById('useTokenId').value = tokenId;
    
    // Load available plans
    fetch('{{ route("lottery.unified.available.plans") }}')
        .then(response => response.json())
        .then(data => {
            const planSelect = document.getElementById('planSelect');
            planSelect.innerHTML = '<option value="">Choose a plan...</option>';
            
            data.plans.forEach(plan => {
                planSelect.innerHTML += `<option value="${plan.id}">${plan.name} - $${plan.price}</option>`;
            });
        });
    
    new bootstrap.Modal(document.getElementById('useTokenModal')).show();
}

// Handle transfer form submission
document.getElementById('transferTokenForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("lottery.unified.transfer.token") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Transfer failed');
        }
    });
});

// Handle use token form submission
document.getElementById('useTokenForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("lottery.unified.use.token") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Token usage failed');
        }
    });
});

// Show specific tab function
function showTab(tabName) {
    const tab = document.querySelector(`#${tabName}-tab`);
    if (tab) {
        new bootstrap.Tab(tab).show();
    }
}
</script>
