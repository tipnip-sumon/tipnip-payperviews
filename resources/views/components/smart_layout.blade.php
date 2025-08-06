@php
    // Get device detection from middleware
    $isMobile = request()->attributes->get('is_mobile', false);
    $deviceType = request()->attributes->get('device_type', 'desktop');
    
    // Initialize variables
    $userAgent = request()->header('User-Agent', '');
    $screenWidth = request()->header('X-Screen-Width');
    
    // Check modal_settings for Web Install Suggestion Modal
    $webInstallModalEnabled = false;
    $webInstallModalSettings = null; 
    
    try {
        // Check if modal_settings table exists and get web install modal settings
        $webInstallModalSettings = \DB::table('modal_settings')
            ->where('modal_name', 'web_install_suggestion')
            ->where('is_active', 1)
            ->first();
            
        if ($webInstallModalSettings) {
            $webInstallModalEnabled = true;
            
            // Log the modal settings for debugging
            if (config('app.debug')) {
                \Log::info('Web Install Modal Settings', [
                    'modal_id' => $webInstallModalSettings->id ?? null,
                    'is_active' => $webInstallModalSettings->is_active ?? null,
                    'show_frequency' => $webInstallModalSettings->show_frequency ?? null,
                    'target_users' => $webInstallModalSettings->target_users ?? null
                ]);
            }
        }
    } catch (\Exception $e) {
        // Modal settings table doesn't exist or query failed
        \Log::warning('Modal settings table not found or query failed: ' . $e->getMessage());
        $webInstallModalEnabled = false;
    }
    
    // Fallback to manual detection if middleware didn't run
    if (!request()->attributes->has('is_mobile')) {
        $isMobile = false;
        // Check for mobile patterns in user agent
        $mobilePatterns = [
            '/android/i',
            '/webos/i',
            '/iphone/i',
            '/ipad/i',
            '/ipod/i',
            '/blackberry/i',
            '/iemobile/i',
            '/mobile/i',
            '/phone/i'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $isMobile = true;
                break;
            }
        }
        
        // Also check for small screen width if available
        if ($screenWidth && $screenWidth <= 991) {
            $isMobile = true;
        }
    }
    
    // Debug logging
    if (config('app.debug')) {
        \Log::info('Smart Layout Detection', [
            'user_agent' => $userAgent,
            'is_mobile' => $isMobile,
            'device_type' => $deviceType,
            'screen_width' => $screenWidth,
            'web_install_modal_enabled' => $webInstallModalEnabled
        ]);
    }
@endphp

@if($isMobile)
    {{-- Load Mobile Layout --}}
    <x-mobile_layout>
        @yield('content')
    </x-mobile_layout>
@else
    {{-- Load Desktop Layout --}}
    <x-desktop_layout>
        @yield('content')
    </x-desktop_layout>
@endif

{{-- Web Install Suggestion Modal System - Database Controlled --}}
@if($webInstallModalEnabled && $webInstallModalSettings)
@php
    // Advanced modal logic based on database settings
    $shouldShowModal = false;
    $currentDomain = request()->getHost();
    $isPayPerViewsDomain = str_contains($currentDomain, 'payperviews.net') || str_contains($currentDomain, 'localhost');
    
    // Get target users from database settings
    $targetUsers = $webInstallModalSettings->target_users ?? 'all'; // all, new_users, guests, verified, unverified
    $showFrequency = $webInstallModalSettings->show_frequency ?? 'daily'; // once, daily, weekly, session
    $maxShows = $webInstallModalSettings->max_shows ?? 7; // Maximum times to show
    $delaySeconds = $webInstallModalSettings->delay_seconds ?? 3; // Delay before showing
    
    // Check user targeting
    $userEligible = false;
    
    if ($targetUsers === 'all') {
        $userEligible = true;
    } elseif ($targetUsers === 'guests' && !auth()->check()) {
        $userEligible = true;
    } elseif ($targetUsers === 'new_users' && auth()->check()) {
        $user = auth()->user();
        $userEligible = $user->created_at->diffInDays(now()) <= 7;
    } elseif ($targetUsers === 'verified' && auth()->check()) {
        $userEligible = auth()->user()->email_verified_at !== null;
    } elseif ($targetUsers === 'unverified' && auth()->check()) {
        $userEligible = auth()->user()->email_verified_at === null;
    }
    
    // Check frequency constraints
    $frequencyMet = false;
    $sessionKey = 'web_install_modal_shown';
    $lastShownKey = 'web_install_modal_last_shown';
    $showCountKey = 'web_install_modal_count';
    
    if ($showFrequency === 'once') {
        $frequencyMet = !session($sessionKey, false);
    } elseif ($showFrequency === 'daily') {
        $lastShown = session($lastShownKey);
        $frequencyMet = !$lastShown || now()->diffInDays($lastShown) >= 1;
    } elseif ($showFrequency === 'weekly') {
        $lastShown = session($lastShownKey);
        $frequencyMet = !$lastShown || now()->diffInWeeks($lastShown) >= 1;
    } elseif ($showFrequency === 'session') {
        $frequencyMet = !session($sessionKey, false);
    }
    
    // Check maximum shows limit
    $showCount = session($showCountKey, 0);
    $withinMaxShows = $showCount < $maxShows;
    
    // Final decision
    $shouldShowModal = $userEligible && $frequencyMet && $withinMaxShows && 
                      !session('web_install_modal_dismissed_permanently', false);
    
    // Debug logging
    if (config('app.debug')) {
        \Log::info('Web Install Modal Decision', [
            'should_show' => $shouldShowModal,
            'user_eligible' => $userEligible,
            'frequency_met' => $frequencyMet,
            'within_max_shows' => $withinMaxShows,
            'target_users' => $targetUsers,
            'show_frequency' => $showFrequency,
            'show_count' => $showCount,
            'max_shows' => $maxShows
        ]);
    }
@endphp

@if($shouldShowModal)
<!-- Database-Controlled Web Install Suggestion Modal -->
<div class="modal fade" id="databaseWebInstallModal" tabindex="-1" aria-labelledby="databaseWebInstallModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <div class="d-flex align-items-center">
                    <i class="fe fe-smartphone me-2 fs-4"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="databaseWebInstallModalLabel">
                            {{ $webInstallModalSettings->title ?? 'Install PayPerViews App' }}
                        </h5>
                        <small class="opacity-75">{{ $webInstallModalSettings->subtitle ?? 'Get the best mobile experience' }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- App Installation Benefits -->
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <div class="mb-3">
                            <i class="fe fe-download fs-1 text-primary"></i>
                        </div>
                        <h4 class="mb-3">{{ $webInstallModalSettings->heading ?? 'Install Our PWA App' }}</h4>
                        <p class="text-muted">
                            {{ $webInstallModalSettings->description ?? 'Get faster access, offline capabilities, and native app experience right from your browser!' }}
                        </p>
                    </div>
                </div>

                <!-- Installation Benefits -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="avatar avatar-lg bg-success-transparent mb-2 mx-auto">
                                <i class="fe fe-zap text-success"></i>
                            </div>
                            <h6 class="mb-1">Lightning Fast</h6>
                            <small class="text-muted">Instant loading & smooth performance</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="avatar avatar-lg bg-info-transparent mb-2 mx-auto">
                                <i class="fe fe-wifi-off text-info"></i>
                            </div>
                            <h6 class="mb-1">Works Offline</h6>
                            <small class="text-muted">Access content even without internet</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="avatar avatar-lg bg-warning-transparent mb-2 mx-auto">
                                <i class="fe fe-bell text-warning"></i>
                            </div>
                            <h6 class="mb-1">Push Notifications</h6>
                            <small class="text-muted">Stay updated with earnings & news</small>
                        </div>
                    </div>
                </div>

                <!-- Browser-Specific Instructions -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info border-0">
                            <h6 class="alert-heading">
                                <i class="fe fe-info me-2"></i>How to Install:
                            </h6>
                            <div id="install-instructions">
                                <!-- JavaScript will populate browser-specific instructions -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="dismissWebInstallModal('later')">
                                <i class="fe fe-clock me-2"></i>Remind Later
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary w-100" id="installAppBtn">
                                <i class="fe fe-download me-2"></i>Install Now
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-link btn-sm text-muted" onclick="dismissWebInstallModal('permanent')">
                                Don't show this again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Web Install Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Database-controlled Web Install Modal initializing...');
    
    // Configuration from database
    const modalConfig = {
        delaySeconds: {{ $delaySeconds }},
        showFrequency: '{{ $showFrequency }}',
        maxShows: {{ $maxShows }},
        modalId: {{ $webInstallModalSettings->id ?? 0 }}
    };
    
    console.log('Modal Config:', modalConfig);
    
    // PWA Installation variables
    let deferredPrompt;
    let isAppInstalled = false;
    
    // Check if app is already installed
    if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
        isAppInstalled = true;
        console.log('✅ App is already installed');
        return; // Don't show modal if app is already installed
    }
    
    // Listen for beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', function(e) {
        console.log('📱 beforeinstallprompt event fired');
        e.preventDefault();
        deferredPrompt = e;
        
        // Update install button to use native prompt
        const installBtn = document.getElementById('installAppBtn');
        if (installBtn) {
            installBtn.onclick = function() {
                console.log('🎯 Install button clicked - using native prompt');
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(choiceResult) {
                        console.log('User choice:', choiceResult.outcome);
                        if (choiceResult.outcome === 'accepted') {
                            trackModalAction('installed_native');
                            dismissWebInstallModal('permanent');
                        } else {
                            trackModalAction('cancelled_native');
                        }
                        deferredPrompt = null;
                    });
                } else {
                    showManualInstallInstructions();
                }
            };
        }
    });
    
    // Update install instructions based on browser
    function updateInstallInstructions() {
        const instructionsDiv = document.getElementById('install-instructions');
        if (!instructionsDiv) return;
        
        const userAgent = navigator.userAgent.toLowerCase();
        let instructions = '';
        
        if (userAgent.includes('chrome') && !userAgent.includes('edg')) {
            instructions = `
                <ol class="mb-0">
                    <li>Click the <strong>Install</strong> button above, or</li>
                    <li>Look for the <i class="fe fe-download"></i> install icon in the address bar</li>
                    <li>Click <strong>"Install PayPerViews"</strong></li>
                </ol>
            `;
        } else if (userAgent.includes('firefox')) {
            instructions = `
                <ol class="mb-0">
                    <li>Click the <i class="fe fe-home"></i> home icon in the address bar</li>
                    <li>Select <strong>"Add to Home Screen"</strong></li>
                    <li>Confirm installation</li>
                </ol>
            `;
        } else if (userAgent.includes('safari')) {
            instructions = `
                <ol class="mb-0">
                    <li>Tap the <i class="fe fe-share"></i> share button</li>
                    <li>Scroll down and tap <strong>"Add to Home Screen"</strong></li>
                    <li>Tap <strong>"Add"</strong> to confirm</li>
                </ol>
            `;
        } else if (userAgent.includes('edg')) {
            instructions = `
                <ol class="mb-0">
                    <li>Click the <strong>Install</strong> button above, or</li>
                    <li>Click the <i class="fe fe-more-horizontal"></i> menu (⋯)</li>
                    <li>Select <strong>"Install PayPerViews"</strong></li>
                </ol>
            `;
        } else {
            instructions = `
                <p class="mb-0">
                    <strong>Look for an install option</strong> in your browser's menu or address bar. 
                    The exact steps vary by browser, but you should see an option to install or add this site to your home screen.
                </p>
            `;
        }
        
        instructionsDiv.innerHTML = instructions;
    }
    
    // Show manual install instructions when native prompt is not available
    function showManualInstallInstructions() {
        updateInstallInstructions();
        trackModalAction('manual_instructions_shown');
        
        // Change button text to indicate manual installation
        const installBtn = document.getElementById('installAppBtn');
        if (installBtn) {
            installBtn.innerHTML = '<i class="fe fe-info me-2"></i>Instructions Above';
            installBtn.disabled = true;
        }
    }
    
    // Show the modal with delay
    setTimeout(function() {
        const modal = document.getElementById('databaseWebInstallModal');
        if (modal) {
            console.log('📱 Showing Web Install modal after delay');
            
            // Update install instructions
            updateInstallInstructions();
            
            // Show modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Track modal shown
            trackModalAction('shown');
            
            // Update session to mark as shown
            updateModalSession('shown');
            
            console.log('✅ Web Install modal displayed successfully');
        }
    }, modalConfig.delaySeconds * 1000);
    
    // Global dismiss function
    window.dismissWebInstallModal = function(type) {
        console.log('❌ Dismissing Web Install modal:', type);
        
        const modal = document.getElementById('databaseWebInstallModal');
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
        
        // Track dismissal
        trackModalAction('dismissed_' + type);
        
        // Update session based on dismissal type
        if (type === 'permanent') {
            updateModalSession('dismissed_permanent');
        } else {
            updateModalSession('dismissed_later');
        }
    };
    
    // Track modal actions for analytics
    function trackModalAction(action) {
        console.log('📊 Tracking modal action:', action);
        
        // Send to backend for analytics
        fetch('/api/modal-analytics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                modal_id: modalConfig.modalId,
                modal_name: 'web_install_suggestion',
                action: action,
                user_agent: navigator.userAgent,
                timestamp: new Date().toISOString()
            })
        }).catch(function(error) {
            console.log('Analytics tracking failed:', error);
        });
    }
    
    // Update session storage for modal frequency control
    function updateModalSession(action) {
        fetch('/api/update-modal-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                modal_name: 'web_install_suggestion',
                action: action,
                frequency: modalConfig.showFrequency
            })
        }).catch(function(error) {
            console.log('Session update failed:', error);
        });
    }
    
    console.log('✅ Web Install Modal system initialized successfully');
});
</script>
@endif
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
@stack('script')

{{-- Global Video System for all layouts --}}
<script>
// Global Video System - Available on all pages
(function() {
    'use strict';
    
    // Store route URL for video recording
    const VIDEO_RECORD_ROUTE = "{{ route('video.record-view', ':id') }}";
    
    // Global video watching functionality
    window.playVideo = function(videoId, videoTitle, videoUrl, earningAmount, minimumTime = 20) {
        console.log('🎬 Playing video:', { videoId, videoTitle, videoUrl, earningAmount, minimumTime });
        
        // Check if Bootstrap is available
        if (typeof window.bootstrap === 'undefined') {
            console.error('❌ Bootstrap is not loaded! Modal functionality will not work.');
            alert('Error: Bootstrap is required for video playback. Please refresh the page.');
            return;
        }
        
        // Check if modal exists
        const modal = document.getElementById('globalVideoModal');
        if (!modal) {
            console.error('❌ Global video modal not found!');
            alert('Error: Video modal not available. Please refresh the page.');
            return;
        }
        
        // Update modal content
        const titleElement = document.getElementById('global-modal-video-title');
        const iframeElement = document.getElementById('global-video-iframe');
        const earningElement = document.getElementById('global-earning-amount');
        const minimumTimeElement = document.getElementById('global-minimum-time');
        
        if (titleElement) titleElement.textContent = videoTitle || 'Watch Video to Earn';
        if (iframeElement) iframeElement.src = videoUrl;
        if (earningElement) earningElement.textContent = parseFloat(earningAmount || 0).toFixed(4);
        if (minimumTimeElement) minimumTimeElement.textContent = minimumTime;
        
        // Reset progress
        resetVideoProgress();
        
        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Start progress tracking
        startWatchProgress(videoId, earningAmount, minimumTime);
        
        console.log('✅ Video modal opened successfully');
    };
    
    // Reset video progress
    function resetVideoProgress() {
        const progressBar = document.getElementById('global-progress-bar');
        const progressText = document.getElementById('global-watch-progress');
        const completeBtn = document.getElementById('global-complete-btn');
        
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0s / 20s';
        if (completeBtn) completeBtn.classList.add('d-none');
    }
    
    // Start watch progress tracking
    function startWatchProgress(videoId, earningAmount, minimumTime) {
        console.log('⏱️ Starting watch progress for video:', videoId);
        
        let watchTime = 0;
        const progressBar = document.getElementById('global-progress-bar');
        const progressText = document.getElementById('global-watch-progress');
        const completeBtn = document.getElementById('global-complete-btn');
        
        const interval = setInterval(() => {
            watchTime++;
            const percentage = Math.min((watchTime / minimumTime) * 100, 100);
            
            if (progressBar) progressBar.style.width = percentage + '%';
            if (progressText) progressText.textContent = `${watchTime}s / ${minimumTime}s`;
            
            // Show complete button when minimum time is reached
            if (watchTime >= minimumTime && completeBtn) {
                completeBtn.classList.remove('d-none');
                completeBtn.onclick = () => completeVideoWatching(videoId, earningAmount);
                clearInterval(interval);
                console.log('✅ Minimum watch time reached');
            }
        }, 1000);
        
        // Store interval ID for cleanup
        window.currentVideoInterval = interval;
    }
    
    // Complete video watching and earn money
    window.completeVideoWatching = function(videoId, earningAmount) {
        console.log('💰 Completing video watch for video:', videoId);
        
        const completeBtn = document.getElementById('global-complete-btn');
        if (completeBtn) {
            completeBtn.disabled = true;
            completeBtn.innerHTML = '<i class="fe fe-spinner me-2"></i>Processing...';
        }
        
        // Make API request to record video view (using same route as gallery.blade.php)
        const routeUrl = VIDEO_RECORD_ROUTE.replace(':id', videoId);
        
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                video_id: videoId
            })
        })
        .then(response => {
            console.log('📡 API Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📡 API Response data:', data);
            
            if (data.success) {
                // Close video modal
                const videoModal = bootstrap.Modal.getInstance(document.getElementById('globalVideoModal'));
                if (videoModal) videoModal.hide();
                
                // Show earnings modal
                showEarningsModal(earningAmount, data.message || 'You earned money!', data.new_balance || 0);
                
                // Clear interval
                if (window.currentVideoInterval) {
                    clearInterval(window.currentVideoInterval);
                }
                
                console.log('✅ Video watching completed successfully');
                
                // Optionally reload page after 3 seconds like gallery does
                setTimeout(() => {
                    location.reload();
                }, 3000);
                
            } else {
                throw new Error(data.message || 'Failed to record video view');
            }
        })
        .catch(error => {
            console.error('❌ Error completing video watch:', error);
            alert('Error: ' + error.message);
            
            // Reset button
            if (completeBtn) {
                completeBtn.disabled = false;
                completeBtn.innerHTML = '<i class="fe fe-check me-2"></i>Complete & Earn';
            }
        });
    };
    
    // Show earnings modal
    function showEarningsModal(amount, message, newBalance) {
        console.log('🏆 Showing earnings modal:', { amount, message, newBalance });
        
        const earningsModal = document.getElementById('globalEarningsModal');
        if (!earningsModal) {
            console.error('❌ Earnings modal not found');
            return;
        }
        
        // Update modal content
        const amountElement = document.getElementById('global-earnings-amount');
        const messageElement = document.getElementById('global-earnings-message');
        const balanceElement = document.getElementById('global-total-balance');
        
        if (amountElement) amountElement.textContent = '$' + parseFloat(amount || 0).toFixed(4);
        if (messageElement) messageElement.textContent = message;
        if (balanceElement) balanceElement.textContent = parseFloat(newBalance || 0).toFixed(4);
        
        // Show modal
        const bsModal = new bootstrap.Modal(earningsModal);
        bsModal.show();
    }
    
    // Setup event handlers for video buttons
    function setupVideoEventHandlers() {
        console.log('🔧 Setting up video event handlers...');
        
        // Handle all watch-btn clicks (for gallery.blade.php compatibility)
        document.addEventListener('click', function(e) {
            if (e.target.matches('.watch-btn, .watch-btn *')) {
                const button = e.target.closest('.watch-btn');
                if (!button) return;
                
                console.log('🎯 Watch button clicked:', button);
                
                // Check if this is already being handled by the original gallery system
                if (button.disabled || button.innerHTML.includes('Processing') || button.innerHTML.includes('spinner')) {
                    console.log('⏸️ Button already being processed, skipping global handler');
                    return;
                }
                
                // Only prevent default if we're going to handle it
                const videoId = button.getAttribute('data-video-id') || button.dataset.videoId;
                if (!videoId) {
                    console.log('⚠️ No video ID found, letting original handler take over');
                    return; // Let original handler deal with it
                }
                
                // Try to extract video URL quickly
                const videoCard = button.closest('.video-card') || button.closest('[data-video-id]');
                const iframe = videoCard ? videoCard.querySelector('iframe') : null;
                const hasVideoUrl = iframe && iframe.src && iframe.src !== '';
                
                if (!hasVideoUrl) {
                    console.log('⚠️ No video URL found quickly, letting original gallery handler take over');
                    return; // Let original handler deal with it
                }
                
                // Only now prevent default since we can handle it
                e.preventDefault();
                
                // Disable button during processing
                button.disabled = true;
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="fe fe-spinner me-2"></i>Loading...';
                
                // Find video card and extract data
                let videoTitle = 'Video';
                let videoUrl = '';
                let earningAmount = '0.00';
                
                if (videoCard) {
                    const titleElement = videoCard.querySelector('.card-title, h6, .video-title');
                    if (titleElement) videoTitle = titleElement.textContent.trim();
                    
                    // Enhanced iframe/video URL extraction
                    if (iframe) {
                        videoUrl = iframe.src || iframe.getAttribute('data-src') || iframe.getAttribute('data-lazy');
                        
                        // Clean up URL by removing extra parameters that might cause issues
                        if (videoUrl && videoUrl.includes('?')) {
                            // Keep essential YouTube parameters but remove origin and enablejsapi for modal
                            if (videoUrl.includes('youtube.com/embed/')) {
                                const baseUrl = videoUrl.split('?')[0];
                                videoUrl = baseUrl + '?autoplay=0&rel=0';
                            }
                        }
                        
                        console.log('🔍 Iframe details:', {
                            originalSrc: iframe.src,
                            cleanedUrl: videoUrl
                        });
                    }
                    
                    // Find earning amount by searching for badges with dollar signs
                    const badges = videoCard.querySelectorAll('.badge');
                    for (const badge of badges) {
                        const badgeText = badge.textContent;
                        if (badgeText.includes('$')) {
                            const match = badgeText.match(/\$[\d.,]+/);
                            if (match) {
                                earningAmount = match[0].replace('$', '');
                                break;
                            }
                        }
                    }
                    
                    // Fallback: extract from button text
                    if (!earningAmount || earningAmount === '0.00') {
                        const buttonText = button.textContent;
                        const match = buttonText.match(/\$[\d.,]+/);
                        if (match) {
                            earningAmount = match[0].replace('$', '');
                        }
                    }
                }
                
                console.log('🎬 Extracted video data:', { videoId, videoTitle, videoUrl, earningAmount });
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalHtml;
                
                // Check if we have video URL
                if (videoUrl && videoUrl !== '') {
                    // Play video using global system
                    window.playVideo(videoId, videoTitle, videoUrl, earningAmount);
                } else {
                    console.log('⚠️ No video URL found after extraction, this should not happen');
                    // This should not happen since we checked earlier, but just in case
                    alert('Error: Video URL not found. Please refresh the page and try again.');
                }
            }
        });
        
        console.log('✅ Video event handlers setup complete');
    }
    
    // Setup global video modals (inject into DOM if not present)
    function setupGlobalVideoModals() {
        console.log('🏗️ Setting up global video modals...');
        
        // Video Watch Modal
        if (!document.getElementById('globalVideoModal')) {
            console.log('📱 Creating video watch modal...');
            const videoModalHTML = `
                <div class="modal fade" id="globalVideoModal" tabindex="-1" aria-labelledby="globalVideoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="globalVideoModalLabel">
                                    <i class="fe fe-play me-2"></i><span id="global-modal-video-title">Watch Video to Earn</span>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="position-relative">
                                    <div class="ratio ratio-16x9">
                                        <iframe id="global-video-iframe" 
                                                src="" 
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                        </iframe>
                                    </div>
                                    
                                    <div id="global-video-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" 
                                         style="background: rgba(0,0,0,0.8); z-index: 10; backdrop-filter: blur(5px);">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <div class="text-center text-white">
                                                <div class="spinner-border mb-3" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <h5>Loading video...</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <div class="container-fluid">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <h6 class="mb-1">
                                                    <i class="fe fe-dollar-sign text-success"></i>
                                                    Earning: $<span id="global-earning-amount">0.00</span>
                                                </h6>
                                                <small class="text-muted">Watch for <span id="global-minimum-time">20</span> seconds to earn</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">Watch Progress</small>
                                                    <span class="badge bg-primary" id="global-watch-progress">0s / 20s</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="global-progress-bar"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                            <button type="button" class="btn btn-success btn-lg d-none" id="global-complete-btn">
                                                <i class="fe fe-check me-2"></i>Complete & Earn
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fe fe-x me-2"></i>Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', videoModalHTML);
            console.log('✅ Video watch modal created successfully');
        } else {
            console.log('ℹ️ Video watch modal already exists');
        }
        
        // Earnings Modal
        if (!document.getElementById('globalEarningsModal')) {
            console.log('🏆 Creating earnings modal...');
            const earningsModalHTML = `
                <div class="modal fade" id="globalEarningsModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fe fe-award"></i> Congratulations!
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="mb-3">
                                    <i class="fe fe-dollar-sign fa-3x text-success"></i>
                                </div>
                                <h4 id="global-earnings-amount" class="text-success">$0.00</h4>
                                <p id="global-earnings-message" class="text-muted">You've earned money by watching the video!</p>
                                <div class="alert alert-info">
                                    <strong>Your Total Balance:</strong> $<span id="global-total-balance">0.00</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" id="global-continue-watching-btn" data-bs-dismiss="modal">
                                    <i class="fe fe-check"></i> Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', earningsModalHTML);
            console.log('✅ Earnings modal created successfully');
        } else {
            console.log('ℹ️ Earnings modal already exists');
        }
        
        console.log('✅ Global video modals setup complete');
    }
    
    // Test function for debugging
    window.testVideoSystem = function() {
        console.log('🧪 Testing video system...');
        
        // Check Bootstrap
        if (typeof window.bootstrap !== 'undefined') {
            console.log('✅ Bootstrap is available');
        } else {
            console.log('❌ Bootstrap is NOT available');
        }
        
        // Check modals
        const videoModal = document.getElementById('globalVideoModal');
        const earningsModal = document.getElementById('globalEarningsModal');
        
        console.log('Video Modal:', videoModal ? '✅ Found' : '❌ Missing');
        console.log('Earnings Modal:', earningsModal ? '✅ Found' : '❌ Missing');
        
        // Test iframe inspection on current page
        const videoCards = document.querySelectorAll('.video-card');
        console.log(`📺 Found ${videoCards.length} video cards on page`);
        
        videoCards.forEach((card, index) => {
            const iframe = card.querySelector('iframe');
            const videoId = card.getAttribute('data-video-id');
            console.log(`Card ${index + 1}:`, {
                videoId: videoId,
                hasIframe: !!iframe,
                iframeSrc: iframe ? iframe.src : 'No iframe',
                cardData: card.dataset
            });
        });
        
        // Test with sample video
        if (videoModal && typeof window.bootstrap !== 'undefined') {
            console.log('🎬 Testing sample video...');
            window.playVideo(
                'test123',
                'Test Video',
                'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0&rel=0',
                '0.50',
                5
            );
        }
        
        console.log('🧪 Video system test complete');
    };
    
    // Initialize when DOM is ready
    function initializeVideoSystem() {
        console.log('🚀 Initializing global video system...');
        
        // Wait for Bootstrap to be available
        if (typeof window.bootstrap !== 'undefined') {
            setupGlobalVideoModals();
            setupVideoEventHandlers();
            console.log('✅ Global video system initialized successfully');
        } else {
            // Bootstrap not ready, try again in 100ms
            setTimeout(initializeVideoSystem, 100);
        }
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeVideoSystem);
    } else {
        initializeVideoSystem();
    }
    
})();
</script>

{{-- Fallback JavaScript for client-side detection --}}
<script>
(function() {
    'use strict';
    
    // Client-side device detection fallback
    function detectDevice() {
        const width = window.innerWidth;
        const userAgent = navigator.userAgent.toLowerCase();
        const isMobileUA = /android|webos|iphone|ipad|ipod|blackberry|iemobile|mobile|phone/.test(userAgent);
        const isMobileScreen = width <= 991;
        
        return isMobileUA || isMobileScreen;
    }
    
    // Check if we need to reload with correct layout
    function checkLayoutCorrectness() {
        const isMobileDevice = detectDevice();
        const currentLayout = document.querySelector('.mobile-optimized') ? 'mobile' : 'desktop';
        const expectedLayout = isMobileDevice ? 'mobile' : 'desktop';
        
        console.log('Device Type:', isMobileDevice ? 'Mobile' : 'Desktop');
        console.log('Current Layout:', currentLayout);
        console.log('Expected Layout:', expectedLayout);
        
        // If layout doesn't match, send screen width and reload
        if (currentLayout !== expectedLayout) {
            console.log('Layout mismatch detected, reloading with correct layout...');
            
            // Set screen width header for next request
            if ('sendBeacon' in navigator) {
                // Modern browsers
                const formData = new FormData();
                formData.append('screen_width', window.innerWidth);
                navigator.sendBeacon(window.location.href, formData);
            }
            
            // Set cookie for server-side detection
            document.cookie = `screen_width=${window.innerWidth}; path=/; max-age=3600`;
            
            // Reload the page
            setTimeout(() => {
                window.location.reload();
            }, 100);
        }
    }
    
    // Run detection when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkLayoutCorrectness);
    } else {
        checkLayoutCorrectness();
    }
    
    // Also check on resize (with debounce)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(checkLayoutCorrectness, 300);
    });
})();
</script>
