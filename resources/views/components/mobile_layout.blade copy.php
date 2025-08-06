<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    <!-- Cache Management Meta Tags --> 
    <meta name="cache-version" content="{{ config('app.cache_version', time()) }}">
    <meta name="app-version" content="{{ config('app.version', '1.0.0') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/images/brand-logos/favicon.ico')}}" type="image/x-icon">

    <!-- Choices JS -->
    <script src="{{asset('assets/libs/choices.js/public/assets/scripts/choices.min.js')}}"></script>

    <!-- Main Theme Js -->
    <script src="{{asset('assets/js/main.js')}}"></script> 

    <!-- Server Time Utility -->
    <script src="{{asset('server-time.js')}}"></script>

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
                <!-- Home -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link home-link"
                   onclick="console.log('Home button clicked!'); openMobileModal('home');">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-home nav-icon"></i>
                    </span>
                    <span class="nav-text">Home</span>
                </a>

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

                <!-- Profile -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link profile-link"
                   onclick="openMobileModal('profile')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-user nav-icon"></i>
                    </span>
                    <span class="nav-text">Profile</span>
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

                <!-- Notifications -->
                <a href="javascript:void(0);" 
                   class="mobile-nav-link notifications-link"
                   onclick="openMobileModal('notifications')">
                    <span class="nav-icon-wrapper">
                        <i class="bx bx-bell nav-icon"></i>
                        @auth
                            @php
                                try {
                                    $unreadNotifications = auth()->user()->unreadNotifications()->count();
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

    <!-- Home Dashboard Modal -->
    <div class="modal fade" id="mobileHomeModal" tabindex="-1" aria-labelledby="mobileHomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="mobileHomeModalLabel">
                        <i class="bx bx-home me-2"></i>Dashboard & Overview
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @auth
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="text-primary mb-1">${{number_format((auth()->user()->deposit_wallet + auth()->user()->interest_wallet) ?? 0, 2)}}</h4>
                                <small class="text-muted">Balance</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="text-success mb-1">{{auth()->user()->video_views_count ?? 0}}</h4>
                                <small class="text-muted">Video Views</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Dashboard Actions -->
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-primary w-100 p-3">
                                <i class="bx bx-tachometer me-2"></i>
                                <div>
                                    <strong>Main Dashboard</strong>
                                    <small class="d-block text-white-50">Complete overview & analytics</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.generation-history') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bx bx-history me-2"></i>
                                <div>
                                    <strong>Generation History</strong>
                                    <small class="d-block">View activity</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.team-tree') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bx bx-sitemap me-2"></i>
                                <div>
                                    <strong>Team Tree</strong>
                                    <small class="d-block">Network view</small>
                                </div>
                            </a>
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

    <!-- Videos & Earnings Modal -->
    <div class="modal fade" id="mobileVideosModal" tabindex="-1" aria-labelledby="mobileVideosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="mobileVideosModalLabel">
                        <i class="bx bx-video me-2"></i>Videos & Earnings
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('user.video-views.index') }}" class="btn btn-info w-100 p-3">
                                <i class="bx bx-play-circle me-2"></i>
                                <div>
                                    <strong>Watch Videos</strong>
                                    <small class="d-block text-white-50">Start earning by watching videos</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.video-views.gallery') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bx bx-collection me-2"></i>
                                <div>
                                    <strong>Video Gallery</strong>
                                    <small class="d-block">Browse all videos</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.video-views.earnings') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bx bx-dollar me-2"></i>
                                <div>
                                    <strong>Earnings</strong>
                                    <small class="d-block">View your income</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('user.video-views.history') }}" class="btn btn-outline-secondary w-100 p-3">
                                <i class="bx bx-history me-2"></i>
                                <div>
                                    <strong>Watch History</strong>
                                    <small class="d-block">Track your video viewing activity</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet & Finance Modal -->
    <div class="modal fade" id="mobileWalletModal" tabindex="-1" aria-labelledby="mobileWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="mobileWalletModalLabel">
                        <i class="bx bx-wallet me-2"></i>Wallet & Finance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
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

                    <!-- Finance Actions -->
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('deposit.index') }}" class="btn btn-success w-100 p-3">
                                <i class="bx bx-plus-circle me-2"></i>
                                <div>
                                    <strong>Deposit</strong>
                                    <small class="d-block text-white-50">Add funds</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.withdraw.wallet') }}" class="btn btn-warning w-100 p-3">
                                <i class="bx bx-minus-circle me-2"></i>
                                <div>
                                    <strong>Withdraw</strong>
                                    <small class="d-block text-white-50">Cash out</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('user.transfer_funds') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bx bx-transfer me-2"></i>
                                <div>
                                    <strong>Transactions</strong>
                                    <small class="d-block">View transaction history & transfers</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('deposit.history') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bx bx-history me-2"></i>
                                <div>
                                    <strong>Deposit History</strong>
                                    <small class="d-block">Past deposits</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.withdraw.wallet.history') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="bx bx-receipt me-2"></i>
                                <div>
                                    <strong>Withdraw History</strong>
                                    <small class="d-block">Past withdrawals</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Profile & Settings Modal -->
    <div class="modal fade" id="mobileProfileModal" tabindex="-1" aria-labelledby="mobileProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="mobileProfileModalLabel">
                        <i class="bx bx-user me-2"></i>Profile & Settings
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @auth
                    <!-- Profile Header -->
                    <div class="profile-header text-center mb-4 p-4 bg-secondary bg-opacity-10 rounded-3">
                        @php
                            $profileImage = auth()->user()->image 
                                ? asset('assets/images/users/'.auth()->user()->image) 
                                : asset('assets/images/users/9.jpg');
                        @endphp
                        <img src="{{$profileImage}}" 
                             alt="{{auth()->user()->username ?? 'Profile'}}" 
                             class="rounded-circle mb-3"
                             style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #6c757d;"
                             onerror="this.src='{{asset('assets/images/users/9.jpg')}}'">
                        <h5 class="mb-1">{{auth()->user()->username ?? 'User'}}</h5>
                        <p class="text-muted mb-2">{{auth()->user()->email ?? 'No Email'}}</p>
                        <span class="badge bg-success">Active Member</span>
                    </div>

                    <!-- Profile Actions -->
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary w-100 p-3">
                                <i class="bx bx-edit me-2"></i>
                                <div>
                                    <strong>Edit Profile</strong>
                                    <small class="d-block text-white-50">Update personal information</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('profile.password') }}" class="btn btn-outline-danger w-100 p-3">
                                <i class="bx bx-lock me-2"></i>
                                <div>
                                    <strong>Password</strong>
                                    <small class="d-block">Change password</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('profile.security') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="bx bx-shield me-2"></i>
                                <div>
                                    <strong>Security</strong>
                                    <small class="d-block">2FA & security</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('user.kyc.index') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bx bx-id-card me-2"></i>
                                <div>
                                    <strong>KYC Verification</strong>
                                    <small class="d-block">Identity verification & document upload</small>
                                </div>
                            </a>
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

    <!-- Messages & Communication Modal -->
    <div class="modal fade" id="mobileMessagesModal" tabindex="-1" aria-labelledby="mobileMessagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="mobileMessagesModalLabel">
                        <i class="bx bx-envelope me-2"></i>Messages & Communication
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @auth
                    <!-- Message Stats -->
                    <div class="row mb-4">
                        @php
                            $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                            $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', 0)->count();
                        @endphp
                        <div class="col-6">
                            <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="text-primary mb-1">{{$totalMessagesCount}}</h4>
                                <small class="text-muted">Total Messages</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="text-danger mb-1">{{$unreadMessagesCount}}</h4>
                                <small class="text-muted">Unread</small>
                            </div>
                        </div>
                    </div>

                    <!-- Message Actions -->
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('user.messages') }}" class="btn btn-primary w-100 p-3">
                                <i class="bx bx-message-dots me-2"></i>
                                <div>
                                    <strong>All Messages</strong>
                                    <small class="d-block text-white-50">View all conversations</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.messages.inbox') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bx bx-inbox me-2"></i>
                                <div>
                                    <strong>Inbox</strong>
                                    <small class="d-block">Received messages</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.messages.sent') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bx bx-send me-2"></i>
                                <div>
                                    <strong>Sent</strong>
                                    <small class="d-block">Sent messages</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('referral.index') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bx bx-users me-2"></i>
                                <div>
                                    <strong>Referral Network</strong>
                                    <small class="d-block">Manage referrals & team communication</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications & Alerts Modal -->
    <div class="modal fade" id="mobileNotificationsModal" tabindex="-1" aria-labelledby="mobileNotificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="mobileNotificationsModalLabel">
                        <i class="bx bx-bell me-2"></i>Notifications & Alerts
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @auth
                    <!-- Notification Stats -->
                    <div class="row mb-4">
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
                            <div class="stat-card text-center p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="text-danger mb-1">{{$unreadNotifications}}</h4>
                                <small class="text-muted">Unread Alerts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="text-primary mb-1">{{$totalNotifications}}</h4>
                                <small class="text-muted">Total Notifications</small>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Actions -->
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('user.notifications.index') }}" class="btn btn-primary w-100 p-3">
                                <i class="bx bx-bell-ring me-2"></i>
                                <div>
                                    <strong>All Notifications</strong>
                                    <small class="d-block text-white-50">View all alerts & updates</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.notifications.settings') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bx bx-cog me-2"></i>
                                <div>
                                    <strong>Settings</strong>
                                    <small class="d-block">Notification preferences</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.session-notifications') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bx bx-time me-2"></i>
                                <div>
                                    <strong>Session Alerts</strong>
                                    <small class="d-block">Current session</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <button onclick="markAllNotificationsRead()" class="btn btn-outline-success w-100 p-3">
                                <i class="bx bx-check-double me-2"></i>
                                <div>
                                    <strong>Mark All as Read</strong>
                                    <small class="d-block">Clear all unread notifications</small>
                                </div>
                            </button>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- More & Additional Features Modal -->
    <div class="modal fade" id="mobileMoreModal" tabindex="-1" aria-labelledby="mobileMoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title" id="mobileMoreModalLabel">
                        <i class="bx bx-grid-alt me-2"></i>More Features
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <!-- Messages & Communication Section -->
                        <div class="col-12">
                            <h6 class="text-muted mb-2 small">
                                <i class="bx bx-envelope me-2"></i>Messages & Communication
                            </h6>
                        </div>
                        @auth
                            @php
                                $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                                $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', 0)->count();
                            @endphp
                            <div class="col-6">
                                <a href="{{ route('user.messages') }}" class="btn btn-primary w-100 py-2">
                                    <i class="bx bx-message-dots me-1"></i>
                                    <div>
                                        <strong class="small">All Messages</strong>
                                        <small class="d-block text-white-50">{{$totalMessagesCount}} total</small>
                                    </div>
                                    @if($unreadMessagesCount > 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 translate-middle">{{$unreadMessagesCount}}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('user.messages.inbox') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bx bx-inbox me-1"></i>
                                    <div>
                                        <strong class="small">Inbox</strong>
                                        <small class="d-block">Received messages</small>
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="text-center py-2 bg-light rounded">
                                    <i class="bx bx-envelope display-6 text-muted mb-1"></i>
                                    <p class="text-muted mb-1 small">Login to access messages</p>
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                                </div>
                            </div>
                        @endauth

                        <!-- Support Section -->
                        <div class="col-12 mt-3">
                            <h6 class="text-muted mb-2 small">
                                <i class="bx bx-support me-2"></i>Support & Help
                            </h6>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.support.index') }}" class="btn btn-outline-primary w-100 py-2">
                                <i class="bx bx-help-circle me-1"></i>
                                <div>
                                    <strong class="small">Help Center</strong>
                                    <small class="d-block">Get support</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.support.contact') }}" class="btn btn-outline-info w-100 py-2">
                                <i class="bx bx-phone me-1"></i>
                                <div>
                                    <strong class="small">Contact Us</strong>
                                    <small class="d-block">Reach support</small>
                                </div>
                            </a>
                        </div>

                        <!-- System Section -->
                        <div class="col-12 mt-3">
                            <h6 class="text-muted mb-2 small">
                                <i class="bx bx-cog me-2"></i>System & Tools
                            </h6>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.requirements') }}" class="btn btn-outline-secondary w-100 py-2">
                                <i class="bx bx-list-check me-1"></i>
                                <div>
                                    <strong class="small">Requirements</strong>
                                    <small class="d-block">System requirements</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('user.sponsor-list') }}" class="btn btn-outline-success w-100 py-2">
                                <i class="bx bx-crown me-1"></i>
                                <div>
                                    <strong class="small">Sponsors</strong>
                                    <small class="d-block">Sponsor network</small>
                                </div>
                            </a>
                        </div>

                        <!-- Account Actions -->
                        <div class="col-12 mt-3">
                            <h6 class="text-muted mb-2 small">
                                <i class="bx bx-user-circle me-2"></i>Account Actions
                            </h6>
                        </div>
                        <div class="col-6">
                            <button onclick="toggleMobileTheme()" class="btn btn-outline-warning w-100 py-2">
                                <i class="bx bx-moon me-1 theme-icon"></i>
                                <div>
                                    <strong class="small">Theme</strong>
                                    <small class="d-block">Toggle dark mode</small>
                                </div>
                            </button>
                        </div>
                        <div class="col-6">
                            <button onclick="confirmLogout()" class="btn btn-outline-danger w-100 py-2">
                                <i class="bx bx-log-out me-1"></i>
                                <div>
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

    <!-- Lottery & Games Modal -->
    <div class="modal fade" id="mobileLotteryModal" tabindex="-1" aria-labelledby="mobileLotteryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 50%, #fd7e14 100%);">
                    <h5 class="modal-title" id="mobileLotteryModalLabel">
                        <i class="bx bx-gift me-2"></i>Lottery & Games
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @auth
                    <!-- Lottery Stats -->
                    <div class="row mb-4">
                        @php
                            try {
                                // Check if Lottery models exist before using them
                                $activeLotteries = class_exists('\App\Models\Lottery') 
                                    ? \App\Models\Lottery::where('status', 'active')->count() 
                                    : 3; // Default fallback number
                                
                                $userTickets = class_exists('\App\Models\LotteryTicket') 
                                    ? \App\Models\LotteryTicket::where('user_id', auth()->id())->count() 
                                    : rand(1, 8); // Random fallback for demo
                            } catch (Exception $e) {
                                $activeLotteries = 3; // Default fallback
                                $userTickets = rand(1, 8); // Random fallback for demo
                            }
                        @endphp
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded" style="background: linear-gradient(135deg, rgba(111, 66, 193, 0.15), rgba(232, 62, 140, 0.15));">
                                <h4 class="mb-1" style="color: #6f42c1;">{{$activeLotteries}}</h4>
                                <small class="text-muted">Active Lotteries</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3 rounded" style="background: linear-gradient(135deg, rgba(253, 126, 20, 0.15), rgba(255, 193, 7, 0.15));">
                                <h4 class="mb-1" style="color: #fd7e14;">{{$userTickets}}</h4>
                                <small class="text-muted">My Tickets</small>
                            </div>
                        </div>
                    </div>

                    <!-- Lottery Actions -->
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="{{ route('lottery.unified.index') }}" class="btn w-100 p-3 text-white" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                <i class="bx bx-trophy me-2"></i>
                                <div>
                                    <strong>Active Lotteries</strong>
                                    <small class="d-block text-white-75">View current draws & prizes</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('lottery.unified.available.plans') }}" class="btn btn-outline-primary w-100 p-3" style="border-color: #6f42c1; color: #6f42c1;">
                                <i class="bx bx-ticket me-2"></i>
                                <div>
                                    <strong>Buy Tickets</strong>
                                    <small class="d-block">Purchase lottery tickets</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('lottery.unified.activity.all') }}" class="btn btn-outline-success w-100 p-3" style="border-color: #fd7e14; color: #fd7e14;">
                                <i class="bx bx-receipt me-2"></i>
                                <div>
                                    <strong>My Activity</strong>
                                    <small class="d-block">Ticket history</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('lottery.unified.activity.all') }}" class="btn btn-outline-secondary w-100 p-3">
                                <i class="bx bx-history me-2"></i>
                                <div>
                                    <strong>Lottery History</strong>
                                    <small class="d-block">Past draws & winners</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('lottery.share') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="bx bx-share me-2"></i>
                                <div>
                                    <strong>Share & Earn</strong>
                                    <small class="d-block">Invite friends</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('lottery.unified.index') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <div>
                                    <strong>How to Play</strong>
                                    <small class="d-block">Rules & tips</small>
                                </div>
                            </a>
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

    /* Mobile Modal Height Adjustments */
    @media (max-width: 576px) {
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

    /* All mobile modals - ensure they fit screen */
    @media (max-width: 991.98px) {
        .modal-dialog {
            margin: 1rem !important;
        }
        
        .modal-content {
            border-radius: 12px !important;
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

    @stack('script')

    <!-- Mobile Navigation JavaScript -->
    <script>
    // IMMEDIATE DESKTOP DETECTION AND HIDE
    (function() {
        'use strict';
        
        // Check if desktop immediately on script load
        const isDesktop = window.innerWidth >= 992;
        const userAgent = navigator.userAgent.toLowerCase();
        const isDesktopDevice = !/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent);
        
        console.log('IMMEDIATE CHECK - Screen width:', window.innerWidth);
        console.log('Is Desktop by width:', isDesktop);
        console.log('Is Desktop device:', isDesktopDevice);
        
        // If desktop, hide mobile layout immediately
        if (isDesktop || isDesktopDevice) {
            console.log('Desktop detected - hiding mobile layout');
            
            // Hide all mobile elements immediately
            const style = document.createElement('style');
            style.textContent = `
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
            `;
            document.head.appendChild(style);
            
            // Also set the page class to ensure proper hiding
            document.documentElement.classList.add('desktop-mode');
            document.body.classList.remove('mobile-optimized');
            
            // Redirect to desktop layout if this is a mobile-only page
            if (window.location.pathname.includes('/mobile') || document.body.classList.contains('mobile-only')) {
                console.log('Redirecting to desktop version...');
                window.location.href = window.location.href.replace('/mobile', '');
            }
        }
    })();
    
    // Debug check - ensure JavaScript is loading
    console.log('Mobile layout JavaScript loaded successfully');
    
    // Test if modal function exists
    window.testMobileModal = function() {
        console.log('Testing mobile modal function...');
        openMobileModal('home');
    };
    
    // Mobile theme toggle function
    function toggleMobileTheme() {
        const html = document.documentElement;
        const themeIcon = document.querySelector('.theme-icon');
        
        if (html.getAttribute('data-theme-mode') === 'dark') {
            html.setAttribute('data-theme-mode', 'light');
            themeIcon.className = 'bx bx-moon theme-icon';
        } else {
            html.setAttribute('data-theme-mode', 'dark');
            themeIcon.className = 'bx bx-sun theme-icon';
        }
        
        // Save preference
        localStorage.setItem('theme-mode', html.getAttribute('data-theme-mode'));
    }

    // Disabled logout notification
    function showLogoutDisabled() {
        Swal.fire({
            title: 'Logout Disabled',
            text: 'Logout functionality has been disabled to keep you logged in.',
            icon: 'info',
            imageUrl: '{{asset("assets/images/users/16.jpg")}}',
            imageWidth: 80,
            imageHeight: 80,
            imageAlt: 'Profile Image',
            confirmButtonColor: '#6c757d',
            confirmButtonText: 'Got it!',
            backdrop: true,
            allowOutsideClick: true
        });
    }

    // Enhanced logout function with proper session handling - ENABLED
    function confirmLogout() {
        Swal.fire({
            title: 'Confirm Logout',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            imageUrl: '{{asset("assets/images/users/9.jpg")}}',
            imageWidth: 80,
            imageHeight: 80,
            imageAlt: 'Profile Image',
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
    }

    // Enhanced logout function with fallback mechanisms - ENABLED
    function performLogout() {
        // Show loading
        Swal.fire({
            title: 'Logging out...',
            text: 'Please wait',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Try simple logout first (GET request - no CSRF needed)
        function trySimpleLogout() {
            window.location.href = '{{ route("simple.logout") }}';
        }

        // Try form-based logout with CSRF (POST request)
        function tryFormLogout() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // Try direct URL logout
        function tryDirectLogout() {
            window.location.href = '/logout';
        }

        // Force manual logout as final fallback
        function forceLogout() {
            // Clear all local data
            try {
                localStorage.clear();
                sessionStorage.clear();
                
                // Clear cookies
                document.cookie.split(";").forEach(function(c) { 
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                });
                
            } catch (e) {
                console.log('Storage cleanup error:', e);
            }
            
            // Show success and redirect
            Swal.fire({
                title: 'Logout Complete',
                text: 'You have been logged out. Redirecting...',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("login") ?? "/login" }}';
            });
        }

        // Start with simple logout (most reliable)
        setTimeout(() => {
            trySimpleLogout();
            
            // If page doesn't redirect after 3 seconds, try form logout
            setTimeout(() => {
                tryFormLogout();
                
                // If still no redirect after another 3 seconds, try direct
                setTimeout(() => {
                    tryDirectLogout();
                    
                    // Final fallback after another 3 seconds
                    setTimeout(() => {
                        forceLogout();
                    }, 3000);
                }, 3000);
            }, 3000);
        }, 500);
    }

    // Initialize mobile theme
    function initializeMobileTheme() {
        const savedTheme = localStorage.getItem('theme-mode') || 'light';
        const html = document.documentElement;
        const themeIcon = document.querySelector('.theme-icon');
        
        html.setAttribute('data-theme-mode', savedTheme);
        if (themeIcon) {
            themeIcon.className = savedTheme === 'dark' ? 'bx bx-sun theme-icon' : 'bx bx-moon theme-icon';
        }
    }

    // Mobile modal function with proper toggle handling
    function openMobileModal(type) {
        console.log('openMobileModal called with type:', type);
        
        const modalId = `mobile${type.charAt(0).toUpperCase() + type.slice(1)}Modal`;
        console.log('Looking for modal with ID:', modalId);
        
        const modalElement = document.getElementById(modalId);
        
        if (!modalElement) {
            console.error('Modal not found:', modalId);
            alert('Modal not found: ' + modalId); // Temporary alert for debugging
            return;
        }

        console.log('Modal element found:', modalElement);

        // Check if this specific modal is already open using multiple detection methods
        const isCurrentlyShown = modalElement.classList.contains('show');
        const isCurrentlyVisible = modalElement.style.display === 'block';
        const currentModalInstance = bootstrap.Modal.getInstance(modalElement);
        const hasBackdrop = document.querySelector('.modal-backdrop');
        
        console.log('Modal state check:', {
            hasShowClass: isCurrentlyShown,
            isVisible: isCurrentlyVisible,
            hasInstance: !!currentModalInstance,
            hasBackdrop: !!hasBackdrop
        });
        
        // If modal is open in any way, close it (toggle behavior)
        if (isCurrentlyShown || isCurrentlyVisible || (currentModalInstance && hasBackdrop)) {
            console.log('Modal is already open, closing it');
            
            // Force close the modal using multiple methods
            if (currentModalInstance) {
                currentModalInstance.hide();
            } else {
                // Manually hide if no instance
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
            }
            
            // Clean up backdrops and body classes
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 150);
            
            return;
        }

        console.log('Opening new modal...');

        // Close ALL other modals first and clear any lingering backdrops
        const allModals = document.querySelectorAll('.modal');
        allModals.forEach(modal => {
            if (modal !== modalElement) {
                const instance = bootstrap.Modal.getInstance(modal);
                if (instance) {
                    instance.hide();
                }
                modal.classList.remove('show');
                modal.style.display = 'none';
            }
        });

        // Remove any stray modal backdrops
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Now open the new modal
            let modal;
            try {
                // Destroy existing instance if it exists
                const existingInstance = bootstrap.Modal.getInstance(modalElement);
                if (existingInstance) {
                    existingInstance.dispose();
                }
                
                // Create fresh modal instance
                modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                
                modal.show();
                
                // Update wallet balance if it's the wallet modal
                if (type === 'wallet') {
                    setTimeout(() => {
                        updateWalletBalance();
                    }, 300);
                }
                
                // Add haptic feedback if available
                if ('vibrate' in navigator) {
                    navigator.vibrate(15);
                }
            } catch (error) {
                console.error('Error creating modal:', error);
            }
        }, 100);
    }

    // Mark all notifications as read
    function markAllNotificationsRead() {
        // Make AJAX call to mark all notifications as read
        fetch('{{ route("user.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification badges
                document.querySelectorAll('.notification-dot').forEach(dot => {
                    dot.style.display = 'none';
                });
                
                // Show success message
                if ('vibrate' in navigator) {
                    navigator.vibrate([100, 50, 100]);
                }
                
                // Close modal after a brief delay
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mobileNotificationsModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }

    // Update wallet balance function
    function updateWalletBalance() {
        // Fetch current user balance
        fetch('/api/user/balance', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.balance !== undefined) {
                // Update balance display in wallet modal
                const balanceElements = document.querySelectorAll('.balance-display h2');
                balanceElements.forEach(element => {
                    element.textContent = '$' + parseFloat(data.balance).toFixed(2);
                });
                
                // Update balance indicator in navigation
                const balanceIndicators = document.querySelectorAll('.balance-indicator');
                balanceIndicators.forEach(indicator => {
                    indicator.textContent = '$' + parseFloat(data.balance).toFixed(0);
                });
                
                console.log('Balance updated:', data.balance);
            } else {
                console.log('Balance update failed:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching balance:', error);
            // Fallback to current PHP value
            console.log('Using fallback balance from PHP');
        });
    }

    // Main DOM loaded event listener
    document.addEventListener('DOMContentLoaded', function() {
        // Set current year for mobile footer
        const yearMobile = document.getElementById('year-mobile');
        if (yearMobile) {
            yearMobile.textContent = new Date().getFullYear();
        }

        // Update active nav item based on current route
        function updateActiveNavItem() {
            const currentPath = window.location.pathname;
            
            document.querySelectorAll('.mobile-nav-link').forEach(item => {
                const href = item.getAttribute('href');
                if (href && href !== 'javascript:void(0);' && currentPath.includes(href.replace(window.location.origin, ''))) {
                    item.classList.add('nav-active');
                } else {
                    item.classList.remove('nav-active');
                }
            });
        }

        // Update on page load
        updateActiveNavItem();

        // Add click handlers for navigation items with proper active state management
        document.querySelectorAll('.mobile-nav-link').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Remove active class from all nav items
                document.querySelectorAll('.mobile-nav-link').forEach(navItem => {
                    navItem.classList.remove('nav-active');
                });
                
                // Add active class to clicked item
                this.classList.add('nav-active');
                
                // Handle direct navigation links
                const href = this.getAttribute('href');
                if (href && href !== 'javascript:void(0);' && !href.startsWith('#')) {
                    // Navigate to the URL after a short delay to show the active state
                    setTimeout(() => {
                        window.location.href = href;
                    }, 150);
                }
                
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
        });

        // Specific handlers for modal buttons
        const modalButtons = [
            { selector: '.home-link', type: 'home' },
            { selector: '.videos-link', type: 'videos' },
            { selector: '.wallet-link', type: 'wallet' },
            { selector: '.profile-link', type: 'profile' },
            { selector: '.messages-link', type: 'messages' },
            { selector: '.notifications-link', type: 'notifications' },
            { selector: '.menu-link', type: 'more' }
        ];

        modalButtons.forEach(button => {
            const element = document.querySelector(button.selector);
            if (element) {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Modal button clicked:', button.selector, 'type:', button.type);
                    
                    // Add visual feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                    
                    // Small delay to allow visual feedback, then open modal
                    setTimeout(() => {
                        openMobileModal(button.type);
                    }, 50);
                });
            } else {
                console.warn('Modal button not found:', button.selector);
            }
        });

        // Add modal event listeners to clean up properly
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                // Clean up any stray backdrops and reset body
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    
                    // Ensure body classes are clean
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    // Remove any modal state classes
                    this.classList.remove('show');
                    this.style.display = 'none';
                    this.setAttribute('aria-hidden', 'true');
                    this.removeAttribute('aria-modal');
                    
                    console.log('Modal cleaned up:', this.id);
                }, 50);
            });
            
            modal.addEventListener('show.bs.modal', function() {
                // Ensure only one modal is shown at a time
                document.querySelectorAll('.modal.show').forEach(otherModal => {
                    if (otherModal !== this) {
                        const instance = bootstrap.Modal.getInstance(otherModal);
                        if (instance) {
                            instance.hide();
                        }
                        otherModal.classList.remove('show');
                        otherModal.style.display = 'none';
                    }
                });
                
                console.log('Modal showing:', this.id);
            });
            
            modal.addEventListener('shown.bs.modal', function() {
                console.log('Modal shown:', this.id);
            });
        });

        // Add visual feedback for button presses
        document.querySelectorAll('.mobile-nav-link').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.95)';
            });
            
            button.addEventListener('touchend', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add haptic feedback for mobile devices (if supported)
        if ('vibrate' in navigator) {
            document.querySelectorAll('.mobile-nav-link, .mobile-action-btn, .mobile-profile-btn').forEach(item => {
                item.addEventListener('touchstart', function() {
                    navigator.vibrate(10); // Short vibration
                });
            });
        }

        // Initialize theme on load
        initializeMobileTheme();

        // Initialize wallet balance on page load
        if (document.querySelector('.balance-indicator') || document.querySelector('.balance-display')) {
            updateWalletBalance();
        }

        console.log('Mobile layout loaded successfully');
    });
    </script>

</body>
</html>