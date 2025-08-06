<x-smart_layout>
    @section('top_title', $pageTitle ?? 'Daily Video Report')
    @section('title', $pageTitle ?? 'Daily Video Report')
    @section('content')
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-calendar-day"></i> Daily Video Report
                                </h4>
                                <p class="mb-0">Your video watching activity and earnings for today</p>
                                <small class="text-light">{{ now()->format('l, F j, Y') }}</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <h5 class="mb-1">
                                        <i class="fas fa-coins"></i> Today's Earnings
                                    </h5>
                                    <h3 class="mb-0">${{ number_format($todayEarnings ?? 0, 4) }}</h3>
                                    <small class="text-light">{{ $todayVideoCount ?? 0 }} videos watched</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Videos Watched</p>
                                <h3 class="text-white mb-0">{{ $todayVideoCount ?? 0 }}</h3>
                                <small class="text-light">Today's total</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-play-circle fa-2x opacity-75"></i>
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
                                <p class="text-white mb-1">Today's Earnings</p>
                                <h3 class="text-white mb-0">${{ number_format($todayEarnings ?? 0, 4) }}</h3>
                                <small class="text-light">Total earned today</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
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
                                <p class="text-white mb-1">Average Per Video</p>
                                <h3 class="text-white mb-0">
                                    @php
                                        $todayVideoCount = $todayVideoCount ?? 0;
                                        $todayEarnings = $todayEarnings ?? 0;
                                        $avgPerVideo = $todayVideoCount > 0 ? $todayEarnings / $todayVideoCount : 0;
                                    @endphp
                                    ${{ number_format($avgPerVideo, 4) }}
                                </h3>
                                <small class="text-light">Per video today</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Weekly Average</p>
                                <h3 class="text-white mb-0">${{ number_format($averageDailyEarnings ?? 0, 4) }}</h3>
                                <small class="text-light">7-day average</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-week fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Comparison -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-primary"></i> 7-Day Performance Comparison
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="weeklyChart" height="100"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Earnings</th>
                                                <th>Videos</th>
                                                <th>Trend</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($weeklyData) && count($weeklyData) > 0)
                                                @foreach($weeklyData as $index => $day)
                                                    <tr class="{{ isset($day['date']) && $day['date'] == today()->toDateString() ? 'table-primary' : '' }}">
                                                        <td>
                                                            <strong>{{ $day['day_name'] ?? 'N/A' }}</strong>
                                                            @if(isset($day['date']) && $day['date'] == today()->toDateString())
                                                                <span class="badge bg-primary ms-1">Today</span>
                                                            @endif
                                                        </td>
                                                        <td>${{ number_format($day['earnings'] ?? 0, 4) }}</td>
                                                        <td>{{ $day['videos'] ?? 0 }}</td>
                                                        <td>
                                                            @if($index > 0 && isset($weeklyData[$index - 1]))
                                                                @php
                                                                    $previousEarnings = $weeklyData[$index - 1]['earnings'] ?? 0;
                                                                    $currentEarnings = $day['earnings'] ?? 0;
                                                                    $trend = $currentEarnings - $previousEarnings;
                                                                @endphp
                                                                @if($trend > 0)
                                                                    <i class="fas fa-arrow-up text-success"></i>
                                                                    <small class="text-success">+${{ number_format($trend, 4) }}</small>
                                                                @elseif($trend < 0)
                                                                    <i class="fas fa-arrow-down text-danger"></i>
                                                                    <small class="text-danger">${{ number_format($trend, 4) }}</small>
                                                                @else
                                                                    <i class="fas fa-minus text-muted"></i>
                                                                    <small class="text-muted">No change</small>
                                                                @endif
                                                            @else
                                                                <small class="text-muted">-</small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Video Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock text-info"></i> Today's Video Activity
                            </h5>
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm" onclick="refreshActivity()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                                <a href="{{ route('gallery') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-play"></i> Watch More Videos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($todayViews) && is_countable($todayViews) && count($todayViews) > 0)
                            <div class="timeline">
                                @foreach($todayViews as $view)
                                    <div class="timeline-item mb-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                <div class="timeline-badge bg-primary">
                                                    <i class="fas fa-play text-white"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    @if(isset($view->videoLink) && $view->videoLink && isset($view->videoLink->thumbnail) && $view->videoLink->thumbnail)
                                                        <img src="{{ $view->videoLink->thumbnail }}" 
                                                             alt="Video Thumbnail" 
                                                             class="rounded me-2" 
                                                             style="width: 50px; height: 35px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-2"
                                                             style="width: 50px; height: 35px;">
                                                            <i class="fas fa-video text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $view->videoLink->title ?? 'Video Not Available' }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ isset($view->viewed_at) && $view->viewed_at ? $view->viewed_at->format('h:i A') : 'N/A' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-dollar-sign"></i> {{ number_format($view->earned_amount ?? 0, 4) }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                @if(isset($view->videoLink) && $view->videoLink && isset($view->videoLink->description) && $view->videoLink->description)
                                                    <small class="text-muted">
                                                        {{ \Illuminate\Support\Str::limit($view->videoLink->description, 60) }}
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-2">
                                                <small class="text-muted">
                                                    {{ isset($view->viewed_at) && $view->viewed_at ? $view->viewed_at->diffForHumans() : 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="col-md-1">
                                                @if(isset($view->videoLink) && $view->videoLink && isset($view->videoLink->id))
                                                    <a href="{{ route('gallery') }}#video-{{ $view->videoLink->id }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="View Video">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- No Activity Today -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Videos Watched Today</h4>
                                <p class="text-muted mb-4">
                                    You haven't watched any videos today. Start watching to earn money!
                                </p>
                                <a href="{{ route('gallery') }}" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Start Watching Videos
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Goals and Achievements -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-target"></i> Daily Goals
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $dailyGoalVideos = 10;
                            $dailyGoalEarnings = 0.05;
                            $videoCount = $todayVideoCount ?? 0;
                            $earnings = $todayEarnings ?? 0;
                            $videoProgress = $dailyGoalVideos > 0 ? min(($videoCount / $dailyGoalVideos) * 100, 100) : 0;
                            $earningsProgress = $dailyGoalEarnings > 0 ? min(($earnings / $dailyGoalEarnings) * 100, 100) : 0;
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small>Video Goal: {{ $videoCount }}/{{ $dailyGoalVideos }}</small>
                                <small>{{ number_format($videoProgress, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $videoProgress }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <small>Earnings Goal: ${{ number_format($earnings, 4) }}/${{ number_format($dailyGoalEarnings, 2) }}</small>
                                <small>{{ number_format($earningsProgress, 0) }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $earningsProgress }}%"></div>
                            </div>
                        </div>
                        
                        @if($videoProgress >= 100 && $earningsProgress >= 100)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-trophy"></i> Congratulations! You've achieved your daily goals!
                            </div>
                        @elseif($videoProgress >= 100 || $earningsProgress >= 100)
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-star"></i> Great progress! You're almost there!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-trophy"></i> Quick Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h5 class="text-primary mb-0">
                                    @if(auth()->check() && auth()->user()->videoViews())
                                        {{ auth()->user()->videoViews()->count() }}
                                    @else
                                        0
                                    @endif
                                </h5>
                                <small class="text-muted">Total Videos</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success mb-0">
                                    @if(auth()->check() && auth()->user()->videoViews())
                                        ${{ number_format(auth()->user()->videoViews()->sum('earned_amount'), 2) }}
                                    @else
                                        $0.00
                                    @endif
                                </h5>
                                <small class="text-muted">Total Earned</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h6 class="text-info mb-0">
                                    @if(auth()->check() && auth()->user()->videoViews())
                                        {{ auth()->user()->videoViews()->whereDate('created_at', '>=', now()->startOfWeek())->count() }}
                                    @else
                                        0
                                    @endif
                                </h6>
                                <small class="text-muted">This Week</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-warning mb-0">
                                    @if(auth()->check() && auth()->user()->videoViews())
                                        {{ auth()->user()->videoViews()->whereDate('created_at', '>=', now()->startOfMonth())->count() }}
                                    @else
                                        0
                                    @endif
                                </h6>
                                <small class="text-muted">This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
    <style>
        .timeline {
            position: relative;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 0;
        }
        
        .timeline-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        .progress {
            border-radius: 10px;
        }
        
        .card-header.bg-success,
        .card-header.bg-info {
            border-radius: 0.375rem 0.375rem 0 0;
        }
        
        .table-primary {
            --bs-table-bg: #cff4fc;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 30px;
            width: 2px;
            height: calc(100% - 10px);
            background-color: #dee2e6;
            transform: translateX(-50%);
        }
        
        @media (max-width: 768px) {
            .timeline-item::after {
                display: none;
            }
        }
    </style>
    @endpush

    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Weekly Chart
            const ctx = document.getElementById('weeklyChart');
            if (ctx) {
                const weeklyData = @json($weeklyData ?? []);
                
                if (weeklyData && weeklyData.length > 0) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: weeklyData.map(item => item.day_name || 'N/A'),
                            datasets: [{
                                label: 'Daily Earnings ($)',
                                data: weeklyData.map(item => parseFloat(item.earnings || 0)),
                                borderColor: 'rgb(54, 162, 235)',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(54, 162, 235)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Earned: $' + parseFloat(context.parsed.y).toFixed(4);
                                        }
                                    }
                                }
                            },
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
                } else {
                    // Show no data message
                    ctx.getContext('2d').fillText('No data available', 10, 50);
                }
            }
        });

        function refreshActivity() {
            window.location.reload();
        }

        // Auto-refresh every 5 minutes
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 300000); // 5 minutes
    </script>
    @endpush
</x-smart_layout>
