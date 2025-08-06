<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4 my-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2"><i class="fas fa-play-circle me-2"></i>{{ $pageTitle }}</h2>
                            <p class="mb-0 opacity-75">Watch videos and earn money instantly. Complete videos to earn rewards!</p>
                            @if($hasActiveInvestment && isset($userStats))
                                <small class="opacity-75">
                                    <i class="fas fa-star"></i> Plan: {{ $userStats['plan_name'] }} | 
                                    <i class="fas fa-coins"></i> Earning Rate: ${{ number_format($userStats['earning_rate'], 4) }} per video |
                                    <i class="fas fa-calendar-day"></i> Daily Limit: {{ $userStats['daily_limit'] }} videos
                                </small>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="text-center">
                                <div class="h3 mb-1">${{ number_format($userStats['total_earnings'], 4) }}</div>
                                <small class="opacity-75">Total Earnings</small>
                                @if($hasActiveInvestment && isset($userStats))
                                    <div class="mt-1">
                                        <small class="opacity-75">
                                            Today: {{ $userStats['todays_views'] }}/{{ $userStats['daily_limit'] }} videos
                                            @if($userStats['remaining_views'] > 0)
                                                <span class="badge bg-light text-dark ms-1">{{ $userStats['remaining_views'] }} left</span>
                                            @else
                                                <span class="badge bg-warning text-dark ms-1">Limit reached</span>
                                            @endif
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary h3 mb-2">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">{{ $userStats['total_videos_watched'] }}</div>
                    <small class="text-muted">Videos Watched</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success h3 mb-2">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">${{ number_format($userStats['total_earnings'], 4) }}</div>
                    <small class="text-muted">Total Earned</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info h3 mb-2">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">{{ $userStats['today_videos'] }}</div>
                    <small class="text-muted">Today's Videos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning h3 mb-2">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="h4 mb-1 text-dark">${{ number_format($userStats['today_earnings'], 4) }}</div>
                    <small class="text-muted">Today's Earnings</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history me-1"></i> View History
                </a>
                <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success">
                    <i class="fas fa-chart-line me-1"></i> Earnings Report
                </a>
            </div>
        </div>
    </div>

    <!-- Videos Grid -->
    <div class="row" id="videos-container">
        @forelse($videos as $video)
            <div class="col-lg-4 col-md-6 mb-4 video-item" data-video-id="{{ $video->id }}">
                <div class="card h-100 border-0 shadow-sm video-card hover-shadow">
                    <div class="position-relative">
                        <!-- Video Thumbnail -->
                        <div class="video-thumbnail" style="height: 200px; position: relative; overflow: hidden; border-radius: 0.5rem 0.5rem 0 0;">
                            @if(str_contains($video->video_url, 'youtube.com') || str_contains($video->video_url, 'youtu.be'))
                                @php
                                    $videoId = '';
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                @if($videoId)
                                    <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg" 
                                         class="video-thumb-img" 
                                         style="width: 100%; height: 100%; object-fit: cover;" 
                                         alt="{{ $video->title }}">
                                    <div class="play-overlay">
                                        <i class="fas fa-play-circle fa-3x text-white"></i>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                        <i class="fas fa-play-circle fa-3x text-primary"></i>
                                    </div>
                                @endif
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="fas fa-play-circle fa-3x text-primary"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Status & Earning Badge -->
                        @if(in_array($video->id, $watchedVideoIds))
                            <span class="position-absolute top-0 start-0 badge bg-success m-2">
                                <i class="fas fa-check me-1"></i>Watched
                            </span>
                        @endif
                        <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                            <i class="fas fa-dollar-sign me-1"></i>${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h6 class="card-title fw-bold text-dark">{{ $video->title ?: 'Video ' . $video->id }}</h6>
                        
                        @if($video->description)
                            <p class="card-text small text-muted mb-2">
                                {{ Str::limit($video->description, 80) }}
                            </p>
                        @endif
                        
                        <div class="video-meta mb-3">
                            <div class="d-flex justify-content-between align-items-center text-sm">
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ number_format($video->views_count) }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ gmdate('i:s', $video->duration ?? 120) }}
                                </small>
                            </div>
                            <div class="mt-1">
                                <small class="text-success fw-bold">
                                    <i class="fas fa-coins text-warning me-1"></i>Earn ${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}
                                </small>
                            </div>
                        </div>

                        <!-- Action Button -->
                        @if(in_array($video->id, $watchedVideoIds))
                            <button class="btn btn-success btn-sm w-100" disabled>
                                <i class="fas fa-check me-1"></i>Already Watched
                            </button>
                        @else
                            <button class="btn btn-primary w-100 watch-video-btn" 
                                    data-video-id="{{ $video->id }}" 
                                    data-video-url="{{ $video->video_url }}"
                                    data-video-title="{{ $video->title }}"
                                    data-earning="{{ $userStats['earning_rate'] ?? $video->cost_per_click }}"
                                    data-duration="{{ $video->duration ?? 120 }}"
                                    data-category="{{ $video->category ?? 'general' }}"
                                    data-views="{{ $video->views_count }}">
                                <i class="fas fa-play me-1"></i>
                                Watch & Earn ${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        @if(isset($message) && $message)
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4 class="text-dark">{{ $message }}</h4>
                            @if(!$hasActiveInvestment)
                                <p class="text-muted">You need to make an investment to start earning from videos.</p>
                                <div class="mt-4">
                                    <a href="{{ route('user.plan') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-rocket"></i> View Investment Plans
                                    </a>
                                </div>
                            @elseif($hasActiveInvestment && isset($userStats) && $userStats['remaining_views'] <= 0)
                                <p class="text-muted">Your daily video limit will reset tomorrow. Come back then to continue earning!</p>
                                <div class="mt-4">
                                    <span class="badge bg-info bg-gradient px-3 py-2">
                                        <i class="fas fa-clock"></i> Reset Tomorrow
                                    </span>
                                </div>
                            @else
                                <p class="text-muted">Video access is not available for your current plan.</p>
                                <div class="mt-4">
                                    <a href="{{ route('user.plan') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-upgrade"></i> Upgrade Plan
                                    </a>
                                </div>
                            @endif
                        @else
                            <i class="fas fa-video fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Videos Available</h4>
                            <p class="text-muted">There are currently no videos available to watch. Please check back later!</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Load More Section -->
    @if($videos->count() >= 12)
        <div class="row mt-4">
            <div class="col-12 text-center">
                <button class="btn btn-outline-primary" id="load-more-btn">
                    <i class="fas fa-plus me-2"></i>Load More Videos
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Video Watch Modal -->
<div class="modal fade" id="videoWatchModal" tabindex="-1" aria-labelledby="videoWatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="videoWatchModalLabel">
                    <i class="fas fa-play-circle me-2"></i><span id="modal-video-title">Watch Video to Earn</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Video Container -->
                <div class="position-relative">
                    <div class="ratio ratio-16x9">
                        <iframe id="video-iframe" 
                                src="" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </div>
                    
                    <!-- Video Overlay -->
                    <div id="video-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" 
                         style="background: rgba(0,0,0,0.8); z-index: 10; backdrop-filter: blur(5px);">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center text-white">
                                <div class="spinner-border text-success mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h5>Processing your earning...</h5>
                                <p>Please wait while we verify your watch time.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <!-- Earning Info -->
                            <div class="mb-3">
                                <div class="alert alert-info mb-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Watch at least <strong id="minimum-watch-time">20 seconds</strong> to earn <strong id="modal-earning">$0.00</strong>
                                </div>
                            </div>
                            
                            <!-- Watch Progress -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Watch Progress</span>
                                    <span id="progress-text" class="badge bg-primary">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" id="watch-progress" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">
                                    <span id="watch-time">0</span> seconds watched 
                                    (minimum <span id="minimum-required-time">20</span> seconds required)
                                </small>
                            </div>
                            
                            <!-- Video Info -->
                            <div class="row text-sm">
                                <div class="col-sm-6">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <span class="text-muted" id="modal-video-views">0 views</span>
                                </div>
                                <div class="col-sm-6">
                                    <i class="fas fa-tag text-muted me-1"></i>
                                    <span class="text-muted" id="modal-video-category">General</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button type="button" class="btn btn-success btn-lg w-100" id="claim-earnings-btn" disabled>
                                <i class="fas fa-coins me-1"></i>Claim $<span id="claim-amount">0.00</span>
                            </button>
                            <div class="mt-2">
                                <button type="button" class="btn btn-secondary btn-sm w-100" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Close Video
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-coins"></i> Congratulations!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-dollar-sign fa-3x text-success"></i>
                </div>
                <h4 id="success-earnings-amount" class="text-success"></h4>
                <p id="success-earnings-message" class="text-muted"></p>
                <div class="alert alert-info">
                    <strong>Your Total Earnings:</strong> $<span id="success-total-balance"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="fas fa-check"></i> Continue Watching
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentVideoId = null;
    let watchTimer = null;
    let watchDuration = 0;
    let videoDuration = 0;
    let earningAmount = 0;
    let minimumWatchTime = 20; // Will be calculated dynamically
    let videoOffset = {{ $videos->count() }};

    // Watch video button click
    $(document).on('click', '.watch-video-btn', function() {
        currentVideoId = $(this).data('video-id');
        const videoUrl = $(this).data('video-url');
        const videoTitle = $(this).data('video-title');
        const videoCategory = $(this).data('video-category');
        const videoViews = $(this).data('video-views');
        earningAmount = $(this).data('earning');
        videoDuration = $(this).data('duration') || 120; // default 2 minutes
        
        // Calculate minimum watch time: 80% of video or 15 seconds minimum, whichever is less
        minimumWatchTime = Math.min(Math.max(Math.ceil(videoDuration * 0.8), 15), videoDuration);
        
        // Calculate percentage for display
        const watchPercentage = Math.round((minimumWatchTime / videoDuration) * 100);
        
        // Update modal content
        $('#modal-video-title').text(videoTitle);
        $('#modal-earning').text('$' + parseFloat(earningAmount).toFixed(4));
        $('#claim-amount').text(parseFloat(earningAmount).toFixed(4));
        $('#modal-video-views').text(videoViews.toLocaleString() + ' views');
        $('#modal-video-category').text(videoCategory.charAt(0).toUpperCase() + videoCategory.slice(1));
        $('#minimum-watch-time').text(minimumWatchTime + ' seconds (' + watchPercentage + '% of video)');
        $('#minimum-required-time').text(minimumWatchTime);
        
        // Convert video URL to embed format
        let embedUrl = videoUrl;
        if (videoUrl.includes('youtube.com/watch')) {
            const videoId = new URL(videoUrl).searchParams.get('v');
            embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&enablejsapi=1`;
        } else if (videoUrl.includes('youtu.be/')) {
            const videoId = videoUrl.split('/').pop().split('?')[0];
            embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&enablejsapi=1`;
        }
        
        $('#video-iframe').attr('src', embedUrl);
        $('#videoWatchModal').modal('show');
        
        // Reset progress
        watchDuration = 0;
        updateProgress();
        
        // Start watch timer
        startWatchTimer();
    });

    // Start watch timer
    function startWatchTimer() {
        if (watchTimer) clearInterval(watchTimer);
        
        watchTimer = setInterval(function() {
            // Only count if page is visible and focused and modal is open
            if (!document.hidden && document.hasFocus() && $('#videoWatchModal').hasClass('show')) {
                watchDuration += 1;
                updateProgress();
                
                // Check if user has watched enough (dynamic minimum time)
                if (watchDuration >= minimumWatchTime) {
                    $('#claim-earnings-btn').prop('disabled', false);
                    $('#claim-earnings-btn').addClass('pulse');
                }
            }
        }, 1000);
    }

    // Update progress bar
    function updateProgress() {
        const progress = Math.min((watchDuration / minimumWatchTime) * 100, 100);
        $('#watch-progress').css('width', progress + '%');
        $('#progress-text').text(Math.round(progress) + '%');
        $('#watch-time').text(watchDuration);
        
        // Update progress bar color
        if (watchDuration >= minimumWatchTime) {
            $('#watch-progress').removeClass('bg-warning').addClass('bg-success');
            $('#progress-text').removeClass('bg-primary').addClass('bg-success');
        } else if (watchDuration >= Math.floor(minimumWatchTime * 0.5)) {
            $('#watch-progress').removeClass('bg-primary').addClass('bg-warning');
            $('#progress-text').removeClass('bg-primary').addClass('bg-warning');
        }
    }

    // Claim earnings
    $('#claim-earnings-btn').on('click', function() {
        if (!currentVideoId) return;
        
        if (watchDuration < minimumWatchTime) {
            Swal.fire({
                title: 'Not Enough Watch Time',
                text: `Please watch at least ${minimumWatchTime} seconds to earn money.`,
                icon: 'warning',
                confirmButtonText: 'Continue Watching'
            });
            return;
        }
        
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
        
        // Show overlay
        $('#video-overlay').removeClass('d-none');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.post('{{ route("user.video-views.watch") }}', {
            video_id: currentVideoId,
            watch_duration: watchDuration
        })
        .done(function(response) {
            $('#video-overlay').addClass('d-none');
            
            if (response.success) {
                // Hide video modal
                $('#videoWatchModal').modal('hide');
                
                // Show success modal
                $('#success-earnings-amount').text('You earned $' + parseFloat(response.earned_amount).toFixed(4));
                $('#success-earnings-message').text(response.message);
                $('#success-total-balance').text(parseFloat(response.total_earnings).toFixed(4));
                $('#successModal').modal('show');
                
                // Update the video card to show as watched
                const videoCard = $(`.video-item[data-video-id="${currentVideoId}"]`);
                videoCard.find('.watch-video-btn')
                    .removeClass('btn-primary')
                    .addClass('btn-success')
                    .prop('disabled', true)
                    .html('<i class="fas fa-check me-1"></i>Already Watched');
                
                // Add watched badge
                const badgeContainer = videoCard.find('.position-relative');
                if (!badgeContainer.find('.badge.bg-success').length) {
                    badgeContainer.prepend(`
                        <span class="position-absolute top-0 start-0 badge bg-success m-2">
                            <i class="fas fa-check me-1"></i>Watched
                        </span>
                    `);
                }
                
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
            }
        })
        .fail(function(xhr) {
            $('#video-overlay').addClass('d-none');
            const response = xhr.responseJSON;
            Swal.fire({
                title: 'Error',
                text: response?.message || 'An error occurred while processing your request.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
        });
    });

    // Load more videos
    $('#load-more-btn').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        
        $.ajax({
            url: '{{ route("user.video-views.index") }}',
            method: 'GET',
            data: {
                ajax: 1,
                offset: videoOffset,
                limit: 12
            },
            success: function(response) {
                if (response.success && response.videos.length > 0) {
                    let videosHtml = '';
                    response.videos.forEach(function(video) {
                        const isWatched = video.user_has_viewed;
                        const thumbnailUrl = video.thumbnail || 'https://via.placeholder.com/320x180?text=Video';
                        
                        videosHtml += `
                            <div class="col-lg-4 col-md-6 mb-4 video-item" data-video-id="${video.id}">
                                <div class="card h-100 border-0 shadow-sm video-card hover-shadow">
                                    <div class="position-relative">
                                        <div class="video-thumbnail" style="height: 200px; position: relative; overflow: hidden; border-radius: 0.5rem 0.5rem 0 0;">
                                            <img src="${thumbnailUrl}" 
                                                 class="video-thumb-img" 
                                                 style="width: 100%; height: 100%; object-fit: cover;" 
                                                 alt="${video.title}">
                                            <div class="play-overlay">
                                                <i class="fas fa-play-circle fa-3x text-white"></i>
                                            </div>
                                        </div>
                                        ${isWatched ? 
                                            '<span class="position-absolute top-0 start-0 badge bg-success m-2"><i class="fas fa-check me-1"></i>Watched</span>' : 
                                            ''
                                        }
                                        <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                            <i class="fas fa-dollar-sign me-1"></i>$${parseFloat(video.cost_per_click).toFixed(4)}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold text-dark">${video.title}</h6>
                                        ${video.description ? 
                                            `<p class="card-text small text-muted mb-2">${video.description.substring(0, 80)}${video.description.length > 80 ? '...' : ''}</p>` : 
                                            ''
                                        }
                                        <div class="video-meta mb-3">
                                            <div class="d-flex justify-content-between align-items-center text-sm">
                                                <small class="text-muted">
                                                    <i class="fas fa-eye me-1"></i>${video.views_count.toLocaleString()}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>${Math.floor(video.duration / 60)}:${(video.duration % 60).toString().padStart(2, '0')}
                                                </small>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-success fw-bold">
                                                    <i class="fas fa-coins text-warning me-1"></i>Earn $${parseFloat(video.cost_per_click).toFixed(4)}
                                                </small>
                                            </div>
                                        </div>
                                        ${!isWatched ? 
                                            `<button class="btn btn-primary w-100 watch-video-btn" 
                                                    data-video-id="${video.id}" 
                                                    data-video-url="${video.video_url}"
                                                    data-video-title="${video.title}"
                                                    data-earning="${video.cost_per_click}"
                                                    data-duration="${video.duration}"
                                                    data-category="${video.category}"
                                                    data-views="${video.views_count}">
                                                <i class="fas fa-play me-1"></i>
                                                Watch & Earn $${parseFloat(video.cost_per_click).toFixed(4)}
                                            </button>` : 
                                            `<button class="btn btn-success btn-sm w-100" disabled>
                                                <i class="fas fa-check me-1"></i>Already Watched
                                            </button>`
                                        }
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#videos-container').append(videosHtml);
                    videoOffset += response.videos.length;
                    
                    if (!response.has_more) {
                        button.parent().html('<p class="text-muted">No more videos available</p>');
                    } else {
                        button.prop('disabled', false).html('<i class="fas fa-plus me-2"></i>Load More Videos');
                    }
                } else {
                    button.parent().html('<p class="text-muted">No more videos available</p>');
                }
            },
            error: function() {
                button.prop('disabled', false).html('<i class="fas fa-plus me-2"></i>Load More Videos');
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load more videos. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Clean up when modal is closed
    $('#videoWatchModal').on('hidden.bs.modal', function() {
        if (watchTimer) {
            clearInterval(watchTimer);
            watchTimer = null;
        }
        $('#video-iframe').attr('src', '');
        watchDuration = 0;
        currentVideoId = null;
        $('#claim-earnings-btn').prop('disabled', true)
            .removeClass('pulse')
            .html('<i class="fas fa-coins me-1"></i>Claim $<span id="claim-amount">0.00</span>');
        updateProgress();
    });

    // Pause timer when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page hidden - pausing timer');
        } else {
            console.log('Page visible - resuming timer');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.video-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.video-card:hover {
    border-color: #007bff;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.video-thumbnail {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    overflow: hidden;
}

.video-thumb-img {
    transition: transform 0.3s ease;
}

.video-card:hover .video-thumb-img {
    transform: scale(1.05);
}

.play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .play-overlay {
    opacity: 1;
}

.text-sm {
    font-size: 0.875rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.badge.bg-gradient {
    background: linear-gradient(135deg, var(--bs-success) 0%, #198754 100%) !important;
}

.badge.bg-info.bg-gradient {
    background: linear-gradient(135deg, var(--bs-info) 0%, #0dcaf0 100%) !important;
}

.progress {
    background-color: rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

.pulse {
    animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
    0% {
        box-shadow: 0 0 0 0px rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0px rgba(40, 167, 69, 0);
    }
}

.modal-xl {
    max-width: 1200px;
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

#video-overlay {
    backdrop-filter: blur(5px);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.badge {
    border-radius: 6px;
    font-weight: 500;
}

.fw-bold {
    font-weight: 600 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Video card animations */
.video-item {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .video-card {
        margin-bottom: 1.5rem;
    }
    
    .modal-footer .container-fluid .row .col-md-4 {
        text-align: center !important;
        margin-top: 1rem;
    }
    
    .modal-footer .btn {
        width: 100%;
    }
}

/* Loading animation */
.spinner-border {
    width: 2rem;
    height: 2rem;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Better spacing for mobile */
@media (max-width: 576px) {
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .col-lg-4.col-md-6.mb-4 {
        margin-bottom: 1.5rem;
    }
}
</style>
@endpush
</x-smart_layout>
