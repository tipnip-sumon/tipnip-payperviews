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
                <a href="#" id="refreshTokenLink" style="color: #6c757d; font-size: 13px;" onclick="manualTokenRefresh(event)" title="100% Fix: Clear cache & refresh session (like Ctrl+F5)">
                    <i class="fas fa-broom"></i> Clear Cache
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
    // Proactive Session Health Check
    async function checkSessionHealth() {
        try {
            // Check if CSRF token exists and is valid
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                // console.log('No CSRF token found, refreshing session...');
                await refreshCSRFToken().catch(err => console.log('CSRF refresh failed:', err));
                return;
            }
            
            // Test session with a simple request
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
            
            const response = await fetch('/session/refresh', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (response.status === 419) {
                // console.log('Session health check detected CSRF issue, fixing...');
                await refreshCSRFToken().catch(err => console.log('CSRF refresh failed:', err));
            } else if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    //console.log('Session health check passed');
                }
            }
            
        } catch (error) {
            if (error.name === 'AbortError') {
                //console.log('Session health check timed out');
            } else {
                //console.log('Session health check failed, will handle on form submission:', error);
            }
            // Don't show error to user, just log it and handle later
        }
    }

    // Handle post-logout state to force fresh page
    function handlePostLogoutState() {
        try {
            // Check URL parameters for logout indicators
            const urlParams = new URLSearchParams(window.location.search);
            const hasRefreshParam = urlParams.has('refresh');
            const hasLogoutTimestamp = urlParams.has('t');
            
            // Check session flash data for logout completion
            const logoutCompleted = @json(session('logout_completed', false));
            const sessionDestroyed = @json(session('session_destroyed', false));
            const forcePageRefresh = @json(session('force_page_refresh', false));
            
            // Check if browser indicates this is from logout
            const referrer = document.referrer;
            const fromLogout = referrer && (referrer.includes('logout') || referrer.includes('force-logout'));
            
            // Detect if we need to force a complete refresh
            if (hasRefreshParam || logoutCompleted || sessionDestroyed || forcePageRefresh || fromLogout) {
                console.log('Post-logout state detected, ensuring fresh page state');
                
                // Clear any cached form data
                const form = document.getElementById('loginForm');
                if (form) {
                    form.reset();
                }
                
                // Force complete cache refresh if this is the first post-logout load
                if (!sessionStorage.getItem('post_logout_refresh_done')) {
                    sessionStorage.setItem('post_logout_refresh_done', 'true');
                    
                    // Clean the URL and force refresh
                    const cleanUrl = window.location.pathname;
                    if (window.location.search.includes('refresh=1') || window.location.search.includes('t=')) {
                        console.log('Forcing complete page refresh to clear post-logout state');
                        window.location.replace(cleanUrl + '?fresh=' + Date.now());
                        return;
                    }
                }
                
                // Ensure CSRF token is fresh
                setTimeout(() => {
                    refreshCSRFToken();
                }, 100);
            }
            
        } catch (error) {
            // console.log('Error in handlePostLogoutState:', error);
        }
    }

document.addEventListener('DOMContentLoaded', function() {
    // console.log('Fresh login page loaded');

    // Wrap everything in try-catch to prevent browser extension errors
    try {
        // CRITICAL: Check if user just logged out and force complete page refresh
        handlePostLogoutState();
        
        // Proactive session health check to prevent issues
        checkSessionHealth();
        
        // Browser Compatibility and Multi-Session Detection
        initBrowserCompatibility();
        
        // Cache Detection and Warning
        detectCacheIssues();
    } catch (initError) {
        // console.log('Initialization error (possibly browser extension):', initError);
        // Continue with basic functionality
    }
    
    // Debug CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    //console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
    if (!csrfToken) {
        //console.error('CSRF token not found in meta tag!');
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
    });

    // CSRF Token Refresh Function - Enhanced to use session refresh endpoint
    async function refreshCSRFToken() {
        try {
            //console.log('Attempting to refresh CSRF token via session refresh...');
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
            
            // Use our new session refresh endpoint for more reliable token refresh
            const response = await fetch('/session/refresh', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.csrf_token) {
                    // Update the current page's CSRF token
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', data.csrf_token);
                    }
                    
                    const tokenInput = document.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = data.csrf_token;
                    }
                    
                    //console.log('CSRF token and session refreshed successfully');
                    return data.csrf_token;
                } else {
                    throw new Error('Invalid response format from session refresh');
                }
            } else {
                // Fallback to original method if endpoint fails
                //console.log('Session refresh endpoint failed, trying fallback method...');
                return await refreshCSRFTokenFallback().catch(err => {
                    //console.log('Fallback CSRF refresh failed:', err);
                    return null;
                });
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                //console.log('CSRF token refresh timed out');
            } else {
                console.error('CSRF token refresh failed:', error);
            }
            //console.log('Using fallback method...');
            return await refreshCSRFTokenFallback().catch(err => {
                //console.log('Fallback CSRF refresh failed:', err);
                return null;
            });
        }
    }
    
    // Fallback CSRF refresh method
    async function refreshCSRFTokenFallback() {
        try {
            //console.log('Using fallback CSRF refresh method...');
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
            
            const response = await fetch('/login', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
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
                    
                    //console.log('CSRF token refreshed via fallback method');
                    return csrfToken;
                } else {
                    throw new Error('Could not extract CSRF token from response');
                }
            } else {
                throw new Error('Fallback method also failed');
            }
        } catch (error) {
            console.error('Fallback CSRF token refresh failed:', error);
            //console.log('Final fallback: page reload...');
            // Only reload as absolute last resort
            setTimeout(() => {
                window.location.href = '/login';
            }, 1000);
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
        //console.log('Initializing browser compatibility...');

        // Detect browser and version
        const browserInfo = detectBrowser();
        //console.log('Browser detected:', browserInfo);

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

        //console.log('Session initialization:', {
        //    sessionKey: currentSessionKey ? 'Present' : 'Missing',
        //    csrfToken: csrfToken ? 'Present' : 'Missing'
        //});

        if (currentSessionKey && csrfToken) {
            updateSessionIndicator('active', 'Session active and ready for login');
            //console.log('Session status: Active');
            
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
                //console.log('Session verification successful');
                updateSessionIndicator('active', 'Session verified and ready for login');
            } else {
                //console.warn('Session verification failed:', response.status);
                updateSessionIndicator('warning', 'Session verification failed');
                
                if (response.status === 419) {
                    //console.log('CSRF token expired - will refresh on form submission');
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
        //console.log('Attempting to fix inactive session...');

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
            //console.log('Multiple session detected - clearing old session data');

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
                //console.log('Session conflict detected from another tab');
                showMultiTabWarning();
            }
        });
        
        // Check for session conflicts periodically
        setInterval(function() {
            const currentSessionKey = document.querySelector('meta[name="session-key"]')?.getAttribute('content');
            const storedSessionKey = localStorage.getItem('login_session_key');
            
            if (storedSessionKey && storedSessionKey !== currentSessionKey) {
                //console.log('Session mismatch detected');
                handleSessionMismatch();
            }
        }, 30000); // Check every 30 seconds
    }

    function setupSessionRefresh() {
        // Auto-refresh CSRF token for old browsers every 10 minutes
        const browserInfo = detectBrowser();
        if (browserInfo.isOldBrowser) {
            setInterval(function() {
                //console.log('Auto-refreshing session for old browser compatibility');
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
        //console.log('Handling session mismatch - refreshing page');
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
            //console.log('Session indicator updated:', status, '-', message);
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
                        <li>If orange/expired: Wait for auto-refresh or click "Clear Cache" for 100% fix</li>
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
                    //console.log('Session refreshed successfully for old browser');
                }
            }).catch(error => {
                //console.log('Session refresh failed for old browser:', error);
            });
        } catch (error) {
            //console.log('Session refresh not supported in this browser');
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

    // Cache Issue Detection
    function detectCacheIssues() {
        //console.log('Checking for cache issues...');

        // Check if this is a cache-busted reload
        const urlParams = new URLSearchParams(window.location.search);
        const cacheCleared = urlParams.get('_cache_clear');
        
        if (cacheCleared) {
            // Remove cache clear parameter from URL
            urlParams.delete('_cache_clear');
            urlParams.delete('_t');
            const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', cleanUrl);
            
            // Show success message for cache clear
            setTimeout(() => {
                showValidation('username', 'Cache cleared successfully! Login should work perfectly now.', 'success');
                setTimeout(() => hideValidation('username'), 5000);
            }, 500);
            return;
        }
        
        // Detect potential cache issues
        let cacheWarnings = [];
        
        // Check for stale timestamps in localStorage
        try {
            const storedData = localStorage.getItem('tipnip_session_data');
            if (storedData) {
                const data = JSON.parse(storedData);
                const hoursSinceStored = (Date.now() - (data.timestamp || 0)) / (1000 * 60 * 60);
                if (hoursSinceStored > 24) {
                    cacheWarnings.push('Old session data detected');
                }
            }
        } catch (e) {
            //console.log('No localStorage data found');
        }
        
        // Check for service worker cache
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                if (registrations.length > 0) {
                    cacheWarnings.push('Service worker cache detected');
                }
            });
        }
        
        // Check browser cache indicators
        const performance = window.performance;
        if (performance && performance.navigation && performance.navigation.type === 1) {
            // This is a reload, might have cache issues
            cacheWarnings.push('Page reloaded from cache');
        }
        
        // Show cache warning if issues detected
        if (cacheWarnings.length > 0) {
            //console.log('Cache issues detected:', cacheWarnings);

            setTimeout(() => {
                const refreshLink = document.getElementById('refreshTokenLink');
                if (refreshLink) {
                    refreshLink.style.color = '#ffc107';
                    refreshLink.style.fontWeight = 'bold';
                    refreshLink.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Clear Cache (Recommended)';
                    refreshLink.title = 'Cache issues detected! Click to clear and fix login problems.';
                }
            }, 2000);
        }
    }

    // Manual CSRF Token Refresh with Complete Browser Cache Clearing
    window.manualTokenRefresh = async function(event) {
        if (event) event.preventDefault();
        
        // Check current session status
        const indicator = document.getElementById('sessionIndicator');
        const currentStatus = indicator?.title || '';
        
        let dialogText = 'This will completely clear browser cache and refresh your security token to prevent session timeouts.';
        if (currentStatus.includes('inactive') || currentStatus.includes('expired')) {
            dialogText = 'This will fix your inactive session, clear browser cache, and refresh your security token.';
        } else if (currentStatus.includes('conflict')) {
            dialogText = 'This will resolve session conflicts, clear browser cache, and refresh your security token.';
        }
        
        Swal.fire({
            title: 'Fix Session Issues?',
            html: `
                <div style="text-align: left; font-size: 14px;">
                    <p><i class="fas fa-shield-alt" style="color: #3085d6;"></i> ${dialogText}</p>
                    <p><strong>Current Status:</strong> ${currentStatus}</p>
                    <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;">
                        <p style="margin: 0; font-weight: bold;"><i class="fas fa-broom" style="color: #28a745;"></i> What this will do:</p>
                        <ul style="margin: 5px 0; padding-left: 20px; font-size: 13px;">
                            <li>Clear all browser cache and cookies for this site</li>
                            <li>Refresh CSRF security tokens</li>
                            <li>Reset session status to active</li>
                            <li>Hard reload the page (like Ctrl+F5)</li>
                        </ul>
                    </div>
                    <p style="color: #28a745;"><strong>✅ 100% guaranteed to fix session issues!</strong></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-magic"></i> Fix & Clear Cache Now',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: async () => {
                try {
                    updateSessionIndicator('warning', 'Clearing cache and refreshing session...');
                    
                    // Step 1: Clear all possible browser storage
                    if ('caches' in window) {
                        const cacheNames = await caches.keys();
                        await Promise.all(cacheNames.map(name => caches.delete(name)));
                        //console.log('Service worker caches cleared');
                    }
                    
                    // Step 2: Clear localStorage and sessionStorage
                    if (typeof(Storage) !== "undefined") {
                        localStorage.clear();
                        sessionStorage.clear();
                        //console.log('Browser storage cleared');
                    }
                    
                    // Step 3: Clear cookies for this domain
                    document.cookie.split(";").forEach(function(c) { 
                        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
                    });
                    //console.log('Cookies cleared');
                    
                    // Step 4: Refresh CSRF token
                    await refreshCSRFToken();
                    //console.log('CSRF token refreshed');
                    
                    return true;
                } catch (error) {
                    console.error('Cache clearing error:', error);
                    Swal.showValidationMessage('Failed to clear cache. Will proceed with hard refresh.');
                    return true; // Still proceed to hard refresh
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show success message briefly before hard refresh
                Swal.fire({
                    title: 'Cache Cleared!',
                    html: `
                        <div style="text-align: center;">
                            <p><i class="fas fa-check-circle" style="color: #28a745; font-size: 24px;"></i></p>
                            <p>Browser cache cleared successfully!</p>
                            <p style="color: #6c757d; font-size: 13px;">Performing hard refresh now...</p>
                        </div>
                    `,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    // Force a hard refresh (equivalent to Ctrl+F5)
                    //console.log('Performing hard refresh...');

                    // Method 1: Try using location.reload with force parameter
                    try {
                        window.location.reload(true);
                    } catch (e) {
                        // Method 2: If that fails, use cache-busting reload
                        const url = new URL(window.location);
                        url.searchParams.set('_t', Date.now());
                        url.searchParams.set('_cache_clear', '1');
                        window.location.href = url.toString();
                    }
                });
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
                //console.log('Password reset test result:', data);
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
            //console.log('Current CSRF token:', currentToken ? 'Present' : 'Missing');
            
            const formData = new FormData(form);
            
            // Ensure we have the latest token
            if (currentToken) {
                formData.set('_token', currentToken);
            }

            //console.log('Submitting login form...');

            // Small delay to ensure all storage operations complete before submission
            await new Promise(resolve => setTimeout(resolve, 100));
            //console.log('🚀 STORAGE DELAY: Completed - proceeding with form submission');

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

            //console.log('Login response status:', response.status);

            // Handle CSRF token mismatch with ONE retry only
            if (response.status === 419) {
                //console.log('CSRF token mismatch (419) - attempting refresh and retry...');

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

                        //console.log('Retrying login with refreshed token...');
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

                        //console.log('Retry response status:', response.status);

                        // If still 419 after retry, handle gracefully without showing error
                        if (response.status === 419) {
                            //console.log('CSRF token still invalid after retry - handling automatically');

                            // Instead of showing error, silently regenerate session and try again
                            try {
                                // Clear any session issues by forcing a fresh start
                                await fetch('/session/refresh', {
                                    method: 'POST',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin'
                                });
                                
                                // Auto-reload page to get fresh session without user intervention
                                //console.log('Auto-refreshing page to resolve session issue...');
                                window.location.reload();
                                return;
                                
                            } catch (error) {
                                //console.log('Session refresh failed, showing user-friendly message');
                                
                                // Only show error as last resort, but make it user-friendly
                                Swal.fire({
                                    title: 'Login Refresh Needed',
                                    text: 'Please click OK to refresh the login page automatically.',
                                    icon: 'info',
                                    confirmButtonText: 'OK, Refresh',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                }).then(() => {
                                    window.location.reload();
                                });
                                return;
                            }
                        }
                    } else {
                        throw new Error('Failed to refresh CSRF token');
                    }
                } catch (refreshError) {
                    console.error('CSRF refresh failed:', refreshError);
                    
                    // Instead of showing confusing error, handle gracefully
                    //console.log('Handling session refresh failure gracefully...');

                    // Try to clear session and restart cleanly
                    try {
                        // Clear local storage and session storage
                        localStorage.clear();
                        sessionStorage.clear();
                        
                        // Show user-friendly message and auto-refresh
                        Swal.fire({
                            title: 'Refreshing Login',
                            text: 'Please wait while we refresh your login page...',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = '/login'; // Go to fresh login page
                        });
                        
                    } catch (error) {
                        // Final fallback - just reload
                        //console.log('Final fallback - reloading page');
                        window.location.reload();
                    }
                    
                    return;
                }
            }

            if (response.ok || response.redirected) {
                //console.log('Login successful! Response:', {
                //    status: response.status,
                //    redirected: response.redirected,
                //    url: response.url
                //});
                
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
                    
                    //console.log('Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                });
            } else if (response.status === 403) {
                //console.log('Login failed - Email not verified (403)');
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
                //console.log('CSRF token still invalid after retry');
                Swal.fire({
                    title: 'Session Expired!',
                    text: 'Your session has expired. Please refresh the page and try again.',
                    icon: 'warning',
                    confirmButtonText: 'Refresh Page'
                }).then(() => {
                    window.location.reload();
                });
            } else if (response.status === 422) {
                //console.log('Validation errors (422)');
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
                    
                    // Try to get the raw response text to check for lock messages
                    try {
                        const responseText = await response.text();
                       // console.log('Raw response text:', responseText);
                        
                        // Check if the response contains lock-related keywords
                        if (responseText.toLowerCase().includes('lock') || 
                            responseText.toLowerCase().includes('attempt') ||
                            responseText.toLowerCase().includes('suspended')) {
                            
                            Swal.fire({
                                title: 'Account Issue Detected!',
                                text: 'Your account may be temporarily locked due to multiple failed login attempts. Please wait a few minutes and try again.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            
                            showValidation('username', 'Account may be temporarily locked', 'error');
                            return;
                        }
                    } catch (textError) {
                        console.error('Could not read response text:', textError);
                    }
                    
                    // Fallback to generic error if JSON parsing fails
                    Swal.fire({
                        title: 'Login Failed!',
                        text: 'Invalid credentials. Please check your username/email and password.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                    return;
                }

                // console.log('401 Response data:', data); // Debug log

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
                // Server error - check if it's related to account lockout
                console.error('Server error (500):', response);
                
                let serverErrorHandled = false;
                
                try {
                    const errorData = await response.json();
                   // console.log('500 Error data:', errorData);
                    
                    // Check if the 500 error is actually related to account lockout
                    if (errorData.message && 
                        (errorData.message.toLowerCase().includes('lock') || 
                         errorData.message.toLowerCase().includes('attempt') ||
                         errorData.message.toLowerCase().includes('suspended'))) {
                        
                        Swal.fire({
                            title: 'Account Temporarily Locked!',
                            html: `
                                <div style="text-align: left; font-size: 14px;">
                                    <p><i class="fas fa-lock" style="color: #e74c3c;"></i> Your account appears to be temporarily locked due to multiple failed login attempts.</p>
                                    <p style="margin-top: 15px;">Please wait a few minutes and try again, or contact our support team for immediate assistance.</p>
                                    <p style="margin-top: 10px; color: #666; font-size: 12px;">Error details: ${errorData.message}</p>
                                </div>
                            `,
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        });
                        
                        showValidation('username', 'Account temporarily locked - please wait and try again', 'error');
                        serverErrorHandled = true;
                    }
                } catch (jsonError) {
                    //console.log('Could not parse 500 error as JSON:', jsonError);

                    // Try to read raw response text for lockout keywords
                    try {
                        const responseText = await response.text();
                       // console.log('500 Raw response:', responseText);
                        
                        if (responseText.toLowerCase().includes('lock') || 
                            responseText.toLowerCase().includes('attempt') ||
                            responseText.toLowerCase().includes('isLocked') ||
                            responseText.toLowerCase().includes('locked_until')) {
                            
                            Swal.fire({
                                title: 'Account Lock Issue Detected!',
                                html: `
                                    <div style="text-align: left; font-size: 14px;">
                                        <p><i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i> There appears to be an issue with your account status.</p>
                                        <p><strong>Possible Cause:</strong> Your account may be temporarily locked due to multiple failed login attempts.</p>
                                        <p style="margin-top: 15px;"><strong>Recommended Actions:</strong></p>
                                        <ul style="margin: 10px 0; padding-left: 20px;">
                                            <li>Wait 10-15 minutes before trying again</li>
                                            <li>Ensure you're using the correct credentials</li>
                                            <li>Contact support if the issue persists</li>
                                        </ul>
                                    </div>
                                `,
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false
                            });
                            
                            showValidation('username', 'Possible account lock issue detected', 'error');
                            serverErrorHandled = true;
                        }
                    } catch (textError) {
                        //console.log('Could not read 500 response text:', textError);
                    }
                }
                
                // If we haven't handled this as a lockout issue, show generic server error
                if (!serverErrorHandled) {
                    Swal.fire({
                        title: 'Server Error!',
                        text: 'There was a server error. Please try again or contact support if the issue persists.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
                
            } else {
                // Handle any other response status
                //console.log('Unhandled response status:', response.status);
                //console.log('Response headers:', response.headers);
                
                let errorMessage = 'An unexpected error occurred. Please try again.';
                let errorData = null;
                
                try {
                    errorData = await response.json();
                    //console.log('Error response data:', errorData);
                    
                    // Check for account lock information even in unexpected status codes
                    if (errorData.error_type === 'account_locked' || 
                        errorData.error_type === 'account_locked_now' ||
                        (errorData.message && errorData.message.toLowerCase().includes('lock'))) {
                        
                        const unlockTime = errorData.unlock_time_human || 'later';
                        
                        Swal.fire({
                            title: 'Account Locked!',
                            html: `
                                <div style="text-align: left; font-size: 14px;">
                                    <p><i class="fas fa-lock" style="color: #e74c3c;"></i> Your account is temporarily locked.</p>
                                    <p><strong>Try again:</strong> ${unlockTime}</p>
                                    <p style="margin-top: 15px;">Please wait for the lock to expire or contact support for assistance.</p>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        });
                        
                        showValidation('username', errorData.message || 'Account temporarily locked', 'error');
                        return;
                    }
                    
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    //console.log('Could not parse error response as JSON:', e);
                    // Try to get raw text
                    try {
                        const responseText = await response.text();
                       // console.log('Raw error response:', responseText);

                        if (responseText.toLowerCase().includes('lock')) {
                            errorMessage = 'Your account may be temporarily locked. Please wait and try again.';
                        }
                    } catch (textError) {
                        //console.log('Could not read error response text:', textError);
                    }
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
            
            // Enhanced error handling - check if this is a network/CORS issue vs actual login failure
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                // Network error
                Swal.fire({
                    title: 'Connection Error!',
                    text: 'Unable to connect to the server. Please check your internet connection and try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            } else if (error.name === 'AbortError') {
                // Request was aborted (timeout)
                Swal.fire({
                    title: 'Request Timeout!',
                    text: 'The login request timed out. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            } else {
                // Check if the error message contains account lock information
                const errorMessage = error.message || error.toString();
                
                if (errorMessage.toLowerCase().includes('lock') || errorMessage.toLowerCase().includes('attempt')) {
                    // This might be an account lockout scenario that fell through
                    Swal.fire({
                        title: 'Account Status Check Required!',
                        text: 'There may be an issue with your account status. Please wait a moment and try again, or contact support if the issue persists.',
                        icon: 'warning',
                        confirmButtonText: 'Try Again'
                    });
                } else {
                    // Generic error handling
                    Swal.fire({
                        title: 'Login Failed!',
                        text: 'An unexpected error occurred during login. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    });
                }
            }
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
    // console.log('Checking URL parameters for session expired...');
    // console.log('session_expired param:', urlParams.get('session_expired'));
    // console.log('csrf_error param:', urlParams.get('csrf_error'));
    // console.log('Current URL:', window.location.href);
    // console.log('Document referrer:', document.referrer);

    // Only show session expired if explicitly indicated by URL parameters
    // Do NOT show it just because user came from a protected route
    if (urlParams.get('session_expired') === '1' || urlParams.get('csrf_error') === '1') {
        // console.log('Showing session expired alert due to URL parameters');
        Swal.fire({
            title: 'Session Expired!',
            text: 'Your session has expired. Please try logging in again.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    } else {
        // console.log('No explicit session expired parameters found - not showing alert');
    }

    // console.log('Login page initialized successfully');
    // console.log('Available test function: testForgotPassword()');
    // console.log('Available help function: showSessionStatusHelp()');
    
    // Show success message if everything is working properly
    const sessionIndicator = document.getElementById('sessionIndicator');
    if (sessionIndicator && sessionIndicator.style.background === 'rgb(40, 167, 69)') {
        // console.log('✅ All systems operational - ready for login');
    }

    // Make session status help globally available
    window.showSessionStatusHelp = showSessionStatusHelp;

    // Fallback submission for very old browsers
    function submitWithFallback() {
        // console.log('Using fallback submission for old browser');

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
            // console.log('Session mismatch detected in health check');
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
