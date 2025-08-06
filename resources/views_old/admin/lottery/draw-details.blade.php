<x-layout>
    @section('top_title', 'Draw Details')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Draw Details - #' . $draw->id)
            
            <!-- Quick Actions -->
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-secondary">
                            <i class="fe fe-arrow-left"></i> Back to Draws
                        </a>
                        @if($draw->status === 'pending')
                            <button type="button" class="btn btn-warning" onclick="performDraw()">
                                <i class="fe fe-shuffle"></i> Perform Draw Now
                            </button>
                            @if($draw->lotterySetting && $draw->lotterySetting->manual_winner_selection)
                                <a href="{{ route('admin.lottery.draws.manual-winners', $draw->id) }}" class="btn btn-info">
                                    <i class="fe fe-users"></i> Manual Winner Selection
                                </a>
                            @endif
                        @endif
                        @if($draw->status === 'completed' && !$draw->prizes_distributed)
                            <button type="button" class="btn btn-success" onclick="distributePrizes()">
                                <i class="fe fe-dollar-sign"></i> Distribute Prizes
                            </button>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fe fe-more-vertical"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            @if($draw->status === 'pending')
                                <li><a class="dropdown-item" href="{{ route('admin.lottery.draws.edit', $draw->id) }}">
                                    <i class="fe fe-edit-2"></i> Edit Draw
                                </a></li>
                            @endif
                            <li><a class="dropdown-item" href="#" onclick="exportTickets()">
                                <i class="fe fe-download"></i> Export Tickets
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="printDraw()">
                                <i class="fe fe-printer"></i> Print Details
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            @if($draw->status === 'pending')
                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelDraw()">
                                    <i class="fe fe-x-circle"></i> Cancel Draw
                                </a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Status Summary Cards -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <div class="h2 text-primary mb-2">
                                    {{ $draw->status === 'pending' ? 'Upcoming' : ucfirst($draw->status) }}
                                </div>
                                <p class="text-muted mb-0">Draw Status</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="h2 text-info mb-2">{{ $ticketsSold }}</div>
                                <p class="text-muted mb-0">Tickets Sold</p>
                                <small class="text-muted">of {{ number_format($draw->max_tickets) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="h2 text-success mb-2">${{ number_format($totalRevenue, 2) }}</div>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="h2 text-warning mb-2">${{ number_format($prizePool, 2) }}</div>
                                <p class="text-muted mb-0">Prize Pool</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draw Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-info me-2"></i>Draw Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <strong>Draw ID:</strong><br>
                                <span class="text-muted">#{{ $draw->id }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Draw Date:</strong><br>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($draw->draw_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Draw Time:</strong><br>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($draw->draw_time)->format('h:i A') }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Ticket Price:</strong><br>
                                <span class="text-muted">${{ number_format($draw->ticket_price, 2) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Max Tickets:</strong><br>
                                <span class="text-muted">{{ number_format($draw->max_tickets) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Admin Commission:</strong><br>
                                <span class="text-muted">{{ $draw->admin_commission }}%</span>
                            </div>
                            <div class="col-6">
                                <strong>Auto Draw:</strong><br>
                                <span class="badge {{ $draw->auto_draw ? 'bg-success' : 'bg-danger' }}">
                                    {{ $draw->auto_draw ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <strong>Auto Prize Distribution:</strong><br>
                                <span class="badge {{ $draw->auto_prize_distribution ? 'bg-success' : 'bg-danger' }}">
                                    {{ $draw->auto_prize_distribution ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prize Structure -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-award me-2"></i>Prize Structure</h5>
                    </div>
                    <div class="card-body">
                        @if($draw->prize_distribution)
                            @php
                                $prizeDistribution = is_array($draw->prize_distribution) 
                                    ? $draw->prize_distribution 
                                    : json_decode($draw->prize_distribution, true);
                            @endphp
                            @if($prizeDistribution && is_array($prizeDistribution))
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Position</th>
                                                <th>Percentage</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($prizeDistribution as $position => $prizeInfo)
                                                <tr>
                                                    <td>
                                                        @if(isset($prizeInfo['position']))
                                                            @if($prizeInfo['position'] == 1)
                                                                <span class="badge bg-warning">1st Place</span>
                                                            @elseif($prizeInfo['position'] == 2)
                                                                <span class="badge bg-secondary">2nd Place</span>
                                                            @elseif($prizeInfo['position'] == 3)
                                                                <span class="badge bg-info">3rd Place</span>
                                                            @else
                                                                <span class="badge bg-light text-dark">{{ $prizeInfo['position'] }}th Place</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-light text-dark">{{ $position + 1 }}th Place</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $prizeInfo['percentage'] ?? 'N/A' }}%</td>
                                                    <td>${{ number_format(($prizeInfo['amount'] ?? ($prizePool * ($prizeInfo['percentage'] ?? 0) / 100)), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Prize distribution data is invalid</p>
                            @endif
                        @else
                            <p class="text-muted">No prize structure defined</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Winners (if draw completed) -->
            @if($draw->status === 'completed' && $winners && count($winners) > 0)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fe fe-users me-2"></i>Winners</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Ticket Number</th>
                                            <th>Winner</th>
                                            <th>Prize Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($winners as $index => $winner)
                                            <tr>
                                                <td>
                                                    @if($index === 0)
                                                        <span class="badge bg-warning">1st</span>
                                                    @elseif($index === 1)
                                                        <span class="badge bg-secondary">2nd</span>
                                                    @elseif($index === 2)
                                                        <span class="badge bg-info">3rd</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ $index + 1 }}th</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code>{{ $winner->ticket_number }}</code>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $winner->user->name ?? 'Unknown' }}</strong><br>
                                                        <small class="text-muted">{{ $winner->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="text-success">${{ number_format($winner->prize_amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $winner->prize_distributed ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $winner->prize_distributed ? 'Distributed' : 'Pending' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Tickets -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-ticket me-2"></i>Recent Tickets</h5>
                        <a href="{{ route('admin.lottery.tickets', ['draw_id' => $draw->id]) }}" class="btn btn-sm btn-outline-primary">
                            View All Tickets
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentTickets && count($recentTickets) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ticket Number</th>
                                            <th>Buyer</th>
                                            <th>Purchase Date</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTickets as $ticket)
                                            <tr>
                                                <td>
                                                    <code>{{ $ticket->ticket_number }}</code>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $ticket->user->name ?? 'Unknown' }}</strong><br>
                                                        <small class="text-muted">{{ $ticket->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $ticket->created_at ? $ticket->created_at->format('M d, Y h:i A') : 'Unknown' }}
                                                </td>
                                                <td>
                                                    ${{ number_format($ticket->ticket_price, 2) }}
                                                </td>
                                                <td>
                                                    @if($draw->status === 'completed')
                                                        @php
                                                            $isWinner = $winners->where('lottery_ticket_id', $ticket->id)->first();
                                                        @endphp
                                                        @if($isWinner)
                                                            <span class="badge bg-success">Winner</span>
                                                        @else
                                                            <span class="badge bg-secondary">Lost</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-info">Active</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-ticket fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No tickets sold yet</h5>
                                <p class="text-muted">Tickets will appear here once users start purchasing them.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    function performDraw() {
        if (confirm('Are you sure you want to perform this draw now? This action cannot be undone.')) {
            // Show loading
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fe fe-loader"></i> Processing...';
            button.disabled = true;
            
            // Make AJAX request
            fetch('{{ route("admin.lottery.draws.perform", $draw->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Draw performed successfully!');
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to perform draw');
                    // Restore button
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while performing the draw');
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    }

    function distributePrizes() {
        if (confirm('Are you sure you want to distribute prizes to winners? This action cannot be undone.')) {
            // Show loading
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fe fe-loader"></i> Distributing...';
            button.disabled = true;
            
            // Make AJAX request
            fetch('{{ route("admin.lottery.draws.distribute", $draw->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Prizes distributed successfully!');
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to distribute prizes');
                    // Restore button
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while distributing prizes');
                // Restore button
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    }

    function cancelDraw() {
        if (confirm('Are you sure you want to cancel this draw? All purchased tickets will be refunded.')) {
            // Show loading
            showAlert('warning', 'Cancelling draw...');
            
            // Make AJAX request
            fetch('{{ route("admin.lottery.draws.cancel", $draw->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Draw cancelled successfully!');
                    // Redirect to draws list after 2 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.lottery.draws") }}';
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to cancel draw');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while cancelling the draw');
            });
        }
    }

    function exportTickets() {
        window.location.href = '{{ route("admin.lottery.draws.export", ["id" => $draw->id, "type" => "tickets"]) }}';
    }

    function printDraw() {
        window.print();
    }

    function showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the page
        const firstCard = document.querySelector('.card');
        firstCard.parentNode.insertBefore(alertDiv, firstCard);
    }
</script>
@endpush
</x-layout>
