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
    
    <!-- Universal Layout Styles -->
    <style>
        /* Universal header styles */
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
        }
        
        .universal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 2rem 0;
            margin-top: 4rem;
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
        <!-- Universal Header -->
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
                        @auth
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="ri-dashboard-line me-1"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-sm me-2">
                                <i class="ri-login-circle-line me-1"></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">
                                <i class="ri-user-add-line me-1"></i> Register
                            </a>
                        @endauth
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
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.support.index') }}">
                                <i class="ri-customer-service-2-line me-1"></i> Support
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="universal-content">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-check-circle-line me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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
                                    @auth
                                        <li><a href="{{ route('user.support.index') }}" class="text-muted">Support</a></li>
                                    @endauth
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
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Node Waves JS -->
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>

    <!-- Main Theme Js -->
    <script src="{{asset('assets/js/main.js')}}"></script>

    @stack('scripts')

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
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.trim();
                        if (searchTerm) {
                            window.location.href = `{{ route('docs.index') }}?search=${encodeURIComponent(searchTerm)}`;
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
