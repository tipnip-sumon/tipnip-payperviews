<x-layout>
    @section('top_title', 'Lottery Management Dashboard')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Management')
            
            <!-- Statistics Cards -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="h3 text-primary mb-2">{{ number_format($stats['total_draws']) }}</div>
                                        <p class="text-muted mb-0 fw-semibold">Total Draws</p>
                                    </div>
                                    <div class="avatar-lg bg-primary-subtle rounded">
                                        <i class="fe fe-refresh-cw avatar-title fs-24 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="h3 text-success mb-2">${{ number_format($stats['total_revenue'], 2) }}</div>
                                        <p class="text-muted mb-0 fw-semibold">Total Revenue</p>
                                    </div>
                                    <div class="avatar-lg bg-success-subtle rounded">
                                        <i class="fe fe-dollar-sign avatar-title fs-24 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="h3 text-info mb-2">{{ number_format($stats['total_tickets']) }}</div>
                                        <p class="text-muted mb-0 fw-semibold">Total Tickets</p>
                                    </div>
                                    <div class="avatar-lg bg-info-subtle rounded">
                                        <i class="fe fe-ticket avatar-title fs-24 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="h3 text-warning mb-2">${{ number_format($stats['total_prizes'], 2) }}</div>
                                        <p class="text-muted mb-0 fw-semibold">Prizes Paid</p>
                                    </div>
                                    <div class="avatar-lg bg-warning-subtle rounded">
                                        <i class="fe fe-award avatar-title fs-24 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Draw Status -->
            <div class="col-xl-8 col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-calendar me-2"></i>Current Draw Status</h5>
                        <div class="d-flex gap-2">
                            @if($currentDraw && $currentDraw->status === 'pending')
                                <button type="button" class="btn btn-success btn-sm" onclick="performDraw({{ $currentDraw->id }})">
                                    <i class="fe fe-play"></i> Perform Draw
                                </button>
                            @endif
                            <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary btn-sm">
                                <i class="fe fe-plus"></i> New Draw
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($currentDraw)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-md bg-primary-subtle rounded">
                                                <i class="fe fe-hash avatar-title fs-18 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Draw #{{ $currentDraw->id }}</h6>
                                            <p class="text-muted mb-0">{{ $currentDraw->draw_number ?? 'DRAW_' . $currentDraw->id }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-md bg-info-subtle rounded">
                                                <i class="fe fe-calendar avatar-title fs-18 text-info"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Draw Date</h6>
                                            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($currentDraw->draw_date)->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-md bg-success-subtle rounded">
                                                <i class="fe fe-ticket avatar-title fs-18 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Tickets Sold</h6>
                                            <p class="text-muted mb-0">{{ number_format($currentDraw->total_tickets_sold ?? 0) }} tickets</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-md bg-warning-subtle rounded">
                                                <i class="fe fe-dollar-sign avatar-title fs-18 text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Prize Pool</h6>
                                            <p class="text-muted mb-0">${{ number_format($currentDraw->total_prize_pool ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                @php
                                    $statusClass = match($currentDraw->status) {
                                        'pending' => 'bg-warning',
                                        'drawn' => 'bg-info',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} fs-12">{{ ucfirst($currentDraw->status) }}</span>
                                @if($currentDraw->status === 'pending' && ($currentDraw->total_tickets_sold ?? 0) >= ($settings->min_tickets_for_draw ?? 1))
                                    <span class="badge bg-success ms-2">Ready for Draw</span>
                                @elseif($currentDraw->status === 'pending')
                                    <span class="badge bg-secondary ms-2">{{ ($settings->min_tickets_for_draw ?? 1) - ($currentDraw->total_tickets_sold ?? 0) }} more tickets needed</span>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-calendar-x fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No Current Draw</h5>
                                <p class="text-muted">Create a new draw to get started.</p>
                                <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                                    <i class="fe fe-plus"></i> Create Draw
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-xl-4 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-zap me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.lottery.tickets') }}" class="btn btn-outline-primary">
                                <i class="fe fe-ticket me-2"></i>Manage Tickets
                            </a>
                            <a href="{{ route('admin.lottery.winners') }}" class="btn btn-outline-success">
                                <i class="fe fe-award me-2"></i>View Winners
                                @if($pendingClaims->count() > 0)
                                    <span class="badge bg-warning ms-2">{{ $pendingClaims->count() }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.lottery-settings.index') }}" class="btn btn-outline-info">
                                <i class="fe fe-settings me-2"></i>Lottery Settings
                            </a>
                            <a href="{{ route('admin.lottery.report') }}" class="btn btn-outline-warning">
                                <i class="fe fe-bar-chart-2 me-2"></i>Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Draws -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-clock me-2"></i>Recent Draws</h5>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentDraws && count($recentDraws) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Draw #</th>
                                            <th>Date</th>
                                            <th>Tickets</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentDraws as $draw)
                                            <tr>
                                                <td><strong>#{{ $draw->id }}</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($draw->draw_date)->format('M d, Y') }}</td>
                                                <td>{{ number_format($draw->total_tickets_sold ?? 0) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = match($draw->status) {
                                                            'pending' => 'bg-warning',
                                                            'drawn' => 'bg-info',
                                                            'completed' => 'bg-success',
                                                            'cancelled' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ ucfirst($draw->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.lottery.draws.details', $draw->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-inbox fs-1 text-muted"></i>
                                <h6 class="text-muted mt-3">No Recent Draws</h6>
                                <p class="text-muted mb-0">Recent draws will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Claims -->
            <div class="col-xl-6 col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fe fe-clock me-2"></i>Pending Claims
                            @if($pendingClaims->count() > 0)
                                <span class="badge bg-warning ms-2">{{ $pendingClaims->count() }}</span>
                            @endif
                        </h5>
                        <a href="{{ route('admin.lottery.winners') }}?claim_status=pending" class="btn btn-sm btn-outline-warning">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($pendingClaims && count($pendingClaims) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Winner</th>
                                            <th>Draw</th>
                                            <th>Prize</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingClaims->take(5) as $claim)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $claim->user->name ?? 'Unknown' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $claim->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </td>
                                                <td>#{{ $claim->lotteryDraw->id ?? 'N/A' }}</td>
                                                <td><strong class="text-success">${{ number_format($claim->prize_amount, 2) }}</strong></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" onclick="forceClaim({{ $claim->id }})">
                                                        <i class="fe fe-check"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-check-circle fs-1 text-success"></i>
                                <h6 class="text-muted mt-3">No Pending Claims</h6>
                                <p class="text-muted mb-0">All prizes have been claimed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Draws Management Table -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-list me-2"></i>All Draws</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleFilters()">
                                <i class="fe fe-filter"></i> Filters
                            </button>
                            <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus"></i> New Draw
                            </a>
                        </div>
                    </div>
                    
                    <!-- Filters (initially hidden) -->
                    <div class="card-body border-bottom d-none" id="filtersSection">
                        <form method="GET" action="{{ route('admin.lottery.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="drawn" {{ request('status') === 'drawn' ? 'selected' : '' }}>Drawn</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Draw ID, tickets..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-search"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.lottery.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fe fe-x"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        @if($draws && count($draws) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                                   class="text-white text-decoration-none">
                                                    Draw # 
                                                    @if(request('sort') === 'id')
                                                        <i class="fe fe-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'draw_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                                   class="text-white text-decoration-none">
                                                    Date 
                                                    @if(request('sort') === 'draw_date')
                                                        <i class="fe fe-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_tickets', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                                   class="text-white text-decoration-none">
                                                    Tickets 
                                                    @if(request('sort') === 'total_tickets')
                                                        <i class="fe fe-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>
                                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_prize', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                                   class="text-white text-decoration-none">
                                                    Prize Pool 
                                                    @if(request('sort') === 'total_prize')
                                                        <i class="fe fe-chevron-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>Status</th>
                                            <th>Winners</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($draws as $draw)
                                            <tr>
                                                <td><strong>#{{ $draw->id }}</strong></td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($draw->draw_date)->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($draw->draw_date)->format('h:i A') }}</small>
                                                </td>
                                                <td>{{ number_format($draw->total_tickets_sold ?? 0) }}</td>
                                                <td><strong class="text-success">${{ number_format($draw->total_prize_pool ?? 0, 2) }}</strong></td>
                                                <td>
                                                    @php
                                                        $statusClass = match($draw->status) {
                                                            'pending' => 'bg-warning',
                                                            'drawn' => 'bg-info',
                                                            'completed' => 'bg-success',
                                                            'cancelled' => 'bg-danger',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ ucfirst($draw->status) }}</span>
                                                </td>
                                                <td>
                                                    @if($draw->winners && count($draw->winners) > 0)
                                                        <span class="badge bg-success">{{ count($draw->winners) }} winner(s)</span>
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
                                                            <li><a class="dropdown-item" href="{{ route('admin.lottery.draws.details', $draw->id) }}">
                                                                <i class="fe fe-eye"></i> View Details
                                                            </a></li>
                                                            @if($draw->status === 'pending')
                                                                <li><a class="dropdown-item" href="#" onclick="performDraw({{ $draw->id }})">
                                                                    <i class="fe fe-play"></i> Perform Draw
                                                                </a></li>
                                                                <li><a class="dropdown-item text-warning" href="#" onclick="cancelDraw({{ $draw->id }})">
                                                                    <i class="fe fe-x-circle"></i> Cancel Draw
                                                                </a></li>
                                                            @endif
                                                            @if(in_array($draw->status, ['cancelled', 'completed']) && ($draw->total_tickets_sold ?? 0) === 0)
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteDraw({{ $draw->id }})">
                                                                    <i class="fe fe-trash-2"></i> Delete
                                                                </a></li>
                                                            @endif
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
                                        Showing {{ $draws->firstItem() }} to {{ $draws->lastItem() }} of {{ $draws->total() }} draws
                                    </div>
                                    {{ $draws->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fe fe-calendar fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No draws found</h5>
                                <p class="text-muted">No draws match your current filter criteria.</p>
                                <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                                    <i class="fe fe-plus"></i> Create First Draw
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
<script>
    function toggleFilters() {
        const filtersSection = document.getElementById('filtersSection');
        filtersSection.classList.toggle('d-none');
    }

    function performDraw(drawId) {
        if (confirm('Are you sure you want to perform this draw? This action cannot be undone.')) {
            showAlert('info', 'Performing draw...');
            
            fetch(`{{ url('admin/lottery/draws') }}/${drawId}/perform`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Draw performed successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to perform draw');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while performing the draw');
            });
        }
    }

    function cancelDraw(drawId) {
        if (confirm('Are you sure you want to cancel this draw? All tickets will be refunded.')) {
            showAlert('info', 'Cancelling draw...');
            
            fetch(`{{ url('admin/lottery/draws') }}/${drawId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Draw cancelled successfully!');
                    setTimeout(() => {
                        window.location.reload();
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

    function deleteDraw(drawId) {
        if (confirm('Are you sure you want to delete this draw? This action cannot be undone.')) {
            showAlert('info', 'Deleting draw...');
            
            fetch(`{{ url('admin/lottery/draws') }}/${drawId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Draw deleted successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to delete draw');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while deleting the draw');
            });
        }
    }

    function forceClaim(winnerId) {
        if (confirm('Are you sure you want to force claim this prize?')) {
            showAlert('info', 'Processing claim...');
            
            fetch(`{{ url('admin/lottery/winners') }}/${winnerId}/force-claim`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || 'Prize claimed successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', data.message || 'Failed to claim prize');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while claiming the prize');
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

    // Auto-refresh every 30 seconds for live updates
    setInterval(() => {
        // Only auto-refresh if no filters are applied
        if (!window.location.search) {
            window.location.reload();
        }
    }, 30000);
</script>
@endpush
</x-layout>
