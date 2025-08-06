<x-layout>
    @section('top_title', 'Lottery Winners')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Winners Management')
            
            <!-- Statistics Cards -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="h2 text-success mb-2">{{ number_format($totalWinners) }}</div>
                                <p class="text-muted mb-0">Total Winners</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="h2 text-warning mb-2">${{ number_format($totalPrizes, 2) }}</div>
                                <p class="text-muted mb-0">Total Prizes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="h2 text-info mb-2">${{ number_format($distributedPrizes, 2) }}</div>
                                <p class="text-muted mb-0">Distributed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <div class="h2 text-danger mb-2">${{ number_format($pendingPrizes, 2) }}</div>
                                <p class="text-muted mb-0">Pending</p>
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
                            <button type="button" class="btn btn-outline-primary" onclick="exportWinners()">
                                <i class="fe fe-download"></i> Export
                            </button>
                            <button type="button" class="btn btn-warning" onclick="distributePendingPrizes()">
                                <i class="fe fe-dollar-sign"></i> Distribute All Pending
                            </button>
                            <button type="button" class="btn btn-success" onclick="sendWinnerNotifications()">
                                <i class="fe fe-mail"></i> Notify Winners
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lottery.winners') }}" class="row g-3">
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
                                <label class="form-label">Position</label>
                                <select name="position" class="form-select">
                                    <option value="">All Positions</option>
                                    <option value="1" {{ request('position') === '1' ? 'selected' : '' }}>1st Place</option>
                                    <option value="2" {{ request('position') === '2' ? 'selected' : '' }}>2nd Place</option>
                                    <option value="3" {{ request('position') === '3' ? 'selected' : '' }}>3rd Place</option>
                                    <option value="other" {{ request('position') === 'other' ? 'selected' : '' }}>Other Positions</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Prize Status</label>
                                <select name="prize_status" class="form-select">
                                    <option value="">All</option>
                                    <option value="distributed" {{ request('prize_status') === 'distributed' ? 'selected' : '' }}>Distributed</option>
                                    <option value="pending" {{ request('prize_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Winner name, email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-search"></i>
                                    </button>
                                    <a href="{{ route('admin.lottery.winners') }}" class="btn btn-secondary">
                                        <i class="fe fe-x"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Winners Table -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-award me-2"></i>Winners List</h5>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All
                                </label>
                            </div>
                            <span class="text-muted">{{ $winners->total() }} total winners</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($winners && count($winners) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                            </th>
                                            <th>Position</th>
                                            <th>Draw</th>
                                            <th>Winner</th>
                                            <th>Ticket Number</th>
                                            <th>Prize Amount</th>
                                            <th>Win Date</th>
                                            <th>Prize Status</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($winners as $winner)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input winner-checkbox" 
                                                           value="{{ $winner->id }}">
                                                </td>
                                                <td>
                                                    @if($winner->position === 1)
                                                        <span class="badge bg-warning fs-6"><i class="fe fe-award"></i> 1st</span>
                                                    @elseif($winner->position === 2)
                                                        <span class="badge bg-secondary fs-6"><i class="fe fe-award"></i> 2nd</span>
                                                    @elseif($winner->position === 3)
                                                        <span class="badge bg-info fs-6"><i class="fe fe-award"></i> 3rd</span>
                                                    @else
                                                        <span class="badge bg-light text-dark fs-6"><i class="fe fe-award"></i> {{ $winner->position }}th</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($winner->draw_id)
                                                        <a href="{{ route('admin.lottery.draws.details', $winner->draw_id) }}" 
                                                           class="text-decoration-none">
                                                            <strong>Draw #{{ $winner->draw_id }}</strong>
                                                        </a>
                                                    @else
                                                        <strong class="text-muted">Draw #N/A</strong>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($winner->draw->draw_date ?? '')->format('M d, Y') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-primary text-white me-3">
                                                            {{ strtoupper(substr($winner->user->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $winner->user->name ?? 'Unknown User' }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $winner->user->email ?? 'No email' }}</small>
                                                            @if($winner->user->phone)
                                                                <br>
                                                                <small class="text-muted">{{ $winner->user->phone }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="fs-6">{{ $winner->ticket_number }}</code>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong class="text-success fs-5">${{ number_format($winner->prize_amount, 2) }}</strong>
                                                        @if($winner->position === 1)
                                                            <br><small class="text-warning">🏆 First Prize</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $winner->win_date ? \Carbon\Carbon::parse($winner->win_date)->format('M d, Y') : 'Unknown' }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $winner->win_date ? \Carbon\Carbon::parse($winner->win_date)->format('h:i A') : '' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($winner->prize_distributed)
                                                        <span class="badge bg-success">
                                                            <i class="fe fe-check"></i> Distributed
                                                        </span>
                                                        @if($winner->distribution_date)
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($winner->distribution_date)->format('M d, Y') }}
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fe fe-clock"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" data-bs-toggle="dropdown">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="#" onclick="viewWinnerDetails({{ $winner->id }})">
                                                                <i class="fe fe-eye"></i> View Details
                                                            </a></li>
                                                            @if(!$winner->prize_distributed)
                                                                <li><a class="dropdown-item" href="#" onclick="distributePrize({{ $winner->id }})">
                                                                    <i class="fe fe-dollar-sign"></i> Distribute Prize
                                                                </a></li>
                                                            @endif
                                                            <li><a class="dropdown-item" href="#" onclick="sendWinnerNotification({{ $winner->id }})">
                                                                <i class="fe fe-mail"></i> Send Notification
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="printWinnerCertificate({{ $winner->id }})">
                                                                <i class="fe fe-printer"></i> Print Certificate
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="removeWinner({{ $winner->id }})">
                                                                <i class="fe fe-x-circle"></i> Remove Winner
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
                                        Showing {{ $winners->firstItem() }} to {{ $winners->lastItem() }} of {{ $winners->total() }} winners
                                    </div>
                                    {{ $winners->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-award fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No winners found</h5>
                                <p class="text-muted">No winners match your current filter criteria.</p>
                                <a href="{{ route('admin.lottery.winners') }}" class="btn btn-primary">
                                    <i class="fe fe-refresh-cw"></i> Clear Filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Winner Details Modal -->
        <div class="modal fade" id="winnerDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Winner Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="winnerDetailsContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        const checkboxes = document.querySelectorAll('.winner-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('selectAllHeader').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.winner-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('selectAll').checked = this.checked;
    });

    // Update select all when individual checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('winner-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.winner-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.winner-checkbox:checked');
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

    function getSelectedWinners() {
        const checkboxes = document.querySelectorAll('.winner-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function exportWinners() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'true');
        window.location.href = '{{ route("admin.lottery.winners") }}?' + params.toString();
    }

    function distributePendingPrizes() {
        if (confirm('Are you sure you want to distribute all pending prizes? This action cannot be undone.')) {
            showAlert('info', 'Distributing prizes...');
            
            fetch('{{ route("admin.lottery.winners.distribute-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'All pending prizes distributed successfully');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to distribute prizes');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while distributing prizes');
            });
        }
    }

    function sendWinnerNotifications() {
        const selected = getSelectedWinners();
        const message = selected.length > 0 
            ? `Send notifications to ${selected.length} selected winner(s)?`
            : 'Send notifications to all winners?';
            
        if (confirm(message)) {
            showAlert('info', 'Sending notifications...');
            
            fetch('{{ route("admin.lottery.winners.notify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    winner_ids: selected.length > 0 ? selected : 'all'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Notifications sent successfully');
                } else {
                    showAlert('danger', data.message || 'Failed to send notifications');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while sending notifications');
            });
        }
    }

    function viewWinnerDetails(winnerId) {
        // Load winner details via AJAX
        fetch(`{{ route('admin.lottery.winners') }}/${winnerId}/details`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('winnerDetailsContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('winnerDetailsModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load winner details');
            });
    }

    function distributePrize(winnerId) {
        if (confirm('Are you sure you want to distribute the prize to this winner?')) {
            showAlert('info', 'Distributing prize...');
            
            fetch(`{{ route('admin.lottery.winners') }}/${winnerId}/distribute`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Prize distributed successfully');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to distribute prize');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while distributing the prize');
            });
        }
    }

    function sendWinnerNotification(winnerId) {
        showAlert('info', 'Sending notification...');
        
        fetch(`{{ route('admin.lottery.winners') }}/${winnerId}/notify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Notification sent successfully');
            } else {
                showAlert('danger', data.message || 'Failed to send notification');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while sending notification');
        });
    }

    function printWinnerCertificate(winnerId) {
        window.open(`{{ route('admin.lottery.winners') }}/${winnerId}/certificate`, '_blank');
    }

    function removeWinner(winnerId) {
        if (confirm('Are you sure you want to remove this winner? This action cannot be undone.')) {
            showAlert('info', 'Removing winner...');
            
            fetch(`{{ route('admin.lottery.winners') }}/${winnerId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Winner removed successfully');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to remove winner');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while removing the winner');
            });
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
