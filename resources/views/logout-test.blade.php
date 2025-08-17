<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logout Test - Complete Session Destruction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Complete Session Destruction Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Test Complete Logout Functionality</strong><br>
                            This page tests the enhanced logout system that completely destroys browser sessions.
                        </div>

                        <!-- Session Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Current Session Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>User:</strong> 
                                            @auth
                                                {{ auth()->user()->username }} (ID: {{ auth()->id() }})
                                            @else
                                                <span class="text-danger">Not Logged In</span>
                                            @endauth
                                        </p>
                                        <p><strong>Session ID:</strong> <code>{{ session()->getId() }}</code></p>
                                        <p><strong>IP Address:</strong> {{ request()->ip() }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                                        <p><strong>User Agent:</strong> 
                                            <small>{{ substr(request()->userAgent(), 0, 50) }}...</small>
                                        </p>
                                        <p><strong>Session Status:</strong> 
                                            @auth
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endauth
                                        </p>
                                        @auth
                                        @php
                                            $lastActivity = session('last_activity_time', time());
                                            $inactiveMinutes = (time() - $lastActivity) / 60;
                                            $timeoutMinutes = 30; // Default
                                            $userSettings = \Illuminate\Support\Facades\Cache::get("user_session_settings_security_" . auth()->id(), []);
                                            if (isset($userSettings['session_timeout_hours'])) {
                                                $timeoutMinutes = $userSettings['session_timeout_hours'] * 60;
                                            }
                                        @endphp
                                        <p><strong>Inactive Time:</strong> {{ round($inactiveMinutes, 1) }} minutes</p>
                                        <p><strong>Timeout Limit:</strong> {{ $timeoutMinutes }} minutes</p>
                                        <p><strong>Remaining Time:</strong> 
                                            <span class="badge {{ ($timeoutMinutes - $inactiveMinutes) < 5 ? 'bg-danger' : 'bg-success' }}">
                                                {{ max(0, round($timeoutMinutes - $inactiveMinutes, 1)) }} minutes
                                            </span>
                                        </p>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>

                        @auth
                        <!-- Logout Test Buttons -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Logout Testing Methods</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <button type="button" class="btn btn-danger w-100" onclick="testCompleteLogout()">
                                            <i class="fas fa-power-off me-2"></i>
                                            Complete Logout
                                            <small class="d-block">Enhanced with session destruction</small>
                                        </button>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button type="button" class="btn btn-warning w-100" onclick="testStandardLogout()">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Standard Logout
                                            <small class="d-block">Normal Laravel logout</small>
                                        </button>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button type="button" class="btn btn-info w-100" onclick="testAjaxLogout()">
                                            <i class="fas fa-wifi me-2"></i>
                                            AJAX Logout
                                            <small class="d-block">JSON response test</small>
                                        </button>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button type="button" class="btn btn-secondary w-100" onclick="testSessionTimeout()">
                                            <i class="fas fa-clock me-2"></i>
                                            Test Auto-Timeout
                                            <small class="d-block">Force session timeout</small>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Activity Test -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Session Activity Monitoring</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-success w-100" onclick="extendSession()">
                                            <i class="fas fa-clock me-2"></i>
                                            Extend Session
                                            <small class="d-block">Reset activity timer</small>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-primary w-100" onclick="checkSessionStatus()">
                                            <i class="fas fa-search me-2"></i>
                                            Check Session
                                            <small class="d-block">Get current status</small>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-warning w-100" onclick="simulateInactivity()">
                                            <i class="fas fa-pause me-2"></i>
                                            Simulate Inactivity
                                            <small class="d-block">Test timeout warning</small>
                                        </button>
                                    </div>
                                </div>
                                <div id="sessionStatusResult" class="mt-3"></div>
                            </div>
                        </div>

                        <!-- Storage Test -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Storage Test (Set Before Logout)</h6>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-secondary" onclick="setTestData()">
                                    <i class="fas fa-database me-2"></i>
                                    Set Test Data in Storage
                                </button>
                                <div id="storageStatus" class="mt-3"></div>
                            </div>
                        </div>
                        @else
                        <!-- Not Logged In -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>You are not logged in.</strong><br>
                            <a href="{{ route('login') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Test Logout
                            </a>
                        </div>
                        @endauth

                        <!-- Navigation -->
                        <div class="text-center mt-4">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Test complete logout with session destruction
        function testCompleteLogout() {
            Swal.fire({
                title: 'Test Complete Logout?',
                text: 'This will completely destroy your session and clear all storage.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Test Complete Logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    performCompleteLogout();
                }
            });
        }

        // Test session timeout by setting old activity time
        function testSessionTimeout() {
            Swal.fire({
                title: 'Test Session Timeout?',
                text: 'This will simulate an inactive session and trigger automatic logout.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Test Timeout'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set a very old last activity time to trigger timeout
                    fetch('/user/extend-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            test_timeout: true,
                            old_activity_time: Math.floor(Date.now() / 1000) - (35 * 60) // 35 minutes ago
                        })
                    })
                    .then(() => {
                        Swal.fire('Test Started!', 'Session timeout simulation initiated. The system will detect this on next check.', 'info');
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    });
                }
            });
        }

        // Extend session manually
        function extendSession() {
            fetch('/user/extend-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    extend_session: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Session Extended!', 'Your session activity has been updated.', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to extend session', 'error');
            });
        }

        // Check current session status
        function checkSessionStatus() {
            fetch('/user/session-check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('sessionStatusResult');
                resultDiv.innerHTML = `
                    <div class="alert alert-info">
                        <h6>Session Status:</h6>
                        <ul class="mb-0">
                            <li><strong>Authenticated:</strong> ${data.authenticated ? 'Yes' : 'No'}</li>
                            <li><strong>Inactive Time:</strong> ${data.inactive_minutes} minutes</li>
                            <li><strong>Timeout Limit:</strong> ${data.timeout_minutes} minutes</li>
                            <li><strong>Remaining Time:</strong> ${data.remaining_minutes} minutes</li>
                            <li><strong>Status:</strong> ${data.remaining_minutes < 5 ? '<span class="text-danger">Warning - Expiring Soon</span>' : '<span class="text-success">Active</span>'}</li>
                        </ul>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('sessionStatusResult').innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> Failed to check session status. You may already be logged out.
                    </div>
                `;
            });
        }

        // Simulate inactivity for testing
        function simulateInactivity() {
            if (window.autoSessionTimeout) {
                window.autoSessionTimeout.destroy();
            }
            
            // Create a new instance with very short timeout for testing
            window.testTimeout = new AutoSessionTimeout({
                timeoutMinutes: 1, // 1 minute for testing
                warningMinutes: 0.5, // 30 seconds warning
                checkInterval: 5000, // Check every 5 seconds
                enableWarnings: true,
                enableLogging: true
            });
            
            Swal.fire({
                title: 'Inactivity Simulation Started',
                text: 'Timeout set to 1 minute with 30-second warning for testing. Do not move your mouse or interact with the page.',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false
            });
        }

        // Test AJAX logout
        function testAjaxLogout() {
            fetch("{{ route('logout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    test: 'ajax',
                    complete_logout: true
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('AJAX logout response:', data);
                if (data.success) {
                    Swal.fire('Success!', 'AJAX logout completed', 'success').then(() => {
                        window.location.href = data.redirect_url || "{{ route('login') }}";
                    });
                }
            })
            .catch(error => {
                console.error('AJAX logout error:', error);
                Swal.fire('Error!', 'AJAX logout failed', 'error');
            });
        }

        // Perform complete logout with all cleanup
        function performCompleteLogout() {
            // Show loading
            Swal.fire({
                title: 'Testing Complete Logout...',
                text: 'Destroying session and clearing all data...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Clear all client storage
            try {
                localStorage.clear();
                sessionStorage.clear();
                
                // Clear cookies
                document.cookie.split(";").forEach(function(c) { 
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                });
            } catch(e) {
                console.warn('Storage cleanup failed:', e);
            }

            // Server logout
            fetch("{{ route('logout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    test: 'complete',
                    complete_logout: true,
                    destroy_session: true
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                Swal.fire({
                    title: 'Complete Logout Test Successful!',
                    text: 'Session has been completely destroyed.',
                    icon: 'success',
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.replace(data.redirect_url || "{{ route('login') }}?test_complete=1");
                });
            })
            .catch(error => {
                console.error('Complete logout error:', error);
                Swal.close();
                window.location.href = "{{ route('logout') }}?fallback=1";
            });
        }

        // Set test data in storage
        function setTestData() {
            try {
                localStorage.setItem('test_data', 'This should be cleared on logout');
                sessionStorage.setItem('test_session', 'This should also be cleared');
                
                document.getElementById('storageStatus').innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i>
                        Test data set in localStorage and sessionStorage.<br>
                        <small>This data should be completely cleared after logout.</small>
                    </div>
                `;
            } catch(e) {
                document.getElementById('storageStatus').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times me-2"></i>
                        Failed to set test data: ${e.message}
                    </div>
                `;
            }
        }

        // Check for test data on page load
        document.addEventListener('DOMContentLoaded', function() {
            const testData = localStorage.getItem('test_data');
            const testSession = sessionStorage.getItem('test_session');
            
            if (testData || testSession) {
                document.getElementById('storageStatus').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Storage data found!</strong> Logout may not have completely cleared storage.<br>
                        <small>localStorage: ${testData || 'none'}<br>sessionStorage: ${testSession || 'none'}</small>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
