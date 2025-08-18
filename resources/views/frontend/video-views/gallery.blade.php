<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle) 
    @section('content') 
        <!-- Server Time Counter Section -->
        <div class="container-fluid mb-2" style="padding-left: 0; padding-right: 0;">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="card border-0 shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%, #f093fb 100%); position: relative;">
                        <!-- Animated Background Pattern -->
                        <div class="position-absolute w-100 h-100" style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 50px 50px; opacity: 0.4; animation: float 20s ease-in-out infinite;"></div>
                        
                        <div class="card-body p-4 position-relative">
                            <div class="row align-items-center">
                                <!-- Left Section - Title and Description -->
                                <div class="col-lg-7 col-md-6 col-12 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3 p-3 rounded-circle position-relative" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.3);">
                                            <i class="fas fa-server fa-xl text-white" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
                                            <div class="position-absolute top-0 start-100 translate-middle">
                                                <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.6rem;">
                                                    <i class="fas fa-circle fa-xs me-1"></i>LIVE
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-white">
                                            <h4 class="fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                Server Time Dashboard
                                            </h4>
                                            <p class="mb-1 opacity-75 small">
                                                <i class="fas fa-clock me-1"></i>
                                                Real-time synchronization active
                                            </p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-success bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                                                    <i class="fas fa-shield-alt me-1"></i>Secure
                                                </span>
                                                <span class="badge bg-success bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                                                    <i class="fas fa-sync-alt me-1"></i>Auto-sync
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Section - Enhanced Timer Card -->
                                <div class="col-lg-5 col-md-6 col-12">
                                    <div id="countdown-timer" class="timer-section">
                                        <div class="card border-0 shadow-lg timer-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 1rem; transform: translateY(-5px);">
                                            <div class="card-header border-0 text-center py-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 1rem 1rem 0 0;">
                                                <div class="d-flex align-items-center justify-content-center mb-2">
                                                    <div class="me-2 p-2 rounded-circle bg-primary bg-opacity-10">
                                                        <i class="fas fa-clock fa-pulse text-primary"></i>
                                                    </div>
                                                    <h6 class="text-dark fw-bold mb-0">Daily Reset Timer</h6>
                                                </div>
                                                <span class="badge bg-success text-white px-3 py-1 rounded-pill" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                    <i class="fas fa-server me-1"></i>Server Time Zone
                                                </span>
                                            </div>
                                            
                                            <div class="card-body p-4 text-center">
                                                <!-- Timer Display -->
                                                <div class="timer-display mb-3">
                                                    <div id="timer-display" class="display-6 fw-bold text-primary mb-2" style="font-family: 'Segoe UI', system-ui; letter-spacing: 2px; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                        Loading...
                                                    </div>
                                                    <div class="timer-labels d-flex justify-content-center gap-4 text-muted small fw-semibold">
                                                        <span class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-hourglass-half mb-1"></i>
                                                            HOURS
                                                        </span>
                                                        <span class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-clock mb-1"></i>
                                                            MINUTES
                                                        </span>
                                                        <span class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-stopwatch mb-1"></i>
                                                            SECONDS
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Enhanced Progress Section -->
                                                <div class="progress-section">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-muted fw-semibold">
                                                            <i class="fas fa-chart-line me-1"></i>
                                                            Daily Progress
                                                        </small>
                                                        <span class="badge bg-primary text-white fw-bold" id="timer-percentage">0%</span>
                                                    </div>
                                                    <div class="progress shadow-sm" style="height: 12px; border-radius: 10px; background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);">
                                                        <div id="timer-progress" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                             role="progressbar" 
                                                             style="width: 0%; border-radius: 10px; background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted mt-2 d-block">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Limits reset at <strong>00:00 UTC</strong>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <!-- Card Footer with Additional Info -->
                                            <div class="card-footer border-0 text-center py-2" style="background: rgba(248, 249, 250, 0.8); border-radius: 0 0 1rem 1rem;">
                                                <div class="row g-2 text-center">
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-globe text-primary"></i>
                                                        </small>
                                                        <small class="fw-semibold text-dark">UTC</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-sync text-success"></i>
                                                        </small>
                                                        <small class="fw-semibold text-dark">Auto</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-shield-check text-warning"></i>
                                                        </small>
                                                        <small class="fw-semibold text-dark">Secure</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Dashboard Section -->
        @auth
        <div class="container-fluid mb-4" style="padding-left: 0; padding-right: 0;">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="card border-0 shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); position: relative;">
                        <!-- Animated Background Pattern -->
                        <div class="position-absolute w-100 h-100" style="background-image: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 1px, transparent 1px), radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 1px, transparent 1px), radial-gradient(circle at 40% 40%, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px; opacity: 0.6; animation: float 15s ease-in-out infinite reverse;"></div>
                        
                        <div class="card-body p-4 position-relative">
                            <div class="row align-items-center">
                                <!-- Left Section - Title and Description -->
                                <div class="col-lg-4 col-md-12 col-12 mb-3 mb-lg-0">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3 p-3 rounded-circle position-relative" style="background: rgba(255,255,255,0.25); backdrop-filter: blur(15px); border: 2px solid rgba(255,255,255,0.4);">
                                            <i class="fas fa-chart-line fa-xl text-white" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></i>
                                            <div class="position-absolute top-0 start-100 translate-middle">
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.6rem;">
                                                    <i class="fas fa-dollar-sign fa-xs me-1"></i>LIVE
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-white">
                                            <h4 class="fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                Income Dashboard
                                            </h4>
                                            <p class="mb-1 opacity-75 small">
                                                <i class="fas fa-coins me-1"></i>
                                                Real-time earnings tracking
                                            </p>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="badge bg-warning bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                                                    <i class="fas fa-trending-up me-1"></i>Active
                                                </span>
                                                <span class="badge bg-warning bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                                                    <i class="fas fa-eye me-1"></i>Live
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Middle Section - Today's Income -->
                                <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                                    <div class="card border-0 shadow-lg income-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 1rem; transform: translateY(-5px);">
                                        <div class="card-header border-0 text-center py-3" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 1rem 1rem 0 0;">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="me-2 p-2 rounded-circle bg-warning bg-opacity-20">
                                                    <i class="fas fa-calendar-day text-warning"></i>
                                                </div>
                                                <h6 class="text-dark fw-bold mb-0">Today's Income</h6>
                                            </div>
                                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; color: white !important;">
                                                <i class="fas fa-clock me-1"></i>{{ date('M d, Y') }}
                                            </span>
                                        </div>
                                        
                                        <div class="card-body p-4 text-center">
                                            <div class="income-display mb-3">
                                                <div class="display-5 fw-bold text-success mb-2" id="today-income-display" style="font-family: 'Segoe UI', system-ui; letter-spacing: 1px; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                    ${{ number_format($userStats['today_earnings'] ?? 0, 4) }}
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-video me-1"></i>
                                                    Today: {{ $userStats['todays_views'] ?? 0 }}/{{ $userStats['daily_limit'] ?? 0 }} videos watched
                                                    @if(isset($userStats['daily_limit']) && isset($userStats['todays_views']))
                                                        @php
                                                            $remaining = max(0, $userStats['daily_limit'] - $userStats['todays_views']);
                                                        @endphp
                                                        @if($remaining > 0)
                                                            <span class="badge bg-light text-dark ms-1">{{ $remaining }} left</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark ms-1">Limit reached</span>
                                                        @endif
                                                    @endif
                                                </small>
                                            </div>
                                            
                                            <div class="progress-section">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted fw-semibold">
                                                        <i class="fas fa-target me-1"></i>
                                                        Daily Progress
                                                    </small>
                                                    @if(isset($userStats['daily_limit']) && $userStats['daily_limit'] > 0)
                                                        @php
                                                            $progress = min(100, (($userStats['todays_views'] ?? 0) / $userStats['daily_limit']) * 100);
                                                        @endphp
                                                        <span class="badge bg-success text-white fw-bold">{{ round($progress) }}%</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white fw-bold">0%</span>
                                                    @endif
                                                </div>
                                                <div class="progress shadow-sm" style="height: 8px; border-radius: 10px; background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);">
                                                    @if(isset($userStats['daily_limit']) && $userStats['daily_limit'] > 0)
                                                        @php
                                                            $progress = min(100, (($userStats['todays_views'] ?? 0) / $userStats['daily_limit']) * 100);
                                                        @endphp
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                             role="progressbar" 
                                                             style="width: {{ $progress }}%; border-radius: 10px; background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);">
                                                        </div>
                                                    @else
                                                        <div class="progress-bar" role="progressbar" style="width: 0%; border-radius: 10px; background: #6c757d;"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Section - Total Income -->
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="card border-0 shadow-lg income-card" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: 1rem; transform: translateY(-5px);">
                                        <div class="card-header border-0 text-center py-3" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); border-radius: 1rem 1rem 0 0;">
                                            <div class="d-flex align-items-center justify-content-center mb-2">
                                                <div class="me-2 p-2 rounded-circle bg-info bg-opacity-20">
                                                    <i class="fas fa-chart-bar text-info"></i>
                                                </div>
                                                <h6 class="text-dark fw-bold mb-0">Total Income</h6>
                                            </div>
                                            <span class="badge bg-info text-white px-3 py-1 rounded-pill" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;">
                                                <i class="fas fa-infinity me-1"></i>All Time
                                            </span>
                                        </div>
                                        
                                        <div class="card-body p-4 text-center">
                                            <div class="income-display mb-3">
                                                <div class="display-5 fw-bold text-primary mb-2" id="total-income-display" style="font-family: 'Segoe UI', system-ui; letter-spacing: 1px; text-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                    ${{ number_format($userStats['total_earnings'] ?? 0, 4) }}
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-wallet me-1"></i>
                                                    Current Balance: ${{ number_format(auth()->user()->balance ?? 0, 4) }}
                                                </small>
                                            </div>
                                            
                                            <div class="stats-section">
                                                <div class="row g-2 text-center">
                                                    <div class="col-6">
                                                        <div class="stat-item p-2 rounded" style="background: rgba(17, 153, 142, 0.1);">
                                                            <div class="fw-bold text-success">{{ $userStats['total_videos'] ?? 0 }}</div>
                                                            <small class="text-muted">Total Videos</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-item p-2 rounded" style="background: rgba(56, 239, 125, 0.1);">
                                                            <div class="fw-bold text-success">${{ number_format($userStats['earning_rate'] ?? 0.001, 4) }}</div>
                                                            <small class="text-muted">Per Video</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endauth

        <!-- Video Gallery Section -->
        <div class="row" id="videos-section">
            <div class="col-12">
                <div class="card custom-card border-0 shadow-lg">
                    <div class="card-header bg-gradient-primary text-white border-0 py-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h4 class="card-title mb-1 fw-bold">
                                    <i class="fas fa-play-circle text-warning me-2"></i>Watch Videos & Earn Money
                                </h4>
                                <p class="mb-0 text-white-75">Choose any video below to start earning instantly</p>
                            </div>
                            @if($videos->count() > 0)
                                <div class="text-end">
                                    <span class="badge bg-light text-primary px-3 py-2 fs-6" id="video-count-badge">
                                        {{ $videos->count() }} Videos Available
                                    </span>
                                </div>
                            @endif
                        </div>

                    </div>
                    <div class="card-body p-4">
                        @if($videos->count() > 0)
                            <div class="row g-4">
                                @foreach($videos as $video)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                                        <div class="card video-card h-100 border-0 shadow-sm hover-shadow position-relative" data-video-id="{{ $video->id }}">
                                            <!-- Video Thumbnail -->
                                            <div class="position-relative overflow-hidden">
                                                <div class="video-thumbnail position-relative" style="height: 220px; border-radius: 0.75rem 0.75rem 0 0;">
                                                    @if(str_contains($video->video_url, 'youtube.com') || str_contains($video->video_url, 'youtu.be'))
                                                        @php
                                                            $videoId = '';
                                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video->video_url, $matches)) {
                                                                $videoId = $matches[1];
                                                            }
                                                        @endphp
                                                        @if($videoId)
                                                            <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg" 
                                                                 class="video-thumb-img w-100 h-100" 
                                                                 style="object-fit: cover; transition: transform 0.3s ease;" 
                                                                 alt="{{ $video->title }}">
                                                            <div class="play-overlay position-absolute top-50 start-50 translate-middle">
                                                                <div class="play-button-wrapper p-3 rounded-circle bg-dark bg-opacity-75">
                                                                    <i class="fas fa-play text-white" style="font-size: 1.5rem; margin-left: 3px;"></i>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                                <i class="fas fa-play-circle fa-3x text-primary"></i>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                            <div class="text-center">
                                                                <i class="fas fa-play-circle fa-3x text-primary mb-2"></i>
                                                                <p class="text-muted small mb-0">Video Ready</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                                
                                                <!-- Video Badges -->
                                                <div class="video-badges">
                                                    <!-- Earnings Badge -->
                                                    <div class="position-absolute top-0 end-0 m-3">
                                                        <span class="badge bg-success bg-gradient shadow-lg px-3 py-2 rounded-pill">
                                                            <i class="fas fa-dollar-sign text-white me-1"></i>
                                                            <strong>${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}</strong>
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- New Video Badge -->
                                                    <div class="position-absolute top-0 start-0 m-3">
                                                        <span class="badge bg-primary bg-gradient shadow-lg px-3 py-2 rounded-pill">
                                                            <i class="fas fa-star text-white me-1"></i>
                                                            <strong>New</strong>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>                            
                            
                            <!-- Card Body -->
                            <div class="card-body p-4">
                                <!-- Video Title -->
                                <h5 class="card-title text-dark fw-bold mb-3" style="line-height: 1.4;">
                                    {{ $video->title }}
                                </h5>
                                
                                <!-- Video Description -->
                                @if($video->description)
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">
                                        {{ Str::limit($video->description, 120) }}
                                    </p>
                                @endif
                                
                                <!-- Video Stats -->
                                <div class="video-stats mb-4">
                                    <div class="row g-2 text-center">
                                        <div class="col-4" style="display: none">
                                            <div class="stat-item p-2 rounded bg-light">
                                                <i class="fas fa-eye text-primary mb-1 d-block"></i>
                                                <small class="text-muted d-block">Views</small>
                                                <strong class="text-dark small">{{ number_format($video->views_count) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item p-2 rounded bg-light">
                                                <i class="fas fa-clock text-info mb-1 d-block"></i>
                                                <small class="text-muted d-block">Duration</small>
                                                <strong class="text-dark small">{{ $video->duration ?? 120 }}s</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item p-2 rounded bg-success bg-opacity-10">
                                                <i class="fas fa-coins text-warning mb-1 d-block"></i>
                                                <small class="text-muted d-block">Earning</small>
                                                <strong class="text-success small">${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                @auth
                                    <button class="btn btn-primary btn-lg watch-btn bg-gradient shadow-sm w-100 position-relative" 
                                            data-video-id="{{ $video->id }}"
                                            data-video-url="{{ $video->video_url ?? '' }}"
                                            data-video-title="{{ $video->title ?? 'Video' }}"
                                            data-earning="{{ $userStats['earning_rate'] ?? $video->cost_per_click ?? 0.001 }}"
                                            data-video-category="{{ $video->category ?? 'general' }}"
                                            data-video-views="{{ $video->views_count ?? 0 }}"
                                            data-duration="{{ $video->duration ?? 120 }}"
                                            style="border-radius: 0.75rem; padding: 12px 20px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-play me-2"></i>
                                            <span class="fw-bold">Watch & Earn ${{ number_format($userStats['earning_rate'] ?? $video->cost_per_click, 4) }}</span>
                                        </div>
                                        <div class="button-shine position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%); border-radius: 0.75rem; opacity: 0; transition: opacity 0.3s ease;"></div>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg w-100" style="border-radius: 0.75rem; padding: 12px 20px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            <span class="fw-bold">Login to Earn</span>
                                        </div>
                                    </a>
                                @endauth
                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                @if(isset($message) && $message)
                                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                    <h4 class="text-dark">{{ $message }}</h4>
                                    @if(!$hasActiveInvestment)
                                        <p class="text-muted">You can earn money by watching videos! Make a deposit to unlock more videos and higher earning rates.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('invest.index') }}" class="btn btn-primary btn-lg">
                                                <i class="fas fa-rocket"></i> View Deposit Plans
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
                                        <p class="text-muted">Upgrade your plan to access more videos and earn higher rates!</p>
                                        <div class="mt-4">
                                            <a href="{{ route('invest.index') }}" class="btn btn-primary btn-lg">
                                                <i class="fas fa-upgrade"></i> Upgrade Plan
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <i class="fas fa-video fa-4x text-primary mb-3"></i>
                                    <h4 class="text-dark">No Videos Available</h4>
                                    <p class="text-muted">Check back later for new videos to watch and earn money!</p>
                                    <div class="mt-4">
                                        <span class="badge bg-info bg-gradient px-3 py-2">
                                            <i class="fas fa-clock"></i> Coming Soon
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
                                        <div class="alert alert-info mb-2" id="video-status-alert">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Watch at least <strong id="minimum-watch-time">20 seconds</strong> to earn <strong id="modal-earning">$0.00</strong>
                                            <div id="video-status" class="mt-1" style="display: none;">
                                                <small class="text-warning">
                                                    <i class="fas fa-pause me-1"></i><span id="pause-reason">Video paused - switch back to this tab to continue</span>
                                                </small>
                                            </div>
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
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            <span class="text-muted" id="modal-video-duration">0s</span>
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
                            <strong>Your Total Earnings:</strong> $<span id="total-balance"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="continue-watching-btn" data-bs-dismiss="modal">
                            <i class="fas fa-check"></i> Continue Watching
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
    <!-- Video Gallery Instant Refresh Styles -->
    <style>
        .watch-btn.clicked-processing {
            pointer-events: none;
            opacity: 0.8;
            transform: scale(0.98);
            transition: all 0.3s ease;
        }
        
        .watch-btn.watched {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            border-color: #28a745 !important;
            color: white !important;
            cursor: not-allowed !important;
            opacity: 0.9;
        }
        
        .video-card.watched-video {
            opacity: 0.7;
            transform: scale(0.98);
            transition: all 0.5s ease;
        }
        
        .earnings-notification {
            animation: bounceIn 0.6s ease-out;
        }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .income-update-highlight {
            animation: highlightUpdate 2s ease-out;
        }
        
        @keyframes highlightUpdate {
            0% { background-color: rgba(40, 167, 69, 0.1); }
            50% { background-color: rgba(40, 167, 69, 0.3); }
            100% { background-color: transparent; }
        }
    </style>
    
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('server-time.js') }}"></script>
    
    <!-- Wait for jQuery to load -->
    <script>
        // Development mode check
        const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        
        // Ensure jQuery is loaded before proceeding
        if (typeof $ === 'undefined') {
            if (isDevelopment) console.error('jQuery not loaded properly');
            document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');
        }
        
        // Ensure CSRF token is available
        $(document).ready(function() {
            // FIREFOX CACHE FIX - Add cache control and page reload detection
            // Force no-cache for Firefox and other browsers
            if (window.performance) {
                // Check if page was loaded from cache
                if (performance.navigation.type === 1) {
                    console.log('Page reloaded - checking for updated video state');
                    
                    // Add cache-busting parameter to force fresh data
                    const currentUrl = new URL(window.location);
                    if (!currentUrl.searchParams.has('refresh')) {
                        currentUrl.searchParams.set('refresh', Date.now());
                        window.location.replace(currentUrl.toString());
                        return;
                    }
                }
            }
            
            // Add cache control meta tags dynamically for Firefox
            $('head').append('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">');
            $('head').append('<meta http-equiv="Pragma" content="no-cache">');
            $('head').append('<meta http-equiv="Expires" content="0">');
            
            // VALIDATE VIDEO STATE FROM SERVER - Firefox cache fix
            console.log('Validating video state from server...');
            
            // Get current video count from server data
            const serverVideoCount = {{ $videos->count() }};
            const displayedVideoCount = $('.video-card').length;
            
            console.log('Server video count:', serverVideoCount);
            console.log('Displayed video count:', displayedVideoCount);
            
            // If counts don't match, there's a cache issue
            if (serverVideoCount !== displayedVideoCount) {
                console.warn('Video count mismatch detected - forcing page refresh');
                setTimeout(() => {
                    window.location.reload(true); // Force reload from server
                }, 1000);
                return;
            }
            
            // Validate each video exists in server data - remove cached videos not in server list
            const serverVideoIds = @json($videos->pluck('id')->toArray());
            $('.video-card').each(function() {
                const videoId = parseInt($(this).data('video-id'));
                if (!serverVideoIds.includes(videoId)) {
                    console.log('Removing cached video not in server list:', videoId);
                    $(this).closest('.col-xl-3, .col-lg-4, .col-md-6, .col-sm-6, .col-12').fadeOut(500, function() {
                        $(this).remove();
                        // Update video count badge if function exists
                        if (typeof updateVideoCountBadge === 'function') {
                            updateVideoCountBadge();
                        }
                    });
                }
            });
            
            // Ensure CSRF meta tag exists
            if (!$('meta[name="csrf-token"]').length) {
                $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
                if (isDevelopment) console.log('CSRF meta tag added to head');
            }
            
            // Get fresh CSRF token for this session
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (isDevelopment) console.log('CSRF token loaded:', csrfToken ? 'Found' : 'Missing');
            
            // Set up global CSRF token for AJAX requests with error handling
            if (csrfToken) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (isDevelopment) console.log('Global AJAX CSRF token configured');
            } else {
                if (isDevelopment) console.error('CSRF token not available for AJAX setup');
            }
            
            // REAL-TIME SERVER VALIDATION FOR FIREFOX CACHE FIX
            // Cross-check with server to ensure data is fresh
            $.ajax({
                url: '{{ route("user.video-views.validate-count") }}',
                type: 'GET',
                cache: false,
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                success: function(response) {
                    console.log('Server validation response:', response);
                    
                    if (response.video_count !== serverVideoCount) {
                        console.warn('Server count changed after page load:', {
                            page_load_count: serverVideoCount,
                            current_server_count: response.video_count,
                            page_load_id: response.page_load_id
                        });
                        
                        // Force page refresh to get updated state
                        setTimeout(() => {
                            console.log('Refreshing page to sync with server state');
                            window.location.reload(true);
                        }, 2000);
                    } else {
                        console.log('Video state validated - browser and server in sync');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Video validation failed:', error);
                    // Continue without validation rather than breaking functionality
                }
            });
        });
    </script>
    
    <!-- Multiple Tab Detection Script -->
    <script>
        $(document).ready(function() {
            // Ensure jQuery is available
            if (typeof $ === 'undefined') {
                if (isDevelopment) console.error('jQuery not available for tab detection');
                return;
            }
            
            // Generate unique tab ID for this tab
            const tabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            // Store tab ID in sessionStorage (unique per tab)
            sessionStorage.setItem('currentTabId', tabId);
            
            // Add tab ID to existing AJAX setup (don't override CSRF token)
            const originalBeforeSend = $.ajaxSettings.beforeSend;
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    // Call original beforeSend if it exists (preserves CSRF token)
                    if (originalBeforeSend) {
                        originalBeforeSend.call(this, xhr, settings);
                    }
                    // Add tab ID header
                    xhr.setRequestHeader('X-Tab-ID', tabId);
                }
            });
            
            // Add tab ID to any forms on the page
            $('form').each(function() {
                if (!$(this).find('input[name="tab_id"]').length) {
                    $(this).append('<input type="hidden" name="tab_id" value="' + tabId + '">');
                }
            });
            
            // Periodically check tab status
            setInterval(function() {
                checkTabStatus();
            }, 30000); // Check every 30 seconds
            
            // Make tabId available globally for checkTabStatus function
            window.currentTabId = tabId;
        });
        
        // Track when tab becomes visible/hidden
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (isDevelopment) console.log('Tab hidden - pausing activity');
            } else {
                if (isDevelopment) console.log('Tab visible - resuming activity');
                // Check if we're still the active tab
                checkTabStatus();
            }
        });
        
        // Cleanup tab when page unloads
        window.addEventListener('beforeunload', function() {
            // Use sendBeacon for reliable cleanup
            if (navigator.sendBeacon && window.currentTabId) {
                const formData = new FormData();
                formData.append('tab_id', window.currentTabId);
                
                // Get CSRF token with fallback
                let csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                } else {
                    // Fallback: try to get from a hidden input
                    const tokenInput = document.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        formData.append('_token', tokenInput.value);
                    }
                }
                
                navigator.sendBeacon('{{ route("user.tab-cleanup") }}', formData);
            }
        });
        
        // Function to check tab status
        function checkTabStatus() {
            if (!window.currentTabId) return;
            
            fetch('{{ route("user.video-views.gallery") }}', {
                method: 'HEAD',
                headers: {
                    'X-Tab-ID': window.currentTabId,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).catch(function(error) {
                if (error.status === 403) {
                    showMultipleTabWarning();
                }
            });
        }
        
        // Show warning for multiple tabs
        function showMultipleTabWarning() {
            Swal.fire({
                icon: 'warning',
                title: 'Multiple Tabs Detected!',
                text: 'Video viewing is restricted to one tab at a time. Please close other tabs.',
                confirmButtonText: 'Go to Dashboard',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '{{ route("user.dashboard") }}';
            });
        }
        
    </script>
    
    <script>
        // Check if server-time.js loaded correctly
        if (typeof window.getServerTime !== 'function') {
            if (isDevelopment) console.error('server-time.js not loaded properly, adding fallback implementation');
            // Fallback implementation
            window.getServerTime = async function() {
                try {
                    const response = await fetch('/api/server-time');
                    if (!response.ok) throw new Error('Failed to fetch server time');
                    const data = await response.json();
                    return data.server_time;
                } catch (e) {
                    if (isDevelopment) console.error('Error in fallback getServerTime:', e);
                    return null;
                }
            };
        }
        
        // Force show timer immediately when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (isDevelopment) console.log('DOM loaded - forcing timer visibility');
            var timerElement = document.getElementById('countdown-timer');
            if (timerElement) {
                timerElement.style.display = 'block';
                if (isDevelopment) console.log('Timer element found and display set to block');
                
                // Start a temporary local timer immediately while waiting for server time
                function tempUpdateTimer() {
                    var now = new Date();
                    var tomorrow = new Date(now);
                    tomorrow.setHours(24, 0, 0, 0);
                    var diff = Math.floor((tomorrow - now) / 1000);
                    
                    var h = String(Math.floor(diff / 3600)).padStart(2, '0');
                    var m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    var s = String(diff % 60).padStart(2, '0');
                    
                    document.getElementById('timer-display').innerHTML = h + '<span style="opacity: 0.7; color: #34495e;">:</span>' + m + '<span style="opacity: 0.7; color: #34495e;">:</span>' + s;
                    
                    // Update progress bar
                    var totalSeconds = 24 * 60 * 60;
                    var percentComplete = 100 - ((diff / totalSeconds) * 100);
                    document.getElementById('timer-progress').style.width = Math.max(0, percentComplete) + '%';
                    document.getElementById('timer-percentage').textContent = Math.round(Math.max(0, percentComplete)) + '%';
                }
                
                // Run immediately and then start the interval
                tempUpdateTimer();
                var tempTimerInterval = setInterval(tempUpdateTimer, 1000);
                
                // Replace with server time after a short delay
                setTimeout(function() {
                    clearInterval(tempTimerInterval);
                    if (isDevelopment) console.log('Temporary timer stopped, initializing server time countdown');
                }, 3000);
            } else {
                if (isDevelopment) console.error('Timer element not found!');
            }
        });
        
        // Server-time based countdown timer with improved UI
        $(document).ready(function() {
            if (isDevelopment) console.log('Gallery page loaded, initializing countdown timer...');
            
            // Initialize global variables for video watching
            window.currentVideoId = null;
            window.watchDuration = 0;
            window.watchTimer = null;
            window.earningAmount = 0;
            window.minimumWatchTime = 20; // Default minimum watch time
            window.videoWasPaused = false; // Track video pause state
            window.isTabActive = true; // Track tab visibility state
            window.isWindowFocused = true; // Track window focus state
            window.userInteractionDetected = true; // Track recent user interaction
            
            // Initialize video count badge to ensure it's accurate
            setTimeout(function() {
                updateVideoCountBadge();
            }, 100);
            
            // Use the global serverTimeOffset from server-time.js (don't redeclare it)
            // serverTimeOffset is already declared in server-time.js
            
            // Initialize video watching functionality
            
            // Initialize timer based on server time
            initializeServerTime();
            
            // Get and set up server time
            async function initializeServerTime() {
                
                try {
                    // Try to get server time (this also calculates and stores serverTimeOffset automatically)
                    const serverTimeStr = await window.getServerTime();
                    if (!serverTimeStr) {
                        throw new Error('Failed to get server time');
                    }
                    
                    // Parse server time (offset is already calculated by getServerTime function)
                    const serverTime = new Date(serverTimeStr);
                    // Note: serverTimeOffset is already set by getServerTime() function in server-time.js
                    
                    // Calculate next midnight on server time
                    const nextResetTime = new Date(serverTime);
                    nextResetTime.setHours(24, 0, 0, 0);
                    
                    // Always show the timer for server time display
                    $('#countdown-timer').show();
                    startServerTimeCountdown(nextResetTime);
                    updateProgressBar(nextResetTime);
                    
                    isTimerInitialized = true;
                } catch (error) {
                    if (isDevelopment) console.error('Error initializing server time:', error);
                    fallbackToLocalTimer();
                }
            }
            
            // Start the server-time based countdown
            function startServerTimeCountdown(nextResetTime) {
                
                // Make sure the timer is visible
                $('#countdown-timer').show();
                
                function updateTimer() {
                    // Get current time using server offset
                    const now = new Date();
                    const adjustedNow = new Date(now.getTime() + serverTimeOffset);
                    
                    // Calculate difference in seconds until next reset
                    const diff = Math.floor((nextResetTime - adjustedNow) / 1000);
                    
                    if (diff <= 0) {
                        // Time's up - reload page to get new videos
                        $('#timer-display').text('00:00:00');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }
                    
                    // Format time
                    const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    const seconds = String(diff % 60).padStart(2, '0');
                    
                    // Update display with enhanced formatting
                    $('#timer-display').html(`${hours}<span style="opacity: 0.7; color: #34495e;">:</span>${minutes}<span style="opacity: 0.7; color: #34495e;">:</span>${seconds}`);
                    
                    // Update progress bar every second
                    updateProgressBarWidth(diff, nextResetTime);
                    
                    // Run again in 1 second
                    setTimeout(updateTimer, 1000);
                }
                
                // Start countdown immediately
                updateTimer();
            }
            
            // Set up the initial progress bar
            function updateProgressBar(nextResetTime) {
                const now = new Date();
                const adjustedNow = new Date(now.getTime() + serverTimeOffset);
                const totalSeconds = 24 * 60 * 60; // Seconds in a day
                const remainingSeconds = Math.floor((nextResetTime - adjustedNow) / 1000);
                const percentComplete = 100 - ((remainingSeconds / totalSeconds) * 100);
                
                $('#timer-progress').css('width', percentComplete + '%');
            }
            
            // Update progress bar width during countdown
            function updateProgressBarWidth(remainingSeconds, nextResetTime) {
                const totalSeconds = 24 * 60 * 60; // Seconds in a day
                const percentComplete = 100 - ((remainingSeconds / totalSeconds) * 100);
                
                // Update progress bar
                $('#timer-progress').css('width', Math.max(0, percentComplete) + '%');
                
                // Update percentage display
                $('#timer-percentage').text(Math.round(Math.max(0, percentComplete)) + '%');
            }
            
            // Fallback to local time if server time fails
            function fallbackToLocalTimer() {
                
                // Always show the timer for server time display
                $('#countdown-timer').show();
                startLocalTimeCountdown();
            }
            
            // Start a local-time based countdown as fallback
            function startLocalTimeCountdown() {
                
                function updateLocalTimer() {
                    // Get current time
                    const now = new Date();
                    
                    // Set target time to next midnight
                    const tomorrow = new Date(now);
                    tomorrow.setHours(24, 0, 0, 0);
                    
                    // Calculate difference in seconds
                    const diff = Math.floor((tomorrow - now) / 1000);
                    
                    if (diff <= 0) {
                        // Time's up - reload page to get new videos
                        $('#timer-display').text('00:00:00');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }
                    
                    // Format time
                    const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                    const seconds = String(diff % 60).padStart(2, '0');
                    
                    // Update display with enhanced formatting
                    $('#timer-display').html(`${hours}<span style="opacity: 0.7; color: #34495e;">:</span>${minutes}<span style="opacity: 0.7; color: #34495e;">:</span>${seconds}`);
                    
                    // Update progress bar
                    const totalSeconds = 24 * 60 * 60; // Seconds in a day
                    const percentComplete = 100 - ((diff / totalSeconds) * 100);
                    $('#timer-progress').css('width', Math.max(0, percentComplete) + '%');
                    $('#timer-percentage').text(Math.round(Math.max(0, percentComplete)) + '%');
                    
                    // Run again in 1 second
                    setTimeout(updateLocalTimer, 1000);
                }
                
                // Start the local timer immediately
                updateLocalTimer();
            }
            
            // Content Slider Functionality (Top Section)
            let currentContentSlide = 0;
            const totalContentSlides = 3; // Hero, Earnings, Timer
            let isContentSliding = false;
            let contentAutoSlideInterval = null;
            
            // Initialize content slider
            function initializeContentSlider() {
                console.log('Initializing content slider');
                currentContentSlide = 0; // Reset to first slide
                
                // Set initial active states
                updateContentSliderDisplay();
                
                // Auto slide every 10 seconds
                startContentAutoSlide();
                
                // Pause auto slide on hover
                $('.content-slider-container').hover(
                    function() { 
                        console.log('Pausing auto slide on hover');
                        pauseContentAutoSlide(); 
                    },
                    function() { 
                        console.log('Resuming auto slide after hover');
                        startContentAutoSlide(); 
                    }
                );
            }
            
            // Update content slider display
            function updateContentSliderDisplay() {
                const translateX = -currentContentSlide * 100;
                console.log('Updating slider display - slide:', currentContentSlide, 'translateX:', translateX);
                $('#contentSlider').css('transform', `translateX(${translateX}%)`);
                
                // Update dots
                $('.content-dot').removeClass('active').removeClass('bg-white').addClass('bg-white').css('opacity', '0.5');
                $('.content-dot[data-slide="' + currentContentSlide + '"]').addClass('active').css('opacity', '1');
                
                // Show/hide timer based on current slide
                if (currentContentSlide === 2) {
                    // Timer slide is active, show timer
                    $('#countdown-timer').show();
                } else {
                    // Other slides, hide timer if needed based on user stats
                    @auth
                    @if(isset($userStats['remaining_views']) && $userStats['remaining_views'] <= 0)
                        $('#countdown-timer').show();
                    @else
                        $('#countdown-timer').hide();
                    @endif
                    @else
                        $('#countdown-timer').hide();
                    @endauth
                }
            }
            
            // Go to specific content slide
            function goToContentSlide(slideIndex) {
                if (isContentSliding || slideIndex < 0 || slideIndex >= totalContentSlides) {
                    console.log('Cannot change slide - sliding in progress or invalid index:', slideIndex);
                    return;
                }
                
                console.log('Going to slide:', slideIndex, 'from:', currentContentSlide);
                isContentSliding = true;
                currentContentSlide = slideIndex;
                updateContentSliderDisplay();
                
                setTimeout(() => {
                    isContentSliding = false;
                    console.log('Slide transition complete');
                }, 500);
            }
            
            // Next content slide
            function nextContentSlide() {
                const nextIndex = (currentContentSlide + 1) % totalContentSlides;
                console.log('Next slide - current:', currentContentSlide, 'next:', nextIndex);
                goToContentSlide(nextIndex);
            }
            
            // Previous content slide
            function prevContentSlide() {
                const prevIndex = (currentContentSlide - 1 + totalContentSlides) % totalContentSlides;
                console.log('Previous slide - current:', currentContentSlide, 'prev:', prevIndex);
                goToContentSlide(prevIndex);
            }
            
            // Start content auto slide
            function startContentAutoSlide() {
                clearInterval(contentAutoSlideInterval);
                contentAutoSlideInterval = setInterval(() => {
                    console.log('Auto advancing to next slide');
                    nextContentSlide();
                }, 10000); // 10 seconds
                console.log('Auto slide started');
            }
            
            // Pause content auto slide
            function pauseContentAutoSlide() {
                clearInterval(contentAutoSlideInterval);
                console.log('Auto slide paused');
            }
            
            // Content slider event handlers
            $(document).on('click', '#contentNextBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Next button clicked, current slide:', currentContentSlide);
                nextContentSlide();
                pauseContentAutoSlide();
                setTimeout(startContentAutoSlide, 8000); // Resume after 8 seconds
            });
            
            $(document).on('click', '#contentPrevBtn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Previous button clicked, current slide:', currentContentSlide);
                prevContentSlide();
                pauseContentAutoSlide();
                setTimeout(startContentAutoSlide, 8000); // Resume after 8 seconds
            });
            
            // Content dot navigation
            $(document).on('click', '.content-dot', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const slideIndex = parseInt($(this).data('slide'));
                console.log('Dot clicked, going to slide:', slideIndex);
                goToContentSlide(slideIndex);
                pauseContentAutoSlide();
                setTimeout(startContentAutoSlide, 8000); // Resume after 8 seconds
            });
            
            // Keyboard navigation for content slider
            $(document).keydown(function(e) {
                if ($('.content-slider-container').is(':visible')) {
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        prevContentSlide();
                    } else if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        nextContentSlide();
                    }
                }
            });
            
            // Touch/swipe support for content slider (mobile)
            let contentTouchStartY = 0;
            let contentTouchEndY = 0;
            
            $('.content-slider-container').on('touchstart', function(e) {
                contentTouchStartY = e.originalEvent.touches[0].clientY;
            });
            
            $('.content-slider-container').on('touchmove', function(e) {
                // Allow some movement for content slider
            });
            
            $('.content-slider-container').on('touchend', function(e) {
                contentTouchEndY = e.originalEvent.changedTouches[0].clientY;
                handleContentSwipe();
            });
            
            function handleContentSwipe() {
                const swipeThreshold = 50;
                const swipeDistance = contentTouchStartY - contentTouchEndY;
                
                if (Math.abs(swipeDistance) > swipeThreshold) {
                    if (swipeDistance > 0) {
                        // Swipe up - next slide
                        nextContentSlide();
                    } else {
                        // Swipe down - previous slide
                        prevContentSlide();
                    }
                    pauseContentAutoSlide();
                    setTimeout(startContentAutoSlide, 8000);
                }
            }
            
            // Initialize content slider when page loads
            $(document).ready(function() {
                console.log('Document ready - initializing content slider');
                // Reset all sliding states
                currentContentSlide = 0;
                isContentSliding = false;
                
                // Wait for DOM elements to be fully rendered
                setTimeout(function() {
                    console.log('Starting content slider initialization');
                    initializeContentSlider();
                }, 100); // Small delay to ensure DOM is ready
                
                // Also initialize on window load as backup
                $(window).on('load', function() {
                    console.log('Window loaded - backup slider initialization');
                    if (!contentAutoSlideInterval) {
                        initializeContentSlider();
                    }
                });
            });
            
            // Function to instantly update income dashboard
            function updateIncomeDashboardInstantly(response) {
                try {
                    // Extract values with fallbacks
                    const newBalance = parseFloat(response.total_earnings || response.user_balance || 0);
                    const currentBalance = parseFloat(response.user_balance || newBalance);
                    const todayEarnings = parseFloat(response.today_earnings || 0);
                    const earnedAmount = parseFloat(response.earned_amount || 0);
                    
                    // Update balance in header area
                    $('h5').filter(function() {
                        return $(this).text().includes('Your Balance:');
                    }).html('<i class="fas fa-wallet"></i> Your Balance: $' + currentBalance.toFixed(4));
                    
                    // Update total video earnings if exists
                    $('p').filter(function() {
                        return $(this).text().includes('Total Video Earnings:');
                    }).html('Total Video Earnings: $' + newBalance.toFixed(4));
                    
                    // UPDATE INCOME DISPLAYS - Real-time income dashboard updates
                    // Update today's income display with highlight animation
                    $('#today-income-display').text('$' + todayEarnings.toFixed(4)).addClass('income-update-highlight');
                    
                    // Update total income display with highlight animation
                    $('#total-income-display').text('$' + newBalance.toFixed(4)).addClass('income-update-highlight');
                    
                    // Remove highlight class after animation
                    setTimeout(() => {
                        $('#today-income-display, #total-income-display').removeClass('income-update-highlight');
                    }, 2000);
                    
                    // Update current balance in total income card
                    $('#total-income-display').siblings('small').html('<i class="fas fa-wallet me-1"></i>Current Balance: $' + currentBalance.toFixed(4));
                    
                    // INSTANT VIDEO COUNT UPDATE - Update today's video count immediately
                    const todaysViewsElement = $('small').filter(function() {
                        return $(this).text().includes('Today:') && $(this).text().includes('videos watched');
                    });
                    
                    if (todaysViewsElement.length > 0) {
                        // Extract current counts from server response or calculate
                        const newTodaysViews = parseInt(response.todays_views) || (parseInt('{{ $userStats['todays_views'] ?? 0 }}') + 1);
                        const dailyLimit = parseInt('{{ $userStats['daily_limit'] ?? 0 }}');
                        const remainingViews = Math.max(0, dailyLimit - newTodaysViews);
                        
                        // Update the video count text
                        let updatedText = `Today: ${newTodaysViews}/${dailyLimit} videos watched`;
                        
                        // Update remaining views badge
                        if (remainingViews > 0) {
                            updatedText += ` <span class="badge bg-light text-dark ms-1">${remainingViews} left</span>`;
                        } else {
                            updatedText += ` <span class="badge bg-warning text-dark ms-1">Limit reached</span>`;
                        }
                        
                        // Apply the update
                        todaysViewsElement.html(updatedText);
                        
                        // Update today's income progress bar
                        if (dailyLimit > 0) {
                            const progressPercentage = Math.min(100, (newTodaysViews / dailyLimit) * 100);
                            
                            // Find and update progress bar in today's income section
                            $('.progress-bar').each(function() {
                                const parentCard = $(this).closest('.income-card');
                                if (parentCard.find('h6:contains("Today\'s Income")').length > 0) {
                                    $(this).css('width', progressPercentage + '%');
                                    
                                    // Update progress percentage badge
                                    parentCard.find('.badge.bg-success').text(Math.round(progressPercentage) + '%');
                                }
                            });
                        }
                        
                        // If daily limit reached, show countdown timer immediately
                        if (remainingViews <= 0) {
                            $('#countdown-timer').show();
                            
                            // Start countdown if not already running
                            if (typeof window.getServerTime === 'function') {
                                window.getServerTime().then(serverTimeStr => {
                                    if (serverTimeStr) {
                                        const serverTime = new Date(serverTimeStr);
                                        const nextResetTime = new Date(serverTime);
                                        nextResetTime.setHours(24, 0, 0, 0);
                                        
                                        // Update timer display
                                        const now = new Date();
                                        const adjustedNow = new Date(now.getTime() + (serverTime.getTime() - now.getTime()));
                                        const diff = Math.floor((nextResetTime - adjustedNow) / 1000);
                                        
                                        if (diff > 0) {
                                            const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
                                            const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                                            const seconds = String(diff % 60).padStart(2, '0');
                                            $('#timer-display').text(`${hours}:${minutes}:${seconds}`);
                                        }
                                    }
                                }).catch(() => {
                                    // Fallback to local time
                                });
                            }
                        }
                    }
                    
                    console.log('Income dashboard updated instantly:', {
                        todayEarnings: todayEarnings,
                        totalEarnings: newBalance,
                        currentBalance: currentBalance
                    });
                } catch (error) {
                    console.error('Error updating income dashboard:', error);
                }
            }

            // Handle watch button clicks
            $(document).on('click', '.watch-btn', function() {
                try {
                    // Store reference to clicked button
                    const clickedButton = this;
                    
                    // Debug: Log button state
                    console.log('Button clicked. Classes:', $(this).attr('class'));
                    console.log('Button disabled:', $(this).prop('disabled'));
                    console.log('Button has watched class:', $(this).hasClass('watched'));
                    
                    // Check if video is already watched (prevent already watched videos from being clicked)
                    // Only check for 'watched' class and if button is specifically disabled due to completion
                    if ($(this).hasClass('watched') || ($(this).prop('disabled') && $(this).hasClass('clicked-processing') === false)) {
                        Swal.fire({
                            title: 'Already Watched',
                            text: 'You have already watched this video and earned money from it.',
                            icon: 'info',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true
                        });
                        return;
                    }
                    
                    // Check if button is currently being processed (prevent double-clicks during loading)
                    if ($(this).hasClass('clicked-processing') || $(this).prop('disabled')) {
                        console.log('Button is currently being processed, ignoring click');
                        return;
                    }
                    
                    // Immediately mark this button as clicked to prevent double-clicks
                    $(this).addClass('clicked-processing').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
                    
                    // Get video data from button attributes
                    currentVideoId = $(this).data('video-id');
                    const videoUrl = $(this).data('video-url');
                    const videoTitle = $(this).data('video-title') || 'Video';
                    const videoCategory = $(this).data('video-category') || 'general';
                    const videoViews = parseInt($(this).data('video-views')) || 0;
                    const videoDuration = parseInt($(this).data('duration')) || 30;
                    earningAmount = parseFloat($(this).data('earning')) || 0;
                    
                    // Validate required data
                    if (!currentVideoId) {
                        showVideoError('Video ID is missing. Please refresh the page and try again.');
                        // Reset button
                        resetVideoButton(this);
                        return;
                    }
                    
                    if (!videoUrl || videoUrl.trim() === '') {
                        showVideoError('Video URL is missing. Please contact support.');
                        // Reset button
                        resetVideoButton(this);
                        return;
                    }
                    
                    if (earningAmount <= 0) {
                        earningAmount = 0.001; // Set minimum earning
                    }
                    
                    // Calculate minimum watch time: 80% of video or 15 seconds minimum, whichever is less
                    minimumWatchTime = Math.min(Math.max(Math.ceil(videoDuration * 0.8), 15), videoDuration);
                    
                    // Calculate percentage for display
                    const watchPercentage = Math.round((minimumWatchTime / videoDuration) * 100);
                    
                    // Update modal content with validation
                    try {
                        $('#modal-video-title').text(videoTitle);
                        $('#modal-earning').text('$' + earningAmount.toFixed(4));
                        $('#claim-amount').text(earningAmount.toFixed(4));
                        $('#modal-video-views').text(videoViews.toLocaleString() + ' views');
                        $('#modal-video-duration').text(videoDuration + 's');
                        $('#modal-video-category').text(videoCategory.charAt(0).toUpperCase() + videoCategory.slice(1));
                        $('#minimum-watch-time').text(minimumWatchTime + ' seconds (' + watchPercentage + '% of video)');
                        $('#minimum-required-time').text(minimumWatchTime);
                    } catch (modalError) {
                        showVideoError('Failed to update video information. Please try again.');
                        return;
                    }
                
                    // Convert video URL to embed format with better validation
                    let embedUrl = '';
                    try {
                        embedUrl = convertToEmbedUrl(videoUrl);
                        if (!embedUrl) {
                            throw new Error('Failed to convert video URL to embed format');
                        }
                    } catch (urlError) {
                        showVideoError('Invalid video URL format. Please contact support.');
                        return;
                    }
                    
                    // Set iframe source and show modal
                    try {
                        $('#video-iframe').attr('src', embedUrl);
                        $('#videoWatchModal').modal('show');
                        
                        // Reset progress and video state
                        watchDuration = 0;
                        window.videoWasPaused = false;
                        window.isTabActive = !document.hidden;
                        window.isWindowFocused = document.hasFocus();
                        updateProgress();
                        
                        // Start watch timer
                        startWatchTimer();
                        
                    } catch (iframeError) {
                        showVideoError('Failed to load video player. Please try again.');
                        return;
                    }
                    
                } catch (error) {
                    showVideoError('Failed to initialize video. Please refresh the page and try again.');
                }
            });
            
            // Helper function to show video errors
            function showVideoError(message) {
                Swal.fire({
                    title: 'Video Error',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545',
                    footer: 'If this problem persists, please contact support.'
                });
            }

            // Helper function to reset video buttons to their original state
            function resetVideoButton(button) {
                const $button = $(button);
                
                // Remove all processing and status classes
                $button.removeClass('clicked-processing btn-secondary btn-warning btn-danger disabled');
                
                // Check if this video has actually been watched (has 'watched' class on button or parent)
                const isWatched = $button.hasClass('watched') || $button.closest('.video-card').hasClass('watched');
                
                if (isWatched) {
                    // Keep watched state
                    $button.addClass('btn-success watched')
                           .html('<i class="fas fa-check me-2"></i>Watched')
                           .prop('disabled', true);
                } else {
                    // Reset to original watch state
                    $button.addClass('btn-success')
                           .html('<i class="fas fa-play me-2"></i>Watch & Earn')
                           .prop('disabled', false);
                }
            }

            // Helper function to update the video count badge in real-time
            function updateVideoCountBadge() {
                const remainingVideos = $('.video-card:visible').length;
                const videoBadge = $('#video-count-badge');
                
                console.log('Updating video count badge:', remainingVideos, 'videos remaining');
                
                if (videoBadge.length > 0) {
                    if (remainingVideos > 0) {
                        // Update the badge text with the new count
                        videoBadge.text(remainingVideos + ' Videos Available');
                        
                        // Add a subtle animation to highlight the change
                        videoBadge.addClass('income-update-highlight');
                        setTimeout(function() {
                            videoBadge.removeClass('income-update-highlight');
                        }, 2000);
                    } else {
                        // Hide the badge completely when no videos are left
                        videoBadge.closest('.text-end').fadeOut(300);
                    }
                } else if (remainingVideos > 0) {
                    // If badge was hidden but we have videos again (unlikely but good to handle)
                    const headerDiv = $('.card-header .d-flex.align-items-center.justify-content-between.flex-wrap');
                    if (headerDiv.find('.text-end').length === 0) {
                        headerDiv.append(`
                            <div class="text-end">
                                <span class="badge bg-light text-primary px-3 py-2 fs-6" id="video-count-badge">
                                    ${remainingVideos} Videos Available
                                </span>
                            </div>
                        `);
                    }
                }
                
                // Also update the title if needed
                const cardTitle = $('.card-title:contains("Watch Videos & Earn Money")');
                if (cardTitle.length > 0 && remainingVideos === 0) {
                    // Could update the title to reflect completion, but let's keep it simple for now
                }
            }
            
            // Helper function to convert various video URLs to embed format
            function convertToEmbedUrl(videoUrl) {
                if (!videoUrl || typeof videoUrl !== 'string') {
                    return null;
                }
                
                const url = videoUrl.trim();
                
                // Handle YouTube URLs
                if (url.includes('youtube.com/watch')) {
                    const videoIdMatch = url.match(/[?&]v=([^&]+)/);
                    if (videoIdMatch && videoIdMatch[1]) {
                        return `https://www.youtube.com/embed/${videoIdMatch[1]}?autoplay=1&enablejsapi=1&rel=0&modestbranding=1&controls=1&disablekb=0`;
                    }
                } else if (url.includes('youtu.be/')) {
                    const videoIdMatch = url.split('/').pop();
                    if (videoIdMatch) {
                        const videoId = videoIdMatch.split('?')[0];
                        if (videoId) {
                            return `https://www.youtube.com/embed/${videoId}?autoplay=1&enablejsapi=1&rel=0&modestbranding=1&controls=1&disablekb=0`;
                        }
                    }
                } else if (url.includes('youtube.com/embed/')) {
                    // Already an embed URL, just add parameters
                    if (url.includes('?')) {
                        return url + '&autoplay=1&enablejsapi=1&rel=0&modestbranding=1&controls=1&disablekb=0';
                    } else {
                        return url + '?autoplay=1&enablejsapi=1&rel=0&modestbranding=1&controls=1&disablekb=0';
                    }
                }
                
                // Handle Vimeo URLs
                else if (url.includes('vimeo.com/')) {
                    const videoIdMatch = url.match(/vimeo\.com\/(\d+)/);
                    if (videoIdMatch && videoIdMatch[1]) {
                        return `https://player.vimeo.com/video/${videoIdMatch[1]}?autoplay=1`;
                    }
                }
                
                // Handle direct video files (mp4, webm, etc.)
                else if (url.match(/\.(mp4|webm|ogg|mov|avi)(\?.*)?$/i)) {
                    return url;
                }
                
                // If URL is already an embed URL or direct URL, return as-is
                else if (url.includes('embed') || url.startsWith('data:') || url.startsWith('blob:')) {
                    return url;
                }
                
                // Fallback: try to use the URL directly
                else {
                    return url;
                }
                
                return null;
            }

            // Video control functions with enhanced platform support
            window.pauseVideo = function() {
                try {
                    const iframe = document.getElementById('video-iframe');
                    if (iframe && iframe.contentWindow) {
                        const src = iframe.src;
                        
                        // For YouTube videos
                        if (src.includes('youtube.com')) {
                            iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                        }
                        // For Vimeo videos
                        else if (src.includes('vimeo.com')) {
                            iframe.contentWindow.postMessage('{"method":"pause"}', '*');
                        }
                    }
                } catch (error) {
                    // Silently handle errors
                }
            };
            
            window.resumeVideo = function() {
                try {
                    const iframe = document.getElementById('video-iframe');
                    if (iframe && iframe.contentWindow) {
                        const src = iframe.src;
                        
                        // For YouTube videos
                        if (src.includes('youtube.com')) {
                            iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                        }
                        // For Vimeo videos
                        else if (src.includes('vimeo.com')) {
                            iframe.contentWindow.postMessage('{"method":"play"}', '*');
                        }
                    }
                } catch (error) {
                    // Silently handle errors
                }
            };

            // Start watch timer with better error handling and video sync
            function startWatchTimer() {
                try {
                    if (watchTimer) {
                        clearInterval(watchTimer);
                    }
                    
                    if (!currentVideoId) {
                        return;
                    }
                    
                    watchTimer = setInterval(function() {
                        try {
                            // Check if progress should continue - must have tab visible, window focused, and modal open
                            const modalIsOpen = $('#videoWatchModal').hasClass('show');
                            const tabVisible = window.isTabActive && !document.hidden;
                            const windowActive = window.isWindowFocused;
                            
                            // More aggressive check: require ALL conditions to be true
                            const shouldProgress = tabVisible && windowActive && modalIsOpen;
                            
                            if (shouldProgress) {
                                // Progress continues - ensure video is playing
                                if (window.videoWasPaused) {
                                    window.resumeVideo();
                                    window.videoWasPaused = false;
                                    
                                    // Hide pause status
                                    $('#video-status').hide();
                                }
                                
                                watchDuration += 1;
                                updateProgress();
                                
                                // Check if user has watched enough (dynamic minimum time)
                                if (watchDuration >= minimumWatchTime) {
                                    $('#claim-earnings-btn').prop('disabled', false);
                                    $('#claim-earnings-btn').addClass('pulse');
                                }
                            } else {
                                // Progress paused - ensure video is paused
                                if (!window.videoWasPaused) {
                                    window.pauseVideo();
                                    window.videoWasPaused = true;
                                    
                                    // Show pause status with specific reason
                                    let pauseReason = 'Video paused - ';
                                    if (!tabVisible) {
                                        pauseReason += 'tab is hidden or not visible';
                                    } else if (!windowActive) {
                                        pauseReason += 'window is not focused (click on this window)';
                                    } else if (!modalIsOpen) {
                                        pauseReason += 'modal is closed';
                                    } else {
                                        pauseReason += 'unknown reason';
                                    }
                                    
                                    $('#pause-reason').text(pauseReason);
                                    $('#video-status').show();
                                }
                            }
                        } catch (timerError) {
                            // Silently handle timer errors
                        }
                    }, 1000);
                } catch (error) {
                    // Silently handle errors
                }
            }

            // Update progress bar with error handling
            function updateProgress() {
                try {
                    if (typeof watchDuration === 'undefined' || typeof minimumWatchTime === 'undefined') {
                        return;
                    }
                    
                    const progress = Math.min((watchDuration / minimumWatchTime) * 100, 100);
                    $('#watch-progress').css('width', progress + '%');
                    $('#progress-text').text(Math.round(progress) + '%');
                    $('#watch-time').text(watchDuration);
                    
                    // Update progress bar color
                    if (watchDuration >= minimumWatchTime) {
                        $('#watch-progress').removeClass('bg-warning').addClass('bg-success');
                        $('#progress-text').removeClass('bg-primary bg-warning').addClass('bg-success');
                    } else if (watchDuration >= Math.floor(minimumWatchTime * 0.5)) {
                        $('#watch-progress').removeClass('bg-primary').addClass('bg-warning');
                        $('#progress-text').removeClass('bg-primary').addClass('bg-warning');
                    }
                } catch (error) {
                    // Silently handle errors
                }
            }

            // Claim earnings
            $('#claim-earnings-btn').on('click', function() {
                try {
                    if (!currentVideoId) {
                        return;
                    }
                    
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
                
                // Get fresh CSRF token with multiple fallback methods
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                // Fallback 1: Try to get from Laravel global if meta tag fails
                if (!csrfToken && window.Laravel && window.Laravel.csrfToken) {
                    csrfToken = window.Laravel.csrfToken;
                }
                
                // Fallback 2: Try to get from another common location
                if (!csrfToken) {
                    const csrfInput = $('input[name="_token"]').first();
                    if (csrfInput.length) {
                        csrfToken = csrfInput.val();
                    }
                }
                
                // Final check
                if (!csrfToken) {
                    $('#video-overlay').addClass('d-none');
                    Swal.fire({
                        title: 'Security Error',
                        text: 'Security token missing. Please refresh the page and try again.',
                        icon: 'error',
                        confirmButtonText: 'Refresh Page',
                        allowOutsideClick: false
                    }).then(() => {
                        location.reload();
                    });
                    button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
                    return;
                }
                
                // Debug: Log request data
                console.log('Sending AJAX request for video:', currentVideoId);
                console.log('Watch duration:', watchDuration);
                console.log('Minimum required:', minimumWatchTime);
                
                // Final check: Ensure the button wasn't marked as watched during modal interaction
                const finalButton = $(`.video-card[data-video-id="${currentVideoId}"] .watch-btn`);
                if (finalButton.hasClass('watched')) {
                    console.log('Video was marked as watched during modal interaction, aborting request');
                    $('#video-overlay').addClass('d-none');
                    button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
                    return;
                }
                
                $.ajax({
                    url: '{{ route("user.video-views.watch") }}', 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        video_id: currentVideoId,
                        watch_duration: watchDuration,
                        _token: csrfToken
                    },
                    success: function(response) {
                    $('#video-overlay').addClass('d-none');
                    
                    // Debug: Log server response
                    console.log('Server response:', response);
                    console.log('Response message:', response.message);
                    console.log('Response success:', response.success);
                    
                    // Check if this is an "already watched" scenario where earnings were still processed
                    const isAlreadyWatched = response.message && response.message.toLowerCase().includes('already earned');
                    const hasEarnings = response.earned_amount && parseFloat(response.earned_amount) > 0;
                    const hasBalanceUpdate = response.user_balance || response.total_earnings;
                    
                    // Special handling for false "already watched" errors (when server incorrectly identifies fresh videos as watched)
                    if (isAlreadyWatched && !hasEarnings && !hasBalanceUpdate && response.success === false) {
                        console.warn('Server returned false "already watched" error for fresh video');
                        
                        // Reset button state
                        resetVideoButton(clickedButton);
                        
                        // Show a different message indicating this might be a server issue
                        Swal.fire({
                            title: 'Temporary Issue',
                            text: 'There was a temporary issue processing your request. Please try again in a moment.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            timer: 4000,
                            timerProgressBar: true
                        });
                        return;
                    }
                    
                    if (response.success || (isAlreadyWatched && (hasEarnings || hasBalanceUpdate))) {
                        // INSTANT UI REFRESH - Always update displays regardless of success/already watched status
                        if (hasBalanceUpdate || hasEarnings) {
                            // Force refresh of income dashboard with live data
                            updateIncomeDashboardInstantly(response);
                        }
                        // UPDATE INCOME DISPLAYS using the instant update function
                        const newBalance = parseFloat(response.total_earnings || 0);
                        const currentBalance = parseFloat(response.user_balance || newBalance);
                        const todayEarnings = parseFloat(response.today_earnings || 0);
                        const earnedAmount = parseFloat(response.earned_amount || 0);
                        
                        // Hide video modal first
                        $('#videoWatchModal').modal('hide');
                        
                        // REMOVE VIDEO FROM GALLERY after successful watch confirmation
                        // This provides immediate feedback to the user and prevents re-clicking
                        const videoCard = $(`.video-card[data-video-id="${currentVideoId}"]`);
                        const watchButton = videoCard.find('.watch-btn');
                        
                        // Immediately mark as watched before animation
                        watchButton.addClass('watched btn-success').removeClass('btn-primary clicked-processing')
                                  .html('<i class="fas fa-check me-2"></i>Watched & Earned')
                                  .prop('disabled', true);
                        
                        // Animate the removal with fade out effect
                        videoCard.fadeOut(300, function() {
                            // Remove the video card completely from DOM
                            const parentCol = videoCard.closest('.col-lg-4, .col-md-6, .col-sm-12');
                            parentCol.remove();
                            
                            // Update the video count badge in real-time
                            updateVideoCountBadge();
                            
                            // Check if there are any videos left in the gallery
                            const remainingVideos = $('.video-card:visible').length;
                            
                            if (remainingVideos === 0) {
                                // No more videos - show completion message
                                const videoContainer = $('.row').has('.video-card').first();
                                videoContainer.html(`
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                            <h4 class="text-dark">All Videos Completed!</h4>
                                            <p class="text-muted">Excellent work! You've watched all your assigned videos for today and earned money from each one.</p>
                                            <div class="mt-4">
                                                <span class="badge bg-success bg-gradient px-3 py-2 fs-6">
                                                    <i class="fas fa-coins"></i> Total Earned Today: $${parseFloat(response.today_earnings || response.earned_amount || 0).toFixed(4)}
                                                </span>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg me-2">
                                                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                                </a>
                                                <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success btn-lg">
                                                    <i class="fas fa-chart-line"></i> View Earnings Report
                                                </a>
                                            </div>
                                            <div class="mt-3">
                                                <small class="text-muted">New videos will be available tomorrow!</small>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        });
                        
                        // Show earnings modal with appropriate messaging
                        if (isAlreadyWatched) {
                            $('#earnings-amount').text('Video Already Watched');
                            $('#earnings-message').text('You have already earned from this video today. Your previous earnings are included in your balance.');
                            // Don't show the earnings modal for already watched videos, just close the video modal
                            $('#videoWatchModal').modal('hide');
                            
                            // Show a more friendly notification
                            Swal.fire({
                                title: 'Video Already Watched',
                                text: 'You have already earned from this video today. Your earnings are safely in your balance.',
                                icon: 'info',
                                confirmButtonText: 'Got it!',
                                timer: 3000,
                                timerProgressBar: true,
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                }
                            });
                        } else {
                            // Hide video modal first to prevent conflicts
                            $('#videoWatchModal').modal('hide');
                            
                            // Wait for video modal to close, then show earnings modal
                            setTimeout(function() {
                                // Show earnings modal with updated values
                                $('#earnings-amount').text('You earned $' + parseFloat(response.earned_amount).toFixed(4));
                                $('#earnings-message').text(response.message || 'Congratulations! Your earnings have been added to your balance.');
                                $('#total-balance').text(currentBalance.toFixed(4));
                                
                                // Show the earnings modal
                                $('#earningsModal').modal('show');
                            }, 300);
                        }
                        
                    } else {
                        // Check if this is an "already watched" case that should be treated as informational
                        const isAlreadyWatched = response.message && response.message.toLowerCase().includes('already earned');
                        
                        if (isAlreadyWatched) {
                            // Hide video modal first
                            $('#videoWatchModal').modal('hide');
                            
                            // Remove video from gallery to prevent future clicks
                            const videoCard = $(`.video-card[data-video-id="${currentVideoId}"]`);
                            const watchButton = videoCard.find('.watch-btn');
                            
                            // Mark as already watched immediately
                            watchButton.addClass('watched btn-success').removeClass('btn-primary clicked-processing')
                                      .html('<i class="fas fa-check me-2"></i>Already Watched')
                                      .prop('disabled', true);
                            
                            videoCard.fadeOut(300, function() {
                                const parentCol = videoCard.closest('.col-lg-4, .col-md-6, .col-sm-12');
                                parentCol.remove();
                                
                                // Update the video count badge in real-time
                                updateVideoCountBadge();
                                
                                // Check if there are any videos left
                                const remainingVideos = $('.video-card:visible').length;
                                if (remainingVideos === 0) {
                                    const videoContainer = $('.row').has('.video-card').first();
                                    videoContainer.html(`
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                                <h4 class="text-dark">All Available Videos Completed!</h4>
                                                <p class="text-muted">You've watched all your available videos for today.</p>
                                                <div class="mt-4">
                                                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg me-2">
                                                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                                    </a>
                                                    <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success btn-lg">
                                                        <i class="fas fa-chart-line"></i> View Earnings
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                }
                            });
                            
                            // Show friendly notification instead of error
                            Swal.fire({
                                title: 'Already Watched',
                                text: 'You have already earned from this video today. Your earnings are safely in your balance.',
                                icon: 'info',
                                confirmButtonText: 'Continue',
                                timer: 4000,
                                timerProgressBar: true,
                                showClass: {
                                    popup: 'animate__animated animate__bounceIn'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOut'
                                }
                            });
                        } else {
                            // Handle other types of errors
                            Swal.fire({
                                title: 'Unable to Process',
                                text: response.message || 'Unable to process your request at this time.',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                footer: 'If this continues, please contact support.'
                            });
                        }
                        
                        button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
                    }
                    },
                    error: function(xhr) {
                        $('#video-overlay').addClass('d-none');
                        
                        let errorMessage = 'An error occurred while processing your request.';
                        let errorTitle = 'Error';
                        
                        // Check for specific CSRF token errors
                        if (xhr.status === 419) {
                            errorTitle = 'Session Expired';
                            errorMessage = 'Your session has expired. Please refresh the page and try again.';
                        } else if (xhr.status === 403) {
                            errorTitle = 'Access Denied';
                            errorMessage = 'Access denied. Please refresh the page and try again.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again later.';
                        }
                        
                        Swal.fire({
                            title: errorTitle,
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: xhr.status === 419 ? 'Refresh Page' : 'OK',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (xhr.status === 419 && result.isConfirmed) {
                                location.reload();
                            }
                        });
                        
                        button.prop('disabled', false).html('<i class="fas fa-coins me-1"></i>Claim $' + parseFloat(earningAmount).toFixed(4));
                    }
                });
                } catch (error) {
                    $('#video-overlay').addClass('d-none');
                    Swal.fire({
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Clean up when modal is closed
            $('#videoWatchModal').on('hidden.bs.modal', function() {
                try {
                    
                    // Clear watch timer
                    if (watchTimer) {
                        clearInterval(watchTimer);
                        watchTimer = null;
                    }
                    
                    // Clear iframe source to stop video
                    $('#video-iframe').attr('src', '');
                    
                    // Reset variables
                    watchDuration = 0;
                    currentVideoId = null;
                    earningAmount = 0;
                    minimumWatchTime = 20;
                    window.videoWasPaused = false; // Reset video pause state
                    window.isTabActive = !document.hidden; // Reset tab state
                    window.isWindowFocused = document.hasFocus(); // Reset focus state
                    
                    // Reset button state
                    $('#claim-earnings-btn')
                        .prop('disabled', true)
                        .removeClass('pulse')
                        .html('<i class="fas fa-coins me-1"></i>Claim $<span id="claim-amount">0.00</span>');
                    
                    // Reset progress
                    updateProgress();
                    
                    // Hide overlay if it's showing
                    $('#video-overlay').addClass('d-none');
                    
                } catch (error) {
                    // Silently handle errors
                }
            });

            // Also handle modal show event for better initialization
            $('#videoWatchModal').on('show.bs.modal', function() {
                try {
                    
                    // Ensure overlay is hidden
                    $('#video-overlay').addClass('d-none');
                    
                    // Hide video status initially
                    $('#video-status').hide();
                    
                    // Set initial focus states
                    window.isTabActive = !document.hidden;
                    window.isWindowFocused = document.hasFocus();
                    
                    // Reset progress bar colors
                    $('#watch-progress').removeClass('bg-warning bg-success').addClass('bg-primary');
                    $('#progress-text').removeClass('bg-warning bg-success').addClass('bg-primary');
                    
                } catch (error) {
                    // Silently handle errors
                }
            });

            // Enhanced visibility change handling with video control
            document.addEventListener('visibilitychange', function() {
                window.isTabActive = !document.hidden;
                
                if (document.hidden) {
                    // Video will be paused by the timer logic
                } else {
                    // Video will be resumed by the timer logic if modal is still open
                    if ($('#videoWatchModal').hasClass('show')) {
                        // Video will resume playback
                    }
                }
            });

            // Window focus/blur events for additional video control
            window.addEventListener('focus', function() {
                window.isWindowFocused = true;
                window.userInteractionDetected = true;
                if ($('#videoWatchModal').hasClass('show') && window.isTabActive) {
                    // Video resume will be handled by the timer logic
                }
            });
            
            window.addEventListener('blur', function() {
                window.isWindowFocused = false;
                if ($('#videoWatchModal').hasClass('show')) {
                    // Video pause will be handled by the timer logic
                }
            });
            
            // Track user interactions to detect if they're actively using the page
            document.addEventListener('mousemove', function() {
                window.userInteractionDetected = true;
            });
            
            document.addEventListener('keydown', function() {
                window.userInteractionDetected = true;
            });
            
            document.addEventListener('click', function() {
                window.userInteractionDetected = true;
            });

            // Handle Continue Watching button click - now just closes modal without reload
            $('#continue-watching-btn').on('click', function() {
                console.log('Continue Watching button clicked - closing modal only (no page reload needed)');
                
                // Force close any open modals to prevent conflicts
                $('.modal').modal('hide');
                
                // Wait a bit for modals to close, then show success message
                setTimeout(function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your earnings have been added to your balance. You can continue watching more videos.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }, 300);
            });
            
            // Global modal conflict prevention - ensure only one modal at a time
            $('.modal').on('show.bs.modal', function() {
                // Close any other open modals before showing this one
                $('.modal').not(this).modal('hide');
            });
            
            // Prevent modal backdrop conflicts
            $(document).on('hidden.bs.modal', '.modal', function() {
                if ($('.modal.show').length > 0) {
                    $('body').addClass('modal-open');
                }
            });
            
            // Remove the automatic reload from earnings modal close
            $(document).on('hidden.bs.modal', '#earningsModal', function() {
                console.log('Earnings modal closed - UI already updated, no reload needed');
                // No automatic reload - UI is already updated
            });

            // YouTube API integration (optional - for advanced video tracking)
            let players = {};
            
            // Initialize YouTube players if needed
            window.onYouTubeIframeAPIReady = function() {
                console.log('YouTube API ready');
            };
            
            // Smooth scrolling for anchor links
            $(document).on('click', '.smooth-scroll', function(e) {
                e.preventDefault();
                const targetId = $(this).attr('href');
                if (targetId && targetId.startsWith('#')) {
                    const targetElement = $(targetId);
                    if (targetElement.length) {
                        $('html, body').animate({
                            scrollTop: targetElement.offset().top - 100
                        }, 800, 'swing');
                    }
                }
            });
            
            // Add floating animation stagger
            $('.floating-elements > div').each(function(index) {
                $(this).css('animation-delay', (index * 0.5) + 's');
            });
        });
    </script>
    
    <!-- Enhanced Responsive CSS -->
    <style>
        /* Enhanced Video Card Styles */
        .video-card {
            transition: all 0.3s ease;
            border-radius: 1rem !important;
            overflow: hidden;
        }
        
        .video-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        .video-card .video-thumb-img {
            transition: transform 0.3s ease;
        }
        
        .video-card:hover .video-thumb-img {
            transform: scale(1.05);
        }
        
        .play-overlay {
            transition: all 0.3s ease;
            opacity: 0.8;
        }
        
        .video-card:hover .play-overlay {
            opacity: 1;
            transform: scale(1.1);
        }
        
        .watch-btn:hover .button-shine {
            opacity: 1 !important;
            animation: shine 0.6s ease-in-out;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Timer Enhancements */
        .timer-card {
            transition: all 0.3s ease;
        }
        
        .timer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(220, 53, 69, 0.3) !important;
        }
        
        .timer-digits {
            letter-spacing: 2px;
        }
        
        /* Balance Card Enhancements */
        .balance-card {
            transition: all 0.3s ease;
        }
        
        .balance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255,255,255,0.1) !important;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .timer-digits {
                font-size: 1.8rem !important;
            }
        }
        
        @media (max-width: 992px) {
            .timer-card {
                margin-bottom: 1rem;
            }
            
            .balance-card h4 {
                font-size: 1.5rem !important;
            }
            
            .user-plan-info {
                margin-bottom: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .timer-digits {
                font-size: 1.6rem !important;
            }
            
            .video-card {
                margin-bottom: 1.5rem;
            }
            
            .balance-card {
                text-align: center;
                margin-top: 1rem;
            }
            
            .admin-debug-section .row > div {
                margin-bottom: 0.5rem;
            }
            
            /* Hide stat labels on mobile to save space */
            .video-stats .stat-item small.text-muted {
                display: none !important;
            }
            
            /* Adjust stat items for mobile - only show icons and values */
            .video-stats .stat-item {
                padding: 0.75rem 0.5rem !important;
                text-align: center;
            }
            
            .video-stats .stat-item i {
                font-size: 1rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .video-stats .stat-item strong {
                font-size: 0.85rem !important;
                font-weight: 700 !important;
                display: block;
                line-height: 1.2;
            }
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem !important;
            }
            
            .timer-card {
                padding: 1.5rem !important;
            }
            
            .balance-card {
                padding: 1.5rem !important;
            }
            
            .timer-digits {
                font-size: 1.4rem !important;
            }
            
            .video-thumbnail {
                height: 180px !important;
            }
            
            /* More compact stat items for very small screens */
            .video-stats .stat-item {
                padding: 0.5rem 0.25rem !important;
                min-height: 65px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            .video-stats .stat-item i {
                font-size: 0.9rem !important;
                margin-bottom: 0.375rem !important;
            }
            
            .video-stats .stat-item strong {
                font-size: 0.8rem !important;
                font-weight: 800 !important;
                text-align: center;
            }
            
            /* Reduce video card padding */
            .video-card .card-body {
                padding: 1rem !important;
            }
            
            .video-stats {
                margin-bottom: 1rem !important;
            }
        }
        
        /* Badge Enhancements */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .video-badges .badge {
            font-size: 0.7rem;
            backdrop-filter: blur(10px);
        }
        
        /* Progress Bar Enhancements */
        .progress {
            background-color: rgba(255,255,255,0.2) !important;
            border-radius: 10px !important;
            overflow: hidden;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #ffc107, #ff8c00) !important;
            border-radius: 10px !important;
            position: relative;
            overflow: hidden;
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(45deg, transparent 35%, rgba(255,255,255,0.3) 50%, transparent 65%);
            animation: progressShine 2s infinite;
        }
        
        @keyframes progressShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Stat Items Enhancement */
        .stat-item {
            transition: all 0.2s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Text Enhancements */
        .text-white-75 {
            color: rgba(255,255,255,0.75) !important;
        }
        
        /* Loading States */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Hero Banner Styles */
        .hero-banner {
            position: relative;
            overflow: hidden;
        }
        
        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            z-index: 1;
        }
        
        .hero-banner > * {
            position: relative;
            z-index: 2;
        }
        
        .icon-wrapper {
            transition: transform 0.3s ease;
        }
        
        .hero-banner:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        .stats-card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .floating-elements .floating-coin,
        .floating-elements .floating-play,
        .floating-elements .floating-dollar {
            pointer-events: none;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .smooth-scroll {
            scroll-behavior: smooth;
        }
        
        /* Feature Items */
        .feature-item {
            transition: transform 0.2s ease;
        }
        
        .feature-item:hover {
            transform: translateX(5px);
        }
        
        /* Progress Ring */
        .progress-ring .progress-bar {
            transition: width 0.5s ease;
        }
        
        /* Guest CTA Animation */
        .guest-cta .fas {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Responsive Adjustments for Hero */
        @media (max-width: 768px) {
            .hero-banner {
                min-height: 150px !important;
            }
            
            .hero-banner .p-5 {
                padding: 2rem !important;
            }
            
            .display-6 {
                font-size: 1.5rem !important;
            }
            
            .lead {
                font-size: 1rem !important;
            }
            
            .floating-elements {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .hero-features .col-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .stats-card {
                margin-top: 1rem;
            }
        }
        
        /* Video Slider Styles */
        .content-slider-container {
            position: relative;
            width: 100%;
            min-height: 200px;
            overflow: hidden;
        }
        
        .content-slider-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }
        
        .content-slide {
            flex: 0 0 100%;
            width: 100%;
            height: 100%;
        }
        
        .content-nav-btn {
            transition: all 0.3s ease;
            border: none;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9) !important;
            z-index: 10 !important;
            position: relative;
            cursor: pointer;
        }
        
        .content-nav-btn:hover {
            background: rgba(255,255,255,1) !important;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .content-nav-btn:active {
            transform: scale(0.95);
        }
        
        .content-nav-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
        }
        
        .content-slider-dots {
            justify-content: center;
        }
        
        .content-dot {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .content-dot:hover {
            background-color: rgba(255,255,255,0.8) !important;
            transform: scale(1.2);
        }
        
        .content-dot.active {
            background-color: rgba(255,255,255,0.9) !important;
            transform: scale(1.3);
        }
        
        /* Enhanced banner styles */
        .hero-banner, .earnings-banner, .timer-banner {
            transition: all 0.3s ease;
        }
        
        .earnings-banner {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }
        
        .timer-banner {
            background: linear-gradient(135deg, #dc3545 0%, #ff6347 100%) !important;
        }
        
        /* Responsive Design for Content Slider */
        @media (max-width: 768px) {
            .content-slider-container {
                min-height: 400px !important; /* Increased height for mobile */
            }
            
            .hero-banner, .earnings-banner, .timer-banner {
                min-height: 400px !important; /* Ensure all slides have same height */
            }
            
            .hero-content, .earnings-content, .timer-content {
                text-align: center;
                padding: 2rem !important;
            }
            
            .hero-stats, .balance-card, .timer-card {
                margin-top: 2rem;
            }
            
            .display-6 {
                font-size: 1.8rem !important;
            }
            
            .content-nav-btn {
                width: 45px !important;
                height: 45px !important;
                z-index: 10 !important;
                position: relative !important;
            }
            
            .content-nav-btn i {
                font-size: 1.1rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .content-slider-container {
                min-height: 450px !important; /* Even taller for small screens */
            }
            
            .hero-banner, .earnings-banner, .timer-banner {
                min-height: 450px !important; /* Consistent height across all slides */
            }
            
            .hero-content, .earnings-content, .timer-content {
                padding: 1.5rem !important;
            }
            
            .display-6 {
                font-size: 1.5rem !important;
            }
            
            .content-nav-btn {
                width: 40px !important;
                height: 40px !important;
                z-index: 10 !important;
                position: relative !important;
            }
            
            .content-nav-btn i {
                font-size: 1rem !important;
            }
            
            /* Make navigation dots larger on mobile */
            .content-dot {
                width: 15px !important;
                height: 15px !important;
            }
        }
        
        @media (max-width: 375px) {
            .content-slider-container {
                min-height: 500px !important; /* Extra height for very small screens */
            }
            
            .hero-banner, .earnings-banner, .timer-banner {
                min-height: 500px !important;
            }
        }
        
        /* Income Card Enhancements */
        .income-card {
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .income-card:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        
        .income-card .card-header {
            position: relative;
            overflow: hidden;
        }
        
        .income-card .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }
        
        .income-card:hover .card-header::before {
            left: 100%;
        }
        
        .income-display {
            position: relative;
        }
        
        .income-display .display-5 {
            position: relative;
            z-index: 2;
        }
        
        .stat-item {
            transition: all 0.3s ease;
            border-radius: 0.5rem !important;
        }
        
        .stat-item:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Floating Animation for Income Cards */
        @keyframes incomeFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }
        
        .income-card {
            animation: incomeFloat 4s ease-in-out infinite;
        }
        
        .income-card:nth-child(2) {
            animation-delay: 1s;
        }
        
        .income-card:nth-child(3) {
            animation-delay: 2s;
        }
        
        /* Responsive Income Dashboard */
        @media (max-width: 992px) {
            .income-card {
                margin-bottom: 1rem;
            }
            
            .income-display .display-5 {
                font-size: 2rem !important;
            }
        }
        
        @media (max-width: 768px) {
            .income-display .display-5 {
                font-size: 1.75rem !important;
            }
            
            .income-card .card-body {
                padding: 1.5rem !important;
            }
        }
    </style>
    
    @endpush
    
    @push('style')
    <style>
        /* Timer Section Styles */
        .timer-section {
            transition: all 0.3s ease;
        }
        
        .timer-card {
            transition: all 0.3s ease;
        }
        
        .timer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(220, 53, 69, 0.3) !important;
        }
        
        .timer-digits {
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .timer-icon-wrapper i {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Enhanced Card Styles */
        .bg-gradient {
            position: relative;
            overflow: hidden;
        }
        
        .bg-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }
        
        .icon-wrapper {
            transition: transform 0.3s ease;
        }
        
        .icon-wrapper:hover {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .timer-digits {
                font-size: 1.8rem !important;
                letter-spacing: 2px;
            }
            
            .timer-card {
                margin-top: 1rem;
            }
            
            .timer-labels {
                font-size: 0.7rem !important;
                gap: 2rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .timer-digits {
                font-size: 1.6rem !important;
                letter-spacing: 1px;
            }
            
            .timer-card {
                padding: 1.5rem !important;
            }
            
            .timer-labels {
                gap: 1.5rem !important;
            }
        }
        
        /* Video Card Styles */
        .video-card {
            transition: all 0.3s ease;
            border-radius: 1rem !important;
            overflow: hidden;
        }
        
        .video-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }
        
        .video-card .video-thumb-img {
            transition: transform 0.3s ease;
        }
        
        .video-card:hover .video-thumb-img {
            transform: scale(1.05);
        }
        
        .play-overlay {
            transition: all 0.3s ease;
            opacity: 0.8;
        }
        
        .video-card:hover .play-overlay {
            opacity: 1;
            transform: scale(1.1);
        }
        
        .watch-btn:hover .button-shine {
            opacity: 1 !important;
            animation: shine 0.6s ease-in-out;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Mobile Responsive Adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
            
            .timer-card {
                transform: translateY(-2px) !important;
            }
            
            .display-6 {
                font-size: 1.5rem !important;
            }
            
            .timer-labels {
                gap: 1rem !important;
            }
            
            .timer-labels span {
                font-size: 0.6rem !important;
            }
            
            .icon-wrapper {
                padding: 1rem !important;
                margin-right: 1rem !important;
            }
            
            .icon-wrapper i {
                font-size: 1.2rem !important;
            }
            
            h4 {
                font-size: 1.1rem !important;
            }
            
            .badge {
                font-size: 0.65rem !important;
                padding: 0.25rem 0.5rem !important;
            }
            
            .progress {
                height: 10px !important;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .timer-card {
                transform: translateY(0) !important;
                margin-top: 1rem;
            }
            
            .display-6 {
                font-size: 1.3rem !important;
                letter-spacing: 1px !important;
            }
            
            .timer-labels {
                gap: 0.75rem !important;
            }
            
            .timer-labels span {
                font-size: 0.55rem !important;
            }
            
            .row.align-items-center {
                margin: 0 !important;
            }
            
            .col-lg-7, .col-lg-5 {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            .icon-wrapper {
                padding: 0.75rem !important;
                margin-right: 0.75rem !important;
            }
            
            h4 {
                font-size: 1rem !important;
            }
            
            .progress {
                height: 8px !important;
            }
        }
        
        /* Enhanced Timer Card Animations */
        .timer-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .timer-card:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
        }
        
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }
        
        /* Floating background animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-5px) rotate(1deg); }
            50% { transform: translateY(-10px) rotate(0deg); }
            75% { transform: translateY(-5px) rotate(-1deg); }
        }
        
        /* Remove excessive margins on PC */
        @media (min-width: 992px) {
            .container-fluid {
                max-width: 100%;
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .timer-card {
                transform: translateY(-5px);
            }
        }
    </style>
    @endpush
</x-smart_layout>