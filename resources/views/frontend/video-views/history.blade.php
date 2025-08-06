<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @section('content')
        <!-- Statistics Cards -->
        <div class="row mb-4 my-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Total Videos Watched</h6>
                                <h3 class="mb-0 fw-bold" style="color: #0d6efd;">{{ $stats['total_videos_watched'] }}</h3>
                            </div>
                            <div class="bg-primary-light rounded-circle p-3">
                                <i class="fas fa-play-circle fa-lg" style="color: #0d6efd;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Total Earnings</h6>
                                <h3 class="mb-0 fw-bold" style="color: #198754;">${{ number_format($stats['total_earnings'], 4) }}</h3>
                            </div>
                            <div class="bg-success-light rounded-circle p-3">
                                <i class="fas fa-dollar-sign fa-lg" style="color: #198754;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Today's Videos</h6>
                                <h3 class="mb-0 fw-bold" style="color: #0dcaf0;">{{ $stats['today_videos'] }}</h3>
                                <small class="text-muted">${{ number_format($stats['today_earnings'], 4) }} earned</small>
                            </div>
                            <div class="bg-info-light rounded-circle p-3">
                                <i class="fas fa-calendar-day fa-lg" style="color: #0dcaf0;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">This Week</h6>
                                <h3 class="mb-0 fw-bold" style="color: #6f42c1;">{{ $stats['this_week_videos'] }}</h3>
                                <small class="text-muted">${{ number_format($stats['this_week_earnings'], 4) }} earned</small>
                            </div>
                            <div class="bg-purple-light rounded-circle p-3">
                                <i class="fas fa-calendar-week fa-lg" style="color: #6f42c1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Optimization Info Banner (Hidden for production) --}}
        {{-- 
        @if(isset($optimization_info))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success border-success bg-light">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-rocket fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Video History System Optimized
                            </h6>
                            <p class="mb-0 small text-muted">
                                ⚡ {{ $optimization_info['system_version'] }} • 📊 {{ $optimization_info['efficiency_gain'] }} database efficiency • 🚀 {{ $optimization_info['performance_boost'] }} loading
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">{{ $optimization_info['system_version'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        --}}

        <!-- Filters and Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0" style="color: #495057;">Filter History</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-play me-1"></i>Watch Videos
                                </a>
                                <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-chart-bar me-1"></i>Earnings Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('user.video-views.history') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ $filters['date_from'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ $filters['date_to'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        @if($dailyEarnings->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="color: #495057;">Daily Earnings (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="earningsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Video History Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0" style="color: #495057;">Video Viewing History</h5>
                    </div>
                    <div class="card-body">
                        @if($videoHistory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="color: #495057;">Date</th>
                                            <th style="color: #495057;">Videos Watched</th>
                                            <th style="color: #495057;">Total Earned</th>
                                            <th style="color: #495057;">Average per Video</th>
                                            <th style="color: #495057;">Video Details</th>
                                            <th style="color: #495057;">Device Info</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($videoHistory as $history)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <i class="fas fa-calendar-day fa-lg text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $history->view_date->format('M d, Y') }}</h6>
                                                        <small class="text-muted">{{ $history->view_date->format('l') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6 px-3 py-2">
                                                    {{ $history->total_videos ?? 0 }} videos
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold fs-5" style="color: #198754;">
                                                    ${{ number_format($history->total_earned ?? 0, 4) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $avgPerVideo = ($history->total_videos && $history->total_earned) 
                                                        ? $history->total_earned / $history->total_videos 
                                                        : 0;
                                                @endphp
                                                <span class="text-muted">
                                                    ${{ number_format($avgPerVideo, 4) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $videoData = json_decode($history->video_data ?? '[]', true) ?: [];
                                                @endphp
                                                @if(count($videoData) > 0)
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#videoDetails{{ $history->id }}" 
                                                            aria-expanded="false">
                                                        <i class="fas fa-eye me-1"></i>View Details ({{ count($videoData) }})
                                                    </button>
                                                @else
                                                    <span class="text-muted">No details</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    @if($history->device_info)
                                                        @php
                                                            $deviceInfo = Str::limit($history->device_info, 30);
                                                        @endphp
                                                        {{ $deviceInfo }}
                                                    @else
                                                        -
                                                    @endif
                                                </small>
                                                <br>
                                                <code style="font-size: 0.75rem;">{{ $history->ip_address }}</code>
                                            </td>
                                        </tr>
                                        @if(count($videoData) > 0)
                                        <tr>
                                            <td colspan="6" class="p-0">
                                                <div class="collapse" id="videoDetails{{ $history->id }}">
                                                    <div class="card card-body bg-light m-2">
                                                        <h6 class="text-muted mb-3">Videos watched on {{ $history->view_date->format('M d, Y') }}:</h6>
                                                        <div class="row">
                                                            @foreach($videoData as $videoDetail)
                                                            <div class="col-md-6 col-lg-4 mb-2">
                                                                <div class="card card-body bg-white p-2">
                                                                    <small class="text-primary fw-bold">{{ $videoDetail['video_title'] ?? 'Unknown Video' }}</small>
                                                                    <small class="text-success">Earned: ${{ number_format($videoDetail['earned_amount'] ?? 0, 4) }}</small>
                                                                    <small class="text-muted">Category: {{ $videoDetail['category'] ?? 'General' }}</small>
                                                                    <small class="text-muted">Time: {{ \Carbon\Carbon::parse($videoDetail['watched_at'] ?? now())->format('h:i A') }}</small>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $videoHistory->withQueryString()->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Video History Found</h5>
                                <p class="text-muted">You haven't watched any videos yet. Start watching videos to earn money!</p>
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-primary">
                                    <i class="fas fa-play me-1"></i>Watch Videos Now
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
        <style>
            .bg-primary-light {
                background-color: rgba(13, 110, 253, 0.1);
            }
            
            .bg-success-light {
                background-color: rgba(25, 135, 84, 0.1);
            }
            
            .bg-info-light {
                background-color: rgba(13, 202, 240, 0.1);
            }
            
            .bg-purple-light {
                background-color: rgba(111, 66, 193, 0.1);
            }
            
            .card {
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }
            
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            }
            
            .table-hover tbody tr:hover {
                background-color: rgba(0,0,0,0.025);
            }
            
            .badge {
                font-size: 0.75em;
                padding: 0.35em 0.65em;
                border-radius: 0.375rem;
            }
            
            .fw-bold {
                font-weight: 700 !important;
            }
            
            .text-muted {
                color: #6c757d !important;
            }
            
            .gap-2 {
                gap: 0.5rem !important;
            }
            
            .me-1 {
                margin-right: 0.25rem !important;
            }
            
            .me-2 {
                margin-right: 0.5rem !important;
            }
            
            .me-3 {
                margin-right: 1rem !important;
            }
            
            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem;
                }
                
                .table-responsive {
                    font-size: 0.875rem;
                }
                
                .btn-sm {
                    font-size: 0.8rem;
                    padding: 0.2rem 0.4rem;
                }
            }
        </style>
    @endpush

    @push('script')
        @if($dailyEarnings->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('earningsChart').getContext('2d');
                
                const chartData = {
                    labels: [
                        @foreach($dailyEarnings as $earning)
                            '{{ \Carbon\Carbon::parse($earning->date)->format("M d") }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Daily Earnings ($)',
                        data: [
                            @foreach($dailyEarnings as $earning)
                                {{ $earning->total_earnings }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Videos Watched',
                        data: [
                            @foreach($dailyEarnings as $earning)
                                {{ $earning->total_views }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                };

                const config = {
                    type: 'line',
                    data: chartData,
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
                                    text: 'Videos Watched'
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.datasetIndex === 0) {
                                            return 'Earnings: $' + context.parsed.y.toFixed(4);
                                        } else {
                                            return 'Videos: ' + context.parsed.y;
                                        }
                                    }
                                }
                            }
                        }
                    }
                };

                new Chart(ctx, config);
            });
        </script>
        @endif
    @endpush
</x-smart_layout>
