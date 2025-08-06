<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head> 

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    
    <!-- Apple Touch Icons (PNG format required) -->
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/payperviews-apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/payperviews-apple-touch-icon.png') }}">
    
    <!-- Android Chrome Icons -->
    {{-- <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/images/logo/payperviews-android-192.png') }}"> --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/icon-fonts/bootstrap-icons/icons/docs/static/assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/icon-fonts/bootstrap-icons/icons/docs/static/assets/img/favicons/favicon-16x16.png') }}">
    
    <title> @yield('top_title') - PayPerViews </title>
    <meta name="description" content="PayPerViews - Earn Money Watching Videos | Professional Video Earning Platform with Lottery System">
    <meta name="Author" content="PayPerViews Team">
	<meta name="keywords" content="video earning,watch videos,earn money,payperviews,lottery system,referral earnings,online income,video platform,earning dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Auto-refresh meta tag for CSRF token freshness -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- PayPerViews Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/images/logo/payperviews-icon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>
    
    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" >

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet" >

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" >

</head>

<body>
    @yield('content')
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- Show Password JS -->
    <script src="{{ asset('assets/js/show-password.js')}}"></script>

</body>

</html>