@forelse($videos as $index => $video)
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4 video-item" 
         data-title="{{ strtolower($video->title) }}"
         data-aos="fade-up" 
         data-aos-delay="{{ ($index % 6) * 100 }}">
        <div class="card video-card h-100">
            <div class="video-thumbnail">
                <div class="iframe-container" data-embed-url="{{ $video->embed_url }}">
                    <!-- Placeholder for lazy loading -->
                    <div class="iframe-placeholder">
                        <div class="placeholder-content">
                            <i class="fas fa-play-circle fa-3x text-primary mb-3"></i>
                            <h6>{{ $video->title }}</h6>
                            <p class="text-muted">Click to load video</p>
                        </div>
                    </div>
                </div>
                
                <div class="video-overlay">
                    <div class="play-button">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                
                <div class="stats-badge">
                    <i class="fas fa-eye me-1"></i>{{ number_format($video->views_count) }}
                </div>
                
                @if($video->earning_per_view > 0)
                    <div class="stats-badge earning-badge">
                        <i class="fas fa-coins me-1"></i>${{ number_format($video->earning_per_view, 4) }}
                    </div>
                @endif
            </div>
            
            <div class="card-body-modern">
                <h5 class="video-title">{{ $video->title }}</h5>
                <p class="video-description">
                    {{ Str::limit($video->description, 120) }}
                </p>
                
                <div class="video-stats">
                    <div class="row text-center">
                        <div class="col-4 stat-group">
                            <div class="stat-value text-primary">{{ number_format($video->views_count) }}</div>
                            <small class="text-muted">Views</small>
                        </div>
                        <div class="col-4 stat-group">
                            <div class="stat-value text-success">${{ number_format($video->cost_per_click, 2) }}</div>
                            <small class="text-muted">Total Earnings</small>
                        </div>
                        <div class="col-4 stat-group">
                            <div class="stat-value text-warning">${{ number_format($video->earning_per_view, 4) }}</div>
                            <small class="text-muted">Per View</small>
                        </div>
                    </div>
                </div>
                
                <button class="btn btn-gradient watch-btn btn-watch-earn" 
                        data-earning="{{ number_format($video->earning_per_view, 4) }}">
                    <i class="fas fa-play me-2"></i>
                    Watch & Earn ${{ number_format($video->earning_per_view, 4) }}
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-12" data-aos="fade-up">
        <div class="text-center py-5">
            <i class="fas fa-video fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No Videos Available</h3>
            <p class="text-muted mb-4">We're working hard to bring you amazing content. Check back soon!</p>
            <a href="{{ route('register') }}" class="btn btn-gradient btn-modern">
                <i class="fas fa-bell me-2"></i>Get Notified
            </a>
        </div>
    </div>
@endforelse
