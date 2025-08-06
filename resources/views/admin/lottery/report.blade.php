<x-layout>
    @section('top_title', 'Lottery Reports')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Reports & Analytics')
            
            <!-- Quick Stats -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <div class="h4 text-primary mb-1">{{ number_format($stats['total_draws']) }}</div>
                                <p class="text-muted mb-0 small">Total Draws</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="h4 text-success mb-1">{{ number_format($stats['total_tickets']) }}</div>
                                <p class="text-muted mb-0 small">Tickets Sold</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="h4 text-warning mb-1">{{ number_format($stats['total_winners']) }}</div>
                                <p class="text-muted mb-0 small">Winners</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="h4 text-info mb-1">${{ number_format($stats['total_revenue'], 2) }}</div>
                                <p class="text-muted mb-0 small">Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-secondary">
                            <div class="card-body text-center">
                                <div class="h4 text-secondary mb-1">${{ number_format($stats['total_prizes'], 2) }}</div>
                                <p class="text-muted mb-0 small">Total Prizes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <div class="h4 text-danger mb-1">${{ number_format($stats['admin_commission'], 2) }}</div>
                                <p class="text-muted mb-0 small">Commission</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Filters -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-filter me-2"></i>Report Filters</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="exportReport()">
                                <i class="fe fe-download"></i> Export Report
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="printReport()">
                                <i class="fe fe-printer"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.lottery.report') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <select name="date_range" class="form-select" onchange="toggleCustomDates()">
                                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ request('date_range') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="last_week" {{ request('date_range') === 'last_week' ? 'selected' : '' }}>Last Week</option>
                                    <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('date_range') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="start_date_field" style="display: {{ request('date_range') === 'custom' ? 'block' : 'none' }}">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2" id="end_date_field" style="display: {{ request('date_range') === 'custom' ? 'block' : 'none' }}">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Report Type</label>
                                <select name="report_type" class="form-select">
                                    <option value="summary" {{ request('report_type') === 'summary' ? 'selected' : '' }}>Summary</option>
                                    <option value="detailed" {{ request('report_type') === 'detailed' ? 'selected' : '' }}>Detailed</option>
                                    <option value="financial" {{ request('report_type') === 'financial' ? 'selected' : '' }}>Financial</option>
                                    <option value="winners" {{ request('report_type') === 'winners' ? 'selected' : '' }}>Winners</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fe fe-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-trending-up me-2"></i>Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-pie-chart me-2"></i>Draw Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Financial Report -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-dollar-sign me-2"></i>Financial Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Total Ticket Sales:</strong></td>
                                        <td class="text-end"><span class="text-success">${{ number_format($financial['total_sales'], 2) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Prizes Distributed:</strong></td>
                                        <td class="text-end"><span class="text-warning">${{ number_format($financial['prizes_distributed'], 2) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pending Prize Distribution:</strong></td>
                                        <td class="text-end"><span class="text-info">${{ number_format($financial['prizes_pending'], 2) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Admin Commission:</strong></td>
                                        <td class="text-end"><span class="text-primary">${{ number_format($financial['admin_commission'], 2) }}</span></td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Net Profit:</strong></td>
                                        <td class="text-end"><span class="text-{{ $financial['net_profit'] >= 0 ? 'success' : 'danger' }}">${{ number_format($financial['net_profit'], 2) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Profit Margin:</strong></td>
                                        <td class="text-end"><span class="text-muted">{{ number_format($financial['profit_margin'], 2) }}%</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Draws -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-star me-2"></i>Top Performing Draws</h5>
                    </div>
                    <div class="card-body">
                        @if($topDraws && count($topDraws) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Draw</th>
                                            <th>Tickets</th>
                                            <th>Revenue</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topDraws as $draw)
                                            <tr>
                                                <td>
                                                    @if($draw->id)
                                                        <a href="{{ route('admin.lottery.draws.details', $draw->id) }}" class="text-decoration-none">
                                                            <strong>#{{ $draw->id }}</strong>
                                                        </a>
                                                    @else
                                                        <strong class="text-muted">#N/A</strong>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($draw->draw_date)->format('M d, Y') }}</small>
                                                </td>
                                                <td>{{ number_format($draw->tickets_sold) }}</td>
                                                <td>${{ number_format($draw->revenue, 2) }}</td>
                                                <td><span class="text-success">${{ number_format($draw->profit, 2) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">No draw data available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Winners -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fe fe-award me-2"></i>Recent Winners</h5>
                        <a href="{{ route('admin.lottery.winners') }}" class="btn btn-sm btn-outline-primary">
                            View All Winners
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentWinners && count($recentWinners) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>Draw</th>
                                            <th>Winner</th>
                                            <th>Ticket</th>
                                            <th>Prize</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentWinners as $winner)
                                            <tr>
                                                <td>
                                                    @if($winner->position === 1)
                                                        <span class="badge bg-warning">1st</span>
                                                    @elseif($winner->position === 2)
                                                        <span class="badge bg-secondary">2nd</span>
                                                    @elseif($winner->position === 3)
                                                        <span class="badge bg-info">3rd</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ $winner->position }}th</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($winner->draw_id)
                                                        <a href="{{ route('admin.lottery.draws.details', $winner->draw_id) }}" class="text-decoration-none">
                                                            #{{ $winner->draw_id }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">#N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $winner->user->name ?? 'Unknown' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $winner->user->email ?? 'No email' }}</small>
                                                </td>
                                                <td>
                                                    <code>{{ $winner->ticket_number }}</code>
                                                </td>
                                                <td>
                                                    <strong class="text-success">${{ number_format($winner->prize_amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $winner->prize_distributed ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $winner->prize_distributed ? 'Distributed' : 'Pending' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $winner->win_date ? \Carbon\Carbon::parse($winner->win_date)->format('M d, Y') : 'Unknown' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fe fe-award fs-1 text-muted"></i>
                                <h6 class="text-muted mt-3">No recent winners</h6>
                                <p class="text-muted">Winners will appear here after draws are completed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Activity -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fe fe-users me-2"></i>User Activity Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h3 text-primary">{{ number_format($userStats['total_users']) }}</div>
                                    <p class="text-muted mb-0">Total Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h3 text-success">{{ number_format($userStats['active_users']) }}</div>
                                    <p class="text-muted mb-0">Active Users</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h3 text-warning">{{ number_format($userStats['winners_count']) }}</div>
                                    <p class="text-muted mb-0">Total Winners</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h3 text-info">${{ number_format($userStats['avg_spending'], 2) }}</div>
                                    <p class="text-muted mb-0">Avg. User Spending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates() {
        const dateRange = document.querySelector('[name="date_range"]').value;
        const startDateField = document.getElementById('start_date_field');
        const endDateField = document.getElementById('end_date_field');
        
        if (dateRange === 'custom') {
            startDateField.style.display = 'block';
            endDateField.style.display = 'block';
        } else {
            startDateField.style.display = 'none';
            endDateField.style.display = 'none';
        }
    }

    function exportReport() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'true');
        window.location.href = '{{ route("admin.lottery.report") }}?' + params.toString();
    }

    function printReport() {
        window.print();
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['revenue']['labels'] ?? []),
            datasets: [{
                label: 'Revenue',
                data: @json($chartData['revenue']['data'] ?? []),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['status']['labels'] ?? []),
            datasets: [{
                data: @json($chartData['status']['data'] ?? []),
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Auto-refresh data every 5 minutes
    setInterval(function() {
        // Only refresh if we're on the current report page
        if (window.location.pathname.includes('report')) {
            window.location.reload();
        }
    }, 300000); // 5 minutes
</script>
@endpush
</x-layout>
