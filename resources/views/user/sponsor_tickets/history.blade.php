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
                        <li class="breadcrumb-item active">Transfer History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Outgoing Transfers -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sent Transfers</h5>
                    <small class="text-muted">Tickets you have transferred to others</small>
                </div>
                <div class="card-body">
                    @if($outgoingTransfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Transfer Code</th>
                                        <th>Ticket #</th>
                                        <th>Sent To</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outgoingTransfers as $transfer)
                                    <tr>
                                        <td><code>{{ $transfer->transfer_code }}</code></td>
                                        <td>
                                            @if($transfer->specialTicket)
                                                <code>{{ $transfer->specialTicket->ticket_number }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transfer->toUser)
                                                {{ $transfer->toUser->username }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transfer->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($transfer->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($transfer->status == 'cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @elseif($transfer->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-light">{{ ucfirst($transfer->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($transfer->transfer_message)
                                                <small class="text-muted">{{ Str::limit($transfer->transfer_message, 30) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Sent Transfers</h5>
                            <p class="text-muted">You haven't sent any ticket transfers yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Incoming Transfers -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Received Transfers</h5>
                    <small class="text-muted">Tickets transferred to you by others</small>
                </div>
                <div class="card-body">
                    @if($incomingTransfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Transfer Code</th>
                                        <th>Ticket #</th>
                                        <th>From</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomingTransfers as $transfer)
                                    <tr>
                                        <td><code>{{ $transfer->transfer_code }}</code></td>
                                        <td>
                                            @if($transfer->specialTicket)
                                                <code>{{ $transfer->specialTicket->ticket_number }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transfer->fromUser)
                                                {{ $transfer->fromUser->username }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transfer->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($transfer->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($transfer->status == 'cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @elseif($transfer->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-light">{{ ucfirst($transfer->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($transfer->transfer_message)
                                                <small class="text-muted">{{ Str::limit($transfer->transfer_message, 30) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Received Transfers</h5>
                            <p class="text-muted">You haven't received any ticket transfers yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="text-center">
                <a href="{{ route('user.sponsor-tickets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Sponsor Tickets
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
</x-smart_layout>
