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
    <meta name="admin-cache-enabled" content="true">
    <meta name="cache-timestamp" content="{{ time() }}">
    
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

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="{{asset('assets/libs/node-waves/waves.min.css')}}" rel="stylesheet" > 

    <!-- Simplebar Css -->
    <link href="{{asset('assets/libs/simplebar/simplebar.min.css')}}" rel="stylesheet" >
    
    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/@simonwep/pickr/themes/nano.min.css')}}">
    
    <!-- Choices Css -->
    <link rel="stylesheet" href="{{asset('assets/libs/choices.js/public/assets/styles/choices.min.css')}}">


<!-- Full Calendar CSS -->
<link rel="stylesheet" href="{{asset('assets/libs/fullcalendar/main.min.css')}}">

@stack('styles')

<!-- Notification Styles -->
<style>
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 18px;
    height: 18px;
    font-size: 10px;
    font-weight: 600;
    line-height: 18px;
    text-align: center;
    border-radius: 50%;
    z-index: 10;
}

.pulse-danger {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 8px;
    height: 8px;
    background-color: #dc3545;
    border-radius: 50%;
    animation: pulse-danger 2s infinite;
}

@keyframes pulse-danger {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.notifications-dropdown {
    position: relative;
}

.bg-light-info {
    background-color: rgba(13, 202, 240, 0.1) !important;
    border-left: 3px solid #0dcaf0 !important;
}

.list-group-item {
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.avatar.online::after,
.avatar.offline::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.avatar.online::after {
    background-color: #28a745;
}

.avatar.offline::after {
    background-color: #6c757d;
}
</style>

<!-- Immediate Global Function Definition Script -->
<script>
// Define openTestEmailModal immediately to prevent ReferenceError
window.openTestEmailModal = window.openTestEmailModal || function() {
    console.log('openTestEmailModal called (early definition)');
    
    // If main implementation is loaded, use it
    if (window.showTestEmailModal && typeof window.showTestEmailModal === 'function') {
        window.showTestEmailModal();
    } else {
        // Fallback to settings page
        console.log('Fallback: redirecting to general settings');
        window.location.href = '{{ route("admin.settings.general") }}#email-tab';
    }
};

// Make it globally accessible without window prefix
if (typeof openTestEmailModal === 'undefined') {
    function openTestEmailModal() {
        return window.openTestEmailModal();
    }
}

console.log('Early openTestEmailModal definition loaded');
</script>

</head>

<body>

    <!-- Start Switcher -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel" style="display: none;">
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
                            <a href="{{ route('admin.dashboard') }}" class="header-logo">
                                <img src="{{ adminLogo() }}" alt="logo" class="desktop-logo">
                                <img src="{{ adminLogo() }}" alt="logo" class="toggle-logo">
                                <img src="{{ adminLogo() }}" alt="logo" class="desktop-dark">
                                <img src="{{ adminLogo() }}" alt="logo" class="toggle-dark">
                                <img src="{{ adminLogo() }}" alt="logo" class="desktop-white">
                                <img src="{{ adminLogo() }}" alt="logo" class="toggle-white">
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
                            <form class="form-inline d-none d-lg-block">
                                <div class="search-element">
                                    <input type="search" class="form-control header-search" placeholder="Search…" aria-label="Search" tabindex="1">
                                    <button class="btn" >
                                        <i class="fe fe-search"></i>
                                    </button>
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

                    <!-- Start::header-element -->
                        <div class="header-element header-search d-lg-none">
                            <!-- Start::header-link -->
                            <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown">
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                            </a>
            
                            <ul class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                                <li>
                                    <span class="dropdown-item d-flex align-items-center" >
                                        <span class="input-group">
                                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search input" aria-describedby="button-addon2">
                                            <button class="btn btn-primary" type="button" id="button-addon2">Search</button>
                                        </span>
                                    </span>
                                </li>
                            </ul>
            
                            <!-- End::header-link -->
                        </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
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
                    <div class="header-element notifications-dropdown">
                        <!-- Start::header-link|notification-rights -->
                        <a href="javascript:void(0);" class="header-link" data-bs-toggle="offcanvas" data-bs-target="#notification-sidebar-canvas" id="messageDropdown">
                            <i class="fe fe-bell header-link-icon"></i>
                            @php 
                                try {
                                    $unreadNotifications = \App\Models\AdminNotification::where('read', false)->count();
                                } catch (\Exception $e) {
                                    $unreadNotifications = 0;
                                }
                            @endphp 
                            @if($unreadNotifications > 0)
                                <span class="pulse-danger"></span>
                                <span class="badge bg-danger rounded-pill notification-badge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                            @endif
                        </a>
                        <!-- End::header-link|notification-rights -->
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element main-header-profile">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle mx-0 w-100" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div>
                                <img src="{{ adminLogo() }}" alt="Admin Avatar" class="rounded-3 avatar avatar-md" style="object-fit: cover;">
                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <Li>
                                <div class="p-3 text-center border-bottom">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <img src="{{ adminLogo() }}" alt="Admin Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <a href="{{ route('admin.dashboard') }}" class="text-center fw-semibold text-decoration-none">{{ Session::get('name') ?? auth()->guard('admin')->user()->name ?? 'Admin' }}</a>
                                            <p class="text-center user-semi-title fs-13 mb-0 text-muted">{{ Session::get('username') ?? auth()->guard('admin')->user()->username ?? 'admin' }}</p>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fe fe-shield me-1"></i>{{ ucfirst(auth()->guard('admin')->user()->role ?? 'admin') }}
                                        @if(auth()->guard('admin')->user()?->is_super_admin)
                                            <span class="badge bg-warning-transparent ms-1">Super Admin</span>
                                        @endif
                                    </small>
                                </div>
                            </Li>
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.dashboard') }}"><i class="fe fe-user me-2"></i>My Profile</a></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.notifications.index') ?? route('admin.dashboard') }}">
                                    <i class="fe fe-mail me-2"></i>Notifications 
                                    @php 
                                        try {
                                            $unreadCount = \App\Models\AdminNotification::where('read_at', null)->count();
                                        } catch (\Exception $e) {
                                            $unreadCount = 0;
                                        }
                                    @endphp 
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.settings.general') }}"><i class="fe fe-settings me-2"></i>System Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changepasswordnmodal"><i class="fe fe-edit-3 me-2"></i>Change Password</a></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.support.index') ?? route('admin.dashboard') }}">
                                    <i class="fe fe-headphones me-2"></i>Support Center
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li> 
                            <li>
                                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;" id="logoutForm">
                                    @csrf
                                    <button type="button" class="dropdown-item d-flex align-items-center text-danger" style="background: none; border: none; width: 100%; text-align: left;" onclick="handleAdminLogoutWithCacheClearing(event);" ondblclick="emergencyAdminLogoutWithCacheClearing();" title="Click to logout with cache clearing, Double-click for immediate logout">
                                        <i class="fe fe-power me-2"></i>Log Out
                                        <small class="text-muted ms-2">(Clear Cache)</small>
                                    </button>
                                </form>
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
            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="header-logo"> 
                    <img src="{{ adminLogo() }}" alt="logo" class="desktop-logo">
                    <img src="{{ adminLogo() }}" alt="logo" class="toggle-logo">
                    <img src="{{ adminLogo() }}" alt="logo" class="desktop-dark">
                    <img src="{{ adminLogo() }}" alt="logo" class="toggle-dark">
                    <img src="{{ adminLogo() }}" alt="logo" class="desktop-white">
                    <img src="{{ adminLogo() }}" alt="logo" class="toggle-white">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                     <!-- Start::Sidebar User -->
                    <div class="app-sidebar__user mb-3">
                        <div class="dropdown user-pro-body text-center user-pic">
                            <span class="avatar avatar-xxl online avatar-rounded">
                                <img src="{{ adminLogo() }}" alt="Admin Avatar" style="object-fit: cover;">
                            </span>
                            <div class="user-info mt-1">
                                <h5 class=" mb-1">{{ Session::get('name') ?? auth()->guard('admin')->user()->name ?? 'Admin' }}</h5>
                                <span class="text-muted app-sidebar__user-name text-sm">{{ Session::get('username') ?? auth()->guard('admin')->user()->username ?? 'admin' }}</span>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="fe fe-shield me-1"></i>{{ ucfirst(auth()->guard('admin')->user()->role ?? 'admin') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                     <!-- End::Sidebar User -->
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>

                    <x-adminMenu />

                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->
        

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                {{ $topShow ?? '' }}
                {{ $transfer_member ?? '' }}
                @yield('breadcrumb')
                @yield('content')
            <!-- Top Dashboard -->

            </div>
        </div>
        <!-- End::app-content -->

        <!-- Footer Start -->
        <footer class="footer mt-auto py-3 shadow-none text-center">
            <div class="container">
                <span class="text-muted"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-dark fw-semibold">www.payperviews.net</a>.
                     All rights reserved
                </span>
            </div>
        </footer>
        <!-- Footer End -->
        <!-- Start Right-Sidebar -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="notification-sidebar-canvas" aria-labelledby="offcanvasRightLabel1">
            <div class="offcanvas-header border-bottom">
                @php 
                    try {
                        $unreadCount = \App\Models\AdminNotification::where('read', false)->count();
                        $totalCount = \App\Models\AdminNotification::count();
                    } catch (\Exception $e) {
                        $unreadCount = 0;
                        $totalCount = 0;
                    }
                @endphp 
                <h5 class="offcanvas-title text-default fs-17 fw-medium" id="offcanvasRightLabel1">
                    Notifications 
                    @if($unreadCount > 0)
                        <span class="badge bg-danger-transparent" id="notifiation-data">{{ $unreadCount }} Unread</span>
                    @else
                        <span class="badge bg-success-transparent" id="notifiation-data">All Read</span>
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0 sidebar-right">
                <div id="header-notification-scroll">
                    @php 
                        try {
                            $notifications = \App\Models\AdminNotification::with('admin')
                                ->orderBy('created_at', 'desc')
                                ->limit(15)
                                ->get();
                        } catch (\Exception $e) {
                            $notifications = collect();
                        }
                    @endphp 
                    
                    @forelse($notifications as $notification)
                        <div class="list-group-item align-items-center border-start-0 border-end-0 border-top-0 {{ $notification->read ? '' : 'bg-light-info' }}">
                            <div class="d-flex">
                                <span class="avatar avatar-lg me-3 avatar-rounded {{ $notification->read ? 'offline' : 'online' }}">
                                    @if($notification->type == 'user_registration')
                                        <span class="avatar bg-primary-transparent"><i class="fe fe-user-plus"></i></span>
                                    @elseif($notification->type == 'deposit')
                                        <span class="avatar bg-success-transparent"><i class="fe fe-credit-card"></i></span>
                                    @elseif($notification->type == 'withdrawal')
                                        <span class="avatar bg-warning-transparent"><i class="fe fe-arrow-up-right"></i></span>
                                    @elseif($notification->type == 'kyc_submission')
                                        <span class="avatar bg-info-transparent"><i class="fe fe-id-card"></i></span>
                                    @elseif($notification->type == 'support_ticket')
                                        <span class="avatar bg-secondary-transparent"><i class="fe fe-headphones"></i></span>
                                    @elseif($notification->type == 'transfer')
                                        <span class="avatar bg-purple-transparent"><i class="fe fe-send"></i></span>
                                    @else
                                        <span class="avatar bg-dark-transparent"><i class="fe fe-bell"></i></span>
                                    @endif
                                </span>
                                <div class="w-65">
                                    <a href="{{ $notification->action_url ?? 'javascript:void(0);' }}" class="fw-medium fs-16">
                                        {{ $notification->title ?? 'System Notification' }}
                                        @if(!$notification->read)
                                            <span class="badge bg-danger-transparent ms-1">New</span>
                                        @endif
                                    </a>
                                    <p class="text-muted mb-1 fs-13">{{ Str::limit($notification->message ?? 'No message', 60) }}</p>
                                    <span class="text-muted fs-12 ms-auto d-inline-block">
                                        <i class="mdi mdi-clock text-muted me-1"></i>
                                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Unknown time' }}
                                    </span>
                                </div>
                                <div class="ms-auto">
                                    <div class="text-end">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="fe fe-more-horizontal p-1 text-muted"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            @if(!$notification->read)
                                                <li>
                                                    <a class="dropdown-item mark-as-read" href="javascript:void(0);" data-id="{{ $notification->id }}">
                                                        <i class="fe fe-eye me-2"></i>Mark as Read
                                                    </a>
                                                </li>
                                            @endif
                                            @if($notification->action_url)
                                                <li>
                                                    <a class="dropdown-item" href="{{ $notification->action_url }}">
                                                        <i class="fe fe-external-link me-2"></i>View Details
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item text-danger delete-notification" href="javascript:void(0);" data-id="{{ $notification->id }}">
                                                    <i class="fe fe-trash-2 me-2"></i>Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 empty-item1">
                            <div class="text-center">
                                <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                                    <i class="ri-notification-off-line fs-2"></i>
                                </span>
                                <h6 class="fw-medium mt-3">No Notifications</h6>
                                <p class="text-muted">You have no new notifications at this time.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                @if($notifications->count() > 0)
                    <div class="p-3 empty-header-item1 border-top">
                        <div class="d-grid gap-2">
                            @if($unreadCount > 0)
                                <button class="btn btn-outline-primary btn-sm" id="markAllRead">
                                    <i class="fe fe-check-circle me-1"></i>Mark All as Read
                                </button>
                            @endif
                            <a href="{{ route('admin.notifications.index') ?? route('admin.dashboard') }}" class="btn btn-primary btn-sm">
                                <i class="fe fe-list me-1"></i>View All Notifications
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- End Right-Sidebar -->


        <!--Change password Modal -->
        <div class="modal fade" id="changepasswordnmodal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.change-password') }}" id="changePasswordForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="passwordError"></div>
                            <div class="alert alert-success d-none" id="passwordSuccess"></div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="current_password" placeholder="Enter current password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Enter new password" required minlength="6">
                                <small class="form-text text-muted">Password must be at least 6 characters long</small>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" id="passwordSpinner"></span>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Change password Modal  -->

        <!--Clock-IN Modal -->
        <div class="modal fade"  id="clockinmodal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="fe fe-clock  me-1"></span>Clock In</h5>
                        <button  class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="countdowntimer text-center">
                            <div class="mt-3 d-flex justify-content-center fs-30 digital-clock"></div>
                            <label class="form-label">Current Time</label>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">IP Address</label>
                            <input type="text" class="form-control" placeholder="225.192.145.1" disabled="">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Working Form</label>
                            <select name="projects"  class="form-control custom-select" data-trigger>
                                <option value="0">Select</option>
                                <option value="1">Office</option>
                                <option value="2">Home</option>
                                <option value="3">Others</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Note:</label>
                            <textarea class="form-control" rows="3">Some text here...</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Clock In</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Clock-IN Modal  -->

        <!--Apply Leaves Modal -->
        <div class="modal fade"  id="newtaskmodal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Task ID</label>
                                    <input class="form-control" placeholder="Number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Task Title</label>
                                    <input class="form-control" placeholder="Text">
                                </div>
                            </div>
                        </div>
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Department:</label>
                                    <select class="form-control custom-select" data-trigger data-placeholder="Select Department">
                                        <option value="1">Designing Department</option>
                                        <option value="2">Development Department</option>
                                        <option value="3">Marketing Department</option>
                                        <option value="4">Human Resource Department</option>
                                        <option value="5">Managers Department</option>
                                        <option value="6">Application Department</option>
                                        <option value="7">Support Department</option>
                                        <option value="8">IT Department</option>
                                        <option value="9">Technical Department</option>
                                        <option value="10">Accounts Department</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Assign To:</label>
                                    <select class="form-control custom-select" data-trigger data-placeholder="Select Department">
                                        <option value="1">Faith Harris</option>
                                        <option value="2">Austin Bell</option>
                                        <option value="3">Maria Bower</option>
                                        <option value="4">Peter Hill</option>
                                        <option value="5">Adam Quinn</option>
                                        <option value="6">Victoria Lyman</option>
                                        <option value="7">Melanie Coleman</option>
                                        <option value="8">Justin Metcalfe</option>
                                        <option value="9">Ryan Young</option>
                                        <option value="10">Jennifer Hardacre</option>
                                        <option value="12">Jennifer Hardacre</option>
                                        <option value="13">Justin Parr</option>
                                        <option value="14">Julia Hodges</option>
                                        <option value="15">Michael Sutherland</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Task Priority:</label>
                            <select class="form-control custom-select" data-trigger data-placeholder="Select Priority">
                                <option value="1">High</option>
                                <option value="2">Medium</option>
                                <option value="3">Low</option>
                            </select>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="leave-content active  mt-3" id="single">
                                    <div class="form-group">
                                        <label class="form-label">From:</label>
                                        <div class="input-group">
                                            <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                                            <input type="text" class="form-control choose-date" placeholder="DD-MM-YYYY">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="leave-content active  mt-3" id="single1">
                                    <div class="form-group">
                                        <label class="form-label">TO:</label>
                                        <div class="input-group">
                                            <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                                            <input type="text" class="form-control choose-date" placeholder="DD-MM-YYYY">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Description:</label>
                            <textarea class="form-control" id="text-area" rows="3"></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Attachment:</label>
                            <div class="form-group">
                                    <input class="form-control" type="file">
                            </div>
                        </div>
                        <div class="custom-controls-stacked d-md-flex mt-3">
                            <label class="form-label me-5">Work Status :</label>
                            <div class="form-check mb-0 me-4">
                                <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault13">
                                <label class="form-check-label" for="flexRadioDefault13">
                                    Completed
                                </label>
                            </div>
                            <div class="form-check mb-0 me-4">
                                <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault11">
                                <label class="form-check-label" for="flexRadioDefault11">
                                    Pending
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault12">
                                <label class="form-check-label" for="flexRadioDefault12">
                                    On Progress
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button  class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                        <button  class="btn btn-success successful-notify">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Apply Leaves Modal  -->
        
        <!-- Notification JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mark single notification as read
            document.querySelectorAll('.mark-as-read').forEach(function(element) {
                element.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    markNotificationAsRead(notificationId, this);
                });
            });
            
            // Mark all notifications as read
            const markAllReadBtn = document.getElementById('markAllRead');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    markAllNotificationsAsRead();
                });
            }
            
            // Delete notification
            document.querySelectorAll('.delete-notification').forEach(function(element) {
                element.addEventListener('click', function() {
                    const notificationId = this.getAttribute('data-id');
                    if (confirm('Are you sure you want to delete this notification?')) {
                        deleteNotification(notificationId, this);
                    }
                });
            });
        });
        
        function markNotificationAsRead(notificationId, element) {
            fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the "New" badge and mark as read styling
                    const listItem = element.closest('.list-group-item');
                    listItem.classList.remove('bg-light-info');
                    const newBadge = listItem.querySelector('.badge.bg-danger-transparent');
                    if (newBadge) {
                        newBadge.remove();
                    }
                    
                    // Update unread count
                    updateNotificationCount();
                    
                    // Remove mark as read button
                    element.closest('li').remove();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                alert('Failed to mark notification as read. Please try again.');
            });
        }
        
        function markAllNotificationsAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove all "New" badges and read styling
                    document.querySelectorAll('.list-group-item.bg-light-info').forEach(function(item) {
                        item.classList.remove('bg-light-info');
                    });
                    document.querySelectorAll('.badge.bg-danger-transparent').forEach(function(badge) {
                        if (badge.textContent.includes('New')) {
                            badge.remove();
                        }
                    });
                    
                    // Update header badge and count
                    const headerBadge = document.querySelector('.notification-badge');
                    if (headerBadge) {
                        headerBadge.remove();
                    }
                    
                    const pulseDanger = document.querySelector('.pulse-danger');
                    if (pulseDanger) {
                        pulseDanger.remove();
                    }
                    
                    // Update notification panel header
                    const notificationData = document.getElementById('notifiation-data');
                    if (notificationData) {
                        notificationData.textContent = 'All Read';
                        notificationData.className = 'badge bg-success-transparent';
                    }
                    
                    // Hide mark all read button
                    const markAllBtn = document.getElementById('markAllRead');
                    if (markAllBtn) {
                        markAllBtn.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
                alert('Failed to mark all notifications as read. Please try again.');
            });
        }
        
        function deleteNotification(notificationId, element) {
            fetch(`/admin/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the notification item
                    const listItem = element.closest('.list-group-item');
                    listItem.remove();
                    
                    // Update notification count
                    updateNotificationCount();
                    
                    // Check if no notifications left
                    const remainingNotifications = document.querySelectorAll('#header-notification-scroll .list-group-item').length;
                    if (remainingNotifications === 0) {
                        // Show empty state
                        const scrollContainer = document.getElementById('header-notification-scroll');
                        scrollContainer.innerHTML = `
                            <div class="p-5 empty-item1">
                                <div class="text-center">
                                    <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                                        <i class="ri-notification-off-line fs-2"></i>
                                    </span>
                                    <h6 class="fw-medium mt-3">No Notifications</h6>
                                    <p class="text-muted">You have no notifications at this time.</p>
                                </div>
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error('Error deleting notification:', error);
                alert('Failed to delete notification. Please try again.');
            });
        }
        
        function updateNotificationCount() {
            // This function would typically make an AJAX call to get updated counts
            // For now, we'll update the UI based on visible unread notifications
            const unreadItems = document.querySelectorAll('.list-group-item.bg-light-info').length;
            
            const headerBadge = document.querySelector('.notification-badge');
            const notificationData = document.getElementById('notifiation-data');
            
            if (unreadItems === 0) {
                if (headerBadge) {
                    headerBadge.remove();
                }
                const pulseDanger = document.querySelector('.pulse-danger');
                if (pulseDanger) {
                    pulseDanger.remove();
                }
                if (notificationData) {
                    notificationData.textContent = 'All Read';
                    notificationData.className = 'badge bg-success-transparent';
                }
            } else {
                if (headerBadge) {
                    headerBadge.textContent = unreadItems > 99 ? '99+' : unreadItems;
                }
                if (notificationData) {
                    notificationData.textContent = `${unreadItems} Unread`;
                    notificationData.className = 'badge bg-danger-transparent';
                }
            }
        }
        </script>
        

            <!-- Modal -->
            <div class="modal fade"  id="remindermodal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Reminder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Project</label>
                                <select class="form-control custom-select" data-trigger data-placeholder="Select Department">
                                    <option value="1">Project 01</option>
                                    <option value="2">Project 02</option>
                                    <option value="3">Project 03</option>
                                    <option value="4">Project 04</option>
                                    <option value="5">Project 05</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label class="form-label">Select Date</label>
                                <div class="input-group">
                                    <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
                                    <input type="text" class="form-control choose-date flatpickr-input" placeholder="DD-MM-YYYY" readonly="readonly">
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="form-label">Note:</label>
                                <textarea class="form-control" rows="3">Some text here...</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->

    </div>

    
    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="fe fe-chevrons-up"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

    <!-- jQuery JS (Required for many components) -->
    <script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>

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


    <!-- Apex Charts JS -->
    <script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>

    <!-- Chartjs Chart JS -->
    <script src="{{asset('assets/libs/chart.js/chart.min.js')}}"></script>

    <!-- Date & Time Picker JS -->
    <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>

    <!-- Default date picker js-->
    <script src="{{asset('assets/js/default-flat-datepicker.js')}}"></script> 

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    
    <!-- Admin Cache Manager JS -->
    <script src="{{asset('assets/js/admin-cache-manager.js')}}"></script> 

    <!-- Admin Auto-Refresh Prevention JS -->
    <script src="{{asset('assets/js/admin-auto-refresh-prevention.js')}}"></script> 

    <!-- Index2 js-->
    <script src="{{asset('assets/js/index3.js')}}"></script>

    <!-- Notifications JS -->
    <script src="{{asset('assets/libs/awesome-notifications/index.var.js')}}"></script>

    <!-- Successful-notify JS -->
    <script src="{{asset('assets/js/successful-notify.js')}}"></script>

    
    <!-- Custom-Switcher JS -->
    <script src="{{asset('assets/js/custom-switcher.min.js')}}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets/js/custom.js')}}"></script>
    
    <!-- Debug: Verify SweetAlert2 loading -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Main Layout DOMContentLoaded');
            if (typeof Swal !== 'undefined') {
                console.log('✅ SweetAlert2 loaded successfully in main layout');
            } else {
                console.error('❌ SweetAlert2 failed to load in main layout');
            }
        });
    </script>
    
    <!-- Advanced Popup System -->
    <script src="{{asset('assets/js/advanced-popup-system.js')}}"></script>
    
    @stack('script')
    @yield('pageJsScripts')
    
    <!-- Initialize Popup System for Admin -->
    <script>
    // Define global functions immediately (not waiting for document ready)
    window.showCreateDrawModal = function() {
        console.log('showCreateDrawModal called');
        
        // Create modal for new draw creation
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fe fe-plus me-2"></i>🎰 Create New Lottery Draw
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fe fe-info me-2"></i>
                            <strong>Choose your creation method:</strong> Auto-generate for quick setup or manual configuration for custom draws.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i class="fe fe-zap" style="font-size: 2.5rem; color: #198754;"></i>
                                        <h6 class="mt-2">Auto Generate</h6>
                                        <p class="text-muted small">Quick setup with default settings</p>
                                        <button class="btn btn-success btn-sm w-100" onclick="generateAutoDraw('standard')">
                                            <i class="fe fe-zap me-1"></i>Generate Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i class="fe fe-edit-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                                        <h6 class="mt-2">Manual Configuration</h6>
                                        <p class="text-muted small">Full control over all settings</p>
                                        <button class="btn btn-primary btn-sm w-100" onclick="goToManualCreate()">
                                            <i class="fe fe-settings me-1"></i>Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <i class="fe fe-users" style="font-size: 2.5rem; color: #ffc107;"></i>
                                        <h6 class="mt-2">Winner Control</h6>
                                        <p class="text-muted small">Manual winner selection</p>
                                        <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-warning btn-sm w-100">
                                            <i class="fe fe-edit-3 me-1"></i>Select Winners
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="text-muted">Recent Draws:</h6>
                            <div id="recent-draws-preview">
                                <div class="d-flex align-items-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading recent draws...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list me-1"></i>View All Draws
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Check if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        } else {
            console.error('Bootstrap is not loaded');
            alert('Unable to open modal: Bootstrap is not loaded');
            return;
        }
        
        // Load recent draws preview
        setTimeout(() => {
            const previewElement = document.getElementById('recent-draws-preview');
            if (previewElement) {
                previewElement.innerHTML = `
                    <div class="small text-muted">
                        <i class="fe fe-info me-1"></i>
                        Choose auto-generate for instant setup, manual configuration for custom prizes and settings, or winner control for specific ticket selection.
                    </div>
                `;
            }
        }, 1000);
        
        // Clean up modal when closed
        modal.addEventListener('hidden.bs.modal', function() {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        });
    };

    // Function to go to manual create page
    window.goToManualCreate = function() {
        console.log('goToManualCreate called');
        window.location.href = '{{ route("admin.lottery.draws") }}?action=create';
    };

    // Function to generate auto draw
    window.generateAutoDraw = function(type) {
        console.log('generateAutoDraw called with type:', type);
        // Add your auto draw generation logic here
        alert('Auto draw generation feature will be implemented soon!');
    };
    
    // Debug: Log that functions are defined
    console.log('Global admin functions defined:', {
        showCreateDrawModal: typeof window.showCreateDrawModal,
        goToManualCreate: typeof window.goToManualCreate,
        generateAutoDraw: typeof window.generateAutoDraw
    });

    $(document).ready(function() {
        // Initialize the advanced popup system
        if (typeof AdvancedPopupSystem !== 'undefined') {
            const popupSystem = new AdvancedPopupSystem();
            console.log('Admin popup system initialized');
        } else {
            console.error('AdvancedPopupSystem not found');
        }
        
        console.log('Global admin functions initialized successfully');
    });
    </script>
    
    <script>
    let inactivityTime = function () {
        let timer;
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
        document.onclick = resetTimer;
        document.onscroll = resetTimer;

        function logout() {
            @if(auth()->guard('admin')->check())
                // Create POST form for admin logout
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.logout') }}";
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            @else
                // Create POST form for user logout
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('logout') }}";
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            @endif
        }

        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(logout, 15 * 60 * 1000); // 15 minutes
        }
    };
    inactivityTime();
    
    // Change Password JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const changePasswordForm = document.getElementById('changePasswordForm');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const passwordSpinner = document.getElementById('passwordSpinner');
        const passwordError = document.getElementById('passwordError');
        const passwordSuccess = document.getElementById('passwordSuccess');

        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset alerts
                passwordError.classList.add('d-none');
                passwordSuccess.classList.add('d-none');
                
                // Show spinner
                passwordSpinner.classList.remove('d-none');
                changePasswordBtn.disabled = true;
                
                // Get form data
                const formData = new FormData(changePasswordForm);
                
                // Validate passwords match
                const password = formData.get('password');
                const passwordConfirmation = formData.get('password_confirmation');
                
                if (password !== passwordConfirmation) {
                    passwordError.textContent = 'Passwords do not match';
                    passwordError.classList.remove('d-none');
                    passwordSpinner.classList.add('d-none');
                    changePasswordBtn.disabled = false;
                    return;
                }
                
                if (password.length < 6) {
                    passwordError.textContent = 'Password must be at least 6 characters long';
                    passwordError.classList.remove('d-none');
                    passwordSpinner.classList.add('d-none');
                    changePasswordBtn.disabled = false;
                    return;
                }
                
                // Submit form
                fetch(changePasswordForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    passwordSpinner.classList.add('d-none');
                    changePasswordBtn.disabled = false;
                    
                    if (data.success) {
                        passwordSuccess.textContent = data.message || 'Password changed successfully!';
                        passwordSuccess.classList.remove('d-none');
                        changePasswordForm.reset();
                        
                        // Close modal after 2 seconds
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('changepasswordnmodal')).hide();
                            window.location.reload();
                        }, 2000);
                    } else {
                        passwordError.textContent = data.message || 'An error occurred. Please try again.';
                        passwordError.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    passwordSpinner.classList.add('d-none');
                    changePasswordBtn.disabled = false;
                    passwordError.textContent = 'An error occurred. Please try again.';
                    passwordError.classList.remove('d-none');
                });
            });
        }
    });
    
    // Admin Logout Confirmation
    function confirmLogout() {
        Swal.fire({
            title: 'Logout Confirmation',
            text: 'Are you sure you want to log out?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fe fe-log-out me-2"></i>Yes, logout',
            cancelButtonText: '<i class="fe fe-x me-2"></i>Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            buttonsStyling: true,
            customClass: {
                confirmButton: 'swal2-confirm-logout',
                cancelButton: 'swal2-cancel-logout',
                popup: 'swal2-logout-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state with SweetAlert
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait while we log you out securely',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Show loading state on button as well
                const logoutBtn = document.querySelector('#logoutForm button');
                if (logoutBtn) {
                    logoutBtn.innerHTML = '<i class="fe fe-loader me-2 spin"></i>Logging out...';
                    logoutBtn.disabled = true; 
                }
                
                // Submit the form
                document.getElementById('logoutForm').submit();
            }
        });
    }
    
    // Add CSS for spinning loader
    const style = document.createElement('style');
    style.textContent = `
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Custom SweetAlert2 Logout Styles */
        .swal2-logout-popup {
            border-radius: 8px !important;
        }
        .swal2-confirm-logout {
            padding: 8px 20px !important;
            font-weight: 500 !important;
            border-radius: 5px !important;
            margin: 0 5px !important;
        }
        .swal2-cancel-logout {
            padding: 8px 20px !important;
            font-weight: 500 !important;
            border-radius: 5px !important;
            margin: 0 5px !important;
        }
        .swal2-actions {
            gap: 10px !important;
        }
    `;
    document.head.appendChild(style);
    </script>

    <!-- Page Specific Scripts -->
    @yield('pageJsScripts')
    
    <!-- Stacked Scripts -->
    @stack('scripts')
    
    <!-- Test Email Modal - Global -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailModalLabel">📧 Test Email System</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="testEmailForm" action="{{ route('admin.settings.test-email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="test_email" class="form-label">
                                <i class="fe fe-mail me-2"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="test_email" name="test_email" 
                                   placeholder="Enter email address to test" required>
                            <div class="form-text">
                                <i class="fe fe-info me-1"></i>A test email will be sent to verify your email configuration
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="test_subject" class="form-label">
                                <i class="fe fe-edit me-2"></i>Subject (Optional)
                            </label>
                            <input type="text" class="form-control" id="test_subject" name="test_subject" 
                                   placeholder="Test email subject">
                        </div>
                        
                        <div class="mb-3">
                            <label for="test_message" class="form-label">
                                <i class="fe fe-message-square me-2"></i>Message (Optional)
                            </label>
                            <textarea class="form-control" id="test_message" name="test_message" 
                                      rows="3" placeholder="Test email message"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fe fe-info me-2"></i>
                            <strong>Note:</strong> This will send a test email using your current email configuration.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fe fe-x me-2"></i>Cancel
                    </button>
                    <button type="submit" form="testEmailForm" class="btn btn-primary" id="testEmailSubmitBtn">
                        <i class="fe fe-send me-2"></i>Send Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Email System JavaScript - Global Scope -->
    <script>
    console.log('Loading test email modal system...');
    
    // Define functions immediately in global scope to prevent reference errors
    window.openTestEmailModal = function() {
        console.log('openTestEmailModal called');
        
        try {
            // Check if the showTestEmailModal function exists (modal available)
            if (typeof window.showTestEmailModal === 'function') {
                console.log('Using main layout modal');
                window.showTestEmailModal();
                return;
            } else {
                console.log('showTestEmailModal not found, trying fallback');
            }
            
            // Try to show the modal directly if it exists
            const testEmailModal = document.getElementById('testEmailModal');
            if (testEmailModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log('Using Bootstrap modal directly');
                const modal = new bootstrap.Modal(testEmailModal);
                modal.show();
                return;
            }
            
            console.log('Modal element not found or Bootstrap not available');
            // Fallback: redirect to general settings page with email tab
            console.log('Fallback: redirecting to general settings');
            window.location.href = "{{ route('admin.settings.general') }}#email-tab";
        } catch (error) {
            console.error('Error in openTestEmailModal:', error);
            // Final fallback
            try {
                window.location.href = "{{ route('admin.settings.general') }}#email-tab";
            } catch(e) {
                console.error('Fallback redirect failed:', e);
                window.location.href = "{{ url('admin/settings/general') }}#email-tab";
            }
        }
    };
    
    // Global function for showing test email modal
    window.showTestEmailModal = function() {
        console.log('showTestEmailModal function called');
        
        // Show the modal
        const modalElement = document.getElementById('testEmailModal');
        const modal = new bootstrap.Modal(modalElement);
        
        // Add event listener for when modal is hidden to ensure cleanup
        modalElement.addEventListener('hidden.bs.modal', function() {
            // Force cleanup of any remaining backdrop or body styles
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            console.log('Modal cleanup completed');
        });
        
        modal.show();
        
        // Pre-fill with default values
        setTimeout(() => {
            const emailInput = document.getElementById('test_email');
            const subjectInput = document.getElementById('test_subject');
            const messageInput = document.getElementById('test_message');
            
            // Try to get email from settings page input if available
            const settingsEmailInput = document.getElementById('test_email_input');
            if (emailInput && settingsEmailInput && settingsEmailInput.value.trim()) {
                emailInput.value = settingsEmailInput.value.trim();
            }
            
            if (subjectInput && !subjectInput.value) {
                subjectInput.value = 'Test Email from Email Automation System';
            }
            
            if (messageInput && !messageInput.value) {
                messageInput.value = 'This is a test email from the Email Automation System. If you receive this, your email configuration is working correctly!\n\nSent at: ' + new Date().toLocaleString();
            }
            
            // Focus on email input if empty, otherwise focus on subject
            if (emailInput) {
                if (!emailInput.value) {
                    emailInput.focus();
                } else if (subjectInput) {
                    subjectInput.focus();
                }
            }
        }, 100);
    };
    
    // Fallback function definition for compatibility
    if (typeof showTestEmailModal === 'undefined') {
        function showTestEmailModal() {
            return window.showTestEmailModal();
        }
    }
    
    // Also create a local alias for backwards compatibility
    if (typeof openTestEmailModal === 'undefined') {
        function openTestEmailModal() {
            return window.openTestEmailModal();
        }
    }
    
    // Handle form submission
    document.addEventListener('DOMContentLoaded', function() {
        // Test Email Menu Item Event Listener
        const testEmailMenuItem = document.getElementById('testEmailMenuItem');
        if (testEmailMenuItem) {
            console.log('Test email menu item found, attaching click handler');
            testEmailMenuItem.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Test email menu item clicked');
                
                // Try multiple approaches to open the test email modal
                try {
                    if (typeof window.openTestEmailModal === 'function') {
                        console.log('Using window.openTestEmailModal');
                        window.openTestEmailModal();
                    } else if (typeof openTestEmailModal === 'function') {
                        console.log('Using openTestEmailModal');
                        openTestEmailModal();
                    } else if (typeof window.showTestEmailModal === 'function') {
                        console.log('Using window.showTestEmailModal');
                        window.showTestEmailModal();
                    } else {
                        console.log('No modal function found, redirecting to settings');
                        // Fallback: redirect to general settings page with email tab
                        try {
                            window.location.href = "{{ route('admin.settings.general') }}#email-tab";
                        } catch(e) {
                            console.error('Redirect error:', e);
                            window.location.href = "{{ url('admin/settings/general') }}#email-tab";
                        }
                    }
                } catch (error) {
                    console.error('Error opening test email modal:', error);
                    try {
                        window.location.href = "{{ route('admin.settings.general') }}#email-tab";
                    } catch(e) {
                        console.error('Fallback redirect error:', e);
                        window.location.href = "{{ url('admin/settings/general') }}#email-tab";
                    }
                }
            });
        } else {
            console.log('Test email menu item not found on this page');
        }
        
        // Test Email Form Handler
        const testEmailForm = document.getElementById('testEmailForm');
        if (testEmailForm) {
            testEmailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const emailInput = document.getElementById('test_email');
                // Find submit button by ID
                const submitBtn = document.getElementById('testEmailSubmitBtn');
                
                // Better validation - check if element exists and has value after trimming
                if (!emailInput) {
                    alert('Email input field not found');
                    return;
                }
                
                const emailValue = emailInput.value.trim();
                if (!emailValue) {
                    alert('Please enter an email address');
                    emailInput.focus();
                    return;
                }
                
                // Basic email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailValue)) {
                    alert('Please enter a valid email address');
                    emailInput.focus();
                    return;
                }
                
                console.log('Submitting test email form to:', emailValue);
                console.log('Submit button found:', submitBtn);
                
                // Show loading state if button is found
                let originalText = '';
                if (submitBtn) {
                    originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fe fe-loader me-2 fa-spin"></i>Sending...';
                    submitBtn.disabled = true;
                }
                
                // Submit form
                fetch(testEmailForm.action, {
                    method: 'POST',
                    body: new FormData(testEmailForm),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    
                    if (data.success) {
                        // Close modal first to prevent blur
                        const modalElement = document.getElementById('testEmailModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        // Remove any backdrop manually if it exists
                        setTimeout(() => {
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) {
                                backdrop.remove();
                            }
                            
                            // Remove modal-open class from body
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            
                            // Show success message after modal is closed
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message || 'Test email sent successfully to ' + emailInput.value,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false,
                                    allowOutsideClick: true,
                                    allowEscapeKey: true
                                });
                            } else {
                                alert(data.message || 'Test email sent successfully to ' + emailInput.value);
                            }
                        }, 300);
                        
                        // Reset form
                        testEmailForm.reset();
                    } else {
                        throw new Error(data.message || 'Failed to send test email');
                    }
                })
                .catch(error => {
                    console.error('Test email error:', error);
                    
                    // Ensure modal backdrop is removed on error too
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to send test email: ' + error.message,
                            icon: 'error',
                            allowOutsideClick: true,
                            allowEscapeKey: true
                        });
                    } else {
                        alert('Failed to send test email: ' + error.message);
                    }
                })
                .finally(() => {
                    // Restore button state if button was found
                    if (submitBtn && originalText) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                });
            });
        }
    });
    
    console.log('Test email modal system loaded. Functions available:');
    console.log('- showTestEmailModal:', typeof window.showTestEmailModal);
    console.log('- openTestEmailModal:', typeof window.openTestEmailModal);
    
    // Make function globally accessible for menu items
    if (typeof window.showTestEmailModal !== 'undefined' && typeof window.openTestEmailModal !== 'undefined') {
        console.log('✅ All test email functions are ready and accessible globally');
    } else {
        console.warn('⚠️ Some test email functions not found');
    }

    // Global function for bulk verification actions
    function handleBulkVerificationActions() {
        console.log('handleBulkVerificationActions called');
        
        if (typeof showBulkVerificationActions === 'function') {
            try {
                showBulkVerificationActions();
            } catch (error) {
                console.error('Error showing bulk verification actions:', error);
                // Fallback to navigation
                window.location.href = "{{ route('admin.users.verification.dashboard') }}";
            }
        } else if (typeof Swal !== 'undefined') {
            // Show a simple modal with basic options if the main function doesn't exist
            Swal.fire({
                title: '⚡ Bulk Verification Actions',
                html: `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Quick Actions:</strong> Select verification management options.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100" onclick="window.location.href='{{ route('admin.users.verification.email') }}'">
                                📧 Email Verification
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" onclick="window.location.href='{{ route('admin.users.verification.phone') }}'">
                                📱 Phone Verification
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="window.location.href='{{ route('admin.users.verification.kyc') }}'">
                                🆔 KYC Verification
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="window.location.href='{{ route('admin.users.verification.2fa') }}'">
                                🔐 2FA Management
                            </button>
                        </div>
                        <div class="col-12 mt-3">
                            <button class="btn btn-secondary w-100" onclick="window.location.href='{{ route('admin.users.verification.dashboard') }}'">
                                📊 Go to Verification Dashboard
                            </button>
                        </div>
                    </div>
                `,
                width: '600px',
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true
            });
        } else {
            // If SweetAlert2 is not available, navigate to dashboard
            window.location.href = "{{ route('admin.users.verification.dashboard') }}";
        }
    }

    // Make handleBulkVerificationActions globally accessible
    window.handleBulkVerificationActions = handleBulkVerificationActions;
    
    // Global Browser Cache Manager Function
    function showBrowserCacheManager() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🌐 Browser Cache Manager',
                html: `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary w-100" onclick="clearDomainCache()">
                                <i class="fe fe-globe"></i> Clear Domain Cache
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-warning w-100" onclick="clearAdvancedCache()">
                                <i class="fe fe-settings"></i> Advanced Clear
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" onclick="clearLocalStorage()">
                                <i class="fe fe-database"></i> Clear Storage
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" onclick="clearServiceWorkers()">
                                <i class="fe fe-cpu"></i> Clear SW
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Choose the type of cache clearing needed</small>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Close',
                width: '500px'
            });
        } else {
            alert('Browser Cache Manager requires SweetAlert2');
        }
    }

    // Global Cache Helper Functions
    function clearDomainCache() {
        window.location.href = '/browser_cache_clear/only_this_domain';
    }

    function clearAdvancedCache() {
        window.location.href = '/browser_cache_clear/advanced';
    }

    function clearLocalStorage() {
        try {
            localStorage.clear();
            sessionStorage.clear();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '✅ Success!',
                    text: 'Local storage cleared successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Local storage cleared successfully');
            }
        } catch (error) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Failed to clear local storage', 'error');
            } else {
                alert('Error: Failed to clear local storage');
            }
        }
    }

    function clearServiceWorkers() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '✅ Success!',
                        text: 'Service workers cleared successfully',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Service workers cleared successfully');
                }
            });
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Info', 'Service workers not supported in this browser', 'info');
            } else {
                alert('Service workers not supported in this browser');
            }
        }
    }

    // ===========================================
    // LOTTERY MANAGEMENT FUNCTIONS
    // ===========================================

    // Function to show auto-generate draw modal
    function showAutoGenerateModal() {
        // Create modal for auto-generate draw
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fe fe-zap me-2"></i>⚡ Auto Generate Draw
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fe fe-zap me-2"></i>
                            <strong>Quick Auto-Generate:</strong> Instantly create a new lottery draw with automatic settings from your lottery configuration.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="fe fe-clock" style="font-size: 2rem; color: #0d6efd;"></i>
                                        <h6 class="mt-2">Standard Auto-Draw</h6>
                                        <p class="text-muted small">Uses default lottery settings</p>
                                        <button class="btn btn-primary btn-sm" onclick="generateAutoDraw('standard')">
                                            <i class="fe fe-plus me-1"></i>Generate Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="fe fe-settings" style="font-size: 2rem; color: #198754;"></i>
                                        <h6 class="mt-2">Quick Custom</h6>
                                        <p class="text-muted small">Set basic parameters</p>
                                        <button class="btn btn-success btn-sm" onclick="showQuickCustomForm()">
                                            <i class="fe fe-edit me-1"></i>Customize
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Recent Activity:</h6>
                            <div id="recent-draws-info">
                                <div class="d-flex align-items-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading recent draws...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="{{ route('admin.lottery.draws') }}" class="btn btn-outline-primary">
                            <i class="fe fe-list me-1"></i>Manage All Draws
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Load recent draws info
        setTimeout(() => {
            const recentDrawsInfo = document.getElementById('recent-draws-info');
            if (recentDrawsInfo) {
                recentDrawsInfo.innerHTML = `
                    <div class="small text-muted">
                        <i class="fe fe-info me-1"></i>
                        Auto-generate will create draws with your current lottery settings. 
                        You can modify them later from the draws management page.
                    </div>
                `;
            }
        }, 1000);
        
        // Clean up modal when closed
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }

    // Function to generate auto draw
    function generateAutoDraw(type) {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fe fe-loader me-1"></i>Generating...';
        button.disabled = true;
        
        fetch('{{ route("admin.lottery.auto-generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type: type,
                auto_draw: true,
                auto_prize_distribution: true
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            // Check if response is successful
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If response is a redirect (like success with redirect), treat as success
                if (response.status >= 200 && response.status < 300) {
                    return { success: true, message: 'Auto-draw generated successfully!', redirected: true };
                } else {
                    throw new Error('Response is not JSON and not a successful redirect');
                }
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success || data.redirected) {
                // Close modal and show success
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) modalInstance.hide();
                });
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '✅ Success!',
                        text: data.message || `Auto-draw generated successfully! Draw ID: #${data.draw_id || 'Generated'}`,
                        icon: 'success',
                        confirmButtonText: 'View Draws',
                        showCancelButton: true,
                        cancelButtonText: 'Stay Here'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("admin.lottery.draws") }}';
                        }
                    });
                } else {
                    const message = data.message || `✅ Auto-draw generated successfully! Draw ID: #${data.draw_id || 'Generated'}`;
                    alert(message);
                    if (confirm('Would you like to view the draws page?')) {
                        window.location.href = '{{ route("admin.lottery.draws") }}';
                    }
                }
            } else {
                // Handle error response
                const errorMessage = data.message || data.error || 'Failed to generate auto-draw';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '❌ Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('❌ Error: ' + errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Auto-generate error:', error);
            
            // Better error handling
            let errorMessage = 'An error occurred while generating the auto-draw';
            
            if (error.message.includes('HTTP error')) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Invalid response from server. The operation may have succeeded but confirmation failed.';
            } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                errorMessage = 'Network error. Please check your connection and try again.';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '❌ Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    footer: '<small>If this persists, please check the draws page to see if the draw was created.</small>'
                });
            } else {
                alert('❌ ' + errorMessage + '\n\nIf this persists, please check the draws page to see if the draw was created.');
            }
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    // Function to show quick custom form
    function showQuickCustomForm() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Coming Soon!',
                text: 'Quick custom form will be available in the next update. For now, please use the standard auto-generate or create a draw manually.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            alert('🚧 Quick custom form will be available in the next update. For now, please use the standard auto-generate or create a draw manually.');
        }
    }

    // Make cache functions globally accessible
    window.showBrowserCacheManager = showBrowserCacheManager;
    window.clearDomainCache = clearDomainCache;
    window.clearAdvancedCache = clearAdvancedCache;
    window.clearLocalStorage = clearLocalStorage;
    window.clearServiceWorkers = clearServiceWorkers;
    
    // Make lottery functions globally accessible
    window.showAutoGenerateModal = showAutoGenerateModal;
    window.generateAutoDraw = generateAutoDraw;
    window.showQuickCustomForm = showQuickCustomForm;
    
    console.log('✅ Bulk verification actions function loaded and globally accessible');
    console.log('✅ Browser cache manager functions loaded and globally accessible');
    </script>
</body>

</html>