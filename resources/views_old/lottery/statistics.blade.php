<x-smart_layout>

@section('content')
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="page-title">📊 Lottery Statistics</h1>
                        <p class="text-muted">Comprehensive lottery system analytics and insights</p>
                    </div>
                    <div>
                        <a href="{{ route('lottery.index') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Buy Tickets
                        </a>
                        <a href="{{ route('lottery.results') }}" class="btn btn-info">
                            <i class="fe fe-award me-2"></i>View Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Overview Statistics -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $globalStats['total_draws'] ?? 0 }}</h3>
                                    <p class="text-muted mb-0">Total Draws</p>
                                </div>
                                <div class="avatar bg-primary">
                                    <i class="fe fe-calendar"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fe fe-trending-up me-1"></i>
                                    {{ $globalStats['completed_draws'] ?? 0 }} completed
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ number_format($globalStats['total_tickets'] ?? 0) }}</h3>
                                    <p class="text-muted mb-0">Tickets Sold</p>
                                </div>
                                <div class="avatar bg-success">
                                    <i class="fe fe-ticket"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="fe fe-users me-1"></i>
                                    {{ $globalStats['unique_players'] ?? 0 }} players
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">${{ number_format($globalStats['total_revenue'] ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total Revenue</p>
                                </div>
                                <div class="avatar bg-warning">
                                    <i class="fe fe-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fe fe-arrow-up me-1"></i>
                                    ${{ number_format($globalStats['avg_revenue_per_draw'] ?? 0, 2) }} avg/draw
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">${{ number_format($globalStats['total_prizes'] ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Prizes Awarded</p>
                                </div>
                                <div class="avatar bg-info">
                                    <i class="fe fe-gift"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fe fe-trophy me-1"></i>
                                    {{ $globalStats['total_winners'] ?? 0 }} winners
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Revenue Trend (Last 30 Days)
                            </h4>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Prize Distribution -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                Prize Distribution
                            </h4>
                        </div>
                        <div class="card-body">
                            <canvas id="prizeChart" height="300"></canvas>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-circle text-warning me-2"></i>1st Prize</span>
                                    <span>{{ $prizeDistribution['first_prize_percentage'] ?? 50 }}%</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-circle text-secondary me-2"></i>2nd Prize</span>
                                    <span>{{ $prizeDistribution['second_prize_percentage'] ?? 30 }}%</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-circle text-bronze me-2"></i>3rd Prize</span>
                                    <span>{{ $prizeDistribution['third_prize_percentage'] ?? 20 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Top Performers -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-star me-2"></i>
                                Top Performing Draws
                            </h4>
                        </div>
                        <div class="card-body">
                            @if(isset($topDraws) && $topDraws->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Draw #</th>
                                                <th>Date</th>
                                                <th>Tickets Sold</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topDraws as $draw)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('lottery.draw.details', $draw->id) }}" class="text-decoration-none">
                                                            #{{ $draw->id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $draw->draw_date->format('M d, Y') }}</td>
                                                    <td>{{ $draw->total_tickets_sold }}</td>
                                                    <td class="text-success fw-bold">${{ number_format($draw->total_tickets_sold * ($settings->ticket_price ?? 2), 2) }}</td>
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
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-crown me-2"></i>
                                Recent Big Winners
                            </h4>
                        </div>
                        <div class="card-body">
                            @if(isset($recentWinners) && $recentWinners->count() > 0)
                                @foreach($recentWinners as $winner)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar {{ $winner->position == 1 ? 'bg-warning' : ($winner->position == 2 ? 'bg-secondary' : 'bg-bronze') }} me-3">
                                            <i class="fas fa-{{ $winner->position == 1 ? 'trophy' : 'medal' }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $winner->user->firstname ?? 'Winner' }} {{ substr($winner->user->lastname ?? '', 0, 1) }}.</h6>
                                            <small class="text-muted">
                                                Draw #{{ $winner->lottery_draw_id }} - {{ $winner->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">${{ number_format($winner->prize_amount, 2) }}</div>
                                            <small class="text-muted">{{ $winner->position }}{{ $winner->position == 1 ? 'st' : ($winner->position == 2 ? 'nd' : 'rd') }} Prize</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No recent winners</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Detailed Analytics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <h5 class="mb-3">Participation Stats</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Average Tickets per Draw</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['avg_tickets_per_draw'] ?? 0, 1) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Average Players per Draw</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['avg_players_per_draw'] ?? 0, 1) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tickets per Player</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['avg_tickets_per_player'] ?? 0, 1) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Return Rate</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['return_rate'] ?? 0, 1) }}%</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="mb-3">Financial Metrics</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Revenue per Ticket</td>
                                        <td class="text-end fw-bold">${{ number_format($settings->ticket_price ?? 2, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Average Prize Pool</td>
                                        <td class="text-end fw-bold">${{ number_format($detailedStats['avg_prize_pool'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Admin Commission</td>
                                        <td class="text-end fw-bold">${{ number_format($detailedStats['total_commission'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Payout Ratio</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['payout_ratio'] ?? 0, 1) }}%</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="mb-3">Draw Statistics</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <td>Most Popular Draw Time</td>
                                        <td class="text-end fw-bold">{{ $detailedStats['popular_draw_time'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Fastest Sold Out</td>
                                        <td class="text-end fw-bold">{{ $detailedStats['fastest_sold_out'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Completion Rate</td>
                                        <td class="text-end fw-bold">{{ number_format($detailedStats['completion_rate'] ?? 0, 1) }}%</td>
                                    </tr>
                                    <tr>
                                        <td>Average Draw Duration</td>
                                        <td class="text-end fw-bold">{{ $detailedStats['avg_draw_duration'] ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @auth
                <!-- Your Personal Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-user me-2"></i>
                            Your Personal Statistics
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center border rounded p-3">
                                    <h4 class="text-primary">{{ $personalStats['total_tickets'] ?? 0 }}</h4>
                                    <small class="text-muted">Tickets Purchased</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center border rounded p-3">
                                    <h4 class="text-success">${{ number_format($personalStats['total_spent'] ?? 0, 2) }}</h4>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center border rounded p-3">
                                    <h4 class="text-warning">{{ $personalStats['total_wins'] ?? 0 }}</h4>
                                    <small class="text-muted">Total Wins</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center border rounded p-3">
                                    <h4 class="text-info">${{ number_format($personalStats['total_winnings'] ?? 0, 2) }}</h4>
                                    <small class="text-muted">Total Winnings</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Win Rate:</span>
                                    <span class="fw-bold">{{ number_format($personalStats['win_rate'] ?? 0, 1) }}%</span>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" style="width: {{ $personalStats['win_rate'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['revenue']['labels'] ?? []),
            datasets: [{
                label: 'Revenue',
                data: @json($chartData['revenue']['data'] ?? []),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Prize Distribution Chart
    const prizeCtx = document.getElementById('prizeChart').getContext('2d');
    new Chart(prizeCtx, {
        type: 'doughnut',
        data: {
            labels: ['1st Prize', '2nd Prize', '3rd Prize'],
            datasets: [{
                data: [
                    {{ $prizeDistribution['first_prize_percentage'] ?? 50 }},
                    {{ $prizeDistribution['second_prize_percentage'] ?? 30 }},
                    {{ $prizeDistribution['third_prize_percentage'] ?? 20 }}
                ],
                backgroundColor: [
                    '#ffc107',
                    '#6c757d',
                    '#cd7f32'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<style>
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.bg-bronze {
    background-color: #cd7f32;
}
.text-bronze {
    color: #cd7f32;
}
</style>
@endsection
</x-smart_layout>
