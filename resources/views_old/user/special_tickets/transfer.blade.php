<x-smart_layout>

@section('title', 'Transfer Special Tickets')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 text-white p-4 mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title mb-0">
                            <i class="fas fa-exchange-alt me-3"></i>Transfer Special Tickets
                        </h1>
                        <p class="page-subtitle mb-0 opacity-75">Send your special tickets to other users instantly</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-1"></i>Dashboard
                            </a>
                            <a href="{{ route('special.tickets.tokens') }}" class="btn btn-outline-light">
                                <i class="fe fe-star me-1"></i>My Tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transfer Form -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-primary-light rounded me-3">
                            <i class="fe fe-send text-primary"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Send Special Ticket</h4>
                            <p class="text-muted small mb-0">Choose a ticket and recipient to start transfer</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($availableTickets && $availableTickets->count() > 0)
                        <form action="{{ route('special.tickets.send.transfer') }}" method="POST" id="transferForm">
                            @csrf
                            
                            <!-- Available Tickets Display -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fe fe-star text-warning me-1"></i>
                                    Select Ticket to Transfer
                                    <span class="text-muted">({{ $availableTickets->count() }} available)</span>
                                </label>
                                <div class="row g-3">
                                    @foreach($availableTickets as $ticket)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="ticket-card">
                                                <input type="radio" name="ticket_id" value="{{ $ticket->id }}" id="ticket_{{ $ticket->id }}" class="ticket-radio">
                                                <label for="ticket_{{ $ticket->id }}" class="ticket-label">
                                                    <div class="ticket-header">
                                                        <div class="ticket-type">
                                                            @switch($ticket->type ?? 'discount')
                                                                @case('discount')
                                                                    <i class="fas fa-percentage text-success"></i>
                                                                    <span>Discount</span>
                                                                    @break
                                                                @case('bonus')
                                                                    <i class="fas fa-gift text-warning"></i>
                                                                    <span>Bonus</span>
                                                                    @break
                                                                @case('premium')
                                                                    <i class="fas fa-crown text-info"></i>
                                                                    <span>Premium</span>
                                                                    @break
                                                                @case('vip')
                                                                    <i class="fas fa-gem text-purple"></i>
                                                                    <span>VIP</span>
                                                                    @break
                                                                @case('loyalty')
                                                                    <i class="fas fa-medal text-bronze"></i>
                                                                    <span>Loyalty</span>
                                                                    @break
                                                                @case('cashback')
                                                                    <i class="fas fa-coins text-primary"></i>
                                                                    <span>Cashback</span>
                                                                    @break
                                                                @case('referral')
                                                                    <i class="fas fa-users text-info"></i>
                                                                    <span>Referral</span>
                                                                    @break
                                                                @case('special')
                                                                    <i class="fas fa-star text-danger"></i>
                                                                    <span>Special</span>
                                                                    @break
                                                                @case('event')
                                                                    <i class="fas fa-calendar text-warning"></i>
                                                                    <span>Event</span>
                                                                    @break
                                                                @default
                                                                    <i class="fas fa-ticket-alt text-primary"></i>
                                                                    <span>General</span>
                                                            @endswitch
                                                        </div>
                                                        <div class="ticket-status">
                                                            <span class="badge bg-success-soft text-success">Available</span>
                                                        </div>
                                                    </div>
                                                    <div class="ticket-content">
                                                        <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
                                                        <div class="ticket-value">
                                                            ${{ number_format($ticket->getDiscountPotential(100) ?? $ticket->value ?? 0, 2) }}
                                                            <small class="text-muted">value</small>
                                                        </div>
                                                        <div class="ticket-meta">
                                                            <small class="text-muted">
                                                                <i class="fe fe-calendar me-1"></i>
                                                                {{ $ticket->created_at->format('M d, Y') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Transfer Details -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fe fe-user text-primary me-1"></i>
                                        Recipient Username
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" name="recipient_username" class="form-control" 
                                               placeholder="Enter username" required>
                                    </div>
                                    <small class="text-muted">Enter the exact username of the recipient</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fe fe-tag text-info me-1"></i>
                                        Transfer Type
                                    </label>
                                    <select name="transfer_type" class="form-select" id="transferType" required>
                                        <option value="gift">🎁 Gift (Free Transfer)</option>
                                        <option value="sale">💰 Sale (Paid Transfer)</option>
                                        <option value="share">🤝 Share (Temporary Access)</option>
                                        <option value="trade">🔄 Trade (Exchange)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Conditional Fields -->
                            <div class="row g-3 mt-2" id="amountField" style="display: none;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fe fe-dollar-sign text-success me-1"></i>
                                        Sale Amount
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="amount" class="form-control" 
                                               min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="wallet">Wallet Balance</option>
                                        <option value="escrow">Escrow Service</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="form-label fw-semibold">
                                    <i class="fe fe-message-circle text-muted me-1"></i>
                                    Message (Optional)
                                </label>
                                <textarea name="message" class="form-control" rows="3" 
                                          placeholder="Add a personal message to the recipient..."></textarea>
                            </div>
                            
                            <!-- Transfer Options -->
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="require_confirmation" id="requireConfirmation">
                                    <label class="form-check-label" for="requireConfirmation">
                                        Require recipient confirmation before transfer
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="notify_email" id="notifyEmail" checked>
                                    <label class="form-check-label" for="notifyEmail">
                                        Send email notification to recipient
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="fe fe-info text-primary me-1"></i>
                                            No fees for ticket transfers
                                        </small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-light me-2" onclick="resetForm()">
                                            <i class="fe fe-refresh-ccw me-1"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fe fe-send me-1"></i>Send Transfer Request
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle mx-auto mb-3">
                                <i class="fe fe-star text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="text-muted">No Transferable Tickets</h4>
                            <p class="text-muted mb-4">You don't have any special tickets available for transfer at the moment.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('special.tickets.index') }}" class="btn btn-primary">
                                    <i class="fe fe-arrow-left me-1"></i>Back to Dashboard
                                </a>
                                <a href="{{ route('lottery.index') }}" class="btn btn-outline-primary">
                                    <i class="fe fe-plus me-1"></i>Buy Lottery Tickets
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Transfer Statistics -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title">
                        <i class="fe fe-bar-chart text-info me-2"></i>Transfer Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-success mb-1">{{ $transferStats['total_sent'] ?? 0 }}</h3>
                                <p class="text-muted small mb-0">
                                    <i class="fe fe-arrow-up-right me-1"></i>Sent
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="text-info mb-1">{{ $transferStats['total_received'] ?? 0 }}</h3>
                                <p class="text-muted small mb-0">
                                    <i class="fe fe-arrow-down-left me-1"></i>Received
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-warning mb-1">{{ $transferStats['pending_outgoing'] ?? 0 }}</h4>
                                <p class="text-muted small mb-0">
                                    <i class="fe fe-clock me-1"></i>Pending Out
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-primary mb-1">{{ $transferStats['pending_incoming'] ?? 0 }}</h4>
                                <p class="text-muted small mb-0">
                                    <i class="fe fe-inbox me-1"></i>Pending In
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title">
                        <i class="fe fe-zap text-warning me-2"></i>Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('special.tickets.incoming') }}" class="btn btn-outline-warning d-flex align-items-center">
                            <i class="fe fe-inbox me-2"></i>
                            <span>Incoming Transfers</span>
                            @if(($transferStats['pending_incoming'] ?? 0) > 0)
                                <span class="badge bg-warning text-dark ms-auto">{{ $transferStats['pending_incoming'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('special.tickets.outgoing') }}" class="btn btn-outline-info d-flex align-items-center">
                            <i class="fe fe-arrow-up-right me-2"></i>
                            <span>Outgoing Transfers</span>
                            @if(($transferStats['pending_outgoing'] ?? 0) > 0)
                                <span class="badge bg-info ms-auto">{{ $transferStats['pending_outgoing'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('special.tickets.tokens') }}" class="btn btn-outline-primary">
                            <i class="fe fe-star me-2"></i>All My Tickets
                        </a>
                        <a href="{{ route('special.tickets.history') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-clock me-2"></i>Transfer History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transfer Tips -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title">
                        <i class="fe fe-help-circle text-info me-2"></i>Transfer Tips
                    </h4>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <i class="fe fe-check-circle text-success me-2 mt-1"></i>
                                <small class="text-muted">Double-check recipient username before sending</small>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <i class="fe fe-shield text-primary me-2 mt-1"></i>
                                <small class="text-muted">All transfers are secured and tracked</small>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <i class="fe fe-clock text-warning me-2 mt-1"></i>
                                <small class="text-muted">Transfers are processed instantly</small>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex">
                                <i class="fe fe-gift text-success me-2 mt-1"></i>
                                <small class="text-muted">No fees for gifting tickets to friends</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
}

.avatar {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-lg {
    width: 4rem;
    height: 4rem;
}

.bg-primary-light {
    background-color: rgba(99, 102, 241, 0.1);
}

.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.1);
}

.ticket-card {
    position: relative;
    margin-bottom: 1rem;
}

.ticket-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.ticket-label {
    display: block;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    margin-bottom: 0;
}

.ticket-label:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
}

.ticket-radio:checked + .ticket-label {
    border-color: #6366f1;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.ticket-type {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.ticket-number {
    font-size: 1.1rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.25rem;
}

.ticket-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #059669;
    margin-bottom: 0.5rem;
}

.ticket-meta {
    margin-top: 0.5rem;
}

.stat-item {
    padding: 0.5rem;
}

.text-purple {
    color: #8b5cf6 !important;
}

.text-bronze {
    color: #cd7f32 !important;
}

.form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5855eb 0%, #7c3aed 100%);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .ticket-label {
        padding: 0.75rem;
    }
    
    .ticket-value {
        font-size: 1.1rem;
    }
    
    .ticket-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferType = document.getElementById('transferType');
    const amountField = document.getElementById('amountField');
    const amountInput = amountField?.querySelector('input');
    
    transferType?.addEventListener('change', function() {
        if (this.value === 'sale') {
            amountField.style.display = 'flex';
            if (amountInput) amountInput.required = true;
        } else {
            amountField.style.display = 'none';
            if (amountInput) amountInput.required = false;
        }
    });
});

function resetForm() {
    document.getElementById('transferForm').reset();
    document.getElementById('amountField').style.display = 'none';
    
    // Uncheck all radio buttons
    const radios = document.querySelectorAll('.ticket-radio');
    radios.forEach(radio => radio.checked = false);
}

// Form validation
document.getElementById('transferForm')?.addEventListener('submit', function(e) {
    const checkedTicket = document.querySelector('.ticket-radio:checked');
    if (!checkedTicket) {
        e.preventDefault();
        alert('Please select a ticket to transfer.');
        return false;
    }
    
    const transferType = document.getElementById('transferType').value;
    const amountInput = document.querySelector('input[name="amount"]');
    
    if (transferType === 'sale' && (!amountInput.value || parseFloat(amountInput.value) <= 0)) {
        e.preventDefault();
        alert('Please enter a valid sale amount.');
        return false;
    }
});
</script>
@endpush

@endsection
</x-smart_layout>
