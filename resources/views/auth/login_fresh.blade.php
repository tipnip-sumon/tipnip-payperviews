<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PayPerViews</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="alternate icon" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\GeneralSetting::getLogo() }}">
    
    <!-- Cache Control for Multi-Session Support -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Session Storage Key for Multi-Tab Detection -->
    <meta name="session-key" content="{{ session()->getId() }}">
    <meta name="app-version" content="{{ time() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
    
<style>
    .login-container { 
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        width: 100%;
        max-width: 450px;
        position: relative;
        z-index: 1;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .back-to-home {
        position: absolute;
        top: -15px;
        left: -15px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #667eea;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .back-to-home:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        color: #764ba2;
    }

    .login-title {
        color: #333;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .login-subtitle {
        color: #666;
        font-size: 16px;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-label {
        display: block;
        color: #333;
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-input {
        width: 100%;
        padding: 15px;
        border: 2px solid #e1e5e9;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input.error {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .form-input.success {
        border-color: #27ae60;
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 45px;
        cursor: pointer;
        color: #999;
        font-size: 16px;
        transition: color 0.3s ease;
        z-index: 5;
    }

    .password-toggle:hover {
        color: #667eea;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        margin: 20px 0;
        gap: 12px;
    }

    .checkbox-input {
        width: 18px;
        height: 18px;
        margin: 0;
        cursor: pointer;
    }

    .checkbox-label {
        color: #333;
        font-size: 14px;
        cursor: pointer;
        user-select: none;
    }

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .forgot-password-link {
        text-align: center;
        margin: 20px 0;
    }

    .forgot-password-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    .forgot-password-link a:hover {
        text-decoration: underline;
    }

    .forgot-password-link #refreshTokenLink {
        transition: all 0.3s ease;
        padding: 5px 8px;
        border-radius: 4px;
        background: rgba(108, 117, 125, 0.1);
        border: 1px solid transparent;
    }

    .forgot-password-link #refreshTokenLink:hover {
        color: #3085d6 !important;
        text-decoration: underline;
        background: rgba(48, 133, 214, 0.1);
        border-color: rgba(48, 133, 214, 0.2);
    }

    .forgot-password-link #refreshTokenLink i {
        margin-right: 4px;
        transition: transform 0.3s ease;
    }

    .forgot-password-link #refreshTokenLink:hover i {
        transform: rotate(180deg);
    }

    /* Old Browser Support Styles */
    .browser-warning {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #856404;
    }

    .session-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #28a745;
        animation: pulse 2s infinite;
    }

    .session-indicator.conflict {
        background: #dc3545;
        animation: blink 1s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }

    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #e1e5e9;
    }

    .register-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .email-verification-alert {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: none;
    }

    .email-verification-alert.show {
        display: block;
    }

    .alert-title {
        font-weight: 600;
        color: #f39c12;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .alert-text {
        color: #e67e22;
        font-size: 13px;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .resend-btn {
        background: #f39c12;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .resend-btn:hover:not(:disabled) {
        background: #e67e22;
    }

    .resend-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .validation-message {
        margin-top: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        display: none;
    }

    .validation-message.error {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
        border: 1px solid rgba(231, 76, 60, 0.2);
    }

    .validation-message.success {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
        border: 1px solid rgba(39, 174, 96, 0.2);
    }

    .validation-message.warning {
        background: rgba(255, 193, 7, 0.1);
        color: #f39c12;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 30px 20px;
            margin: 10px;
        }
        
        .login-title {
            font-size: 24px;
        }
    }
</style>
</head>

<body>
<div class="login-container">
    <div class="login-card">
        <!-- Session Status Indicator -->
        <div class="session-indicator" id="sessionIndicator" title="Session Status: Checking..." onclick="showSessionStatusHelp()" style="cursor: pointer;"></div>
        
        <!-- Back to Home Button -->
        <a href="{{ url('/') }}" class="back-to-home" title="Back to Home">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <div class="login-header">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to your PayPerViews account</p>
        </div>

        <!-- Email Verification Alert -->
        <div class="email-verification-alert" id="emailVerificationAlert">
            <div class="alert-title">Email Verification Required</div>
            <div class="alert-text">Your account is not verified. Please check your email for verification link or click below to resend.</div>
            <button type="button" class="resend-btn" id="resendVerificationBtn">
                Resend Verification Email
            </button>
        </div>

        <!-- Account Lock Status Alert -->
        @if(isset($accountLocked) && $accountLocked)
            <div class="validation-message error" style="display: block; margin-bottom: 15px;">
                <i class="fas fa-lock"></i> Account temporarily locked due to multiple failed login attempts. 
                Please try again {{ $lockExpiry }} or contact support for assistance.
            </div>
        @endif

        <!-- Remaining Attempts Warning -->
        @if(isset($remainingAttempts) && $remainingAttempts > 0 && $remainingAttempts < 5)
            <div class="validation-message warning" style="display: block; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle"></i> You have {{ $remainingAttempts }} login attempt{{ $remainingAttempts > 1 ? 's' : '' }} remaining before your account will be temporarily locked.
            </div>
        @endif

        <!-- Session Flash Messages for Attempts -->
        @if(session('warning_attempts'))
            <div class="validation-message warning" style="display: block; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle"></i> {{ session('warning_attempts') }}
            </div>
        @endif

        <form id="loginForm" action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Email/Username -->
            <div class="form-group">
                <label for="username" class="form-label">Email or Username</label>
                <input type="text" id="username" name="username" class="form-input" 
                       placeholder="Enter your email address or username"
                       value="{{ old('username') }}" required>
                <div class="validation-message" id="usernameValidation"></div>
            </div>

            <!-- Password -->
            <div class="form-group" style="position: relative;">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" 
                       placeholder="Enter your password" required>
                <span class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </span>
                <div class="validation-message" id="passwordValidation"></div>
            </div>

            <!-- Remember Me -->
            <div class="checkbox-container">
                <input type="checkbox" id="remember" name="remember" class="checkbox-input">
                <label for="remember" class="checkbox-label">
                    Remember me for 30 days
                </label>
            </div>

            <!-- Forgot Password Link -->
            <div class="forgot-password-link">
                <a href="{{ route('password.request') }}" id="forgotPasswordLink">Forgot your password?</a>
                <span style="margin: 0 10px; color: #ccc;">|</span>
                <a href="#" id="refreshTokenLink" style="color: #6c757d; font-size: 13px;" onclick="manualTokenRefresh(event)" title="Fix browser/session issues">
                    <i class="fas fa-sync-alt"></i> Fix Session Issues
                </a>
            </div>

            <!-- Display Server Errors -->
            @if ($errors->any())
                <div class="validation-message error" style="display: block; margin-bottom: 15px;">
                    @foreach ($errors->all() as $error)
                        • {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <!-- Display Success Messages --> 
            @if (session('success'))
                <div class="validation-message success" style="display: block; margin-bottom: 15px;">
                    {{ session('success') }}
                </div>
            @endif

            <button type="submit" class="submit-btn" id="submitBtn">
                Sign In
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fresh login page loaded');
    
    // Browser Compatibility and Multi-Session Detection
    initBrowserCompatibility();
    
    // Debug CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
    if (!csrfToken) {
        console.error('CSRF token not found in meta tag!');
    }

    // Form elements
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');
    const emailVerificationAlert = document.getElementById('emailVerificationAlert');
    const resendBtn = document.getElementById('resendVerificationBtn');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');

    // Debug form initialization
    console.log('Login form initialized:', {
        form: !!form,
        action: form ? form.action : 'N/A',
        method: form ? form.method : 'N/A',
        submitBtn: !!submitBtn,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...'
    });

    // Load remember me preference from cookie
    loadRememberPreference();

    // Password toggle function
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Utility functions
    function showValidation(element, message, type) {
        const validation = document.getElementById(element + 'Validation');
        
        if (validation) {
            validation.textContent = message;
            validation.className = `validation-message ${type}`;
            validation.style.display = 'block';
        }
    }

    function hideValidation(element) {
        const validation = document.getElementById(element + 'Validation');
        
        if (validation) {
            validation.style.display = 'none';
        }
    }

    // Load remember me preference from cookie
    function loadRememberPreference() {
        const rememberCookie = getCookie('remember_me_preference');
        if (rememberCookie === 'true') {
            rememberCheckbox.checked = true;
            
            // Load saved email if available
            const savedEmail = getCookie('saved_email');
            if (savedEmail && usernameInput.value === '') {
                usernameInput.value = savedEmail;
            }
        }
    }

    // Handle remember me checkbox
    rememberCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Set 30-day cookie preference
            setCookie('remember_me_preference', 'true', 30);
            showValidation('username', 'Login credentials will be remembered for 30 days', 'success');
        } else {
            // Remove cookies when unchecked
            deleteCookie('remember_me_preference');
            deleteCookie('saved_email');
            showValidation('username', 'Remember me disabled - cookies cleared', 'warning');
            
            setTimeout(() => {
                hideValidation('username');
            }, 3000);
        }
    });

    // Check remaining login attempts when user finishes entering username/email
    let usernameCheckTimeout;
    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameCheckTimeout);
        hideValidation('username'); // Clear previous messages
        
        // Wait 1 second after user stops typing to check attempts
        usernameCheckTimeout = setTimeout(() => {
            const username = this.value.trim();
            if (username.length >= 3) { // Only check if username/email is at least 3 characters
                checkUserLoginAttempts(username);
            }
        }, 1000);
    });

    // Function to check user login attempts
    async function checkUserLoginAttempts(username) {
        try {
            const response = await fetch('/login?' + new URLSearchParams({
                check_attempts: '1',
                username: username
            }), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                // Parse the response to extract attempt information
                const html = await response.text();
                
                // Check if there are warnings about remaining attempts
                if (html.includes('remaining before your account will be temporarily locked')) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const warningElement = doc.querySelector('.validation-message.warning');
                    
                    if (warningElement) {
                        const warningText = warningElement.textContent.trim();
                        showValidation('username', warningText, 'warning');
                    }
                } else if (html.includes('Account temporarily locked')) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const errorElement = doc.querySelector('.validation-message.error');
                    
                    if (errorElement) {
                        const errorText = errorElement.textContent.trim();
                        showValidation('username', errorText, 'error');
                    }
                }
            }
        } catch (error) {
            // Silently ignore errors to not disrupt user experience
            console.log('Could not check login attempts:', error);
        }
    }

    // CSRF Token Refresh Function - Enhanced to actually refresh token
    async function refreshCSRFToken() {
        try {
            console.log('Attempting to refresh CSRF token...');
            
            // Make a request to get a fresh token without reloading
            const response = await fetch('/login', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const html = await response.text();
                
                // Extract CSRF token from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const csrfToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                if (csrfToken) {
                    // Update the current page's CSRF token
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', csrfToken);
                    }
                    
                    const tokenInput = document.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = csrfToken;
                    }
                    
                    console.log('CSRF token refreshed successfully');
                    return csrfToken;
                } else {
                    throw new Error('Could not extract CSRF token from response');
                }
            } else {
                throw new Error('Failed to fetch fresh token');
            }
        } catch (error) {
            console.error('CSRF token refresh failed:', error);
            console.log('Falling back to page reload...');
            window.location.reload();
            return null;
        }
    }

    // Improved CSRF Token Getter Function
    function getCSRFToken() {
        // First try meta tag
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (metaToken) {
            return metaToken;
        }
        
        // Fallback: try to get from form
        const formToken = document.querySelector('input[name="_token"]')?.value;
        if (formToken) {
            return formToken;
        }
        
        // Last resort: generate from Laravel session
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'XSRF-TOKEN') {
                return decodeURIComponent(value);
            }
        }
        
        console.error('No CSRF token found!');
        return '';
    }

    // Show CSRF Token Refresh Alert
    function showCSRFRefreshAlert() {
        return Swal.fire({
            title: 'Session Expired',
            html: `
                <div style="text-align: left; font-size: 14px;">
                    <p><i class="fas fa-clock" style="color: #f39c12;"></i> Your session has expired for security reasons.</p>
                    <p>Click <strong>"Refresh Token"</strong> to get a new security token and try logging in again.</p>
                    <p style="margin-top: 15px; color: #666;">This helps keep your account secure.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-sync-alt"></i> Refresh Token',
            cancelButtonText: '<i class="fas fa-redo"></i> Reload Page',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    await refreshCSRFToken();
                    return true;
                } catch (error) {
                    Swal.showValidationMessage('Failed to refresh token. Please reload the page.');
                    return false;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Token Refreshed!',
                    text: 'Security token updated successfully. You can now try logging in again.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                return 'refreshed';
            } else if (result.isDismissed) {
                // User chose to reload page
                window.location.reload();
                return 'reloaded';
            }
        });
    }

    // Cookie helper functions
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
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
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    // Browser Compatibility and Multi-Session Support Functions
    function initBrowserCompatibility() {
        console.log('Initializing browser compatibility...');
        
        // Detect browser and version
        const browserInfo = detectBrowser();
        console.log('Browser detected:', browserInfo);
        
        // Initialize session status
        initializeSessionStatus();
        
        // Check for multiple tabs/sessions
        checkMultipleSessions();
        
        // Handle old browser compatibility
        if (browserInfo.isOldBrowser) {
            showOldBrowserWarning(browserInfo);
        }
        
        // Handle session storage conflicts
        handleSessionConflicts();
        
        // Auto-refresh for stale sessions
        setupSessionRefresh();
        
        // Verify session is active
        verifySessionActive();
    }

    function initializeSessionStatus() {
        const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        console.log('Session initialization:', {
            sessionKey: currentSessionKey ? 'Present' : 'Missing',
            csrfToken: csrfToken ? 'Present' : 'Missing'
        });
        
        if (currentSessionKey && csrfToken) {
            updateSessionIndicator('active', 'Session active and ready for login');
            console.log('Session status: Active');
            
            // Add a subtle visual feedback that system is ready
            setTimeout(() => {
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.style.boxShadow = '0 0 10px rgba(102, 126, 234, 0.3)';
                    setTimeout(() => {
                        submitBtn.style.boxShadow = '';
                    }, 2000);
                }
            }, 500);
        } else {
            updateSessionIndicator('inactive', 'Session not properly initialized');
            console.warn('Session status: Inactive - missing session data');
            
            // Attempt to fix inactive session
            setTimeout(() => {
                fixInactiveSession();
            }, 1000);
        }
    }

    function verifySessionActive() {
        // Perform a lightweight session check
        fetch('/login', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                console.log('Session verification successful');
                updateSessionIndicator('active', 'Session verified and ready for login');
            } else {
                console.warn('Session verification failed:', response.status);
                updateSessionIndicator('warning', 'Session verification failed');
                
                if (response.status === 419) {
                    console.log('CSRF token expired - will refresh on form submission');
                    updateSessionIndicator('expired', 'CSRF token expired - will refresh automatically');
                }
            }
        })
        .catch(error => {
            console.error('Session verification error:', error);
            updateSessionIndicator('error', 'Session verification error');
        });
    }

    function fixInactiveSession() {
        console.log('Attempting to fix inactive session...');
        
        Swal.fire({
            title: 'Activating Session',
            text: 'Initializing your login session...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            // Reload page to get fresh session
            window.location.reload();
        });
    }

    function detectBrowser() {
        const ua = navigator.userAgent;
        let browser = {
            name: 'Unknown',
            version: 0,
            isOldBrowser: false,
            needsPolyfill: false
        };

        // Chrome
        if (ua.includes('Chrome') && !ua.includes('Edge')) {
            const match = ua.match(/Chrome\/(\d+)/);
            browser.name = 'Chrome';
            browser.version = match ? parseInt(match[1]) : 0;
            browser.isOldBrowser = browser.version < 80;
        }
        // Firefox
        else if (ua.includes('Firefox')) {
            const match = ua.match(/Firefox\/(\d+)/);
            browser.name = 'Firefox';
            browser.version = match ? parseInt(match[1]) : 0;
            browser.isOldBrowser = browser.version < 75;
        }
        // Safari
        else if (ua.includes('Safari') && !ua.includes('Chrome')) {
            const match = ua.match(/Version\/(\d+)/);
            browser.name = 'Safari';
            browser.version = match ? parseInt(match[1]) : 0;
            browser.isOldBrowser = browser.version < 13;
        }
        // Edge
        else if (ua.includes('Edge') || ua.includes('Edg/')) {
            const match = ua.match(/Edg?\/(\d+)/);
            browser.name = 'Edge';
            browser.version = match ? parseInt(match[1]) : 0;
            browser.isOldBrowser = browser.version < 80;
        }
        // Internet Explorer
        else if (ua.includes('MSIE') || ua.includes('Trident')) {
            browser.name = 'IE';
            browser.version = 11; // Assume IE11
            browser.isOldBrowser = true;
            browser.needsPolyfill = true;
        }

        return browser;
    }

    function checkMultipleSessions() {
        const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
        const storedSessionKey = localStorage.getItem('login_session_key');
        
        if (storedSessionKey && storedSessionKey !== currentSessionKey) {
            console.log('Multiple session detected - clearing old session data');
            
            // Clear old session data
            localStorage.removeItem('login_session_key');
            localStorage.removeItem('login_cache_timestamp');
            sessionStorage.clear();
            
            // Show notification about session change
            showSessionChangeNotification();
        }
        
        // Store current session key
        localStorage.setItem('login_session_key', currentSessionKey);
    }

    function handleSessionConflicts() {
        // Listen for storage events (when another tab changes something)
        window.addEventListener('storage', function(e) {
            if (e.key === 'login_session_key' && e.newValue !== e.oldValue) {
                console.log('Session conflict detected from another tab');
                showMultiTabWarning();
            }
        });
        
        // Check for session conflicts periodically
        setInterval(function() {
            const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
            const storedSessionKey = localStorage.getItem('login_session_key');
            
            if (storedSessionKey && storedSessionKey !== currentSessionKey) {
                console.log('Session mismatch detected');
                handleSessionMismatch();
            }
        }, 30000); // Check every 30 seconds
    }

    function setupSessionRefresh() {
        // Auto-refresh CSRF token for old browsers every 10 minutes
        const browserInfo = detectBrowser();
        if (browserInfo.isOldBrowser) {
            setInterval(function() {
                console.log('Auto-refreshing session for old browser compatibility');
                refreshSessionForOldBrowser();
            }, 600000); // 10 minutes
        }
    }

    function showOldBrowserWarning(browserInfo) {
        const warningHtml = `
            <div style="text-align: left; font-size: 14px;">
                <p><i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i> 
                <strong>Browser Compatibility Notice</strong></p>
                <p>You're using ${browserInfo.name} version ${browserInfo.version}, which may have limited compatibility.</p>
                <p><strong>Recommendations:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Update to the latest browser version</li>
                    <li>Clear browser cache and cookies</li>
                    <li>Disable browser extensions that may interfere</li>
                    <li>If issues persist, try a different browser</li>
                </ul>
                <p style="color: #666; font-size: 12px;">This page will automatically handle compatibility issues.</p>
            </div>
        `;
        
        Swal.fire({
            title: 'Browser Compatibility',
            html: warningHtml,
            icon: 'warning',
            confirmButtonText: 'Continue',
            cancelButtonText: 'Help',
            showCancelButton: true,
            timer: 15000, // Auto-close after 15 seconds
            timerProgressBar: true
        }).then((result) => {
            if (result.isDismissed && result.dismiss === 'cancel') {
                showBrowserHelp();
            }
        });
    }

    function showSessionChangeNotification() {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
        
        toast.fire({
            icon: 'info',
            title: 'Session Updated',
            text: 'Your session has been refreshed for security.'
        });
    }

    function showMultiTabWarning() {
        updateSessionIndicator('conflict', 'Multiple tabs detected - may cause conflicts');
        
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 7000,
            timerProgressBar: true
        });
        
        toast.fire({
            icon: 'warning',
            title: 'Multiple Tabs Detected',
            text: 'Login in another tab may affect this session. Please use one tab for login.'
        });
    }

    function handleSessionMismatch() {
        console.log('Handling session mismatch - refreshing page');
        updateSessionIndicator('conflict', 'Session conflict detected');
        
        Swal.fire({
            title: 'Session Conflict',
            text: 'Your session has changed in another tab. The page will refresh to sync.',
            icon: 'info',
            timer: 3000,
            showConfirmButton: false,
            allowOutsideClick: false
        }).then(() => {
            window.location.reload();
        });
    }

    function updateSessionIndicator(status, message) {
        const indicator = document.getElementById('sessionIndicator');
        if (indicator) {
            // Remove all status classes
            indicator.className = 'session-indicator';
            
            // Add appropriate status class
            switch(status) {
                case 'active':
                    indicator.style.background = '#28a745'; // Green
                    indicator.style.animation = 'pulse 2s infinite';
                    break;
                case 'conflict':
                    indicator.className += ' conflict';
                    indicator.style.background = '#dc3545'; // Red
                    indicator.style.animation = 'blink 1s infinite';
                    break;
                case 'inactive':
                    indicator.style.background = '#6c757d'; // Gray
                    indicator.style.animation = 'none';
                    break;
                case 'warning':
                    indicator.style.background = '#ffc107'; // Yellow
                    indicator.style.animation = 'pulse 1s infinite';
                    break;
                case 'expired':
                    indicator.style.background = '#fd7e14'; // Orange
                    indicator.style.animation = 'pulse 0.5s infinite';
                    break;
                case 'error':
                    indicator.style.background = '#dc3545'; // Red
                    indicator.style.animation = 'blink 0.5s infinite';
                    break;
                default:
                    indicator.style.background = '#28a745'; // Default green
                    indicator.style.animation = 'pulse 2s infinite';
            }
            
            indicator.title = 'Session Status: ' + message;
            console.log('Session indicator updated:', status, '-', message);
        }
    }

    function showSessionStatusHelp() {
        const currentStatus = document.getElementById('sessionIndicator')?.title || 'Unknown';
        
        Swal.fire({
            title: 'Session Status Help',
            html: `
                <div style="text-align: left; font-size: 14px;">
                    <p><strong>Current Status:</strong> ${currentStatus}</p>
                    <hr style="margin: 15px 0;">
                    <p><strong>Status Indicators:</strong></p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li><span style="color: #28a745;">●</span> <strong>Green:</strong> Session active and ready</li>
                        <li><span style="color: #ffc107;">●</span> <strong>Yellow:</strong> Session warning or verification needed</li>
                        <li><span style="color: #fd7e14;">●</span> <strong>Orange:</strong> Session expired but will auto-refresh</li>
                        <li><span style="color: #6c757d;">●</span> <strong>Gray:</strong> Session inactive</li>
                        <li><span style="color: #dc3545;">●</span> <strong>Red:</strong> Session conflict or error</li>
                    </ul>
                    <hr style="margin: 15px 0;">
                    <p><strong>Solutions:</strong></p>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>If inactive/gray: Refresh the page</li>
                        <li>If red/conflict: Close other login tabs</li>
                        <li>If orange/expired: Wait for auto-refresh or click "Fix Session Issues"</li>
                        <li>If persistent issues: Clear browser cache and cookies</li>
                    </ul>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Got it',
            showCancelButton: true,
            cancelButtonText: 'Refresh Page',
            width: '500px'
        }).then((result) => {
            if (result.isDismissed && result.dismiss === 'cancel') {
                window.location.reload();
            }
        });
    }

    function refreshSessionForOldBrowser() {
        // Lightweight session refresh for old browsers
        try {
            fetch('/login', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin'
            }).then(response => {
                if (response.ok) {
                    console.log('Session refreshed successfully for old browser');
                }
            }).catch(error => {
                console.log('Session refresh failed for old browser:', error);
            });
        } catch (error) {
            console.log('Session refresh not supported in this browser');
        }
    }

    function showBrowserHelp() {
        const helpHtml = `
            <div style="text-align: left; font-size: 14px;">
                <h4 style="margin-bottom: 15px; color: #333;">Browser Update Instructions</h4>
                
                <div style="margin-bottom: 15px;">
                    <strong>Chrome:</strong> Menu → Help → About Google Chrome
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Firefox:</strong> Menu → Help → About Firefox
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Safari:</strong> Safari → About Safari (Mac) or Help → About Safari
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Edge:</strong> Menu → Help and feedback → About Microsoft Edge
                </div>
                
                <hr style="margin: 15px 0;">
                
                <p><strong>Alternative browsers:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Google Chrome (Recommended)</li>
                    <li>Mozilla Firefox</li>
                    <li>Microsoft Edge</li>
                    <li>Safari (for Mac users)</li>
                </ul>
                
                <p style="color: #666; font-size: 12px; margin-top: 15px;">
                    If you continue having issues, please contact our support team.
                </p>
            </div>
        `;
        
        Swal.fire({
            title: 'Browser Help',
            html: helpHtml,
            icon: 'info',
            confirmButtonText: 'Close',
            width: '500px'
        });
    }

    // Email verification alert functionality
    function showEmailVerificationAlert(email) {
        emailVerificationAlert.classList.add('show');
        
        if (email) {
            resendBtn.disabled = false;
            resendBtn.onclick = function() {
                resendVerificationEmail(email);
            };
        } else {
            // No email available (username was used for login)
            resendBtn.disabled = true;
            resendBtn.innerHTML = 'Email required to resend';
            resendBtn.onclick = function() {
                Swal.fire({
                    title: 'Email Required',
                    text: 'Please enter your email address to resend verification.',
                    input: 'email',
                    inputPlaceholder: 'Enter your email address',
                    showCancelButton: true,
                    confirmButtonText: 'Send Verification',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        resendVerificationEmail(result.value);
                    }
                });
            };
        }
    }

    function hideEmailVerificationAlert() {
        emailVerificationAlert.classList.remove('show');
    }

    // Resend verification email
    async function resendVerificationEmail(email) {
        resendBtn.disabled = true;
        const originalText = resendBtn.innerHTML;
        resendBtn.innerHTML = '<span class="loading-spinner"></span>Sending...';

        try {
            const response = await fetch('/resend-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email }),
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (response.ok && data.success) {
                Swal.fire({
                    title: 'Email Sent!',
                    text: 'Verification email has been sent. Please check your inbox.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                hideEmailVerificationAlert();
            } else {
                throw new Error(data.message || 'Failed to send verification email');
            }
        } catch (error) {
            console.error('Resend verification error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Failed to send verification email. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } finally {
            resendBtn.disabled = false;
            resendBtn.innerHTML = originalText;
        }
    }

    // Manual CSRF Token Refresh (for proactive refresh)
    window.manualTokenRefresh = async function(event) {
        if (event) event.preventDefault();
        
        // Check current session status
        const indicator = document.getElementById('sessionIndicator');
        const currentStatus = indicator?.title || '';
        
        let dialogText = 'This will refresh your security token to prevent session timeouts.';
        if (currentStatus.includes('inactive') || currentStatus.includes('expired')) {
            dialogText = 'This will fix your inactive session and refresh your security token.';
        } else if (currentStatus.includes('conflict')) {
            dialogText = 'This will resolve session conflicts and refresh your security token.';
        }
        
        Swal.fire({
            title: 'Fix Session Issues?',
            html: `
                <div style="text-align: left; font-size: 14px;">
                    <p><i class="fas fa-shield-alt" style="color: #3085d6;"></i> ${dialogText}</p>
                    <p><strong>Current Status:</strong> ${currentStatus}</p>
                    <p style="margin-top: 10px;">Recommended if you've been on this page for a while or experiencing login issues.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-sync-alt"></i> Fix Session Now',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    updateSessionIndicator('warning', 'Refreshing session...');
                    await refreshCSRFToken();
                    return true;
                } catch (error) {
                    Swal.showValidationMessage('Failed to refresh token. Please try again.');
                    return false;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Session Fixed!',
                    text: 'Your session has been refreshed successfully.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                showValidation('username', 'Session and security token refreshed successfully', 'success');
                setTimeout(() => {
                    hideValidation('username');
                }, 3000);
            }
        });
    };

    // Test forgot password system
    forgotPasswordLink.addEventListener('click', function(e) {
        const currentEmail = usernameInput.value.trim();
        
        if (currentEmail && currentEmail.includes('@')) {
            // If email is entered, pass it to forgot password page
            const url = new URL(this.href);
            url.searchParams.set('email', currentEmail);
            this.href = url.toString();
        }
    });

    // Enhanced forgot password testing function
    window.testForgotPassword = function(email) {
        if (!email) {
            email = usernameInput.value.trim() || prompt('Enter email address to test password reset:');
        }
        
        if (email) {
            fetch('/password/email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email }),
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Password reset test result:', data);
                if (data.status === 'passwords.sent') {
                    Swal.fire({
                        title: 'Password Reset Email Sent!',
                        text: 'Password reset link has been sent to: ' + email,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Test Result',
                        text: data.message || 'Password reset system tested successfully',
                        icon: 'info'
                    });
                }
            })
            .catch(error => {
                console.error('Password reset test error:', error);
                Swal.fire({
                    title: 'Test Error',
                    text: 'Failed to test password reset system',
                    icon: 'error'
                });
            });
        }
    };

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Form submission started:', {
            username: usernameInput.value.trim(),
            hasPassword: !!passwordInput.value,
            remember: rememberCheckbox.checked,
            action: form.action,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')?.substring(0, 10) + '...'
        });

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Signing In...';

        // Clear previous validations
        hideValidation('username');
        hideValidation('password');

        try {
            // Browser compatibility check before submission
            const browserInfo = detectBrowser();
            if (browserInfo.needsPolyfill) {
                // Use fallback for very old browsers
                return submitWithFallback();
            }

            // Save email for remember me if checked
            if (rememberCheckbox.checked) {
                setCookie('saved_email', usernameInput.value.trim(), 30);
            }

            // Update session tracking
            const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
            localStorage.setItem('login_session_key', currentSessionKey);

            // Get fresh CSRF token before submission
            const currentToken = getCSRFToken();
            console.log('Current CSRF token:', currentToken ? 'Present' : 'Missing');
            
            const formData = new FormData(form);
            
            // Ensure we have the latest token
            if (currentToken) {
                formData.set('_token', currentToken);
            }
            
            console.log('Submitting login form...');
            
            let response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache' // Prevent caching issues
                },
                credentials: 'same-origin'
            });
            
            console.log('Login response status:', response.status);
            
            // Handle CSRF token mismatch with ONE retry only
            if (response.status === 419) {
                console.log('CSRF token mismatch (419) - attempting refresh and retry...');
                
                try {
                    const newToken = await refreshCSRFToken();
                    if (newToken) {
                        // Create completely fresh form data with new token
                        const retryFormData = new FormData();
                        retryFormData.append('_token', newToken);
                        retryFormData.append('username', usernameInput.value.trim());
                        retryFormData.append('password', passwordInput.value);
                        if (rememberCheckbox.checked) {
                            retryFormData.append('remember', '1');
                        }
                        
                        console.log('Retrying login with refreshed token...');
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
                        
                        console.log('Retry response status:', response.status);
                        
                        // If still 419 after retry, show error instead of reload
                        if (response.status === 419) {
                            console.log('CSRF token still invalid after retry');
                            Swal.fire({
                                title: 'Session Issue!',
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
                        throw new Error('Failed to refresh CSRF token');
                    }
                } catch (refreshError) {
                    console.error('CSRF refresh failed:', refreshError);
                    
                    Swal.fire({
                        title: 'Session Problem!',
                        text: 'Unable to refresh your session. Please try again.',
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

            if (response.ok || response.redirected) {
                console.log('Login successful! Response:', {
                    status: response.status,
                    redirected: response.redirected,
                    url: response.url
                });
                
                // Clear the form for security
                form.reset();
                
                Swal.fire({
                    title: 'Login Successful!',
                    text: 'Welcome back! Redirecting to your dashboard...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    // Determine redirect URL
                    let redirectUrl = '/user/dashboard';
                    
                    if (response.redirected && response.url) {
                        // If the response was redirected, use the redirect URL
                        redirectUrl = response.url;
                    } else {
                        // Check for intended redirect from URL params
                        const urlParams = new URLSearchParams(window.location.search);
                        const intendedUrl = urlParams.get('redirect');
                        if (intendedUrl && !intendedUrl.includes('/login')) {
                            redirectUrl = intendedUrl;
                        }
                    }
                    
                    console.log('Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                });
            } else if (response.status === 403) {
                console.log('Login failed - Email not verified (403)');
                // Email not verified
                const data = await response.json();
                
                // Get email - try from response data first, then determine if username field contains email
                let emailToResend = data.user_email;
                if (!emailToResend) {
                    const usernameValue = usernameInput.value.trim();
                    if (usernameValue.includes('@')) {
                        emailToResend = usernameValue;
                    } else {
                        // If username was provided, we need to show alert but may not be able to auto-resend
                        emailToResend = null;
                    }
                }
                
                showEmailVerificationAlert(emailToResend);
                
                Swal.fire({
                    title: 'Email Not Verified!',
                    text: 'Please verify your email address before logging in. Check your inbox or click resend below.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else if (response.status === 419) {
                // Final 419 check - this should rarely happen after retry
                console.log('CSRF token still invalid after retry');
                Swal.fire({
                    title: 'Session Expired!',
                    text: 'Your session has expired. Please refresh the page and try again.',
                    icon: 'warning',
                    confirmButtonText: 'Refresh Page'
                }).then(() => {
                    window.location.reload();
                });
            } else if (response.status === 422) {
                console.log('Validation errors (422)');
                const data = await response.json();
                
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const message = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                        showValidation(field, message, 'error');
                    });
                }
                
                // Check if it's an email verification issue
                if (data.message && data.message.includes('verify')) {
                    showEmailVerificationAlert(usernameInput.value.trim());
                }
                
            } else if (response.status === 401) {
                // Invalid credentials or account locked
                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    // Fallback to generic error if JSON parsing fails
                    Swal.fire({
                        title: 'Login Failed!',
                        text: 'Invalid credentials. Please check your username/email and password.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                    return;
                }
                
                console.log('401 Response data:', data); // Debug log
                
                if (data.error_type === 'account_locked') {
                    // Account is currently locked
                    const unlockTime = data.unlock_time_human || 'later';
                    const durationText = data.duration_text || 'several minutes';
                    
                    Swal.fire({
                        title: 'Account Locked!',
                        html: `
                            <div style="text-align: left; font-size: 14px;">
                                <p><i class="fas fa-lock" style="color: #e74c3c;"></i> Your account has been temporarily locked due to multiple failed login attempts.</p>
                                <p><strong>Unlock Time:</strong> ${unlockTime}</p>
                                <p><strong>Duration:</strong> Approximately ${durationText}</p>
                                <p style="margin-top: 15px;">Please wait for the lock to expire or contact our support team for immediate assistance.</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                    
                    showValidation('username', data.message || 'Account temporarily locked', 'error');
                    
                } else if (data.error_type === 'account_locked_now') {
                    // Account just got locked due to this attempt
                    const lockDuration = data.lock_duration_minutes || 10;
                    const durationText = lockDuration < 60 
                        ? lockDuration + ' minute' + (lockDuration !== 1 ? 's' : '')
                        : Math.round(lockDuration / 60 * 10) / 10 + ' hour' + (lockDuration >= 120 ? 's' : '');
                    
                    Swal.fire({
                        title: 'Account Locked!',
                        html: `
                            <div style="text-align: left; font-size: 14px;">
                                <p><i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> Your account has been locked due to too many failed login attempts.</p>
                                <p><strong>Lock Duration:</strong> ${durationText}</p>
                                <p style="margin-top: 15px;">Please wait ${durationText} before trying again, or contact our support team for assistance.</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                    
                    showValidation('password', data.message || 'Account locked due to failed attempts', 'error');
                    
                } else if (data.error_type === 'invalid_password' && data.remaining_attempts !== undefined) {
                    // Invalid password with remaining attempts info
                    const remaining = data.remaining_attempts;
                    const isLastAttempt = remaining === 1;
                    
                    let warningHtml = `
                        <div style="text-align: left; font-size: 14px;">
                            <p><i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i> Invalid password.</p>
                    `;
                    
                    if (remaining > 0) {
                        warningHtml += `
                            <p><strong>Remaining Attempts:</strong> ${remaining} attempt${remaining > 1 ? 's' : ''}</p>
                            ${isLastAttempt ? '<p style="color: #e74c3c;"><strong>Warning:</strong> One more failed attempt will lock your account!</p>' : ''}
                        `;
                    }
                    
                    warningHtml += `
                            <p style="margin-top: 15px;">Please check your password and try again.</p>
                        </div>
                    `;
                    
                    Swal.fire({
                        title: isLastAttempt ? 'Last Attempt Warning!' : 'Invalid Password',
                        html: warningHtml,
                        icon: isLastAttempt ? 'warning' : 'error',
                        confirmButtonText: 'Try Again',
                        allowOutsideClick: false
                    });
                    
                    showValidation('password', data.message || 'Invalid password', 'error');
                    
                } else {
                    // Generic 401 error
                    showValidation('password', data.message || 'Invalid email/username or password', 'error');
                    
                    Swal.fire({
                        title: 'Login Failed!',
                        text: data.message || 'Invalid email/username or password. Please check your credentials and try again.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
                
            } else if (response.status === 500) {
                // Server error
                console.error('Server error (500):', response);
                Swal.fire({
                    title: 'Server Error!',
                    text: 'There was a server error. Please try again or contact support if the issue persists.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                
            } else {
                // Handle any other response status
                console.log('Unhandled response status:', response.status);
                let errorMessage = 'An unexpected error occurred. Please try again.';
                
                try {
                    const data = await response.json();
                    errorMessage = data.message || errorMessage;
                } catch (e) {
                    // If we can't parse JSON, use default message
                }
                
                Swal.fire({
                    title: 'Login Failed!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        } catch (error) {
            console.error('Login error:', error);
            
            // Generic error handling - most errors should be handled in the response checking above
            Swal.fire({
                title: 'Login Failed!',
                text: 'An unexpected error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Sign In';
        }
    });

    // Check for logout success message from session
    @if(session('logout_completed'))
    Swal.fire({
        title: 'Logged Out!',
        text: 'You have been successfully logged out.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
    @endif

    // Check for logout success message from URL parameter (fallback)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('logout') === '1') {
        Swal.fire({
            title: 'Logged Out!',
            text: 'You have been successfully logged out.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
        
        // Clean up URL by removing logout parameter
        if (window.history.replaceState) {
            const cleanUrl = window.location.pathname + window.location.search.replace(/[?&]logout=1/, '').replace(/^&/, '?');
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }
    
    // Check if we need to show session expired message (only if explicitly indicated)
    console.log('Checking URL parameters for session expired...');
    console.log('session_expired param:', urlParams.get('session_expired'));
    console.log('csrf_error param:', urlParams.get('csrf_error'));
    console.log('Current URL:', window.location.href);
    console.log('Document referrer:', document.referrer);
    
    // Only show session expired if explicitly indicated by URL parameters
    // Do NOT show it just because user came from a protected route
    if (urlParams.get('session_expired') === '1' || urlParams.get('csrf_error') === '1') {
        console.log('Showing session expired alert due to URL parameters');
        Swal.fire({
            title: 'Session Expired!',
            text: 'Your session has expired. Please try logging in again.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else {
        console.log('No explicit session expired parameters found - not showing alert');
    }

    console.log('Login page initialized successfully');
    console.log('Available test function: testForgotPassword()');
    console.log('Available help function: showSessionStatusHelp()');
    
    // Show success message if everything is working properly
    const sessionIndicator = document.getElementById('sessionIndicator');
    if (sessionIndicator && sessionIndicator.style.background === 'rgb(40, 167, 69)') {
        console.log('✅ All systems operational - ready for login');
    }

    // Make session status help globally available
    window.showSessionStatusHelp = showSessionStatusHelp;

    // Fallback submission for very old browsers
    function submitWithFallback() {
        console.log('Using fallback submission for old browser');
        
        Swal.fire({
            title: 'Processing Login...',
            text: 'Using compatibility mode for your browser.',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            // Use traditional form submission for old browsers
            form.submit();
        });
    }

    // Add periodic session health check for multi-tab scenarios
    setInterval(function() {
        const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
        const storedSessionKey = localStorage.getItem('login_session_key');
        
        if (storedSessionKey && currentSessionKey && storedSessionKey !== currentSessionKey) {
            console.log('Session mismatch detected in health check');
            handleSessionMismatch();
        }
    }, 60000); // Check every minute

    // Handle page visibility changes (when user switches tabs)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page became visible again - check for session changes
            setTimeout(checkMultipleSessions, 500);
        }
    });

    // Enhanced beforeunload handling for old browsers
    window.addEventListener('beforeunload', function(e) {
        // Clean up session storage for better compatibility
        if (sessionStorage.getItem('login_temp_data')) {
            sessionStorage.removeItem('login_temp_data');
        }
    });
});
</script>

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>

</body>
</html>
