<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Cache Control Meta Tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="{{ gmdate('D, d M Y H:i:s') }} GMT">
    
    <title>Admin Login - PayPerViews</title>
    
    <!-- Favicon -->
    @php
        $faviconUrl = asset('favicon.svg');
        if (isset($settings) && $settings && $settings->favicon) {
            $faviconUrl = getMediaUrl($settings->favicon, 'favicon');
        }
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .admin-login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        .admin-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e74c3c, #c0392b);
        }
        
        .admin-brand {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .admin-brand h1 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .admin-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #ecf0f1;
            border-radius: 12px;
            height: 55px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.15);
            background: #fff;
        }
        
        .form-floating label {
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .btn-admin-login {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            border-radius: 12px;
            height: 55px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-admin-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(231, 76, 60, 0.4);
        }
        
        .btn-admin-login:active {
            transform: translateY(0);
        }
        
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .admin-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .admin-footer a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .admin-footer a:hover {
            color: #c0392b;
        }
        
        .session-indicator {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .session-active { background-color: #27ae60; }
        .session-warning { background-color: #f39c12; }
        .session-error { background-color: #e74c3c; }
        
        .validation-message {
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .validation-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .validation-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .admin-help {
            background: rgba(236, 240, 241, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .admin-help-toggle {
            background: none;
            border: none;
            color: #e74c3c;
            font-size: 13px;
            cursor: pointer;
            text-decoration: underline;
        }
        
        .clear-cache-btn {
            color: #e74c3c;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .clear-cache-btn:hover {
            color: #c0392b;
        }
        
        /* Background Animation */
        .admin-bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .admin-bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.02) 2px,
                rgba(255, 255, 255, 0.02) 4px
            );
            animation: drift 20s linear infinite;
        }
        
        @keyframes drift {
            0% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
            100% { transform: translateX(-50%) translateY(-50%) rotate(360deg); }
        }
        
        .admin-security-badge {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }
        
        /* Password Toggle Button */
        .password-toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
            z-index: 5;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle-btn:hover {
            color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.1);
            transform: translateY(-50%) scale(1.05);
        }
        
        .password-toggle-btn:focus {
            outline: none;
            color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2);
        }
        
        .password-toggle-btn:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .password-toggle-btn i {
            font-size: 16px;
            transition: all 0.2s ease;
        }
        
        .password-toggle-btn:hover i {
            transform: scale(1.1);
        }
        
        /* Add some padding to password input to prevent text overlap with button */
        #password {
            padding-right: 50px !important;
        }
        
        /* Enhanced Remember Me Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(231, 76, 60, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(231, 76, 60, 0.1);
            transition: all 0.3s ease;
        }
        
        .form-check:hover {
            background: rgba(231, 76, 60, 0.08);
            border-color: rgba(231, 76, 60, 0.2);
        }
        
        .form-check-input {
            width: 18px !important;
            height: 18px !important;
            margin-right: 12px;
            margin-top: 0 !important;
            flex-shrink: 0;
            border: 2px solid #dee2e6 !important;
            border-radius: 4px !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            position: relative;
        }
        
        .form-check-input:checked {
            background-color: #e74c3c !important;
            border-color: #e74c3c !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e") !important;
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25) !important;
            border-color: #e74c3c !important;
        }
        
        /* Override Bootstrap's ::before pseudo-element */
        .form-check-input::before,
        .form-check-input::after {
            display: none !important;
        }
        
        .form-check-label {
            cursor: pointer;
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 0;
            transition: color 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .form-check-label i {
            margin-right: 8px;
            font-size: 14px;
        }
        
        .form-check:hover .form-check-label {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="admin-bg-animation"></div>
    
    <div class="admin-login-container">
        <div class="admin-card">
            <!-- Session Status Indicator -->
            <div id="sessionIndicator" class="session-indicator session-active" title="Admin Session: Active and secure"></div>
            
            <!-- Admin Brand -->
            <div class="admin-brand">
                <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
                <div class="admin-subtitle">PayPerViews Administration</div>
            </div>
            
            <!-- Security Badge -->
            <div class="admin-security-badge">
                <i class="fas fa-lock"></i> Secure Admin Access
            </div>
            
            <!-- Login Info -->
            <div class="alert alert-info mb-3" style="font-size: 13px; padding: 10px 15px;">
                <i class="fas fa-info-circle"></i> You can login using either your <strong>email address</strong> or <strong>username</strong>
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
            @endif
            
            <!-- Admin Login Form -->
            <form id="adminLoginForm" action="{{ route('admin.login') }}" method="POST">
                @csrf
                
                <!-- Email/Username Input -->
                <div class="form-floating">
                    <input type="text" class="form-control" id="email" name="email" placeholder="email@example.com or username" required autofocus autocomplete="username">
                    <label for="email"><i class="fas fa-user"></i> Admin Email or Username</label>
                </div>
                
                <!-- Password Input with Toggle -->
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <button type="button" class="password-toggle-btn" id="togglePassword" title="Show/Hide Password">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                
                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        <i class="fas fa-memory"></i> Keep me signed in for 30 days
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="btn btn-admin-login w-100">
                    <i class="fas fa-sign-in-alt"></i> Sign In to Admin Panel
                </button>
            </form>
            
            <!-- Admin Help -->
            <div class="admin-help">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="admin-help-toggle" onclick="toggleAdminHelp()">
                        <i class="fas fa-question-circle"></i> Admin Help
                    </button>
                    <a href="#" id="clearCacheLink" class="clear-cache-btn" onclick="clearAdminCache(event)" title="Clear cache & refresh admin session">
                        <i class="fas fa-broom"></i> Clear Cache
                    </a>
                </div>
                <div id="adminHelpContent" style="display: none; margin-top: 15px;">
                    <h6><i class="fas fa-info-circle"></i> Session Status Colors:</h6>
                    <ul style="margin: 10px 0; padding-left: 20px; font-size: 12px;">
                        <li>🟢 Green: Admin session active and secure</li>
                        <li>🟡 Orange: Session warning or expiring soon</li>
                        <li>🔴 Red: Session expired or security issue</li>
                    </ul>
                    <p style="margin-bottom: 0;"><strong>Having login issues?</strong> Click "Clear Cache" for a 100% fix!</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="admin-footer">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Back to User Login
                </a>
            </div>
        </div>
    </div>

<!-- SweetAlert2 -->
<script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin login page loaded');
    
    // Admin Cache Detection
    detectAdminCacheIssues();
    
    // Debug CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('Admin CSRF Token found:', csrfToken ? 'Yes' : 'No');
    if (!csrfToken) {
        console.error('Admin CSRF token not found in meta tag!');
    }

    // Form elements
    const form = document.getElementById('adminLoginForm');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');

    // Debug form initialization
    console.log('Admin form initialized:', {
        form: !!form,
        action: form ? form.action : 'N/A',
        method: form ? form.method : 'N/A',
        submitBtn: !!submitBtn,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...'
    });

    // Initialize session indicator
    updateAdminSessionIndicator('active', 'Admin Session: Active and secure');

    // Password Toggle Functionality
    const togglePassword = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');

    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'text') {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                togglePassword.setAttribute('title', 'Hide Password');
            } else {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                togglePassword.setAttribute('title', 'Show Password');
            }
            
            // Maintain focus on password input
            passwordInput.focus();
        });
        
        // Keyboard accessibility for password toggle
        togglePassword.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    }

    // Remember Me Cookie Management
    const rememberCookieName = 'admin_remember_preference';
    
    // Check if remember preference cookie exists and set checkbox accordingly
    if (getCookie(rememberCookieName) === 'true') {
        rememberCheckbox.checked = true;
    }
    
    // Handle remember checkbox change
    rememberCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Set cookie for 30 days when checked
            setCookie(rememberCookieName, 'true', 30);
            console.log('Admin remember preference: Set for 30 days');
            
            // Show visual feedback
            Swal.fire({
                title: 'Remember Me Enabled',
                text: 'Your login preference will be remembered for 30 days',
                icon: 'info',
                timer: 2000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true
            });
        } else {
            // Remove cookie when unchecked
            deleteCookie(rememberCookieName);
            console.log('Admin remember preference: Removed');
            
            // Show visual feedback
            Swal.fire({
                title: 'Remember Me Disabled',
                text: 'Your login preference has been cleared',
                icon: 'info',
                timer: 2000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timerProgressBar: true
            });
        }
    });

    // Form submission handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Admin form submission started:', {
            emailOrUsername: emailInput.value.trim(),
            hasPassword: !!passwordInput.value,
            remember: rememberCheckbox.checked,
            action: form.action,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...'
        });

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Signing In...';

        // Clear previous validations
        hideAdminValidation('email');
        hideAdminValidation('password');

        try {
            // Basic validation
            if (!emailInput.value.trim()) {
                showAdminValidation('email', 'Admin email or username is required', 'error');
                return;
            }

            if (!passwordInput.value) {
                showAdminValidation('password', 'Password is required', 'error');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('email', emailInput.value.trim());
            formData.append('password', passwordInput.value);
            if (rememberCheckbox.checked) {
                formData.append('remember', '1');
            }

            console.log('Submitting admin login...');
            let response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin'
            });

            // Handle CSRF token mismatch with ONE retry only
            if (response.status === 419) {
                console.log('Admin CSRF token mismatch (419) - attempting refresh and retry...');
                
                try {
                    const newToken = await refreshAdminCSRFToken();
                    if (newToken) {
                        // Create completely fresh form data with new token
                        const retryFormData = new FormData();
                        retryFormData.append('_token', newToken);
                        retryFormData.append('email', emailInput.value.trim());
                        retryFormData.append('password', passwordInput.value);
                        if (rememberCheckbox.checked) {
                            retryFormData.append('remember', '1');
                        }
                        
                        console.log('Retrying admin login with refreshed token...');
                        response = await fetch(form.action, {
                            method: 'POST',
                            body: retryFormData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Cache-Control': 'no-cache'
                            },
                            credentials: 'same-origin'
                        });
                        
                        console.log('Admin retry response status:', response.status);
                        
                        // If still 419 after retry, show error instead of reload
                        if (response.status === 419) {
                            console.log('Admin CSRF token still invalid after retry');
                            Swal.fire({
                                title: 'Admin Session Issue!',
                                text: 'There seems to be a session problem. Please try refreshing the page.',
                                icon: 'warning',
                                confirmButtonText: 'Refresh Page',
                                cancelButtonText: 'Try Again',
                                showCancelButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                            return;
                        }
                    } else {
                        throw new Error('Failed to refresh admin CSRF token');
                    }
                } catch (refreshError) {
                    console.error('Admin CSRF refresh failed:', refreshError);
                    
                    Swal.fire({
                        title: 'Admin Session Problem!',
                        text: 'Unable to refresh your admin session. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Try Again',
                        cancelButtonText: 'Refresh Page',
                        showCancelButton: true
                    }).then((result) => {
                        if (result.isDismissed && result.dismiss === 'cancel') {
                            window.location.reload();
                        }
                    });
                    return;
                }
            }

            // Process the final response
            if (response.ok || response.redirected) {
                console.log('Admin login successful! Response:', {
                    status: response.status,
                    redirected: response.redirected,
                    url: response.url
                });
                
                // Clear the form for security
                form.reset();
                
                Swal.fire({
                    title: 'Admin Login Successful!',
                    text: 'Welcome to the admin panel! Redirecting...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    // Determine redirect URL
                    let redirectUrl = '/admin/dashboard';
                    
                    if (response.redirected && response.url) {
                        // If the response was redirected, use the redirect URL
                        redirectUrl = response.url;
                    } else {
                        // Check for intended redirect from URL params
                        const urlParams = new URLSearchParams(window.location.search);
                        const intendedUrl = urlParams.get('redirect');
                        if (intendedUrl && !intendedUrl.includes('/admin/login')) {
                            redirectUrl = intendedUrl;
                        }
                    }
                    
                    console.log('Admin redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                });
            } else if (response.status === 403) {
                console.log('Admin login failed - Invalid credentials (403)');
                const data = await response.json();
                
                Swal.fire({
                    title: 'Access Denied!',
                    text: data.message || 'Invalid admin credentials or insufficient privileges.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                
                showAdminValidation('password', 'Invalid admin credentials', 'error');
            } else if (response.status === 422) {
                console.log('Admin validation failed (422)');
                const data = await response.json();
                
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        showAdminValidation(field, messages[0], 'error');
                    }
                } else {
                    showAdminValidation('email', 'Please check your email/username and password', 'error');
                }
            } else {
                console.log('Admin login failed with status:', response.status);
                
                Swal.fire({
                    title: 'Login Failed!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                
                showAdminValidation('password', 'Login failed. Please try again.', 'error');
            }

        } catch (error) {
            console.error('Admin login error:', error);
            
            Swal.fire({
                title: 'Connection Error!',
                text: 'Unable to connect to the server. Please check your internet connection and try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
            
            showAdminValidation('password', 'Connection error. Please try again.', 'error');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In to Admin Panel';
        }
    });

    // Admin validation functions
    function showAdminValidation(fieldName, message, type) {
        hideAdminValidation(fieldName);
        
        const field = document.getElementById(fieldName);
        if (!field) return;
        
        const validationDiv = document.createElement('div');
        validationDiv.className = `validation-message validation-${type}`;
        validationDiv.id = `${fieldName}-validation`;
        validationDiv.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${message}`;
        
        field.parentNode.insertBefore(validationDiv, field.nextSibling);
        
        if (type === 'error') {
            field.classList.add('is-invalid');
            field.focus();
        } else {
            field.classList.add('is-valid');
        }
    }

    function hideAdminValidation(fieldName) {
        const field = document.getElementById(fieldName);
        const validationDiv = document.getElementById(`${fieldName}-validation`);
        
        if (field) {
            field.classList.remove('is-invalid', 'is-valid');
        }
        
        if (validationDiv) {
            validationDiv.remove();
        }
    }

    // Admin session indicator update
    function updateAdminSessionIndicator(status, message) {
        const indicator = document.getElementById('sessionIndicator');
        if (!indicator) return;
        
        // Remove all status classes
        indicator.classList.remove('session-active', 'session-warning', 'session-error');
        
        // Add new status class
        indicator.classList.add(`session-${status}`);
        indicator.title = message;
        
        console.log('Admin session indicator updated:', status, message);
    }

    // Admin CSRF token refresh
    async function refreshAdminCSRFToken() {
        try {
            console.log('Refreshing admin CSRF token...');
            updateAdminSessionIndicator('warning', 'Refreshing admin session...');
            
            const response = await fetch('/admin/login', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (newToken && newToken !== document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) {
                // Update the CSRF token in meta tag
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken);
                
                // Update any CSRF inputs in forms
                const csrfInputs = document.querySelectorAll('input[name="_token"]');
                csrfInputs.forEach(input => {
                    input.value = newToken;
                });
                
                updateAdminSessionIndicator('active', 'Admin session refreshed successfully');
                console.log('Admin CSRF token refreshed successfully');
                return newToken;
            } else {
                throw new Error('No valid CSRF token found in response');
            }
        } catch (error) {
            console.error('Failed to refresh admin CSRF token:', error);
            updateAdminSessionIndicator('error', 'Failed to refresh admin session');
            throw error;
        }
    }
});

// Admin cache detection
function detectAdminCacheIssues() {
    console.log('Checking for admin cache issues...');
    
    // Check if this is a cache-busted reload
    const urlParams = new URLSearchParams(window.location.search);
    const cacheCleared = urlParams.get('_admin_cache_clear');
    
    if (cacheCleared) {
        // Remove cache clear parameter from URL
        urlParams.delete('_admin_cache_clear');
        urlParams.delete('_t');
        const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', cleanUrl);
        
        // Show success message for cache clear
        setTimeout(() => {
            Swal.fire({
                title: 'Cache Cleared!',
                text: 'Admin cache cleared successfully! Login should work perfectly now.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        }, 500);
        return;
    }
    
    // Detect potential admin cache issues
    let cacheWarnings = [];
    
    // Check for stale admin data in localStorage
    try {
        const storedData = localStorage.getItem('admin_session_data');
        if (storedData) {
            const data = JSON.parse(storedData);
            const hoursSinceStored = (Date.now() - (data.timestamp || 0)) / (1000 * 60 * 60);
            if (hoursSinceStored > 12) {
                cacheWarnings.push('Old admin session data detected');
            }
        }
    } catch (e) {
        console.log('No admin localStorage data found');
    }
    
    // Show admin cache warning if issues detected
    if (cacheWarnings.length > 0) {
        console.log('Admin cache issues detected:', cacheWarnings);
        
        setTimeout(() => {
            const clearLink = document.getElementById('clearCacheLink');
            if (clearLink) {
                clearLink.style.color = '#e74c3c';
                clearLink.style.fontWeight = 'bold';
                clearLink.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Clear Cache (Recommended)';
                clearLink.title = 'Admin cache issues detected! Click to clear and fix login problems.';
            }
        }, 2000);
    }
}

// Admin help toggle
function toggleAdminHelp() {
    const content = document.getElementById('adminHelpContent');
    const toggle = document.querySelector('.admin-help-toggle');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        toggle.innerHTML = '<i class="fas fa-times"></i> Hide Help';
    } else {
        content.style.display = 'none';
        toggle.innerHTML = '<i class="fas fa-question-circle"></i> Admin Help';
    }
}

// Admin cache clearing
async function clearAdminCache(event) {
    if (event) event.preventDefault();
    
    Swal.fire({
        title: 'Clear Admin Cache?',
        html: `
            <div style="text-align: left; font-size: 14px;">
                <p><i class="fas fa-shield-alt" style="color: #e74c3c;"></i> This will completely clear admin browser cache and refresh your security token.</p>
                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;">
                    <p style="margin: 0; font-weight: bold;"><i class="fas fa-broom" style="color: #e74c3c;"></i> What this will do:</p>
                    <ul style="margin: 5px 0; padding-left: 20px; font-size: 13px;">
                        <li>Clear all browser cache and cookies for admin</li>
                        <li>Refresh CSRF security tokens</li>
                        <li>Reset admin session status</li>
                        <li>Hard reload the page (like Ctrl+F5)</li>
                    </ul>
                </div>
                <p style="color: #e74c3c;"><strong>✅ 100% guaranteed to fix admin login issues!</strong></p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-magic"></i> Clear Admin Cache',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: async () => {
            try {
                // Clear admin-specific storage
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    console.log('Admin service worker caches cleared');
                }
                
                if (typeof(Storage) !== "undefined") {
                    localStorage.removeItem('admin_session_data');
                    sessionStorage.clear();
                    console.log('Admin browser storage cleared');
                }
                
                // Clear admin cookies
                document.cookie.split(";").forEach(function(c) { 
                    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                });
                console.log('Admin cookies cleared');
                
                return true;
            } catch (error) {
                console.error('Admin cache clearing error:', error);
                return true; // Still proceed to hard refresh
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show success message briefly before hard refresh
            Swal.fire({
                title: 'Admin Cache Cleared!',
                html: `
                    <div style="text-align: center;">
                        <p><i class="fas fa-check-circle" style="color: #e74c3c; font-size: 24px;"></i></p>
                        <p>Admin cache cleared successfully!</p>
                        <p style="color: #6c757d; font-size: 13px;">Performing hard refresh now...</p>
                    </div>
                `,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                // Force a hard refresh with admin cache parameter
                console.log('Performing admin hard refresh...');
                
                try {
                    window.location.reload(true);
                } catch (e) {
                    // Cache-busting reload with admin parameter
                    const url = new URL(window.location);
                    url.searchParams.set('_t', Date.now());
                    url.searchParams.set('_admin_cache_clear', '1');
                    window.location.href = url.toString();
                }
            });
        }
    });
}

// Cookie utility functions
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax; Secure";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function deleteCookie(name) {
    document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax; Secure";
}
</script>

</body>
</html>
