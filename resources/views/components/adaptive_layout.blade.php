<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Documentation') - {{ config('app.name') }}</title>
    <meta name="Description" content="PayPerViews Documentation and Help Center">
    <meta name="Author" content="PayPerViews">
    <meta name="keywords" content="documentation,help,guide,faq,privacy,terms">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    @php
        $faviconUrl = asset('favicon.svg');
        if (isset($settings) && $settings && $settings->favicon) {
            $faviconUrl = getMediaUrl($settings->favicon, 'favicon');
        }
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">

    <!-- Bootstrap Css -->
    <link id="style" href="{{asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" >

    <!-- Style Css -->
    <link href="{{asset('assets/css/styles.min.css')}}" rel="stylesheet" >

    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" >

    <!-- Node Waves Css -->
    <link href="{{asset('assets/libs/node-waves/waves.min.css')}}" rel="stylesheet" >

    <!-- Simplebar Css -->
    <link href="{{asset('assets/libs/simplebar/simplebar.min.css')}}" rel="stylesheet" >

    @stack('styles')
    
    @auth
        <!-- Authenticated User: Use Smart Layout Styles -->
        <style>
            /* Authenticated users get the full dashboard experience */
            .auth-user-notice {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 0.75rem 0;
                text-align: center;
                font-size: 0.875rem;
                border-bottom: 3px solid #5a67d8;
            }
            
            .auth-user-notice a {
                color: #ffffff;
                text-decoration: underline;
                font-weight: 600;
            }
            
            .auth-user-notice a:hover {
                color: #e2e8f0;
            }
            
            .auth-content-wrapper {
                background: #f8f9fa;
                min-height: calc(100vh - 80px);
                padding: 2rem 0;
            }
            
            .auth-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                padding: 2rem;
                margin-bottom: 2rem;
            }
            
            .back-to-dashboard {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                border-radius: 50px;
                padding: 15px 25px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                transition: all 0.3s ease;
            }
            
            .back-to-dashboard:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
                color: white;
            }
        </style>
    @else
        <!-- Non-authenticated User: Use Universal Layout Styles -->
        <style>
            /* Universal header styles for guests */
            .universal-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 1rem 0;
                box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            }
            
            .universal-nav {
                background: #fff;
                border-bottom: 1px solid #e9ecef;
                padding: 0.5rem 0;
            }
            
            .universal-nav .nav-link {
                color: #495057;
                font-weight: 500;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                transition: all 0.3s ease;
            }
            
            .universal-nav .nav-link:hover,
            .universal-nav .nav-link.active {
                background-color: #667eea;
                color: #fff;
            }
            
            .universal-content {
                min-height: calc(100vh - 200px);
                padding: 2rem 0;
                background: #fff;
                color: #212529;
            }
            
            .universal-footer {
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
                padding: 2rem 0;
                margin-top: 4rem;
                color: #6c757d;
            }
        </style>
    @endauth
    
    <!-- Common Styles -->
    <style>
        /* Base text color settings */
        body {
            color: #212529;
            background-color: #fff;
        }
        
        .universal-content h1,
        .universal-content h2,
        .universal-content h3,
        .universal-content h4,
        .universal-content h5,
        .universal-content h6 {
            color: #212529;
        }
        
        .universal-content p,
        .universal-content div,
        .universal-content span {
            color: #212529;
        }
        
        .card-title {
            color: #212529 !important;
        }
        
        .card-text {
            color: #6c757d !important;
        }
        
        /* Custom badge and utility styles */
        .bg-primary-soft {
            background-color: rgba(102, 126, 234, 0.1) !important;
        }
        
        .text-primary {
            color: #667eea !important;
        }
        
        /* Link styles */
        a {
            color: #667eea;
            text-decoration: none;
        }
        
        a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .text-decoration-none {
            text-decoration: none !important;
        }
        
        .text-decoration-none:hover {
            text-decoration: none !important;
        }
        
        /* Ensure proper text colors in lists */
        .list-group-item {
            color: #212529;
        }
        
        .list-group-item h6 {
            color: #212529 !important;
        }
        
        /* Search and content styles */
        .search-container {
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        .search-input {
            border-radius: 50px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0,0,0,0.15);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .universal-header h1 {
                font-size: 1.5rem;
            }
            
            .universal-nav .nav-link {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <!-- Page Loader -->
    <div id="loader">
        <img src="{{asset('assets/images/media/loader.svg')}}" alt="">
    </div>

    <div class="page">
        <!-- Guest User Layout (Documentation is guest-only) -->
        <header class="universal-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="text-white mb-0">
                            <i class="ri-book-open-line me-2"></i>
                            @yield('page-title', 'Documentation Center')
                        </h1>
                        <p class="text-white-50 mb-0 mt-1">
                            @yield('page-description', 'Find answers, guides, and helpful resources')
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-outline-light btn-sm me-2" onclick="copyToClipboard(window.location.href, this)" title="Copy page link">
                            <i class="ri-link me-1"></i> Copy Link
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm me-2">
                            <i class="ri-login-circle-line me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-user-add-line me-1"></i> Register
                        </a>
                    </div>
                </div>
            </div>
        </header>

            <!-- Universal Navigation -->
            <nav class="universal-nav">
                <div class="container">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('docs.index') ? 'active' : '' }}" href="{{ route('docs.index') }}">
                                <i class="ri-file-text-line me-1"></i> Documentation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">
                                <i class="ri-question-answer-line me-1"></i> FAQ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('privacy-policy') ? 'active' : '' }}" href="{{ route('privacy-policy') }}">
                                <i class="ri-shield-check-line me-1"></i> Privacy Policy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('terms-and-conditions') ? 'active' : '' }}" href="{{ route('terms-and-conditions') }}">
                                <i class="ri-file-list-3-line me-1"></i> Terms & Conditions
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="universal-content">
                <div class="container">
                    {{ $slot }}
                </div>
            </main>

            <!-- Universal Footer -->
            <footer class="universal-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>PayPerViews Documentation</h5>
                            <p class="text-muted mb-0">
                                Your comprehensive guide to understanding and using our platform effectively.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Quick Links</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('docs.index') }}" class="text-muted">Documentation</a></li>
                                        <li><a href="{{ route('faq') }}" class="text-muted">FAQ</a></li>
                                        <li><a href="{{ route('register') }}" class="text-muted">Join Now</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Legal</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('privacy-policy') }}" class="text-muted">Privacy Policy</a></li>
                                        <li><a href="{{ route('terms-and-conditions') }}" class="text-muted">Terms & Conditions</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="text-center text-muted">
                        <small>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
                    </div>
                </div>
            </footer>

        <!-- Floating Share Button for Guests -->
        <button class="floating-share-btn" onclick="toggleSharePanel()" title="Share this page">
            <i class="ri-share-line"></i>
        </button>
    </div>

    <!-- Common Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <i class="ri-check-circle-line me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <i class="ri-error-warning-line me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Floating Share Panel -->
    <div class="share-panel" id="sharePanel">
        <button class="share-btn facebook" onclick="shareToFacebook()">
            <i class="ri-facebook-fill me-1"></i> Facebook
        </button>
        <button class="share-btn twitter" onclick="shareToTwitter()">
            <i class="ri-twitter-fill me-1"></i> Twitter
        </button>
        <button class="share-btn linkedin" onclick="shareToLinkedIn()">
            <i class="ri-linkedin-fill me-1"></i> LinkedIn
        </button>
        <button class="share-btn email" onclick="shareViaEmail()">
            <i class="ri-mail-fill me-1"></i> Email
        </button>
        <button class="share-btn" onclick="copyToClipboard(window.location.href, this)">
            <i class="ri-link me-1"></i> Copy Link
        </button>
        <button class="share-btn" onclick="toggleSharePanel()" style="background: #6c757d; color: white;">
            <i class="ri-close-line"></i> Close
        </button>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Node Waves JS -->
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>

    <!-- Main Theme Js -->
    <script src="{{asset('assets/js/main.js')}}"></script>

    @stack('script')

    <!-- Page Loading Script -->
    <script>
        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.display = 'none';
            }
        });

        // Search functionality (if search input exists)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Adaptive layout script loaded for GUEST users');
            
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                console.log('Found search input with class .search-input');
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.trim();
                        if (searchTerm) {
                            window.location.href = `{{ route('docs.index') }}?search=${encodeURIComponent(searchTerm)}`;
                        }
                    }
                });
            } else {
                console.log('No search input with class .search-input found (this is normal)');
            }

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert.position-fixed');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Copy Link Functionality
            addCopyLinkFeature();
        });

        @auth
        // Additional functionality for authenticated users
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for dashboard navigation
            const dashboardLinks = document.querySelectorAll('a[href*="dashboard"]');
            dashboardLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Add loading state
                    this.classList.add('loading');
                    this.innerHTML = '<i class="ri-loader-4-line me-1 spin"></i> Loading...';
                });
            });
        });
        @endauth

        // Copy Link Feature
        function addCopyLinkFeature() {
            // Only add copy link button to page header for guest users (auth users have it in Quick Actions)
            @guest
            const pageHeader = document.querySelector('h1');
            if (pageHeader && !pageHeader.querySelector('.copy-link-btn')) {
                const copyButton = document.createElement('button');
                copyButton.className = 'btn btn-outline-secondary btn-sm ms-2 copy-link-btn';
                copyButton.innerHTML = '<i class="ri-link me-1"></i> Copy Link';
                copyButton.title = 'Copy page link to clipboard';
                
                copyButton.addEventListener('click', function() {
                    copyToClipboard(window.location.href, this);
                });
                
                pageHeader.appendChild(copyButton);
            }
            @endguest

            // Add copy buttons to all headings in content
            const contentArea = document.querySelector('.markdown-content, .auth-card, .universal-content');
            if (contentArea) {
                const headings = contentArea.querySelectorAll('h1, h2, h3, h4, h5, h6');
                headings.forEach(function(heading, index) {
                    // Skip if heading already has a copy button
                    if (heading.querySelector('.heading-copy-btn')) return;
                    
                    // Generate ID if not present
                    if (!heading.id) {
                        heading.id = 'heading-' + index;
                    }
                    
                    // Add copy link button
                    const copyButton = document.createElement('button');
                    copyButton.className = 'heading-copy-btn';
                    copyButton.innerHTML = '<i class="ri-link"></i>';
                    copyButton.title = 'Copy link to this section';
                    
                    copyButton.addEventListener('click', function() {
                        const url = window.location.origin + window.location.pathname + '#' + heading.id;
                        copyToClipboard(url, this);
                    });
                    
                    heading.style.position = 'relative';
                    heading.appendChild(copyButton);
                });
            }
        }

        // Copy to clipboard function
        function copyToClipboard(text, button) {
            console.log('Copy to clipboard called with:', text);
            
            if (navigator.clipboard && window.isSecureContext) {
                // Modern clipboard API
                navigator.clipboard.writeText(text).then(function() {
                    console.log('Copy successful via clipboard API');
                    showCopySuccess(button);
                }).catch(function(err) {
                    console.log('Clipboard API failed, trying fallback:', err);
                    fallbackCopyToClipboard(text, button);
                });
            } else {
                console.log('Using fallback copy method');
                // Fallback for older browsers
                fallbackCopyToClipboard(text, button);
            }
        }

        // Fallback copy method
        function fallbackCopyToClipboard(text, button) {
            console.log('Using fallback copy method for:', text);
            
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                console.log('execCommand copy result:', successful);
                
                if (successful) {
                    showCopySuccess(button);
                } else {
                    showCopyError(button);
                }
            } catch (err) {
                console.log('execCommand copy failed:', err);
                showCopyError(button);
            }
            
            document.body.removeChild(textArea);
        }

        // Show copy success
        function showCopySuccess(button) {
            const originalHTML = button.innerHTML;
            const originalClasses = button.className;
            
            button.innerHTML = '<i class="ri-check-line me-1"></i> Copied!';
            
            // Handle different button types
            if (button.classList.contains('btn-outline-success')) {
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-success');
            } else if (button.classList.contains('btn-outline-light')) {
                button.classList.remove('btn-outline-light');
                button.classList.add('btn-success');
            } else {
                button.classList.add('btn-success');
            }
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.className = originalClasses; // Restore original classes
            }, 2000);
        }

        // Show copy error
        function showCopyError(button) {
            const originalHTML = button.innerHTML;
            const originalClasses = button.className;
            
            button.innerHTML = '<i class="ri-error-warning-line me-1"></i> Failed';
            button.classList.add('btn-danger');
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.className = originalClasses; // Restore original classes
            }, 2000);
        }

        // Share Panel Functions
        function toggleSharePanel() {
            const panel = document.getElementById('sharePanel');
            panel.classList.toggle('active');
        }

        function shareToFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function shareToTwitter() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
        }

        function shareToLinkedIn() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
        }

        function shareViaEmail() {
            const url = window.location.href;
            const title = document.title;
            const body = `Check out this documentation: ${title}\n\n${url}`;
            window.location.href = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(body)}`;
        }

        // Add keyboard shortcut for copy link (Ctrl+Shift+C)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'C') {
                e.preventDefault();
                copyToClipboard(window.location.href, { innerHTML: 'Keyboard shortcut' });
            }
            
            // Add keyboard shortcut to toggle share panel (Ctrl+Shift+S)
            if (e.ctrlKey && e.shiftKey && e.key === 'S') {
                e.preventDefault();
                toggleSharePanel();
            }
        });
    </script>

    <!-- Additional CSS for animations -->
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .spin {
            animation: spin 1s linear infinite;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .alert.position-fixed {
            animation: slideInRight 0.3s ease-out;
            max-width: 400px;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Copy Link Button Styles */
        .copy-link-btn {
            border-radius: 20px;
            font-size: 0.8rem;
            padding: 5px 12px;
            transition: all 0.3s ease;
        }

        .copy-link-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .heading-copy-btn {
            position: absolute;
            right: -35px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #6c757d;
            font-size: 1rem;
            padding: 5px;
            border-radius: 4px;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .heading-copy-btn:hover {
            color: #495057;
            background: #f8f9fa;
            transform: translateY(-50%) scale(1.1);
        }

        h1:hover .heading-copy-btn,
        h2:hover .heading-copy-btn,
        h3:hover .heading-copy-btn,
        h4:hover .heading-copy-btn,
        h5:hover .heading-copy-btn,
        h6:hover .heading-copy-btn {
            opacity: 1;
        }

        /* Mobile responsiveness for copy buttons */
        @media (max-width: 768px) {
            .heading-copy-btn {
                position: relative;
                right: auto;
                top: auto;
                transform: none;
                opacity: 1;
                margin-left: 10px;
                display: inline-block;
            }
            
            .copy-link-btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
            
            /* Stack Quick Actions buttons on mobile for authenticated users */
            @auth
            .auth-card .row .col-md-4.text-md-end {
                text-align: center !important;
                margin-top: 1rem;
            }
            
            .auth-card .row .col-md-4.text-md-end .btn {
                margin: 0.25rem;
                font-size: 0.8rem;
                padding: 6px 12px;
            }
            @endauth
            
            /* Stack header buttons on mobile for guests */
            @guest
            .universal-header .col-md-4.text-md-end {
                text-align: center !important;
                margin-top: 1rem;
            }
            
            .universal-header .col-md-4.text-md-end .btn {
                margin: 0.25rem;
                font-size: 0.8rem;
                padding: 6px 12px;
            }
            @endguest
        }

        /* Floating Share Panel */
        .share-panel {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 15px;
            z-index: 1000;
            display: none;
            flex-direction: column;
            gap: 10px;
        }

        .share-panel.active {
            display: flex;
        }

        .share-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .share-btn:hover {
            transform: translateY(-2px);
        }

        .share-btn.facebook {
            background: #4267B2;
            color: white;
        }

        .share-btn.twitter {
            background: #1DA1F2;
            color: white;
        }

        .share-btn.linkedin {
            background: #0077b5;
            color: white;
        }

        .share-btn.email {
            background: #ea4335;
            color: white;
        }

        /* Floating Share Button */
        .floating-share-btn {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 999;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }

        .floating-share-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #218838, #1ba085);
        }

        /* Animation for share button */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .floating-share-btn.pulse {
            animation: pulse 2s infinite;
        }
    </style>
</body>
</html>
