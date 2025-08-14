

<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('top_title') </title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
	<meta name="keywords" content="html template, dashboard template, admin template, dashboard, admin, html css templates, bootstrap template, hr dashboard, dashboard html css, employee dashboard, admin dashboard bootstrap, admin panel bootstrap, bootstrap admin, dashboard css, project dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Cache Management Meta Tags --> 
    <meta name="cache-version" content="{{ config('app.cache_version', time()) }}">
    <meta name="app-version" content="{{ config('app.version', '1.0.0') }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Favicon -->
    @php
        $faviconUrl = asset('assets/images/brand-logos/favicon.ico');
        if (isset($settings) && $settings && $settings->favicon) {
            $faviconUrl = getMediaUrl($settings->favicon, 'favicon');
        }
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    
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
    
    <!-- Custom Timer Styles -->
    <style>
        /* Timer container animation */
        .timer-container {
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.6);
            animation: pulse-shadow 2s infinite alternate;
        }
        
        /* Pulse animation for the timer container */
        @keyframes pulse-shadow {
            0% { box-shadow: 0 0 15px rgba(220, 53, 69, 0.6); }
            100% { box-shadow: 0 0 20px rgba(255, 193, 7, 0.8); }
        }
        
        /* Clock icon animation */
        .timer-icon {
            animation: pulse 1s infinite;
        }
        
        /* Timer digits styling */
        .timer-digits {
            font-size: 2rem !important;
            font-weight: 700 !important;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
            letter-spacing: 2px;
        }
        
        /* Progress bar animation */
        #timer-progress {
            transition: width 1s linear;
        }
        
        /* Mobile responsiveness for timer */
        @media (max-width: 767.98px) {
            .timer-title {
                font-size: 14px;
                width: 100%;
                margin-bottom: 5px;
                text-align: center;
            }
            
            .timer-digits {
                font-size: 1.75rem !important;
            }
            
            #countdown-timer .badge {
                margin: 0 auto;
                display: block;
                width: fit-content;
            }
        }
        
        @media (max-width: 575.98px) {
            .timer-digits {
                font-size: 1.5rem !important;
            }
        }
        
        /* Mobile specific styles for notification icon */
        @media (max-width: 768px) {
            .header-element.notifications-dropdown {
                display: flex !important;
                align-items: center;
                margin-right: 10px !important;
            }
            
            .notifications-dropdown .header-link {
                padding: 8px !important;
                border-radius: 50%;
                background: rgba(108, 117, 125, 0.1);
                transition: all 0.3s ease;
                min-width: 40px;
                min-height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .notifications-dropdown .header-link:hover {
                background: rgba(108, 117, 125, 0.2);
                transform: scale(1.05);
            }
            
            .notifications-dropdown .header-link-icon {
                font-size: 18px !important;
                color: #495057 !important;
            }
            
            .header-element.notifications-dropdown .position-absolute {
                top: -5px !important;
                right: -5px !important;
                font-size: 10px !important;
                min-width: 18px !important;
                height: 18px !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                transform: none !important;
            }
            
            /* Ensure header content right is properly spaced on mobile */
            .header-content-right {
                display: flex !important;
                align-items: center;
                gap: 5px;
            }
            
            .header-element.header-theme-mode {
                margin-right: 5px !important;
            }
        }
        
        /* Enhanced Sidebar User Area Styles */
        .user-profile-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            margin: 0 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .user-profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(106, 90, 205, 0.1), rgba(30, 144, 255, 0.1));
            z-index: -1;
            opacity: 0.8;
        }
        
        .user-profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        /* User Avatar Section */
        .user-avatar-section .profile-avatar {
            position: relative;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 0 30px rgba(106, 90, 205, 0.4);
            transition: all 0.3s ease;
        }
        
        .user-avatar-section .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(106, 90, 205, 0.6);
        }
        
        .user-profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .online-indicator {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 16px;
            height: 16px;
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);
            animation: pulse-online 2s infinite;
        }
        
        @keyframes pulse-online {
            0% { box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4); }
            50% { box-shadow: 0 2px 15px rgba(76, 175, 80, 0.8); }
            100% { box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4); }
        }
        
        .user-name {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
            margin-bottom: 5px;
        }
        
        .user-status {
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            display: inline-block;
        }
        
        /* Balance Cards */
        .balance-cards-container {
            margin-top: 20px;
        }
        
        .balance-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .balance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(30, 144, 255, 0.1), rgba(106, 90, 205, 0.1));
            z-index: -1;
        }
        
        .balance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.3);
        }
        
        .balance-card-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .balance-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #FFD700, #FFA500);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .balance-icon i {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: bold;
        }
        
        .balance-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .balance-label {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 2px;
        }
        
        .balance-amount {
            color: #FFD700;
            font-size: 1.2rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }
        
        /* Quick Stats Row */
        .quick-stats-row {
            display: flex;
            gap: 10px;
        }
        
        .stat-item {
            flex: 1;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 12px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .stat-icon.deposit {
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
        }
        
        .stat-icon.interest {
            background: linear-gradient(45deg, #FF6B6B, #FF8E8E);
        }
        
        .stat-icon i {
            font-size: 14px;
            color: white;
        }
        
        .stat-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .stat-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.7rem;
            font-weight: 500;
            margin-bottom: 2px;
        }
        
        .stat-value {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        /* Quick Actions */
        .user-quick-actions {
            margin-top: 20px;
        }
        
        .action-buttons-row {
            display: flex;
            gap: 8px;
            justify-content: space-between;
        }
        
        .action-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 8px;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            color: white;
            text-decoration: none;
        }
        
        .action-btn.deposit-btn:hover {
            border-color: rgba(76, 175, 80, 0.5);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }
        
        .action-btn.withdraw-btn:hover {
            border-color: rgba(255, 107, 107, 0.5);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
        }
        
        .action-btn.profile-btn:hover {
            border-color: rgba(106, 90, 205, 0.5);
            box-shadow: 0 6px 20px rgba(106, 90, 205, 0.3);
        }
        
        .action-btn i {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .action-btn span {
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.9;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .user-profile-card {
                margin: 0 5px;
                padding: 15px;
            }
            
            .quick-stats-row {
                flex-direction: column;
                gap: 8px;
            }
            
            .stat-item {
                flex-direction: row;
                text-align: left;
            }
            
            .action-buttons-row {
                flex-wrap: wrap;
            }
            
            .action-btn {
                min-width: 80px;
            }
        }
        
        /* Custom Logo Styles */
        .header-logo {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 15px !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
        }
        
        .header-logo:hover {
            transform: scale(1.05);
            text-decoration: none !important;
        }
        
        .logo-icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #6A5ACD, #1E90FF);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(106, 90, 205, 0.3);
            transition: all 0.3s ease;
        }
        
        .logo-icon-container:hover {
            box-shadow: 0 6px 25px rgba(106, 90, 205, 0.5);
            transform: rotate(5deg);
        }
        
        .logo-icon {
            font-size: 24px !important;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .logo-text-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .logo-text {
            font-size: 1.4rem !important;
            font-weight: 700 !important;
            background: linear-gradient(135deg, #6A5ACD, #1E90FF, #00BFFF);
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            text-shadow: none !important;
            letter-spacing: 0.5px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .logo-icon-container {
                width: 35px;
                height: 35px;
            }
            
            .logo-icon {
                font-size: 18px !important;
            }
            
            .logo-text {
                font-size: 1.1rem !important;
            }
        }
        
        /* Dark theme adjustments */
        .dark-layout .logo-text {
            background: linear-gradient(135deg, #8A7DFF, #4FC3F7, #26C6DA);
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        /* Animation for logo */
        @keyframes logoGlow {
            0% { 
                box-shadow: 0 4px 15px rgba(106, 90, 205, 0.3);
            }
            50% { 
                box-shadow: 0 6px 25px rgba(106, 90, 205, 0.6);
            }
            100% { 
                box-shadow: 0 4px 15px rgba(106, 90, 205, 0.3);
            }
        }
        
        .logo-icon-container {
            animation: logoGlow 3s ease-in-out infinite;
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
        
        /* Mobile responsive adjustments */
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

        /* Enhanced Password Modal Styles */
        .password-strength .progress {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .password-strength .progress-bar {
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .password-requirements ul li {
            transition: all 0.3s ease;
            padding: 2px 0;
        }
        
        .password-requirements ul li.text-success {
            transform: scale(1.02);
        }
        
        #password_match_indicator {
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .form-control.is-valid {
            border-color: #198754;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        
        .modal-header.bg-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #6f42c1) !important;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Enhanced Search Bar Styles */
        .search-element {
            position: relative;
            max-width: 400px;
            width: 100%;
        }
        
        .header-search {
            width: 100% !important;
            border-radius: 25px !important;
            padding: 12px 55px 12px 20px !important;
            border: 2px solid transparent !important;
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            color: #fff !important;
            transition: all 0.3s ease !important;
            font-size: 14px !important;
        }
        
        .header-search::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .header-search:focus {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            outline: none !important;
        }
        
        .search-btn {
            position: absolute !important;
            right: 8px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            border: none !important;
            background: linear-gradient(45deg, #007bff, #6f42c1) !important;
            color: white !important;
            border-radius: 50% !important;
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            z-index: 10 !important;
            cursor: pointer !important;
        }
        
        .search-btn:hover {
            background: linear-gradient(45deg, #0056b3, #5a2d91) !important;
            transform: translateY(-50%) scale(1.1) !important;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3) !important;
        }
        
        .search-btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2) !important;
        }

        /* Form styling for search */
        #headerSearchForm {
            margin: 0 !important;
            width: 100% !important;
        }
        
        .form-inline .search-element {
            flex: 1 !important;
            width: auto !important;
        }
        
        /* Ensure proper layout on different screen sizes */
        @media (min-width: 992px) {
            .search-element {
                min-width: 300px;
            }
        }
        
        @media (max-width: 1200px) {
            .search-element {
                max-width: 250px;
            }
            
            .header-search {
                font-size: 13px !important;
            }
        }
        
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            margin-top: 5px;
        }
        
        .search-suggestion-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        
        .search-suggestion-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .search-suggestion-item:last-child {
            border-bottom: none;
        }
        
        .search-suggestion-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .search-category {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .search-category:hover {
            transform: scale(1.05);
        }
        
        /* Mobile Search Enhancements */
        .main-header-dropdown .dropdown-item {
            border: none !important;
        }
        
        #mobileSearchInput {
            border-radius: 20px !important;
            border: 2px solid #e9ecef !important;
            padding: 10px 15px !important;
        }
        
        #mobileSearchInput:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.1) !important;
        }
        
        .input-group .btn-primary {
            border-radius: 0 20px 20px 0 !important;
            background: linear-gradient(45deg, #007bff, #6f42c1) !important;
            border: none !important;
        }
        
        /* Search Results Highlight */
        .search-highlight {
            background: linear-gradient(120deg, #a8edea 0%, #fed6e3 100%);
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        /* Loading Animation */
        .search-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .search-loading::after {
            content: '';
            width: 20px;
            height: 20px;
            margin-left: 10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Dark theme support */
        .dark-theme .header-search {
            background: rgba(0, 0, 0, 0.2) !important;
        }
        
        .dark-theme .header-search:focus {
            background: rgba(0, 0, 0, 0.3) !important;
        }
        
        .dark-theme .search-suggestions {
            background: #2a2d3a;
            color: #fff;
        }
        
        .dark-theme .search-suggestion-item:hover {
            background: #3a3d4a;
        }
    </style>


<!-- Full Calendar CSS -->
<link rel="stylesheet" href="{{asset('assets/libs/fullcalendar/main.min.css')}}">

<!-- Auto-Suggestion Install System Styles -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

#webInstallSuggestionModal .modal-content {
    border-radius: 15px;
    overflow: hidden;
}

#webInstallSuggestionModal .modal-header {
    border-radius: 15px 15px 0 0;
}

#webInstallSuggestionModal .setup-step .card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

#webInstallSuggestionModal .setup-step .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

#webInstallSuggestionModal .avatar {
    transition: all 0.3s ease;
}

#webInstallSuggestionModal .setup-step:hover .avatar {
    transform: scale(1.1);
}

#webInstallSuggestionModal .progress {
    border-radius: 10px;
    overflow: hidden;
}

#webInstallSuggestionModal .progress-bar {
    transition: width 0.8s ease;
}

#webInstallSuggestionModal .btn {
    border-radius: 8px;
    font-weight: 500;
}

#webInstallSuggestionModal .alert {
    border-radius: 10px;
    border-left: 4px solid #0dcaf0;
}

.install-suggestion-backdrop {
    backdrop-filter: blur(5px);
}

/* Animation for modal entrance */
@keyframes slideInUp {
    from {
        transform: translate(-50%, 100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%);
        opacity: 1;
    }
}

#webInstallSuggestionModal.show .modal-dialog {
    animation: slideInUp 0.5s ease;
}

/* Success checkmark animation */
@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.badge.bg-success {
    animation: checkmark 0.6s ease;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    #webInstallSuggestionModal .modal-dialog {
        margin: 10px;
    }
    
    #webInstallSuggestionModal .modal-body {
        padding: 20px 15px;
    }
    
    #webInstallSuggestionModal .col-md-6 {
        margin-bottom: 15px;
    }
    
    #webInstallSuggestionModal .d-flex.flex-wrap.gap-2 {
        flex-direction: column;
    }
    
    #webInstallSuggestionModal .btn-sm {
        width: 100%;
        margin-bottom: 8px;
    }
}

/* Quick access buttons styling */
#webInstallSuggestionModal .btn-outline-primary,
#webInstallSuggestionModal .btn-outline-success,
#webInstallSuggestionModal .btn-outline-info,
#webInstallSuggestionModal .btn-outline-warning,
#webInstallSuggestionModal .btn-outline-secondary {
    border-width: 2px;
    transition: all 0.3s ease;
}

#webInstallSuggestionModal .btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

#webInstallSuggestionModal .btn-outline-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
}

/* Loading animation for progress bar */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
</style>

<!-- Dynamic Modal Styles -->
<link href="{{asset('css/dynamic-modal.css')}}" rel="stylesheet">

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
                $hasDeposits = \App\Models\Deposit::where('user_id', $user->id)->where('status', 1)->orWhere('status', 2)->exists();
                $hasInvestments = \App\Models\Invest::where('user_id', $user->id)->exists();
            } catch (\Exception $e) {
                $hasDeposits = false;
                $hasInvestments = false;
            }
            
            $profileComplete = !empty($user->firstname) && !empty($user->lastname) && !empty($user->mobile);
            
            // Check if modal should be shown (only once per day for up to 7 days)
            $lastShownDate = session('install_suggestion_last_shown_date');
            $todayDate = now()->format('Y-m-d');
            $hasShownToday = $lastShownDate === $todayDate;
            
            // Show install suggestion if:
            // 1. User is new (within 7 days)
            // 2. User hasn't completed basic actions
            // 3. Modal hasn't been shown today
            // 4. User hasn't permanently dismissed it
            $showInstallSuggestion = $isNewUser && 
                                   (!$hasDeposits || !$hasInvestments || !$profileComplete || !$user->email_verified_at) &&
                                   !$hasShownToday &&
                                   !session('install_suggestion_dismissed_permanently');
        } else {
            // Show for guests on payperviews.net domain or localhost (but not more than once per day)
            $lastShownDate = session('install_suggestion_last_shown_date');
            $todayDate = now()->format('Y-m-d');
            $hasShownToday = $lastShownDate === $todayDate;
            
            $showInstallSuggestion = $isPayPerViewsDomain && 
                                   !$hasShownToday && 
                                   !session('install_suggestion_dismissed_permanently');
        }
    @endphp

    @if($showInstallSuggestion)
    <!-- Web Install Suggestion Modal -->
    <div class="modal fade" id="webInstallSuggestionModal" tabindex="-1" aria-labelledby="webInstallSuggestionModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                                                $hasDeposits = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 1)->orWhere('status', 2)->exists();
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
                        <div class="d-flex flex-column">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="dontShowToday">
                                <label class="form-check-label text-muted small" for="dontShowToday">
                                    Don't show again today
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dontShowAgainPermanently">
                                <label class="form-check-label text-muted small" for="dontShowAgainPermanently">
                                    Don't show this again (permanently)
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
        // Check if mobile device
        const isMobile = window.innerWidth <= 991.98;
        
        // Auto-show modal after delay (longer on mobile for better UX)
        const showDelay = isMobile ? 3000 : 2000; // 3 seconds on mobile, 2 on desktop
        
        setTimeout(function() {
            if (!localStorage.getItem('install_suggestion_dismissed')) {
                var modal = new bootstrap.Modal(document.getElementById('webInstallSuggestionModal'), {
                    backdrop: 'static',
                    keyboard: true
                });
                
                // Ensure modal is properly positioned on mobile
                if (isMobile) {
                    // Add mobile-specific class
                    document.getElementById('webInstallSuggestionModal').classList.add('mobile-welcome-modal');
                    
                    // Temporarily hide mobile navigation to prevent conflicts
                    const mobileNav = document.querySelector('.mobile-navbar-container');
                    if (mobileNav) {
                        mobileNav.style.zIndex = '9998';
                    }
                }
                
                modal.show();
                
                // Re-enable mobile navigation after modal is hidden
                document.getElementById('webInstallSuggestionModal').addEventListener('hidden.bs.modal', function() {
                    if (isMobile) {
                        const mobileNav = document.querySelector('.mobile-navbar-container');
                        if (mobileNav) {
                            mobileNav.style.zIndex = '9999';
                        }
                        document.getElementById('webInstallSuggestionModal').classList.remove('mobile-welcome-modal');
                    }
                });
                
                // Fallback: If modal doesn't show properly on mobile, show a notification bar
                if (isMobile) {
                    setTimeout(function() {
                        const modalElement = document.getElementById('webInstallSuggestionModal');
                        if (!modalElement.classList.contains('show')) {
                            showWelcomeBanner();
                        }
                    }, 1000);
                }
            }
        }, showDelay);

        // Fallback welcome banner for mobile if modal fails
        function showWelcomeBanner() {
            const banner = document.createElement('div');
            banner.id = 'welcome-banner';
            banner.className = 'welcome-banner-mobile';
            banner.innerHTML = `
                <div class="welcome-banner-content">
                    <div class="welcome-banner-text">
                        <strong>Welcome to PayPerViews!</strong>
                        <br><small>Quick Setup & Installation Guide</small>
                    </div>
                    <div class="welcome-banner-actions">
                        <button class="btn btn-sm btn-primary me-2" onclick="showWelcomeModal()">
                            <i class="fe fe-zap me-1"></i>Get Started
                        </button>
                        <button class="btn btn-sm btn-outline-light" onclick="dismissWelcomeBanner()">
                            <i class="fe fe-x"></i>
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(banner);
            
            // Show banner with animation
            setTimeout(() => banner.classList.add('show'), 100);
        }

        // Function to show welcome modal manually
        window.showWelcomeModal = function() {
            const banner = document.getElementById('welcome-banner');
            if (banner) banner.remove();
            
            const modal = new bootstrap.Modal(document.getElementById('webInstallSuggestionModal'), {
                backdrop: true,
                keyboard: true
            });
            modal.show();
        }

        // Function to dismiss welcome banner
        window.dismissWelcomeBanner = function() {
            const banner = document.getElementById('welcome-banner');
            if (banner) {
                banner.classList.remove('show');
                setTimeout(() => banner.remove(), 300);
            }
            localStorage.setItem('install_suggestion_dismissed', 'true');
        }

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
                    $hasDeposits = \App\Models\Deposit::where('user_id', auth()->id())->where('status', 1)->orWhere('status', 2)->exists();
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

        // Handle don't show today checkbox
        document.getElementById('dontShowToday').addEventListener('change', function() {
            if (this.checked) {
                // Uncheck permanent dismissal if daily is checked
                document.getElementById('dontShowAgainPermanently').checked = false;
            }
        });

        // Handle don't show permanently checkbox
        document.getElementById('dontShowAgainPermanently').addEventListener('change', function() {
            if (this.checked) {
                // Uncheck daily dismissal if permanent is checked
                document.getElementById('dontShowToday').checked = false;
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
            const dontShowToday = document.getElementById('dontShowToday').checked;
            const dontShowPermanently = document.getElementById('dontShowAgainPermanently').checked;
            
            if (dontShowToday || dontShowPermanently) {
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
                    },
                    body: JSON.stringify({
                        dismiss_type: dontShowPermanently ? 'permanent' : 'daily',
                        date: new Date().toISOString().split('T')[0] // Current date in Y-m-d format
                    })
                }).catch(function(error) {
                    console.log('Note: Session storage not available');
                });
            } else {
                // If neither checkbox is checked, just record that modal was shown today
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
                    },
                    body: JSON.stringify({
                        dismiss_type: 'shown_today',
                        date: new Date().toISOString().split('T')[0]
                    })
                }).catch(function(error) {
                    console.log('Note: Session storage not available');
                });
            }
        });
    });
    </script>
    @endif
    <!-- End Auto-Suggestion Web Install System -->

    <!-- Start Switcher -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Switcher</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="border-bottom border-block-end-dashed">
                <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                    <button class="nav-link active" id="switcher-home-tab" data-bs-toggle="tab" data-bs-target="#switcher-home"
                        type="button" role="tab" aria-controls="switcher-home" aria-selected="true">Theme Styles</button>
                    <button class="nav-link" id="switcher-profile-tab" data-bs-toggle="tab" data-bs-target="#switcher-profile"
                        type="button" role="tab" aria-controls="switcher-profile" aria-selected="false">Theme Colors</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel" aria-labelledby="switcher-home-tab"
                    tabindex="0">
                    <div class="">
                        <p class="switcher-style-head">Theme Color Mode:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-light-theme">
                                        Light
                                    </label>
                                    <input class="form-check-input" type="radio" name="theme-style" id="switcher-light-theme"
                                        checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-dark-theme">
                                        Dark
                                    </label>
                                    <input class="form-check-input" type="radio" name="theme-style" id="switcher-dark-theme">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Directions:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-ltr">
                                        LTR
                                    </label>
                                    <input class="form-check-input" type="radio" name="direction" id="switcher-ltr" checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-rtl">
                                        RTL
                                    </label>
                                    <input class="form-check-input" type="radio" name="direction" id="switcher-rtl">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Navigation Styles:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-vertical">
                                        Vertical
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-style" id="switcher-vertical"
                                        checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-horizontal">
                                        Horizontal
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-style"
                                        id="switcher-horizontal">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="navigation-menu-styles">
                        <p class="switcher-style-head">Vertical & Horizontal Menu Styles:</p>
                        <div class="row switcher-style gx-0 pb-2 gy-2">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-menu-click">
                                        Menu Click
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                        id="switcher-menu-click">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-menu-hover">
                                        Menu Hover
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                        id="switcher-menu-hover">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-icon-click">
                                        Icon Click
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                        id="switcher-icon-click">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-icon-hover">
                                        Icon Hover
                                    </label>
                                    <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                        id="switcher-icon-hover">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sidemenu-layout-styles">
                        <p class="switcher-style-head">Sidemenu Layout Styles:</p>
                        <div class="row switcher-style gx-0 pb-2 gy-2">
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-default-menu">
                                        Default Menu
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-default-menu" checked>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-closed-menu">
                                        Closed Menu
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-closed-menu">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-icontext-menu">
                                        Icon Text
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-icontext-menu">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-icon-overlay">
                                        Icon Overlay
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-icon-overlay">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-detached">
                                        Detached
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-detached">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-double-menu">
                                        Double Menu
                                    </label>
                                    <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                        id="switcher-double-menu">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Page Styles:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-regular">
                                        Regular
                                    </label>
                                    <input class="form-check-input" type="radio" name="page-styles" id="switcher-regular"
                                        checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-classic">
                                        Classic
                                    </label>
                                    <input class="form-check-input" type="radio" name="page-styles" id="switcher-classic">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-modern">
                                        Modern
                                    </label>
                                    <input class="form-check-input" type="radio" name="page-styles" id="switcher-modern">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Layout Width Styles:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-full-width">
                                        Full Width
                                    </label>
                                    <input class="form-check-input" type="radio" name="layout-width" id="switcher-full-width"
                                        checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-boxed">
                                        Boxed
                                    </label>
                                    <input class="form-check-input" type="radio" name="layout-width" id="switcher-boxed">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Menu Positions:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-menu-fixed">
                                        Fixed
                                    </label>
                                    <input class="form-check-input" type="radio" name="menu-positions" id="switcher-menu-fixed"
                                        checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-menu-scroll">
                                        Scrollable
                                    </label>
                                    <input class="form-check-input" type="radio" name="menu-positions" id="switcher-menu-scroll">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Header Positions:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-header-fixed">
                                        Fixed
                                    </label>
                                    <input class="form-check-input" type="radio" name="header-positions"
                                        id="switcher-header-fixed" checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-header-scroll">
                                        Scrollable
                                    </label>
                                    <input class="form-check-input" type="radio" name="header-positions"
                                        id="switcher-header-scroll">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <p class="switcher-style-head">Loader:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-loader-enable">
                                        Enable
                                    </label>
                                    <input class="form-check-input" type="radio" name="page-loader"
                                        id="switcher-loader-enable" checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-loader-disable">
                                        Disable
                                    </label>
                                    <input class="form-check-input" type="radio" name="page-loader"
                                        id="switcher-loader-disable" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade border-0" id="switcher-profile" role="tabpanel" aria-labelledby="switcher-profile-tab" tabindex="0">
                    <div>
                        <div class="theme-colors">
                            <p class="switcher-style-head">Menu Colors:</p>
                            <div class="d-flex switcher-style pb-2">
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Light Menu" type="radio" name="menu-colors"
                                        id="switcher-menu-light">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Dark Menu" type="radio" name="menu-colors"
                                        id="switcher-menu-dark" checked>
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Color Menu" type="radio" name="menu-colors"
                                        id="switcher-menu-primary">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Gradient Menu" type="radio" name="menu-colors"
                                        id="switcher-menu-gradient">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-transparent"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Transparent Menu"
                                        type="radio" name="menu-colors" id="switcher-menu-transparent">
                                </div>
                            </div>
                            <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Menu dynamically change from below Theme Primary color picker</div>
                        </div>
                        <div class="theme-colors">
                            <p class="switcher-style-head">Header Colors:</p>
                            <div class="d-flex switcher-style pb-2">
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Light Header" type="radio" name="header-colors"
                                        id="switcher-header-light" checked>
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Dark Header" type="radio" name="header-colors"
                                        id="switcher-header-dark">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Color Header" type="radio" name="header-colors"
                                        id="switcher-header-primary">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Gradient Header" type="radio" name="header-colors"
                                        id="switcher-header-gradient">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Transparent Header" type="radio" name="header-colors"
                                        id="switcher-header-transparent">
                                </div>
                            </div>
                            <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Header dynamically change from below Theme Primary color picker</div>
                        </div>
                        <div class="theme-colors">
                            <p class="switcher-style-head">Theme Primary:</p>
                            <div class="d-flex flex-wrap align-items-center switcher-style">
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary-1" type="radio"
                                        name="theme-primary" id="switcher-primary">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary-2" type="radio"
                                        name="theme-primary" id="switcher-primary1">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary-3" type="radio" name="theme-primary"
                                        id="switcher-primary2">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary-4" type="radio" name="theme-primary"
                                        id="switcher-primary3">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-primary-5" type="radio" name="theme-primary"
                                        id="switcher-primary4">
                                </div>
                                <div class="form-check switch-select ps-0 mt-1 color-primary-light">
                                    <div class="theme-container-primary"></div>
                                    <div class="pickr-container-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="theme-colors">
                            <p class="switcher-style-head">Theme Background:</p>
                            <div class="d-flex flex-wrap align-items-center switcher-style">
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-bg-1" type="radio"
                                        name="theme-background" id="switcher-background">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-bg-2" type="radio"
                                        name="theme-background" id="switcher-background1">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-bg-3" type="radio" name="theme-background"
                                        id="switcher-background2">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-bg-4" type="radio"
                                        name="theme-background" id="switcher-background3">
                                </div>
                                <div class="form-check switch-select me-3">
                                    <input class="form-check-input color-input color-bg-5" type="radio"
                                        name="theme-background" id="switcher-background4">
                                </div>
                                <div class="form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent">
                                    <div class="theme-container-background"></div>
                                    <div class="pickr-container-background"></div>
                                </div>
                            </div>
                        </div>
                        <div class="menu-image mb-3">
                            <p class="switcher-style-head">Menu With Background Image:</p>
                            <div class="d-flex flex-wrap align-items-center switcher-style">
                                <div class="form-check switch-select m-2">
                                    <input class="form-check-input bgimage-input bg-img1" type="radio"
                                        name="theme-background" id="switcher-bg-img">
                                </div>
                                <div class="form-check switch-select m-2">
                                    <input class="form-check-input bgimage-input bg-img2" type="radio"
                                        name="theme-background" id="switcher-bg-img1">
                                </div>
                                <div class="form-check switch-select m-2">
                                    <input class="form-check-input bgimage-input bg-img3" type="radio" name="theme-background"
                                        id="switcher-bg-img2">
                                </div>
                                <div class="form-check switch-select m-2">
                                    <input class="form-check-input bgimage-input bg-img4" type="radio"
                                        name="theme-background" id="switcher-bg-img3">
                                </div>
                                <div class="form-check switch-select m-2">
                                    <input class="form-check-input bgimage-input bg-img5" type="radio"
                                        name="theme-background" id="switcher-bg-img4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-block justify-content-between canvas-footer flex-wrap">
                    <a href="javascript:void(0);" id="reset-all" class="btn btn-danger d-grid my-1 mx-0">Reset</a> 
                </div>
            </div>
        </div>
    </div>
    <!-- End Switcher -->


    <!-- Loader -->
    <div id="loader" >
        <img src="{{asset('assets/images/media/loader.svg')}}" alt="">
    </div>
    <!-- Loader -->

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
                                @php
                                    // Simple logo URL determination
                                    $logoUrl = asset('assets/images/brand-logos/desktop-logo.png'); // Default fallback
                                    
                                    try {
                                        $appSettings = isset($settings) ? $settings : getSettings();
                                        if ($appSettings && !empty($appSettings->logo)) {
                                            $dynamicLogoUrl = getMediaUrl($appSettings->logo, 'logo');
                                            if (!empty($dynamicLogoUrl)) {
                                                $logoUrl = $dynamicLogoUrl;
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Use default fallback on any error
                                    }
                                @endphp
                                <img src="{{ $logoUrl }}" alt="logo" class="desktop-logo">
                                <img src="{{ $logoUrl }}" alt="logo" class="toggle-logo">
                                <img src="{{ $logoUrl }}" alt="logo" class="desktop-dark">
                                <img src="{{ $logoUrl }}" alt="logo" class="toggle-dark">
                                <img src="{{ $logoUrl }}" alt="logo" class="desktop-white">
                                <img src="{{ $logoUrl }}" alt="logo" class="toggle-white">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);">
                            <span>
                        
                            </span>
                        </a>
                        <!-- End::header-link -->
                        <!-- Start::header-search -->
                        <div class="mt-0">
                            <form class="form-inline d-none d-lg-block" id="headerSearchForm">
                                <div class="search-element">
                                    <input type="search" 
                                           class="form-control header-search" 
                                           placeholder="Search videos, users, transactions..." 
                                           aria-label="Search" 
                                           tabindex="1"
                                           id="headerSearchInput"
                                           autocomplete="off">
                                    <button class="btn search-btn" type="submit">
                                        <i class="fe fe-search"></i>
                                    </button>
                                    <div class="search-suggestions" id="searchSuggestions"></div>
                                </div>
                            </form>
                        </div>
                        <!-- End::header-search -->
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">

                    <!-- Mobile search removed as requested -->
                    <!-- End::header-element -->                    <!-- Start::header-element -->
                    <div class="header-element header-theme-mode">
                        <!-- Start::header-link|layout-setting -->
                        <a href="javascript:void(0);" class="header-link layout-setting">
                            <span class="light-layout lh-1">
                                <!-- Start::header-link-icon -->
                            <i class="fe fe-moon header-link-icon"></i>
                                <!-- End::header-link-icon -->
                            </span>
                            <span class="dark-layout lh-1">
                                <!-- Start::header-link-icon -->
                            <i class="fe fe-sun header-link-icon"></i>
                                <!-- End::header-link-icon -->
                            </span>
                        </a>
                        <!-- End::header-link|layout-setting -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <!-- End::header-element -->  

                    <!-- Start::header-element -->
                    <div class="header-element messages-dropdown">
                        <!-- Start::header-link|messages -->
                        <a href="{{ route('user.messages') }}" class="header-link position-relative" title="Messages">
                            <i class="fe fe-mail header-link-icon"></i>
                            @auth
                                @php
                                    $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', false)->count();
                                @endphp
                                @if($unreadMessagesCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            @endauth
                        </a>
                        <!-- End::header-link|messages -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element notifications-dropdown" style="display: none;">
                        <!-- Start::header-link|notification-rights -->
                        <a href="javascript:void(0);" class="header-link position-relative" data-bs-toggle="offcanvas" data-bs-target="#notification-sidebar-canvas" id="messageDropdown">
                            <i class="fe fe-bell header-link-icon"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="header-notification-badge" data-realtime-update="notification-badge">
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </a>
                        <!-- End::header-link|notification-rights -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element --> 
                    <div class="header-element main-header-profile">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle mx-0 w-100" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div>
                                @auth
                                    <img src="{{ Auth::user()->avatar_url }}" alt="img" class="rounded-3 avatar avatar-md">
                                @else
                                    <img src="{{ asset('assets/images/users/16.jpg') }}" alt="img" class="rounded-3 avatar avatar-md">
                                @endauth
                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <!-- User Info Header -->
                            <li class="dropdown-header-section">
                                <div class="p-3 text-center border-bottom bg-gradient-primary">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        @auth
                                            <img src="{{ Auth::user()->avatar_url }}" alt="Profile" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('assets/images/users/16.jpg') }}" alt="Profile" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endauth
                                        <div class="text-start">
                                            <h6 class="mb-0 text-white fw-semibold">@auth{{ Auth::user()->username }}@endauth</h6>
                                            <small class="text-white-50">@auth{{ Auth::user()->email }}@endauth</small>
                                        </div>
                                    </div>
                                    <div class="user-balance-info">
                                        <span class="badge bg-white text-primary" data-realtime-update="profile-balance">
                                            Balance: $@auth{{ getAmount(auth()->user()->deposit_wallet+auth()->user()->interest_wallet) }}@endauth
                                            <span class="realtime-loading d-none">
                                                <i class="fe fe-loader spin"></i>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </li>

                            <!-- Navigation Links -->
                            <li class="dropdown-section">
                                <h6 class="dropdown-header text-muted">
                                    <i class="fe fe-user me-1"></i>Account
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.index') }}">
                                    <i class="fe fe-user me-2 text-primary"></i>
                                    <span>My Profile</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
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

                            <!-- Financial Section -->
                            <li class="dropdown-section">
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
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('deposit.history') }}">
                                    <i class="fe fe-list me-2 text-info"></i>
                                    <span>Transaction History</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('invest.index') }}">
                                    <i class="fe fe-trending-up me-2 text-primary"></i>
                                    <span>Investment Plans</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                            </li>

                            <!-- Activity Section -->
                            <li class="dropdown-section">
                                <h6 class="dropdown-header text-muted">
                                    <i class="fe fe-activity me-1"></i>Activity
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.video-views.index') }}">
                                    <i class="fe fe-play-circle me-2 text-purple"></i>
                                    <span>Video Views</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.refferral-history') }}">
                                    <i class="fe fe-users me-2 text-success"></i>
                                    <span>Referral Earnings</span>
                                    <span class="badge bg-success-transparent ms-auto">Active</span>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('user.support.index') }}">
                                    <i class="fe fe-headphones me-2 text-orange"></i>
                                    <span>Support Center</span>
                                    <i class="fe fe-chevron-right ms-auto text-muted"></i>
                                </a>
                            </li>

                            <!-- Quick Actions -->
                            <li class="dropdown-section">
                                <div class="p-3 border-top">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fe fe-bell me-1"></i>
                                                <small>Notifications</small>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-success btn-sm w-100">
                                                <i class="fe fe-home me-1"></i>
                                                <small>Dashboard</small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Logout Section -->
                            <li class="dropdown-section">
                                <div class="border-top pt-2">
                                    <!-- Primary logout using simple route (no CSRF, no middleware issues) -->
                                    <a href="javascript:void(0);" 
                                       class="dropdown-item d-flex align-items-center text-danger" 
                                       onclick="performLogout()">
                                        <i class="fe fe-power me-2"></i>
                                        <span class="fw-semibold">Sign Out</span>
                                    </a>
                                    
                                    <!-- Hidden fallback logout forms -->
                                    <form id="simple-logout-form" action="{{ route('simple.logout') }}" method="GET" style="display: none;">
                                    </form>
                                    
                                    <form id="fallback-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>  
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->

        </header>
        <!-- /app-header -->
        
        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">
            
            <!-- Start::horizontal-main (hidden by default, shown when in horizontal mode) -->
            <div class="horizontal-main" id="horizontal-main" style="display: none;">
                <nav class="horizontal-nav">
                    <div class="container-fluid">
                        <ul class="navbar-nav">
                            <!-- Horizontal navigation items -->
                        </ul>
                    </div>
                </nav>
            </div>
            <!-- End::horizontal-main -->

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
                <!-- User Top Show Start -->
                    {{-- <x-userTopShow /> --}}
                <!-- User Top Show End -->
            </div>
        </div>
        <!-- End::app-content -->

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 shadow-none">
            <!-- Desktop Footer -->
            <div class="container d-none d-md-block text-center">
                <span class="text-muted"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-dark fw-semibold">www.payperviews.net</a>.
                     All rights reserved
                </span>
            </div>
        </footer>
        <!-- Footer End -->

        <!-- Mobile Navigation - Enhanced with Messages & Notifications -->
        <div class="mobile-navbar-container" id="mobileNavbarContainer">
                <!-- Top Mobile Header with Profile & Notifications -->
                <div class="mobile-header-bar d-md-none">
                    <div class="mobile-header-content">
                        <!-- Profile Picture Button (Left) -->
                        <a href="javascript:void(0);" 
                           class="mobile-profile-btn"
                           onclick="openMobileModal('profile')"
                           title="Profile Menu">
                            @auth
                                <img src="{{ Auth::user()->avatar_url }}" alt="Profile" class="mobile-profile-avatar">
                            @else
                                <img src="{{ asset('assets/images/users/16.jpg') }}" alt="Profile" class="mobile-profile-avatar">
                            @endauth
                            <span class="online-dot"></span>
                        </a>

                        <!-- Center Logo/Brand -->
                        <div class="mobile-brand">
                            <a href="{{ route('user.dashboard') }}" class="mobile-brand-link">
                                <i class="fe fe-play-circle brand-icon"></i>
                                <span class="brand-text">EarnHub</span>
                            </a>
                        </div>

                        <!-- Action Icons (Right) -->
                        <div class="mobile-actions">
                            @auth
                                <!-- Messages Icon (only show if user has messages) -->
                                @php
                                    $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', false)->count();
                                    $totalMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->count();
                                @endphp
                                @if($totalMessagesCount > 0)
                                    <a href="{{ route('user.messages') }}" 
                                       class="mobile-action-btn messages-btn"
                                       title="Messages">
                                        <i class="fe fe-mail"></i>
                                        @if($unreadMessagesCount > 0)
                                            <span class="notification-badge">{{ $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount }}</span>
                                        @endif
                                    </a>
                                @endif

                                <!-- Notifications Icon (only show if user has notifications) -->
                                @php
                                    $unreadNotificationsCount = \App\Models\UserNotification::where('user_id', auth()->id())->where('read', false)->count();
                                    $totalNotificationsCount = \App\Models\UserNotification::where('user_id', auth()->id())->count();
                                @endphp
                                @if($totalNotificationsCount > 0)
                                    <a href="{{ route('user.notifications.index') }}" 
                                       class="mobile-action-btn notifications-btn"
                                       title="Notifications">
                                        <i class="fe fe-bell"></i>
                                        @if($unreadNotificationsCount > 0)
                                            <span class="notification-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                                        @endif
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation Bar -->
                <nav class="mobile-navbar" role="navigation" aria-label="Mobile Navigation">
                    <div class="mobile-nav-items">
                        <!-- Dashboard -->
                        <a href="{{ route('user.dashboard') }}" 
                           class="mobile-nav-link {{ request()->routeIs('user.dashboard') ? 'nav-active' : '' }}">
                            <span class="nav-icon-wrapper">
                                <i class="fe fe-home nav-icon"></i>
                            </span>
                            <span class="nav-text">Home</span>
                        </a>

                        <!-- Videos -->
                        <a href="{{ route('user.video-views.index') }}" 
                           class="mobile-nav-link {{ request()->routeIs('user.video-views.*') ? 'nav-active' : '' }}">
                            <span class="nav-icon-wrapper">
                                <i class="fe fe-play-circle nav-icon"></i>
                            </span>
                            <span class="nav-text">Videos</span>
                        </a>

                        <!-- Wallet/Balance -->
                        <a href="javascript:void(0);" 
                           class="mobile-nav-link wallet-link"
                           data-bs-toggle="modal" 
                           data-bs-target="#mobileWalletModal">
                            <span class="nav-icon-wrapper">
                                <i class="fe fe-credit-card nav-icon"></i>
                            </span>
                            <span class="nav-text">Wallet</span>
                            @auth
                                <span class="balance-indicator">
                                    ${{ number_format(auth()->user()->deposit_wallet + auth()->user()->interest_wallet, 0) }}
                                </span>
                            @endauth
                        </a>

                        <!-- Menu/More -->
                        <a href="javascript:void(0);" 
                           class="mobile-nav-link menu-link"
                           onclick="openMobileModal('menu')">
                            <span class="nav-icon-wrapper">
                                <i class="fe fe-menu nav-icon"></i>
                            </span>
                            <span class="nav-text">Menu</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
        <!-- End Mobile Navigation -->
        
        <!-- Enhanced Mobile Navigation Styles -->
        <style>
        /* Mobile Navigation Container */
        .mobile-navbar-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            display: none; /* Initially hidden, shown on mobile */
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
            height: 70px; /* Fixed height */
        }

        /* GENTLE HIDE FOR MOBILE NAVIGATION ON DESKTOP */
        @media (min-width: 992px) {
            /* Only hide mobile navigation container gently */
            .mobile-navbar-container,
            .mobile-header-bar,
            #mobileNavbarContainer {
                display: none !important;
            }
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

        /* Hide on desktop/PC devices explicitly - FORCE HIDE */
        @media (min-width: 992px) {
            .mobile-navbar-container,
            .mobile-header-bar,
            #mobileNavbarContainer {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                position: absolute !important;
                top: -9999px !important;
                left: -9999px !important;
                width: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
                z-index: -1 !important;
            }
            
            /* Ensure body has no mobile-specific padding on desktop */
            body {
                padding-bottom: 0 !important;
                padding-top: 0 !important;
                margin-bottom: 0 !important;
                margin-top: 0 !important;
            }
            
            /* Reset main content margin on desktop */
            .main-content {
                margin-bottom: 0 !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            
            /* Force show desktop elements */
            .app-header {
                display: block !important;
            }
            
            .app-sidebar {
                display: block !important;
            }
        }

        /* Show on mobile devices only */
        @media (max-width: 991.98px) {
            .mobile-navbar-container,
            .mobile-header-bar {
                display: block !important;
            }
            
            /* Hide desktop header on mobile */
            .app-header {
                display: none !important;
            }
            
            /* Hide desktop sidebar on mobile */
            .app-sidebar {
                display: none !important;
            }
            
            /* Fix main content positioning for mobile */
            .main-content {
                margin-left: 0 !important;
                margin-top: 70px !important; /* Top header height */
                margin-bottom: 80px !important; /* Bottom navbar height */
                padding-top: 20px !important;
                padding-bottom: 20px !important;
                min-height: calc(100vh - 150px) !important;
            }
            
            /* Ensure container has proper spacing */
            .main-content .container-fluid {
                padding-top: 10px !important;
                padding-bottom: 20px !important;
            }
            
            /* Hide desktop footer on mobile */
            .footer .container.d-none.d-md-block {
                display: none !important;
            }
            
            /* Hide any desktop-only elements */
            .d-none.d-md-block,
            .d-none.d-lg-block {
                display: none !important;
            }
        }

        /* Mobile Navbar Base Styling */
        .mobile-navbar {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            height: 80px; /* Fixed height */
        }

        /* Navigation Items Container */
        .mobile-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 12px 8px 15px 8px;
            min-height: 75px;
        }

        /* Individual Navigation Links */
        .mobile-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 6px;
            text-decoration: none;
            color: #6c757d;
            border-radius: 12px;
            min-width: 55px;
            flex: 1;
            max-width: 70px;
            transition: all 0.3s ease;
            position: relative;
        }

        /* Icon Wrapper */
        .nav-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            position: relative;
            background: rgba(108, 117, 125, 0.1);
        }

        /* Navigation Icons */
        .nav-icon {
            font-size: 20px;
            line-height: 1;
            transition: all 0.3s ease;
        }

        /* Navigation Text */
        .nav-text {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            margin-top: 2px;
            transition: all 0.3s ease;
        }

        /* Balance Indicator */
        .balance-indicator {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #28a745;
            color: white;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 4px;
            border-radius: 8px;
            min-width: 20px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
            border: 1px solid white;
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

        /* Wallet Link Special Styling */
        .mobile-nav-link.wallet-link:hover .balance-indicator {
            background: #20c997;
            transform: scale(1.1);
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
                padding: 10px 5px 12px 5px;
            }
            
            .mobile-nav-link {
                min-width: 50px;
                padding: 6px 4px;
            }
            
            .nav-icon-wrapper {
                width: 36px;
                height: 36px;
            }
            
            .nav-icon {
                font-size: 18px;
            }
            
            .nav-text {
                font-size: 10px;
            }

            .balance-indicator {
                font-size: 7px;
                padding: 1px 3px;
                min-width: 18px;
                height: 12px;
            }

            body {
                padding-top: 65px !important;
            }
        }

        @media (max-width: 480px) {
            .mobile-actions {
                gap: 12px;
            }

            .mobile-action-btn {
                width: 36px;
                height: 36px;
            }

            .mobile-action-btn i {
                font-size: 16px;
            }

            .notification-badge {
                font-size: 9px;
                min-width: 16px;
                height: 16px;
            }
        }

        /* Extra small screens */
        @media (max-width: 375px) {
            .mobile-nav-link {
                min-width: 45px;
                padding: 5px 3px;
            }
            
            .nav-icon-wrapper {
                width: 32px;
                height: 32px;
            }
            
            .nav-icon {
                font-size: 16px;
            }
            
            .nav-text {
                font-size: 9px;
            }
        }

        /* Smooth entrance animation */
        .mobile-navbar-container {
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

        /* Custom SweetAlert Styles for Logout */
        .swal-logout-popup {
            border-radius: 15px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .swal-logout-title {
            font-weight: 600 !important;
            color: #2c3e50 !important;
        }

        .swal-logout-text {
            color: #6c757d !important;
            font-size: 16px !important;
        }

        .swal-logout-actions {
            gap: 15px !important;
            margin-top: 20px !important;
        }

        /* Fix button blur and styling issues */
        .swal-confirm-btn {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3) !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
        }

        .swal-confirm-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4) !important;
        }

        .swal-cancel-btn {
            background: #6c757d !important;
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3) !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
        }

        .swal-cancel-btn:hover {
            background: #5a6268 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4) !important;
        }

        /* Ensure buttons are not blurred */
        .swal-confirm-btn,
        .swal-cancel-btn {
            filter: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            opacity: 1 !important;
        }

        /* Loading popup styles */
        .swal-loading-popup {
            border-radius: 15px !important;
        }

        /* Fix any potential focus issues */
        .swal-confirm-btn:focus,
        .swal-cancel-btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
        }

        /* Mobile Welcome Modal Fixes */
        @media (max-width: 991.98px) {
            /* Make modal fullscreen on small mobile devices */
            #webInstallSuggestionModal .modal-dialog {
                margin: 0 !important;
                max-width: 100% !important;
                height: 100vh !important;
                width: 100% !important;
            }
            
            #webInstallSuggestionModal .modal-content {
                height: 100% !important;
                border-radius: 0 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            
            #webInstallSuggestionModal .modal-body {
                flex: 1 !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding: 1rem !important;
            }
            
            #webInstallSuggestionModal .modal-header {
                flex-shrink: 0 !important;
                padding: 1rem !important;
            }
            
            #webInstallSuggestionModal .modal-footer {
                flex-shrink: 0 !important;
                padding: 1rem !important;
                margin-top: 0 !important;
            }
        }

        /* Medium mobile devices */
        @media (min-width: 576px) and (max-width: 767.98px) {
            #webInstallSuggestionModal .modal-dialog {
                margin: 1rem !important;
                max-width: calc(100% - 2rem) !important;
                height: calc(100vh - 2rem) !important;
            }
        }

        /* Ensure modal is above mobile navigation */
        #webInstallSuggestionModal {
            z-index: 99999 !important;
        }

        #webInstallSuggestionModal .modal-backdrop {
            z-index: 99998 !important;
        }

        /* Welcome modal specific mobile improvements */
        @media (max-width: 575.98px) {
            #webInstallSuggestionModal .modal-body .row.g-3 .col-md-6 {
                margin-bottom: 1rem;
            }
            
            #webInstallSuggestionModal .card-body {
                padding: 1rem !important;
            }
            
            #webInstallSuggestionModal .btn {
                font-size: 0.875rem !important;
                padding: 0.5rem 1rem !important;
            }
            
            #webInstallSuggestionModal .modal-title {
                font-size: 1.1rem !important;
            }
            
            #webInstallSuggestionModal .progress {
                height: 6px !important;
            }
        }

        /* Mobile welcome modal class */
        .mobile-welcome-modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 99999 !important;
        }

        .mobile-welcome-modal .modal-dialog {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
        }

        .mobile-welcome-modal .modal-content {
            height: 100% !important;
            border-radius: 0 !important;
            border: none !important;
        }

        /* Welcome Banner Fallback Styles */
        .welcome-banner-mobile {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 1rem;
            z-index: 99999;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .welcome-banner-mobile.show {
            transform: translateY(0);
        }

        .welcome-banner-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-banner-text {
            flex: 1;
        }

        .welcome-banner-text strong {
            font-size: 1rem;
            display: block;
            margin-bottom: 0.25rem;
        }

        .welcome-banner-text small {
            opacity: 0.9;
            font-size: 0.875rem;
        }

        .welcome-banner-actions {
            flex-shrink: 0;
            margin-left: 1rem;
        }

        @media (max-width: 575.98px) {
            .welcome-banner-content {
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
            }
            
            .welcome-banner-actions {
                margin-left: 0;
            }
            
            .welcome-banner-text strong {
                font-size: 0.95rem;
            }
            
            .welcome-banner-text small {
                font-size: 0.8rem;
            }
        }
        </style>

        <!-- Fresh Mobile Navigation JavaScript -->
        <script>
        // GENTLE DESKTOP HIDE - CSS-only solution that preserves the navbar
        (function() {
            'use strict';
            
            // Check if desktop immediately
            const isDesktop = window.innerWidth >= 992;
            console.log('IMMEDIATE CHECK - Screen width:', window.innerWidth, 'Is Desktop:', isDesktop);
            
            if (isDesktop) {
                // Add gentle CSS hide that preserves the navbar in DOM
                const gentleCSS = document.createElement('style');
                gentleCSS.id = 'gentle-mobile-hide';
                gentleCSS.innerHTML = `
                    /* Gentle hide for mobile navigation on desktop - preserves in DOM */
                    @media (min-width: 992px) {
                        .mobile-navbar-container,
                        .mobile-header-bar,
                        #mobileNavbarContainer {
                            display: none !important;
                        }
                        
                        /* Reset body styles for desktop */
                        body {
                            padding-top: 0 !important;
                            padding-bottom: 0 !important;
                            margin-top: 0 !important;
                            margin-bottom: 0 !important;
                        }
                        
                        .main-content {
                            margin-top: 0 !important;
                            margin-bottom: 0 !important;
                            padding-top: 0 !important;
                            padding-bottom: 0 !important;
                        }
                    }
                `;
                document.head.appendChild(gentleCSS);
                console.log('GENTLE CSS applied for desktop hide - navbar preserved');
                
                // Set body styles gently
                document.body.style.setProperty('padding-top', '0', 'important');
                document.body.style.setProperty('padding-bottom', '0', 'important');
            }
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Fresh mobile navigation script loaded');

            // Set current year for mobile footer
            const yearMobile = document.getElementById('year-mobile');
            if (yearMobile) {
                yearMobile.textContent = new Date().getFullYear();
            }

            // Get the fresh mobile navbar container
            const mobileNavbarContainer = document.querySelector('.mobile-navbar-container');
            const isMobile = window.innerWidth <= 991.98; // Mobile up to 991.98px
            
            console.log('Screen width:', window.innerWidth);
            console.log('Is mobile:', isMobile);
            console.log('Mobile navbar container found:', !!mobileNavbarContainer);
            
            if (mobileNavbarContainer) {
                console.log('Fresh mobile navigation found:', mobileNavbarContainer);
                
            // GENTLE HIDE ON DESKTOP - Preserve navigation in DOM
            if (!isMobile) {
                // Desktop device - gentle hide with CSS only
                mobileNavbarContainer.style.setProperty('display', 'none', 'important');
                console.log('Mobile navigation gently hidden for desktop (preserved in DOM)');
                
                // Ensure body has no mobile padding
                document.body.style.setProperty('padding-bottom', '0', 'important');
                document.body.style.setProperty('padding-top', '0', 'important');
                document.body.style.setProperty('margin-bottom', '0', 'important');
                document.body.style.setProperty('margin-top', '0', 'important');
                
                // Ensure main content has no mobile spacing
                const mainContent = document.querySelector('.main-content');
                if (mainContent) {
                    mainContent.style.setProperty('margin-top', '0', 'important');
                    mainContent.style.setProperty('margin-bottom', '0', 'important');
                    mainContent.style.setProperty('padding-top', '0', 'important');
                    mainContent.style.setProperty('padding-bottom', '0', 'important');
                }
                
                return; // Exit early for desktop
            }                // Mobile device - show navigation
                mobileNavbarContainer.style.setProperty('display', 'block', 'important');
                console.log('Mobile navigation activated for mobile device');
            } else {
                console.error('Mobile navbar container not found!');
            }

            // Check nav items for both navigation systems
            const navItems = document.querySelectorAll('.mobile-nav-link, .mobile-nav-item');
            console.log('Found ' + navItems.length + ' mobile nav links');

            navItems.forEach((item, index) => {
                console.log('Nav link ' + index + ':', item.href);
            });

            // Add click handlers for navigation items
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items of the same type
                    if (item.classList.contains('mobile-nav-link')) {
                        document.querySelectorAll('.mobile-nav-link').forEach(nav => nav.classList.remove('nav-active'));
                        this.classList.add('nav-active');
                    } else if (item.classList.contains('mobile-nav-item')) {
                        document.querySelectorAll('.mobile-nav-item').forEach(nav => nav.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });

            // Update active nav item based on current route
            function updateActiveNavItem() {
                const currentPath = window.location.pathname;
                
                // Update fresh navigation
                document.querySelectorAll('.mobile-nav-link').forEach(item => {
                    const href = item.getAttribute('href');
                    if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
                        item.classList.add('nav-active');
                    } else {
                        item.classList.remove('nav-active');
                    }
                });

                // Update alternative navigation
                document.querySelectorAll('.mobile-nav-item').forEach(item => {
                    const href = item.getAttribute('href');
                    if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            // Update on page load
            updateActiveNavItem();

            // Update on navigation (for SPA-like behavior)
            window.addEventListener('popstate', updateActiveNavItem);

            // Handle window resize
            function handleResize() {
                const isMobileNow = window.innerWidth <= 991.98; // Mobile up to 991.98px
                const mobileNavbarContainer = document.querySelector('.mobile-navbar-container');
                
                if (!isMobileNow && mobileNavbarContainer) {
                    // Desktop view - gentle hide with display none
                    mobileNavbarContainer.style.setProperty('display', 'none', 'important');
                    console.log('Resized to desktop - mobile nav gently hidden');
                    
                    // Remove mobile body padding on desktop
                    document.body.style.setProperty('padding-bottom', '0', 'important');
                    document.body.style.setProperty('padding-top', '0', 'important');
                    
                    // Apply gentle hide CSS if not already applied
                    const gentleCSS = document.getElementById('gentle-mobile-hide');
                    if (!gentleCSS) {
                        const newGentleCSS = document.createElement('style');
                        newGentleCSS.id = 'gentle-mobile-hide';
                        newGentleCSS.innerHTML = `
                            @media (min-width: 992px) {
                                .mobile-navbar-container,
                                .mobile-header-bar,
                                #mobileNavbarContainer {
                                    display: none !important;
                                }
                            }
                        `;
                        document.head.appendChild(newGentleCSS);
                    }
                } else if (isMobileNow && mobileNavbarContainer) {
                    // Mobile view - show navigation
                    mobileNavbarContainer.style.setProperty('display', 'block', 'important');
                    console.log('Switched to mobile view');
                }
            }

            window.addEventListener('resize', handleResize);

            // Add haptic feedback for mobile devices (if supported)
            if ('vibrate' in navigator && isMobile) {
                navItems.forEach(item => {
                    item.addEventListener('touchstart', function() {
                        navigator.vibrate(10); // Short vibration
                    });
                });
            }

            console.log('Fresh mobile navigation initialization complete');

            // Initialize proper mobile navigation display
            function initializeMobileNavigation() {
                const isMobileDevice = window.innerWidth <= 991.98;
                
                if (mobileNavbarContainer) {
                    if (isMobileDevice) {
                        // Only show on actual mobile devices
                        mobileNavbarContainer.style.setProperty('display', 'block', 'important');
                        console.log('Mobile navigation activated for mobile device');
                        
                        // Initialize mobile theme
                        initializeMobileTheme();
                        
                        // Add debugging info for content visibility on mobile only
                        console.log('Body bottom padding:', window.getComputedStyle(document.body).paddingBottom);
                        console.log('Viewport height:', window.innerHeight);
                        console.log('Document height:', document.body.scrollHeight);
                    } else {
                        // Hide on desktop/PC - gentle hide only
                        mobileNavbarContainer.style.setProperty('display', 'none', 'important');
                        console.log('Mobile navigation gently hidden for desktop device (preserved in DOM)');
                        
                        // Remove any mobile-specific body padding on desktop
                        document.body.style.setProperty('padding-bottom', '0', 'important');
                        document.body.style.setProperty('padding-top', '0', 'important');
                    }
                }
            }

            // Function to update mobile navigation badges dynamically
            function updateMobileNavigationBadges() {
                // Check if we have any topbar items to show
                let hasTopbarItems = false;
                
                // Check for messages
                const messagesLink = document.querySelector('.mobile-topbar-link.messages-link');
                if (messagesLink) {
                    hasTopbarItems = true;
                    console.log('Messages link found in mobile navigation');
                }

                // Check for notifications
                const notificationsLink = document.querySelector('.mobile-topbar-link.notifications-link');
                if (notificationsLink) {
                    hasTopbarItems = true;
                    console.log('Notifications link found in mobile navigation');
                }

                // Theme toggle is always available, so if no messages/notifications, only show theme
                const themeToggle = document.querySelector('.mobile-topbar-link.theme-toggle-link');
                if (themeToggle) {
                    hasTopbarItems = true;
                }

                // Show/hide topbar based on content
                const mobileTopbar = document.getElementById('mobileTopbar');
                if (mobileTopbar && hasTopbarItems) {
                    mobileTopbar.style.display = 'flex';
                    document.body.classList.add('mobile-topbar-visible');
                    console.log('Mobile topbar shown with', hasTopbarItems ? 'content' : 'theme only');
                } else if (mobileTopbar) {
                    mobileTopbar.style.display = 'none';
                    document.body.classList.remove('mobile-topbar-visible');
                    console.log('Mobile topbar hidden - no content');
                }
            }

            // Function to handle mobile topbar link clicks with feedback
            function setupMobileTopbarInteractions() {
                document.querySelectorAll('.mobile-topbar-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Add haptic feedback if supported
                        if ('vibrate' in navigator) {
                            navigator.vibrate(30);
                        }

                        // Add visual feedback
                        const iconWrapper = this.querySelector('.topbar-icon-wrapper');
                        if (iconWrapper) {
                            iconWrapper.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                iconWrapper.style.transform = '';
                            }, 150);
                        }

                        console.log('Mobile topbar link clicked:', this.className);
                    });

                    // Add touch feedback for better mobile UX
                    link.addEventListener('touchstart', function() {
                        this.style.opacity = '0.7';
                    });

                    link.addEventListener('touchend', function() {
                        this.style.opacity = '';
                    });
                });
            }
            
            // Initialize on page load
            initializeMobileNavigation();
            
            // Setup mobile topbar interactions
            setupMobileTopbarInteractions();
            
            // Update mobile navigation badges
            updateMobileNavigationBadges();
            
            // Re-initialize on window resize
            window.addEventListener('resize', initializeMobileNavigation);
        });
        </script>


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

        <!-- Enhanced Mobile Navigation Modal with 2x2 Grid -->
        <div class="modal fade" id="mobileNavModal" tabindex="-1" aria-labelledby="mobileNavModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title d-flex align-items-center" id="mobileNavModalLabel">
                            <i class="fe fe-user me-2" id="modalIcon"></i>
                            <span id="modalTitle">Profile</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-3" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Profile Modal Content -->
                        <div id="profileModalContent" class="modal-section">
                            <div class="modal-grid-container">
                                <!-- 2x2 Grid Layout -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('profile.index') }}'">
                                            <div class="modal-item-icon bg-primary-light">
                                                <i class="fe fe-user text-primary"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">My Profile</h6>
                                                <small class="text-muted">View & Edit</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="openPasswordModal()">
                                            <div class="modal-item-icon bg-warning-light">
                                                <i class="fe fe-lock text-warning"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Password</h6>
                                                <small class="text-muted">Security</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="toggleTheme()">
                                            <div class="modal-item-icon bg-info-light">
                                                <i class="fe fe-moon text-info" id="themeIcon"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Theme</h6>
                                                <small class="text-muted">Dark/Light</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.refferral-history') }}'">
                                            <div class="modal-item-icon bg-success-light">
                                                <i class="fe fe-users text-success"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">My Team</h6>
                                                <small class="text-muted">Referrals</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Extra Content Section -->
                                <div class="modal-extra-content">
                                    <h6 class="text-muted mb-3">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                            <i class="fe fe-edit-2 me-2"></i>Edit Profile
                                        </a>
                                        <a href="{{ route('user.support.index') }}" class="btn btn-outline-info btn-sm d-flex align-items-center">
                                            <i class="fe fe-headphones me-2"></i>Support Center
                                        </a>
                                        <a href="{{ route('user.messages') }}" class="btn btn-outline-success btn-sm d-flex align-items-center">
                                            <i class="fe fe-mail me-2"></i>Messages
                                        </a>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="text-muted mb-3">Account Info</h6>
                                        <div class="info-cards">
                                            <div class="info-card d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Member Since:</span>
                                                <span class="fw-bold">@auth {{ Auth::user()->created_at->format('M Y') }} @endauth</span>
                                            </div>
                                            <div class="info-card d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Status:</span>
                                                <span class="badge bg-success">Active</span>
                                            </div>
                                            <div class="info-card d-flex justify-content-between align-items-center">
                                                <span class="text-muted">Referral Code:</span>
                                                <span class="fw-bold text-primary">@auth {{ Auth::user()->username }} @endauth</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dashboard Modal Content -->
                        <div id="dashboardModalContent" class="modal-section d-none">
                            <div class="modal-grid-container">
                                <!-- 2x2 Grid Layout -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.dashboard') }}'">
                                            <div class="modal-item-icon bg-primary-light">
                                                <i class="fe fe-home text-primary"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Dashboard</h6>
                                                <small class="text-muted">Overview</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('deposit.history') }}'">
                                            <div class="modal-item-icon bg-info-light">
                                                <i class="fe fe-activity text-info"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Activity</h6>
                                                <small class="text-muted">Transactions</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.refferral-history') }}'">
                                            <div class="modal-item-icon bg-success-light">
                                                <i class="fe fe-trending-up text-success"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Earnings</h6>
                                                <small class="text-muted">Income</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.support.index') }}'">
                                            <div class="modal-item-icon bg-warning-light">
                                                <i class="fe fe-help-circle text-warning"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Support</h6>
                                                <small class="text-muted">Help</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Extra Content Section -->
                                <div class="modal-extra-content">
                                    <h6 class="text-muted mb-3">Quick Stats</h6>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="stat-card">
                                                <div class="stat-icon">
                                                    <i class="fe fe-dollar-sign text-success"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <h6 class="mb-0">@auth ${{ number_format(Auth::user()->balance, 2) }} @endauth</h6>
                                                    <small class="text-muted">Balance</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-card">
                                                <div class="stat-icon">
                                                    <i class="fe fe-users text-primary"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <h6 class="mb-0">@auth {{ Auth::user()->referrals->count() }} @endauth</h6>
                                                    <small class="text-muted">Referrals</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="performance-section">
                                        <h6 class="text-muted mb-3">Performance</h6>
                                        <div class="progress-item mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>Daily Goal</small>
                                                <small class="text-success">85%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: 85%"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>Monthly Target</small>
                                                <small class="text-primary">62%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: 62%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invest Modal Content -->
                        <div id="investModalContent" class="modal-section d-none">
                            <div class="modal-grid-container">
                                <!-- 2x2 Grid Layout -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('invest.index') }}'">
                                            <div class="modal-item-icon bg-primary-light">
                                                <i class="fe fe-trending-up text-primary"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Invest Now</h6>
                                                <small class="text-muted">Plans</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('invest.history') }}'">
                                            <div class="modal-item-icon bg-success-light">
                                                <i class="fe fe-play text-success"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Running</h6>
                                                <small class="text-muted">Active</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('invest.log') }}'">
                                            <div class="modal-item-icon bg-info-light">
                                                <i class="fe fe-list text-info"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">History</h6>
                                                <small class="text-muted">Logs</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('invest.statistics') }}'">
                                            <div class="modal-item-icon bg-warning-light">
                                                <i class="fe fe-percent text-warning"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Statistics</h6>
                                                <small class="text-muted">Analytics</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Extra Content Section -->
                                <div class="modal-extra-content">
                                    <h6 class="text-muted mb-3">Investment Portfolio</h6>
                                    <div class="portfolio-section mb-3">
                                        <div class="portfolio-item d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Total Invested:</span>
                                            <span class="fw-bold text-primary">$0.00</span>
                                        </div>
                                        <div class="portfolio-item d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Active Plans:</span>
                                            <span class="fw-bold text-success">0</span>
                                        </div>
                                        <div class="portfolio-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Return:</span>
                                            <span class="fw-bold text-info">$0.00</span>
                                        </div>
                                    </div>

                                    <h6 class="text-muted mb-3">Investment Tips</h6>
                                    <div class="tips-container">
                                        <div class="tip-item mb-2">
                                            <i class="fe fe-check-circle text-success me-2"></i>
                                            <small>Start with small amounts</small>
                                        </div>
                                        <div class="tip-item mb-2">
                                            <i class="fe fe-check-circle text-success me-2"></i>
                                            <small>Diversify your portfolio</small>
                                        </div>
                                        <div class="tip-item">
                                            <i class="fe fe-check-circle text-success me-2"></i>
                                            <small>Monitor regularly</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Videos Modal Content -->
                        <div id="videosModalContent" class="modal-section d-none">
                            <div class="modal-grid-container">
                                <!-- 2x2 Grid Layout -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="openVideoModal()">
                                            <div class="modal-item-icon bg-primary-light">
                                                <i class="fe fe-play-circle text-primary"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Watch Videos</h6>
                                                <small class="text-muted">Earn Now</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.video-views.history') }}'">
                                            <div class="modal-item-icon bg-success-light">
                                                <i class="fe fe-list text-success"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">History</h6>
                                                <small class="text-muted">Viewed</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('video.earnings') }}'">
                                            <div class="modal-item-icon bg-warning-light">
                                                <i class="fe fe-dollar-sign text-warning"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Earnings</h6>
                                                <small class="text-muted">Income</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('video.daily-report') }}'">
                                            <div class="modal-item-icon bg-info-light">
                                                <i class="fe fe-gift text-info"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Reports</h6>
                                                <small class="text-muted">Daily</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Extra Content Section -->
                                <div class="modal-extra-content">
                                    <h6 class="text-muted mb-3">Video Stats</h6>
                                    <div class="video-stats mb-3">
                                        <div class="stat-row d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Today's Views:</span>
                                            <span class="fw-bold text-primary" id="todayViewsCount">0</span>
                                        </div>
                                        <div class="stat-row d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Today's Earnings:</span>
                                            <span class="fw-bold text-success" id="todayEarningsAmount">$0.00</span>
                                        </div>
                                        <div class="stat-row d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Earned:</span>
                                            <span class="fw-bold text-info" id="totalEarnedAmount">$0.00</span>
                                        </div>
                                    </div>

                                    <div class="progress-section">
                                        <h6 class="text-muted mb-3">Daily Progress</h6>
                                        <div class="progress-item">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Daily Target</small>
                                                <small class="text-primary" id="dailyProgressText">0/10</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary" style="width: 0%" id="dailyProgressBar"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <h6 class="text-muted mb-2">Available Categories</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-primary-transparent">Entertainment</span>
                                            <span class="badge bg-success-transparent">Educational</span>
                                            <span class="badge bg-info-transparent">Technology</span>
                                            <span class="badge bg-warning-transparent">Business</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Modal Content -->
                        <div id="walletModalContent" class="modal-section d-none">
                            <div class="modal-grid-container">
                                <!-- 2x2 Grid Layout -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('deposit.index') }}'">
                                            <div class="modal-item-icon bg-success-light">
                                                <i class="fe fe-plus-circle text-success"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Deposit</h6>
                                                <small class="text-muted">Add Funds</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.withdraw') }}'">
                                            <div class="modal-item-icon bg-danger-light">
                                                <i class="fe fe-minus-circle text-danger"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Withdraw</h6>
                                                <small class="text-muted">Cash Out</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('deposit.history') }}'">
                                            <div class="modal-item-icon bg-info-light">
                                                <i class="fe fe-list text-info"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">History</h6>
                                                <small class="text-muted">Transactions</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="modal-grid-item" onclick="window.location.href='{{ route('user.transfer_funds') }}'">
                                            <div class="modal-item-icon bg-warning-light">
                                                <i class="fe fe-send text-warning"></i>
                                            </div>
                                            <div class="modal-item-content">
                                                <h6 class="mb-1">Transfer</h6>
                                                <small class="text-muted">Send Money</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Extra Content Section -->
                                <div class="modal-extra-content">
                                    <h6 class="text-muted mb-3">Wallet Summary</h6>
                                    <div class="wallet-summary mb-3">
                                        <div class="balance-item d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Available Balance:</span>
                                            <span class="fw-bold text-success">@auth ${{ number_format(Auth::user()->balance, 2) }} @endauth</span>
                                        </div>
                                        <div class="balance-item d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Total Deposited:</span>
                                            <span class="fw-bold text-primary">$0.00</span>
                                        </div>
                                        <div class="balance-item d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Withdrawn:</span>
                                            <span class="fw-bold text-danger">$0.00</span>
                                        </div>
                                    </div>

                                    <h6 class="text-muted mb-3">Payment Methods</h6>
                                    <div class="payment-methods">
                                        <div class="payment-item d-flex align-items-center mb-2">
                                            <i class="fe fe-credit-card text-primary me-2"></i>
                                            <small>Credit/Debit Cards</small>
                                        </div>
                                        <div class="payment-item d-flex align-items-center mb-2">
                                            <i class="fe fe-smartphone text-success me-2"></i>
                                            <small>Mobile Payments</small>
                                        </div>
                                        <div class="payment-item d-flex align-items-center">
                                            <i class="fe fe-globe text-info me-2"></i>
                                            <small>Cryptocurrency</small>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <h6 class="text-muted mb-2">Recent Activity</h6>
                                        <div class="activity-list">
                                            <div class="activity-item d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fe fe-plus-circle text-success me-2"></i>
                                                    <small>No recent activity</small>
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

        <!-- Enhanced Mobile Modal Styles -->
        <style>
        .modal-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-light { background-color: rgba(0, 123, 255, 0.1); }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.1); }
        .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }

        /* 2x2 Grid Item Styling */
        .modal-grid-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .modal-grid-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }

        .modal-grid-item:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .modal-grid-item .modal-item-icon {
            margin-bottom: 8px;
            transition: transform 0.3s ease;
        }

        .modal-grid-item:hover .modal-item-icon {
            transform: scale(1.1);
        }

        .modal-item-content h6 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .modal-item-content small {
            color: #6c757d;
            font-size: 0.75rem;
        }

        /* Stat Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }

        .stat-card .stat-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .stat-card .stat-content h6 {
            font-weight: 600;
            margin-bottom: 2px;
        }

        /* Progress Items */
        .progress-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px;
        }

        /* Info Cards */
        .info-card {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-card:last-child {
            border-bottom: none;
        }

        /* Portfolio Items */
        .portfolio-item {
            padding: 6px 0;
        }

        /* Balance Items */
        .balance-item {
            padding: 6px 0;
        }

        /* Tip Items */
        .tip-item {
            display: flex;
            align-items: center;
            padding: 4px 0;
        }

        /* Payment Items */
        .payment-item {
            padding: 4px 0;
        }

        /* Activity Items */
        .activity-item {
            padding: 6px 0;
        }

        /* Video Stats */
        .video-stats {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }

        .stat-row {
            padding: 4px 0;
        }

        /* Progress Section */
        .progress-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }

        /* Badge Transparents */
        .bg-primary-transparent { background-color: rgba(0, 123, 255, 0.1) !important; color: #007bff !important; }
        .bg-success-transparent { background-color: rgba(40, 167, 69, 0.1) !important; color: #28a745 !important; }
        .bg-info-transparent { background-color: rgba(23, 162, 184, 0.1) !important; color: #17a2b8 !important; }
        .bg-warning-transparent { background-color: rgba(255, 193, 7, 0.1) !important; color: #ffc107 !important; }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .modal-section {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments for modal grid */
        @media (max-width: 576px) {
            .modal-grid-item {
                min-height: 75px;
                padding: 12px;
            }
            
            .modal-item-icon {
                width: 35px;
                height: 35px;
            }
            
            .modal-item-content h6 {
                font-size: 0.85rem;
            }
            
            .modal-item-content small {
                font-size: 0.7rem;
            }
        }

        /* Mobile Modal Improvements */
        #mobileNavModal .modal-body {
            padding-bottom: 20px;
        }

        /* Responsive adjustments for modal grid */
        @media (max-width: 576px) {
            .modal-grid-item {
                min-height: 75px;
                padding: 12px;
            }
            
            .modal-item-icon {
                width: 35px;
                height: 35px;
            }
            
            .modal-item-content h6 {
                font-size: 0.85rem;
            }
            
            .modal-item-content small {
                font-size: 0.7rem;
            }
        }
        </style>

        <!-- Mobile Modal JavaScript -->
        <script>
        // Removed conflicting openMobileModal function - now using the one at the bottom of the file
            }
            
            // Update active navigation states
            updateActiveNavigation('profile');
            
            // Update modal title and icon
            const modalTitle = document.getElementById('modalTitle');
            const modalIcon = document.getElementById('modalIcon');
            
            if (modalTitle) modalTitle.textContent = 'Profile';
            if (modalIcon) modalIcon.className = 'fe fe-user me-2';
            
            // Show the modal
            modal.show();
            
            console.log('Opened profile modal');
        }

        function getCurrentActiveSection() {
            // Check which section is currently visible
            const sections = ['profile', 'dashboard', 'invest', 'videos', 'wallet'];
            for (const section of sections) {
                const contentElement = document.getElementById(section + 'ModalContent');
                if (contentElement && !contentElement.classList.contains('d-none')) {
                    return section;
                }
            }
            return 'profile'; // default
        }

        function updateActiveNavigation(activeSection) {
            // Remove active class from all nav links
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.classList.remove('nav-active');
            });
            
            // Add active class to the selected nav link
            const activeLink = document.querySelector(`[data-nav="${activeSection}"]`);
            if (activeLink) {
                activeLink.classList.add('nav-active');
                
                // Add visual feedback with scale animation
                activeLink.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    activeLink.style.transform = '';
                }, 150);
            }
            
            // Also try to find by onclick attribute if data-nav doesn't exist
            if (!activeLink) {
                const alternativeLink = document.querySelector(`[onclick*="openMobileModal('${activeSection}')"]`);
                if (alternativeLink) {
                    alternativeLink.classList.add('nav-active');
                    
                    // Add visual feedback
                    alternativeLink.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        alternativeLink.style.transform = '';
                    }, 150);
                }
            }
            
            console.log('Updated active navigation for:', activeSection);
        }

        function openPasswordModal() {
            // Close the mobile nav modal
            const mobileNavModal = bootstrap.Modal.getInstance(document.getElementById('mobileNavModal'));
            if (mobileNavModal) {
                mobileNavModal.hide();
            }
            
            // Open the password change modal
            setTimeout(() => {
                const passwordModal = new bootstrap.Modal(document.getElementById('changepasswordnmodal'));
                passwordModal.show();
            }, 300);
        }

        function toggleTheme() {
            // Get current theme from localStorage or default to light
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            const themeIcon = document.getElementById('themeIcon');
            
            // Save new theme
            localStorage.setItem('theme', newTheme);
            
            // Apply theme to document
            document.documentElement.setAttribute('data-theme', newTheme);
            document.body.setAttribute('data-theme', newTheme);
            
            // Update icon
            if (newTheme === 'dark') {
                themeIcon.className = 'fe fe-sun text-warning';
                // Apply dark theme styles
                document.body.style.backgroundColor = '#1a1a1a';
                document.body.style.color = '#ffffff';
                
                // Update modal styles for dark theme
                const modals = document.querySelectorAll('.modal-content');
                modals.forEach(modal => {
                    modal.style.backgroundColor = '#2d2d2d';
                    modal.style.color = '#ffffff';
                });
            } else {
                themeIcon.className = 'fe fe-moon text-info';
                // Apply light theme styles
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
                
                // Reset modal styles for light theme
                const modals = document.querySelectorAll('.modal-content');
                modals.forEach(modal => {
                    modal.style.backgroundColor = '';
                    modal.style.color = '';
                });
            }
            
            // Add visual feedback
            const themeButton = themeIcon.closest('.modal-grid-item');
            if (themeButton) {
                themeButton.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    themeButton.style.transform = '';
                }, 150);
            }
            
            console.log('Theme switched to:', newTheme);
        }

        // Enhanced Mobile Theme Toggle Function
        function toggleMobileTheme() {
            // Get current theme from localStorage or default to light
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            const mobileThemeIcon = document.getElementById('mobileThemeIcon');
            
            // Save new theme
            localStorage.setItem('theme', newTheme);
            
            // Apply theme to document
            document.documentElement.setAttribute('data-theme', newTheme);
            document.body.setAttribute('data-theme', newTheme);
            
            // Update mobile theme icon
            if (newTheme === 'dark') {
                if (mobileThemeIcon) {
                    mobileThemeIcon.className = 'fe fe-sun topbar-icon';
                }
                // Apply dark theme styles
                document.body.style.backgroundColor = '#1a1a1a';
                document.body.style.color = '#ffffff';
                
                // Update mobile navbar for dark theme
                const mobileNavbar = document.querySelector('.mobile-navbar');
                if (mobileNavbar) {
                    mobileNavbar.style.background = 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)';
                }
                
                // Update mobile topbar for dark theme
                const mobileTopbar = document.querySelector('.mobile-nav-topbar');
                if (mobileTopbar) {
                    mobileTopbar.style.background = 'rgba(52, 73, 94, 0.3)';
                }
                
                // Update mobile nav links for dark theme
                document.querySelectorAll('.mobile-nav-link, .mobile-topbar-link').forEach(link => {
                    link.style.color = '#ecf0f1';
                });
                
                console.log('Dark theme applied to mobile navigation');
            } else {
                if (mobileThemeIcon) {
                    mobileThemeIcon.className = 'fe fe-moon topbar-icon';
                }
                // Apply light theme styles
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
                
                // Reset mobile navbar for light theme
                const mobileNavbar = document.querySelector('.mobile-navbar');
                if (mobileNavbar) {
                    mobileNavbar.style.background = 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)';
                }
                
                // Reset mobile topbar for light theme
                const mobileTopbar = document.querySelector('.mobile-nav-topbar');
                if (mobileTopbar) {
                    mobileTopbar.style.background = 'rgba(0, 123, 255, 0.05)';
                }
                
                // Reset mobile nav links for light theme
                document.querySelectorAll('.mobile-nav-link, .mobile-topbar-link').forEach(link => {
                    link.style.color = '#6c757d';
                });
                
                console.log('Light theme applied to mobile navigation');
            }
            
            // Sync with desktop theme toggle if exists
            const desktopThemeIcon = document.querySelector('.header-theme-mode .header-link-icon');
            if (desktopThemeIcon) {
                if (newTheme === 'dark') {
                    desktopThemeIcon.className = 'fe fe-sun header-link-icon';
                } else {
                    desktopThemeIcon.className = 'fe fe-moon header-link-icon';
                }
            }
            
            // Sync with modal theme icon if exists
            const modalThemeIcon = document.getElementById('themeIcon');
            if (modalThemeIcon) {
                if (newTheme === 'dark') {
                    modalThemeIcon.className = 'fe fe-sun text-warning';
                } else {
                    modalThemeIcon.className = 'fe fe-moon text-info';
                }
            }
            
            // Add visual feedback with haptic vibration if supported
            if ('vibrate' in navigator) {
                navigator.vibrate(50); // Short vibration
            }
            
            // Add visual feedback animation
            const themeButton = mobileThemeIcon ? mobileThemeIcon.closest('.mobile-topbar-link') : null;
            if (themeButton) {
                themeButton.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    themeButton.style.transform = '';
                }, 150);
            }
            
            console.log('Mobile theme switched to:', newTheme);
        }

        // Initialize mobile theme on page load
        function initializeMobileTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const mobileThemeIcon = document.getElementById('mobileThemeIcon');
            
            // Apply saved theme
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.body.setAttribute('data-theme', savedTheme);
            
            if (mobileThemeIcon) {
                if (savedTheme === 'dark') {
                    mobileThemeIcon.className = 'fe fe-sun topbar-icon';
                    document.body.style.backgroundColor = '#1a1a1a';
                    document.body.style.color = '#ffffff';
                    
                    // Apply dark theme to mobile navbar if visible
                    const mobileNavbar = document.querySelector('.mobile-navbar');
                    if (mobileNavbar) {
                        mobileNavbar.style.background = 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)';
                    }
                    
                    // Apply dark theme to mobile topbar if visible
                    const mobileTopbar = document.querySelector('.mobile-nav-topbar');
                    if (mobileTopbar) {
                        mobileTopbar.style.background = 'rgba(52, 73, 94, 0.3)';
                    }
                } else {
                    mobileThemeIcon.className = 'fe fe-moon topbar-icon';
                    document.body.style.backgroundColor = '';
                    document.body.style.color = '';
                    
                    // Reset mobile navbar to light theme
                    const mobileNavbar = document.querySelector('.mobile-navbar');
                    if (mobileNavbar) {
                        mobileNavbar.style.background = 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)';
                    }
                    
                    // Reset mobile topbar to light theme
                    const mobileTopbar = document.querySelector('.mobile-nav-topbar');
                    if (mobileTopbar) {
                        mobileTopbar.style.background = 'rgba(0, 123, 255, 0.05)';
                    }
                }
            }
            
            console.log('Mobile theme initialized:', savedTheme);
        }

        function logout() { 
            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fe fe-log-out me-1"></i>Yes, Logout',
                cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
                buttonsStyling: false, // Disable default styling to use custom
                reverseButtons: true, // Cancel button on left, confirm on right
                focusCancel: false, // Don't auto-focus cancel button
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal-logout-popup',
                    title: 'swal-logout-title',
                    htmlContainer: 'swal-logout-text',
                    confirmButton: 'btn btn-danger swal-confirm-btn',
                    cancelButton: 'btn btn-secondary swal-cancel-btn',
                    actions: 'swal-logout-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Please wait while we log you out.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal-loading-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Close modal first if open
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mobileNavModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Perform logout after short delay
                    setTimeout(() => {
                        // Try logout form first
                        const logoutForm = document.getElementById('simple-logout-form');
                        if (logoutForm) {
                            logoutForm.submit();
                        } else {
                            // Fallback logout methods
                            fetch('/logout', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    window.location.href = '/login';
                                } else {
                                    // Method 2: Direct redirect
                                    window.location.href = '/logout';
                                }
                            })
                            .catch(() => {
                                // Method 3: Final fallback
                                window.location.href = '/login';
                            });
                        }
                    }, 1000);
                }
            });
        }

        // Initialize theme on page load
        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const themeIcon = document.getElementById('themeIcon');
            
            // Apply saved theme
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.body.setAttribute('data-theme', savedTheme);
            
            if (savedTheme === 'dark') {
                if (themeIcon) themeIcon.className = 'fe fe-sun text-warning';
                document.body.style.backgroundColor = '#1a1a1a';
                document.body.style.color = '#ffffff';
            } else {
                if (themeIcon) themeIcon.className = 'fe fe-moon text-info';
            }
            
            console.log('Theme initialized:', savedTheme);
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            
            // Add modal event listeners
            const mobileNavModal = document.getElementById('mobileNavModal');
            if (mobileNavModal) {
                // Clear active states when modal is hidden
                mobileNavModal.addEventListener('hidden.bs.modal', function () {
                    document.querySelectorAll('.mobile-nav-link').forEach(link => {
                        link.classList.remove('nav-active');
                    });
                    console.log('Mobile modal hidden - cleared active states');
                });
                
                // Handle modal shown event
                mobileNavModal.addEventListener('shown.bs.modal', function () {
                    console.log('Mobile modal shown');
                });
            }
            
            // Add haptic feedback for mobile buttons
            document.querySelectorAll('.mobile-nav-link, .modal-grid-item').forEach(button => {
                button.addEventListener('click', function() {
                    // Vibrate if supported
                    if ('vibrate' in navigator) {
                        navigator.vibrate(50);
                    }
                });
            });
        });

        </script>

        <!-- Video Player Modal -->
        <div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-labelledby="videoPlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title d-flex align-items-center" id="videoPlayerModalLabel">
                            <i class="fe fe-play-circle me-2"></i>
                            <span id="videoModalTitle">Video Player</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-0" style="height: 70vh;">
                        <!-- Video Container -->
                        <div class="video-container position-relative w-100 h-100 bg-dark">
                            <!-- Loading Spinner -->
                            <div id="videoLoadingSpinner" class="text-center text-white d-flex align-items-center justify-content-center h-100">
                                <div>
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 4rem; height: 4rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div>Loading videos...</div>
                                </div>
                            </div>
                            
                            <!-- Video List -->
                            <div id="videoListContainer" class="h-100" style="display: none;">
                                <div class="p-4">
                                    <h4 class="text-white mb-4">Available Videos</h4>
                                    <div id="videosList" class="row g-3">
                                        <!-- Videos will be loaded here -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Video Player -->
                            <div id="videoPlayerContainer" class="h-100" style="display: none;">
                                <iframe id="videoPlayer" 
                                        src="" 
                                        frameborder="0" 
                                        allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        class="w-100 h-100">
                                </iframe>
                                
                                <!-- Video Controls Overlay -->
                                <div class="video-controls position-absolute bottom-0 start-0 end-0 bg-gradient text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-1" id="currentVideoTitle">Video Title</h6>
                                            <small class="text-muted" id="videoProgress">Loading...</small>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button class="btn btn-outline-light btn-sm me-2" onclick="goBackToVideoList()">
                                                <i class="fe fe-list me-1"></i>Back to List
                                            </button>
                                            <button class="btn btn-success btn-sm" id="markCompleteBtn" disabled onclick="markVideoComplete()">
                                                <i class="fe fe-check me-1"></i>Mark Complete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Videos Available -->
                            <div id="noVideosContainer" class="text-center text-white d-flex align-items-center justify-content-center h-100" style="display: none;">
                                <div>
                                    <div class="mb-4">
                                        <i class="fe fe-video-off" style="font-size: 4rem; color: #6c757d;"></i>
                                    </div>
                                    <h4 class="mb-3">No Videos Available</h4>
                                    <p class="text-muted mb-4">No videos are available at the moment. Please check back later.</p>
                                    <button class="btn btn-primary" onclick="loadAvailableVideos()">
                                        <i class="fe fe-refresh-cw me-2"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video JavaScript Functions -->
        <script>
        let availableVideos = [];
        let currentVideo = null;
        let videoWatchTimer = null;
        let videoWatchedSeconds = 0;

        function openVideoModal() {
            // Close the mobile nav modal first if it's open
            const mobileNavModal = bootstrap.Modal.getInstance(document.getElementById('mobileNavModal'));
            if (mobileNavModal) {
                mobileNavModal.hide();
            }
            
            // Wait a bit for the mobile modal to close, then open video modal
            setTimeout(() => {
                // Show the video modal
                const modal = new bootstrap.Modal(document.getElementById('videoPlayerModal'));
                modal.show();
                
                // Load available videos
                loadAvailableVideos();
            }, 300);
        }

        function loadAvailableVideos() {
            // Show loading
            showVideoLoading();
            
            // Fetch videos from backend
            fetch('/api/user/daily-videos', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.videos && data.videos.length > 0) {
                    availableVideos = data.videos;
                    displayVideoList();
                } else {
                    showNoVideos();
                }
            })
            .catch(error => {
                console.error('Error loading videos:', error);
                showNoVideos();
            });
        }

        function showVideoLoading() {
            document.getElementById('videoLoadingSpinner').style.display = 'flex';
            document.getElementById('videoListContainer').style.display = 'none';
            document.getElementById('videoPlayerContainer').style.display = 'none';
            document.getElementById('noVideosContainer').style.display = 'none';
        }

        function displayVideoList() {
            const videosList = document.getElementById('videosList');
            videosList.innerHTML = '';
            
            availableVideos.forEach((video, index) => {
                const videoCard = document.createElement('div');
                videoCard.className = 'col-md-6 col-lg-4';
                videoCard.innerHTML = `
                    <div class="card bg-secondary text-white h-100 video-card" data-video-index="${index}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fe fe-play text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">${video.title || 'Video ' + (index + 1)}</h6>
                                    <small class="text-muted">Duration: ${formatDuration(video.duration || 180)}</small>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success">
                                        <i class="fe fe-dollar-sign me-1"></i>${(video.earning_amount || 0).toFixed(4)}
                                    </span>
                                    <small class="text-muted">${video.category || 'General'}</small>
                                </div>
                                <button class="btn btn-primary btn-sm w-100" onclick="playVideo(${index})">
                                    <i class="fe fe-play me-1"></i>Watch Now
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                videosList.appendChild(videoCard);
            });
            
            // Show video list
            document.getElementById('videoLoadingSpinner').style.display = 'none';
            document.getElementById('videoListContainer').style.display = 'block';
            document.getElementById('videoPlayerContainer').style.display = 'none';
            document.getElementById('noVideosContainer').style.display = 'none';
        }

        function showNoVideos() {
            document.getElementById('videoLoadingSpinner').style.display = 'none';
            document.getElementById('videoListContainer').style.display = 'none';
            document.getElementById('videoPlayerContainer').style.display = 'none';
            document.getElementById('noVideosContainer').style.display = 'flex';
        }

        function playVideo(index) {
            if (index >= 0 && index < availableVideos.length) {
                currentVideo = availableVideos[index];
                
                // Update video player
                document.getElementById('videoPlayer').src = currentVideo.url;
                document.getElementById('currentVideoTitle').textContent = currentVideo.title || 'Video ' + (index + 1);
                document.getElementById('videoProgress').textContent = `Earning: $${(currentVideo.earning_amount || 0).toFixed(4)}`;
                
                // Show video player
                document.getElementById('videoLoadingSpinner').style.display = 'none';
                document.getElementById('videoListContainer').style.display = 'none';
                document.getElementById('videoPlayerContainer').style.display = 'block';
                document.getElementById('noVideosContainer').style.display = 'none';
                
                // Start watch timer
                startWatchTimer();
            }
        }

        function goBackToVideoList() {
            // Stop timer
            stopWatchTimer();
            
            // Reset video player
            document.getElementById('videoPlayer').src = '';
            
            // Show video list
            displayVideoList();
        }

        function startWatchTimer() {
            videoWatchedSeconds = 0;
            const minWatchTime = Math.max(30, Math.floor((currentVideo.duration || 180) * 0.8)); // 80% of video duration
            
            videoWatchTimer = setInterval(() => {
                videoWatchedSeconds++;
                
                // Update progress
                const progress = (videoWatchedSeconds / minWatchTime) * 100;
                document.getElementById('videoProgress').textContent = 
                    `Watched: ${formatDuration(videoWatchedSeconds)} / ${formatDuration(minWatchTime)} (${Math.min(100, Math.round(progress))}%)`;
                
                // Enable complete button when minimum watch time is reached
                if (videoWatchedSeconds >= minWatchTime) {
                    document.getElementById('markCompleteBtn').disabled = false;
                }
            }, 1000);
        }

        function stopWatchTimer() {
            if (videoWatchTimer) {
                clearInterval(videoWatchTimer);
                videoWatchTimer = null;
            }
        }

        function markVideoComplete() {
            if (!currentVideo) return;
            
            // Record video view
            fetch('/user/video-views/watch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    video_id: currentVideo.id,
                    watch_duration: videoWatchedSeconds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Video Completed!',
                        text: `You earned $${(currentVideo.earning_amount || 0).toFixed(4)}`,
                        icon: 'success',
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    // Update balance if provided
                    if (data.new_balance !== undefined) {
                        updateUserBalance(data.new_balance);
                    }
                    
                    // Go back to video list
                    setTimeout(() => {
                        goBackToVideoList();
                        loadAvailableVideos(); // Refresh the list
                    }, 1500);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to record video view',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error recording video view:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while recording your view',
                    icon: 'error'
                });
            });
        }

        function formatDuration(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }

        function updateUserBalance(newBalance) {
            // Update balance display if exists
            const balanceElements = document.querySelectorAll('[data-balance]');
            balanceElements.forEach(element => {
                element.textContent = '$' + newBalance.toFixed(2);
            });
        }

        // Clean up when modal is closed
        document.getElementById('videoPlayerModal').addEventListener('hidden.bs.modal', function () {
            stopWatchTimer();
            document.getElementById('videoPlayer').src = '';
            currentVideo = null;
            videoWatchedSeconds = 0;
        });
        </script>

        <!-- Password Change JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('changePasswordForm');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const passwordSpinner = document.getElementById('passwordSpinner');
            
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
                
                if (!validateForm()) {
                    return;
                }
                
                submitPasswordChange();
            });
            
            function checkPasswordStrength(password) {
                const strengthBar = document.getElementById('password_strength_bar');
                const strengthText = document.getElementById('password_strength_text');
                
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
                    const icon = element.querySelector('i');
                    
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
            
            function validateForm() {
                let isValid = true;
                clearErrors();
                
                // Check if passwords match
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    showError('new_password_confirmation_error', 'Passwords do not match');
                    isValid = false;
                }
                
                // Check password strength
                const password = newPasswordInput.value;
                if (password.length < 8) {
                    showError('new_password_error', 'Password must be at least 8 characters long');
                    isValid = false;
                }
                
                return isValid;
            }
            
            function submitPasswordChange() {
                changePasswordBtn.disabled = true;
                passwordSpinner.classList.remove('d-none');
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Password updated successfully!',
                            icon: 'success',
                            timer: 3000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            form.reset();
                            bootstrap.Modal.getInstance(document.getElementById('changepasswordnmodal')).hide();
                        });
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                showError(key + '_error', data.errors[key][0]);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'An error occurred. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    changePasswordBtn.disabled = false;
                    passwordSpinner.classList.add('d-none');
                });
            }
            
            function showError(elementId, message) {
                const errorElement = document.getElementById(elementId);
                if (errorElement) {
                    errorElement.textContent = message;
                    const inputElement = document.getElementById(elementId.replace('_error', ''));
                    if (inputElement) {
                        inputElement.classList.add('is-invalid');
                    }
                }
            }
            
            function clearErrors() {
                const errorElements = document.querySelectorAll('.invalid-feedback');
                errorElements.forEach(element => {
                    element.textContent = '';
                });
                
                const inputs = document.querySelectorAll('.form-control');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
                
                document.getElementById('passwordAlert').classList.add('d-none');
            }
        });
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fe fe-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'fe fe-eye';
            }
        }
        </script>

        <!-- Enhanced Search Functionality -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search elements
            const headerSearchInput = document.getElementById('headerSearchInput');
            const headerSearchForm = document.getElementById('headerSearchForm');
            const searchSuggestions = document.getElementById('searchSuggestions');
            
            // Search categories removed with mobile search
            
            let searchTimeout;
            let currentSuggestions = [];
            
            // Sample search data - replace with actual data from your backend
            const searchData = {
                videos: [
                    { type: 'video', title: 'How to Earn Money Online', category: 'Educational', icon: 'fe-play-circle' },
                    { type: 'video', title: 'Investment Strategies 2024', category: 'Finance', icon: 'fe-play-circle' },
                    { type: 'video', title: 'Passive Income Ideas', category: 'Business', icon: 'fe-play-circle' },
                ],
                users: [
                    { type: 'user', name: 'John Investor', role: 'Premium Member', icon: 'fe-user' },
                    { type: 'user', name: 'Sarah Trader', role: 'VIP Member', icon: 'fe-user' },
                    { type: 'user', name: 'Mike Entrepreneur', role: 'Active Member', icon: 'fe-user' },
                ],
                transactions: [
                    { type: 'transaction', description: 'Deposit Transaction', amount: '$500.00', icon: 'fe-credit-card' },
                    { type: 'transaction', description: 'Withdrawal Request', amount: '$250.00', icon: 'fe-minus-circle' },
                    { type: 'transaction', description: 'Investment Return', amount: '$75.50', icon: 'fe-trending-up' },
                ],
                investments: [
                    { type: 'investment', name: 'Basic Plan', return: '5% Daily', icon: 'fe-star' },
                    { type: 'investment', name: 'Premium Plan', return: '8% Daily', icon: 'fe-award' },
                    { type: 'investment', name: 'VIP Plan', return: '12% Daily', icon: 'fe-crown' },
                ]
            };
            
            // Initialize search functionality
            if (headerSearchInput) {
                headerSearchInput.addEventListener('input', function() {
                    handleSearchInput(this.value, searchSuggestions);
                });
                
                headerSearchInput.addEventListener('focus', function() {
                    if (this.value.length > 0) {
                        searchSuggestions.style.display = 'block';
                    }
                });
                
                headerSearchInput.addEventListener('blur', function() {
                    setTimeout(() => {
                        searchSuggestions.style.display = 'none';
                    }, 200);
                });
            }
            
            if (headerSearchInput) {
                // Desktop search functionality remains
            }
            
            // Form submission handlers
            if (headerSearchForm) {
                headerSearchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    performSearch(headerSearchInput.value);
                });
            }
            
            // Category selection handlers removed with mobile search
            
            function handleSearchInput(query, suggestionsContainer) {
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    suggestionsContainer.style.display = 'none';
                    return;
                }
                
                // Show loading
                suggestionsContainer.innerHTML = '<div class="search-loading">Searching...</div>';
                suggestionsContainer.style.display = 'block';
                
                searchTimeout = setTimeout(() => {
                    // Fetch suggestions from backend
                    fetch(`/user/search/suggestions?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const suggestions = flattenSuggestions(data);
                        displaySuggestions(suggestions, suggestionsContainer);
                    })
                    .catch(error => {
                        console.error('Search suggestions error:', error);
                        // Fallback to local suggestions
                        const suggestions = getSearchSuggestions(query);
                        displaySuggestions(suggestions, suggestionsContainer);
                    });
                }, 300);
            }
            
            function flattenSuggestions(data) {
                const suggestions = [];
                Object.keys(data).forEach(category => {
                    data[category].forEach(item => {
                        suggestions.push({
                            ...item,
                            category: category,
                            relevance: 10 // All backend results are highly relevant
                        });
                    });
                });
                return suggestions.slice(0, 8); // Limit to 8 suggestions
            }
            
            function getSearchSuggestions(query) {
                const results = [];
                const queryLower = query.toLowerCase();
                
                // Search through all categories
                Object.keys(searchData).forEach(category => {
                    searchData[category].forEach(item => {
                        const searchableText = Object.values(item).join(' ').toLowerCase();
                        if (searchableText.includes(queryLower)) {
                            results.push({
                                ...item,
                                category: category,
                                relevance: calculateRelevance(queryLower, searchableText)
                            });
                        }
                    });
                });
                
                // Sort by relevance
                return results.sort((a, b) => b.relevance - a.relevance).slice(0, 8);
            }
            
            function calculateRelevance(query, text) {
                const exactMatch = text.includes(query) ? 10 : 0;
                const wordMatches = query.split(' ').filter(word => text.includes(word)).length;
                const position = text.indexOf(query);
                const positionScore = position === 0 ? 5 : (position > 0 ? 2 : 0);
                
                return exactMatch + wordMatches + positionScore;
            }
            
            function displaySuggestions(suggestions, container) {
                if (suggestions.length === 0) {
                    container.innerHTML = `
                        <div class="search-suggestion-item">
                            <div class="search-suggestion-icon">
                                <i class="fe fe-search"></i>
                            </div>
                            <div>
                                <div>No results found</div>
                                <small class="text-muted">Try a different search term</small>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                suggestions.forEach(item => {
                    html += createSuggestionHTML(item);
                });
                
                container.innerHTML = html;
                
                // Add click handlers
                container.querySelectorAll('.search-suggestion-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const query = this.dataset.query;
                        performSearch(query);
                    });
                });
            }
            
            function createSuggestionHTML(item) {
                let title, subtitle, iconClass;
                
                switch (item.type) {
                    case 'video':
                        title = item.title;
                        subtitle = `Video • ${item.category}`;
                        iconClass = 'fe-play-circle text-primary';
                        break;
                    case 'user':
                        title = item.name;
                        subtitle = `User • ${item.role}`;
                        iconClass = 'fe-user text-success';
                        break;
                    case 'transaction':
                        title = item.description;
                        subtitle = `Transaction • ${item.amount}`;
                        iconClass = 'fe-credit-card text-info';
                        break;
                    case 'investment':
                        title = item.name;
                        subtitle = `Investment • ${item.return}`;
                        iconClass = 'fe-star text-warning';
                        break;
                    default:
                        title = 'Unknown';
                        subtitle = '';
                        iconClass = 'fe-search';
                }
                
                return `
                    <div class="search-suggestion-item" data-query="${title}">
                        <div class="search-suggestion-icon">
                            <i class="fe ${iconClass}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">${highlightQuery(title, getCurrentQuery())}</div>
                            <small class="text-muted">${subtitle}</small>
                        </div>
                        <div class="text-muted">
                            <i class="fe fe-corner-down-left" style="font-size: 12px;"></i>
                        </div>
                    </div>
                `;
            }
            
            function highlightQuery(text, query) {
                if (!query || query.length < 2) return text;
                
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<span class="search-highlight">$1</span>');
            }
            
            function getCurrentQuery() {
                return headerSearchInput?.value || '';
            }
            
            function performSearch(query) {
                if (!query || query.trim().length === 0) {
                    Swal.fire({
                        title: 'Search Required',
                        text: 'Please enter a search term',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }
                
                // Hide suggestions
                if (searchSuggestions) searchSuggestions.style.display = 'none';
                
                // Show loading with SweetAlert2
                Swal.fire({
                    title: 'Searching...',
                    text: `Looking for "${query}"`,
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Redirect to search results page
                const searchUrl = new URL('/user/search', window.location.origin);
                searchUrl.searchParams.set('q', query.trim());
                
                // Add a small delay to show the loading message
                setTimeout(() => {
                    window.location.href = searchUrl.toString();
                }, 500);
            }
            
            // Global search shortcut (Ctrl+K or Cmd+K)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    if (headerSearchInput) {
                        headerSearchInput.focus();
                    } else if (mobileSearchInput) {
                        mobileSearchInput.focus();
                    }
                }
            });
        });
        </script>

        

    </div>

    
    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="fe fe-chevrons-up"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS for SweetAlert2 -->
    <style>
        .swal2-popup {
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;
        }
        
        .swal2-title {
            font-weight: 600 !important;
            color: #333 !important;
        }
        
        .swal2-content {
            color: #666 !important;
        }
        
        .swal2-icon.swal2-warning {
            border-color: #f0ad4e !important;
            color: #f0ad4e !important;
        }
        
        .swal2-icon.swal2-success {
            border-color: #5cb85c !important;
            color: #5cb85c !important;
        }
        
        .swal2-icon.swal2-error {
            border-color: #d9534f !important;
            color: #d9534f !important;
        }
        
        .swal2-timer-progress-bar {
            background: rgba(0,123,255,.25) !important;
        }
        
        .swal2-backdrop-show {
            background: rgba(0,0,0,0.5) !important;
        }
        
        /* Button animations */
        .swal2-actions .btn {
            margin: 0 8px !important;
            transition: all 0.3s ease !important;
        }
        
        .swal2-actions .btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
        
        /* Loading animation */
        .swal2-loading .swal2-styled {
            box-shadow: inset 0 0 0 1px rgba(0,0,0,.05) !important;
        }
    </style>

    <!-- Popper JS -->
    <script src="{{asset('assets/libs/@popperjs/core/umd/popper.min.js')}}"></script>

    <!-- Bootstrap JS -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{asset('assets/js/defaultmenu.min.js')}}"></script>
    
    <!-- Fix for defaultmenu.min.js null reference errors -->
    <script>
        // Safety wrapper to prevent defaultmenu.min.js errors
        document.addEventListener('DOMContentLoaded', function() {
            // Create missing elements that defaultmenu.min.js expects
            const requiredElements = [
                { selector: '.horizontal-main', className: 'horizontal-main' },
                { selector: '.main-menu', className: 'main-menu' },
                { selector: '.horizontal-nav', className: 'horizontal-nav' },
                { selector: '.switcher-item', className: 'switcher-item' },
                { selector: '.nav-item', className: 'nav-item' },
                { selector: '.has-arrow', className: 'has-arrow' }
            ];
            
            requiredElements.forEach(element => {
                if (!document.querySelector(element.selector)) {
                    const newElement = document.createElement('div');
                    newElement.className = element.className;
                    newElement.style.display = 'none';
                    document.body.appendChild(newElement);
                }
            });
            
            // Override problematic functions to prevent errors
            if (typeof window.checkHoriMenu === 'function') {
                const originalCheckHoriMenu = window.checkHoriMenu;
                window.checkHoriMenu = function() {
                    try {
                        return originalCheckHoriMenu.apply(this, arguments);
                    } catch (error) {
                        console.warn('checkHoriMenu error prevented:', error.message);
                        return false;
                    }
                };
            }
            
            if (typeof window.switcherArrowFn === 'function') {
                const originalSwitcherArrowFn = window.switcherArrowFn;
                window.switcherArrowFn = function() {
                    try {
                        return originalSwitcherArrowFn.apply(this, arguments);
                    } catch (error) {
                        console.warn('switcherArrowFn error prevented:', error.message);
                        return false;
                    }
                };
            }
        });
    </script>

    <!-- Node Waves JS-->
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <!-- Sticky JS -->
    <script src="{{asset('assets/js/sticky.js')}}"></script>

    <!-- Simplebar JS -->
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/js/simplebar.js')}}"></script>

    <!-- Color Picker JS -->
    <script src="{{asset('assets/libs/@simonwep/pickr/pickr.es5.min.js')}}"></script>


    <!-- Apex Charts JS -->
    <script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>

    <!-- Chartjs Chart JS -->
    <script src="{{asset('assets/libs/chart.js/chart.min.js')}}"></script>

    <!-- Date & Time Picker JS -->
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>

    <!-- Default date picker js-->
    <script src="{{asset('assets/js/default-flat-datepicker.js')}}"></script> 

    <!-- Enhanced Notification Sidebar Canvas (Disabled) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notification-sidebar-canvas" aria-labelledby="notificationSidebarLabel" style="display: none;">
        <div class="offcanvas-header border-bottom bg-gradient-primary text-white">
            <h5 class="offcanvas-title d-flex align-items-center" id="notificationSidebarLabel">
                <i class="fe fe-bell me-2"></i>Notifications
                <span class="badge bg-light text-dark ms-2" id="notification-count">0</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <!-- Enhanced Notification Actions -->
            <div class="notification-actions-bar p-3 border-bottom bg-light">
                <div class="row g-2">
                    <div class="col-4">
                        <button class="btn btn-sm btn-outline-primary w-100" onclick="markAllAsRead()" id="mark-all-btn" title="Mark All Read">
                            <i class="fe fe-check-circle me-1"></i>
                            <span class="d-none d-md-inline">Mark All</span>
                        </button>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-sm btn-outline-danger w-100" onclick="clearAllNotifications()" id="clear-all-btn" title="Clear All">
                            <i class="fe fe-trash me-1"></i>
                            <span class="d-none d-md-inline">Clear All</span>
                        </button>
                    </div>
                    <div class="col-4">
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-outline-info w-100" title="View All">
                            <i class="fe fe-eye me-1"></i>
                            <span class="d-none d-md-inline">View All</span>
                        </a>
                    </div>
                </div>
                
                <!-- Notification Stats -->
                <div class="notification-stats mt-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">Total</small>
                            <span class="fw-bold text-primary" id="total-count">0</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Unread</small>
                            <span class="fw-bold text-warning" id="unread-count">0</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Today</small>
                            <span class="fw-bold text-success" id="today-count">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification List -->
            <div id="notification-list" class="notification-list">
                <div class="text-center p-4" id="notification-loading">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted">Loading notifications...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="notification-empty" class="text-center p-4 d-none">
                <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                <h6 class="text-muted">No notifications</h6>
                <p class="text-muted mb-0">You're all caught up!</p>
            </div>

            <!-- Load More -->
            <div class="text-center p-3 border-top d-none" id="load-more-container">
                <button class="btn btn-sm btn-outline-secondary" onclick="loadMoreNotifications()">
                    Load More
                </button>
            </div>

            <!-- View All Notifications Link -->
            <div class="text-center p-3 border-top">
                <a href="{{ route('user.notifications.index') }}" class="btn btn-primary btn-sm">
                    <i class="fe fe-list me-1"></i>View All Notifications
                </a>
            </div>
        </div>
    </div>

    <!-- Notification Sidebar Script -->
    <script>
        let notificationOffset = 0;
        let notificationLimit = 10;
        let hasMoreNotifications = true;

        document.addEventListener('DOMContentLoaded', function() {
            // Add custom CSS for notification badge
            const style = document.createElement('style');
            style.textContent = `
                #header-notification-badge {
                    background-color: #dc3545 !important;
                    color: white !important;
                    font-weight: bold !important;
                    min-width: 20px !important;
                    height: 20px !important;
                    font-size: 11px !important;
                    line-height: 20px !important;
                    text-align: center !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    border: 2px solid white !important;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
                    border-radius: 50% !important;
                    padding: 0 !important;
                }
                
                /* Equal spacing between header elements */
                .header-element.header-theme-mode {
                    margin-right: 15px !important;
                }
                
                .header-element.notifications-dropdown {
                    margin-right: 15px !important;
                }
                
                .header-element.main-header-profile {
                    margin-right: 0 !important;
                }
                
                #header-notification-badge.d-none {
                    display: none !important;
                }
                
                .notifications-dropdown .header-link {
                    position: relative !important;
                }
                
                /* Ensure proper positioning */
                .header-element.notifications-dropdown {
                    position: relative;
                }
                
                .header-element.notifications-dropdown .position-absolute {
                    top: -8px !important;
                    right: -8px !important;
                    transform: none !important;
                }
            `;
            document.head.appendChild(style);
            
            // Disabled notification loading to prevent errors
            // loadNotifications();
            
            // Disabled auto refresh notifications to prevent errors
            // setInterval(refreshNotificationCount, 30000);
        });

        /*
        // Disabled notification functions to prevent errors
        function loadNotifications(refresh = false) {
            if (refresh) {
                notificationOffset = 0;
                hasMoreNotifications = true;
            }

            // Show loading state
            document.getElementById('notification-loading').style.display = 'block';
            
            // Update action buttons during load
            const markAllBtn = document.getElementById('mark-all-btn');
            const clearAllBtn = document.getElementById('clear-all-btn');
            if (markAllBtn) markAllBtn.disabled = true;
            if (clearAllBtn) clearAllBtn.disabled = true;

            fetch('{{ route("user.notifications.dropdown") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('notification-loading').style.display = 'none';
                    
                    if (data.success) {
                        updateNotificationCount(data.unread_count);
                        updateNotificationStats(data);
                        renderNotifications(data.notifications, refresh);
                        
                        // Re-enable action buttons
                        if (markAllBtn) markAllBtn.disabled = false;
                        if (clearAllBtn) clearAllBtn.disabled = false;
                    } else {
                        showNotificationError();
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    document.getElementById('notification-loading').style.display = 'none';
                    showNotificationError();
                    
                    // Re-enable action buttons even on error
                    if (markAllBtn) markAllBtn.disabled = false;
                    if (clearAllBtn) clearAllBtn.disabled = false;
                });
        }

        function updateNotificationStats(data) {
            // Update the stats in the notification sidebar
            const totalCount = document.getElementById('total-count');
            const unreadCount = document.getElementById('unread-count');
            const todayCount = document.getElementById('today-count');
            
            if (totalCount) totalCount.textContent = data.total_count || 0;
            if (unreadCount) unreadCount.textContent = data.unread_count || 0;
            
            // Calculate today's notifications (this would need to come from backend)
            const today = new Date().toDateString();
            const todayNotifications = data.notifications ? data.notifications.filter(n => {
                return new Date(n.created_at).toDateString() === today;
            }).length : 0;
            
            if (todayCount) todayCount.textContent = todayNotifications;
        }

        function renderNotifications(notifications, refresh = false) {
            const notificationList = document.getElementById('notification-list');
            const emptyState = document.getElementById('notification-empty');
            
            if (refresh) {
                notificationList.innerHTML = '';
            }

            if (notifications.length === 0) {
                if (refresh || notificationList.children.length === 0) {
                    notificationList.classList.add('d-none');
                    emptyState.classList.remove('d-none');
                }
                return;
            }

            notificationList.classList.remove('d-none');
            emptyState.classList.add('d-none');

            notifications.forEach(notification => {
                const notificationElement = createNotificationElement(notification);
                notificationList.appendChild(notificationElement);
            });
        }

        function createNotificationElement(notification) {
            const element = document.createElement('div');
            element.className = `notification-item p-3 border-bottom ${!notification.read ? 'bg-light' : ''}`;
            element.setAttribute('data-notification-id', notification.id);
            
            element.innerHTML = `
                <div class="d-flex">
                    <div class="me-3">
                        <i class="${notification.icon} fs-5 text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${notification.title}</h6>
                        <p class="mb-1 text-muted small">${notification.message}</p>
                        <small class="text-muted">${notification.time_ago}</small>
                        ${!notification.read ? '<span class="badge bg-primary ms-2">New</span>' : ''}
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown">
                            <i class="fe fe-more-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            ${!notification.read ? `<li><a class="dropdown-item" href="javascript:void(0);" onclick="markAsRead(${notification.id})"><i class="fe fe-check me-2"></i>Mark as Read</a></li>` : ''}
                            ${notification.action_url ? `<li><a class="dropdown-item" href="${notification.action_url}"><i class="fe fe-external-link me-2"></i>View</a></li>` : ''}
                            <li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteNotification(${notification.id})"><i class="fe fe-trash me-2"></i>Delete</a></li>
                        </ul>
                    </div>
                </div>
            `;

            if (notification.action_url) {
                element.style.cursor = 'pointer';
                element.onclick = () => {
                    window.location.href = '{{ route("user.notifications.redirect", ":id") }}'.replace(':id', notification.id);
                };
            }

            return element;
        }

        function updateNotificationCount(count) {
            const countElement = document.getElementById('notification-count');
            const headerBadge = document.getElementById('header-notification-badge');
            
            countElement.textContent = count;
            
            if (count > 0) {
                countElement.classList.remove('d-none');
                if (headerBadge) {
                    headerBadge.classList.remove('d-none');
                    // Display the count number in the badge
                    headerBadge.textContent = count > 99 ? '99+' : count;
                    // Ensure the badge has proper red background styling
                    headerBadge.classList.add('bg-danger');
                    // Enhanced styling for perfect centering
                    headerBadge.style.minWidth = '20px';
                    headerBadge.style.height = '20px';
                    headerBadge.style.fontSize = '11px';
                    headerBadge.style.lineHeight = '20px';
                    headerBadge.style.textAlign = 'center';
                    headerBadge.style.color = 'white';
                    headerBadge.style.fontWeight = 'bold';
                    headerBadge.style.display = 'flex';
                    headerBadge.style.alignItems = 'center';
                    headerBadge.style.justifyContent = 'center';
                    headerBadge.style.borderRadius = '50%';
                    headerBadge.style.padding = '0';
                }
            } else {
                countElement.classList.add('d-none');
                if (headerBadge) {
                    headerBadge.classList.add('d-none');
                }
            }
        }

        function markAsRead(notificationId) {
            fetch(`{{ route("user.notifications.read", ":id") }}`.replace(':id', notificationId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.classList.remove('bg-light');
                        const badge = notificationElement.querySelector('.badge');
                        if (badge) badge.remove();
                    }
                    refreshNotificationCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function markAllAsRead() {
            fetch('{{ route("user.notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications(true);
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }

        function deleteNotification(notificationId) {
            Swal.fire({
                title: 'Delete Notification?',
                text: 'Are you sure you want to delete this notification?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fe fe-trash me-1"></i>Delete',
                cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`{{ route("user.notifications.delete", ":id") }}`.replace(':id', notificationId), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                        if (notificationElement) {
                            notificationElement.remove();
                        }
                        refreshNotificationCount();
                        
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Notification has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to delete notification.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting notification:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the notification.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                });
            });
        }

        function clearAllNotifications() {
            Swal.fire({
                title: 'Clear All Notifications?',
                text: 'This action cannot be undone and will permanently delete all your notifications.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fe fe-trash me-1"></i>Yes, Clear All',
                cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (!result.isConfirmed) return;

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    Swal.fire({
                        title: 'Error!',
                        text: 'CSRF token not found. Please refresh the page.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                console.log('Clearing all notifications...');

            // Show processing state
            Swal.fire({
                title: 'Clearing Notifications...',
                text: 'Please wait while we clear all your notifications.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Show loading state
            const clearButton = document.querySelector('button[onclick="clearAllNotifications()"]');
            const originalText = clearButton ? clearButton.innerHTML : '';
            if (clearButton) {
                clearButton.innerHTML = '<i class="fe fe-loader me-1"></i>Clearing...';
                clearButton.disabled = true;
            }

            fetch('{{ route("user.notifications.clear-all") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response URL:', response.url);
                
                if (response.status === 404) {
                    throw new Error('Clear all endpoint not found. Please contact support.');
                } else if (response.status === 401) {
                    throw new Error('Authentication required. Please log in again.');
                } else if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page and try again.');
                } else if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Clear all response:', data);
                if (data.success) {
                    loadNotifications(true);
                    console.log('Notifications cleared successfully');
                    
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'All notifications have been cleared successfully.',
                        icon: 'success',
                        confirmButtonText: 'Great!',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    console.error('Failed to clear notifications:', data.message);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to clear notifications: ' + (data.message || 'Unknown error'),
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            })
            .catch(error => {
                console.error('Error clearing all notifications:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error clearing notifications: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            })
            .finally(() => {
                // Restore button state
                if (clearButton) {
                    clearButton.innerHTML = originalText || '<i class="fe fe-trash me-1"></i>Clear All';
                    clearButton.disabled = false;
                }
            });
            }); 
        }

        function refreshNotificationCount() {
            fetch('{{ route("user.notifications.count") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON - likely redirected to login or error page');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateNotificationCount(data.count);
                    } else {
                        throw new Error(data.message || 'Failed to refresh notification count');
                    }
                })
                .catch(error => {
                    // Silently handle errors in production to avoid console spam
                    // Only log if it's a development environment
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.warn('Notification count refresh failed:', error.message);
                    }
                    
                    // If it's an authentication error, user might need to refresh
                    if (error.message.includes('redirected to login')) {
                        // Optionally could show a subtle notification or handle gracefully
                    }
                });
        }

        function showNotificationError() {
            const notificationList = document.getElementById('notification-list');
            notificationList.innerHTML = `
                <div class="text-center p-4">
                    <i class="fe fe-alert-circle fs-1 text-danger mb-3"></i>
                    <h6 class="text-danger">Error loading notifications</h6>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadNotifications(true)">
                        Try Again
                    </button>
                </div>
            `;
        }
        */
    </script>

    <!-- Pre-emptive fix for index3.js null reference errors -->
    <script>
        // Pre-emptive fix to prevent index3.js and custom-switcher.min.js errors when admin elements don't exist
        (function() {
            // Store original methods before any other scripts load
            const originalQuerySelector = document.querySelector;
            const originalGetElementById = document.getElementById;
            
            // List of admin-specific element IDs that may not exist in user layout
            // Including typos and variations found in index3.js and custom-switcher.min.js
            const adminElements = [
                'attendance', 'attendance2', 'attendance3', 'attendance4',
                'attendanc2', 'attendanc3', 'attendanc4', // typos in index3.js
                'revenue-analytics', 'profit-chart', 'sales-chart',
                'admin-chart', 'monthly-chart', 'yearly-chart',
                'statistics', 'statistics1', 'statistics2', 'statistics3', 'statistics4',
                'overview', 'overview-chart', 'project-overview', 'leavesoverview',
                'expenses', 'chartline1', 'chartbar-statistics'
            ];
            
            // Define safe dummy functions for chart functions that may not exist
            const adminChartFunctions = [
                'statistics1', 'overview', 'chartline1', 'expenses',
                'overView', 'projectOverview', 'leavesOverview', 'chartbarstatistics'
            ];
            
            // Create dummy functions to prevent "function is not defined" errors
            adminChartFunctions.forEach(funcName => {
                if (typeof window[funcName] === 'undefined') {
                    window[funcName] = function() {
                        // Silent no-op function for admin charts in user layout
                        return true;
                    };
                }
            });
            
            // Create a safe dummy element for charts
            function createSafeDummyElement() {
                const dummy = document.createElement('div');
                dummy.style.display = 'none';
                dummy.style.width = '1px';
                dummy.style.height = '1px';
                dummy.style.position = 'absolute';
                dummy.style.top = '-9999px';
                dummy.style.left = '-9999px';
                
                // Safe innerHTML setter that doesn't throw errors
                Object.defineProperty(dummy, 'innerHTML', {
                    set: function(value) {
                        // Silently ignore innerHTML assignments
                        return true;
                    },
                    get: function() {
                        return '';
                    },
                    configurable: true,
                    enumerable: true
                });
                
                // Add other chart-related properties that might be accessed
                dummy.offsetWidth = 300;
                dummy.offsetHeight = 200;
                dummy.clientWidth = 300;
                dummy.clientHeight = 200;
                
                return dummy;
            }
            
            // Enhanced selector matching function
            function matchesAdminElement(selector) {
                if (typeof selector !== 'string') return false;
                
                return adminElements.some(id => {
                    return selector.includes('#' + id) || 
                           selector.includes('.' + id) ||
                           selector === '#' + id ||
                           selector === '.' + id;
                });
            }
            
            // Immediately create missing elements
            adminElements.forEach(elementId => {
                if (!originalGetElementById.call(document, elementId)) {
                    const element = createSafeDummyElement();
                    element.id = elementId;
                    
                    // Add to DOM immediately
                    if (document.body) {
                        document.body.appendChild(element);
                    } else {
                        // If body doesn't exist yet, wait for it
                        document.addEventListener('DOMContentLoaded', function() {
                            if (!originalGetElementById.call(document, elementId)) {
                                document.body.appendChild(element);
                            }
                        });
                    }
                }
            });
            
            // Override querySelector with safety checks
            document.querySelector = function(selector) {
                const element = originalQuerySelector.call(this, selector);
                
                // If element doesn't exist and it matches admin patterns, return dummy
                if (!element && matchesAdminElement(selector)) {
                    return createSafeDummyElement();
                }
                
                return element;
            };
            
            // Override getElementById with safety checks
            document.getElementById = function(id) {
                const element = originalGetElementById.call(this, id);
                
                if (!element && adminElements.includes(id)) {
                    return createSafeDummyElement();
                }
                
                return element;
            };
            
            // Also override querySelectorAll for completeness
            const originalQuerySelectorAll = document.querySelectorAll;
            document.querySelectorAll = function(selector) {
                const elements = originalQuerySelectorAll.call(this, selector);
                
                // If no elements found and it's an admin selector, return empty NodeList
                if (elements.length === 0 && matchesAdminElement(selector)) {
                    return []; // Return empty array that can be iterated
                }
                
                return elements;
            };
        })();
    </script>

    <!-- Index2 js-->
    <script src="{{asset('assets/js/index3.js')}}"></script>

    <!-- Notifications JS -->
    <script src="{{asset('assets/libs/awesome-notifications/index.var.js')}}"></script>

    <!-- Successful-notify JS -->
    <script src="{{asset('assets/js/successful-notify.js')}}"></script>

    
    <!-- Custom-Switcher JS -->
    <script src="{{asset('assets/js/custom-switcher.min.js')}}"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets/js/custom.js')}}"></script>
    
    <!-- Login Cache Clearing for Fresh Dashboard -->
    <script src="{{asset('assets/js/login-cache-clear.js')}}"></script>
    
    <!-- Advanced Popup System --> 
    <script src="{{asset('assets/js/advanced-popup-system.js')}}"></script>
    
    @stack('script')
    
    <!-- Global Logout Function -->
    <script>
        // Enhanced logout function with fallback mechanisms
        function performLogout() {
            console.log('performLogout called - attempting simple logout first');
            
            // Primary: Try simple logout (no CSRF, no middleware)
            window.location.href = "{{ route('simple.logout') }}";
        }
        
        // Fallback logout function
        function performLogoutFallback() {
            console.log('performLogoutFallback called - using standard logout');
            
            // Fallback: Try standard logout
            window.location.href = "{{ route('logout') }}";
        }
        
        // Emergency logout function
        function performEmergencyLogout() {
            console.log('performEmergencyLogout called - direct redirect');
            
            // Emergency: Direct redirect to login
            window.location.href = "{{ route('login') }}?emergency_logout=1&t=" + Math.floor(Date.now() / 1000);
        }
        
        // Global error handler for logout failures
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('419')) {
                console.log('419 error detected, attempting emergency logout');
                performEmergencyLogout();
            }
        });
        
        // Simple logout function (cache clearing now happens on login)
        function performLogoutWithCacheClearing() {
            performLogout(); // Use the standard logout function
        }

        // Add debug info to page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Logout system initialized');
            console.log('Available routes:', {
                simple_logout: "{{ route('simple.logout') }}",
                standard_logout: "{{ route('logout') }}",
                login: "{{ route('login') }}"
            });
        });
        
        // Logout confirmation function
        function confirmLogout() {
            // Check if SweetAlert is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Sign Out?',
                    text: 'Are you sure you want to sign out of your account?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fe fe-log-out me-1"></i>Yes, Sign Out',
                    cancelButtonText: '<i class="fe fe-x me-1"></i>Cancel',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Signing Out...',
                            text: 'Please wait while we sign you out.',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Try to find either logout form and submit it with fresh token
                        const logoutForm = document.getElementById('logout-form') || document.getElementById('sidebar-logout-form');
                        if (logoutForm) {
                            // Submit form directly
                            logoutForm.submit();
                        } else {
                            // Fallback to performLogout if no form found
                            performLogout();
                        }
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert not available
                if (confirm('Are you sure you want to sign out?')) {
                    const logoutForm = document.getElementById('logout-form') || document.getElementById('sidebar-logout-form');
                    if (logoutForm) {
                        // Submit form directly
                        logoutForm.submit();
                    } else {
                        performLogout();
                    }
                }
            }
        }
        
        // Simple logout function without confirmation
        function simpleLogout() {
            const logoutForm = document.getElementById('logout-form') || document.getElementById('sidebar-logout-form');
            if (logoutForm) {
                // Submit form directly with current CSRF token
                logoutForm.submit();
            } else {
                performLogout();
            }
        }
        
        // Specific handler for sidebar logout
        function handleSidebarLogout(event) {
            event.preventDefault();
            
            // Try multiple ways to ensure logout works
            const sidebarForm = document.getElementById('sidebar-logout-form');
            
            if (typeof Swal !== 'undefined') {
                // Use SweetAlert if available
                Swal.fire({
                    title: 'Sign Out?',
                    text: 'Are you sure you want to sign out?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Sign Out',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (sidebarForm) {
                            // Submit form directly
                            sidebarForm.submit();
                        } else {
                            performLogout();
                        }
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm('Are you sure you want to sign out?')) {
                    if (sidebarForm) {
                        // Submit form directly
                        sidebarForm.submit();
                    } else {
                        performLogout();
                    }
                }
            }
        }
        
        // Emergency logout for sidebar (no confirmation)
        function emergencySidebarLogout() {
            const sidebarForm = document.getElementById('sidebar-logout-form');
            const simpleSidebarForm = document.getElementById('simple-sidebar-logout');
            
            if (sidebarForm) {
                // Submit form directly
                sidebarForm.submit();
            } else if (simpleSidebarForm) {
                // Submit form directly
                simpleSidebarForm.submit();
            } else {
                performLogout();
            }
        }
        
        // Make it available globally
        window.performLogout = performLogout;
        window.confirmLogout = confirmLogout;
        window.simpleLogout = simpleLogout;
        window.handleSidebarLogout = handleSidebarLogout;
        window.emergencySidebarLogout = emergencySidebarLogout;
        
        // Debug function to show simple logout (Ctrl+Shift+L)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'L') {
                e.preventDefault();
                const simpleForm = document.getElementById('simple-logout-form');
                const emergencyDiv = document.getElementById('emergency-logout');
                const simpleSidebarForm = document.getElementById('simple-sidebar-logout');
                
                const isHidden = simpleForm && simpleForm.style.display === 'none';
                
                // Toggle dropdown debug options
                if (simpleForm && emergencyDiv) {
                    simpleForm.style.display = isHidden ? 'block' : 'none';
                    emergencyDiv.style.display = isHidden ? 'block' : 'none';
                }
                
                // Toggle sidebar debug options
                if (simpleSidebarForm) {
                    simpleSidebarForm.style.display = isHidden ? 'block' : 'none';
                }
            }
        });
    </script>
    
    <!-- Global Real-time Updates System -->
    <script src="{{ asset('assets_custom/js/global-realtime-updates.js') }}"></script>
    
    <!-- Browser Cache Service -->
    <script src="{{ asset('js/browser-cache-service.js') }}?v={{ config('app.cache_version', time()) }}"></script>
    
    <!-- Advanced Cache Management System -->
    <script src="{{ asset('assets/js/cache-manager.js') }}?v={{ config('app.cache_version', time()) }}"></script>
    
    <!-- Initialize Popup System for All Users -->
    <script>
    $(document).ready(function() {
        // Check if the advanced popup system is already initialized
        if (typeof window.AdvancedPopupSystem !== 'undefined' && window.AdvancedPopupSystem) {
            console.log('Advanced popup system already initialized');
        } else {
            console.error('AdvancedPopupSystem not found or not initialized properly');
        }
        
        // Initialize browser cache service
        if (typeof BrowserCacheService !== 'undefined') {
            window.browserCacheService = new BrowserCacheService();
            console.log('Browser cache service initialized');
            
            // Expose global cache clearing function
            window.clearBrowserCache = function() {
                return window.browserCacheService.clearDomainCache();
            };
            
            // Add console helper functions
            window.cacheClearHelpers = {
                // Quick cache clear with confirmation
                clearNow: function() {
                    if (confirm('Clear browser cache for ' + window.location.hostname + '?')) {
                        return window.clearBrowserCache();
                    }
                },
                
                // Silent cache clear (no confirmation)
                clearSilent: function() {
                    return window.clearBrowserCache();
                },
                
                // Clear specific cache types
                clearLocalStorage: function() {
                    localStorage.clear();
                    console.log('✅ LocalStorage cleared');
                },
                
                clearSessionStorage: function() {
                    sessionStorage.clear();
                    console.log('✅ SessionStorage cleared');
                },
                
                clearCookies: function() {
                    document.cookie.split(";").forEach(function(c) { 
                        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                    });
                    console.log('✅ Cookies cleared');
                },
                
                // Show cache status
                showCacheStatus: function() {
                    console.log('🔍 Cache Status for:', window.location.hostname);
                    console.log('LocalStorage items:', localStorage.length);
                    console.log('SessionStorage items:', sessionStorage.length);
                    console.log('Cookies:', document.cookie.split(';').length);
                }
            };
            
            // Console welcome message for cache helpers
            console.log('%c🧹 Browser Cache Helpers Available', 'color: #007bff; font-weight: bold;');
            console.log('Use window.cacheClearHelpers.clearNow() to clear cache with confirmation');
            console.log('Use window.cacheClearHelpers.clearSilent() to clear cache silently');
            console.log('Use window.cacheClearHelpers.showCacheStatus() to check cache status');
            console.log('Use Ctrl+Shift+C to clear cache with UI');
        }
        
        // Initialize real-time updates event listeners
        if (typeof window.globalRealtimeUpdates !== 'undefined') {
            document.addEventListener('globalDataUpdated', function(event) {
                // Custom event handling for specific pages can be added here
                if (typeof window.onGlobalDataUpdate === 'function') {
                    window.onGlobalDataUpdate(event.detail.data);
                }
            });
            
            // Expose global update functions
            window.forceGlobalUpdate = function() {
                window.globalRealtimeUpdates.forceUpdate();
            };
            
            window.setGlobalUpdateInterval = function(seconds) {
                window.globalRealtimeUpdates.setUpdateInterval(seconds * 1000);
            };
        }
        
        // Add email campaign helpers for administrators
        @if(auth()->check() && auth()->user()->role === 'admin')
        window.emailCampaignHelpers = {
            // Check queue status
            checkQueueStatus: function() {
                fetch('{{ route("admin.email-campaigns.queue-status") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📧 Email Queue Status:', data);
                })
                .catch(error => console.error('❌ Queue status check failed:', error));
            },
            
            // Send KYC reminders
            sendKycReminders: function() {
                if (confirm('Send KYC reminder emails to pending users?')) {
                    fetch('{{ route("admin.email-campaigns.send-kyc-reminders") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('📧 KYC Reminders:', data);
                    })
                    .catch(error => console.error('❌ KYC reminder send failed:', error));
                }
            },
            
            // Send inactive user reminders
            sendInactiveReminders: function() {
                if (confirm('Send reminder emails to inactive users?')) {
                    fetch('{{ route("admin.email-campaigns.send-inactive-reminders") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('📧 Inactive User Reminders:', data);
                    })
                    .catch(error => console.error('❌ Inactive reminder send failed:', error));
                }
            },
            
            // Navigate to email campaigns
            goToCampaigns: function() {
                window.location.href = '{{ route("admin.email-campaigns.index") }}';
            }
        };
        
        // Console welcome message for email campaign helpers
        console.log('%c📧 Email Campaign Helpers Available (Admin)', 'color: #28a745; font-weight: bold;');
        console.log('Use window.emailCampaignHelpers.checkQueueStatus() to check email queue');
        console.log('Use window.emailCampaignHelpers.sendKycReminders() to send KYC reminders');
        console.log('Use window.emailCampaignHelpers.sendInactiveReminders() for inactive users');
        console.log('Use window.emailCampaignHelpers.goToCampaigns() to navigate to campaigns');
        @endif
    });
    </script>
    
    @yield('pageJsScripts')
    <script>
    let inactivityTime = function () {
        let timer;
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
        document.onclick = resetTimer;
        document.onscroll = resetTimer;

        function logout() {
            // Create a form to properly submit logout request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('logout') }}";
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);
            
            // Add to body and submit
            document.body.appendChild(form);
            form.submit();
        }

        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(logout, 15 * 60 * 1000); // 15 minutes
        }
    };
    inactivityTime();
    </script>

    <!-- Enhanced Logout Functions with Cache Clearing -->
    <script>
    // Simplified sidebar logout functions (cache clearing now happens on login)
    function handleSidebarLogoutWithCacheClearing(event) {
        // Just call standard logout - no cache clearing needed
        performLogout();
    }

    function emergencySidebarLogoutWithCacheClearing() {
        // Just call standard logout - no cache clearing needed
        performLogout();
    }

    // Backward compatibility for existing logout functions
    function handleSidebarLogout(event) {
        performLogout();
    }

    function emergencySidebarLogout() {
        performLogout();
    }

    // Add logout form to all pages for easy access
    function createLogoutForm() {
        if (!document.getElementById('logout-form')) {
            const form = document.createElement('form');
            form.id = 'logout-form';
            form.method = 'POST';
            form.action = '{{ route("logout") }}';
            form.style.display = 'none';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            form.appendChild(csrfInput);
            document.body.appendChild(form);
        }
    }

    // Initialize logout form on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Only create logout form, don't check URL params that might trigger actions
        createLogoutForm();
    });

    // Global keyboard shortcut for cache clearing (Ctrl+Shift+C)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (window.BrowserCacheService) {
                // Use the browser cache service
                const cacheService = new window.BrowserCacheService();
                
                // Show loading message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Clearing Cache...',
                        text: 'Please wait while we clear your browser cache',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
                
                cacheService.clearDomainCache().then(result => {
                    if (result.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Cache Cleared!',
                                html: `
                                    <div class="text-success mb-3">
                                        <i class="fe fe-check-circle" style="font-size: 3rem;"></i>
                                    </div>
                                    <p>Browser cache successfully cleared for <strong>${result.domain}</strong></p>
                                    <small class="text-muted">Cleared: ${result.methods.join(', ')}</small>
                                `,
                                icon: 'success',
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            });
                        } else {
                            console.log('✅ Cache cleared successfully:', result);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Cache Clear Failed',
                                text: result.error || 'There was an error clearing the cache',
                                icon: 'error',
                                confirmButtonText: 'Try Alternative Method',
                                showCancelButton: true,
                                cancelButtonText: 'Cancel',
                                customClass: {
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-secondary'
                                },
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Fallback: navigate to cache clear route
                                    window.location.href = "{{ route('browser.cache.clear.domain') }}";
                                }
                            });
                        } else {
                            console.error('❌ Cache clearing failed:', result);
                        }
                    }
                }).catch(error => {
                    console.error('❌ Cache clearing failed:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Cache Clear Error',
                            text: 'An unexpected error occurred. Try the alternative method.',
                            icon: 'error',
                            confirmButtonText: 'Try Alternative',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('browser.cache.clear.domain') }}";
                            }
                        });
                    }
                });
            } else if (window.clearBrowserCache) {
                window.clearBrowserCache();
            } else {
                // Fallback: navigate to cache clear route
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Clear Browser Cache?',
                        text: 'This will clear your browser cache for this domain',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#007bff',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Clear Cache',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('browser.cache.clear.domain') }}";
                        }
                    });
                } else {
                    if (confirm('Clear browser cache for this domain?')) {
                        window.location.href = "{{ route('browser.cache.clear.domain') }}";
                    }
                }
            }
        }
    });
    </script>

    <!-- Logout Popup Styles -->
    <style>
    /* Header Logout Popup Styles */
    .header-logout-popup {
        backdrop-filter: blur(5px);
    }
    
    .header-logout-confirm-btn {
        background: linear-gradient(45deg, #dc3545, #e74c3c) !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
        transition: all 0.3s ease !important;
    }
    
    .header-logout-confirm-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
    }
    
    .header-logout-cancel-btn {
        background: linear-gradient(45deg, #6c757d, #495057) !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }
    
    .header-logout-loading-popup {
        backdrop-filter: blur(8px);
    }
    
    .header-emergency-logout-popup {
        backdrop-filter: blur(3px);
    }
    
    /* Sidebar Logout Button Enhancements */
    .side-menu__item:hover {
        background: rgba(108, 117, 125, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    /* Cache Clear Indicator */
    .cache-clear-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        margin-left: 5px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    </style>

    <!-- Enhanced Logout Functionality with SweetAlert2 -->
    <script>
    // Enhanced logout function with SweetAlert2
    function performLogout() {
        // Use SweetAlert2 for better user experience
        Swal.fire({
            title: 'Sign Out',
            text: 'Are you sure you want to sign out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Sign Out',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading with SweetAlert2
                Swal.fire({
                    title: 'Signing Out...',
                    text: 'Please wait while we log you out',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Start logout process
                tryLogout();
            }
        });
    }
    
    function tryLogout() {
        // Method 1: Direct window location change to simple logout
        try {
            window.location.href = '{{ route("simple.logout") }}';
        } catch (e) {
            console.error('Method 1 failed:', e);
            tryLogoutMethod2();
        }
    }
    
    function tryLogoutMethod2() {
        // Method 2: Submit form via JavaScript
        try {
            const form = document.getElementById('simple-logout-form');
            if (form) {
                form.submit();
                return;
            }
        } catch (e) {
            console.error('Method 2 failed:', e);
        }
        
        // Method 3: Fallback to standard logout
        tryLogoutMethod3();
    }
    
    function tryLogoutMethod3() {
        try {
            const fallbackForm = document.getElementById('fallback-logout-form');
            if (fallbackForm) {
                fallbackForm.submit();
                return;
            }
        } catch (e) {
            console.error('Method 3 failed:', e);
        }
        
        // Method 4: Emergency redirect
        tryEmergencyLogout();
    }
    
    function tryEmergencyLogout() {
        // Emergency method - direct navigation with cache busting
        const timestamp = new Date().getTime();
        window.location.replace('/login?emergency=1&t=' + timestamp);
    }
    
    // Handle logout errors
    function handleLogoutError(error) {
        console.error('Logout error:', error);
        
        Swal.fire({
            title: 'Logout Error',
            text: 'There was an issue logging you out. Redirecting to login page...',
            icon: 'error',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            // Force redirect to login
            const timestamp = new Date().getTime();
            window.location.replace('/login?force_logout=1&t=' + timestamp);
        });
    }
    
    // Enhanced logout for sidebar menu (if needed)
    function sidebarLogout() {
        return performLogout();
    }
    
    // Add SweetAlert2 fallback if not loaded
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 not loaded, using native confirm dialog');
        
        // Fallback function
        function performLogout() {
            if (confirm('Are you sure you want to sign out?')) {
                // Simple notification
                console.log('Signing out...');
                tryLogout();
            }
        }
    }
    </script>

    <!-- Mobile Wallet Modal -->
    <div class="modal fade" id="mobileWalletModal" tabindex="-1" aria-labelledby="mobileWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="mobileWalletModalLabel">
                        <i class="fe fe-credit-card me-2"></i>Wallet Actions
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- 2x2 Grid for Wallet Actions -->
                    <div class="row g-3 mb-4">
                        <!-- Buy View Access -->
                        <div class="col-6">
                            <a href="{{ route('invest.index') }}" class="wallet-action-card text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm wallet-card">
                                    <div class="card-body text-center p-3">
                                        <div class="wallet-icon mb-2">
                                            <i class="fe fe-play-circle"></i>
                                        </div>
                                        <h6 class="card-title mb-0">Buy View</h6>
                                        <small class="text-muted">Access</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Buy Lottery Ticket -->
                        <div class="col-6">
                            <a href="{{ route('lottery.unified.index') }}" class="wallet-action-card text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm wallet-card">
                                    <div class="card-body text-center p-3">
                                        <div class="wallet-icon mb-2">
                                            <i class="fe fe-gift"></i>
                                        </div>
                                        <h6 class="card-title mb-0">Buy Lottery</h6>
                                        <small class="text-muted">Ticket</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Withdraw -->
                        <div class="col-6"> 
                            <a href="{{ route('user.withdraw.wallet') }}" class="wallet-action-card text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm wallet-card">
                                    <div class="card-body text-center p-3">
                                        <div class="wallet-icon mb-2">
                                            <i class="fe fe-arrow-up-right"></i>
                                        </div>
                                        <h6 class="card-title mb-0">Withdraw</h6>
                                        <small class="text-muted">Funds</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Transfer -->
                        <div class="col-6">
                            <a href="{{ route('user.transfer_funds') }}" class="wallet-action-card text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm wallet-card">
                                    <div class="card-body text-center p-3">
                                        <div class="wallet-icon mb-2">
                                            <i class="fe fe-send"></i>
                                        </div>
                                        <h6 class="card-title mb-0">Transfer</h6>
                                        <small class="text-muted">Money</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Ticket and Referral Section -->
                    <div class="wallet-additional-section">
                        <hr class="my-3">
                        
                        <!-- Support Ticket -->
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="wallet-feature-icon">
                                            <i class="fe fe-help-circle text-info"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-1">Support Ticket</h6>
                                        <small class="text-muted">Get help and support</small>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('user.support.create') }}" class="btn btn-sm btn-outline-info">
                                            <i class="fe fe-plus me-1"></i>Create
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Referral Link Share -->
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="wallet-feature-icon">
                                            <i class="fe fe-share-2 text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-1">Referral Link</h6>
                                        <small class="text-muted">Share and earn rewards</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="shareReferralLink()">
                                            <i class="fe fe-share me-1"></i>Share
                                        </button>
                                    </div>
                                </div>
                                @auth
                                <div class="mt-2">
                                    <small class="text-muted d-block">Your link:</small>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" 
                                               id="referralLinkInput"
                                               value="{{ url('/register?ref=' . auth()->user()->username) }}" 
                                               readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyReferralLink()">
                                            <i class="fe fe-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Modal Styles -->
    <style>
    .wallet-action-card .wallet-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .wallet-action-card:hover .wallet-card {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }
    
    .wallet-action-card:hover .wallet-card .card-title,
    .wallet-action-card:hover .wallet-card .text-muted {
        color: white !important;
    }
    
    .wallet-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 24px;
        transition: all 0.3s ease;
    }
    
    .wallet-action-card:hover .wallet-icon {
        background: rgba(255,255,255,0.2);
        transform: scale(1.1);
    }
    
    .wallet-feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(0,123,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .wallet-additional-section .card {
        transition: all 0.3s ease;
    }
    
    .wallet-additional-section .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    #mobileWalletModal .modal-content {
        border-radius: 15px;
        border: none;
        overflow: hidden;
    }
    
    #mobileWalletModal .modal-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
    }
    
    @media (max-width: 576px) {
        #mobileWalletModal .modal-dialog {
            margin: 1rem;
        }
        
        .wallet-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }
        
        .wallet-action-card .card-body {
            padding: 1rem !important;
        }
    }
    </style>

    <!-- Wallet Modal JavaScript -->
    <script>
    function shareReferralLink() {
        const referralLink = document.getElementById('referralLinkInput').value;
        
        if (navigator.share) {
            navigator.share({
                title: 'Join PayPerViews',
                text: 'Join me on PayPerViews and start earning!',
                url: referralLink
            }).catch(err => console.log('Error sharing:', err));
        } else {
            // Fallback: copy to clipboard
            copyReferralLink();
        }
    }
    
    function copyReferralLink() {
        const input = document.getElementById('referralLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        
        try {
            document.execCommand('copy');
            
            // Show success feedback
            const button = event.target.closest('button');
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fe fe-check text-success"></i>';
            
            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 2000);
            
            // Show toast if available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Referral link copied to clipboard',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    }
    </script>

    <!-- Mobile Profile Modal -->
    <div class="modal fade" id="mobileProfileModal" tabindex="-1" aria-labelledby="mobileProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="mobileProfileModalLabel">
                        <i class="fe fe-user me-2"></i>Profile Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- User Info Header -->
                    <div class="profile-header p-4 bg-light border-bottom">
                        <div class="d-flex align-items-center">
                            @auth
                                <img src="{{ Auth::user()->avatar_url }}" alt="Profile" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <img src="{{ asset('assets/images/users/16.jpg') }}" alt="Profile" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            @endauth
                            <div>
                                <h6 class="mb-1 fw-bold">@auth{{ Auth::user()->username }}@endauth</h6>
                                <small class="text-muted">@auth{{ Auth::user()->email }}@endauth</small>
                                <div class="mt-1">
                                    <span class="badge bg-success">
                                        Balance: $@auth{{ getAmount(auth()->user()->deposit_wallet+auth()->user()->interest_wallet) }}@endauth
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Actions -->
                    <div class="profile-actions">
                        <!-- Account Section -->
                        <div class="action-section">
                            <h6 class="section-title px-4 py-2 mb-0 bg-light border-bottom">
                                <i class="fe fe-user me-2"></i>Account
                            </h6>
                            <a href="{{ route('profile.index') }}" class="profile-action-item">
                                <i class="fe fe-user text-primary"></i>
                                <span>My Profile</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="profile-action-item">
                                <i class="fe fe-settings text-info"></i>
                                <span>Account Settings</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                            <a href="javascript:void(0);" class="profile-action-item" data-bs-toggle="modal" data-bs-target="#changepasswordnmodal">
                                <i class="fe fe-lock text-warning"></i>
                                <span>Change Password</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                        </div>

                        <!-- Financial Section -->
                        <div class="action-section">
                            <h6 class="section-title px-4 py-2 mb-0 bg-light border-bottom">
                                <i class="fe fe-dollar-sign me-2"></i>Financial
                            </h6>
                            <a href="{{ route('deposit.index') }}" class="profile-action-item">
                                <i class="fe fe-plus-circle text-success"></i>
                                <span>Make Deposit</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                            <a href="{{ route('user.withdraw') }}" class="profile-action-item">
                                <i class="fe fe-minus-circle text-danger"></i>
                                <span>Withdraw Funds</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                            <a href="{{ route('deposit.history') }}" class="profile-action-item">
                                <i class="fe fe-list text-info"></i>
                                <span>Transaction History</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                        </div>

                        <!-- Activity Section -->
                        <div class="action-section">
                            <h6 class="section-title px-4 py-2 mb-0 bg-light border-bottom">
                                <i class="fe fe-activity me-2"></i>Activity
                            </h6>
                            <a href="{{ route('user.video-views.index') }}" class="profile-action-item">
                                <i class="fe fe-play-circle text-purple"></i>
                                <span>Video Views</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                            <a href="{{ route('user.refferral-history') }}" class="profile-action-item">
                                <i class="fe fe-users text-success"></i>
                                <span>Referral Earnings</span>
                                <span class="badge bg-success-transparent ms-auto">Active</span>
                            </a>
                            <a href="{{ route('user.support.index') }}" class="profile-action-item">
                                <i class="fe fe-headphones text-orange"></i>
                                <span>Support Center</span>
                                <i class="fe fe-chevron-right ms-auto"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3">
                    <a href="javascript:void(0);" class="btn btn-danger w-100" onclick="performLogout()">
                        <i class="fe fe-power me-2"></i>Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Modal -->
    <div class="modal fade" id="mobileMenuModal" tabindex="-1" aria-labelledby="mobileMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-dark text-white">
                    <h5 class="modal-title" id="mobileMenuModalLabel">
                        <i class="fe fe-menu me-2"></i>Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Main Menu Items -->
                    <div class="menu-items">
                        <a href="{{ route('user.dashboard') }}" class="menu-action-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="fe fe-home text-primary"></i>
                            <span>Dashboard</span>
                            @if(request()->routeIs('user.dashboard'))
                                <span class="badge bg-primary ms-auto">Current</span>
                            @endif
                        </a>
                        <a href="{{ route('user.video-views.index') }}" class="menu-action-item {{ request()->routeIs('user.video-views.*') ? 'active' : '' }}">
                            <i class="fe fe-play-circle text-success"></i>
                            <span>Video Views</span>
                            @if(request()->routeIs('user.video-views.*'))
                                <span class="badge bg-success ms-auto">Current</span>
                            @endif
                        </a>
                        <a href="{{ route('invest.index') }}" class="menu-action-item">
                            <i class="fe fe-trending-up text-warning"></i>
                            <span>Investment Plans</span>
                        </a>
                        <a href="{{ route('lottery.unified.index') }}" class="menu-action-item">
                            <i class="fe fe-gift text-info"></i>
                            <span>Lottery</span>
                        </a>
                        
                        <div class="menu-divider"></div>
                        
                        <a href="{{ route('user.messages') }}" class="menu-action-item">
                            <i class="fe fe-mail text-primary"></i>
                            <span>Messages</span>
                            @auth
                                @php
                                    $unreadMessagesCount = \App\Models\Message::where('to_user_id', auth()->id())->where('is_read', false)->count();
                                @endphp
                                @if($unreadMessagesCount > 0)
                                    <span class="badge bg-danger ms-auto">{{ $unreadMessagesCount }}</span>
                                @endif
                            @endauth
                        </a>
                        <a href="{{ route('user.notifications.index') }}" class="menu-action-item">
                            <i class="fe fe-bell text-warning"></i>
                            <span>Notifications</span>
                            @auth
                                @php
                                    $unreadNotificationsCount = \App\Models\UserNotification::where('user_id', auth()->id())->where('read', false)->count();
                                @endphp
                                @if($unreadNotificationsCount > 0)
                                    <span class="badge bg-warning ms-auto">{{ $unreadNotificationsCount }}</span>
                                @endif
                            @endauth
                        </a>
                        <a href="{{ route('user.support.index') }}" class="menu-action-item">
                            <i class="fe fe-headphones text-info"></i>
                            <span>Support</span>
                        </a>
                        
                        <div class="menu-divider"></div>
                        
                        <a href="{{ route('deposit.index') }}" class="menu-action-item">
                            <i class="fe fe-plus-circle text-success"></i>
                            <span>Make Deposit</span>
                        </a>
                        <a href="{{ route('user.withdraw') }}" class="menu-action-item">
                            <i class="fe fe-minus-circle text-danger"></i>
                            <span>Withdraw Funds</span>
                        </a>
                        <a href="{{ route('user.transfer_funds') }}" class="menu-action-item">
                            <i class="fe fe-send text-primary"></i>
                            <span>Transfer Money</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Modal Styles -->
    <style>
    /* Profile Modal Styles */
    .profile-action-item,
    .menu-action-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: #495057;
        text-decoration: none;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.3s ease;
    }

    .profile-action-item:hover,
    .menu-action-item:hover {
        background-color: #f8f9fa;
        color: #007bff;
        text-decoration: none;
    }

    .profile-action-item i,
    .menu-action-item i {
        width: 24px;
        text-align: center;
        margin-right: 15px;
        font-size: 16px;
    }

    .menu-action-item.active {
        background-color: #e3f2fd;
        color: #1976d2;
        border-left: 4px solid #1976d2;
    }

    .menu-divider {
        height: 8px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }

    .section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Mobile Modal Responsive */
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 10px;
        }

        .profile-action-item,
        .menu-action-item {
            padding: 12px 15px;
        }

        .profile-action-item i,
        .menu-action-item i {
            margin-right: 12px;
            font-size: 14px;
        }
    }
    </style>

    <!-- Mobile Modal JavaScript -->
    <script>
    function openMobileModal(section) {
        let modalId;
        
        switch(section) {
            case 'profile':
                modalId = 'mobileNavModal'; // This modal contains the profile content
                break;
            case 'menu':
                modalId = 'mobileMenuModal'; // This is the menu modal
                break;
            default:
                console.log('Unsupported modal section:', section);
                return;
        }
        
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.log('Modal not found:', modalId, 'for section:', section);
            return;
        }
        
        try {
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Successfully opened mobile modal:', section, 'with ID:', modalId);
        } catch (error) {
            console.error('Error opening modal:', error);
        }
    }
    </script>

</body>

</html>