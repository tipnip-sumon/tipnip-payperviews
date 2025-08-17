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
                        <h4 class="mb-0">
                            <i class="fas fa-play-circle me-2"></i>Watch Videos & Earn Money
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        @if($videos->count() > 0)
                            <div class="row g-4">
                                @foreach($videos as $video)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                                        <div class="card h-100 border-0 shadow-sm">
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
                                                
                                                @auth
                                                    <button class="btn btn-primary w-100 watch-btn"
                                                            data-video-id="{{ $video->id }}"
                                                            data-video-url="{{ $video->video_url }}"
                                                            data-video-title="{{ $video->title }}"
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
                
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe id="videoFrame" src="" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-md-8">
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" id="watchProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Watch for <span id="requiredTime">20</span> seconds to earn money</small>
                        </div>
                        <div class="col-md-4 text-end">
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
        let requiredTime = 20;
        let currentVideo = null;
        
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
                earning: parseFloat(button.data('earning'))
            };
            
            // Disable button
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
            
            // Set modal content
            $('#videoTitle').text(currentVideo.title);
            $('#earning').text(currentVideo.earning.toFixed(4));
            
            // Convert YouTube URL to embed
            let embedUrl = convertToEmbed(currentVideo.url);
            
            // Reset modal
            watchDuration = 0;
            updateProgress();
            $('#claimBtn').prop('disabled', true);
            
            // Load video
            $('#videoFrame').attr('src', embedUrl);
            $('#videoModal').modal('show');
            
            // Start timer after short delay
            setTimeout(startTimer, 2000);
        });
        
        // Convert YouTube URL to embed format
        function convertToEmbed(url) {
            if (url.includes('youtube.com/watch')) {
                const videoId = url.split('v=')[1].split('&')[0];
                return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            }
            if (url.includes('youtu.be/')) {
                const videoId = url.split('/').pop();
                return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            }
            return url;
        }
        
        // Start watch timer
        function startTimer() {
            if (watchTimer) clearInterval(watchTimer);
            
            watchTimer = setInterval(function() {
                watchDuration++;
                updateProgress();
                
                if (watchDuration >= requiredTime) {
                    $('#claimBtn').prop('disabled', false);
                }
            }, 1000);
        }
        
        // Update progress
        function updateProgress() {
            const progress = Math.min(100, (watchDuration / requiredTime) * 100);
            $('#watchProgress').css('width', progress + '%');
        }
        
        // Claim earnings
        $('#claimBtn').click(function() {
            if (watchDuration < requiredTime) {
                Swal.fire('Not Ready!', 'Please watch for at least ' + requiredTime + ' seconds.', 'warning');
                return;
            }
            
            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // Send AJAX request
            $.post('{{ route("video.simple-watch") }}', {
                video_id: currentVideo.id,
                watch_duration: watchDuration,
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                if (response.success) {
                    // Mark button as watched
                    $(`.watch-btn[data-video-id="${currentVideo.id}"]`)
                        .removeClass('btn-primary')
                        .addClass('btn-success')
                        .html('<i class="fas fa-check me-2"></i>Watched')
                        .prop('disabled', true);
                    
                    // Close modal
                    $('#videoModal').modal('hide');
                    
                    // Show success
                    Swal.fire('Success!', `You earned $${currentVideo.earning.toFixed(4)}!`, 'success');
                } else {
                    Swal.fire('Error!', response.message || 'Failed to process', 'error');
                }
            })
            .fail(function() {
                Swal.fire('Error!', 'Network error. Please try again.', 'error');
            })
            .always(function() {
                button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + currentVideo.earning.toFixed(4));
            });
        });
        
        // Clean up when modal closes
        $('#videoModal').on('hidden.bs.modal', function() {
            if (watchTimer) {
                clearInterval(watchTimer);
                watchTimer = null;
            }
            $('#videoFrame').attr('src', '');
            watchDuration = 0;
            
            // Reset any non-watched buttons
            $('.watch-btn').not('.btn-success').each(function() {
                const earning = $(this).data('earning');
                $(this).prop('disabled', false)
                       .removeClass('btn-secondary')
                       .addClass('btn-primary')
                       .html(`<i class="fas fa-play me-2"></i>Watch & Earn $${parseFloat(earning).toFixed(4)}`);
            });
        });
    });
    </script>
    @endpush
</x-smart_layout>
