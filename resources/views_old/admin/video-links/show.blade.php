<x-layout>
    @section('top_title', 'Video Link Details')
    
    @section('content')
        <div class="row mb-4 my-4">
            @section('title', 'Video Link Details')
            
            <!-- Video Information Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">{{ $videoLink->title }}</h4>
                            <p class="text-muted mb-0">Video ID: #{{ $videoLink->id }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.video-links.edit', $videoLink->id) }}" class="btn btn-warning">
                                <i class="fe fe-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.video-links.index') }}" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Video URL</label>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control" value="{{ $videoLink->video_url }}" readonly>
                                        <a href="{{ $videoLink->video_url }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="fe fe-external-link"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary fs-6">{{ ucfirst($videoLink->category) }}</span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Source Platform</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info fs-6">{{ $videoLink->source_platform ?: 'Unknown' }}</span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Target Country</label>
                                    <p class="mb-0">{{ $videoLink->country ?: 'All Countries' }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <p class="mb-0">
                                        @switch($videoLink->status)
                                            @case('active')
                                                <span class="badge bg-success fs-6">Active</span>
                                                @break
                                            @case('inactive')
                                                <span class="badge bg-secondary fs-6">Inactive</span>
                                                @break
                                            @case('paused')
                                                <span class="badge bg-warning fs-6">Paused</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info fs-6">Completed</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark fs-6">{{ ucfirst($videoLink->status) }}</span>
                                        @endswitch
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Duration</label>
                                    <p class="mb-0">
                                        @if($videoLink->duration)
                                            {{ gmdate('i:s', $videoLink->duration) }} minutes
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Cost Per Click</label>
                                    <p class="mb-0">
                                        <span class="text-success fw-bold fs-5">${{ number_format($videoLink->cost_per_click, 4) }}</span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ads Type</label>
                                    <p class="mb-0">{{ $videoLink->ads_type ? ucfirst(str_replace('_', ' ', $videoLink->ads_type)) : 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created</label>
                                    <p class="mb-0">{{ $videoLink->created_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated</label>
                                    <p class="mb-0">{{ $videoLink->updated_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Video Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-primary-light rounded">
                                    <h3 class="text-primary mb-0">{{ number_format($analytics['total_views']) }}</h3>
                                    <small class="text-muted">Total Views</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-success-light rounded">
                                    <h3 class="text-success mb-0">{{ number_format($analytics['unique_viewers']) }}</h3>
                                    <small class="text-muted">Unique Viewers</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-warning-light rounded">
                                    <h3 class="text-warning mb-0">${{ number_format($analytics['total_earnings'], 2) }}</h3>
                                    <small class="text-muted">Total Earnings</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-info-light rounded">
                                    <h3 class="text-info mb-0">${{ number_format($analytics['avg_earning_per_view'], 4) }}</h3>
                                    <small class="text-muted">Avg Per View</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Preview -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Video Preview</h5>
                    </div>
                    <div class="card-body">
                        <div id="videoPreview">
                            <!-- Preview will be loaded by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Analytics Charts -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily Views Chart (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyViewsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Views -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Views</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Name & Email</th>
                                        <th>Viewed At</th>
                                        <th>Earned Amount</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($analytics['recent_views'] as $view)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $view->user->username ?? 'Unknown' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $view->user->firstname ?? 'Unknown' }} {{ $view->user->lastname ?? '' }}</h6>
                                                        <small class="text-muted">{{ $view->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $view->viewed_at->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $view->viewed_at->format('g:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">${{ number_format($view->earned_amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $view->ip_address }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($view->user_agent ?? 'Unknown', 50) }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fe fe-eye-off display-4 text-muted"></i>
                                                <p class="text-muted mb-0">No views yet</p>
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
    @endsection
</x-layout>
@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize video preview
        loadVideoPreview();
        
        // Initialize daily views chart
        initializeDailyViewsChart();
    });

    function loadVideoPreview() {
        const url = '{{ $videoLink->video_url }}';
        const previewDiv = document.getElementById('videoPreview');
        
        if (url) {
            let embedUrl = '';
            let platform = '';
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                platform = 'YouTube';
                let videoId = '';
                if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                } else if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1].split('&')[0];
                }
                if (videoId) {
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
            } else if (url.includes('vimeo.com')) {
                platform = 'Vimeo';
                const videoId = url.split('vimeo.com/')[1].split('?')[0];
                if (videoId) {
                    embedUrl = `https://player.vimeo.com/video/${videoId}`;
                }
            }
            
            if (embedUrl) {
                previewDiv.innerHTML = `
                    <div class="ratio ratio-16x9">
                        <iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>
                    </div>
                `;
            } else {
                previewDiv.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fe fe-video display-4 text-muted"></i>
                        <p class="text-muted mb-0">Video preview not available</p>
                        <a href="${url}" target="_blank" class="btn btn-primary btn-sm mt-2">
                            <i class="fe fe-external-link"></i> Open Video
                        </a>
                    </div>
                `;
            }
        }
    }

    function initializeDailyViewsChart() {
        const ctx = document.getElementById('dailyViewsChart').getContext('2d');
        
        // Prepare data from PHP
        const dailyViews = {!! json_encode($dailyViews) !!};
        const labels = dailyViews.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const viewsData = dailyViews.map(item => item.views);
        const earningsData = dailyViews.map(item => item.earnings);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Views',
                    data: viewsData,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'Earnings ($)',
                    data: earningsData,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Views'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Earnings ($)'
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
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    if (context.datasetIndex === 1) {
                                        label += '$' + context.parsed.y.toFixed(2);
                                    } else {
                                        label += context.parsed.y;
                                    }
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
