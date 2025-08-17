<x-smart_layout>
    @section('top_title', 'Video Gallery')
    @section('title', 'Video Gallery')
    @section('content')

    <!-- Simple Video Gallery -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-play-circle me-2"></i>Watch Videos & Earn Money
                            </h4>
                            <span class="badge bg-light text-primary" id="videoCounter">
                                <i class="fas fa-video me-1"></i>
                                <span id="videoCount">{{ $videos->count() }}</span> Videos Available
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($videos->count() > 0)
                            <div class="row g-4">
                                @foreach($videos as $video)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-12" data-video-container="{{ $video->id }}">
                                        <div class="card h-100 border-0 shadow-sm" data-video-card="{{ $video->id }}">
                                            <!-- Video Thumbnail -->
                                            <div class="position-relative" style="height: 180px; overflow: hidden;">
                                                @if(str_contains($video->video_url, 'youtube.com') || str_contains($video->video_url, 'youtu.be'))
                                                    @php
                                                        $videoId = '';
                                                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video->video_url, $matches)) {
                                                            $videoId = $matches[1];
                                                        }
                                                    @endphp
                                                    @if($videoId)
                                                        <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg" 
                                                             class="w-100 h-100" 
                                                             style="object-fit: cover;" 
                                                             alt="{{ $video->title }}">
                                                    @endif
                                                @endif
                                                
                                                <!-- Earning Badge -->
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-success">
                                                        ${{ number_format($video->cost_per_click, 4) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $video->title }}</h6>
                                                
                                                <!-- Video Duration Display -->
                                                <div class="d-flex justify-content-between mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>Duration: {{ $video->duration ?? 30 }}s
                                                    </small>
                                                    <small class="text-success">
                                                        <i class="fas fa-eye me-1"></i>{{ $video->views_count ?? 0 }} views
                                                    </small>
                                                </div>
                                                
                                                @auth
                                                    <button class="btn btn-primary w-100 watch-btn"
                                                            data-video-id="{{ $video->id }}"
                                                            data-video-url="{{ $video->video_url }}"
                                                            data-video-title="{{ $video->title }}"
                                                            data-video-duration="{{ $video->duration ?? 30 }}"
                                                            data-earning="{{ $video->cost_per_click }}">
                                                        <i class="fas fa-play me-2"></i>Watch & Earn ${{ number_format($video->cost_per_click, 4) }}
                                                    </button>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                                        <i class="fas fa-sign-in-alt me-2"></i>Login to Watch
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No videos available</h5>
                                <p class="text-muted">Please check back later!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="videoTitle">Watch Video</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-0 position-relative">
                    <div class="ratio ratio-16x9">
                        <iframe id="videoFrame" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                    </div>
                    <!-- Click overlay for manual play when needed -->
                    <div id="playOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.7); z-index: 10;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <button class="btn btn-primary btn-lg" onclick="forceVideoPlay()">
                                <i class="fas fa-play me-2"></i>Click to Play Video
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-md-6">
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="watchProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Watch for <span id="requiredTime">20</span> seconds 
                                <span class="text-primary">(<span id="currentTime">0</span>s / <span id="totalDuration">20</span>s)</span>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="spinner-border spinner-border-sm text-warning me-2" id="watchingIndicator" style="display: none;"></div>
                                    <small class="text-warning fw-bold" id="statusText">Ready to watch</small>
                                </div>
                                <div id="pauseWarning" class="text-danger small mt-2" style="display: none;">
                                    <!-- Pause warning content will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-success" id="claimBtn" disabled>
                                <i class="fas fa-coins me-1"></i>Claim $<span id="earning">0.00</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endsection

    @push('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        let watchTimer = null;
        let watchDuration = 0;
        let requiredTime = 20; // Default, will be updated from video duration
        let currentVideo = null;
        let isVideoPlaying = false;
        let lastActiveTime = Date.now();
        let pauseCount = 0;
        let maxPauses = 2; // Allow maximum 2 pauses
        let tabVisible = true;
        
        // Track tab visibility to detect if user switches tabs or minimizes browser
        document.addEventListener('visibilitychange', function() {
            tabVisible = !document.hidden;
            if (!tabVisible && isVideoPlaying) {
                handleVideoPause('Browser minimized or tab switched away');
            }
        });
        
        // Track window focus/blur for browser window switching
        window.addEventListener('blur', function() {
            if (isVideoPlaying) {
                handleVideoPause('Browser window lost focus');
            }
        });
        
        window.addEventListener('focus', function() {
            if (!isVideoPlaying && tabVisible && watchTimer === null && watchDuration < requiredTime) {
                // Don't auto-resume, require manual resume
                updateStatus('Click Resume to continue watching', 'warning');
            }
        });
        
        // Track page visibility API for mobile browsers
        document.addEventListener('webkitvisibilitychange', function() {
            if (document.webkitHidden && isVideoPlaying) {
                handleVideoPause('Page visibility changed (mobile)');
            }
        });
        
        // Track mouse leave/enter window area
        document.addEventListener('mouseleave', function() {
            if (isVideoPlaying) {
                handleVideoPause('Mouse left browser window');
            }
        });
        
        // Watch button click
        $('.watch-btn').click(function() {
            const button = $(this);
            
            // Prevent if already clicked
            if (button.prop('disabled')) return;
            
            // Get video data
            currentVideo = {
                id: button.data('video-id'),
                url: button.data('video-url'),
                title: button.data('video-title'),
                duration: parseInt(button.data('video-duration')) || 30,
                earning: parseFloat(button.data('earning'))
            };
            
            // Set required time based on video duration (minimum 80% of video or 20 seconds)
            requiredTime = Math.max(20, Math.floor(currentVideo.duration * 0.8));
            
            // Disable button
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
            
            // Set modal content
            $('#videoTitle').text(currentVideo.title);
            $('#earning').text(currentVideo.earning.toFixed(4));
            $('#requiredTime').text(requiredTime);
            $('#totalDuration').text(requiredTime);
            
            // Convert YouTube URL to embed with enhanced parameters
            let embedUrl = convertToEmbed(currentVideo.url);
            
            // Reset modal
            watchDuration = 0;
            pauseCount = 0;
            isVideoPlaying = false;
            updateProgress();
            updateStatus('Loading video...', 'warning');
            $('#claimBtn').prop('disabled', true);
            $('#pauseWarning').hide();
            
            // Load video
            $('#videoFrame').attr('src', embedUrl);
            $('#videoModal').modal('show');
            
            // Start timer after short delay for video to load
            setTimeout(startTimer, 3000);
        });
        
        // Convert YouTube URL to embed format with enhanced parameters
        function convertToEmbed(url) {
            if (url.includes('youtube.com/watch')) {
                const videoId = url.split('v=')[1].split('&')[0];
                return `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&disablekb=1&fs=0&modestbranding=1&rel=0&enablejsapi=1&mute=0&start=0`;
            }
            if (url.includes('youtu.be/')) {
                const videoId = url.split('/').pop();
                return `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&disablekb=1&fs=0&modestbranding=1&rel=0&enablejsapi=1&mute=0&start=0`;
            }
            return url;
        }
        
        // Start watch timer with enhanced validation
        function startTimer() {
            if (watchTimer) clearInterval(watchTimer);
            
            isVideoPlaying = true;
            lastActiveTime = Date.now();
            updateStatus('Watching...', 'success');
            $('#watchingIndicator').show();
            
            watchTimer = setInterval(function() {
                // Only increment if video is actually playing and conditions are met
                if (!isVideoPlaying) {
                    return; // Don't increment time if video is paused
                }
                
                // Check if tab is visible and user is active
                if (!tabVisible) {
                    handleVideoPause('Tab not visible');
                    return;
                }
                
                // Check for user activity (mouse movement, clicks)
                const currentTime = Date.now();
                if (currentTime - lastActiveTime > 30000) { // 30 seconds inactive
                    handleVideoPause('User inactive for 30 seconds');
                    return;
                }
                
                // Increment watch duration only when actively watching
                watchDuration++;
                updateProgress();
                updateCurrentTime();
                
                // Check if required time is reached
                if (watchDuration >= requiredTime) {
                    clearInterval(watchTimer);
                    watchTimer = null;
                    isVideoPlaying = false;
                    $('#claimBtn').prop('disabled', false);
                    updateStatus('Ready to claim!', 'success');
                    $('#watchingIndicator').hide();
                }
                
                // Warning at 75% completion
                if (watchDuration === Math.floor(requiredTime * 0.75)) {
                    updateStatus(`Almost done! ${requiredTime - watchDuration}s remaining`, 'warning');
                }
            }, 1000);
        }
        
        // Handle video pause
        function handleVideoPause(reason) {
            if (!isVideoPlaying) return;
            
            isVideoPlaying = false;
            pauseCount++;
            
            if (watchTimer) {
                clearInterval(watchTimer);
                watchTimer = null;
            }
            
            updateStatus('Video paused', 'danger');
            $('#watchingIndicator').hide();
            $('#pauseWarning').show().html(`
                <div class="text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-pause-circle me-1"></i>⚠️ ${reason}
                    </div>
                    <div class="text-muted small mb-2">
                        Pause ${pauseCount}/${maxPauses} - Watch must be continuous
                    </div>
                    <button class="btn btn-sm btn-warning" onclick="resumeVideo()">
                        <i class="fas fa-play me-1"></i>Resume Watching
                    </button>
                </div>
            `);
            
            // If too many pauses, restart
            if (pauseCount >= maxPauses) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Too Many Pauses!',
                        text: `Maximum ${maxPauses} pauses allowed. Video will restart.`,
                        timer: 3000
                    });
                    restartVideo();
                }, 1000);
            }
        }
        
        // Handle video resume
        function handleVideoResume(reason) {
            if (isVideoPlaying || pauseCount >= maxPauses) return;
            
            $('#pauseWarning').hide();
            updateStatus('Resuming...', 'warning');
            lastActiveTime = Date.now(); // Reset activity timer
            
            // Force video to play again by reloading with autoplay
            const currentSrc = $('#videoFrame').attr('src');
            if (currentSrc) {
                // Add timestamp to force reload and ensure autoplay
                const separator = currentSrc.includes('?') ? '&' : '?';
                const newSrc = currentSrc + separator + 't=' + Date.now() + '&autoplay=1';
                $('#videoFrame').attr('src', newSrc);
            }
            
            setTimeout(() => {
                startTimer(); // This will set isVideoPlaying = true and restart the timer
            }, 2000); // Give more time for video to reload
        }
        
        // Force video play function for manual interaction
        window.forceVideoPlay = function() {
            $('#playOverlay').addClass('d-none');
            const currentSrc = $('#videoFrame').attr('src');
            if (currentSrc) {
                // Force reload with autoplay and interaction
                const separator = currentSrc.includes('?') ? '&' : '?';
                const newSrc = currentSrc + separator + 'autoplay=1&t=' + Date.now();
                $('#videoFrame').attr('src', newSrc);
            }
            
            // Update status
            updateStatus('Video playing...', 'success');
            lastActiveTime = Date.now();
        };
        
        // Enhanced manual resume function (called by resume button)
        window.resumeVideo = function() {
            if (pauseCount < maxPauses) {
                // Show loading indicator
                $('#pauseWarning').html(`
                    <div class="text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-spinner fa-spin me-1"></i>Reloading video...
                        </div>
                        <div class="text-muted small">
                            Video will restart automatically
                        </div>
                    </div>
                `);
                
                // Show play overlay in case manual interaction is needed
                $('#playOverlay').removeClass('d-none');
                
                // Add delay before resume
                setTimeout(() => {
                    handleVideoResume('Manual resume');
                    // Hide overlay after a short time if video starts automatically
                    setTimeout(() => {
                        $('#playOverlay').addClass('d-none');
                    }, 3000);
                }, 1000);
            } else {
                Swal.fire('Too Many Pauses', 'Maximum pauses exceeded. Video will restart.', 'warning');
                restartVideo();
            }
        };
        
        // Restart video
        function restartVideo() {
            watchDuration = 0;
            pauseCount = 0;
            isVideoPlaying = false;
            
            updateProgress();
            updateCurrentTime();
            updateStatus('Restarting...', 'warning');
            $('#pauseWarning').hide();
            $('#claimBtn').prop('disabled', true);
            
            // Reload video
            const currentSrc = $('#videoFrame').attr('src');
            $('#videoFrame').attr('src', '');
            setTimeout(() => {
                $('#videoFrame').attr('src', currentSrc);
                setTimeout(startTimer, 3000);
            }, 1000);
        }
        
        // Update progress
        function updateProgress() {
            const progress = Math.min(100, (watchDuration / requiredTime) * 100);
            $('#watchProgress').css('width', progress + '%');
        }
        
        // Update current time display
        function updateCurrentTime() {
            $('#currentTime').text(watchDuration);
        }
        
        // Update status message
        function updateStatus(message, type) {
            $('#statusText').text(message)
                           .removeClass('text-warning text-success text-danger')
                           .addClass(`text-${type}`);
        }
        
        // Track mouse movement for activity and detect when user leaves video area
        $(document).on('mousemove click keypress', function() {
            lastActiveTime = Date.now();
        });
        
        // Detect Alt+Tab (task switching) and other system shortcuts
        $(document).on('keydown', function(e) {
            lastActiveTime = Date.now();
            
            // Detect Alt+Tab, Ctrl+Tab, Windows key, etc.
            if ((e.altKey && e.key === 'Tab') || 
                (e.ctrlKey && e.key === 'Tab') ||
                e.key === 'Meta' || // Windows key
                (e.ctrlKey && e.shiftKey && e.key === 'Tab') || // Ctrl+Shift+Tab
                (e.altKey && e.key === 'Escape') || // Alt+Esc
                e.key === 'F11' || // Fullscreen toggle
                (e.ctrlKey && e.key === 'w') || // Close tab
                (e.ctrlKey && e.key === 'n') || // New window
                (e.ctrlKey && e.key === 't')) { // New tab
                
                if (isVideoPlaying) {
                    handleVideoPause('System shortcut detected - likely switching applications');
                }
            }
        });
        
        // Detect when user clicks outside the video modal
        $(document).on('click', function(e) {
            if (isVideoPlaying && !$(e.target).closest('#videoModal').length) {
                // User clicked outside video modal
                handleVideoPause('Clicked outside video area');
            }
        });
        
        // Detect window resize (might indicate minimizing)
        $(window).on('resize', function() {
            if (isVideoPlaying) {
                // Check if window is very small (likely minimized)
                if ($(window).width() < 100 || $(window).height() < 100) {
                    handleVideoPause('Browser window minimized');
                }
            }
        });
        
        // Detect scrolling away from video area
        $(window).on('scroll', function() {
            if (isVideoPlaying) {
                const modal = $('#videoModal');
                if (modal.is(':visible')) {
                    const modalTop = modal.offset().top;
                    const modalBottom = modalTop + modal.outerHeight();
                    const windowTop = $(window).scrollTop();
                    const windowBottom = windowTop + $(window).height();
                    
                    // If video modal is not in viewport
                    if (modalBottom < windowTop || modalTop > windowBottom) {
                        handleVideoPause('Scrolled away from video');
                    }
                }
            }
        });
        
        // Detect page unload/refresh attempts
        $(window).on('beforeunload', function() {
            if (isVideoPlaying && watchDuration > 0 && watchDuration < requiredTime) {
                return 'You are currently watching a video. Leaving will lose your progress.';
            }
        });
        
        // Claim earnings with enhanced validation
        $('#claimBtn').click(function() {
            if (watchDuration < requiredTime) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Ready!',
                    text: `Please watch for ${requiredTime - watchDuration} more seconds.`
                });
                return;
            }
            
            // Clear timer
            if (watchTimer) {
                clearInterval(watchTimer);
                watchTimer = null;
            }
            
            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
            
            // Add processing state to the video card
            const videoCard = $(`.watch-btn[data-video-id="${currentVideo.id}"]`).closest('.card');
            videoCard.addClass('border-warning').css('opacity', '0.8');
            
            // Send AJAX request
            $.ajax({
                url: '{{ route("video.simple-watch") }}',
                method: 'POST',
                data: {
                    video_id: currentVideo.id,
                    watch_duration: watchDuration,
                    actual_watch_time: watchDuration,
                    required_time: requiredTime,
                    pause_count: pauseCount,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Congratulations!',
                            text: `You earned ${response.earning} credits! Your balance: ${response.new_balance || 'Updated'}`,
                            timer: 4000,
                            showConfirmButton: false
                        });
                        
                        // Close modal first
                        $('#videoModal').modal('hide');
                        
                        // Find the video card that was just watched using multiple selectors
                        const videoId = currentVideo.id;
                        let watchedVideoButton = $(`.watch-btn[data-video-id="${videoId}"]`);
                        let watchedVideoCard = $(`[data-video-card="${videoId}"]`);
                        let watchedVideoContainer = $(`[data-video-container="${videoId}"]`);
                        
                        // Fallback selectors if data attributes don't work
                        if (watchedVideoContainer.length === 0) {
                            watchedVideoContainer = watchedVideoButton.closest('.col-xl-3, .col-lg-4, .col-md-6, .col-12');
                        }
                        if (watchedVideoCard.length === 0) {
                            watchedVideoCard = watchedVideoButton.closest('.card');
                        }
                        
                        console.log('Video ID:', videoId);
                        console.log('Found button:', watchedVideoButton.length);
                        console.log('Found card:', watchedVideoCard.length);
                        console.log('Found container:', watchedVideoContainer.length);
                        
                        // Add success visual feedback to the video card
                        watchedVideoCard.removeClass('border-warning')
                                       .addClass('border-success bg-light')
                                       .css('opacity', '1');
                        
                        // Show success state on the button briefly
                        watchedVideoButton.removeClass('btn-primary')
                                         .addClass('btn-success')
                                         .html('<i class="fas fa-check me-2"></i>Completed!')
                                         .prop('disabled', true);
                        
                        // Update UI if needed
                        if (response.new_balance) {
                            $('.balance-display').text(response.new_balance);
                        }
                        
                        // Wait a moment to show success state, then remove
                        setTimeout(() => {
                            console.log('Starting removal animation...');
                            
                            // Add fade out animation and remove the entire video container
                            watchedVideoContainer.fadeOut(1000, function() {
                                console.log('Video container faded out, removing...');
                                $(this).remove();
                                
                                // Update video counter
                                const remainingVideos = $('.watch-btn').length;
                                console.log('Remaining videos:', remainingVideos);
                                
                                $('#videoCount').text(remainingVideos);
                                
                                if (remainingVideos === 0) {
                                    // Hide counter and show completion message
                                    $('#videoCounter').fadeOut();
                                    $('.row.g-4').html(`
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                                                <h5 class="text-success">🎉 All Videos Completed!</h5>
                                                <p class="text-muted">Congratulations! You've watched all available videos today.</p>
                                                <p class="text-primary"><strong>Come back tomorrow for new earning opportunities!</strong></p>
                                                <div class="mt-4">
                                                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary me-2">
                                                        <i class="fas fa-dashboard me-1"></i>Go to Dashboard
                                                    </a>
                                                    <button class="btn btn-outline-primary" onclick="location.reload()">
                                                        <i class="fas fa-refresh me-1"></i>Check for New Videos
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    // Update counter text
                                    $('#videoCounter').html(`
                                        <i class="fas fa-video me-1"></i>
                                        <span id="videoCount">${remainingVideos}</span> Videos Remaining
                                    `);
                                }
                            });
                        }, 2000); // Show success state for 2 seconds
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to process video watch.'
                        });
                        // Reset button and card state properly
                        const videoCard = $(`.watch-btn[data-video-id="${currentVideo.id}"]`).closest('.card');
                        videoCard.removeClass('border-warning').css('opacity', '1');
                        button.prop('disabled', false).html(`<i class="fas fa-coins me-1"></i>Claim $${currentVideo.earning.toFixed(4)}`);
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to process video watch.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                    
                    // Reset button and card state properly
                    const videoCard = $(`.watch-btn[data-video-id="${currentVideo.id}"]`).closest('.card');
                    videoCard.removeClass('border-warning').css('opacity', '1');
                    button.prop('disabled', false).html(`<i class="fas fa-coins me-1"></i>Claim $${currentVideo.earning.toFixed(4)}`);
                }
            });
        });
        
        // Enhanced modal close handling
        $('#videoModal').on('hidden.bs.modal', function() {
            // Clear timer
            if (watchTimer) {
                clearInterval(watchTimer);
                watchTimer = null;
            }
            
            // Stop video
            $('#videoFrame').attr('src', '');
            
            // Reset all states
            watchDuration = 0;
            pauseCount = 0;
            isVideoPlaying = false;
            currentVideo = null;
            
            // Reset modal elements
            $('#watchingIndicator').hide();
            $('#pauseWarning').hide();
            updateProgress();
            updateCurrentTime();
            updateStatus('Video closed', 'secondary');
            
            // Re-enable all non-watched buttons (keep watched ones as "Watched Today")
            $('.watch-btn').not('.btn-success').each(function() {
                const earning = $(this).data('earning');
                $(this).prop('disabled', false)
                       .removeClass('btn-secondary')
                       .addClass('btn-primary')
                       .html(`<i class="fas fa-play me-2"></i>Watch & Earn $${parseFloat(earning).toFixed(4)}`);
            });
            
            // Reset claim button
            $('#claimBtn').prop('disabled', true).html('<i class="fas fa-coins me-1"></i>Claim $<span id="earning">0.00</span>');
        });
        
        // Prevent modal close if video is in progress
        $('#videoModal').on('hide.bs.modal', function(e) {
            if (watchTimer && watchDuration > 0 && watchDuration < requiredTime) {
                e.preventDefault();
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Video in Progress',
                    text: `You've watched ${watchDuration}/${requiredTime} seconds. Close now and lose progress?`,
                    showCancelButton: true,
                    confirmButtonText: 'Continue Watching',
                    cancelButtonText: 'Close & Lose Progress',
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        // Force close
                        $('#videoModal').off('hide.bs.modal').modal('hide');
                        
                        // Re-bind the event for next time
                        setTimeout(() => {
                            $('#videoModal').on('hide.bs.modal', arguments.callee);
                        }, 100);
                    }
                });
            }
        });
        
        // Keyboard shortcut prevention and activity tracking
        $('#videoModal').on('keydown', function(e) {
            // Update last activity time
            lastActiveTime = Date.now();
            
            // Prevent common video control shortcuts
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft' || 
                e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
                e.key === ' ' || // Spacebar
                (e.key === 'k' && e.target.tagName !== 'INPUT') ||
                (e.key === 'j' && e.target.tagName !== 'INPUT') ||
                (e.key === 'l' && e.target.tagName !== 'INPUT') ||
                (e.key === 'm' && e.target.tagName !== 'INPUT')) {
                e.preventDefault();
                updateStatus('Video controls disabled during earning period', 'warning');
                setTimeout(() => {
                    if (isVideoPlaying) {
                        updateStatus('Watching...', 'success');
                    }
                }, 2000);
            }
        });
        
        // Additional activity tracking
        $('#videoModal').on('mousemove click', function() {
            lastActiveTime = Date.now();
        });
        
        // Initialize tooltips and other UI elements
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Page load complete
        console.log('Enhanced Video Gallery System Loaded:', {
            features: ['Duration Tracking', 'Pause Detection', 'Activity Monitoring', 'Watch Validation'],
            version: '2.0',
            database_integration: true
        });
    });
    </script>
    @endpush
</x-smart_layout>
