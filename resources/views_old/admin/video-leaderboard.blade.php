<x-layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    @section('content')
        <!-- Page Header -->
        <div class="row mt-4 mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-trophy"></i> Video Leaderboard
                                </h4>
                                <p class="mb-0">See top video watchers and most popular videos</p>
                            </div>
                            <div class="col-md-4 text-end">
                                @auth
                                    <div class="d-flex flex-column align-items-end">
                                        <h5 class="mb-1">
                                            <i class="fas fa-medal"></i> Your Rank: #{{ $userRank ?? 'N/A' }}
                                        </h5>
                                        <small class="text-light">
                                            ${{ number_format(auth()->user()->total_video_earnings ?? 0, 4) }} earned
                                        </small>
                                    </div>
                                @else
                                    <div>
                                        <h5 class="mb-1">Join the Competition!</h5>
                                        <a href="" class="btn btn-light btn-sm">
                                            <i class="fas fa-sign-in-alt"></i> Login to Compete
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="leaderboardTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="top-earners-tab" data-bs-toggle="tab" 
                                        data-bs-target="#top-earners" type="button" role="tab">
                                    <i class="fas fa-users"></i> Top Earners
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="top-videos-tab" data-bs-toggle="tab" 
                                        data-bs-target="#top-videos" type="button" role="tab">
                                    <i class="fas fa-video"></i> Popular Videos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="recent-activity-tab" data-bs-toggle="tab" 
                                        data-bs-target="#recent-activity" type="button" role="tab">
                                    <i class="fas fa-clock"></i> Recent Activity
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="leaderboardTabsContent">
                            <!-- Top Earners Tab -->
                            <div class="tab-pane fade show active" id="top-earners" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="card-title">
                                            <i class="fas fa-crown text-warning"></i> Top Video Earners
                                        </h5>
                                        <p class="text-muted">Users who earned the most money from watching videos</p>
                                    </div>
                                </div>

                                @if($topEarners->count() > 0)
                                    <!-- Top 3 Podium -->
                                    <div class="row mb-4">
                                        @foreach($topEarners->take(3) as $index => $earner)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-0 shadow-sm h-100 
                                                    @if($index == 0) bg-warning text-white
                                                    @elseif($index == 1) bg-light
                                                    @else bg-secondary text-white
                                                    @endif">
                                                    <div class="card-body text-center">
                                                        <div class="position-relative mb-3">
                                                            <img src="{{ $earner->avatar ?? asset('assets/images/users/16.jpg') }}" 
                                                                 alt="User Avatar" 
                                                                 class="rounded-circle" 
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                            <div class="position-absolute top-0 end-0">
                                                                @if($index == 0)
                                                                    <i class="fas fa-crown fa-2x text-yellow"></i>
                                                                @elseif($index == 1)
                                                                    <i class="fas fa-medal fa-2x text-secondary"></i>
                                                                @else
                                                                    <i class="fas fa-award fa-2x text-bronze"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <h5 class="card-title mb-1">
                                                            {{ $earner->firstname }} {{ $earner->lastname }}
                                                        </h5>
                                                        <p class="card-text">
                                                            <small class="@if($index == 0) text-light @else text-muted @endif">
                                                                {{ $earner->username }}
                                                            </small>
                                                        </p>
                                                        <div class="mb-2">
                                                            <h4 class="@if($index == 0) text-white @else text-success @endif">
                                                                ${{ number_format($earner->video_views_sum_earned_amount ?? 0, 4) }}
                                                            </h4>
                                                            <small class="@if($index == 0) text-light @else text-muted @endif">
                                                                {{ $earner->video_views_count ?? 0 }} videos watched
                                                            </small>
                                                        </div>
                                                        <div class="badge 
                                                            @if($index == 0) badge-light text-warning
                                                            @elseif($index == 1) badge-secondary
                                                            @else badge-dark
                                                            @endif">
                                                            Rank #{{ $index + 1 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Remaining Top Earners -->
                                    @if($topEarners->count() > 3)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Rank</th>
                                                        <th>User</th>
                                                        <th>Videos Watched</th>
                                                        <th>Total Earnings</th>
                                                        <th>Average Per Video</th>
                                                        <th>Join Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($topEarners->slice(3) as $index => $earner)
                                                        <tr class="@if(auth()->check() && auth()->id() == $earner->id) table-warning @endif">
                                                            <td>
                                                                <span class="badge bg-primary">
                                                                    #{{ $index + 4 }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img src="{{ $earner->avatar ?? asset('assets/images/users/16.jpg') }}" 
                                                                         alt="Avatar" 
                                                                         class="rounded-circle me-2" 
                                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                                    <div>
                                                                        <div class="fw-bold">
                                                                            {{ $earner->firstname }} {{ $earner->lastname }}
                                                                            @if(auth()->check() && auth()->id() == $earner->id)
                                                                                <span class="badge bg-warning ms-1">You</span>
                                                                            @endif
                                                                        </div>
                                                                        <small class="text-muted">{{ $earner->username }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">
                                                                    {{ $earner->video_views_count ?? 0 }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="text-success fw-bold">
                                                                    ${{ number_format($earner->video_views_sum_earned_amount ?? 0, 4) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    ${{ $earner->video_views_count > 0 ? number_format(($earner->video_views_sum_earned_amount ?? 0) / $earner->video_views_count, 4) : '0.0000' }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">
                                                                    {{ showDateTime($earner->created_at) }}
                                                                </small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Earners Yet</h4>
                                        <p class="text-muted">Be the first to start earning from videos!</p>
                                        <a href="{{ route('gallery') }}" class="btn btn-primary">
                                            <i class="fas fa-play"></i> Start Watching Videos
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Top Videos Tab -->
                            <div class="tab-pane fade" id="top-videos" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="card-title">
                                            <i class="fas fa-fire text-danger"></i> Most Popular Videos
                                        </h5>
                                        <p class="text-muted">Videos with the most views and highest earnings</p>
                                    </div>
                                </div>

                                @if($topVideos->count() > 0)
                                    <div class="row">
                                        @foreach($topVideos as $index => $video)
                                            <div class="col-lg-6 col-md-12 mb-4">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <div class="row g-0 h-100">
                                                        <div class="col-md-4">
                                                            <div class="position-relative">
                                                                <img src="{{ $video->thumbnail }}" 
                                                                     alt="Video Thumbnail" 
                                                                     class="img-fluid rounded-start h-100" 
                                                                     style="object-fit: cover; min-height: 150px;">
                                                                <div class="position-absolute top-0 start-0 m-2">
                                                                    <span class="badge 
                                                                        @if($index == 0) bg-warning
                                                                        @elseif($index == 1) bg-secondary  
                                                                        @elseif($index == 2) bg-info
                                                                        @else bg-primary
                                                                        @endif">
                                                                        #{{ $index + 1 }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="card-body">
                                                                <h6 class="card-title">{{ $video->title }}</h6>
                                                                <p class="card-text">
                                                                    <small class="text-muted">
                                                                        {{ Str::limit($video->description, 100) }}
                                                                    </small>
                                                                </p>
                                                                <div class="row text-center">
                                                                    <div class="col-6">
                                                                        <div class="border-end">
                                                                            <h6 class="text-primary mb-0">
                                                                                {{ number_format($video->clicks_count) }}
                                                                            </h6>
                                                                            <small class="text-muted">Views</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <h6 class="text-success mb-0">
                                                                            ${{ number_format($video->cost_per_click, 2) }}
                                                                        </h6>
                                                                        <small class="text-muted">Earnings</small>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="badge bg-success">
                                                                            ${{ number_format($video->earning_per_view, 4) }}/view
                                                                        </span>
                                                                        <a href="{{ route('gallery') }}#video-{{ $video->id }}" 
                                                                           class="btn btn-sm btn-outline-primary">
                                                                            <i class="fas fa-play"></i> Watch
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-video fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">No Videos Available</h4>
                                        <p class="text-muted">Check back later for popular videos!</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Recent Activity Tab -->
                            <div class="tab-pane fade" id="recent-activity" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="card-title">
                                            <i class="fas fa-clock text-info"></i> Recent Video Activity
                                        </h5>
                                        <p class="text-muted">Latest video views and earnings</p>
                                    </div>
                                </div>

                                <div id="recent-activity-content">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading recent activity...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-white mb-1">Total Participants</p>
                                <h3 class="text-white mb-0">{{ $topEarners->count() }}</h3>
                                <small class="text-light">Active video watchers</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-2x opacity-75"></i>
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
                                <p class="text-white mb-1">Total Videos</p>
                                <h3 class="text-white mb-0">{{ $topVideos->count() }}</h3>
                                <small class="text-light">Available videos</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-video fa-2x opacity-75"></i>
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
                                <p class="text-white mb-1">Total Views</p>
                                <h3 class="text-white mb-0">{{ number_format($topVideos->sum('views_count')) }}</h3>
                                <small class="text-light">All time views</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-eye fa-2x opacity-75"></i>
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
                                <p class="text-white mb-1">Total Paid</p>
                                <h3 class="text-white mb-0">${{ number_format($topVideos->sum('total_earnings'), 2) }}</h3>
                                <small class="text-light">Earnings distributed</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
    <style>
        .text-yellow {
            color: #ffc107 !important;
        }
        .text-bronze {
            color: #cd7f32 !important;
        }
        .badge-gold {
            background-color: #ffd700 !important;
            color: #000 !important;
        }
        .table-warning {
            --bs-table-bg: #fff3cd;
        }
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
    </style>
    @endpush

    @push('script')
    <script>
        $(document).ready(function() {
            // Load recent activity when tab is clicked
            $('#recent-activity-tab').on('click', function() {
                loadRecentActivity();
            });

            // Auto-refresh recent activity every 30 seconds
            setInterval(function() {
                if ($('#recent-activity-tab').hasClass('active')) {
                    loadRecentActivity();
                }
            }, 30000);
        });

        function loadRecentActivity() {
            $.ajax({
                url: '{{ route("video.recent-activity") }}',
                method: 'GET',
                success: function(response) {
                    $('#recent-activity-content').html(response);
                },
                error: function() {
                    $('#recent-activity-content').html(
                        '<div class="text-center py-5">' +
                        '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                        '<h5>Failed to load recent activity</h5>' +
                        '<button class="btn btn-primary" onclick="loadRecentActivity()">Retry</button>' +
                        '</div>'
                    );
                }
            });
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    @endpush
</x-layout>
