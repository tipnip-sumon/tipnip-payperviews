<x-layout>
    @section('top_title', 'Lottery Tickets')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Tickets Management')
            
            <!-- Statistics Cards -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <div class="h2 text-primary mb-2">{{ number_format($totalTickets) }}</div>
                                <p class="text-muted mb-0">Total Tickets</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="h2 text-success mb-2">{{ number_format($soldTickets) }}</div>
                                <p class="text-muted mb-0">Sold Tickets</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="h2 text-warning mb-2">{{ number_format($winningTickets) }}</div>
                                <p class="text-muted mb-0">Winning Tickets</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="h2 text-info mb-2">${{ number_format($totalRevenue, 2) }}</div>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-filter me-2"></i>Filters & Actions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="exportTickets()">
                                <i class="fe fe-download"></i> Export
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fe fe-more-vertical"></i> Bulk Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="refundSelected()">
                                        <i class="fe fe-dollar-sign"></i> Refund Selected
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="markAsWinner()">
                                        <i class="fe fe-award"></i> Mark as Winner
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteSelected()">
                                        <i class="fe fe-trash-2"></i> Delete Selected
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lottery.tickets') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Draw</label>
                                <select name="draw_id" class="form-select">
                                    <option value="">All Draws</option>
                                    @foreach($draws as $draw)
                                        <option value="{{ $draw->id }}" {{ request('draw_id') == $draw->id ? 'selected' : '' }}>
                                            Draw #{{ $draw->id }} - {{ \Carbon\Carbon::parse($draw->draw_date)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="winner" {{ request('status') === 'winner' ? 'selected' : '' }}>Winner</option>
                                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Ticket number, user email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.lottery.tickets') }}" class="btn btn-secondary">
                                        <i class="fe fe-x"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-ticket me-2"></i>Tickets List</h5>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All
                                </label>
                            </div>
                            <span class="text-muted">{{ $tickets->total() }} total tickets</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($tickets && count($tickets) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                            </th>
                                            <th>Ticket Number</th>
                                            <th>Draw</th>
                                            <th>Buyer</th>
                                            <th>Price</th>
                                            <th>Purchase Date</th>
                                            <th>Status</th>
                                            <th>Prize</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input ticket-checkbox" 
                                                           value="{{ $ticket->id }}">
                                                </td>
                                                <td>
                                                    <code class="fs-6">{{ $ticket->ticket_number }}</code>
                                                </td>
                                                <td>
                                                    @if($ticket->lottery_draw_id)
                                                        <a href="{{ route('admin.lottery.draws.details', $ticket->lottery_draw_id) }}" 
                                                           class="text-decoration-none">
                                                            Draw #{{ $ticket->lottery_draw_id }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $ticket->lotteryDraw ? $ticket->lotteryDraw->draw_date->format('M d, Y') : 'Date not available' }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">No Draw Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $ticket->user->fullname ?? 'Unknown User' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $ticket->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong class="text-success">${{ number_format($ticket->ticket_price, 2) }}</strong>
                                                </td>
                                                <td>
                                                    {{ $ticket->purchased_at ? $ticket->purchased_at->format('M d, Y') : ($ticket->created_at ? $ticket->created_at->format('M d, Y') : 'Unknown') }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $ticket->purchased_at ? $ticket->purchased_at->format('h:i A') : ($ticket->created_at ? $ticket->created_at->format('h:i A') : '') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @php
                                                        $status = 'active';
                                                        $badgeClass = 'bg-info';
                                                        
                                                        if ($ticket->lotteryDraw && $ticket->lotteryDraw->status === 'completed') {
                                                            if ($ticket->status === 'winner') {
                                                                $status = 'winner';
                                                                $badgeClass = 'bg-success';
                                                            } else {
                                                                $status = 'lost';
                                                                $badgeClass = 'bg-secondary';
                                                            }
                                                        } elseif ($ticket->status === 'refunded') {
                                                            $status = 'refunded';
                                                            $badgeClass = 'bg-warning';
                                                        } elseif ($ticket->status === 'expired') {
                                                            $status = 'expired';
                                                            $badgeClass = 'bg-dark';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                                </td>
                                                <td>
                                                    @if($ticket->status === 'winner' && $ticket->prize_amount)
                                                        <strong class="text-success">${{ number_format($ticket->prize_amount, 2) }}</strong>
                                                        @if($ticket->claimed_at)
                                                            <br><small class="text-success">Claimed</small>
                                                        @else
                                                            <br><small class="text-warning">Pending</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" data-bs-toggle="dropdown">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="#" onclick="viewTicket({{ $ticket->id }})">
                                                                <i class="fe fe-eye"></i> View Details
                                                            </a></li>
                                                            @if($ticket->status !== 'refunded' && (!$ticket->lotteryDraw || $ticket->lotteryDraw->status !== 'completed'))
                                                                <li><a class="dropdown-item" href="#" onclick="refundTicket({{ $ticket->id }})">
                                                                    <i class="fe fe-dollar-sign"></i> Refund
                                                                </a></li>
                                                            @endif
                                                            @if($ticket->status === 'winner' && !$ticket->claimed_at)
                                                                <li><a class="dropdown-item" href="#" onclick="distributePrize({{ $ticket->id }})">
                                                                    <i class="fe fe-award"></i> Distribute Prize
                                                                </a></li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTicket({{ $ticket->id }})">
                                                                <i class="fe fe-trash-2"></i> Delete
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} tickets
                                    </div>
                                    {{ $tickets->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-ticket fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No tickets found</h5>
                                <p class="text-muted">No tickets match your current filter criteria.</p>
                                <a href="{{ route('admin.lottery.tickets') }}" class="btn btn-primary">
                                    <i class="fe fe-refresh-cw"></i> Clear Filters
                                </a>
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
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('selectAllHeader').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('selectAll').checked = this.checked;
    });

    // Update select all when individual checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('ticket-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.ticket-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.ticket-checkbox:checked');
            const selectAllMain = document.getElementById('selectAll');
            const selectAllHeader = document.getElementById('selectAllHeader');
            
            if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAllMain.checked = true;
                selectAllHeader.checked = true;
            } else {
                selectAllMain.checked = false;
                selectAllHeader.checked = false;
            }
        }
    });

    function getSelectedTickets() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function exportTickets() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'true');
        window.location.href = '{{ route("admin.lottery.tickets") }}?' + params.toString();
    }

    function refundSelected() {
        const selected = getSelectedTickets();
        if (selected.length === 0) {
            alert('Please select tickets to refund');
            return;
        }
        
        if (confirm(`Are you sure you want to refund ${selected.length} selected ticket(s)?`)) {
            performBulkAction('refund', selected);
        }
    }

    function markAsWinner() {
        const selected = getSelectedTickets();
        if (selected.length === 0) {
            alert('Please select tickets to mark as winners');
            return;
        }
        
        if (confirm(`Are you sure you want to mark ${selected.length} selected ticket(s) as winners?`)) {
            performBulkAction('mark_winner', selected);
        }
    }

    function deleteSelected() {
        const selected = getSelectedTickets();
        if (selected.length === 0) {
            alert('Please select tickets to delete');
            return;
        }
        
        if (confirm(`Are you sure you want to delete ${selected.length} selected ticket(s)? This action cannot be undone.`)) {
            performBulkAction('delete', selected);
        }
    }

    function performBulkAction(action, ticketIds) {
        showAlert('info', 'Processing...');
        
        fetch('{{ route("admin.lottery.tickets.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                ticket_ids: ticketIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Action completed successfully');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('danger', data.message || 'Action failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while processing the action');
        });
    }

    function viewTicket(ticketId) {
        // Open ticket details modal or navigate to details page
        window.location.href = '{{ route("admin.lottery.tickets.details", ":id") }}'.replace(':id', ticketId);
    }

    function refundTicket(ticketId) {
        if (confirm('Are you sure you want to refund this ticket?')) {
            performBulkAction('refund', [ticketId]);
        }
    }

    function distributePrize(ticketId) {
        if (confirm('Are you sure you want to distribute the prize for this ticket?')) {
            performBulkAction('distribute_prize', [ticketId]);
        }
    }

    function deleteTicket(ticketId) {
        if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
            performBulkAction('delete', [ticketId]);
        }
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
