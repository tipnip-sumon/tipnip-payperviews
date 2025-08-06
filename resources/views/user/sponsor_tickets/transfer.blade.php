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
                        <li class="breadcrumb-item"><a href="{{ route('user.sponsor-tickets.index') }}">Sponsor Tickets</a></li>
                        <li class="breadcrumb-item active">Transfer</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transfer Sponsor Ticket</h5>
                </div>
                <div class="card-body">
                    <!-- Ticket Information -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Ticket Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Ticket Number:</strong> <code>{{ $ticket->ticket_number }}</code><br>
                                <strong>Status:</strong> 
                                <span class="badge bg-success">{{ ucfirst($ticket->status) }}</span><br>
                                <strong>Transfer Count:</strong> {{ $ticket->transfer_count ?? 0 }}
                            </div>
                            <div class="col-md-6">
                                <strong>Token Value:</strong> 5% discount + early bonus<br>
                                <strong>Expires:</strong> {{ $ticket->token_expires_at ? \Carbon\Carbon::parse($ticket->token_expires_at)->format('M d, Y H:i') : 'Never' }}<br>
                                <strong>Transferable:</strong> 
                                <span class="badge bg-{{ $ticket->is_transferable ? 'success' : 'danger' }}">
                                    {{ $ticket->is_transferable ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Form -->
                    <form method="POST" action="{{ route('user.sponsor-tickets.transfer.submit', $ticket->id) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="recipient_username" class="form-label">Recipient Username</label>
                            <input type="text" class="form-control @error('recipient_username') is-invalid @enderror" 
                                   id="recipient_username" name="recipient_username" 
                                   value="{{ old('recipient_username') }}" 
                                   placeholder="Enter the username of the person to transfer to" required>
                            @error('recipient_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Make sure the username is correct. This transfer cannot be undone.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="transfer_message" class="form-label">Transfer Message (Optional)</label>
                            <textarea class="form-control @error('transfer_message') is-invalid @enderror" 
                                      id="transfer_message" name="transfer_message" rows="3" 
                                      placeholder="Add a personal message for the recipient">{{ old('transfer_message') }}</textarea>
                            @error('transfer_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transfer Warnings -->
                        <div class="alert alert-warning">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Important Notes</h6>
                            <ul class="mb-0">
                                <li>Once transferred, you will no longer own this ticket</li>
                                <li>The recipient will be able to use this ticket for a 5% discount</li>
                                <li>You cannot use this ticket yourself after transferring it</li>
                                <li>This action cannot be undone</li>
                                <li>Make sure the recipient username is correct</li>
                            </ul>
                        </div>

                        <!-- Transfer Benefits Info -->
                        <div class="alert alert-success">
                            <h6 class="alert-heading"><i class="fas fa-gift"></i> What the Recipient Gets</h6>
                            <ul class="mb-0">
                                <li><strong>5% Base Discount:</strong> On any plan purchase</li>
                                <li><strong>Early Usage Bonus:</strong> Up to 5% additional discount if used quickly</li>
                                <li><strong>Transfer Rights:</strong> They can also transfer it to someone else</li>
                                <li><strong>Valid Until:</strong> {{ $ticket->token_expires_at ? \Carbon\Carbon::parse($ticket->token_expires_at)->format('M d, Y H:i') : 'Never expires' }}</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tickets
                            </a>
                            <button type="submit" class="btn btn-primary" id="transferBtn">
                                <i class="fas fa-share"></i> Transfer Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('transferBtn').addEventListener('click', function(e) {
    if (!confirm('Are you sure you want to transfer this ticket? This action cannot be undone.')) {
        e.preventDefault();
    }
});

// Auto-suggest usernames
document.getElementById('recipient_username').addEventListener('input', function() {
    // You can implement username auto-complete here if needed
});
</script>
@endsection
</x-smart_layout>
