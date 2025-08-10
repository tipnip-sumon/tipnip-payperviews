<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PayPerViews</title>
    
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
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 18px;
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email: email })
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email: email })
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

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Signing In...';

        // Clear previous validations
        hideValidation('username');
        hideValidation('password');

        try {
            // Save email for remember me if checked
            if (rememberCheckbox.checked) {
                setCookie('saved_email', usernameInput.value.trim(), 30);
            }

            // First try to submit normally via AJAX
            const formData = new FormData(form);
            
            // Ensure CSRF token is fresh
            const csrfToken = document.querySelector('input[name="_token"]')?.value || 
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (csrfToken) {
                formData.set('_token', csrfToken);
            }
            
            let response;
            try {
                response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (fetchError) {
                console.log('Network error, trying simple login method...');
                // If network error, try simple login
                response = await fetch('/simple-login', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }

            if (response.ok || response.redirected) {
                Swal.fire({
                    title: 'Login Successful!',
                    text: 'Welcome back! Redirecting to your dashboard...',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        window.location.href = '/user/dashboard';
                    }
                });
            } else if (response.status === 403) {
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
            } else if (response.status === 422) {
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
                
            } else if (response.status === 419) {
                // CSRF token expired or missing - try simple login method
                console.log('CSRF error (419), trying simple login method...');
                try {
                    const simpleFormData = new FormData(form);
                    // Remove CSRF token as simple login doesn't need it
                    simpleFormData.delete('_token');
                    
                    const simpleResponse = await fetch('/simple-login', {
                        method: 'POST',
                        body: simpleFormData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (simpleResponse.ok) {
                        const simpleData = await simpleResponse.json();
                        
                        if (simpleData.success) {
                            Swal.fire({
                                title: 'Login Successful!',
                                text: 'Welcome back! Redirecting to your dashboard...',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = simpleData.redirect || '/user/dashboard';
                            });
                        } else {
                            // Handle simple login errors
                            if (simpleData.error_type === 'email_not_verified') {
                                showEmailVerificationError(simpleData.email);
                            } else {
                                showValidation('password', simpleData.message || 'Login failed', 'error');
                                Swal.fire({
                                    title: 'Login Failed!',
                                    text: simpleData.message || 'Login failed. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'Try Again'
                                });
                            }
                        }
                    } else {
                        throw new Error('Simple login failed');
                    }
                } catch (simpleError) {
                    console.error('Simple login error:', simpleError);
                    Swal.fire({
                        title: 'Login Error!',
                        text: 'There was a problem logging you in. Please refresh the page and try again.',
                        icon: 'error',
                        confirmButtonText: 'Refresh Page',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
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
            
            // Don't show generic error if we've already handled a specific case
            if (!error.message.includes('already handled')) {
                Swal.fire({
                    title: 'Login Failed!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Sign In';
        }
    });

    // Check for logout success message
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('logout') === '1') {
        Swal.fire({
            title: 'Logged Out!',
            text: 'You have been successfully logged out.',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    }

    console.log('Login page initialized successfully');
    console.log('Available test function: testForgotPassword()');
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
