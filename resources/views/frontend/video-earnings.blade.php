<x-smart_layout>
    @section('title', $pageTitle)
    @section('content')
    <div class="row">
        <!-- Earnings Overview Cards -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-white mb-1">Total Earnings</p>
                            <h3 class="text-white mb-0">${{ number_format($totalEarnings, 4) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-white mb-1">Today's Earnings</p>
                            <h3 class="text-white mb-0">${{ number_format($todayEarnings, 4) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-white mb-1">Videos Watched</p>
                            <h3 class="text-white mb-0">{{ number_format($totalVideosWatched) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-video fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-white mb-1">This Month</p>
                            <h3 class="text-white mb-0">${{ number_format($thisMonthEarnings, 4) }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings by Video -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar"></i> Earnings by Video
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Video Title</th>
                                    <th>Views</th>
                                    <th>Total Earned</th>
                                    <th>Avg Per View</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($earningsByVideo as $earning)
                                <tr>
                                    <td>{{ $earning->videoLink->title ?? 'N/A' }}</td>
                                    <td>{{ $earning->view_count }}</td>
                                    <td>${{ number_format($earning->total_earned, 4) }}</td>
                                    <td>${{ number_format($earning->total_earned / $earning->view_count, 4) }}</td>
                                    <td>
                                        <a href="{{ route('gallery') }}#video-{{ $earning->video_link_id }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No earnings data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Earnings Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-line-chart"></i> Daily Earnings (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Earnings Chart
        const ctx = document.getElementById('earningsChart').getContext('2d');
        const earningsData = @json($dailyEarnings);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: earningsData.map(item => item.date),
                datasets: [{
                    label: 'Daily Earnings ($)',
                    data: earningsData.map(item => item.daily_total),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(4);
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-smart_layout>
