<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/images/brand-logos/favicon.ico')}}" type="image/x-icon">

    <!-- Choices JS -->
    <script src="{{asset('assets/libs/choices.js/public/assets/scripts/choices.min.js')}}"></script>

    <!-- Main Theme Js -->
    <script src="{{asset('assets/js/main.js')}}"></script> 

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

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/@simonwep/pickr/themes/nano.min.css')}}">

    <!-- Choices Css -->
    <link rel="stylesheet" href="{{asset('assets/libs/choices.js/public/assets/styles/choices.min.css')}}">

    <!-- Mobile Reload Prevention -->
    <style>
        /* Prevent pull-to-refresh on mobile devices */
        body, html {
            overscroll-behavior-y: none;
            touch-action: pan-x pan-y;
        }
        
        /* Prevent accidental refresh gestures */
        .page {
            overscroll-behavior: none;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Disable pull-to-refresh specifically */
        @media (max-width: 768px) {
            body {
                overscroll-behavior-y: contain;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="page mobile-optimized">
        
        <!-- Mobile Top Header -->
        <div class="mobile-header-bar">
            <div class="mobile-header-content">
                <!-- Profile Picture Button (Left) -->
                @auth
                <a href="javascript:void(0);" 
                   class="mobile-profile-btn"
                   onclick="openMobileModal('profile')"
                   title="Profile Menu">
                    @php
                        $profileImage = auth()->user()->image 
                            ? asset('assets/images/users/'.auth()->user()->image) 
                            : asset('assets/images/users/16.jpg');
                    @endphp
                    <img src="{{$profileImage}}" 
                         alt="{{auth()->user()->username ?? 'Profile'}}" 
                         class="mobile-profile-avatar"
                         onerror="this.src='{{asset('assets/images/users/16.jpg')}}'">
                    <div class="online-dot"></div>
                </a>
                @else
                <a href="{{ route('login') }}" 
                   class="mobile-profile-btn"
                   title="Login">
                    <img src="{{asset('assets/images/users/9.jpg')}}" 
                         alt="Guest Profile" 
                         class="mobile-profile-avatar">
                </a>
                @endauth

                <!-- Center Logo/Brand -->
                <div class="mobile-brand">
                    <a href="{{ route('user.dashboard') }}" class="mobile-brand-link">
                        <i class="bx bx-video brand-icon"></i>
                        <span class="brand-text">PayPerViews</span>
                    </a>
                </div>

                <!-- Action Icons (Right) -->
                <div class="mobile-actions">
                    @auth
                        @php
                            $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                        @endphp
                        @if($totalMessagesCount > 0)
                            <a href="{{ route('user.messages') }}" class="mobile-action-btn" title="Messages">
                                <i class="bx bx-envelope"></i>
                                <span class="notification-badge">{{$totalMessagesCount}}</span>
                            </a>
                        @endif

                        <!-- Theme Toggle Button -->
                        <a href="javascript:void(0);" 
                           class="mobile-action-btn theme-toggle" 
                           onclick="toggleMobileTheme()"
                           title="Toggle Theme">
                            <i class="bx bx-moon theme-icon"></i>
                        </a>
                    @else
                        <!-- Login Button for Guest Users -->
                        <a href="{{ route('login') }}" class="mobile-action-btn" title="Login">
                            <i class="bx bx-log-in"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content mobile-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="mobile-navbar" role="navigation" aria-label="Mobile Navigation">
            <div class="mobile-nav-items">

                <!-- Videos -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link videos-link"
                   onclick="openMobileModal('videos')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-video nav-icon"></i>
                    </span>
                    <span class="nav-text">Videos</span>
                </a>

                <!-- Wallet -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link wallet-link"
                   onclick="openMobileModal('wallet')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-wallet nav-icon"></i>
                    </span>
                    <span class="nav-text">Wallet</span>
                    @auth
                        <span class="balance-indicator">${{number_format((auth()->user()->deposit_wallet + auth()->user()->interest_wallet) ?? 0, 2)}}</span>
                    @endauth
                </a>

                <!-- Lottery -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link lottery-link"
                   onclick="openMobileModal('lottery')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-gift nav-icon"></i>
                        @auth
                            @php
                                try {
                                    // Check if Lottery model exists before using it
                                    $activeLotteries = class_exists('\App\Models\Lottery') 
                                        ? \App\Models\Lottery::where('status', 'active')->count() 
                                        : 3; // Default fallback number
                                } catch (Exception $e) {
                                    $activeLotteries = 3; // Default fallback
                                }
                            @endphp
                            @if($activeLotteries > 0)
                                <span class="notification-dot">{{$activeLotteries}}</span>
                            @endif
                        @endauth
                    </span>
                    <span class="nav-text">Lottery</span>
                </a>

                <!-- Plan/Investment/VAPS -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link plan-link"
                   onclick="openMobileModal('grid')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-trending-up nav-icon"></i>
                        @auth
                            @php
                                try {
                                    // Check for active investment plans
                                    $activePlans = \App\Models\Invest::where('user_id', auth()->id())
                                                                      ->where('status', 'active')
                                                                      ->count();
                                } catch (Exception $e) {
                                    $activePlans = 0;
                                }
                            @endphp
                            @if($activePlans > 0)
                                <span class="notification-dot">{{$activePlans}}</span>
                            @endif
                        @endauth
                    </span>
                    <span class="nav-text">VAPS</span>
                </a>

                <!-- Notifications -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link notifications-link"
                   onclick="openMobileModal('notifications')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-bell nav-icon"></i>
                        @auth
                            @php
                                try {
                                    $unreadNotifications = \App\Models\UserNotification::where('user_id', auth()->id())
                                                                ->where('read', false)
                                                                ->count();
                                } catch (Exception $e) {
                                    $unreadNotifications = 0;
                                }
                            @endphp
                            @if($unreadNotifications > 0)
                                <span class="notification-dot">{{$unreadNotifications}}</span>
                            @endif
                        @endauth
                    </span>
                    <span class="nav-text">Alerts</span>
                </a>

                <!-- More/Menu -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link menu-link"
                   onclick="openMobileModal('more')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-grid-alt nav-icon"></i>
                    </span>
                    <span class="nav-text">More</span>
                </a>
            </div>
        </nav>

        <!-- Mobile Footer -->
        <footer class="mobile-footer">
            <div class="text-center">
                <span class="text-muted small"> 
                    © <span id="year-mobile"></span> <strong>PayPerViews</strong>. All rights reserved.
                </span>
            </div>
        </footer>
    </div>

    <!-- Home Dashboard Modal - COMPLETELY RECREATED FOR HORIZONTAL LAYOUT -->
    <div class="modal fade" id="mobileHomeModal" tabindex="-1" aria-labelledby="mobileHomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-primary text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileHomeModalLabel">
                        <i class="bx bx-home me-2"></i>Dashboard & Overview
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- Quick Stats Display -->
                    <div class="stats-section mb-4 p-3 bg-primary bg-opacity-10 rounded-3">
                        <div class="row text-center g-0">
                            <div class="col-6">
                                <h4 class="text-primary mb-1">${{number_format((auth()->user()->deposit_wallet + auth()->user()->interest_wallet) ?? 0, 2)}}</h4>
                                <small class="text-muted">Balance</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{auth()->user()->video_views_count ?? 0}}</h4>
                                <small class="text-muted">Video Views</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Dashboard Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-tachometer me-2"></i>Dashboard Actions
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-tachometer mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-white">Main Dashboard</strong>
                                        <small class="d-block text-white-50">Complete overview</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.generation-history') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-history mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">History</strong>
                                        <small class="d-block">View logs</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.team-tree') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-sitemap mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Team Tree</strong>
                                        <small class="d-block">Network</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-flash me-2"></i>Quick Access
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('referral.index') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-users mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Referrals</strong>
                                        <small class="d-block">Invite & earn</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-play-circle mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Videos</strong>
                                        <small class="d-block">Start earning</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-user-circle display-1 text-muted mb-3"></i>
                        <h5>Welcome to PayPerViews</h5>
                        <p class="text-muted">Please login to access your dashboard</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="bx bx-log-in me-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="bx bx-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Videos & Earnings Modal - COMPLETELY RECREATED FOR HORIZONTAL LAYOUT -->
    <div class="modal fade" id="mobileVideosModal" tabindex="-1" aria-labelledby="mobileVideosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-info text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileVideosModalLabel">
                        <i class="bx bx-video me-2"></i>Videos & Earnings
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- Video Stats Display -->
                    <div class="stats-section mb-4 p-3 bg-info bg-opacity-10 rounded-3">
                        <div class="row text-center g-0">
                            <div class="col-6">
                                <h4 class="text-info mb-1">{{auth()->user()->video_views_count ?? 0}}</h4>
                                <small class="text-muted">Videos Watched</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">${{number_format(auth()->user()->total_video_earnings ?? 0, 2)}}</h4>
                                <small class="text-muted">Total Earnings</small>
                            </div>
                        </div>
                    </div>

                    <!-- Watch Videos Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-play-circle me-2"></i>Watch & Earn
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-play-circle mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-white">Start Watching</strong>
                                        <small class="d-block text-white-50">Watch & earn money</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.video-views.gallery') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-collection mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Gallery</strong>
                                        <small class="d-block">Browse videos</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-dollar mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Earnings</strong>
                                        <small class="d-block">View income</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- History Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-history me-2"></i>History & Analytics
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-secondary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-history mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Watch History</strong>
                                        <small class="d-block">Track viewing activity</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-video display-1 text-muted mb-3"></i>
                        <h5>Start Earning with Videos</h5>
                        <p class="text-muted">Login to watch videos and earn money</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-info btn-lg">
                            <i class="bx bx-log-in me-2"></i>Login to Watch Videos
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-info">
                            <i class="bx bx-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet & Finance Modal - NEWLY CREATED -->
    <div class="modal fade" id="mobileWalletModal" tabindex="-1" aria-labelledby="mobileWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-success text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileWalletModalLabel">
                        <i class="bx bx-wallet me-2"></i>Wallet & Finance
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- Balance Display -->
                    <div class="balance-card text-center mb-4 p-4 bg-gradient bg-success bg-opacity-10 rounded-3">
                        <div class="balance-display">
                            <h2 class="text-success mb-1 fw-bold">${{number_format((auth()->user()->deposit_wallet + auth()->user()->interest_wallet) ?? 0, 2)}}</h2>
                            <p class="text-muted mb-3">Available Balance</p>
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted d-block">Deposit Wallet</small>
                                    <strong class="text-success">${{number_format(auth()->user()->deposit_wallet ?? 0, 2)}}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Interest Wallet</small>
                                    <strong class="text-warning">${{number_format(auth()->user()->interest_wallet ?? 0, 2)}}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-flash me-2"></i>Quick Actions
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('deposit.index') }}" class="btn btn-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-plus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Deposit</strong>
                                        <small class="d-block text-white-50">Add funds</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-minus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Withdraw</strong>
                                        <small class="d-block text-white-50">Cash out</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-transfer me-2"></i>Transactions & History
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.transfer_funds') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-transfer mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Transfer Funds</strong>
                                        <small class="d-block">Move money between wallets</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('deposit.history') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-history mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Deposit History</strong>
                                        <small class="d-block">Past deposits</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.withdraw.wallet.history') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-receipt mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Withdraw History</strong>
                                        <small class="d-block">Past withdrawals</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-wallet display-1 text-muted mb-3"></i>
                        <h5>Access Your Wallet</h5>
                        <p class="text-muted">Login to manage your finances and transactions</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-success btn-lg">
                            <i class="bx bx-log-in me-2"></i>Login to Wallet
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-success">
                            <i class="bx bx-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Profile & Settings Modal - NEWLY CREATED -->
    <div class="modal fade" id="mobileProfileModal" tabindex="-1" aria-labelledby="mobileProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-primary text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileProfileModalLabel">
                        <i class="bx bx-menu me-2"></i>Main Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- User Info Quick Header -->
                    <div class="user-quick-info text-center mb-4 p-3 bg-primary bg-opacity-10 rounded-3">
                        @php
                            $profileImage = auth()->user()->image 
                                ? asset('assets/images/users/'.auth()->user()->image) 
                                : asset('assets/images/users/9.jpg');
                        @endphp
                        <img src="{{$profileImage}}" 
                             alt="{{auth()->user()->username ?? 'Profile'}}" 
                             class="rounded-circle mb-2"
                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #007bff;"
                             onerror="this.src='{{asset('assets/images/users/9.jpg')}}'">
                        <h6 class="mb-1">{{auth()->user()->username ?? 'User'}}</h6>
                        <small class="text-muted">Welcome back!</small>
                    </div>

                    <!-- Main Navigation Menu -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-home me-2"></i>Dashboard & Home
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-home mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Dashboard</strong>
                                        <small class="d-block text-white-50">Main overview</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.video-views.index') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-video mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Videos</strong>
                                        <small class="d-block">Watch & earn</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Menu -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-wallet me-2"></i>Wallet & Finance
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-wallet mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Wallet</strong>
                                        <small class="d-block text-white-50">Balance & funds</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-transfer mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Video Access Security</strong>
                                        <small class="d-block">Video Access Security Plan</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('deposit.index') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-plus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Deposit</strong>
                                        <small class="d-block">Add funds</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-minus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Withdraw</strong>
                                        <small class="d-block">Cash out</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Lottery & Games -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-gift me-2"></i>Lottery & Games
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('lottery.index') }}" class="btn btn-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-gift mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Lottery</strong>
                                        <small class="d-block text-dark">Active draws</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('lottery.my.tickets') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-ticket mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">My Tickets</strong>
                                        <small class="d-block">View tickets</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile & Account -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-user me-2"></i>Profile & Account
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('profile.edit') }}" class="btn btn-secondary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-edit mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Edit Profile</strong>
                                        <small class="d-block text-white-50">Update info</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-bell mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Notifications</strong>
                                        <small class="d-block">View alerts</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('profile.security') }}" class="btn btn-outline-danger w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-shield mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Security</strong>
                                        <small class="d-block">2FA & settings</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.kyc.index') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-id-card mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">KYC</strong>
                                        <small class="d-block">Verification</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Support & More -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-help-circle me-2"></i>Support & More
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.messages') }}" class="btn btn-info w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-envelope mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Messages</strong>
                                        <small class="d-block text-white-50">Chat & support</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <button onclick="confirmLogout()" class="btn btn-danger w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-log-out mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Logout</strong>
                                        <small class="d-block text-white-50">Sign out</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <img src="{{asset('assets/images/users/9.jpg')}}" 
                             alt="Profile" 
                             class="rounded-circle mb-3"
                             style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #6c757d;">
                        <h5>Guest User</h5>
                        <p class="text-muted">Login to access your profile settings</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                            <i class="bx bx-log-in me-2"></i>Login to Your Account
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-user-plus me-2"></i>Create New Account
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Plan/Investment/VAPS Modal - NEWLY CREATED -->
    {{-- <div class="modal fade" id="mobilePlanModal" tabindex="-1" aria-labelledby="mobilePlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header text-white py-3 mobile-modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);">
                    <h5 class="modal-title" id="mobilePlanModalLabel">
                        <i class="bx bx-trending-up me-2"></i>Plan/Investment/VAPS
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- Investment Stats Display -->
                    <div class="stats-section mb-4 p-3 bg-success bg-opacity-10 rounded-3">
                        <div class="row text-center g-0">
                            @php
                                try {
                                    $totalInvestment = \App\Models\Invest::where('user_id', auth()->id())->sum('amount') ?? 0;
                                    $activeInvestments = \App\Models\Invest::where('user_id', auth()->id())->where('status', 'active')->count() ?? 0;
                                    $totalReturns = \App\Models\Invest::where('user_id', auth()->id())->sum('total_return') ?? 0;
                                } catch (Exception $e) {
                                    $totalInvestment = 0;
                                    $activeInvestments = 0;
                                    $totalReturns = 0;
                                }
                            @endphp
                            <div class="col-4">
                                <h4 class="text-success mb-1">${{number_format($totalInvestment, 2)}}</h4>
                                <small class="text-muted">Total Invested</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-primary mb-1">{{$activeInvestments}}</h4>
                                <small class="text-muted">Active Plans</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-warning mb-1">${{number_format($totalReturns, 2)}}</h4>
                                <small class="text-muted">Total Returns</small>
                            </div>
                        </div>
                    </div>

                    <!-- VAPS (Video Access Protection System) Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-shield me-2"></i>Video Access Protection System (VAPS)
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('invest.index') }}" class="btn w-100 p-3 text-white mobile-feature-btn d-flex flex-column align-items-center" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                    <i class="bx bx-shield-check mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-white">VAPS Plans</strong>
                                        <small class="d-block text-white-75">Video Access Security</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('invest.history') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-history mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Investment History</strong>
                                        <small class="d-block">Past investments</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('invest.statistics') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-trending-up mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Statistics</strong>
                                        <small class="d-block">View analytics</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Plans Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-package me-2"></i>Available Investment Plans
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-star mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Basic Plan</strong>
                                        <small class="d-block">Start investing</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-crown mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Premium Plan</strong>
                                        <small class="d-block">High returns</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio Management Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-briefcase me-2"></i>Portfolio Management
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('invest.log') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-pie-chart mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Portfolio</strong>
                                        <small class="d-block">Investment logs</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('invest.statistics') }}" class="btn btn-outline-secondary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-calculator mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Calculator</strong>
                                        <small class="d-block">Plan returns</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-flash me-2"></i>Quick Actions
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('invest.index') }}" class="btn btn-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-refresh mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-white">New Investment</strong>
                                        <small class="d-block text-white-50">Start investing</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-money mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Withdraw</strong>
                                        <small class="d-block text-dark">Cash out</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-trending-up display-1 mb-3" style="color: #28a745;"></i>
                        <h5>Start Your Investment Journey</h5>
                        <p class="text-muted">Login to access VAPS and investment plans</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <i class="bx bx-log-in me-2"></i>Login to Invest
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-success">
                            <i class="bx bx-user-plus me-2"></i>Create Account & Invest
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Custom Grid Modal - 1x1 GRID LAYOUT (Perfect like mobileWalletModal) -->
    <div class="modal fade" id="mobileGridModal" tabindex="-1" aria-labelledby="mobileGridModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-info text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileGridModalLabel">
                        <i class="bx bx-grid me-2"></i>Video Access Plan Security (VAPS)
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth

                    <!-- Secondary Features Section -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-trending-up mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Investment Plans</strong>
                                        <small class="d-block">VAPS & more</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('deposit.index') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-plus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Deposit</strong>
                                        <small class="d-block">Add funds</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.withdraw') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-minus-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Invest Withdraw</strong>
                                        <small class="d-block">Cash out</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications & Alerts Modal - COMPLETELY RECREATED FOR HORIZONTAL LAYOUT -->
    <div class="modal fade" id="mobileNotificationsModal" tabindex="-1" aria-labelledby="mobileNotificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content" style="max-height: 85vh; overflow-y: auto;">
                <div class="modal-header bg-warning text-dark py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileNotificationsModalLabel">
                        <i class="bx bx-bell me-2"></i>Notifications & Alerts
                    </h5>
                    <button type="button" class="btn-close mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body" style="max-height: 70vh; overflow-y: auto;">
                    @auth
                    <!-- Notification Stats Display -->
                    <div class="stats-section mb-4 p-3 bg-warning bg-opacity-10 rounded-3">
                        <div class="row text-center g-0">
                            @php
                                try {
                                    $unreadNotifications = auth()->user()->unreadNotifications()->count();
                                    $totalNotifications = auth()->user()->notifications()->count();
                                } catch (Exception $e) {
                                    $unreadNotifications = 0;
                                    $totalNotifications = 0;
                                }
                            @endphp
                            <div class="col-6">
                                <h4 class="text-danger mb-1">{{$unreadNotifications}}</h4>
                                <small class="text-muted">Unread Alerts</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary mb-1">{{$totalNotifications}}</h4>
                                <small class="text-muted">Total Notifications</small>
                            </div>
                        </div>
                    </div>

                    <!-- View Notifications Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-bell-ring me-2"></i>View Notifications
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index') }}" class="btn btn-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center position-relative">
                                    <i class="bx bx-bell-ring mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-dark">All Notifications</strong>
                                        <small class="d-block text-dark-50">View all alerts</small>
                                    </div>
                                    @if($unreadNotifications > 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 translate-middle mobile-badge">{{$unreadNotifications}}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.notifications.settings') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-cog mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Settings</strong>
                                        <small class="d-block">Preferences</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Notifications Preview -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-list-ul me-2"></i>Recent Notifications
                        </h6>
                        <div class="notification-preview-list">
                            @php
                                try {
                                    $recentNotifications = auth()->user()->notifications()->latest()->take(3)->get();
                                } catch (Exception $e) {
                                    $recentNotifications = collect();
                                }
                            @endphp
                            @if($recentNotifications->count() > 0)
                                @foreach($recentNotifications as $notification)
                                <div class="notification-item p-3 mb-2 bg-light rounded-3 border-start border-warning border-3">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-3">
                                            <i class="bx bx-bell text-warning"></i>
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <h6 class="mb-1 small fw-bold">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </h6>
                                            <p class="mb-1 small text-muted">
                                                {{ Str::limit($notification->data['message'] ?? 'New notification received', 50) }}
                                            </p>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        @if(is_null($notification->read_at))
                                            <span class="badge bg-danger rounded-pill">New</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                <div class="text-center mt-3">
                                    <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bx bx-right-arrow-alt me-1"></i>View All Notifications
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bx bx-bell-off display-6 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No recent notifications</p>
                                    <small class="text-muted">You're all caught up!</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notification Types -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-category me-2"></i>Notification Types
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index', ['type' => 'system']) }}" class="btn btn-outline-secondary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-cog mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">System</strong>
                                        <small class="d-block">App updates</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index', ['type' => 'security']) }}" class="btn btn-outline-danger w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-shield mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Security</strong>
                                        <small class="d-block">Account alerts</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index', ['type' => 'transaction']) }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-dollar mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Financial</strong>
                                        <small class="d-block">Money updates</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('user.notifications.index', ['type' => 'general']) }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-info-circle mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">General</strong>
                                        <small class="d-block">Other notices</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-check-double me-2"></i>Quick Actions
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <button onclick="markAllNotificationsRead()" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-check-double mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Mark All Read</strong>
                                        <small class="d-block">Clear all unread</small>
                                    </div>
                                </button>
                            </div>
                            <div class="col-12">
                                <button onclick="clearAllNotifications()" class="btn btn-outline-danger w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-trash mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Clear All</strong>
                                        <small class="d-block">Delete notifications</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-bell display-1 text-muted mb-3"></i>
                        <h5>Stay Updated</h5>
                        <p class="text-muted">Login to receive important notifications</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg">
                            <i class="bx bx-log-in me-2"></i>Login for Notifications
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-warning">
                            <i class="bx bx-user-plus me-2"></i>Create Account
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- More & Additional Features Modal - NEWLY CREATED -->
    <div class="modal fade" id="mobileMoreModal" tabindex="-1" aria-labelledby="mobileMoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header bg-dark text-white py-3 mobile-modal-header">
                    <h5 class="modal-title" id="mobileMoreModalLabel">
                        <i class="bx bx-grid-alt me-2"></i>More Features
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    <!-- Messages & Communication Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-envelope me-2"></i>Messages & Communication
                        </h6>
                        <div class="row g-3">
                            @auth
                                @php
                                    $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                                    $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', 0)->count();
                                @endphp
                                <div class="col-6">
                                    <a href="{{ route('user.messages') }}" class="btn btn-primary w-100 p-3 mobile-feature-btn">
                                        <i class="bx bx-message-dots mb-1"></i>
                                        <div class="mobile-btn-content">
                                            <strong class="small">All Messages</strong>
                                            <small class="d-block text-white-50">{{$totalMessagesCount}} total</small>
                                        </div>
                                        @if($unreadMessagesCount > 0)
                                            <span class="badge bg-danger position-absolute top-0 end-0 translate-middle mobile-badge">{{$unreadMessagesCount}}</span>
                                        @endif
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('user.messages.inbox') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn">
                                        <i class="bx bx-inbox mb-1"></i>
                                        <div class="mobile-btn-content">
                                            <strong class="small">Inbox</strong>
                                            <small class="d-block">Received messages</small>
                                        </div>
                                    </a>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="text-center py-3 bg-light rounded">
                                        <i class="bx bx-envelope display-6 text-muted mb-2"></i>
                                        <p class="text-muted mb-2 small">Login to access messages</p>
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Support & Help Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-support me-2"></i>Support & Help
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('user.support.index') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-help-circle mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Help Center</strong>
                                        <small class="d-block">Get support</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.support.contact') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-phone mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Contact Us</strong>
                                        <small class="d-block">Reach support</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System & Tools Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-cog me-2"></i>System & Tools
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('user.requirements') }}" class="btn btn-outline-secondary w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-list-check mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Requirements</strong>
                                        <small class="d-block">System requirements</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.sponsor-list') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-crown mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Sponsors</strong>
                                        <small class="d-block">Sponsor network</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Account Actions Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-user-circle me-2"></i>Account Actions
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <button onclick="toggleMobileTheme()" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-moon mb-1 theme-icon"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Theme</strong>
                                        <small class="d-block">Toggle dark mode</small>
                                    </div>
                                </button>
                            </div>
                            <div class="col-6">
                                <button onclick="confirmLogout()" class="btn btn-outline-danger w-100 p-3 mobile-feature-btn">
                                    <i class="bx bx-log-out mb-1"></i>
                                    <div class="mobile-btn-content">
                                        <strong class="small">Logout</strong>
                                        <small class="d-block">Sign out securely</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lottery & Games Modal - COMPLETELY RECREATED FOR HORIZONTAL LAYOUT -->
    <div class="modal fade" id="mobileLotteryModal" tabindex="-1" aria-labelledby="mobileLotteryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content mobile-modal-content">
                <div class="modal-header text-white py-3 mobile-modal-header" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 50%, #fd7e14 100%);">
                    <h5 class="modal-title" id="mobileLotteryModalLabel">
                        <i class="bx bx-gift me-2"></i>Lottery & Games
                    </h5>
                    <button type="button" class="btn-close btn-close-white mobile-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 mobile-modal-body">
                    @auth
                    <!-- Lottery Stats Display -->
                    <div class="stats-section mb-4 p-3 rounded-3" style="background: linear-gradient(135deg, rgba(111, 66, 193, 0.1), rgba(232, 62, 140, 0.1));">
                        <div class="row text-center g-0">
                            @php
                                try {
                                    $activeLotteries = class_exists('\App\Models\Lottery') 
                                        ? \App\Models\Lottery::where('status', 'active')->count() 
                                        : 3;
                                    $userTickets = class_exists('\App\Models\LotteryTicket') 
                                        ? \App\Models\LotteryTicket::where('user_id', auth()->id())->count() 
                                        : rand(1, 8);
                                } catch (Exception $e) {
                                    $activeLotteries = 3;
                                    $userTickets = rand(1, 8);
                                }
                            @endphp
                            <div class="col-6">
                                <h4 class="mb-1" style="color: #6f42c1;">{{$activeLotteries}}</h4>
                                <small class="text-muted">Active Lotteries</small>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1" style="color: #fd7e14;">{{$userTickets}}</h4>
                                <small class="text-muted">My Tickets</small>
                            </div>
                        </div>
                    </div>

                    <!-- Active Lotteries Section -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-trophy me-2"></i>Active Lotteries
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('lottery.draw.details') }}" class="btn w-100 p-3 text-white mobile-feature-btn d-flex flex-column align-items-center" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                    <i class="bx bx-trophy mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small text-white">Active Draws</strong>
                                        <small class="d-block text-white-75">View current prizes</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('lottery.index') }}" class="btn btn-outline-primary w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center" style="border-color: #6f42c1; color: #6f42c1;">
                                    <i class="bx bx-ticket mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Buy Tickets</strong> 
                                        <small class="d-block">Purchase</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('lottery.unified.activity.all') }}" class="btn btn-outline-success w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center" style="border-color: #fd7e14; color: #fd7e14;">
                                    <i class="bx bx-receipt mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">My Activity</strong>
                                        <small class="d-block">History</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tools & Support Section -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-3 small section-header">
                            <i class="bx bx-cog me-2"></i>Tools & Support
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <a href="{{ route('lottery.share') }}" class="btn btn-outline-info w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-share mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">Share & Earn</strong>
                                        <small class="d-block">Invite friends</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('lottery.unified.index') }}" class="btn btn-outline-warning w-100 p-3 mobile-feature-btn d-flex flex-column align-items-center">
                                    <i class="bx bx-info-circle mb-1"></i>
                                    <div class="mobile-btn-content text-center">
                                        <strong class="small">How to Play</strong>
                                        <small class="d-block">Rules & tips</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Guest User Content -->
                    <div class="text-center mb-4">
                        <i class="bx bx-gift display-1 mb-3" style="color: #6f42c1;"></i>
                        <h5>Join the Lottery!</h5>
                        <p class="text-muted">Login to participate in exciting lottery draws</p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                            <i class="bx bx-log-in me-2"></i>Login to Play
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary" style="border-color: #6f42c1; color: #6f42c1;">
                            <i class="bx bx-user-plus me-2"></i>Create Account & Win
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Styles -->
    <style>
    /* DESKTOP HIDE - Force hide mobile layout on desktop/PC */
    @media (min-width: 992px) {
        .mobile-optimized,
        .mobile-header-bar,
        .mobile-navbar,
        .mobile-footer,
        .page.mobile-optimized {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        body {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        
        .main-content.mobile-content {
            display: none !important;
        }
    }
    
    /* Mobile Optimized Body - ONLY SHOW ON MOBILE */
    .mobile-optimized {
        padding-top: 70px !important;
        padding-bottom: 65px !important;
        min-height: 100vh;
        background: #f8f9fa;
    }
    
    /* Show mobile layout only on mobile devices */
    @media (max-width: 991.98px) {
        .mobile-optimized,
        .mobile-header-bar,
        .mobile-navbar,
        .mobile-footer {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
    }

    /* Mobile Header Bar (Top) */
    .mobile-header-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 9998;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        height: 70px;
    }

    .mobile-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        min-height: 70px;
    }

    /* Profile Picture Button */
    .mobile-profile-btn {
        position: relative;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .mobile-profile-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        transition: all 0.3s ease;
    }

    .mobile-profile-btn:hover .mobile-profile-avatar {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .online-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: #28a745;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* Mobile Brand */
    .mobile-brand {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .mobile-brand-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #007bff;
        font-weight: 700;
    }

    .brand-icon {
        font-size: 24px;
        margin-right: 8px;
    }

    .brand-text {
        font-size: 18px;
        font-weight: 700;
    }

    /* Mobile Actions */
    .mobile-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .mobile-action-btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .mobile-action-btn i {
        font-size: 20px;
    }

    .mobile-action-btn:hover {
        background: rgba(0, 123, 255, 0.15);
        color: #007bff;
        transform: scale(1.05);
    }

    .notification-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #dc3545;
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        border: 2px solid white;
    }

    /* Mobile Main Content */
    .mobile-content {
        flex: 1;
        padding: 20px 0;
        min-height: calc(100vh - 150px);
    }

    /* Mobile Navbar (Bottom) */
    .mobile-navbar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-top: 1px solid #e9ecef;
        box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        height: 65px;
    }

    /* Navigation Items Container */
    .mobile-nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 6px 4px 8px 4px;
        min-height: 60px;
    }

    /* Individual Navigation Links */
    .mobile-nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4px 3px;
        text-decoration: none;
        color: #6c757d;
        border-radius: 12px;
        min-width: 45px;
        flex: 1;
        max-width: 55px;
        transition: all 0.3s ease;
        position: relative;
    }

    /* Icon Wrapper */
    .nav-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin-bottom: 3px;
        transition: all 0.3s ease;
        position: relative;
        background: rgba(108, 117, 125, 0.1);
    }

    /* Navigation Icons */
    .nav-icon {
        font-size: 16px;
        line-height: 1;
        transition: all 0.3s ease;
    }

    /* Navigation Text */
    .nav-text {
        font-size: 10px;
        font-weight: 600;
        line-height: 1.2;
        margin-top: 2px;
        transition: all 0.3s ease;
        text-align: center;
    }

    /* Balance Indicator */
    .balance-indicator {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        font-size: 8px;
        font-weight: 700;
        padding: 2px 4px;
        border-radius: 8px;
        min-width: 20px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
        border: 2px solid white;
        z-index: 10;
        animation: balancePulse 3s infinite;
    }

    @keyframes balancePulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.6);
        }
    }

    /* Notification Dot */
    .notification-dot {
        position: absolute;
        top: -3px;
        right: -3px;
        background: #dc3545;
        color: white;
        font-size: 8px;
        font-weight: 700;
        padding: 1px 4px;
        border-radius: 8px;
        min-width: 14px;
        height: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        border: 2px solid white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    /* Real-time notification animations for mobile */
    .pulse-animation {
        animation: mobileBellPulse 2s infinite;
    }

    @keyframes mobileBellPulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
            color: #007bff;
        }
        100% {
            transform: scale(1);
        }
    }

    /* Hover and Active States */
    .mobile-nav-link:hover,
    .mobile-nav-link:focus {
        color: #007bff;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .mobile-nav-link:hover .nav-icon-wrapper {
        background: rgba(0, 123, 255, 0.15);
        transform: scale(1.05);
    }

    /* Active State */
    .mobile-nav-link.nav-active {
        color: #007bff;
    }

    .mobile-nav-link.nav-active .nav-icon-wrapper {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .mobile-nav-link.nav-active .nav-icon {
        color: white;
    }

    /* Mobile Footer */
    .mobile-footer {
        position: fixed;
        bottom: 65px;
        left: 0;
        right: 0;
        background: rgba(248, 249, 250, 0.9);
        padding: 8px 0;
        backdrop-filter: blur(5px);
        border-top: 1px solid rgba(233, 236, 239, 0.5);
    }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .mobile-header-content {
            padding: 10px 15px;
            min-height: 65px;
        }

        .mobile-profile-avatar {
            width: 38px;
            height: 38px;
        }

        .mobile-action-btn {
            width: 38px;
            height: 38px;
        }

        .mobile-action-btn i {
            font-size: 18px;
        }

        .brand-text {
            font-size: 16px;
        }

        .mobile-nav-items {
            padding: 4px 2px 6px 2px;
        }
        
        .mobile-nav-link {
            min-width: 40px;
            max-width: 50px;
            padding: 3px 2px;
        }
        
        .nav-icon-wrapper {
            width: 28px;
            height: 28px;
        }
        
        .nav-icon {
            font-size: 14px;
        }
        
        .nav-text {
            font-size: 9px;
        }

        .balance-indicator {
            font-size: 7px;
            padding: 1px 3px;
            min-width: 18px;
            height: 14px;
            border-radius: 7px;
        }

        .notification-dot {
            font-size: 7px;
            padding: 1px 3px;
            min-width: 12px;
            height: 12px;
        }

        .mobile-optimized {
            padding-top: 65px !important;
        }
    }

    /* Smooth entrance animation */
    .mobile-navbar {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Modal backdrop fix */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5) !important;
    }

    .modal-backdrop.show {
        opacity: 0.5 !important;
    }

    /* Prevent multiple backdrop stacking */
    body.modal-open {
        overflow: hidden !important;
    }

    /* Fix modal z-index issues */
    .modal {
        z-index: 1055 !important;
    }

    .modal-backdrop {
        z-index: 1054 !important;
    }

    /* Button feedback animation */
    .mobile-nav-link.nav-active .nav-icon-wrapper {
        animation: buttonPress 0.2s ease-out;
    }

    @keyframes buttonPress {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1.05); }
    }

    /* Lottery button special styling */
    .lottery-link .nav-icon-wrapper {
        background: linear-gradient(135deg, #6f42c1, #e83e8c);
        color: white;
        box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
    }

    .lottery-link .nav-icon {
        color: white;
        animation: lotteryGlow 2s ease-in-out infinite alternate;
    }

    .lottery-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, #5a2d91, #d63384);
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.5);
    }

    @keyframes lotteryGlow {
        0% { 
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }
        100% { 
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8), 0 0 15px rgba(111, 66, 193, 0.6);
        }
    }

    /* Lottery notification dot special styling */
    .lottery-link .notification-dot {
        background: linear-gradient(135deg, #28a745, #20c997);
        animation: lotteryPulse 1.5s infinite;
    }

    @keyframes lotteryPulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }
        50% {
            transform: scale(1.2);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.6);
        }
    }

    /* Enhanced Mobile Modal Responsiveness - 2x2 Grid System */
    .mobile-modal-content {
        border-radius: 16px !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
    }

    .mobile-modal-header {
        border-radius: 16px 16px 0 0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .mobile-close-btn {
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .mobile-modal-body {
        border-radius: 0 0 16px 16px !important;
    }

    /* 2x2 Grid System for Feature Buttons */
    .mobile-features-grid {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }

    .mobile-features-grid .col-6 {
        flex: 0 0 calc(50% - 4px) !important;
        max-width: calc(50% - 4px) !important;
    }

    .mobile-features-grid .col-12 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }

    /* ENSURE HORIZONTAL LAYOUT FOR ALL MODAL BUTTONS */
    .modal-body .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 6px) !important;
        max-width: calc(50% - 6px) !important;
        min-width: calc(50% - 6px) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Force horizontal display */
    .modal-body .row > div {
        display: inline-block !important;
        vertical-align: top !important;
    }

    .mobile-feature-btn {
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        overflow: hidden !important;
        min-height: 70px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 12px 8px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* ENSURE ALL MODAL BUTTONS DISPLAY HORIZONTALLY */
    .modal-body .btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        min-height: 70px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        word-wrap: break-word !important;
    }

    .modal-body .btn > div {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
    }

    .mobile-feature-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .mobile-btn-content {
        z-index: 2 !important;
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
    }

    .mobile-btn-content strong {
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        margin-bottom: 2px !important;
    }

    .mobile-btn-content small {
        font-size: 10px !important;
        line-height: 1.2 !important;
        opacity: 0.8 !important;
    }

    .mobile-badge {
        font-size: 0.65rem !important;
        padding: 0.25em 0.5em !important;
    }

    .section-header {
        font-weight: 600 !important;
        color: #495057 !important;
        border-bottom: 1px solid #e9ecef !important;
        padding-bottom: 0.5rem !important;
        margin-bottom: 1rem !important;
    }

    /* Mobile Modal Height Adjustments */
    @media (max-width: 576px) {
        /* FORCE HORIZONTAL BUTTON LAYOUT ON MOBILE */
        .modal-body .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: stretch !important;
        }
        
        .modal-body .row .col-6 {
            flex: 0 0 48% !important;
            max-width: 48% !important;
            margin-right: 2% !important;
            margin-bottom: 12px !important;
        }
        
        .modal-body .row .col-6:nth-child(even) {
            margin-right: 0 !important;
        }
        
        .modal-body .row .col-12 {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            margin-bottom: 12px !important;
        }

        /* NEWLY CREATED MORE MODAL - SPECIFIC HORIZONTAL LAYOUT */
        #mobileMoreModal .modal-body .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            gap: 12px !important;
        }

        #mobileMoreModal .modal-body .row .col-6 {
            flex: 1 1 calc(50% - 6px) !important;
            max-width: calc(50% - 6px) !important;
            min-width: calc(50% - 6px) !important;
            margin: 0 !important;
        }

        #mobileMoreModal .mobile-feature-btn {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 80px !important;
            text-align: center !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
        }

        #mobileMoreModal .mobile-feature-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        /* NEWLY CREATED WALLET MODAL - SPECIFIC HORIZONTAL LAYOUT */
        #mobileWalletModal .modal-body .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            gap: 12px !important;
        }

        #mobileWalletModal .modal-body .row .col-6 {
            flex: 1 1 calc(50% - 6px) !important;
            max-width: calc(50% - 6px) !important;
            min-width: calc(50% - 6px) !important;
            margin: 0 !important;
        }

        #mobileWalletModal .mobile-feature-btn {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 80px !important;
            text-align: center !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
        }

        #mobileWalletModal .mobile-feature-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        /* NEWLY CREATED PROFILE MODAL - SPECIFIC HORIZONTAL LAYOUT */
        #mobileProfileModal .modal-body .row {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            gap: 12px !important;
        }

        #mobileProfileModal .modal-body .row .col-6 {
            flex: 1 1 calc(50% - 6px) !important;
            max-width: calc(50% - 6px) !important;
            min-width: calc(50% - 6px) !important;
            margin: 0 !important;
        }

        #mobileProfileModal .mobile-feature-btn {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 80px !important;
            text-align: center !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
        }

        #mobileProfileModal .mobile-feature-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        /* More Modal Specific Adjustments */
        #mobileMoreModal .modal-dialog {
            max-height: 90vh !important;
            margin: 0.5rem !important;
            width: calc(100vw - 1rem) !important;
            max-width: none !important;
        }

        /* Wallet Modal Specific Adjustments */
        #mobileWalletModal .modal-dialog {
            max-height: 90vh !important;
            margin: 0.5rem !important;
            width: calc(100vw - 1rem) !important;
            max-width: none !important;
        }

        /* Profile Modal Specific Adjustments */
        #mobileProfileModal .modal-dialog {
            max-height: 90vh !important;
            margin: 0.5rem !important;
            width: calc(100vw - 1rem) !important;
            max-width: none !important;
        }
        
        #mobileMoreModal .modal-content {
            max-height: 90vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        #mobileMoreModal .modal-body {
            max-height: calc(90vh - 70px) !important;
            overflow-y: auto !important;
            padding: 12px !important;
            flex: 1 !important;
        }
        
        #mobileMoreModal .modal-header {
            flex-shrink: 0 !important;
            padding: 8px 12px !important;
            min-height: 50px !important;
        }
        
        #mobileMoreModal .modal-title {
            font-size: 14px !important;
            font-weight: 600 !important;
        }
        
        /* Compact feature buttons for small screens */
        .mobile-feature-btn {
            padding: 10px 6px !important;
            min-height: 65px !important;
        }
        
        .mobile-btn-content strong {
            font-size: 11px !important;
        }
        
        .mobile-btn-content small {
            font-size: 9px !important;
        }
        
        .section-header {
            font-size: 11px !important;
            margin-bottom: 8px !important;
        }
        
        .mobile-features-grid {
            gap: 6px !important;
        }
        
        .mobile-features-grid .col-6 {
            flex: 0 0 calc(50% - 3px) !important;
            max-width: calc(50% - 3px) !important;
        }
    }

    /* Extra small screens (height < 600px) */
    @media (max-height: 600px) {
        #mobileMoreModal .modal-dialog {
            max-height: 95vh !important;
            margin: 0.25rem !important;
        }
        
        #mobileMoreModal .modal-content {
            max-height: 95vh !important;
        }
        
        #mobileMoreModal .modal-body {
            max-height: calc(95vh - 60px) !important;
            padding: 8px !important;
        }
        
        #mobileMoreModal .modal-header {
            padding: 6px 10px !important;
            min-height: 45px !important;
        }
        
        .mobile-feature-btn {
            padding: 8px 4px !important;
            min-height: 55px !important;
        }
        
        .mobile-btn-content strong {
            font-size: 10px !important;
        }
        
        .mobile-btn-content small {
            font-size: 8px !important;
        }
    }

    /* Portrait orientation specific adjustments */
    @media (orientation: portrait) and (max-width: 576px) {
        #mobileMoreModal .modal-dialog {
            transform: none !important;
            margin-top: 5vh !important;
            margin-bottom: 5vh !important;
        }
    }

    /* Landscape orientation specific adjustments */
    @media (orientation: landscape) and (max-height: 500px) {
        #mobileMoreModal .modal-dialog {
            max-height: 100vh !important;
            margin: 0 !important;
        }
        
        #mobileMoreModal .modal-content {
            max-height: 100vh !important;
            border-radius: 0 !important;
        }
        
        #mobileMoreModal .modal-body {
            max-height: calc(100vh - 50px) !important;
        }
        
        .mobile-feature-btn {
            min-height: 50px !important;
            padding: 6px 4px !important;
        }
    }

    /* Reduce modal heights for small mobile screens */
        #mobileWalletModal .modal-dialog,
        #mobileProfileModal .modal-dialog,
        #mobileLotteryModal .modal-dialog {
            max-height: 85vh !important;
            margin: 1rem auto !important;
        }
        
        #mobileWalletModal .modal-content,
        #mobileProfileModal .modal-content,
        #mobileLotteryModal .modal-content {
            max-height: 85vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        #mobileWalletModal .modal-body,
        #mobileProfileModal .modal-body,
        #mobileLotteryModal .modal-body {
            max-height: calc(85vh - 120px) !important;
            overflow-y: auto !important;
            padding: 15px !important;
        }
        
        /* Ensure close button is always visible */
        #mobileWalletModal .modal-header,
        #mobileProfileModal .modal-header,
        #mobileLotteryModal .modal-header {
            flex-shrink: 0 !important;
            padding: 10px 15px !important;
            min-height: 50px !important;
        }
        
        /* Compact modal headers */
        #mobileWalletModal .modal-title,
        #mobileProfileModal .modal-title,
        #mobileLotteryModal .modal-title {
            font-size: 16px !important;
        }
        
        /* Smaller close button */
        #mobileWalletModal .btn-close,
        #mobileProfileModal .btn-close,
        #mobileLotteryModal .btn-close {
            padding: 8px !important;
            margin: 0 !important;
        }
    

    /* Extra small screens (height < 600px) */
    @media (max-height: 600px) {
        #mobileWalletModal .modal-dialog,
        #mobileProfileModal .modal-dialog,
        #mobileLotteryModal .modal-dialog {
            max-height: 90vh !important;
            margin: 0.5rem auto !important;
        }
        
        #mobileWalletModal .modal-content,
        #mobileProfileModal .modal-content,
        #mobileLotteryModal .modal-content {
            max-height: 90vh !important;
        }
        
        #mobileWalletModal .modal-body,
        #mobileProfileModal .modal-body,
        #mobileLotteryModal .modal-body {
            max-height: calc(90vh - 100px) !important;
            padding: 10px !important;
        }
        
        #mobileWalletModal .modal-header,
        #mobileProfileModal .modal-header,
        #mobileLotteryModal .modal-header {
            padding: 8px 12px !important;
            min-height: 45px !important;
        }
    }

    /* ===== CRITICAL: HORIZONTAL LAYOUT CSS FOR ALL RECREATED MODALS ===== */
    
    /* Home Modal - Force Horizontal 2x2 Layout */
    #mobileHomeModal .modal-body .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #mobileHomeModal .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 6px) !important;
        max-width: calc(50% - 6px) !important;
        min-width: calc(50% - 6px) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #mobileHomeModal .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Videos Modal - Force Horizontal 2x2 Layout */
    #mobileVideosModal .modal-body .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #mobileVideosModal .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 6px) !important;
        max-width: calc(50% - 6px) !important;
        min-width: calc(50% - 6px) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #mobileVideosModal .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Lottery Modal - Force Horizontal 2x2 Layout */
    #mobileLotteryModal .modal-body .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #mobileLotteryModal .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 6px) !important;
        max-width: calc(50% - 6px) !important;
        min-width: calc(50% - 6px) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #mobileLotteryModal .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Notifications Modal - Force Horizontal 2x2 Layout */
    #mobileNotificationsModal .modal-body .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #mobileNotificationsModal .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 6px) !important;
        max-width: calc(50% - 6px) !important;
        min-width: calc(50% - 6px) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #mobileNotificationsModal .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Ensure ALL Buttons Display as Flex Columns - Critical Fix */
    #mobileHomeModal .mobile-feature-btn,
    #mobileVideosModal .mobile-feature-btn,
    #mobileLotteryModal .mobile-feature-btn,
    #mobileNotificationsModal .mobile-feature-btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        min-height: 70px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        word-wrap: break-word !important;
        padding: 12px 8px !important;
    }

    /* Ensure Button Content is Centered */
    #mobileHomeModal .mobile-btn-content,
    #mobileVideosModal .mobile-btn-content,
    #mobileLotteryModal .mobile-btn-content,
    #mobileNotificationsModal .mobile-btn-content {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
        text-align: center !important;
    }

    /* All mobile modals - ensure they fit screen and follow 2x2 system */
    @media (max-width: 991.98px) {
        /* Standard modal dialog settings */
        .modal-dialog {
            margin: 1rem !important;
            max-height: 90vh !important;
        }
        
        .modal-content {
            border-radius: 12px !important;
            max-height: 90vh !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        .modal-body {
            flex: 1 !important;
            overflow-y: auto !important;
        }
        
        /* 2x2 Grid System for ALL Modal Buttons */
        .modal-body .row.g-3 .col-6 {
            margin-bottom: 12px !important;
        }
        
        .modal-body .btn.p-3 {
            min-height: 70px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
        
        .modal-body .btn.p-3 i {
            margin-bottom: 4px !important;
        }
        
        .modal-body .btn.p-3 div {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
        
        .modal-body .btn.p-3 strong {
            font-size: 12px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
        }
        
        .modal-body .btn.p-3 small {
            font-size: 10px !important;
            line-height: 1.2 !important;
            opacity: 0.8 !important;
        }
        
        /* Ensure modal doesn't exceed viewport */
        .modal {
            padding: 0 !important;
        }
        
        .modal-dialog-centered {
            min-height: calc(100vh - 2rem) !important;
        }
        
        /* Compact row spacing for mobile */
        .modal-body .row.g-3 {
            gap: 0.75rem !important;
        }
        
        .modal-body .row.g-2 {
            gap: 0.5rem !important;
        }
        
        /* Smaller padding for action buttons */
        .modal-body .btn.p-3 {
            padding: 0.75rem !important;
        }
        
        /* Compact stats cards */
        .modal-body .stat-card {
            padding: 0.75rem !important;
        }
    }

    /* CRITICAL: MAXIMUM SPECIFICITY CSS TO FORCE HORIZONTAL LAYOUT FOR ALL MODALS */
    /* Target ALL FOUR NEWLY RECREATED MODALS with ultimate specificity */
    #mobileHomeModal .modal-body .row,
    #mobileVideosModal .modal-body .row,
    #mobileLotteryModal .modal-body .row,
    #mobileNotificationsModal .modal-body .row {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        justify-content: space-between !important;
        align-items: stretch !important;
        gap: 8px !important;
        margin: 0 !important;
        width: 100% !important;
    }

    /* Force exact 50% width for col-6 elements in ALL FOUR MODALS */
    #mobileHomeModal .modal-body .row .col-6,
    #mobileVideosModal .modal-body .row .col-6,
    #mobileLotteryModal .modal-body .row .col-6,
    #mobileNotificationsModal .modal-body .row .col-6 {
        flex: 1 1 calc(50% - 4px) !important;
        max-width: calc(50% - 4px) !important;
        min-width: calc(50% - 4px) !important;
        width: calc(50% - 4px) !important;
        padding: 0 !important;
        margin: 0 !important;
        display: inline-block !important;
        float: none !important;
        box-sizing: border-box !important;
    }

    /* Full width for col-12 elements */
    #mobileHomeModal .modal-body .row .col-12,
    #mobileVideosModal .modal-body .row .col-12,
    #mobileLotteryModal .modal-body .row .col-12,
    #mobileNotificationsModal .modal-body .row .col-12 {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Ensure ALL buttons in these modals display properly */
    #mobileHomeModal .modal-body .btn,
    #mobileVideosModal .modal-body .btn,
    #mobileLotteryModal .modal-body .btn,
    #mobileNotificationsModal .modal-body .btn {
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        min-height: 70px !important;
        border-radius: 8px !important;
        padding: 12px 8px !important;
        box-sizing: border-box !important;
        position: relative !important;
    }

    /* Button content alignment */
    #mobileHomeModal .modal-body .btn .mobile-btn-content,
    #mobileVideosModal .modal-body .btn .mobile-btn-content,
    #mobileLotteryModal .modal-body .btn .mobile-btn-content,
    #mobileNotificationsModal .modal-body .btn .mobile-btn-content {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        width: 100% !important;
    }

    /* Stats sections horizontal layout */
    #mobileHomeModal .stats-section .row,
    #mobileVideosModal .stats-section .row,
    #mobileLotteryModal .stats-section .row,
    #mobileNotificationsModal .stats-section .row {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-around !important;
        align-items: center !important;
        text-align: center !important;
    }

    #mobileHomeModal .stats-section .col-6,
    #mobileVideosModal .stats-section .col-6,
    #mobileLotteryModal .stats-section .col-6,
    #mobileNotificationsModal .stats-section .col-6 {
        flex: 1 1 50% !important;
        max-width: 50% !important;
        text-align: center !important;
        padding: 0 8px !important;
    }

    /* Override any Bootstrap default behavior */
    .modal-body .row > * {
        flex-shrink: 0 !important;
    }

    /* Prevent vertical stacking */
    .modal-body .col-6 + .col-6 {
        margin-left: 0 !important;
    }

    /* Force side-by-side display */
    @media (max-width: 991.98px) {
        #mobileHomeModal .modal-body .row .col-6,
        #mobileVideosModal .modal-body .row .col-6,
        #mobileLotteryModal .modal-body .row .col-6,
        #mobileNotificationsModal .modal-body .row .col-6 {
            flex: 1 1 48% !important;
            max-width: 48% !important;
            min-width: 48% !important;
            width: 48% !important;
            display: inline-block !important;
            vertical-align: top !important;
            margin-right: 2% !important;
        }

        #mobileHomeModal .modal-body .row .col-6:nth-child(even),
        #mobileVideosModal .modal-body .row .col-6:nth-child(even),
        #mobileLotteryModal .modal-body .row .col-6:nth-child(even),
        #mobileNotificationsModal .modal-body .row .col-6:nth-child(even) {
            margin-right: 0 !important;
        }
    }

    /* NOTIFICATIONS MODAL SPECIFIC STYLES */
    #mobileNotificationsModal .modal-content {
        max-height: 95vh !important;
        display: flex !important;
        flex-direction: column !important;
    }

    #mobileNotificationsModal .modal-body {
        flex: 1 !important;
        overflow-y: auto !important;
        max-height: 75vh !important;
        padding: 1rem !important;
    }

    /* Notification preview styles */
    .notification-preview-list {
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #6c757d transparent;
    }

    .notification-preview-list::-webkit-scrollbar {
        width: 4px;
    }

    .notification-preview-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .notification-preview-list::-webkit-scrollbar-thumb {
        background-color: #6c757d;
        border-radius: 4px;
    }

    .notification-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 193, 7, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Mobile specific notification modal adjustments */
    @media (max-width: 576px) {
        #mobileNotificationsModal .modal-dialog {
            margin: 0.5rem !important;
            max-width: calc(100% - 1rem) !important;
        }
        
        #mobileNotificationsModal .modal-content {
            max-height: 90vh !important;
        }
        
        #mobileNotificationsModal .modal-body {
            max-height: 70vh !important;
            padding: 0.75rem !important;
        }
        
        .notification-preview-list {
            max-height: 200px;
        }
        
        .notification-item {
            padding: 0.75rem !important;
            margin-bottom: 0.5rem !important;
        }
        
        .notification-icon {
            width: 32px;
            height: 32px;
            margin-right: 0.75rem !important;
        }
    }
    </style>

    <!-- Popper JS -->
    <script src="{{asset('assets/libs/@popperjs/core/umd/popper.min.js')}}"></script>

    <!-- Bootstrap JS -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Node Waves JS-->
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets/js/custom.js')}}"></script>

    <!-- Mobile Functions - Clean & Organized -->
    <script src="{{asset('assets_custom/js/mobile-functions.js')}}"></script> 

    @stack('script')
    <script>
        function confirmLogout() {
        try {
            // Check if SweetAlert is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Logout',
                    text: 'Are you sure you want to logout?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Logout',
                    cancelButtonText: 'Cancel',
                    backdrop: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        performLogout();
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm('Are you sure you want to logout?')) {
                    performLogout();
                }
            }
        } catch (error) {
            console.error('Error in confirmLogout:', error);
            // Final fallback
            if (confirm('Are you sure you want to logout?')) {
                performLogout();
            }
        }
    };
    function performLogout() {
        console.log('performLogout called - attempting simple logout first');
        
        // Primary: Try simple logout (no CSRF, no middleware)
        window.location.href = "{{ route('simple.logout') }}";
    }

    // Debug mobile reload issues
    let reloadCounter = 0;
    let pageLoadTime = Date.now();
    let navigationClicks = 0;
    
    // Detect page unload/reload
    window.addEventListener('beforeunload', function(e) {
        console.log('Mobile page unloading - reason: beforeunload event');
        console.log('Page was loaded for:', (Date.now() - pageLoadTime) / 1000, 'seconds');
        console.log('Navigation clicks during session:', navigationClicks);
        localStorage.setItem('mobileReloadDebug', JSON.stringify({
            time: new Date().toISOString(),
            duration: (Date.now() - pageLoadTime) / 1000,
            clicks: navigationClicks,
            userAgent: navigator.userAgent,
            currentUrl: window.location.href
        }));
    });
    
    // Track navigation clicks to identify if they cause reloads
    document.addEventListener('click', function(e) {
        if (e.target.closest('.mobile-nav-link')) {
            navigationClicks++;
            console.log('Mobile navigation clicked:', e.target.closest('.mobile-nav-link').textContent.trim());
        }
    });
    
    // Prevent pull-to-refresh on mobile
    document.body.style.overscrollBehavior = 'none';
    document.documentElement.style.overscrollBehavior = 'none';
    
    // Prevent double-tap zoom that might interfere
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
        var now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
    
    // Test mobile modal functionality on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Mobile layout loaded');
        
        // Check if this is a reload
        const debugInfo = localStorage.getItem('mobileReloadDebug');
        if (debugInfo) {
            console.log('Previous reload info:', JSON.parse(debugInfo));
        }
        
        // Test if openMobileModal is available
        if (typeof window.openMobileModal === 'function') {
            console.log('✅ openMobileModal function is available');
        } else {
            console.error('❌ openMobileModal function is NOT available - this will cause errors!');
            // Try to load the function after a delay
            setTimeout(() => {
                if (typeof window.openMobileModal === 'function') {
                    console.log('✅ openMobileModal loaded after delay');
                } else {
                    console.error('❌ openMobileModal still not available after delay');
                }
            }, 1000);
        }
        
        // Test Bootstrap Modal availability
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap available:', typeof bootstrap.Modal !== 'undefined' ? 'with Modal' : 'without Modal');
        } else {
            console.error('❌ Bootstrap is NOT available');
        }
        
        // Test if all modal elements exist
        const modalTypes = ['videos', 'wallet', 'grid', 'notifications', 'more', 'lottery', 'profile'];
        modalTypes.forEach(type => {
            const modalId = `mobile${type.charAt(0).toUpperCase() + type.slice(1)}Modal`;
            const modalElement = document.querySelector(`#${modalId}`);
            console.log(`Modal ${modalId}:`, modalElement ? '✅ EXISTS' : '❌ MISSING');
        });
        
        // Test navigation links
        const navLinks = document.querySelectorAll('.mobile-nav-link');
        console.log(`Navigation links found: ${navLinks.length}`);
        navLinks.forEach((link, index) => {
            const onclick = link.getAttribute('onclick');
            const text = link.querySelector('.nav-text')?.textContent || 'unknown';
            console.log(`Nav ${index + 1}: ${text} - onclick: ${onclick || 'none'}`);
        });
        
        // Set current year for footer
        const yearMobile = document.getElementById('year-mobile');
        if (yearMobile) {
            yearMobile.textContent = new Date().getFullYear();
        }

        // Real-time notification badge update system for mobile
        @auth
        function updateMobileNotificationBadge() {
            try {
                // Check if CSRF token exists
                const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenElement) {
                    console.log('CSRF token not found, skipping notification update');
                    return;
                }

                fetch('{{ route("user.notifications.count") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfTokenElement.getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const mobileBadge = document.querySelector('.notifications-link .notification-dot');
                    const bellIcon = document.querySelector('.notifications-link .bx-bell');
                    
                    if (data.count > 0) {
                        if (mobileBadge) {
                            mobileBadge.textContent = data.count;
                            mobileBadge.style.display = 'flex';
                        } else {
                            // Create badge if it doesn't exist
                            const wrapper = document.querySelector('.notifications-link .nav-icon-wrapper');
                            if (wrapper) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'notification-dot';
                                newBadge.textContent = data.count;
                                wrapper.appendChild(newBadge);
                            }
                        }
                        
                        // Add pulse animation to bell icon
                        if (bellIcon) {
                            bellIcon.classList.add('pulse-animation');
                        }
                    } else {
                        if (mobileBadge) {
                            mobileBadge.style.display = 'none';
                        }
                        // Remove pulse animation from bell icon
                        if (bellIcon) {
                            bellIcon.classList.remove('pulse-animation');
                        }
                    }
                })
                .catch(error => {
                    console.log('Mobile notification update error:', error);
                    // Don't throw the error, just log it to prevent page issues
                });
            } catch (error) {
                console.log('Mobile notification update initialization error:', error);
            }
        }

        // Update mobile notification badge every 30 seconds
        setInterval(updateMobileNotificationBadge, 30000);

        // Also update when page becomes visible again (user returns to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateMobileNotificationBadge();
            }
        });

        // Initial update after page load
        setTimeout(updateMobileNotificationBadge, 2000); // Wait 2 seconds after page load
        @endauth
    });
    </script>
    <!-- Loader Element for custom.js compatibility -->
    <div id="loader" class="d-none"></div>
    <div class="loader-overlay" style="display: none;"></div>
    <div class="loading-spinner" style="display: none;"></div>
</body>
</html>
