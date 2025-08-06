<x-layout>
    @section('top_title', 'Manual Winner Manipulation - Draw #' . $draw->display_draw_number)
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Manual Winner Manipulation')
            
            <!-- Header -->
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">🎯 Manual Winner Manipulation - Draw #{{ $draw->display_draw_number }}</h4>
                        <p class="text-muted mb-0">Select and manage winners for this lottery draw</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-outline-primary">
                            <i class="fe fe-arrow-left"></i> Back to Manual Winners
                        </a>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-list"></i> All Draws
                        </a>
                    </div>
                </div>
            </div>

            <!-- Draw Information -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6>Draw Details</h6>
                                <p><strong>Draw #:</strong> {{ $draw->display_draw_number }}</p>
                                <p><strong>Date:</strong> {{ $draw->draw_date->format('M j, Y g:i A') }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $draw->status == 'pending' ? 'warning' : 'success' }}">
                                        {{ ucfirst($draw->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <h6>Ticket Information</h6>
                                <p><strong>Total Tickets:</strong> {{ $tickets->count() }}</p>
                                <p><strong>Ticket Price:</strong> ${{ number_format($draw->ticket_price, 2) }}</p>
                                <p><strong>Total Pool:</strong> ${{ number_format($tickets->count() * $draw->ticket_price, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6>Prize Structure</h6>
                                @if(count($prizeDistribution) > 0)
                                    @foreach($prizeDistribution as $position => $prize)
                                        <p><strong>{{ $position }}:</strong> ${{ number_format($prize['amount'] ?? 0, 2) }}</p>
                                    @endforeach
                                @else
                                    <p class="text-muted">Standard prize structure</p>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <h6>Current Winners</h6>
                                <p><strong>Selected:</strong> {{ $existingWinners->count() }}</p>
                                @if($existingWinners->count() > 0)
                                    <button class="btn btn-warning btn-sm" onclick="clearAllWinners()">
                                        <i class="fe fe-x"></i> Clear All
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fe fe-search"></i></span>
                                    <input type="text" class="form-control" id="ticket-search" 
                                           placeholder="Search by ticket number or username...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="winner-filter">
                                    <option value="all">All Available Tickets</option>
                                    <option value="winners">Current Winners Only</option>
                                    <option value="non-winners">Non-Winners Only</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                                    <label class="form-check-label" for="auto-refresh">
                                        Auto-refresh validation
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success" onclick="selectRandomWinners()">
                                        <i class="fe fe-shuffle"></i> Random Select
                                    </button>
                                    <button class="btn btn-primary" onclick="saveWinnerSelection()">
                                        <i class="fe fe-save"></i> Save Winners
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Indicators -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-info me-2"></i>
                                        <div>
                                            <strong>Ticket Status Legend:</strong>
                                            <span class="badge bg-success ms-2">Available</span>
                                            <span class="badge bg-warning ms-1">Selected Winner</span>
                                            <span class="badge bg-danger ms-1">Unavailable/Used</span>
                                            <span class="badge bg-secondary ms-1">Virtual/Excluded</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draw Completion Status -->
            <div class="col-12 mb-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fe fe-info"></i> Draw Completion Status</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalPrizePositions = count($prizeDistribution);
                            $currentWinners = $existingWinners->count();
                            $remainingPositions = $totalPrizePositions - $currentWinners;
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-primary mb-1">{{ $totalPrizePositions }}</h4>
                                    <small class="text-muted">Total Prize Positions</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-success mb-1">{{ $currentWinners }}</h4>
                                    <small class="text-muted">Manually Selected</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-warning mb-1">{{ $remainingPositions }}</h4>
                                    <small class="text-muted">Remaining Positions</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-info mb-1">{{ $tickets->count() - $currentWinners }}</h4>
                                    <small class="text-muted">Available Tickets</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($remainingPositions > 0)
                            <div class="alert alert-info mt-3 mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><i class="fe fe-alert-circle"></i> Remaining Prize Distribution</h6>
                                        <p class="mb-0">
                                            <strong>{{ $remainingPositions }} positions</strong> still need winners. 
                                            When you perform the draw, the system will:
                                        </p>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Preserve</strong> your manually selected winners (Positions: {{ $existingWinners->pluck('prize_position')->sort()->implode(', ') }})</li>
                                            <li><strong>Auto-select</strong> winners for remaining positions from available real tickets</li>
                                            <li><strong>Exclude virtual tickets</strong> from automatic selection</li>
                                        </ul>
                                    </div>
                                    <div>
                                        @if($remainingPositions <= ($tickets->count() - $currentWinners))
                                            <span class="badge bg-success p-2">
                                                <i class="fe fe-check"></i> Ready to Complete
                                            </span>
                                        @else
                                            <span class="badge bg-warning p-2">
                                                <i class="fe fe-alert-triangle"></i> Need More Tickets
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success mt-3 mb-0">
                                <h6 class="mb-1"><i class="fe fe-check-circle"></i> All Positions Filled</h6>
                                <p class="mb-0">All {{ $totalPrizePositions }} prize positions have been manually assigned. The draw is ready to be finalized.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Winners (if any) -->
            @if($existingWinners->count() > 0)
            <div class="col-12 mb-4">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fe fe-award"></i> Current Winners</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($existingWinners->sortBy('prize_position') as $winner)
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6><i class="fe fe-trophy"></i> {{ $winner->prize_name ?? ('Position ' . $winner->prize_position) }}</h6>
                                            <p class="mb-1"><strong>Ticket:</strong> #{{ $winner->lotteryTicket->ticket_number ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>User:</strong> {{ $winner->user->username ?? 'N/A' }}</p>
                                            <p class="mb-1"><strong>Prize:</strong> 
                                                <span class="text-success">${{ number_format($winner->prize_amount, 2) }}</span>
                                                @if(str_contains($winner->prize_name ?? '', 'Split'))
                                                    <small class="text-muted">(Split among multiple winners)</small>
                                                @endif
                                            </p>
                                            <small class="text-muted">Position: {{ $winner->prize_position }} | ID: {{ $winner->id }}</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-danger mb-1" onclick="removeWinner({{ $winner->id }})">
                                                <i class="fe fe-x"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" onclick="changePosition({{ $winner->id }}, {{ $winner->prize_position }})">
                                                <i class="fe fe-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tickets List -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fe fe-ticket"></i> Available Tickets
                            <span class="badge bg-primary ms-2">{{ $tickets->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped" id="tickets-table">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Ticket #</th>
                                            <th>User Details</th>
                                            <th>Purchase Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            @php
                                                $isWinner = $existingWinners->where('lottery_ticket_id', $ticket->id)->first();
                                                $statusClass = $isWinner ? 'table-success' : 'table-light';
                                                $statusBadge = $isWinner ? 'success' : 'primary';
                                            @endphp
                                            <tr class="ticket-row {{ $statusClass }}" 
                                                data-ticket-id="{{ $ticket->id }}"
                                                data-user-id="{{ $ticket->user_id }}"
                                                data-ticket-number="{{ $ticket->ticket_number }}"
                                                data-is-winner="{{ $isWinner ? 'true' : 'false' }}"
                                                data-is-virtual="{{ $ticket->is_virtual ? 'true' : 'false' }}"
                                                data-used-at="{{ $ticket->used_as_token_at ?? 'null' }}">
                                                <td>
                                                    <input type="checkbox" class="ticket-checkbox" 
                                                           value="{{ $ticket->id }}"
                                                           {{ $isWinner ? 'checked disabled' : '' }}>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong>#{{ $ticket->ticket_number }}</strong>
                                                        <div class="ticket-status-indicator ms-2" id="status-{{ $ticket->id }}">
                                                            @if($isWinner)
                                                                <span class="badge bg-warning">
                                                                    <i class="fe fe-award"></i> Winner - Position {{ $isWinner->prize_position }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-{{ $statusBadge }}">
                                                                    <i class="fe fe-check"></i> Available
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">ID: {{ $ticket->id }}</small>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $ticket->user->username ?? 'N/A' }}</strong>
                                                        <br><small class="text-muted">{{ $ticket->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>{{ $ticket->created_at->format('M j, Y g:i A') }}</td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="badge bg-{{ $ticket->status === 'active' ? 'success' : 'secondary' }} mb-1">
                                                            {{ ucfirst($ticket->status) }}
                                                        </span>
                                                        @if($ticket->is_virtual)
                                                            <span class="badge bg-info">Virtual</span>
                                                        @elseif($ticket->used_as_token_at)
                                                            <span class="badge bg-warning">Used as Token</span>
                                                        @else
                                                            <span class="badge bg-primary">Real Ticket</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($isWinner)
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-danger btn-sm" onclick="removeWinner({{ $isWinner->id }})">
                                                                <i class="fe fe-x"></i> Remove
                                                            </button>
                                                            <button class="btn btn-info btn-sm" onclick="changePosition({{ $isWinner->id }}, {{ $isWinner->prize_position }})">
                                                                <i class="fe fe-edit"></i> Change Position
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-success btn-sm" onclick="makeWinnerWithPosition({{ $ticket->id }})">
                                                                <i class="fe fe-award"></i> Make Winner
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-inbox" style="font-size: 4rem; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">No Tickets Found</h5>
                                <p class="text-muted">No tickets have been purchased for this draw yet.</p>
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
        let selectedTickets = [];
        const drawId = {{ $draw->id }};
        let validationTimer = null;
        let isValidating = false;

        // Real-time ticket validation
        function validateTicketAvailability(ticketId) {
            if (isValidating) return;
            
            return fetch(`/admin/lottery/draws/${drawId}/tickets/${ticketId}/validate`)
                .then(response => response.json())
                .then(data => {
                    const statusIndicator = document.getElementById(`status-${ticketId}`);
                    const ticketRow = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
                    const checkbox = ticketRow?.querySelector('.ticket-checkbox');
                    
                    if (!data.valid) {
                        // Update status indicator
                        if (statusIndicator) {
                            statusIndicator.innerHTML = `<span class="badge bg-danger"><i class="fe fe-x"></i> ${data.reason === 'already_winner' ? 'Already Winner' : 'Unavailable'}</span>`;
                        }
                        
                        // Disable checkbox and uncheck it
                        if (checkbox && !checkbox.disabled) {
                            checkbox.disabled = true;
                            checkbox.checked = false;
                            showError(`Ticket #${data.ticket_number || ticketId} is no longer available: ${data.message}`);
                        }
                        
                        // Update row styling
                        if (ticketRow) {
                            ticketRow.classList.remove('table-success', 'table-light');
                            ticketRow.classList.add('table-danger');
                        }
                        
                        return false;
                    } else {
                        // Update status to show available
                        if (statusIndicator && !checkbox?.checked) {
                            statusIndicator.innerHTML = `<span class="badge bg-primary"><i class="fe fe-check"></i> Available</span>`;
                        }
                        
                        // Ensure checkbox is enabled if not a winner
                        if (checkbox && ticketRow?.getAttribute('data-is-winner') !== 'true') {
                            checkbox.disabled = false;
                            ticketRow.classList.remove('table-danger');
                            ticketRow.classList.add('table-light');
                        }
                        
                        return true;
                    }
                })
                .catch(error => {
                    console.error('Validation error:', error);
                    return false;
                });
        }

        // Auto-refresh validation for selected tickets
        function autoRefreshValidation() {
            if (!document.getElementById('auto-refresh').checked) return;
            
            const selectedCheckboxes = document.querySelectorAll('.ticket-checkbox:checked:not(:disabled)');
            const promises = Array.from(selectedCheckboxes).map(cb => {
                return validateTicketAvailability(cb.value);
            });
            
            Promise.all(promises).then(() => {
                // Schedule next validation
                validationTimer = setTimeout(autoRefreshValidation, 30000); // Every 30 seconds
            });
        }

        // Start auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            // Initial validation
            setTimeout(autoRefreshValidation, 2000);
            
            // Handle auto-refresh toggle
            document.getElementById('auto-refresh').addEventListener('change', function() {
                if (this.checked) {
                    autoRefreshValidation();
                } else {
                    if (validationTimer) {
                        clearTimeout(validationTimer);
                    }
                }
            });
        });

        // Search functionality
        document.getElementById('ticket-search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tickets-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('winner-filter').addEventListener('change', function() {
            const filter = this.value;
            const rows = document.querySelectorAll('#tickets-table tbody tr');
            
            rows.forEach(row => {
                const isWinner = row.getAttribute('data-is-winner') === 'true';
                
                switch(filter) {
                    case 'winners':
                        row.style.display = isWinner ? '' : 'none';
                        break;
                    case 'non-winners':
                        row.style.display = !isWinner ? '' : 'none';
                        break;
                    default:
                        row.style.display = '';
                        break;
                }
            });
        });

        // Make individual ticket a winner with position selection
        function makeWinnerWithPosition(ticketId) {
            // First validate ticket availability
            validateTicketAvailability(ticketId).then(isValid => {
                if (!isValid) {
                    showError('This ticket is no longer available for selection. Please refresh the page.');
                    return;
                }
                
                const position = prompt('Enter the position for this winner (1 for 1st place, 2 for 2nd place, etc.):', '1');
                if (!position || isNaN(position) || position < 1) {
                    showError('Please enter a valid position number (1, 2, 3, etc.)');
                    return;
                }

                if (!confirm(`Make this ticket the winner for Position ${position}?`)) return;

                console.log('Making ticket winner:', ticketId, 'Position:', position);

                fetch(`/admin/lottery/draws/${drawId}/manual-winners`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ticket_ids: [ticketId],
                        position: parseInt(position)
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showSuccess(`Winner added successfully for Position ${position}!`);
                        // Force reload with timestamp to prevent caching
                        window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                    } else {
                        showError(data.message || 'Failed to add winner');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('An error occurred while adding winner: ' + error.message);
                });
            });
        }

        // Change position of existing winner
        function changePosition(winnerId, currentPosition) {
            const newPosition = prompt(`Enter new position for this winner (currently Position ${currentPosition}):`, currentPosition);
            if (!newPosition || isNaN(newPosition) || newPosition < 1) {
                showError('Please enter a valid position number (1, 2, 3, etc.)');
                return;
            }

            if (newPosition == currentPosition) {
                showError('Position is already ' + currentPosition);
                return;
            }

            if (!confirm(`Change this winner from Position ${currentPosition} to Position ${newPosition}?`)) return;

            console.log('Changing winner position:', winnerId, 'New Position:', newPosition);

            fetch(`/admin/lottery/draws/${drawId}/manual-winners/${winnerId}/change-position`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    position: parseInt(newPosition)
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showSuccess(`Winner position changed to Position ${newPosition}!`);
                    // Force reload with timestamp to prevent caching
                    window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                } else {
                    showError(data.message || 'Failed to change position');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while changing position: ' + error.message);
            });
        }

        // Make individual ticket a winner (legacy function - kept for compatibility)
        function makeWinner(ticketId) {
            makeWinnerWithPosition(ticketId);
        }

        // Remove winner
        function removeWinner(winnerId) {
            if (!confirm('Remove this winner selection?')) return;

            fetch(`/admin/lottery/draws/${drawId}/manual-winners/${winnerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Winner removed successfully!');
                    // Force reload with timestamp to prevent caching
                    window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                } else {
                    showError(data.message || 'Failed to remove winner');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while removing winner');
            });
        }

        // Clear all winners
        function clearAllWinners() {
            if (!confirm('Clear all winner selections for this draw?')) return;

            fetch(`/admin/lottery/draws/${drawId}/clear-manual-winners`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('All winners cleared successfully!');
                    // Force reload with timestamp to prevent caching
                    window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                } else {
                    showError(data.message || 'Failed to clear winners');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while clearing winners');
            });
        }

        // Select random winners
        function selectRandomWinners() {
            const numWinners = prompt('How many random winners do you want to select?', '3');
            if (!numWinners || isNaN(numWinners) || numWinners < 1) return;

            const availableTickets = Array.from(document.querySelectorAll('tr[data-is-winner="false"]'))
                .map(row => row.getAttribute('data-ticket-id'));

            if (availableTickets.length < numWinners) {
                showError('Not enough available tickets for random selection');
                return;
            }

            // Randomly select tickets
            const shuffled = availableTickets.sort(() => 0.5 - Math.random());
            const selected = shuffled.slice(0, numWinners);

            if (confirm(`Select ${selected.length} random winners?`)) {
                saveWinnersToServer(selected);
            }
        }

        // Save winner selection with validation
        function saveWinnerSelection() {
            const selectedCheckboxes = document.querySelectorAll('.ticket-checkbox:checked:not(:disabled)');
            const ticketIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (ticketIds.length === 0) {
                showError('Please select at least one ticket');
                return;
            }

            // Validate all selected tickets first
            showSuccess('Validating selected tickets...');
            
            const validationPromises = ticketIds.map(ticketId => validateTicketAvailability(ticketId));
            
            Promise.all(validationPromises).then(results => {
                const invalidTickets = results.filter(result => !result).length;
                
                if (invalidTickets > 0) {
                    showError(`${invalidTickets} of the selected tickets are no longer available. Please review your selection and try again.`);
                    return;
                }
                
                // All tickets are valid, proceed with position assignment
                proceedWithWinnerSelection(ticketIds);
            }).catch(error => {
                console.error('Validation error:', error);
                showError('Error validating tickets. Please try again.');
            });
        }

        // Proceed with winner selection after validation
        function proceedWithWinnerSelection(ticketIds) {
            // Ask for position assignment method
            const assignmentMethod = prompt(
                `You have selected ${ticketIds.length} tickets.\n\n` +
                'Choose position assignment method:\n' +
                '1 = Auto-assign sequential positions (1, 2, 3...)\n' +
                '2 = Assign all to same position\n' +
                '3 = Assign positions manually\n\n' +
                'Enter your choice (1, 2, or 3):', '1'
            );

            if (!assignmentMethod || !['1', '2', '3'].includes(assignmentMethod)) {
                showError('Please choose a valid option (1, 2, or 3)');
                return;
            }

            switch (assignmentMethod) {
                case '1':
                    // Auto-assign sequential positions
                    saveWinnersWithSequentialPositions(ticketIds);
                    break;
                case '2':
                    // Assign all to same position
                    saveWinnersWithSamePosition(ticketIds);
                    break;
                case '3':
                    // Manual position assignment
                    saveWinnersWithManualPositions(ticketIds);
                    break;
            }
        }

        // Save winners with sequential positions
        function saveWinnersWithSequentialPositions(ticketIds) {
            if (confirm(`Add ${ticketIds.length} tickets as winners with sequential positions (1, 2, 3...)?`)) {
                saveWinnersToServer(ticketIds, 'sequential');
            }
        }

        // Save winners with same position
        function saveWinnersWithSamePosition(ticketIds) {
            const position = prompt('Enter the position for all selected tickets:', '1');
            if (!position || isNaN(position) || position < 1) {
                showError('Please enter a valid position number');
                return;
            }

            if (confirm(`Add ${ticketIds.length} tickets as winners for Position ${position}?`)) {
                saveWinnersToServer(ticketIds, 'same', parseInt(position));
            }
        }

        // Save winners with manual positions
        function saveWinnersWithManualPositions(ticketIds) {
            const positions = [];
            let valid = true;

            for (let i = 0; i < ticketIds.length; i++) {
                const position = prompt(`Enter position for ticket ${i + 1}/${ticketIds.length}:`, (i + 1).toString());
                if (!position || isNaN(position) || position < 1) {
                    showError('Invalid position entered. Operation cancelled.');
                    valid = false;
                    break;
                }
                positions.push(parseInt(position));
            }

            if (valid && confirm(`Add ${ticketIds.length} tickets as winners with custom positions?`)) {
                saveWinnersToServer(ticketIds, 'manual', null, positions);
            }
        }

        // Save winners to server with position options
        function saveWinnersToServer(ticketIds, method = 'sequential', samePosition = null, customPositions = null) {
            console.log('Sending ticket IDs:', ticketIds, 'Method:', method);
            
            let requestData = { ticket_ids: ticketIds };

            // Add position data based on method
            switch (method) {
                case 'sequential':
                    // Server will auto-assign sequential positions
                    break;
                case 'same':
                    requestData.same_position = samePosition;
                    break;
                case 'manual':
                    requestData.custom_positions = customPositions;
                    break;
                default:
                    // For single ticket selection (legacy)
                    if (samePosition) {
                        requestData.position = samePosition;
                    }
            }

            fetch(`/admin/lottery/draws/${drawId}/manual-winners`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showSuccess('Winners saved successfully!');
                    // Force reload with timestamp to prevent caching
                    window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                } else {
                    showError(data.message || 'Failed to save winners');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while saving winners: ' + error.message);
            });
        }

        // Get next available position for winner
        function getNextAvailablePosition() {
            const existingWinners = document.querySelectorAll('[data-is-winner="true"]').length;
            return existingWinners + 1;
        }

        // Show success message
        function showSuccess(message) {
            // You can replace this with a toast notification or modal
            alert('✅ ' + message);
        }

        // Show error message
        function showError(message) {
            // You can replace this with a toast notification or modal
            alert('❌ ' + message);
        }

        // Handle URL parameters for pre-selected tickets
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedTickets = urlParams.get('selected_tickets');
            
            if (selectedTickets) {
                const ticketIds = selectedTickets.split(',');
                ticketIds.forEach(ticketId => {
                    const checkbox = document.querySelector(`input[value="${ticketId}"]`);
                    if (checkbox && !checkbox.disabled) {
                        checkbox.checked = true;
                    }
                });
                
                // Show notification about pre-selected tickets
                if (ticketIds.length > 0) {
                    showSuccess(`${ticketIds.length} tickets pre-selected from previous modal`);
                }
            }
        });
    </script>
@endpush
</x-layout>
