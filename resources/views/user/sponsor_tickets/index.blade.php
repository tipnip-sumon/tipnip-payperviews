<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ $pageTitle }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sponsor Tickets</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-primary">{{ $transferStats['total_sponsor_tickets'] }}</h2>
                        <p class="text-muted mb-1">Total Sponsor Tickets</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-success">{{ $transferStats['still_owned'] }}</h2>
                        <p class="text-muted mb-1">Still Owned</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-warning">{{ $transferStats['transferred_out'] }}</h2>
                        <p class="text-muted mb-1">Transferred Out</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card widget-box">
                <div class="card-body">
                    <div class="widget-detail-1 text-center">
                        <h2 class="fw-normal pt-2 mb-1 text-info">{{ $transferStats['received_tickets'] }}</h2>
                        <p class="text-muted mb-1">Received Tickets</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                        <div class="btn-group">
                            <a href="{{ route('user.sponsor-tickets.history') }}" class="btn btn-outline-primary">
                                <i class="fas fa-history"></i> Transfer History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Sponsor Tickets -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Sponsor Tickets</h5>
                    <small class="text-muted">Tickets you received from your referrals' investments</small>
                </div>
                <div class="card-body">
                    @if($sponsorTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>From Referral</th>
                                        <th>Current Owner</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Transfers</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sponsorTickets as $ticket)
                                    <tr>
                                        <td>
                                            <code>{{ $ticket->ticket_number }}</code>
                                        </td>
                                        <td>
                                            @if($ticket->referralUser)
                                                {{ $ticket->referralUser->username }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->currentOwner)
                                                @if($ticket->current_owner_id == auth()->id())
                                                    <span class="badge bg-success">You</span>
                                                @else
                                                    {{ $ticket->currentOwner->username }}
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($ticket->status == 'used_as_token')
                                                <span class="badge bg-info">Used as Token</span>
                                            @elseif($ticket->status == 'winner')
                                                <span class="badge bg-warning">Winner</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $ticket->transfer_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if($ticket->current_owner_id == auth()->id() && $ticket->canBeTransferred())
                                                <a href="{{ route('user.sponsor-tickets.transfer', $ticket->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-share"></i> Transfer
                                                </a>
                                            @elseif($ticket->current_owner_id == auth()->id())
                                                <span class="text-muted small">Cannot Transfer</span>
                                            @else
                                                <span class="text-muted small">Not Owned</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Sponsor Tickets</h5>
                            <p class="text-muted">You haven't received any sponsor tickets yet. Invite friends to invest and earn sponsor tickets!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Received Tickets -->
    @if($receivedTickets->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Received Tickets</h5>
                    <small class="text-muted">Tickets transferred to you by other users</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Original Owner</th>
                                    <th>From Referral</th>
                                    <th>Status</th>
                                    <th>Can Use Token</th>
                                    <th>Received</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receivedTickets as $ticket)
                                <tr>
                                    <td>
                                        <code>{{ $ticket->ticket_number }}</code>
                                    </td>
                                    <td>
                                        @if($ticket->originalOwner)
                                            {{ $ticket->originalOwner->username }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->referralUser)
                                            {{ $ticket->referralUser->username }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($ticket->status == 'used_as_token')
                                            <span class="badge bg-info">Used as Token</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->canBeUsedByUser(auth()->id()))
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->last_transferred_at ? \Carbon\Carbon::parse($ticket->last_transferred_at)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if($ticket->canBeUsedByUser(auth()->id()))
                                            <button class="btn btn-sm btn-success" onclick="useAsToken({{ $ticket->id }})">
                                                <i class="fas fa-percent"></i> Use 5% Discount
                                            </button>
                                        @else
                                            <span class="text-muted small">Cannot Use</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Use Token Modal -->
<div class="modal fade" id="useTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Use Ticket as 5% Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="useTokenForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Plan Amount</label>
                        <input type="number" class="form-control" name="plan_amount" min="1" step="0.01" required>
                        <small class="text-muted">Enter the plan amount to calculate 5% discount</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plan</label>
                        <select class="form-control" name="plan_id" required>
                            <option value="">Select Plan</option>
                            <!-- Plans will be loaded dynamically -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function useAsToken(ticketId) {
    document.getElementById('useTokenForm').action = `/user/sponsor-tickets/${ticketId}/use-token`;
    new bootstrap.Modal(document.getElementById('useTokenModal')).show();
}
</script>
@endsection
</x-smart_layout>
