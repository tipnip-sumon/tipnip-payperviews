<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    @section('content')
        <div class="row mb-4"> 
            <div class="col-md-12">
                <div class="card bg-success text-secondary  bg-gradient border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><i class="fas fa-video"></i> Video Gallery - Earn Money by Watching!</h4>
                                <p class="mb-0">Watch videos and earn money. Each video view adds to your balance!</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <!-- Countdown Timer for New Videos (always present, hidden by default) -->
                                <div id="video-countdown-container" class="mt-2" style="display:none">
                                    <span class="badge bg-info bg-gradient px-3 py-2">
                                        <i class="fas fa-clock"></i>
                                        <span id="countdown-label">New videos in</span>
                                        <span id="video-countdown" class="fw-bold"></span>
                                    </span>
                                </div>
                                @auth
                                    <h5><i class="fas fa-wallet"></i> Your Balance: ${{ number_format(auth()->user()->balance, 4) }}</h5>
                                    <p class="mb-0">Total Video Earnings: ${{ number_format($userTotalEarnings, 4) }}</p>
                                @else
                                    <h5><i class="fas fa-sign-in-alt"></i> Login to Start Earning!</h5>
                                    <a href="{{ route('login') }}" class="btn btn-light btn-sm">Login Now</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card custom-card border-0 shadow">
                    <div class="card-header bg-gradient-success text-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-play-circle text-warning"></i> Watch Videos & Earn Money
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($videos->count() > 0)
                            <div class="row">
                                @foreach($videos as $video)
                                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                        <div class="card video-card h-100 border-0 shadow-sm hover-shadow" data-video-id="{{ $video->id }}">
                                            <div class="position-relative">
                                                <div class="video-container" style="position: relative; height: 250px; border-radius: 0.5rem; overflow: hidden;">
                                                    <iframe 
                                                        width="100%" 
                                                        height="250" 
                                                        src="{{ $video->embed_url }}?enablejsapi=1&origin={{ url('/') }}" 
                                                        title="{{ $video->title }}" 
                                                        frameborder="0" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                                        allowfullscreen
                                                        class="video-iframe"
                                                        style="border-radius: 0.5rem;">
                                                    </iframe>
                                                </div>
                                                
                                                <!-- Earnings Badge -->
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-success bg-gradient shadow-sm">
                                                        <i class="fas fa-dollar-sign text-white"></i> ${{ number_format($video->cost_per_click, 4) }}
                                                    </span>
                                                </div>
                                                
                                                @auth
                                                    @if($video->userViews()->exists())
                                                        <div class="position-absolute top-0 start-0 m-2">
                                                            <span class="badge bg-info bg-gradient shadow-sm">
                                                                <i class="fas fa-check text-white"></i> Watched
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>                            
                            <div class="card-body bg-light">
                                <h6 class="card-title text-dark fw-bold">{{ $video->title }}</h6>
                                @if($video->description)
                                    <p class="card-text small text-muted mb-3">
                                        {{ Str::limit($video->description, 100) }}
                                    </p>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-eye text-primary"></i> {{ number_format($video->views_count) }} views
                                    </small>
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-coins text-warning"></i> ${{ number_format($video->cost_per_click, 2) }} paid
                                    </small>
                                </div>
                                
                                @auth
                                    @if(!$video->userViews()->exists())
                                        <button class="btn btn-primary btn-sm mt-2 watch-btn bg-gradient shadow-sm w-100" 
                                                data-video-id="{{ $video->id }}">
                                            <i class="fas fa-play text-white"></i> Watch & Earn ${{ number_format($video->cost_per_click, 4) }}
                                        </button>
                                    @else
                                        <button class="btn btn-success btn-sm mt-2 bg-gradient shadow-sm w-100" disabled>
                                            <i class="fas fa-check text-white"></i> Already Watched
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mt-2 w-100">
                                        <i class="fas fa-sign-in-alt"></i> Login to Earn
                                    </a>
                                @endauth
                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-video fa-4x text-primary mb-3"></i>
                                <h4 class="text-dark">No Videos Available</h4>
                                <p class="text-muted">Check back later for new videos to watch and earn money!</p>
                                <div class="mt-4">
                                    <span class="badge bg-info bg-gradient px-3 py-2">
                                        <i class="fas fa-clock"></i> Coming Soon
                                    </span>
                                </div>
                            </div>
                        @endif
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-dollar-sign fa-3x text-success"></i>
                        </div>
                        <h4 id="earnings-amount"></h4>
                        <p id="earnings-message"></p>
                        <div class="alert alert-info">
                            <strong>Your Total Balance:</strong> $<span id="total-balance"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                            <i class="fas fa-check"></i> Awesome!
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('server-time.js') }}"></script>
    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .video-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .video-card:hover {
            border-color: #007bff;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
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
        
        .card-body.bg-light {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%) !important;
        }
        
        .video-iframe {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .text-primary {
            color: #007bff !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .fw-bold {
            font-weight: 600 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Countdown Timer for New Videos
            function startCountdown(serverTime, nextResetTime) {
                const countdownEl = document.getElementById('video-countdown');
                const container = document.getElementById('video-countdown-container');
                if (!countdownEl || !container) return;
                container.style.display = '';
                function updateCountdown() {
                    const now = new Date(serverTime.getTime() + (Date.now() - window._clientTimeStart));
                    let diff = Math.floor((nextResetTime - now) / 1000);
                    if (diff < 0) diff = 0;
                    const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                    const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    const s = String(diff % 60).padStart(2, '0');
                    countdownEl.textContent = `${h}:${m}:${s}`;
                    if (diff > 0) {
                        setTimeout(updateCountdown, 1000);
                    } else {
                        countdownEl.textContent = '00:00:00';
                        document.getElementById('countdown-label').textContent = 'New videos available!';
                        setTimeout(() => location.reload(), 2000);
                    }
                }
                updateCountdown();
            }

            // Only show countdown if user reached daily limit
            @auth
            @if(isset($userStats['remaining_views']) && $userStats['remaining_views'] <= 0)
                console.log('Daily limit reached, starting countdown');
                window.getServerTime && window.getServerTime().then(function(serverTimeStr) {
                    if (!serverTimeStr) return;
                    const serverTime = new Date(serverTimeStr.replace(' ', 'T'));
                    window._clientTimeStart = Date.now();
                    // Next reset is at midnight server time
                    const nextReset = new Date(serverTime);
                    nextReset.setHours(24,0,0,0);
                    startCountdown(serverTime, nextReset);
                });
            @endif
            @endauth
            // Handle watch button clicks
            $('.watch-btn').click(function() {
                const videoId = $(this).data('video-id');
                const button = $(this);
                
                // Show loading state
                button.prop('disabled', true);
                button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                recordVideoView(videoId, button);
            });

            // YouTube API integration
            let players = {};
            
            // Initialize YouTube players
            $('iframe[src*="youtube.com"]').each(function() {
                const iframe = this;
                const videoId = $(iframe).closest('.video-card').data('video-id');
                
                // Listen for video end (simplified approach)
                iframe.addEventListener('load', function() {
                    const player = new YT.Player(iframe, {
                        events: {
                            'onStateChange': function(event) {
                                if (event.data === YT.PlayerState.ENDED) {
                                    // Video ended, you can handle it here
                                    console.log('Video ended:', videoId);
                                    // Optionally, you can call recordVideoView here if needed
                                }
                            }
                        }
                    });
                    players[videoId] = player;
                    // Set up video end detection here if needed
                });
            });
        });

        function recordVideoView(videoId, button) {
            $.ajax({
                url: '{{ route("video.record-view", ":id") }}'.replace(':id', videoId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        // Show earnings modal
                        $('#earnings-amount').text('You earned ' + response.message.split('$')[1]);
                        $('#earnings-message').text(response.message);
                        $('#total-balance').text(parseFloat('{{ auth()->user()->balance ?? 0 }}') + parseFloat(response.earned_amount));
                        $('#earningsModal').modal('show');
                        
                        // Update button
                        button.removeClass('btn-primary').addClass('btn-success');
                        button.html('<i class="fas fa-check"></i> Already Watched');
                        
                        // Update balance in header
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                        
                    } else {
                        alert(response.message);
                        // Reset button
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-play"></i> Watch & Earn');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('An error occurred. Please try again.');
                    
                    // Reset button
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-play"></i> Watch & Earn');
                }
            });
        }
    </script>
    @endpush
</x-smart_layout>