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
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-danger w-100" onclick="testCompleteLogout()">
                                            <i class="fas fa-power-off me-2"></i>
                                            Complete Logout
                                            <small class="d-block">Enhanced with session destruction</small>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-warning w-100" onclick="testStandardLogout()">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Standard Logout
                                            <small class="d-block">Normal Laravel logout</small>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-info w-100" onclick="testAjaxLogout()">
                                            <i class="fas fa-wifi me-2"></i>
                                            AJAX Logout
                                            <small class="d-block">JSON response test</small>
                                        </button>
                                    </div>
                                </div>
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

        // Test standard logout
        function testStandardLogout() {
            window.location.href = "{{ route('logout') }}?test=standard";
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
