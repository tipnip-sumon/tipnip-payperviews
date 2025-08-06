<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4 my-4">
        <div class="col-lg-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2"><i class="fas fa-chart-line me-2"></i>{{ $pageTitle }}</h2>
                            <p class="mb-0 opacity-75">Comprehensive overview of your video viewing earnings and statistics</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="text-center">
                                <div class="h3 mb-1">${{ number_format($overallStats['total_earnings'], 4) }}</div>
                                <small class="opacity-75">Total Lifetime Earnings</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary h3 mb-2">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">{{ number_format($overallStats['total_videos_watched']) }}</div>
                    <small class="text-muted">Total Videos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success h3 mb-2">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">${{ number_format($overallStats['total_earnings'], 4) }}</div>
                    <small class="text-muted">Total Earnings</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info h3 mb-2">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">${{ number_format($overallStats['average_per_video'], 4) }}</div>
                    <small class="text-muted">Average per Video</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning h3 mb-2">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">${{ number_format($overallStats['highest_earning'], 4) }}</div>
                    <small class="text-muted">Highest Earning</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Time-based Statistics -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Time-based Performance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border-end pe-3">
                                <h6 class="text-muted mb-1">Today</h6>
                                <div class="h5 text-primary mb-0">{{ $timeStats['today']['videos'] }} videos</div>
                                <small class="text-success">${{ number_format($timeStats['today']['earnings'], 4) }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="ps-3">
                                <h6 class="text-muted mb-1">Yesterday</h6>
                                <div class="h5 text-info mb-0">{{ $timeStats['yesterday']['videos'] }} videos</div>
                                <small class="text-success">${{ number_format($timeStats['yesterday']['earnings'], 4) }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border-end pe-3">
                                <h6 class="text-muted mb-1">This Week</h6>
                                <div class="h5 text-warning mb-0">{{ $timeStats['this_week']['videos'] }} videos</div>
                                <small class="text-success">${{ number_format($timeStats['this_week']['earnings'], 4) }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ps-3">
                                <h6 class="text-muted mb-1">This Month</h6>
                                <div class="h5 text-danger mb-0">{{ $timeStats['this_month']['videos'] }} videos</div>
                                <small class="text-success">${{ number_format($timeStats['this_month']['earnings'], 4) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category-wise Earnings -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Category Performance</h5>
                </div>
                <div class="card-body">
                    @forelse($categoryEarnings as $category)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">{{ ucfirst($category['category']) }}</h6>
                                <small class="text-muted">{{ $category['video_count'] }} videos</small>
                            </div>
                            <div class="text-end">
                                <div class="h6 text-success mb-0">${{ number_format($category['total_earnings'], 4) }}</div>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: {{ ($category['total_earnings'] / $overallStats['total_earnings']) * 100 }}%"></div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No category data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Monthly Earnings Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Monthly Earnings Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyEarningsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Earnings Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Earnings (This Month)</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyEarningsChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Earning Videos -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Top Earning Videos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Videos Watched</th>
                                    <th>Total Earned</th>
                                    <th>Videos Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEarningDays as $day)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($day['date'])->format('l') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $day['total_videos'] }} videos</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">${{ number_format($day['total_earned'], 4) }}</span>
                                        </td>
                                        <td>
                                            @if(!empty($day['videos_watched']))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach(array_slice($day['videos_watched'], 0, 3) as $video)
                                                        <small class="badge bg-secondary">{{ $video['video_title'] ?? 'Video ' . $video['video_id'] }}</small>
                                                    @endforeach
                                                    @if(count($day['videos_watched']) > 3)
                                                        <small class="text-muted">+{{ count($day['videos_watched']) - 3 }} more</small>
                                                    @endif
                                                </div>
                                            @else
                                                <small class="text-muted">No details available</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No earnings data available. Start watching videos to see your earnings here!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap justify-content-center">
                <a href="{{ route('user.video-views.index') }}" class="btn btn-primary">
                    <i class="fas fa-video me-1"></i> Watch More Videos
                </a>
                <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history me-1"></i> View History
                </a>
                <button class="btn btn-outline-success" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Monthly Earnings Chart
    const monthlyCtx = document.getElementById('monthlyEarningsChart').getContext('2d');
    const monthlyData = @json($monthlyEarnings);
    
    const monthlyLabels = monthlyData.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    const monthlyEarnings = monthlyData.map(item => parseFloat(item.total_earnings));
    const monthlyVideos = monthlyData.map(item => parseInt(item.total_videos));

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Earnings ($)',
                data: monthlyEarnings,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                yAxisID: 'y'
            }, {
                label: 'Videos Watched',
                data: monthlyVideos,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Earnings ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Videos Count'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Daily Earnings Chart
    const dailyCtx = document.getElementById('dailyEarningsChart').getContext('2d');
    const dailyData = @json($dailyEarnings);
    
    const dailyLabels = dailyData.map(item => item.day);
    const dailyEarningsData = dailyData.map(item => parseFloat(item.total_earnings));

    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Daily Earnings',
                data: dailyEarningsData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Earnings ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of Month'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

@media print {
    .btn, .card-header {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }
}

.progress {
    background-color: rgba(0,0,0,0.1);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
</x-smart_layout>
