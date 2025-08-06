<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    @stack('styles')
    
    <!-- Desktop Layout Custom Styles -->
    <style>
        /* Header optimizations */
        .header-element {
            margin-right: 0.75rem;
        }
        
        .header-element:last-child {
            margin-right: 0;
        }
        
        /* Profile dropdown improvements */
        .main-header-profile .header-link {
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .main-header-profile .header-link:hover {
            background-color: rgba(0,0,0,0.05);
        }
        
        /* Badge animations */
        .pulse {
            animation: pulse-animation 2s infinite;
        }
        
        @keyframes pulse-animation {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Sidebar optimizations */
        .main-sidebar {
            padding-top: 1rem;
        }
        
        /* Clean sidebar navigation */
        .main-menu-container {
            padding: 0 1rem;
        }
        
        /* Header icon spacing */
        .header-link-icon {
            font-size: 1.25rem;
        }
        
        /* Messages and notifications icons */
        .messages-dropdown .header-link,
        .notifications-dropdown .header-link {
            padding: 0.625rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        /* Theme toggle button styles */
        .header-theme-mode .header-link {
            padding: 0.625rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--primary-rgb), 0.1);
        }
        
        .header-theme-mode .header-link:hover {
            background: rgba(var(--primary-rgb), 0.2);
            transform: translateY(-1px);
        }
        
        .header-theme-mode .header-link-icon {
            font-size: 1.2rem;
            color: rgb(var(--primary-rgb));
        }
        
        /* Theme transition animations */
        html, body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .header, .main-sidebar, .main-content {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        .messages-dropdown .header-link:hover,
        .notifications-dropdown .header-link:hover {
            background-color: rgba(0,0,0,0.05);
        }
        
        /* User Profile Section Styles */
        .app-sidebar__user {
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .user-profile-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 249, 250, 0.95) 100%);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .user-profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        /* Avatar Section */
        .user-avatar-section .profile-avatar {
            position: relative;
            border: 3px solid #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .user-profile-img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .online-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .user-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .user-status {
            font-size: 0.8rem;
            color: #6c757d;
            background: rgba(0, 123, 255, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 500;
        }
        
        /* Balance Cards */
        .balance-cards-container {
            margin-top: 1rem;
        }
        
        .balance-card {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 12px;
            padding: 1rem;
            color: white;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        }
        
        .balance-card-content {
            display: flex;
            align-items: center;
        }
        
        .balance-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            opacity: 0.9;
        }
        
        .balance-label {
            display: block;
            font-size: 0.75rem;
            opacity: 0.8;
            font-weight: 500;
        }
        
        .balance-amount {
            display: block;
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }
        
        /* Quick Stats */
        .quick-stats-row {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            flex: 1;
            min-width: 0; /* Allow flex items to shrink below their content size */
            background: rgba(108, 117, 125, 0.1);
            border-radius: 8px;
            padding: 0.75rem 0.5rem;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .stat-item:hover {
            background: rgba(0, 123, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .stat-icon {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
            display: block;
        }
        
        .stat-icon.deposit {
            color: #28a745;
        }
        
        .stat-icon.interest {
            color: #ffc107;
        }
        
        .stat-label {
            display: block;
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 0.1rem;
        }
        
        .stat-value {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            line-height: 1.2;
            word-break: break-all; /* Prevent overflow on small amounts */
        }
        
        /* Quick Actions */
        .user-quick-actions {
            margin-top: 0.75rem;
        }
        
        .action-buttons-row {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
        }
        
        .action-btn {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.3rem;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.6rem;
            font-weight: 600;
            border: 1px solid transparent;
            line-height: 1.2;
            min-height: 50px;
        }
        
        .action-btn i {
            font-size: 0.95rem;
            margin-bottom: 0.15rem;
            display: block;
        }
        
        .action-btn span {
            font-size: 0.6rem;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        .action-btn.deposit-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .action-btn.deposit-btn:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(40, 167, 69, 0.3);
        }
        
        .action-btn.withdraw-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        
        .action-btn.withdraw-btn:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(220, 53, 69, 0.3);
        }
        
        .action-btn.profile-btn {
            background: linear-gradient(135deg, #6f42c1, #5a3d99);
            color: white;
        }
        
        .action-btn.profile-btn:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(111, 66, 193, 0.3);
        }
        
        /* Slide arrows */
        .slide-left, .slide-right {
            display: none; /* Hidden by default, can be shown if needed */
        }
        
        /* Dark mode adjustments */
        [data-theme-mode="dark"] .user-profile-card {
            background: linear-gradient(135deg, rgba(33, 37, 41, 0.9) 0%, rgba(52, 58, 64, 0.95) 100%);
            border-color: rgba(108, 117, 125, 0.2);
        }
        
        [data-theme-mode="dark"] .user-name {
            color: #f8f9fa;
        }
        
        [data-theme-mode="dark"] .stat-value {
            color: #f8f9fa;
        }
        
        [data-theme-mode="dark"] .stat-item {
            background: rgba(108, 117, 125, 0.2);
        }
        
        [data-theme-mode="dark"] .stat-item:hover {
            background: rgba(0, 123, 255, 0.2);
        }
        
        /* Responsive adjustments for sidebar */
        @media (max-width: 1399.98px) {
            .user-profile-card {
                padding: 1.1rem;
            }
            
            .balance-card {
                padding: 0.9rem;
            }
            
            .balance-icon {
                font-size: 1.3rem;
                margin-right: 0.6rem;
            }
            
            .balance-amount {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 1199.98px) {
            .user-profile-card {
                padding: 1rem;
            }
            
            .balance-card {
                padding: 0.75rem;
            }
            
            .balance-icon {
                font-size: 1.2rem;
                margin-right: 0.5rem;
            }
            
            .balance-label {
                font-size: 0.7rem;
            }
            
            .balance-amount {
                font-size: 0.95rem;
            }
            
            .quick-stats-row {
                gap: 0.4rem;
            }
            
            .stat-item {
                padding: 0.6rem 0.4rem;
            }
            
            .stat-icon {
                font-size: 1.1rem;
            }
            
            .stat-label {
                font-size: 0.65rem;
            }
            
            .stat-value {
                font-size: 0.75rem;
            }
            
            .action-buttons-row {
                gap: 0.3rem;
            }
            
            .action-btn {
                padding: 0.4rem 0.25rem;
                font-size: 0.55rem;
                min-height: 45px;
            }
            
            .action-btn i {
                font-size: 0.85rem;
                margin-bottom: 0.1rem;
            }
            
            .action-btn span {
                font-size: 0.55rem;
            }
        }
        
        @media (max-width: 991.98px) {
            .app-sidebar__user {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .user-profile-card {
                padding: 0.9rem;
            }
            
            .user-profile-img {
                width: 56px;
                height: 56px;
            }
            
            .user-name {
                font-size: 1rem;
            }
            
            .balance-card {
                padding: 0.7rem;
                margin-bottom: 0.6rem;
            }
            
            .balance-icon {
                font-size: 1.1rem;
                margin-right: 0.5rem;
            }
            
            .balance-amount {
                font-size: 0.9rem;
            }
            
            .quick-stats-row {
                gap: 0.3rem;
                flex-direction: column;
            }
            
            .stat-item {
                flex: none;
                padding: 0.5rem;
                display: flex;
                flex-direction: row;
                align-items: center;
                text-align: left;
            }
            
            .stat-icon {
                font-size: 1.1rem;
                margin-right: 0.5rem;
                margin-bottom: 0;
            }
            
            .stat-content {
                flex: 1;
            }
            
            .stat-label {
                font-size: 0.6rem;
                text-align: left;
                margin-bottom: 0.05rem;
            }
            
            .stat-value {
                font-size: 0.7rem;
                text-align: left;
            }
            
            .action-buttons-row {
                gap: 0.25rem;
            }
            
            .action-btn {
                padding: 0.35rem 0.2rem;
                font-size: 0.5rem;
                min-height: 40px;
            }
            
            .action-btn i {
                font-size: 0.75rem;
                margin-bottom: 0.08rem;
            }
            
            .action-btn span {
                font-size: 0.5rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .app-sidebar__user {
                padding: 0.5rem;
            }
            
            .user-profile-card {
                padding: 0.75rem;
            }
            
            .user-profile-img {
                width: 48px;
                height: 48px;
            }
            
            .user-name {
                font-size: 0.9rem;
            }
            
            .user-status {
                font-size: 0.7rem;
                padding: 1px 6px;
            }
            
            .balance-card {
                padding: 0.6rem;
                margin-bottom: 0.5rem;
            }
            
            .balance-label {
                font-size: 0.65rem;
            }
            
            .balance-amount {
                font-size: 0.85rem;
            }
            
            .quick-stats-row {
                gap: 0.25rem;
            }
            
            .stat-item {
                padding: 0.4rem;
            }
            
            .stat-icon {
                font-size: 1rem;
                margin-right: 0.4rem;
            }
            
            .stat-label {
                font-size: 0.55rem;
            }
            
            .stat-value {
                font-size: 0.65rem;
            }
            
            .action-btn {
                padding: 0.3rem 0.15rem;
                font-size: 0.45rem;
                min-height: 38px;
            }
            
            .action-btn i {
                font-size: 0.7rem;
                margin-bottom: 0.05rem;
            }
            
            .action-btn span {
                font-size: 0.45rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .user-profile-card {
                padding: 0.6rem;
            }
            
            .user-profile-img {
                width: 44px;
                height: 44px;
            }
            
            .online-indicator {
                width: 12px;
                height: 12px;
                border-width: 2px;
            }
            
            .user-name {
                font-size: 0.85rem;
            }
            
            .balance-card {
                padding: 0.5rem;
            }
            
            .balance-icon {
                font-size: 1rem;
                margin-right: 0.4rem;
            }
            
            .balance-label {
                font-size: 0.6rem;
            }
            
            .balance-amount {
                font-size: 0.8rem;
            }
            
            .stat-item {
                padding: 0.35rem;
            }
            
            .stat-icon {
                font-size: 0.9rem;
                margin-right: 0.35rem;
            }
            
            .stat-label {
                font-size: 0.5rem;
            }
            
            .stat-value {
                font-size: 0.6rem;
            }
            
            .action-buttons-row {
                gap: 0.2rem;
            }
            
            .action-btn {
                padding: 0.25rem 0.1rem;
                font-size: 0.4rem;
                min-height: 35px;
                border-radius: 4px;
            }
            
            .action-btn i {
                font-size: 0.65rem;
                margin-bottom: 0.05rem;
            }
            
            .action-btn span {
                font-size: 0.4rem;
                line-height: 1.1;
            }
        }
    </style>
</head>

<body>
    <div class="page">
         <!-- app-header -->
         <header class="app-header">

            <!-- Start::main-header-container -->
            <div class="main-header-container container-fluid">

                <!-- Start::header-content-left -->
                <div class="header-content-left">

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="{{route('user.dashboard')}}" class="header-logo">
                                <img src="{{asset('assets/images/brand-logos/desktop-logo.png')}}" alt="logo" class="desktop-logo">
                                <img src="{{asset('assets/images/brand-logos/toggle-logo.png')}}" alt="logo" class="toggle-logo">
                                <img src="{{asset('assets/images/brand-logos/desktop-white.png')}}" alt="logo" class="desktop-dark">
                                <img src="{{asset('assets/images/brand-logos/toggle-dark.png')}}" alt="logo" class="toggle-dark">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);">
                            <span></span>
                        </a>
                        <!-- End::header-link -->
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">

                    <!-- Start::header-element -->
                    <div class="header-element header-theme-mode">
                        <!-- Start::header-link|layout-setting -->
                        <a href="javascript:void(0);" class="header-link layout-setting" title="Toggle Theme">
                            <span class="light-layout" style="display: block;">
                                <!-- moon -->
                                <i class="bx bx-moon header-link-icon"></i>
                            </span>
                            <span class="dark-layout" style="display: none;">
                                <!-- sun -->
                                <i class="bx bx-sun header-link-icon"></i>
                            </span>
                        </a>
                        <!-- End::header-link|layout-setting -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element messages-dropdown">
                        <!-- Start::header-link|messages -->
                        <a href="{{ route('user.messages') }}" class="header-link position-relative" title="Messages">
                            <i class="bx bx-envelope header-link-icon"></i>
                            @auth
                                @php
                                    try {
                                        $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                                    } catch (Exception $e) {
                                        $totalMessagesCount = 0;
                                    }
                                @endphp
                                @if($totalMessagesCount > 0)
                                    <span class="badge bg-secondary rounded-pill header-icon-badge pulse pulse-secondary" id="message-icon-badge">{{$totalMessagesCount}}</span>
                                @endif
                            @endauth
                        </a>
                        <!-- End::header-link|messages -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element notifications-dropdown">
                        <!-- Start::header-link|notifications -->
                        <a href="{{ route('user.notifications.index') }}" class="header-link position-relative" title="Notifications">
                            <i class="bx bx-bell header-link-icon"></i>
                            @auth
                                @php
                                    try {
                                        $unreadNotificationsCount = auth()->user()->unreadNotifications()->count();
                                    } catch (Exception $e) {
                                        $unreadNotificationsCount = 0;
                                    }
                                @endphp
                                @if($unreadNotificationsCount > 0)
                                    <span class="badge bg-primary rounded-pill header-icon-badge pulse pulse-primary" id="notification-icon-badge">{{$unreadNotificationsCount}}</span>
                                @endif
                            @endauth
                        </a>
                        <!-- End::header-link|notifications -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element --> 
                    <div class="header-element main-header-profile">
                        <!-- Start::header-link|dropdown-toggle -->
                        @auth
                        <a href="javascript:void(0);" class="header-link dropdown-toggle mx-0 w-100" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="me-sm-2 me-0">
                                    <img src="{{auth()->user()->image ? asset('assets/images/users/'.auth()->user()->image) : asset('assets/images/users/16.jpg')}}" alt="img" width="32" height="32" class="rounded-circle">
                                </div>
                                <div class="d-sm-block d-none">
                                    <p class="fw-semibold mb-0 lh-1">{{auth()->user()->firstname ?? 'User'}} {{auth()->user()->lastname ?? ''}}</p>
                                    <span class="op-7 fw-normal d-block fs-11">{{auth()->user()->username ?? 'Unknown'}}</span>
                                </div>
                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <li><a class="dropdown-item d-flex" href="{{route('profile.index')}}"><i class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile</a></li>
                            <li><a class="dropdown-item d-flex" href="{{route('user.dashboard')}}"><i class="ti ti-dashboard fs-18 me-2 op-7"></i>Dashboard</a></li>
                            <li><a class="dropdown-item d-flex" href="{{ route('profile.edit') }}"><i class="ti ti-settings fs-18 me-2 op-7"></i>Settings</a></li>
                            <li>
                                <button type="button" onclick="confirmDesktopLogout()" class="dropdown-item d-flex">
                                    <i class="ti ti-power fs-18 me-2 op-7"></i>Logout
                                </button>
                            </li>
                        </ul>
                        @else
                        <!-- Guest User - Login Button -->
                        <a href="{{ route('login') }}" class="header-link mx-0" title="Login">
                            <div class="d-flex align-items-center">
                                <div class="me-sm-2 me-0">
                                    <img src="{{asset('assets/images/users/16.jpg')}}" alt="img" width="32" height="32" class="rounded-circle">
                                </div>
                                <div class="d-sm-block d-none">
                                    <p class="fw-semibold mb-0 lh-1">Guest User</p>
                                    <span class="op-7 fw-normal d-block fs-11">Click to Login</span>
                                </div>
                            </div>
                        </a>
                        @endauth
                    </div>  
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->

        </header>
        <!-- /app-header -->
        
        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{route('user.dashboard')}}" class="header-logo text-decoration-none">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="logo-icon-container me-2">
                            <i class="fe fe-play-circle logo-icon"></i>
                        </div>
                        <div class="logo-text-container">
                            <span class="logo-text">EarnHub Pro</span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open" id="main-menu">
                     <!-- Start::Sidebar User -->
                    <div class="app-sidebar__user mb-4">
                        <div class="user-profile-card">
                            <!-- User Avatar Section -->
                            <div class="user-avatar-section text-center mb-3">
                                <div class="position-relative d-inline-block">
                                    <span class="avatar avatar-xxl online avatar-rounded profile-avatar">
                                        @auth
                                            <img src="{{ Auth::user()->avatar_url }}" alt="User Avatar" class="user-profile-img">
                                        @else
                                            <img src="{{ asset('assets/images/users/16.jpg') }}" alt="Default Avatar" class="user-profile-img">
                                        @endauth
                                    </span>
                                    <div class="online-indicator"></div>
                                </div>
                                <div class="user-basic-info mt-2">
                                    <h5 class="user-name mb-1">@auth{{ Auth::user()->username }}@endauth</h5>
                                    <span class="user-status">Active Member</span>
                                </div>
                            </div>

                            <!-- Balance Cards Section -->
                            <div class="balance-cards-container mb-3">
                                <!-- Main Balance Card -->
                                <div class="balance-card main-balance">
                                    <div class="balance-card-content">
                                        <div class="balance-icon">
                                            <i class="bx bx-dollar-circle"></i>
                                        </div>
                                        <div class="balance-info">
                                            <span class="balance-label">Total Balance</span>
                                            <span class="balance-amount" data-realtime-update="sidebar-total-balance">
                                                $@auth{{ getAmount(auth()->user()->deposit_wallet+auth()->user()->interest_wallet) }}@endauth
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Stats Row -->
                                <div class="quick-stats-row">
                                    <div class="stat-item">
                                        <div class="stat-icon deposit">
                                            <i class="bx bx-trending-up"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span class="stat-label">Deposits</span>
                                            <span class="stat-value" data-realtime-update="sidebar-deposit">$@auth{{ getAmount(auth()->user()->deposit_wallet) }}@endauth</span>
                                        </div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon interest">
                                            <i class="bx bx-star"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span class="stat-label">Interest</span>
                                            <span class="stat-value" data-realtime-update="sidebar-interest">$@auth{{ getAmount(auth()->user()->interest_wallet) }}@endauth</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="user-quick-actions">
                                <div class="action-buttons-row">
                                    <a href="{{ route('deposit.index') }}" class="action-btn deposit-btn" title="Make Deposit">
                                        <i class="bx bx-plus"></i>
                                        <span>Deposit</span>
                                    </a>
                                    <a href="{{ route('user.withdraw.wallet') }}" class="action-btn withdraw-btn" title="Withdraw Funds">
                                        <i class="bx bx-minus"></i>
                                        <span>Withdraw</span>
                                    </a>
                                    <a href="{{ route('profile.index') }}" class="action-btn profile-btn" title="View Profile">
                                        <i class="bx bx-user"></i>
                                        <span>Profile</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!-- End::Sidebar User -->
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>

                    <x-userMenu />

                </nav>
                <!-- End::nav -->
                
                <!-- Slide Right Arrow -->
                <div class="slide-right" id="slide-right">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="m10.707 17.707 5.707-5.707-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg>
                </div>

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!-- End::app-content -->

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 shadow-none">
            <div class="container text-center">
                <span class="text-muted"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-dark fw-semibold">www.payperviews.net</a>.
                     All rights reserved
                </span>
            </div>
        </footer>
        <!-- Footer End -->

    </div>

    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>

    <!-- Popper JS -->
    <script src="{{asset('assets/libs/@popperjs/core/umd/popper.min.js')}}"></script>

    <!-- Bootstrap JS -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{asset('assets/js/defaultmenu.min.js')}}"></script>

    <!-- Node Waves JS-->
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- Sticky JS -->
    <script src="{{asset('assets/js/sticky.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/js/simplebar.js')}}"></script>

    <!-- Color Picker JS -->
    <script src="{{asset('assets/libs/@simonwep/pickr/pickr.es5.min.js')}}"></script>

    <!-- Custom-Switcher JS -->
    <script src="{{asset('assets/js/custom-switcher.min.js')}}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets/js/custom.js')}}"></script>

    @stack('scripts')

    <script>
    // Desktop logout confirmation with SweetAlert
    function confirmDesktopLogout() {
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
                performDesktopLogout();
            }
        });
    }

    // Desktop logout execution with fallbacks
    function performDesktopLogout() {
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

    document.addEventListener('DOMContentLoaded', function() {
        // Set current year
        document.getElementById('year').textContent = new Date().getFullYear();
        
        // Header enhancements
        initHeaderEnhancements();
        
        // Initialize theme toggle
        initThemeToggle();
        
        console.log('Desktop layout loaded successfully');
    });
    
    function initThemeToggle() {
        // Theme toggle functionality
        const themeToggle = document.querySelector('.layout-setting');
        if (themeToggle) {
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleDesktopTheme();
            });
        }
        
        // Initialize theme on page load
        const savedTheme = localStorage.getItem('theme') || 'light';
        applyDesktopTheme(savedTheme);
    }
    
    function toggleDesktopTheme() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        localStorage.setItem('theme', newTheme);
        applyDesktopTheme(newTheme);
        
        console.log('Desktop theme switched to:', newTheme);
    }
    
    function applyDesktopTheme(theme) {
        const html = document.documentElement;
        const body = document.body;
        
        // Update data attributes
        html.setAttribute('data-theme-mode', theme);
        html.setAttribute('data-header-styles', theme);
        body.setAttribute('data-theme', theme);
        
        if (theme === 'dark') {
            // Dark theme styles
            html.classList.add('dark');
            body.classList.add('dark-mode');
            
            // Update theme toggle icon
            document.querySelector('.light-layout').style.display = 'none';
            document.querySelector('.dark-layout').style.display = 'block';
            
        } else {
            // Light theme styles
            html.classList.remove('dark');
            body.classList.remove('dark-mode');
            
            // Update theme toggle icon
            document.querySelector('.light-layout').style.display = 'block';
            document.querySelector('.dark-layout').style.display = 'none';
        }
        
        // Sync with mobile theme if both layouts are present
        const mobileThemeIcon = document.getElementById('mobileThemeIcon');
        if (mobileThemeIcon) {
            if (theme === 'dark') {
                mobileThemeIcon.className = 'fe fe-sun topbar-icon';
            } else {
                mobileThemeIcon.className = 'fe fe-moon topbar-icon';
            }
        }
        
        // Trigger theme change event for other components
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { theme: theme } 
        }));
    }
    
    function initHeaderEnhancements() {
        // Set current year
        const yearElement = document.getElementById('year');
        if (yearElement) {
            yearElement.textContent = new Date().getFullYear();
        }
        
        // Profile dropdown auto-close on outside click
        document.addEventListener('click', function(event) {
            const profileDropdown = document.getElementById('mainHeaderProfile');
            const dropdownMenu = profileDropdown?.nextElementSibling;
            
            if (profileDropdown && dropdownMenu && !profileDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
                const bsDropdown = bootstrap.Dropdown.getInstance(profileDropdown);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            }
        });
        
        // Header link hover effects
        const headerLinks = document.querySelectorAll('.header-link');
        headerLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Badge pulse animation control
        const badges = document.querySelectorAll('.header-icon-badge');
        badges.forEach(badge => {
            // Pause animation on hover
            badge.addEventListener('mouseenter', function() {
                this.style.animationPlayState = 'paused';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.animationPlayState = 'running';
            });
        });
        
        // Initialize user profile card animations
        const profileCard = document.querySelector('.user-profile-card');
        if (profileCard) {
            // Add smooth hover effects
            profileCard.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            profileCard.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
        
        // Initialize action button interactions
        const actionButtons = document.querySelectorAll('.action-btn');
        actionButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    </script>

</body>
</html>
