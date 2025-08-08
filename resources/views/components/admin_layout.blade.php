<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'PayperViews') }}</title>
    <meta name="Description" content="Admin Dashboard for PayperViews">
    <meta name="Author" content="PayperViews Team">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>
    
    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Additional Styles -->
    @stack('script-lib')
    @stack('style')
    
    <style>
        /* Notification Animations */
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .badge.pulse {
            animation: pulse 1.5s infinite;
        }
        
        /* Enhanced notification dropdown */
        .notification-dropdown {
            min-width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        /* Real-time notification styles */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        
        .notification-bell.has-notifications {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #dc3545;
            color: white;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
        
        .notification-badge.pulse {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>

<body>
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
                <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel" aria-labelledby="switcher-home-tab" tabindex="0">
                    <div class="">
                        <p class="switcher-style-head">Theme Color Mode:</p>
                        <div class="row switcher-style gx-0">
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-light-theme">Light</label>
                                    <input class="form-check-input" type="radio" name="theme-style" id="switcher-light-theme" checked>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check switch-select">
                                    <label class="form-check-label" for="switcher-dark-theme">Dark</label>
                                    <input class="form-check-input" type="radio" name="theme-style" id="switcher-dark-theme">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Switcher -->

    <!-- Loader -->
    <div id="loader">
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="Loading...">
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
                                <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                                <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                                <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                                <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                        <!-- End::header-link -->
                    </div>
                    <!-- End::header-element -->
                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">
                    
                    <!-- Start::header-element -->
                    <div class="header-element notifications-dropdown">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle notification-bell" id="notificationDropdown" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false">
                            <i class="fe fe-bell header-link-icon"></i>
                            <span class="notification-badge pulse" id="header-notification-badge" style="display: none;">0</span>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <div class="main-header-dropdown dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold">Notifications</h6>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAllNotificationsAsRead()">
                                        <i class="fe fe-check-circle"></i>
                                    </button>
                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="p-2" id="notification-dropdown-content">
                                <div class="text-center p-3">
                                    <i class="fe fe-bell-off fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No new notifications</p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="p-2 text-center">
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                    View All Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element --> 
                    <div class="header-element">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="me-sm-2 me-0">
                                    <img src="{{ asset('assets/images/faces/9.jpg') }}" alt="img" width="32" height="32" class="rounded-circle">
                                </div>
                                <div class="d-sm-block d-none">
                                    <p class="fw-semibold mb-0 lh-1">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                                    <span class="op-7 fw-normal d-block fs-11">Administrator</span>
                                </div>
                            </div>
                        </a>
                        <!-- End::header-link|dropdown-toggle -->
                        <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                            <li><a class="dropdown-item d-flex" href="{{ route('admin.profile') }}"><i class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile</a></li>
                            <li><a class="dropdown-item d-flex" href="{{ route('admin.notifications.settings') }}"><i class="ti ti-adjustments-horizontal fs-18 me-2 op-7"></i>Settings</a></li>
                            <li>
                                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex border-0 bg-transparent w-100 text-start" style="color: inherit;">
                                        <i class="ti ti-logout fs-18 me-2 op-7"></i>Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link|switcher-icon -->
                        <a href="javascript:void(0);" class="header-link switcher-icon" data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                            <i class="fe fe-settings header-link-icon"></i>
                        </a>
                        <!-- End::header-link|switcher-icon -->
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->
        </header>
        <!-- /app-header -->

        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sidebar-dark-theme" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="header-logo">
                    <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                    <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                    <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                    <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path></svg>
                    </div>
                    @include('components.adminMenu')
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="m10.707 17.707 5.707-5.707-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path></svg></div>
                </nav>
                <!-- End::nav -->

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
        <footer class="footer mt-auto py-3 bg-white text-center">
            <div class="container">
                <span class="text-muted"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-dark fw-semibold">PayperViews</a>.
                    Designed with <span class="bi bi-heart-fill text-danger"></span> by <a href="javascript:void(0);">
                        <span class="fw-semibold text-primary text-decoration-underline">PayperViews Team</span>
                    </a> All
                    rights
                    reserved
                </span>
            </div>
        </footer>
        <!-- Footer End -->

    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="fe fe-arrow-up fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>

    <!-- jQuery JS (Required for many components) -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Cache Management Scripts - Simplified for Login Focus -->
    <script src="{{ asset('assets/js/login-cache-clear.js') }}"></script>
    
    <!-- Cache Monitor for Testing (remove in production) -->
    @if(config('app.debug'))
    <script src="{{ asset('assets/js/cache-monitor.js') }}"></script>
    @endif

    <!-- Additional Scripts -->
    @stack('script')

    <script>
        // Debug: Verify SweetAlert2 is loaded
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                console.log('✅ SweetAlert2 loaded successfully in admin layout');
            } else {
                console.error('❌ SweetAlert2 failed to load in admin layout');
            }
        });

        // Notification Management Functions
        let notificationPollingInterval;
        let lastNotificationCheck = Date.now();

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize notification system
            initializeNotifications();
            
            // Set up polling for real-time notifications
            startNotificationPolling();
            
            // Initialize other components
            updateYear();
        });

        function initializeNotifications() {
            // Load initial notifications
            loadNotifications();
            
            // Set up notification bell click handler
            document.getElementById('notificationDropdown').addEventListener('click', function() {
                loadNotifications();
            });
        }

        function loadNotifications() {
            fetch('{{ route("admin.notifications.dropdown") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationDropdown(data.notifications, data.unread_count);
                    updateNotificationBadge(data.unread_count);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function updateNotificationDropdown(notifications, unreadCount) {
            const container = document.getElementById('notification-dropdown-content');
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center p-3">
                        <i class="fe fe-bell-off fs-1 text-muted mb-2"></i>
                        <p class="text-muted mb-0">No new notifications</p>
                    </div>
                `;
                return;
            }

            const notificationsHtml = notifications.slice(0, 5).map(notification => `
                <div class="notification-item p-2 mb-1 rounded ${!notification.read ? 'unread' : ''}" onclick="handleNotificationClick(${notification.id}, '${notification.action_url || ''}')">
                    <div class="d-flex">
                        <div class="notification-icon bg-${notification.type}-transparent text-${notification.type} me-2">
                            <i class="${notification.icon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold fs-13">${notification.title}</h6>
                            <p class="mb-1 text-muted fs-12 text-truncate" style="max-width: 280px;">${notification.message}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="notification-time">${notification.time_ago}</span>
                                ${notification.priority === 'urgent' ? '<span class="badge bg-danger fs-10">Urgent</span>' : ''}
                                ${notification.priority === 'high' ? '<span class="badge bg-warning fs-10">High</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = notificationsHtml;
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('header-notification-badge');
            const bell = document.querySelector('.notification-bell');
            
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
                badge.classList.add('pulse');
                bell.classList.add('has-notifications');
            } else {
                badge.style.display = 'none';
                badge.classList.remove('pulse');
                bell.classList.remove('has-notifications');
            }
        }

        function handleNotificationClick(notificationId, actionUrl) {
            // Mark notification as read
            fetch(`{{ route("admin.notifications.read", ":id") }}`.replace(':id', notificationId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload notifications to update counts
                    loadNotifications();
                    
                    // Navigate to action URL or notification details
                    if (actionUrl) {
                        window.open(actionUrl, '_blank');
                    } else {
                        window.location.href = `{{ route("admin.notifications.show", ":id") }}`.replace(':id', notificationId);
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        function markAllNotificationsAsRead() {
            fetch('{{ route("admin.notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    showToast('All notifications marked as read', 'success');
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }

        function startNotificationPolling() {
            // Poll for new notifications every 30 seconds
            notificationPollingInterval = setInterval(() => {
                checkForNewNotifications();
            }, 30000);
        }

        function checkForNewNotifications() {
            fetch('{{ route("admin.notifications.dropdown") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const currentUnreadCount = parseInt(document.getElementById('header-notification-badge').textContent) || 0;
                    const newUnreadCount = data.unread_count || 0;
                    
                    // If we have new notifications, update and show animation
                    if (newUnreadCount > currentUnreadCount) {
                        updateNotificationBadge(newUnreadCount);
                        
                        // Show browser notification if permitted
                        if (Notification.permission === 'granted') {
                            const latestNotification = data.notifications[0];
                            if (latestNotification && !latestNotification.read) {
                                new Notification(latestNotification.title, {
                                    body: latestNotification.message,
                                    icon: '/assets/images/brand-logos/favicon.ico'
                                });
                            }
                        }
                        
                        // Play notification sound
                        playNotificationSound();
                    }
                }
            })
            .catch(error => {
                console.error('Error checking for new notifications:', error);
            });
        }

        function playNotificationSound() {
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Could not play notification sound:', e));
            } catch (error) {
                console.log('Notification sound not available');
            }
        }

        function requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }

        function showToast(message, type = 'info') {
            // Simple toast notification function
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fe fe-${type === 'success' ? 'check-circle' : 'info'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 4000);
        }

        function updateYear() {
            document.getElementById('year').textContent = new Date().getFullYear();
        }

        // Request notification permission on page load
        requestNotificationPermission();

        // Clean up intervals when page unloads
        window.addEventListener('beforeunload', function() {
            if (notificationPollingInterval) {
                clearInterval(notificationPollingInterval);
            }
        });

        // Global Browser Cache Manager Function
        function showBrowserCacheManager() {
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
                Swal.fire({
                    title: '✅ Success!',
                    text: 'Local storage cleared successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire('Error', 'Failed to clear local storage', 'error');
            }
        }

        function clearServiceWorkers() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister();
                    }
                    Swal.fire({
                        title: '✅ Success!',
                        text: 'Service workers cleared successfully',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        }
    </script>
    
    <!-- Admin Session Manager -->
    <script src="{{ asset('assets_custom/js/admin-session-manager.js') }}"></script>
    <script>
        // Initialize session manager
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AdminSessionManager !== 'undefined') {
                console.log('Admin Session Manager loaded successfully');
            }
            
            // Handle admin logout with CSRF error fallback
            document.addEventListener('submit', function(e) {
                if (e.target.action && e.target.action.includes('admin/logout')) {
                    e.preventDefault();
                    
                    const form = e.target;
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF token expired, try emergency logout
                            console.log('CSRF token expired, attempting emergency logout');
                            return fetch('{{ route("admin.emergency.logout") }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                        }
                        return response;
                    })
                    .then(response => {
                        if (response.ok) {
                            // Successful logout, redirect to admin login
                            window.location.href = '{{ route("admin.index") }}';
                        } else {
                            throw new Error('Logout failed');
                        }
                    })
                    .catch(error => {
                        console.error('Logout error:', error);
                        // Force redirect to admin login as fallback
                        window.location.href = '{{ route("admin.index") }}';
                    });
                }
            });
        });
    </script>
</body>

</html>
