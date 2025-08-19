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
    @if(auth()->check())
    <meta name="user-authenticated" content="true">
    @endif

    <!-- Favicon -->
    @php
        $faviconUrl = asset('favicon.svg');
        if (isset($settings) && $settings && $settings->favicon) {
            $faviconUrl = getMediaUrl($settings->favicon, 'favicon');
        }
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">

    <!-- Choices JS -->
    {{-- <script src="{{asset('assets/libs/choices.js/public/assets/scripts/choices.min.js')}}"></script> --}}
    {{-- Choices.js disabled to improve loading performance --}}

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
        
        /* Enhanced Header Profile Dropdown Styles */
        .header-profile-dropdown {
            min-width: 280px !important;
            border: none !important;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12) !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }
        
        .dropdown-header-section .bg-gradient-primary {
            background: linear-gradient(135deg, #6A5ACD, #1E90FF) !important;
        }
        
        .dropdown-section {
            padding: 0 !important;
        }
        
        .dropdown-header {
            padding: 8px 16px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            margin: 0 !important;
        }
        
        .header-profile-dropdown .dropdown-item {
            padding: 12px 16px !important;
            border: none !important;
            transition: all 0.3s ease !important;
            font-size: 0.875rem !important;
            position: relative !important;
        }
        
        .header-profile-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(106, 90, 205, 0.1), rgba(30, 144, 255, 0.1)) !important;
            transform: translateX(5px) !important;
            padding-left: 20px !important;
        }
        
        .header-profile-dropdown .dropdown-item i {
            font-size: 16px !important;
            width: 20px !important;
            text-align: center !important;
        }
        
        .header-profile-dropdown .dropdown-item:hover i {
            transform: scale(1.1) !important;
        }
        
        .header-profile-dropdown .text-danger:hover {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 107, 107, 0.1)) !important;
            color: #dc3545 !important;
        }
        
        .header-profile-dropdown .btn-outline-primary,
        .header-profile-dropdown .btn-outline-success {
            border-radius: 8px !important;
            font-size: 0.75rem !important;
            padding: 6px 8px !important;
            transition: all 0.3s ease !important;
        }
        
        .header-profile-dropdown .btn-outline-primary:hover {
            background: linear-gradient(135deg, #6A5ACD, #1E90FF) !important;
            border-color: transparent !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(106, 90, 205, 0.3) !important;
        }
        
        .header-profile-dropdown .btn-outline-success:hover {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            border-color: transparent !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3) !important;
        }
        
        .user-balance-info .badge {
            font-size: 0.75rem !important;
            padding: 6px 12px !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
        }
        
        /* Mobile responsive dropdown optimizations */
        @media (max-width: 768px) {
            .main-header-dropdown {
                max-height: 85vh !important;
                overflow-y: auto !important;
                margin-top: 0 !important;
                border-radius: 12px !important;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;
            }
            
            .dropdown-header-section {
                position: sticky !important;
                top: 0 !important;
                z-index: 10 !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border-radius: 12px 12px 0 0 !important;
            }
            
            .dropdown-section {
                padding: 8px 0 !important;
            }
            
            .dropdown-section h6.dropdown-header {
                padding: 8px 16px 4px 16px !important;
                margin-bottom: 4px !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }
            
            .dropdown-item {
                padding: 10px 16px !important;
                font-size: 0.875rem !important;
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            }
            
            .dropdown-item:last-child {
                border-bottom: none !important;
            }
            
            /* Logout section - always visible at bottom */
            .logout-section {
                position: sticky !important;
                bottom: 0 !important;
                background: #fff !important;
                border-top: 2px solid #f8f9fa !important;
                z-index: 20 !important;
                border-radius: 0 0 12px 12px !important;
            }
            
            .logout-section .dropdown-item {
                border: none !important;
                padding: 15px 16px !important;
                font-weight: 600 !important;
                text-align: center !important;
                background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
                color: white !important;
                margin: 10px !important;
                border-radius: 8px !important;
                font-size: 0.9rem !important;
            }
            
            .logout-section .dropdown-item:hover {
                background: linear-gradient(135deg, #c82333, #dc3545) !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
            }
            
            /* Quick actions simplified for mobile */
            .mobile-quick-actions {
                display: flex !important;
                justify-content: space-between !important;
                padding: 10px !important;
                background: #f8f9fa !important;
                border-radius: 8px !important;
                margin: 0 10px 10px 10px !important;
            }
            
            .mobile-quick-actions .btn {
                flex: 1 !important;
                margin: 0 2px !important;
                padding: 8px 4px !important;
                font-size: 0.75rem !important;
                border-radius: 6px !important;
            }
        }
        
        /* Dropdown item icons color coding */
        .dropdown-item .text-primary { color: #6A5ACD !important; }
        .dropdown-item .text-info { color: #17a2b8 !important; }
        .dropdown-item .text-warning { color: #ffc107 !important; }
        .dropdown-item .text-success { color: #28a745 !important; }
        .dropdown-item .text-danger { color: #dc3545 !important; }
        .dropdown-item .text-purple { color: #6f42c1 !important; }
        .dropdown-item .text-orange { color: #fd7e14 !important; }
        
        /* Hover animation for chevron icons */
        .dropdown-item:hover .fe-chevron-right {
            transform: translateX(3px) !important;
            transition: transform 0.3s ease !important;
        }
        
        /* Badge animations */
        .dropdown-item .badge {
            animation: pulse 2s infinite !important;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Desktop-specific enhancements */
        .desktop-logout-popup {
            border-radius: 15px !important;
        }
        
        /* Real-time balance update styling */
        .realtime-loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Avatar hover effects */
        .avatar:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        
        /* Mobile responsive adjustments for desktop layout */
        @media (max-width: 768px) {
            .header-profile-dropdown {
                min-width: 260px !important;
            }
            
            .dropdown-header {
                font-size: 0.7rem !important;
            }
            
            .header-profile-dropdown .dropdown-item {
                padding: 10px 14px !important;
                font-size: 0.8rem !important;
            }
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
        
        /* Enhanced Logo Text Styles */
        .logo-text-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-text {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px !important;
            line-height: 1.2 !important;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            display: inline-block;
        }
        
        .logo-primary {
            background: linear-gradient(135deg, #6A5ACD, #1E90FF) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 800 !important;
            text-shadow: none !important;
        }
        
        .logo-secondary {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 800 !important;
            text-shadow: none !important;
        }
        
        .logo-accent {
            background: linear-gradient(135deg, #dc3545, #fd7e14) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 800 !important;
            text-shadow: none !important;
            position: relative;
        }
        
        .logo-accent::after {
            content: '✨';
            position: absolute;
            top: -8px;
            right: -15px;
            font-size: 0.8rem;
            animation: sparkle 2s infinite;
        }
        
        @keyframes sparkle {
            0%, 100% { opacity: 0.7; transform: scale(1) rotate(0deg); }
            50% { opacity: 1; transform: scale(1.1) rotate(180deg); }
        }
        
        /* Logo hover effects */
        .header-logo:hover .logo-text {
            transform: scale(1.05) !important;
        }
        
        .header-logo:hover .logo-primary {
            background: linear-gradient(135deg, #7B68EE, #00BFFF) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        .header-logo:hover .logo-secondary {
            background: linear-gradient(135deg, #32cd32, #00ff7f) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        .header-logo:hover .logo-accent {
            background: linear-gradient(135deg, #ff4757, #ffa502) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        /* Dark mode logo adjustments */
        [data-theme-mode="dark"] .logo-primary {
            background: linear-gradient(135deg, #9370DB, #87CEEB) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        [data-theme-mode="dark"] .logo-secondary {
            background: linear-gradient(135deg, #32cd32, #7fffd4) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        [data-theme-mode="dark"] .logo-accent {
            background: linear-gradient(135deg, #ff6b6b, #feca57) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
        }
        
        /* Responsive logo text sizing */
        @media (max-width: 1399.98px) {
            .logo-text {
                font-size: 1.4rem !important;
            }
            .logo-accent::after {
                right: -12px;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 1199.98px) {
            .logo-text {
                font-size: 1.3rem !important;
            }
            .logo-accent::after {
                right: -10px;
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 991.98px) {
            /* Hide sidebar logo on tablet and mobile */
            .main-sidebar-header {
                display: none !important;
            }
            
            /* Hide entire horizontal logo area on mobile */
            .horizontal-logo {
                display: none !important;
            }
            
            /* Hide specific header logo on mobile */
            .toggle-dark {
                display: none !important;
            }
            
            .logo-text {
                font-size: 1.2rem !important;
            }
            .logo-accent::after {
                right: -8px;
                font-size: 0.65rem;
            }
        }
        
        @media (max-width: 767.98px) {
            /* Hide sidebar logo on mobile */
            .main-sidebar-header {
                display: none !important;
            }
            
            /* Hide entire horizontal logo area on mobile */
            .horizontal-logo {
                display: none !important;
            }
            
            /* Hide specific header logo on mobile */
            .toggle-dark {
                display: none !important;
            }
            
            .logo-text {
                font-size: 1.1rem !important;
            }
            .logo-accent::after {
                right: -6px;
                font-size: 0.6rem;
            }
        }
        
        @media (max-width: 575.98px) {
            /* Hide sidebar logo on small mobile */
            .main-sidebar-header {
                display: none !important;
            }
            
            /* Hide entire horizontal logo area on mobile */
            .horizontal-logo {
                display: none !important;
            }
            
            /* Hide specific header logo on mobile */
            .toggle-dark {
                display: none !important;
            }
            
            .logo-text {
                font-size: 1rem !important;
            }
            .logo-accent::after {
                display: none; /* Hide sparkle on very small screens */
            }
        }
        
        /* Logo container responsive adjustments */
        @media (max-width: 480px) {
            .logo-text-container {
                flex-direction: column;
                align-items: center;
            }
            
            .logo-text {
                font-size: 0.9rem !important;
                text-align: center;
            }
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
            
            /* Hide sidebar logo on mobile/tablet */
            .main-sidebar-header {
                display: none !important;
            }
        }
        
        @media (max-width: 767.98px) {
            /* Hide sidebar logo on mobile */
            .main-sidebar-header {
                display: none !important;
            }
            
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
            /* Hide sidebar logo on small mobile */
            .horizontal-logo {
                display: none !important;
            }
            
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

        /* Real-time notification animations */
        .pulse-animation {
            animation: bellPulse 2s infinite;
        }

        @keyframes bellPulse {
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

        .header-icon-badge {
            animation: badgeGlow 2s infinite alternate;
        }

        @keyframes badgeGlow {
            0% {
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            }
            100% {
                box-shadow: 0 0 15px rgba(0, 123, 255, 0.8);
            }
        }

        /* Modal System Styles */
        .dynamic-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dynamic-modal.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile modal adjustments */
        .dynamic-modal.mobile-modal {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(3px);
        }

        .dynamic-modal.small-mobile-modal {
            background: rgba(0, 0, 0, 0.9);
        }

        .modal-content-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 90%;
            max-height: 90%;
            overflow: hidden;
            transition: all 0.3s ease;
            width: 600px;
        }

        .dynamic-modal.show .modal-content-wrapper {
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .modal-body-custom {
            padding: 2rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .modal-footer-custom {
            padding: 1rem 2rem;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .modal-close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-title-custom {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-subtitle-custom {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .modal-content-wrapper {
                max-width: 95%;
                width: calc(100vw - 20px);
                max-height: 85vh;
                margin: 10px;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.8);
                border-radius: 12px;
            }
            
            .dynamic-modal.show .modal-content-wrapper {
                transform: translate(-50%, -50%) scale(1);
            }
            
            .modal-body-custom {
                padding: 1.5rem;
                max-height: 60vh;
                overflow-y: auto;
            }
            
            .modal-header-custom {
                padding: 1rem;
                border-radius: 12px 12px 0 0;
            }
            
            .modal-footer-custom {
                padding: 1rem;
                position: sticky;
                bottom: 0;
                background: #f8f9fa;
                border-radius: 0 0 12px 12px;
            }
            
            .modal-title-custom {
                font-size: 1.25rem;
            }
            
            .modal-subtitle-custom {
                font-size: 0.8rem;
            }
            
            .modal-close-btn {
                top: 10px;
                right: 15px;
                width: 30px;
                height: 30px;
            }
        }

        @media (max-width: 480px) {
            .modal-content-wrapper {
                max-width: 98%;
                width: calc(100vw - 10px);
                max-height: 90vh;
                margin: 5px;
                border-radius: 8px;
            }
            
            .modal-body-custom {
                padding: 1rem;
                max-height: 65vh;
            }
            
            .modal-header-custom {
                padding: 0.75rem;
                border-radius: 8px 8px 0 0;
            }
            
            .modal-footer-custom {
                padding: 0.75rem;
                border-radius: 0 0 8px 8px;
            }
            
            .modal-title-custom {
                font-size: 1.1rem;
                line-height: 1.3;
            }
            
            .modal-subtitle-custom {
                font-size: 0.75rem;
                margin-top: 0.25rem;
            }
            
            .modal-close-btn {
                top: 8px;
                right: 12px;
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 320px) {
            .modal-content-wrapper {
                max-width: 100%;
                width: 100vw;
                max-height: 95vh;
                margin: 0;
                border-radius: 0;
                top: 50%;
                left: 50%;
            }
            
            .modal-body-custom {
                padding: 0.75rem;
                max-height: 70vh;
            }
            
            .modal-header-custom {
                padding: 0.5rem;
                border-radius: 0;
            }
            
            .modal-footer-custom {
                padding: 0.5rem;
                border-radius: 0;
            }
            
            .modal-title-custom {
                font-size: 1rem;
            }
            
            .modal-subtitle-custom {
                font-size: 0.7rem;
            }
        }
    </style>

    <!-- Modal System JavaScript -->
    <script>
        class DynamicModalSystem {
            constructor() {
                this.modals = [];
                this.currentModal = null;
                this.shownModals = JSON.parse(localStorage.getItem('shownModals') || '{}');
                this.sessionStartTime = Date.now();
                this.init();
            }

            init() {
                // Only initialize if we're on a user-facing page (not admin)
                if (window.location.pathname.startsWith('/admin')) {
                    console.debug('Modal system disabled for admin pages');
                    return;
                }
                
                // Load modals from server
                this.loadModals();
                
                // Track session time for modal display rules
                setInterval(() => {
                    this.checkModalDisplay();
                }, 5000); // Check every 5 seconds
            }

            async loadModals() {
                try {
                    // Check if CSRF token exists
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.debug('No CSRF token found, using fallback modals');
                        this.loadFallbackModals();
                        return;
                    }
                    
                    // Use the correct API endpoint
                    const response = await fetch('/api/modals/get-modals', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken.content
                        }
                    });
                    
                    // Check if response is actually JSON
                    const contentType = response.headers.get('content-type');
                    if (response.ok && contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        if (result.success && result.data) {
                            this.modals = result.data;
                            console.info('Loaded ' + result.data.length + ' modals from API (count: ' + (result.count || result.data.length) + ')');
                            console.debug('Modal data:', result.data);
                            setTimeout(() => {
                                this.checkModalDisplay();
                            }, 2000); // Initial check after 2 seconds
                        } else {
                            console.debug('No active modals available from API, using fallback system');
                            this.loadFallbackModals();
                        }
                    } else {
                        // API endpoint doesn't exist or returns HTML, use fallback
                        console.debug('Modal API response invalid, using fallback system');
                        this.loadFallbackModals();
                    }
                } catch (error) {
                    console.debug('Modal API unavailable (' + error.message + '), using fallback system');
                    this.loadFallbackModals();
                }
            }

            loadFallbackModals() {
                // Fallback modal system when API is not available
                this.modals = [
                    {
                        id: 'welcome',
                        title: 'Welcome to PayPerViews!',
                        subtitle: 'Get started with your investment journey',
                        heading: 'Quick Start Guide',
                        description: '<p>Thank you for joining us! Here are some quick tips to get you started:</p><ul><li>Complete your profile information</li><li>Verify your email address</li><li>Make your first deposit</li><li>Explore our investment plans</li></ul>',
                        is_active: true,
                        show_frequency: 'session',
                        delay_seconds: 3,
                        minimum_session_time: 5,
                        show_on_mobile_only: false,
                        show_on_desktop_only: false,
                        include_routes: [],
                        exclude_routes: ['/admin', '/login', '/register'],
                        max_shows: 1
                    }
                ];
                
                console.debug('Fallback modal system initialized with ' + this.modals.length + ' modal(s)');
                
                // Only show for specific conditions
                const isLoggedIn = document.querySelector('meta[name="user-authenticated"]');
                const isAdminPage = window.location.pathname.startsWith('/admin');
                
                if (isLoggedIn && !isAdminPage) {
                    setTimeout(() => {
                        this.checkModalDisplay();
                    }, 3000);
                }
            }

            checkModalDisplay() {
                const currentRoute = window.location.pathname;
                const sessionTime = (Date.now() - this.sessionStartTime) / 1000;

                console.debug('Checking modal display:', {
                    currentRoute: currentRoute,
                    sessionTime: sessionTime,
                    totalModals: this.modals.length
                });

                for (const modal of this.modals) {
                    console.debug('Checking modal:', modal.modal_name || modal.id, {
                        modal: modal,
                        shouldShow: this.shouldShowModal(modal, currentRoute, sessionTime)
                    });
                    
                    if (this.shouldShowModal(modal, currentRoute, sessionTime)) {
                        console.info('Showing modal:', modal.modal_name || modal.id);
                        this.showModal(modal);
                        break; // Show only one modal at a time
                    }
                }
            }

            shouldShowModal(modal, currentRoute, sessionTime) {
                console.debug(`Checking modal ${modal.modal_name}:`);
                
                // Check if modal is active
                if (!modal.is_active) {
                    console.debug(`- Modal ${modal.modal_name} is not active`);
                    return false;
                }
                console.debug(`- Modal ${modal.modal_name} is active ✓`);

                // Check if modal should be shown on current route
                console.debug(`- Current route: ${currentRoute}`);
                console.debug(`- Exclude routes:`, modal.exclude_routes);
                console.debug(`- Include routes:`, modal.include_routes);
                
                if (modal.exclude_routes && modal.exclude_routes.includes(currentRoute)) {
                    console.debug(`- Route ${currentRoute} is excluded`);
                    return false;
                }
                
                if (modal.include_routes && modal.include_routes.length > 0 && !modal.include_routes.includes(currentRoute)) {
                    console.debug(`- Route ${currentRoute} is not in include list`);
                    return false;
                }
                console.debug(`- Route check passed ✓`);

                // Check session time requirement
                console.debug(`- Session time: ${sessionTime}s, Required: ${modal.minimum_session_time || 0}s`);
                if (sessionTime < (modal.minimum_session_time || 0)) {
                    console.debug(`- Session time requirement not met (${sessionTime} < ${modal.minimum_session_time})`);
                    return false;
                }
                console.debug(`- Session time check passed ✓`);

                // Check device restrictions
                const isMobile = window.innerWidth <= 768;
                console.debug(`- Is mobile: ${isMobile}, Show on mobile only: ${modal.show_on_mobile_only}, Show on desktop only: ${modal.show_on_desktop_only}`);
                
                if (modal.show_on_mobile_only && !isMobile) {
                    console.debug(`- Mobile only restriction failed`);
                    return false;
                }
                if (modal.show_on_desktop_only && isMobile) {
                    console.debug(`- Desktop only restriction failed`);
                    return false;
                }
                console.debug(`- Device restrictions passed ✓`);

                // Check frequency rules
                const modalKey = `modal_${modal.id}`;
                const today = new Date().toDateString();
                const thisWeek = this.getWeekKey();

                if (!this.shownModals[modalKey]) {
                    this.shownModals[modalKey] = { count: 0, dates: [], lastShown: null };
                }

                const modalData = this.shownModals[modalKey];
                console.debug(`- Frequency: ${modal.show_frequency}, Modal data:`, modalData);

                let frequencyResult;
                switch (modal.show_frequency) {
                    case 'once':
                        frequencyResult = modalData.count === 0;
                        console.debug(`- Once frequency: count=${modalData.count}, result=${frequencyResult}`);
                        return frequencyResult;
                    case 'daily':
                        frequencyResult = !modalData.dates.includes(today);
                        console.debug(`- Daily frequency: today=${today}, dates=${modalData.dates}, result=${frequencyResult}`);
                        return frequencyResult;
                    case 'weekly':
                        frequencyResult = modalData.lastShown !== thisWeek;
                        console.debug(`- Weekly frequency: thisWeek=${thisWeek}, lastShown=${modalData.lastShown}, result=${frequencyResult}`);
                        return frequencyResult;
                    case 'session':
                        const sessionKey = `modal_${modal.id}_shown`;
                        const sessionShown = sessionStorage.getItem(sessionKey);
                        frequencyResult = !sessionShown;
                        console.debug(`- Session frequency: sessionKey=${sessionKey}, sessionShown=${sessionShown}, result=${frequencyResult}`);
                        return frequencyResult;
                }

                frequencyResult = modalData.count < modal.max_shows;
                console.debug(`- Default frequency: count=${modalData.count}, max=${modal.max_shows}, result=${frequencyResult}`);
                return frequencyResult;
            }

            getWeekKey() {
                const now = new Date();
                const yearStart = new Date(now.getFullYear(), 0, 1);
                const weekNumber = Math.ceil((((now - yearStart) / 86400000) + yearStart.getDay() + 1) / 7);
                return `${now.getFullYear()}-W${weekNumber}`;
            }

            showModal(modal) {
                if (this.currentModal) return; // Don't show if another modal is open

                // Check if mobile device
                const isMobile = window.innerWidth <= 768;
                const isSmallMobile = window.innerWidth <= 480;
                
                // Adjust modal content for mobile
                let modalClasses = 'dynamic-modal';
                if (isMobile) modalClasses += ' mobile-modal';
                if (isSmallMobile) modalClasses += ' small-mobile-modal';

                // Create modal HTML
                const modalHtml = `
                    <div class="${modalClasses}" id="modal-${modal.id}">
                        <div class="modal-content-wrapper">
                            <div class="modal-header-custom">
                                <button class="modal-close-btn" onclick="window.modalSystem.closeModal()">
                                    <i class="fas fa-times"></i>
                                </button>
                                ${modal.title ? `<h3 class="modal-title-custom">${modal.title}</h3>` : ''}
                                ${modal.subtitle ? `<p class="modal-subtitle-custom">${modal.subtitle}</p>` : ''}
                            </div>
                            <div class="modal-body-custom">
                                ${modal.heading ? `<h4>${modal.heading}</h4>` : ''}
                                ${modal.description ? `<div>${modal.description}</div>` : ''}
                            </div>
                            <div class="modal-footer-custom">
                                <button class="btn btn-primary" onclick="window.modalSystem.closeModal()">
                                    ${isMobile ? 'OK' : 'Got it!'}
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                // Add to DOM
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modalElement = document.getElementById(`modal-${modal.id}`);
                this.currentModal = { element: modalElement, data: modal };

                // Prevent body scroll on mobile when modal is open
                if (isMobile) {
                    document.body.style.overflow = 'hidden';
                }

                // Show with delay
                setTimeout(() => {
                    modalElement.classList.add('show');
                }, modal.delay_seconds * 1000 || 0);

                // Track the show
                this.trackModalShow(modal);

                // Add event listeners
                modalElement.addEventListener('click', (e) => {
                    if (e.target === modalElement) {
                        this.closeModal();
                    }
                });
                
                // Handle escape key
                const escapeHandler = (e) => {
                    if (e.key === 'Escape') {
                        this.closeModal();
                        document.removeEventListener('keydown', escapeHandler);
                    }
                };
                document.addEventListener('keydown', escapeHandler);
            }

            closeModal() {
                if (!this.currentModal) return;

                const { element } = this.currentModal;
                element.classList.remove('show');
                
                // Restore body scroll
                document.body.style.overflow = '';
                
                setTimeout(() => {
                    element.remove();
                    this.currentModal = null;
                }, 300);
            }

            trackModalShow(modal) {
                const modalKey = `modal_${modal.id}`;
                const today = new Date().toDateString();
                const thisWeek = this.getWeekKey();

                if (!this.shownModals[modalKey]) {
                    this.shownModals[modalKey] = { count: 0, dates: [], lastShown: null };
                }

                const modalData = this.shownModals[modalKey];
                modalData.count++;
                
                if (!modalData.dates.includes(today)) {
                    modalData.dates.push(today);
                }
                
                modalData.lastShown = thisWeek;

                // Keep only last 30 days of dates
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                modalData.dates = modalData.dates.filter(date => new Date(date) > thirtyDaysAgo);

                localStorage.setItem('shownModals', JSON.stringify(this.shownModals));
                
                // Track in session for session-based modals
                if (modal.show_frequency === 'session') {
                    sessionStorage.setItem(`modal_${modal.id}_shown`, 'true');
                }

                // Send tracking data to server
                this.sendTrackingData(modal);
            }

            async sendTrackingData(modal) {
                try {
                    // Check if CSRF token exists
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.debug('No CSRF token found, skipping modal tracking');
                        return;
                    }
                    
                    const payload = {
                        modal_name: modal.modal_name || modal.id
                    };
                    
                    console.debug('Sending modal tracking request:', {
                        url: '/api/modals/record-show',
                        payload: payload,
                        modalData: modal
                    });
                    
                    // Send tracking data to the correct endpoint
                    const response = await fetch('/api/modals/record-show', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken.content
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    // Check if response is valid
                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            console.debug('Modal tracking recorded successfully for:', modal.modal_name || modal.id);
                        } else {
                            console.debug('Modal tracking failed:', result.message, result.errors || '');
                        }
                    } else {
                        const errorText = await response.text();
                        console.debug('Modal tracking HTTP error:', response.status, errorText);
                        throw new Error(`HTTP ${response.status}`);
                    }
                } catch (error) {
                    // Silently fail if tracking API is not available
                    console.debug('Modal tracking not available:', error.message);
                }
            }
        }

        // Initialize modal system when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            window.modalSystem = new DynamicModalSystem();
        });
    </script>
    
    <!-- Session Timeout Configuration -->
    @auth
    @php
        $timeoutMinutes = 30; // Default timeout
        $userSettings = \Illuminate\Support\Facades\Cache::get("user_session_settings_security_" . auth()->id(), []);
        if (isset($userSettings['session_timeout_hours'])) {
            $timeoutMinutes = $userSettings['session_timeout_hours'] * 60;
        } elseif (env('AUTO_LOGOUT_MINUTES')) {
            $timeoutMinutes = (int) env('AUTO_LOGOUT_MINUTES');
        }
    @endphp
    <meta name="session-timeout" content="{{ $timeoutMinutes }}">
    <meta name="session-warning" content="5">
    @endauth
    </style>
</head>

<body>
    <!-- Auto-Suggestion Web Install System -->
    @php
        $isNewUser = false;
        $showInstallSuggestion = false;
        $currentDomain = request()->getHost();
        $isPayPerViewsDomain = str_contains($currentDomain, 'payperviews.net') || str_contains($currentDomain, 'localhost');
        
        // Check if user is new or needs guidance
        if (auth()->check()) {
            $user = auth()->user();
            $isNewUser = $user->created_at->diffInDays(now()) <= 7; // New user within 7 days
            
            try {
                $hasDeposits = \App\Models\Deposit::where('user_id', $user->id)->where('status', 1)->exists();
                $hasInvestments = \App\Models\Invest::where('user_id', $user->id)->exists();
            } catch (\Exception $e) {
                $hasDeposits = false;
                $hasInvestments = false;
            }
            
            $profileComplete = !empty($user->firstname) && !empty($user->lastname) && !empty($user->mobile);
            
            // Show install suggestion if user is new and hasn't completed basic actions
            $showInstallSuggestion = $isNewUser && (!$hasDeposits || !$hasInvestments || !$profileComplete || !$user->email_verified_at);
            
            // Don't show if user has dismissed it before
            $showInstallSuggestion = $showInstallSuggestion && !session('install_suggestion_dismissed');
        } else {
            // Show for guests on payperviews.net domain or localhost
            $showInstallSuggestion = $isPayPerViewsDomain && !session('install_suggestion_dismissed');
        }
    @endphp

    @if($showInstallSuggestion)
    <!-- Web Install Suggestion Modal -->
    <div class="modal fade" id="webInstallSuggestionModal" tabindex="-1" aria-labelledby="webInstallSuggestionModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <i class="fe fe-zap me-2 fs-4"></i>
                        <div>
                            <h5 class="modal-title mb-0" id="webInstallSuggestionModalLabel">Welcome to {{ config('app.name', 'PayPerViews') }}!</h5>
                            <small class="opacity-75">Quick Setup & Installation Guide</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Progress Indicator -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Setup Progress</span>
                                <span class="badge bg-primary" id="setup-progress">0%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 0%" id="setup-progress-bar"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Installation Steps -->
                    <div class="row g-3">
                        @if(!auth()->check())
                        <!-- Step 1: Register Account -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-primary-transparent mb-3 mx-auto">
                                        <i class="fe fe-user-plus fs-4"></i>
                                    </div>
                                    <h6 class="mb-2">Create Account</h6>
                                    <p class="text-muted small mb-3">Start your journey with a free account</p>
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                                        <i class="fe fe-arrow-right me-1"></i>Register Now
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Login -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-success-transparent mb-3 mx-auto">
                                        <i class="fe fe-log-in fs-4"></i>
                                    </div>
                                    <h6 class="mb-2">Access Dashboard</h6>
                                    <p class="text-muted small mb-3">Login to your account</p>
                                    <a href="{{ route('login') }}" class="btn btn-success btn-sm">
                                        <i class="fe fe-arrow-right me-1"></i>Login
                                    </a>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Step 1: Complete Profile -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 setup-step" data-step="profile">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-info-transparent mb-3 mx-auto position-relative">
                                        <i class="fe fe-user fs-4"></i>
                                        @if(!empty(auth()->user()->firstname) && !empty(auth()->user()->lastname))
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="fe fe-check fs-6"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-2">Complete Profile</h6>
                                    <p class="text-muted small mb-3">Add your personal information</p>
                                    <a href="{{ route('profile.edit') }}" class="btn btn-info btn-sm">
                                        <i class="fe fe-edit me-1"></i>Update Profile
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Verify Email -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 setup-step" data-step="email">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-warning-transparent mb-3 mx-auto position-relative">
                                        <i class="fe fe-mail fs-4"></i>
                                        @if(auth()->user()->email_verified_at)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="fe fe-check fs-6"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-2">Verify Email</h6>
                                    <p class="text-muted small mb-3">Confirm your email address</p>
                                    @if(!auth()->user()->email_verified_at)
                                        <a href="{{ route('verification.notice') }}" class="btn btn-warning btn-sm">
                                            <i class="fe fe-check me-1"></i>Verify Email
                                        </a>
                                    @else
                                        <span class="badge bg-success">Verified</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: First Investment -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 setup-step" data-step="investment">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-success-transparent mb-3 mx-auto position-relative">
                                        <i class="fe fe-trending-up fs-4"></i>
                                        @php
                                            try {
                                                $hasInvestments = \App\Models\Invest::where('user_id', auth()->id())->exists();
                                            } catch (\Exception $e) {
                                                $hasInvestments = false;
                                            }
                                        @endphp
                                        @if($hasInvestments)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="fe fe-check fs-6"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-2">Make Investment</h6>
                                    <p class="text-muted small mb-3">Start earning with your first investment</p>
                                    <a href="{{ route('invest.index') }}" class="btn btn-success btn-sm">
                                        <i class="fe fe-dollar-sign me-1"></i>Invest Now
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Add Funds -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100 setup-step" data-step="deposit">
                                <div class="card-body text-center p-3">
                                    <div class="avatar avatar-lg bg-primary-transparent mb-3 mx-auto position-relative">
                                        <i class="fe fe-credit-card fs-4"></i>
                                        @php
                                            try {
                                                $hasDeposits = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 1)->exists();
                                            } catch (\Exception $e) {
                                                $hasDeposits = false;
                                            }
                                        @endphp
                                        @if($hasDeposits)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="fe fe-check fs-6"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-2">Add Funds</h6>
                                    <p class="text-muted small mb-3">Deposit money to your account</p>
                                    <a href="{{ route('deposit.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fe fe-plus me-1"></i>Deposit
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Quick Links Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3"><i class="fe fe-link me-2"></i>Quick Access</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fe fe-home me-1"></i>Dashboard
                                </a>
                                @if(!auth()->check())
                                <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fe fe-user-plus me-1"></i>Sign Up
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-info btn-sm">
                                    <i class="fe fe-log-in me-1"></i>Login
                                </a>
                                @else
                                <a href="{{ route('invest.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fe fe-trending-up me-1"></i>Investments
                                </a>
                                <a href="{{ route('deposit.index') }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fe fe-credit-card me-1"></i>Deposits
                                </a>
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fe fe-settings me-1"></i>Profile
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Domain Information -->
                    @if($isPayPerViewsDomain)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info border-0 bg-info-transparent">
                                <div class="d-flex align-items-start">
                                    <i class="fe fe-info me-2 mt-1"></i>
                                    <div>
                                        <strong>Welcome to PayPerViews.net!</strong><br>
                                        <small class="text-muted">
                                            You're accessing our premium investment platform. This domain offers advanced features, 
                                            secure transactions, and 24/7 support for all your investment needs.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dontShowAgain">
                                <label class="form-check-label text-muted small" for="dontShowAgain">
                                    Don't show this again
                                </label>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Maybe Later</button>
                            <button type="button" class="btn btn-primary" id="getStartedBtn">
                                <i class="fe fe-arrow-right me-1"></i>Get Started
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-Show Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-show modal after 2 seconds if not dismissed
        setTimeout(function() {
            if (!localStorage.getItem('install_suggestion_dismissed')) {
                var modal = new bootstrap.Modal(document.getElementById('webInstallSuggestionModal'));
                modal.show();
            }
        }, 2000);

        // Calculate and update progress
        function updateSetupProgress() {
            @if(auth()->check())
            let completedSteps = 0;
            let totalSteps = 4;

            // Check profile completion
            @if(!empty(auth()->user()->firstname) && !empty(auth()->user()->lastname))
            completedSteps++;
            @endif

            // Check email verification
            @if(auth()->user()->email_verified_at)
            completedSteps++;
            @endif

            // Check investment
            @php
                try {
                    $hasInvestments = \App\Models\Invest::where('user_id', auth()->id())->exists();
                } catch (\Exception $e) {
                    $hasInvestments = false;
                }
            @endphp
            @if($hasInvestments)
            completedSteps++;
            @endif

            // Check deposit
            @php
                try {
                    $hasDeposits = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 1)->exists();
                } catch (\Exception $e) {
                    $hasDeposits = false;
                }
            @endphp
            @if($hasDeposits)
            completedSteps++;
            @endif

            let progress = Math.round((completedSteps / totalSteps) * 100);
            document.getElementById('setup-progress').textContent = progress + '%';
            document.getElementById('setup-progress-bar').style.width = progress + '%';

            // Auto-dismiss if 100% complete
            if (progress >= 100) {
                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('webInstallSuggestionModal'));
                    if (modal) {
                        modal.hide();
                    }
                    localStorage.setItem('install_suggestion_dismissed', 'true');
                }, 3000);
            }
            @endif
        }

        // Handle don't show again
        document.getElementById('dontShowAgain').addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('install_suggestion_dismissed', 'true');
            } else {
                localStorage.removeItem('install_suggestion_dismissed');
            }
        });

        // Handle get started button
        document.getElementById('getStartedBtn').addEventListener('click', function() {
            @if(!auth()->check())
                window.location.href = '{{ route("register") }}';
            @else
                // Navigate to first incomplete step
                @if(empty(auth()->user()->firstname) || empty(auth()->user()->lastname))
                    window.location.href = '{{ route("profile.edit") }}';
                @elseif(!auth()->user()->email_verified_at)
                    window.location.href = '{{ route("verification.notice") }}';
                @else
                    @php
                        try {
                            $hasInvestments = \App\Models\Invest::where('user_id', auth()->id())->exists();
                            $hasDeposits = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 1)->exists();
                        } catch (\Exception $e) {
                            $hasInvestments = false;
                            $hasDeposits = false;
                        }
                    @endphp
                    @if(!$hasInvestments)
                        window.location.href = '{{ route("invest.index") }}';
                    @elseif(!$hasDeposits)
                        window.location.href = '{{ route("deposit.index") }}';
                    @else
                        window.location.href = '{{ route("user.dashboard") }}';
                    @endif
                @endif
            @endif
        });

        // Update progress on page load
        updateSetupProgress();

        // Handle modal close with session storage
        document.getElementById('webInstallSuggestionModal').addEventListener('hidden.bs.modal', function() {
            if (document.getElementById('dontShowAgain').checked) {
                // Make AJAX call to server to set session
                @if(auth()->check())
                    var url = '{{ route("user.dismiss-install-suggestion") }}';
                @else
                    var url = '{{ route("guest.dismiss-install-suggestion") }}';
                @endif
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(function(error) {
                    console.log('Note: Session storage not available');
                });
            }
        });
    });
    </script>
    @endif
    <!-- End Auto-Suggestion Web Install System -->
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
                                        $unreadNotificationsCount = \App\Models\UserNotification::where('user_id', auth()->id())
                                                                        ->where('read', false)
                                                                        ->count();
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
                            <div>
                                <img src="{{ Auth::user()->avatar_url }}" alt="img" class="rounded-3 avatar avatar-md">
                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <!-- User Info Header -->
                            <li class="dropdown-header-section">
                                <div class="p-3 text-center border-bottom bg-gradient-primary">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="{{ Auth::user()->avatar_url }}" alt="Profile" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="text-start">
                                            <h6 class="mb-0 text-white fw-semibold">{{ Auth::user()->username }}</h6>
                                            <small class="text-white-50">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                    <div class="user-balance-info">
                                        <span class="badge bg-white text-primary" data-realtime-update="profile-balance">
                                            Balance: ${{ getAmount(auth()->user()->deposit_wallet+auth()->user()->interest_wallet) }}
                                            <span class="realtime-loading d-none">
                                                <i class="fe fe-loader spin"></i>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </li>

                            <!-- Mobile Quick Actions - Shown only on mobile -->
                            <li class="d-md-none">
                                <div class="mobile-quick-actions">
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fe fe-home"></i>
                                        <small>Dashboard</small>
                                    </a>
                                    <a href="{{ route('deposit.index') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fe fe-plus-circle"></i>
                                        <small>Deposit</small>
                                    </a>
                                    <a href="{{ route('user.withdraw') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fe fe-minus-circle"></i>
                                        <small>Withdraw</small>
                                    </a>
                                    <a href="{{ route('profile.index') }}" class="btn btn-outline-info btn-sm">
                                        <i class="fe fe-user"></i>
                                        <small>Profile</small>
                                    </a>
                                </div>
                            </li>

                            <!-- Essential Links Only - Reduced for mobile -->
                            <li class="dropdown-section">
                                <h6 class="dropdown-header text-muted">
                                    <i class="fe fe-zap me-1"></i>Quick Access
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.dashboard') }}">
                                    <i class="fe fe-home me-2 text-primary"></i>
                                    <span>Dashboard</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.index') }}">
                                    <i class="fe fe-user me-2 text-info"></i>
                                    <span>My Profile</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.notifications.index') }}">
                                    <i class="fe fe-bell me-2 text-warning"></i>
                                    <span>Notifications</span>
                                    @auth
                                        @php
                                            $unreadNotificationsCount = \App\Models\UserNotification::where('user_id', auth()->id())->where('read', false)->count();
                                        @endphp
                                        @if($unreadNotificationsCount > 0)
                                            <span class="badge bg-warning ms-auto">{{$unreadNotificationsCount}}</span>
                                        @endif
                                    @endauth
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.support.index') }}">
                                    <i class="fe fe-headphones me-2 text-orange"></i>
                                    <span>Support</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                            </li>

                            <!-- Financial Section - Collapsed on mobile -->
                            <li class="dropdown-section d-none d-md-block">
                                <h6 class="dropdown-header text-muted">
                                    <i class="fe fe-dollar-sign me-1"></i>Financial
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('deposit.index') }}">
                                    <i class="fe fe-plus-circle me-2 text-success"></i>
                                    <span>Make Deposit</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.withdraw') }}">
                                    <i class="fe fe-minus-circle me-2 text-danger"></i>
                                    <span>Withdraw Funds</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('invest.index') }}">
                                    <i class="fe fe-trending-up me-2 text-primary"></i>
                                    <span>Investment Plans</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                            </li>

                            <!-- Settings Section - Collapsed on mobile -->
                            <li class="dropdown-section d-none d-md-block">
                                <h6 class="dropdown-header text-muted">
                                    <i class="fe fe-settings me-1"></i>Account Settings
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                    <i class="fe fe-settings me-2 text-info"></i>
                                    <span>Account Settings</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changepasswordnmodal">
                                    <i class="fe fe-lock me-2 text-warning"></i>
                                    <span>Change Password</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                            </li>

                            @auth
                            <!-- Logout Section - Always visible and prominent -->
                            <li class="dropdown-section logout-section">
                                <div class="border-top pt-2">
                                    <!-- Primary logout using simple route (no CSRF, no middleware issues) -->
                                    <a href="javascript:void(0);" 
                                       class="dropdown-item d-flex align-items-center justify-content-center text-danger" 
                                       onclick="performLogout()">
                                        <i class="fe fe-power me-2"></i>
                                        <span class="fw-semibold">Sign Out</span>
                                    </a>
                                    
                                    <!-- Hidden logout form with proper CSRF -->
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            @endauth
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
                            <span class="logo-text">
                                <span class="logo-primary">Earn</span><span class="logo-secondary">Hub</span> <span class="logo-accent">Pro</span>
                            </span> 
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

    <!--Enhanced Change Password Modal -->
    <div class="modal fade" id="changepasswordnmodal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center" id="changePasswordModalLabel">
                        <i class="fe fe-lock me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="changePasswordForm" method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="modal-body">
                        <!-- Alert Container -->
                        <div id="passwordAlert" class="alert d-none" role="alert"></div>
                        
                        <!-- Current Password -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fe fe-shield me-1 text-warning"></i>Current Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Enter your current password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fe fe-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="current_password_error"></div>
                        </div>
                        
                        <!-- New Password -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fe fe-key me-1 text-primary"></i>New Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="Enter your new password"
                                       required
                                       minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fe fe-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="new_password_error"></div>
                            
                            <!-- Password Strength Indicator -->
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" id="password_strength_bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="password_strength_text">Password strength</small>
                            </div>
                            
                            <!-- Password Requirements -->
                            <div class="password-requirements mt-2">
                                <small class="text-muted">Password must contain:</small>
                                <ul class="list-unstyled mb-0" style="font-size: 0.75rem;">
                                    <li id="req-length" class="text-muted"><i class="fe fe-x-circle me-1"></i>At least 8 characters</li>
                                    <li id="req-uppercase" class="text-muted"><i class="fe fe-x-circle me-1"></i>One uppercase letter</li>
                                    <li id="req-lowercase" class="text-muted"><i class="fe fe-x-circle me-1"></i>One lowercase letter</li>
                                    <li id="req-number" class="text-muted"><i class="fe fe-x-circle me-1"></i>One number</li>
                                    <li id="req-special" class="text-muted"><i class="fe fe-x-circle me-1"></i>One special character</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Confirm New Password -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fe fe-check-circle me-1 text-success"></i>Confirm New Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       placeholder="Confirm your new password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fe fe-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="new_password_confirmation_error"></div>
                            <div id="password_match_indicator" class="mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fe fe-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                            <i class="fe fe-save me-1"></i>Update Password
                            <span class="spinner-border spinner-border-sm ms-2 d-none" id="passwordSpinner" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Enhanced Change Password Modal -->

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
    {{-- <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script> --}}
    {{-- Node waves disabled to improve loading performance --}}

    <!-- Sticky JS -->
    <script src="{{asset('assets/js/sticky.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/js/simplebar.js')}}"></script>

    <!-- Color Picker JS -->
    {{-- <script src="{{asset('assets/libs/@simonwep/pickr/pickr.es5.min.js')}}"></script> --}}
    {{-- Color picker disabled to improve loading performance --}} 

    <!-- Custom-Switcher JS -->
    <script src="{{asset('assets/js/custom-switcher.min.js')}}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    
    <!-- SweetAlert2 Test Script (Debug Mode Only) -->
    <!-- Custom JS -->
    <script src="{{asset('assets/js/custom.js')}}"></script>
    
    <!-- Auto Session Timeout for authenticated users -->
    @auth
    <script src="{{asset('js/auto-session-timeout.js')}}"></script>
    @endauth

    @stack('script')

    <script>
    // COMPREHENSIVE LOGOUT SYSTEM WITH COMPLETE SESSION DESTRUCTION
    function performLogout() {
        console.log('Starting complete logout with session destruction...');
        
        // Step 1: Clear all client-side storage
        try {
            localStorage.clear();
            sessionStorage.clear();
            console.log('Client storage cleared');
        } catch(e) {
            console.warn('Could not clear local storage:', e);
        }
        
        // Step 2: Clear all cookies for this domain
        try {
            document.cookie.split(";").forEach(function(c) { 
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
            });
            console.log('Cookies cleared');
        } catch(e) {
            console.warn('Could not clear cookies:', e);
        }
        
        // Step 3: Perform server-side logout with complete session destruction
        performCompleteServerLogout();
    }
    
    // Complete server logout with session destruction
    function performCompleteServerLogout() {
        // Show loading indicator
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Logging out...',
                text: 'Destroying session and clearing all data...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Use fetch API for better control over logout process
        fetch("{{ route('logout') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                complete_logout: true,
                destroy_session: true,
                timestamp: Math.floor(Date.now() / 1000)
            })
        })
        .then(response => {
            console.log('Logout response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Logout successful:', data);
            
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            // Force complete page reload to clear any remaining state
            window.location.replace(data.redirect_url || "{{ route('login') }}?logged_out=1&t=" + Math.floor(Date.now() / 1000));
        })
        .catch(error => {
            console.error('Logout error, using fallback:', error);
            
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            // Fallback to standard logout
            performLogoutFallback();
        });
    }
    
    // Secure logout with session validation (legacy method)
    function performSecureLogout() {
        // Add timestamp and user validation to prevent cross-user access
        const logoutUrl = "{{ route('logout') }}" + 
                         "?user_verify={{ auth()->check() ? auth()->id() : 'guest' }}" +
                         "&session_token=" + generateSessionToken() +
                         "&complete_destroy=1" +
                         "&t=" + Math.floor(Date.now() / 1000);
        
        window.location.href = logoutUrl;
    }
    
    // Generate session token for logout validation
    function generateSessionToken() {
        try {
            const timestamp = Math.floor(Date.now() / 1000);
            return 'token_' + timestamp + '_' + Math.random().toString(36).substring(7);
        } catch(e) {
            return 'fallback_' + Math.random().toString(36).substring(7);
        }
    }
    
    // Enhanced fallback logout function with complete cleanup
    function performLogoutFallback() {
        console.log('Using fallback logout with complete session destruction...');
        
        // Clear storage first
        try {
            localStorage.clear();
            sessionStorage.clear();
            
            // Clear all cookies
            document.cookie.split(";").forEach(function(c) { 
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
            });
        } catch(e) {
            console.warn('Storage/cookie cleanup failed:', e);
        }
        
        // Create form for POST logout with complete destruction flag
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('logout') }}";
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        // Add complete destruction flag
        const destroyInput = document.createElement('input');
        destroyInput.type = 'hidden';
        destroyInput.name = 'complete_destroy';
        destroyInput.value = '1';
        form.appendChild(destroyInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Emergency logout function
    function performEmergencyLogout() {
        // Clear everything locally
        try {
            localStorage.clear();
            sessionStorage.clear();
            
            // Clear all cookies for this domain
            document.cookie.split(";").forEach(function(c) { 
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
            });
        } catch(e) {}
        
        // Emergency: Direct redirect to login with cache busting
        window.location.href = "{{ route('login') }}?emergency_logout=1&user_check={{ auth()->check() ? auth()->id() : 'none' }}&t=" + Math.floor(Date.now() / 1000);
    }
    
    // Session security validator - COMPLETELY DISABLED to fix 419 CSRF errors
    // This function was interfering with the login process
    function validateSessionSecurity() {
        // DISABLED: This function was causing 419 CSRF errors during login
        // Original function checked for cross-user access but interfered with login flow
        console.log('Session security validation disabled - no action taken');
        return true; // Always return true to prevent any interference
    }
    
    // Enhanced error handler for logout and session issues - TEMPORARILY DISABLED
    // This was potentially interfering with login process causing 419 errors
    /*
    window.addEventListener('error', function(e) {
        if (e.message && (e.message.includes('419') || e.message.includes('Unauthorized'))) {
            console.warn('CSRF or authentication error detected, performing emergency logout');
            performEmergencyLogout();
        }
    });
    */
    
    // Comprehensive session monitoring
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Desktop layout loaded - session security validation disabled');
        
        // TEMPORARILY DISABLED: validateSessionSecurity() to fix 419 CSRF errors
        // validateSessionSecurity();
        
        // Monitor for session changes via storage events (but don't validate immediately)
        window.addEventListener('storage', function(e) {
            if (e.key === 'current_user_id' && e.newValue !== e.oldValue) {
                console.warn('User session change detected in another tab', {
                    oldValue: e.oldValue,
                    newValue: e.newValue
                });
                // TEMPORARILY DISABLED: validateSessionSecurity();
            }
        });
        
        // Session validation disabled to prevent interference with login process
        // This was causing 419 CSRF errors during login
    });

    // Desktop logout confirmation with SweetAlert (enhanced)
    function confirmDesktopLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Sign Out?',
                text: 'Are you sure you want to sign out of your account?',
                icon: 'question',
                imageUrl: '{{asset("assets/images/users/9.jpg")}}',
                imageWidth: 80,
                imageHeight: 80,
                imageAlt: 'Profile Image',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fe fe-log-out me-1"></i>Yes, Sign Out',
                cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
                backdrop: true,
                allowOutsideClick: false,
                customClass: {
                    popup: 'desktop-logout-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    performLogout();
                }
            });
        } else {
            // Fallback for no SweetAlert
            if (confirm('Are you sure you want to logout?')) {
                performLogout();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Set current year
        document.getElementById('year').textContent = new Date().getFullYear();
        
        // Header enhancements
        initHeaderEnhancements();
        
        // Initialize theme toggle
        initThemeToggle();
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

    // Password Change Modal JavaScript - Single Clean Implementation
    let passwordChangeInitialized = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('changepasswordnmodal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                if (!passwordChangeInitialized) {
                    initializePasswordChange();
                    passwordChangeInitialized = true;
                }
            });
        }
    });
    
    function initializePasswordChange() {
        const form = document.getElementById('changePasswordForm');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const passwordSpinner = document.getElementById('passwordSpinner');
        const currentPasswordInput = document.getElementById('current_password');
        
        if (!form || !newPasswordInput || !confirmPasswordInput || !currentPasswordInput) {
            return;
        }
        
        // Password strength checker
        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordRequirements(this.value);
        });
        
        // Password match checker
        confirmPasswordInput.addEventListener('input', function() {
            checkPasswordMatch();
        });
        
        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validatePasswordForm()) {
                return;
            }
            
            submitPasswordChange();
        });
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password_strength_bar');
            const strengthText = document.getElementById('password_strength_text');
            
            if (!strengthBar || !strengthText) return;
            
            let strength = 0;
            let strengthLabel = '';
            let strengthClass = '';
            
            if (password.length >= 8) strength += 20;
            if (/[a-z]/.test(password)) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            if (strength < 40) {
                strengthLabel = 'Weak';
                strengthClass = 'bg-danger';
            } else if (strength < 80) {
                strengthLabel = 'Medium';
                strengthClass = 'bg-warning';
            } else {
                strengthLabel = 'Strong';
                strengthClass = 'bg-success';
            }
            
            strengthBar.style.width = strength + '%';
            strengthBar.className = 'progress-bar ' + strengthClass;
            strengthText.textContent = 'Password strength: ' + strengthLabel;
        }
        
        function checkPasswordRequirements(password) {
            const requirements = [
                { id: 'req-length', test: password.length >= 8 },
                { id: 'req-uppercase', test: /[A-Z]/.test(password) },
                { id: 'req-lowercase', test: /[a-z]/.test(password) },
                { id: 'req-number', test: /[0-9]/.test(password) },
                { id: 'req-special', test: /[^A-Za-z0-9]/.test(password) }
            ];
            
            requirements.forEach(req => {
                const element = document.getElementById(req.id);
                if (!element) return;
                
                const icon = element.querySelector('i');
                if (!icon) return;
                
                if (req.test) {
                    element.className = 'text-success';
                    icon.className = 'fe fe-check-circle me-1';
                } else {
                    element.className = 'text-muted';
                    icon.className = 'fe fe-x-circle me-1';
                }
            });
        }
        
        function checkPasswordMatch() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const indicator = document.getElementById('password_match_indicator');
            
            if (!indicator) return;
            
            if (confirmPassword === '') {
                indicator.innerHTML = '';
                return;
            }
            
            if (newPassword === confirmPassword) {
                indicator.innerHTML = '<small class="text-success"><i class="fe fe-check-circle me-1"></i>Passwords match</small>';
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            } else {
                indicator.innerHTML = '<small class="text-danger"><i class="fe fe-x-circle me-1"></i>Passwords do not match</small>';
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
            }
        }
        
        function validatePasswordForm() {
            let isValid = true;
            clearPasswordErrors();
            
            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Check if current password is provided
            if (!currentPassword) {
                showPasswordError('current_password_error', 'Current password is required');
                isValid = false;
            }
            
            // Check password length
            if (newPassword.length < 8) {
                showPasswordError('new_password_error', 'Password must be at least 8 characters long');
                isValid = false;
            }
            
            // Check if passwords match
            if (newPassword !== confirmPassword) {
                showPasswordError('new_password_confirmation_error', 'Passwords do not match');
                isValid = false;
            }
            
            return isValid;
        }
        
        function submitPasswordChange() {
            if (!changePasswordBtn || !passwordSpinner) return;
            
            changePasswordBtn.disabled = true;
            passwordSpinner.classList.remove('d-none');
            
            // Get form data but handle the PUT method properly
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'PUT');
            formData.append('current_password', currentPasswordInput.value);
            formData.append('password', newPasswordInput.value);
            formData.append('password_confirmation', confirmPasswordInput.value);
            
            fetch(form.action, {
                method: 'POST', // Laravel expects POST with _method field for PUT
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                    // Don't set Content-Type when using FormData - let browser set it
                }
            })
            .then(response => {
                if (!response.ok) {
                    // Try to get error details
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Password updated successfully!',
                            icon: 'success',
                            timer: 3000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            form.reset();
                            clearPasswordErrors();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('changepasswordnmodal'));
                            if (modal) modal.hide();
                        });
                    } else {
                        alert(data.message || 'Password updated successfully!');
                        form.reset();
                        clearPasswordErrors();
                        const modal = bootstrap.Modal.getInstance(document.getElementById('changepasswordnmodal'));
                        if (modal) modal.hide();
                    }
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorMessage = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
                            showPasswordError(key + '_error', errorMessage);
                        });
                    } else {
                        const errorMessage = data.message || 'An error occurred. Please try again.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert(errorMessage);
                        }
                    }
                }
            })
            .catch(error => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('An error occurred. Please try again.');
                }
            })
            .finally(() => {
                changePasswordBtn.disabled = false;
                passwordSpinner.classList.add('d-none');
            });
        }
        
        function showPasswordError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                const inputElement = document.getElementById(elementId.replace('_error', ''));
                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                }
            }
        }
        
        function clearPasswordErrors() {
            const errorElements = document.querySelectorAll('#changePasswordForm .invalid-feedback');
            errorElements.forEach(element => {
                element.textContent = '';
                element.style.display = 'none';
            });
            
            const inputs = document.querySelectorAll('#changePasswordForm .form-control');
            inputs.forEach(input => {
                input.classList.remove('is-invalid', 'is-valid');
            });
            
            const passwordAlert = document.getElementById('passwordAlert');
            if (passwordAlert) {
                passwordAlert.classList.add('d-none');
            }
            
            const indicator = document.getElementById('password_match_indicator');
            if (indicator) {
                indicator.innerHTML = '';
            }
        }
    }
    
    // Toggle password visibility function
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '_icon');
        
        if (input && icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fe fe-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'fe fe-eye';
            }
        }
    }

    // Real-time notification badge update system - Using unified function from mobile layout
    @auth
    // Note: The unified updateMobileNotificationBadge function in mobile_layout.blade.php
    // now handles both mobile and desktop notification badges automatically.
    // This comment serves as documentation that desktop notifications are handled
    // by the mobile layout's unified function.
    @endauth

    // ========================================
    // REAL-TIME BALANCE UPDATE SYSTEM
    // ========================================
    
    // Real-time balance update configuration
    let balanceUpdateInterval;
    let isUpdatingBalances = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        initializeRealTimeUpdates();
    });
    
    function initializeRealTimeUpdates() {
        // Start real-time balance updates every 30 seconds
        startBalanceUpdates();
        
        // Update balances when window gains focus
        window.addEventListener('focus', function() {
            updateAllBalances();
        });
        
        // Update balances after successful transactions
        window.addEventListener('transactionComplete', function() {
            updateAllBalances();
        });
    }
    
    function startBalanceUpdates() {
        // Clear existing interval
        if (balanceUpdateInterval) {
            clearInterval(balanceUpdateInterval);
        }
        
        // Update immediately on start
        updateAllBalances();
        
        // Set interval for periodic updates (every 30 seconds)
        balanceUpdateInterval = setInterval(function() {
            updateAllBalances();
        }, 30000);
    }
    
    function stopBalanceUpdates() {
        if (balanceUpdateInterval) {
            clearInterval(balanceUpdateInterval);
            balanceUpdateInterval = null;
        }
    }
    
    async function updateAllBalances() {
        if (isUpdatingBalances) {
            return; // Prevent multiple simultaneous updates
        }
        
        isUpdatingBalances = true;
        
        try {
            // Show loading indicators
            showBalanceLoadingIndicators();
            
            // Fetch fresh balance data
            const response = await fetch('/api/user/balance', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                updateBalanceElements(data.balances);
            }
            
        } catch (error) {
            // Show error indicator briefly
            showBalanceError();
        } finally {
            hideBalanceLoadingIndicators();
            isUpdatingBalances = false;
        }
    }
    
    function updateBalanceElements(balances) {
        // Update all elements with real-time update attributes
        const elementsToUpdate = {
            'sidebar-total-balance': balances.total_balance,
            'sidebar-deposit': balances.deposit_wallet,
            'sidebar-interest': balances.interest_wallet,
            'profile-balance': balances.total_balance,
            'navbar-balance': balances.total_balance,
            'dashboard-total-balance': balances.total_balance,
            'dashboard-deposit-balance': balances.deposit_wallet,
            'dashboard-interest-balance': balances.interest_wallet
        };
        
        Object.keys(elementsToUpdate).forEach(key => {
            const elements = document.querySelectorAll(`[data-realtime-update="${key}"]`);
            
            elements.forEach(element => {
                const newValue = elementsToUpdate[key];
                if (newValue !== undefined) {
                    // Format the balance value
                    const formattedValue = key.includes('balance') ? `$${newValue}` : newValue;
                    
                    // Animate the change
                    animateBalanceChange(element, formattedValue);
                }
            });
        });
    }
    
    function animateBalanceChange(element, newValue) {
        const currentValue = element.textContent.trim();
        
        // Only animate if value has actually changed
        if (currentValue !== newValue) {
            // Add update animation class
            element.classList.add('balance-updating');
            
            // Update the value
            setTimeout(() => {
                element.textContent = newValue;
                element.classList.remove('balance-updating');
                element.classList.add('balance-updated');
                
                // Remove the updated class after animation
                setTimeout(() => {
                    element.classList.remove('balance-updated');
                }, 1000);
            }, 150);
        }
    }
    
    function showBalanceLoadingIndicators() {
        const loadingElements = document.querySelectorAll('[data-realtime-update] .realtime-loading');
        loadingElements.forEach(element => {
            element.classList.remove('d-none');
        });
        
        // Add subtle loading animation to balance elements
        const balanceElements = document.querySelectorAll('[data-realtime-update]');
        balanceElements.forEach(element => {
            element.classList.add('balance-loading');
        });
    }
    
    function hideBalanceLoadingIndicators() {
        const loadingElements = document.querySelectorAll('[data-realtime-update] .realtime-loading');
        loadingElements.forEach(element => {
            element.classList.add('d-none');
        });
        
        // Remove loading animation
        const balanceElements = document.querySelectorAll('[data-realtime-update]');
        balanceElements.forEach(element => {
            element.classList.remove('balance-loading');
        });
    }
    
    function showBalanceError() {
        const balanceElements = document.querySelectorAll('[data-realtime-update]');
        balanceElements.forEach(element => {
            element.classList.add('balance-error');
            setTimeout(() => {
                element.classList.remove('balance-error');
            }, 2000);
        });
    }
    
    // Manual balance refresh function (can be called from buttons)
    window.refreshBalances = function() {
        updateAllBalances();
    };
    
    // Force balance update function (for after transactions)
    window.forceBalanceUpdate = function() {
        setTimeout(() => {
            updateAllBalances();
        }, 1000); // Wait 1 second for server to process
    };
    
    // Stop updates when user logs out or navigates away
    window.addEventListener('beforeunload', function() {
        stopBalanceUpdates();
    });
    
    // ========================================
    // CSS ANIMATIONS FOR BALANCE UPDATES
    // ========================================
    
    // Add CSS for balance update animations
    const balanceUpdateStyles = `
        <style>
        .balance-loading {
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .balance-updating {
            transform: scale(1.05);
            transition: transform 0.15s ease;
        }
        
        .balance-updated {
            background: rgba(40, 167, 69, 0.1);
            border-radius: 4px;
            animation: balanceGlow 1s ease;
        }
        
        .balance-error {
            background: rgba(220, 53, 69, 0.1);
            border-radius: 4px;
            animation: balanceErrorGlow 2s ease;
        }
        
        @keyframes balanceGlow {
            0% { 
                background: rgba(40, 167, 69, 0.3);
                transform: scale(1.02);
            }
            50% { 
                background: rgba(40, 167, 69, 0.2);
            }
            100% { 
                background: rgba(40, 167, 69, 0.1);
                transform: scale(1);
            }
        }
        
        @keyframes balanceErrorGlow {
            0%, 100% { 
                background: rgba(220, 53, 69, 0.1);
            }
            50% { 
                background: rgba(220, 53, 69, 0.2);
            }
        }
        
        .realtime-loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
    `;
    
    // Inject the styles
    document.head.insertAdjacentHTML('beforeend', balanceUpdateStyles);
    </script>

    <!-- Post-logout session manager -->
    <script src="{{ asset('js/post-logout-manager.js') }}"></script>

</body>
</html>
