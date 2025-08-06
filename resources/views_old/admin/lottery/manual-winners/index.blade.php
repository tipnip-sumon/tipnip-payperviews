<x-layout>
    @section('top_title', 'Manual Winner Token/Ticket Selection')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Manual Winner Token/Ticket Selection')
            
            <!-- Header Actions -->
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">🎯 Manual Winner Token/Ticket Selection</h4>
                        <p class="text-muted mb-0">Select specific tickets as winners for lottery draws</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list"></i> All Draws
                        </a>
                        <a href="{{ route('admin.lottery-settings.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-settings"></i> Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fe fe-layers" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ $draws->total() }}</h4>
                                <p class="mb-0">Total Pending Draws</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fe fe-check-circle" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ collect($ticketStats)->where('has_manual_winners', true)->count() }}</h4>
                                <p class="mb-0">Manual Selection Active</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fe fe-ticket" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ collect($ticketStats)->sum('total_tickets') }}</h4>
                                <p class="mb-0">Total Tickets Available</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fe fe-users" style="font-size: 2rem;"></i>
                                <h4 class="mt-2">{{ collect($ticketStats)->sum('manual_winners_count') }}</h4>
                                <p class="mb-0">Manual Winners Selected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lottery.draws.create') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter by Status</label>
                                <select name="filter_status" class="form-select">
                                    <option value="">All Draws</option>
                                    <option value="has_tickets" {{ request('filter_status') == 'has_tickets' ? 'selected' : '' }}>Has Tickets</option>
                                    <option value="has_manual_winners" {{ request('filter_status') == 'has_manual_winners' ? 'selected' : '' }}>Manual Selection Active</option>
                                    <option value="no_manual_winners" {{ request('filter_status') == 'no_manual_winners' ? 'selected' : '' }}>No Manual Selection</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Draws List -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fe fe-list"></i> Available Draws for Manual Winner Selection
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($draws->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Draw Details</th>
                                            <th>Ticket Stats</th>
                                            <th>Manual Selection Status</th>
                                            <th>Prize Structure</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($draws as $draw)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>Draw #{{ $draw->id }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            📅 {{ $draw->draw_date->format('M j, Y') }}
                                                            ⏰ {{ $draw->draw_date->format('g:i A') }}
                                                        </small>
                                                        <br>
                                                        <span class="badge bg-{{ $draw->status == 'pending' ? 'warning' : 'success' }}">
                                                            {{ ucfirst($draw->status) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <strong>🎫 Tickets:</strong> {{ $ticketStats[$draw->id]['total_tickets'] ?? 0 }}
                                                        <br>
                                                        <strong>👥 Users:</strong> {{ $ticketStats[$draw->id]['unique_users'] ?? 0 }}
                                                        <br>
                                                        <strong>💰 Price:</strong> ${{ number_format($draw->ticket_price, 2) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($ticketStats[$draw->id]['has_manual_winners'] ?? false)
                                                        <div class="alert alert-success mb-0 p-2">
                                                            <i class="fe fe-check-circle"></i>
                                                            <strong>Active</strong>
                                                            <br>
                                                            <small>{{ $ticketStats[$draw->id]['manual_winners_count'] ?? 0 }} winners selected</small>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning mb-0 p-2">
                                                            <i class="fe fe-clock"></i>
                                                            <strong>Not Set</strong>
                                                            <br>
                                                            <small>Ready for manual selection</small>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        // Use global lottery settings since individual draw settings are stored in the draw itself
                                                        $prizeStructure = $settings->prize_structure ?? [];
                                                    @endphp
                                                    <div class="small">
                                                        @if(is_array($prizeStructure) && count($prizeStructure) > 0)
                                                            @foreach($prizeStructure as $position => $prize)
                                                                <div class="d-flex justify-content-between">
                                                                    <span>{{ $position }}:</span>
                                                                    <strong>${{ number_format($prize['amount'] ?? 0, 2) }}</strong>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Standard prizes</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical w-100" role="group"> 
                                                        @if(($ticketStats[$draw->id]['total_tickets'] ?? 0) > 0)
                                                            <!-- Manual Winner Manipulation (Primary Action) -->
                            <a href="{{ route('admin.lottery.draws.winner-manipulation', $draw->id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fe fe-edit"></i> Select Winners
                            </a>                                                            <!-- View Tickets -->
                                                            <button type="button" class="btn btn-info btn-sm" 
                                                                    onclick="viewTickets({{ $draw->id }}, '{{ $draw->id }}')">
                                                                <i class="fe fe-eye"></i> View Tickets ({{ $ticketStats[$draw->id]['total_tickets'] ?? 0 }})
                                                            </button>
                                                            
                                                            @if($ticketStats[$draw->id]['has_manual_winners'] ?? false)
                                                                <!-- Clear Manual Selection -->
                                                                <button type="button" class="btn btn-warning btn-sm" 
                                                                        onclick="clearManualWinners({{ $draw->id }})">
                                                                    <i class="fe fe-x"></i> Clear Selection
                                                                </button>
                                                            @endif
                                                        @else
                                                            <span class="text-muted small">No tickets purchased yet</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $draws->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-inbox" style="font-size: 4rem; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">No Pending Draws Found</h5>
                                <p class="text-muted">Create a new draw to start manual winner selection</p>
                                <a href="{{ route('admin.lottery.draws') }}" class="btn btn-primary">
                                    <i class="fe fe-list"></i> View All Draws
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket View Modal -->
        <div class="modal fade" id="ticketsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fe fe-ticket"></i> Tickets for Draw #<span id="modal-draw-id"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="tickets-content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading tickets...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="selectWinnersFromModal()">
                            <i class="fe fe-edit"></i> Select Winners
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        let currentDrawId = null;

        // View tickets for a specific draw
        function viewTickets(drawId, drawIdDisplay) {
            currentDrawId = drawId;
            document.getElementById('modal-draw-id').textContent = drawIdDisplay;
            
            const modal = new bootstrap.Modal(document.getElementById('ticketsModal'));
            modal.show();
            
            // Load tickets via AJAX
            fetch(`/admin/lottery/draws/${drawId}/tickets`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                displayTickets(data.tickets || []);
            })
            .catch(error => {
                console.error('Error loading tickets:', error);
                document.getElementById('tickets-content').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fe fe-alert-triangle"></i>
                        Failed to load tickets. Please try again.
                    </div>
                `;
            });
        }

        // Display tickets in modal
        function displayTickets(tickets) {
            const content = document.getElementById('tickets-content');
            
            if (tickets.length === 0) {
                content.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fe fe-info"></i>
                        No tickets found for this draw.
                    </div>
                `;
                return;
            }

            let html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fe fe-search"></i></span>
                            <input type="text" class="form-control" id="ticket-search" 
                                   placeholder="Search by ticket number or username...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="selectAllTickets()">
                                Select All
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="clearAllSelections()">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm" id="tickets-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Ticket #</th>
                                <th>User</th>
                                <th>Purchase Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            tickets.forEach(ticket => {
                html += `
                    <tr class="ticket-row" data-ticket-id="${ticket.id}">
                        <td>
                            <input type="checkbox" class="ticket-checkbox" value="${ticket.id}">
                        </td>
                        <td><strong>#${ticket.ticket_number}</strong></td>
                        <td>
                            <div>
                                <strong>${ticket.user?.username || 'N/A'}</strong>
                                <br><small class="text-muted">${ticket.user?.email || 'N/A'}</small>
                            </div>
                        </td>
                        <td>${new Date(ticket.created_at).toLocaleDateString()}</td>
                        <td>
                            <span class="badge bg-${ticket.status === 'active' ? 'success' : 'secondary'}">
                                ${ticket.status || 'active'}
                            </span>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <p class="text-muted">
                        <strong>Total tickets:</strong> ${tickets.length} | 
                        <strong>Selected:</strong> <span id="selected-count">0</span>
                    </p>
                </div>
            `;

            content.innerHTML = html;

            // Add search functionality
            document.getElementById('ticket-search').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tickets-table tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Add checkbox event listeners
            document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
        }

        // Update selected count
        function updateSelectedCount() {
            const selected = document.querySelectorAll('.ticket-checkbox:checked').length;
            const countElement = document.getElementById('selected-count');
            if (countElement) {
                countElement.textContent = selected;
            }
        }

        // Select all tickets
        function selectAllTickets() {
            document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        }

        // Clear all selections
        function clearAllSelections() {
            document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        }

        // Navigate to winner selection with selected tickets
        function selectWinnersFromModal() {
            const selectedTickets = Array.from(document.querySelectorAll('.ticket-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            if (selectedTickets.length === 0) {
                alert('Please select at least one ticket');
                return;
            }
            
            // Navigate to winner manipulation page with selected tickets
            const url = `/admin/lottery/draws/${currentDrawId}/winner-manipulation?selected_tickets=${selectedTickets.join(',')}`;
            window.location.href = url;
        }

        // Clear manual winners
        function clearManualWinners(drawId) {
            if (!confirm('Are you sure you want to clear all manual winner selections for this draw?')) {
                return;
            }

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
                    alert('Manual winner selections cleared successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to clear manual winners'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while clearing manual winners');
            });
        }

        // Auto-refresh every 30 seconds to show updated ticket counts
        setInterval(function() {
            // Only refresh if no modal is open
            if (!document.querySelector('.modal.show')) {
                location.reload();
            }
        }, 30000);
    </script>
@endpush
</x-layout>
