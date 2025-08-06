<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)

@section('content')
<div class="container py-4 my-4">
    <!-- User Stats Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white border-0 shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4><i class="fas fa-video me-2"></i>Watch Videos & Earn Money</h4>
                            <p class="mb-0">Currently watching: <span id="current-video-display-title">{{ $video->title ?? 'Loading...' }}</span></p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @auth
                                <h5><i class="fas fa-wallet me-1"></i>Balance: $<span id="user-balance">{{ number_format(auth()->user()->interest_wallet, 4) }}</span></h5>
                                <p class="mb-0 small">Today's Earnings: $<span id="today-earnings">0.0000</span></p>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Current Video Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg" id="main-video-card">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0">
                                <i class="fas fa-play-circle me-2"></i>
                                <span id="current-video-title">{{ $video->title ?? 'Watch Video' }}</span>
                            </h4>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-success fs-6" id="current-video-earning">
                                <i class="fas fa-dollar-sign me-1"></i>Earn $<span id="earning-amount">{{ number_format($video->cost_per_click, 4) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0"> 
                    <!-- Video Container -->
                    <div class="position-relative">
                        <div class="ratio ratio-16x9" id="video-container">
                            @if(str_contains($video->video_url, 'youtube.com') || str_contains($video->video_url, 'youtu.be'))
                                @php
                                    $videoId = '';
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&enablejsapi=1";
                                @endphp
                                <iframe id="video-player" 
                                        src="{{ $embedUrl }}" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            @else
                                <iframe id="video-player" 
                                        src="{{ $video->video_url }}" 
                                        frameborder="0" 
                                        allowfullscreen>
                                </iframe>
                            @endif
                        </div>
                        
                        <!-- Overlay for tracking -->
                        <div id="video-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" 
                             style="background: rgba(0,0,0,0.7); z-index: 10;">
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
                
                <div class="card-footer bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <!-- Watch Progress -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Watch Progress</span>
                                    <span id="progress-text" class="badge bg-primary">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" id="watch-progress" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">Watch at least 80% to earn money</small>
                            </div>
                            
                            <!-- Video Info -->
                            <div class="row text-sm">
                                <div class="col-sm-6">
                                    <i class="fas fa-eye text-muted me-1"></i>
                                    <span class="text-muted" id="current-video-views">{{ number_format($video->views_count) }} views</span>
                                </div>
                                <div class="col-sm-6">
                                    <i class="fas fa-tag text-muted me-1"></i>
                                    <span class="text-muted" id="current-video-category">{{ ucfirst($video->category) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-success btn-lg" id="claim-earnings-btn" disabled>
                                <i class="fas fa-coins me-2"></i>Claim $<span id="claim-amount">{{ number_format($video->cost_per_click, 4) }}</span>
                            </button>
                            <div class="mt-2">
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Videos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- More Videos Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>More Videos to Watch
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row" id="more-videos-container">
                        <!-- Videos will be loaded here via AJAX -->
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading more videos...</p>
                        </div>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center mt-4" id="load-more-section" style="display: none;">
                        <button class="btn btn-outline-primary" id="load-more-btn">
                            <i class="fas fa-plus me-2"></i>Load More Videos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>How to Earn:</h6>
                <ul class="mb-0">
                    <li>Watch at least 80% of the video duration</li>
                    <li>Keep the video tab active and focused</li>
                    <li>Click "Claim Earnings" button when it becomes available</li>
                    <li>Switch between videos without losing progress</li>
                    <li>You can only earn from each video once</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Earnings Modal -->
<div class="modal fade" id="earningsModal" tabindex="-1">
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
                <h4 id="earnings-amount" class="text-success"></h4>
                <p id="earnings-message" class="text-muted"></p>
                <div class="alert alert-info">
                    <strong>Your Total Balance:</strong> $<span id="total-balance"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check"></i> Continue Watching
                </button>
                <a href="{{ route('user.video-views.index') }}" class="btn btn-primary">
                    <i class="fas fa-video"></i> More Videos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
$(document).ready(function() {
    var watchTimer = null;
    var watchDuration = 0;
    
    // Video data from server - using proper escaping
    var currentVideoData = {
        id: {{ $video->id }},
        duration: {{ $video->duration ?? 120 }},
        cost_per_click: {{ $video->cost_per_click }},
        title: @json($video->title ?? 'Watch Video'),
        category: '{{ $video->category ?? "general" }}',
        views_count: {{ $video->views_count ?? 0 }}
    };
    
    var videoDuration = currentVideoData.duration;
    var videoId = currentVideoData.id;
    var requiredWatchTime = Math.floor(videoDuration * 0.8);
    
    // Video watch progress storage
    let videoProgress = {};
    
    // Initialize current video progress
    videoProgress[videoId] = {
        watchDuration: 0,
        claimed: false
    };
    
    // Start watching timer when page loads
    startWatchTimer();
    
    // Load more videos
    loadMoreVideos();
    
    function startWatchTimer() {
        if (watchTimer) clearInterval(watchTimer);
        
        watchTimer = setInterval(function() {
            // Only count if page is visible and focused
            if (!document.hidden && document.hasFocus()) {
                watchDuration += 1;
                videoProgress[videoId].watchDuration = watchDuration;
                updateProgress();
                
                // Check if user has watched enough
                if (watchDuration >= requiredWatchTime && !videoProgress[videoId].claimed) {
                    $('#claim-earnings-btn').prop('disabled', false);
                    $('#claim-earnings-btn').addClass('pulse');
                }
            }
        }, 1000);
    }
    
    function updateProgress() {
        const progress = Math.min((watchDuration / videoDuration) * 100, 100);
        $('#watch-progress').css('width', progress + '%');
        $('#progress-text').text(Math.round(progress) + '%');
        
        // Update progress bar color
        if (progress >= 80) {
            $('#watch-progress').removeClass('bg-warning').addClass('bg-success');
            $('#progress-text').removeClass('bg-primary').addClass('bg-success');
        } else if (progress >= 50) {
            $('#watch-progress').removeClass('bg-primary').addClass('bg-warning');
            $('#progress-text').removeClass('bg-primary').addClass('bg-warning');
        }
    }
    
    function loadMoreVideos() {
        $.ajax({
            url: '{{ route("user.video-views.index") }}',
            method: 'GET',
            data: {
                ajax: 1,
                exclude: videoId
            },
            success: function(response) {
                if (response.videos && response.videos.length > 0) {
                    let videosHtml = '';
                    response.videos.forEach(function(video) {
                        const isWatched = video.user_has_viewed;
                        videosHtml += `
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                <div class="card video-card h-100 border-0 shadow-sm hover-shadow" data-video-id="${video.id}">
                                    <div class="position-relative">
                                        <div class="video-thumbnail" style="height: 200px; background: url('${video.thumbnail}') center/cover; border-radius: 0.5rem 0.5rem 0 0;">
                                            <div class="play-overlay d-flex align-items-center justify-content-center h-100">
                                                <i class="fas fa-play-circle fa-3x text-white"></i>
                                            </div>
                                        </div>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-dollar-sign"></i> $${parseFloat(video.cost_per_click).toFixed(4)}
                                            </span>
                                        </div>
                                        ${isWatched ? '<div class="position-absolute top-0 start-0 m-2"><span class="badge bg-info"><i class="fas fa-check"></i> Watched</span></div>' : ''}
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">${video.title}</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-eye"></i> ${video.views_count.toLocaleString()} views
                                            </small>
                                            <small class="text-success fw-bold">
                                                <i class="fas fa-coins text-warning"></i> $${parseFloat(video.cost_per_click).toFixed(2)}
                                            </small>
                                        </div>
                                        ${!isWatched ? 
                                            `<button class="btn btn-primary btn-sm w-100 switch-video-btn" 
                                                    data-video-id="${video.id}"
                                                    data-title="${video.title}"
                                                    data-url="${video.video_url}"
                                                    data-duration="${video.duration || 120}"
                                                    data-cost="${video.cost_per_click}"
                                                    data-category="${video.category}"
                                                    data-views="${video.views_count}">
                                                <i class="fas fa-play"></i> Watch & Earn $${parseFloat(video.cost_per_click).toFixed(4)}
                                            </button>` : 
                                            `<button class="btn btn-success btn-sm w-100" disabled>
                                                <i class="fas fa-check"></i> Already Watched
                                            </button>`
                                        }
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#more-videos-container').html(videosHtml);
                } else {
                    $('#more-videos-container').html(`
                        <div class="col-12 text-center py-4">
                            <i class="fas fa-video fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No more videos available</h5>
                            <p class="text-muted">Check back later for new videos!</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#more-videos-container').html(`
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5 class="text-warning">Error loading videos</h5>
                        <button class="btn btn-primary btn-sm" onclick="loadMoreVideos()">Try Again</button>
                    </div>
                `);
            }
        });
    }
    
    // Handle video switching
    $(document).on('click', '.switch-video-btn', function() {
        const newVideoData = {
            id: $(this).data('video-id'),
            title: $(this).data('title'),
            url: $(this).data('url'),
            duration: $(this).data('duration'),
            cost_per_click: $(this).data('cost'),
            category: $(this).data('category'),
            views_count: $(this).data('views')
        };
        
        switchToVideo(newVideoData);
    });
    
    function switchToVideo(newVideoData) {
        // Stop current timer
        if (watchTimer) {
            clearInterval(watchTimer);
            watchTimer = null;
        }
        
        // Initialize progress for new video if not exists
        if (!videoProgress[newVideoData.id]) {
            videoProgress[newVideoData.id] = {
                watchDuration: 0,
                claimed: false
            };
        }
        
        // Update current video data
        currentVideoData = newVideoData;
        videoId = newVideoData.id;
        videoDuration = newVideoData.duration;
        requiredWatchTime = Math.floor(videoDuration * 0.8);
        watchDuration = videoProgress[videoId].watchDuration;
        
        // Update UI
        $('#current-video-title').text(newVideoData.title);
        $('#earning-amount').text(parseFloat(newVideoData.cost_per_click).toFixed(4));
        $('#claim-amount').text(parseFloat(newVideoData.cost_per_click).toFixed(4));
        $('#current-video-views').text(newVideoData.views_count.toLocaleString() + ' views');
        $('#current-video-category').text(newVideoData.category.charAt(0).toUpperCase() + newVideoData.category.slice(1));
        
        // Update video player
        let embedUrl = newVideoData.url;
        if (newVideoData.url.includes('youtube.com') || newVideoData.url.includes('youtu.be')) {
            const videoIdMatch = newVideoData.url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
            if (videoIdMatch) {
                embedUrl = `https://www.youtube.com/embed/${videoIdMatch[1]}?autoplay=1&enablejsapi=1`;
            }
        }
        
        $('#video-player').attr('src', embedUrl);
        
        // Reset claim button
        if (videoProgress[videoId].claimed) {
            $('#claim-earnings-btn').prop('disabled', true)
                .removeClass('btn-success pulse')
                .addClass('btn-outline-success')
                .html('<i class="fas fa-check me-2"></i>Already Earned');
        } else {
            $('#claim-earnings-btn').prop('disabled', watchDuration < requiredWatchTime)
                .removeClass('btn-outline-success')
                .addClass('btn-success')
                .html(`<i class="fas fa-coins me-2"></i>Claim $${parseFloat(newVideoData.cost_per_click).toFixed(4)}`);
        }
        
        // Update progress
        updateProgress();
        
        // Start timer for new video
        startWatchTimer();
        
        // Scroll to video
        $('html, body').animate({
            scrollTop: $('#main-video-card').offset().top - 20
        }, 500);
    }
    
    // Handle claim earnings button
    $('#claim-earnings-btn').on('click', function() {
        if (watchDuration < requiredWatchTime) {
            Swal.fire({
                title: 'Not Enough Watch Time',
                text: `Please watch at least 80% of the video to earn money.`,
                icon: 'warning',
                confirmButtonText: 'Continue Watching'
            });
            return;
        }
        
        if (videoProgress[videoId].claimed) {
            Swal.fire({
                title: 'Already Claimed',
                text: 'You have already earned from this video.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        
        // Show overlay
        $('#video-overlay').removeClass('d-none');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.post('{{ route("user.video-views.watch") }}', {
            video_id: videoId,
            watch_duration: watchDuration
        })
        .done(function(response) {
            $('#video-overlay').addClass('d-none');
            
            if (response.success) {
                // Mark as claimed
                videoProgress[videoId].claimed = true;
                
                // Show success modal
                $('#earnings-amount').text('You earned $' + parseFloat(response.earned_amount).toFixed(4));
                $('#earnings-message').text(response.message);
                $('#total-balance').text(parseFloat(response.total_earnings).toFixed(4));
                $('#earningsModal').modal('show');
                
                // Update button
                button.removeClass('btn-success pulse')
                    .addClass('btn-outline-success')
                    .html('<i class="fas fa-check me-2"></i>Earned!');
                
                // Remove from available videos list
                $(`.video-card[data-video-id="${videoId}"]`).find('.switch-video-btn')
                    .removeClass('btn-primary')
                    .addClass('btn-success')
                    .prop('disabled', true)
                    .html('<i class="fas fa-check"></i> Already Watched');
                
                // Add watched badge
                $(`.video-card[data-video-id="${videoId}"] .position-relative`).append(`
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-info">
                            <i class="fas fa-check"></i> Watched
                        </span>
                    </div>
                `);
                
            } else {
                Swal.fire({
                    title: 'Error',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                button.prop('disabled', false).html(`<i class="fas fa-coins me-2"></i>Claim $${parseFloat(currentVideoData.cost_per_click).toFixed(4)}`);
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
            button.prop('disabled', false).html(`<i class="fas fa-coins me-2"></i>Claim $${parseFloat(currentVideoData.cost_per_click).toFixed(4)}`);
        });
    });
    
    // Pause timer when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page hidden - pausing timer');
        } else {
            console.log('Page visible - resuming timer');
        }
    });
    
    // Clean up timer when page unloads
    window.addEventListener('beforeunload', function() {
        if (watchTimer) {
            clearInterval(watchTimer);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.text-sm {
    font-size: 0.875rem;
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

.progress {
    background-color: rgba(0,0,0,0.1);
}

#video-overlay {
    backdrop-filter: blur(5px);
}

.ratio-16x9 {
    --bs-aspect-ratio: 56.25%;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.video-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.video-card:hover {
    border-color: #007bff;
}

.video-thumbnail {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

.play-overlay {
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .play-overlay {
    opacity: 1;
}

.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%) !important;
}

.badge.bg-gradient {
    background: linear-gradient(135deg, var(--bs-success) 0%, #198754 100%) !important;
}

.badge.bg-info.bg-gradient {
    background: linear-gradient(135deg, var(--bs-info) 0%, #0dcaf0 100%) !important;
}

.btn.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%) !important;
    border: none;
}

.btn-success.bg-gradient {
    background: linear-gradient(135deg, var(--bs-success) 0%, #198754 100%) !important;
}

.fw-bold {
    font-weight: 600 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.switch-video-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

@media (max-width: 768px) {
    .card-footer .row > .col-md-4 {
        text-align: center !important;
    }
    
    #claim-earnings-btn {
        width: 100%;
    }
    
    .video-card {
        margin-bottom: 1rem;
    }
}

/* Loading animation */
.spinner-border {
    width: 2rem;
    height: 2rem;
}

/* Video grid responsiveness */
@media (max-width: 576px) {
    .col-lg-4.col-md-6.col-sm-12 {
        margin-bottom: 1.5rem;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Card animations */
.video-card {
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

/* Modal improvements */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

/* Progress bar improvements */
.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Button improvements */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Badge improvements */
.badge {
    border-radius: 6px;
    font-weight: 500;
}
</style>
@endpush
</x-smart_layout>
