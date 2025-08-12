<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - PayPerViews</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="alternate icon" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\GeneralSetting::getLogo() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
    
<style>
    .register-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .register-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        width: 100%;
        max-width: 500px;
        position: relative;
        z-index: 1;
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .progress-container {
        margin-bottom: 30px;
        position: relative;
        z-index: 10;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        position: relative;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        margin-bottom: 8px;
    }

    .step.active .step-number {
        background: #667eea;
        color: white;
    }

    .step.completed .step-number {
        background: #27ae60;
        color: white;
    }

    .step-title {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }

    .step.active .step-title {
        color: #667eea;
        font-weight: 600;
    }

    .step.completed .step-title {
        color: #27ae60;
    }

    .step-connector {
        position: absolute;
        top: 20px;
        left: 50%;
        width: calc(100% - 40px);
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
        transform: translateX(20px);
    }

    .step.completed .step-connector {
        background: #27ae60;
    }

    .step:last-child .step-connector {
        display: none;
    }

    .form-step {
        display: none !important;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .form-step.active {
        display: block !important;
        opacity: 1;
        visibility: visible;
        animation: slideIn 0.3s ease-in-out;
    }

    /* Force hide step 2 initially */
    #step2 {
        display: none !important;
    }

    #step2.active {
        display: block !important;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .step-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        gap: 15px;
    }

    .btn-previous {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
    }

    .btn-previous:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }

    .btn-next {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 2;
    }

    .btn-next:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }

    .btn-submit {
        background: #27ae60;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 2;
    }

    .btn-submit:hover {
        background: #219a52;
        transform: translateY(-1px);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
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

    .register-title {
        color: #333;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .register-subtitle {
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

    .validation-message.loading {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
        border: 1px solid rgba(52, 152, 219, 0.2);
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
        align-items: flex-start;
        margin: 25px 0;
        gap: 12px;
    }

    .checkbox-input {
        width: 20px;
        height: 20px;
        margin: 0;
        cursor: pointer;
    }

    .checkbox-label {
        color: #333;
        font-size: 14px;
        line-height: 1.5;
        cursor: pointer;
        user-select: none;
    }

    .checkbox-label a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .checkbox-label a:hover {
        text-decoration: underline;
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

    .login-link {
        text-align: center;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #e1e5e9;
    }

    .login-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
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
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        text-align: center;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        display: none; /* Hidden by default */
    }

    @media (max-width: 768px) {
        .register-card {
            padding: 30px 20px;
            margin: 10px;
        }
        
        .register-title {
            font-size: 24px;
        }
    }
</style>
</head>

<body>
<div class="register-container">
    <div class="register-card">
        <!-- Back to Home Button -->
        <a href="{{ url('/') }}" class="back-to-home" title="Back to Home">
            <i class="fas fa-arrow-left"></i>
        </a>
        
                <div class="register-header">
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join PayPerViews and start earning</p>
            
            <!-- Progress Steps -->
            <div class="progress-container">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">Basic Info</div>
                        <div class="step-connector"></div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">Security</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Verification Alert -->
        <div class="email-verification-alert" id="emailVerificationAlert">
            <div class="alert-title">Email Verification Required</div>
            <div class="alert-text">Please verify your email address to complete registration.</div>
            <button type="button" class="resend-btn" id="resendVerificationBtn">
                Resend Verification Email
            </button>
        </div>

        <form id="registerForm" action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Step 1: Basic Information -->
            <div class="form-step active" id="step1">
                <!-- Sponsor ID -->
                <div class="form-group">
                    <label for="sponsor" class="form-label">Sponsor ID (Optional)</label>
                    <input type="text" id="sponsor" name="sponsor" class="form-input" 
                           placeholder="Enter sponsor username or referral code"
                           value="{{ request('ref') ?? old('sponsor') }}">
                    <div class="validation-message" id="sponsorValidation"></div>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" id="username" name="username" class="form-input" 
                           placeholder="Choose a unique username (3-20 characters)" 
                           value="{{ old('username') }}" required>
                    <div class="validation-message" id="usernameValidation"></div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="Enter your email address" 
                           value="{{ old('email') }}" required>
                    <div class="validation-message" id="emailValidation"></div>
                </div>

                <div class="step-navigation">
                    <div></div> <!-- Empty div for spacing -->
                    <button type="button" class="btn-next" id="nextStep1">
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Security & Agreement -->
            <div class="form-step" id="step2">
                <!-- Password -->
                <div class="form-group" style="position: relative;">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Enter a strong password (min. 8 characters)" required>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </span>
                    <div class="validation-message" id="passwordValidation"></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group" style="position: relative;">
                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                           placeholder="Confirm your password" required>
                    <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="passwordConfirmIcon"></i>
                    </span>
                    <div class="validation-message" id="passwordConfirmValidation"></div>
                </div>

                <!-- Terms & Conditions -->
                <div class="checkbox-container">
                    <input type="checkbox" id="agree" name="agree" class="checkbox-input" required>
                    <label for="agree" class="checkbox-label">
                        I agree to the <a href="#" class="terms-link">Terms & Conditions</a> and <a href="#" class="privacy-link">Privacy Policy</a>
                    </label>
                </div>

                <div class="step-navigation">
                    <button type="button" class="btn-previous" id="prevStep2">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-submit" id="submitForm">
                        Create Account <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign In</a> |
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fresh registration page loaded');

    // Form elements
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const agreeCheckbox = document.getElementById('agree');
    const emailVerificationAlert = document.getElementById('emailVerificationAlert');
    const resendBtn = document.getElementById('resendVerificationBtn');

    // Real-time validation
    const sponsorInput = document.getElementById('sponsor');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    // Validation timeouts
    let sponsorTimeout, usernameTimeout, emailTimeout;

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
        const input = document.getElementById(element);
        
        if (validation) {
            validation.textContent = message;
            validation.className = `validation-message ${type}`;
            validation.style.display = 'block';
        }
        
        if (input) {
            input.classList.remove('error', 'success');
            if (type === 'error') {
                input.classList.add('error');
            } else if (type === 'success') {
                input.classList.add('success');
            }
        }
    }

    function hideValidation(element) {
        const validation = document.getElementById(element + 'Validation');
        const input = document.getElementById(element);
        
        if (validation) {
            validation.style.display = 'none';
        }
        
        if (input) {
            input.classList.remove('error', 'success');
        }
    }

    function makeRequest(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
    }

    // Sponsor validation
    async function validateSponsor(sponsor) {
        if (!sponsor.trim()) {
            hideValidation('sponsor');
            return;
        }

        // Detect if it's a referral hash (32 character hex string)
        const isReferralHash = /^[a-f0-9]{32}$/i.test(sponsor.trim());
        const isUsername = /^[a-zA-Z0-9_]{3,20}$/.test(sponsor.trim());
        
        if (!isReferralHash && !isUsername) {
            showValidation('sponsor', 'Enter a valid username (3-20 characters) or referral hash (32 characters)', 'error');
            return;
        }

        showValidation('sponsor', isReferralHash ? 'Validating referral hash...' : 'Checking sponsor username...', 'loading');

        try {
            const response = await makeRequest('/validate-sponsor', { sponsor: sponsor });
            const data = await response.json();

            if (data.valid) {
                const sponsorType = isReferralHash ? 'referral link' : 'username';
                showValidation('sponsor', `✓ Valid sponsor found via ${sponsorType}: ${data.sponsor_name || data.sponsor_username}`, 'success');
            } else {
                showValidation('sponsor', data.message || 'Invalid sponsor ID', 'error');
            }
        } catch (error) {
            console.error('Sponsor validation error:', error);
            showValidation('sponsor', 'Unable to validate sponsor. Please try again.', 'error');
        }
    }

    // Username validation
    async function validateUsername(username) {
        if (!username.trim()) {
            hideValidation('username');
            return;
        }

        if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
            showValidation('username', 'Username must be 3-20 characters (letters, numbers, underscores)', 'error');
            return;
        }

        showValidation('username', 'Checking availability...', 'loading');

        try {
            const response = await makeRequest('/validate-username', { username: username });
            const data = await response.json();

            if (data.available) {
                showValidation('username', '✓ Username is available', 'success');
            } else {
                showValidation('username', data.message || 'Username is not available', 'error');
            }
        } catch (error) {
            console.error('Username validation error:', error);
            showValidation('username', 'Unable to check username. Please try again.', 'error');
        }
    }

    // Email validation
    async function validateEmail(email) {
        if (!email.trim()) {
            hideValidation('email');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showValidation('email', 'Please enter a valid email address', 'error');
            return;
        }

        showValidation('email', 'Checking availability...', 'loading');

        try {
            const response = await makeRequest('/validate-email', { email: email });
            const data = await response.json();

            if (data.available) {
                showValidation('email', '✓ Email is available', 'success');
            } else {
                showValidation('email', data.message || 'Email is already registered', 'error');
                
                // Check if user exists but email not verified
                if (data.needs_verification) {
                    showEmailVerificationAlert(email);
                }
            }
        } catch (error) {
            console.error('Email validation error:', error);
            showValidation('email', 'Unable to check email. Please try again.', 'error');
        }
    }

    // Password validation
    function validatePassword() {
        const password = passwordInput.value;
        
        if (!password) {
            hideValidation('password');
            return;
        }

        if (password.length < 8) {
            showValidation('password', 'Password must be at least 8 characters', 'error');
            return;
        }

        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        if (strength < 2) {
            showValidation('password', 'Password is weak. Add numbers and special characters.', 'error');
        } else if (strength < 3) {
            showValidation('password', 'Password strength: Fair', 'loading');
        } else {
            showValidation('password', '✓ Strong password', 'success');
        }

        validatePasswordConfirm();
    }

    // Password confirmation validation
    function validatePasswordConfirm() {
        const password = passwordInput.value;
        const confirm = passwordConfirmInput.value;

        if (!confirm) {
            hideValidation('password_confirmation');
            return;
        }

        if (password !== confirm) {
            showValidation('password_confirmation', 'Passwords do not match', 'error');
        } else {
            showValidation('password_confirmation', '✓ Passwords match', 'success');
        }
    }

    // Email verification alert
    function showEmailVerificationAlert(email) {
        emailVerificationAlert.classList.add('show');
        resendBtn.disabled = false;
        
        resendBtn.onclick = function() {
            resendVerificationEmail(email);
        };
    }

    // Resend verification email
    async function resendVerificationEmail(email) {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<span class="loading-spinner"></span>Sending...';

        try {
            const response = await makeRequest('/email/resend-public', { email: email });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Email Sent!',
                    text: 'Verification email has been sent. Please check your inbox.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                emailVerificationAlert.classList.remove('show');
            } else {
                throw new Error(data.message || 'Failed to send verification email');
            }
        } catch (error) {
            console.error('Resend verification error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to send verification email. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } finally {
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend Verification Email';
        }
    }

    // Event listeners for real-time validation
    sponsorInput.addEventListener('input', function() {
        clearTimeout(sponsorTimeout);
        let sponsorValue = this.value.trim();
        
        // Auto-extract referral hash from full URL if user pastes entire link
        if (sponsorValue.includes('register?ref=')) {
            const urlMatch = sponsorValue.match(/[?&]ref=([a-f0-9]{32})/i);
            if (urlMatch) {
                sponsorValue = urlMatch[1];
                this.value = sponsorValue;
                showValidation('sponsor', '✓ Referral hash extracted from URL', 'success');
            }
        }
        
        sponsorTimeout = setTimeout(() => validateSponsor(sponsorValue), 500);
    });

    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        usernameTimeout = setTimeout(() => validateUsername(this.value.trim()), 500);
    });

    emailInput.addEventListener('input', function() {
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(() => validateEmail(this.value.trim()), 500);
    });

    passwordInput.addEventListener('input', validatePassword);
    passwordConfirmInput.addEventListener('input', validatePasswordConfirm);

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Basic validation
        if (!agreeCheckbox.checked) {
            Swal.fire({
                title: 'Terms Required!',
                text: 'Please accept the terms and conditions to continue.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Creating Account...';

        try {
            // Submit form
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok || response.redirected) {
                Swal.fire({
                    title: 'Registration Successful!',
                    text: 'Your account has been created. Please check your email for verification.',
                    icon: 'success',
                    confirmButtonText: 'Continue to Login',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = response.redirected ? response.url : '/login';
                });
            } else if (response.status === 422) {
                const data = await response.json();
                displayValidationErrors(data.errors);
            } else {
                throw new Error('Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            Swal.fire({
                title: 'Registration Failed!',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Account';
        }
    });

    // Display validation errors
    function displayValidationErrors(errors) {
        // Map Laravel field names to our element IDs
        const fieldMapping = {
            'password_confirmation': 'password_confirmation',
            'agree': 'agree',
            'terms': 'agree', // Handle both field names
            'sponsor_id': 'sponsor', // Laravel might use sponsor_id while we use sponsor
            'name': 'username', // Laravel might use name while we use username
            'username': 'username',
            'email': 'email',
            'password': 'password'
        };

        Object.keys(errors).forEach(field => {
            const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            const elementId = fieldMapping[field] || field;
            
            // Only show validation if the element exists
            if (document.getElementById(elementId + 'Validation')) {
                showValidation(elementId, message, 'error');
            } else {
                console.warn(`Validation element not found for field: ${field} (mapped to: ${elementId})`);
            }
        });

        const errorList = Object.keys(errors).map(field => 
            `${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}: ${Array.isArray(errors[field]) ? errors[field][0] : errors[field]}`
        ).join('<br>');

        Swal.fire({
            title: 'Validation Errors!',
            html: `Please correct the following:<br><br>${errorList}`,
            icon: 'error',
            confirmButtonText: 'Fix Errors'
        });
    }

    // Check for referral parameter and auto-validate
    const urlParams = new URLSearchParams(window.location.search);
    const refParam = urlParams.get('ref');
    if (refParam && sponsorInput && !sponsorInput.value) {
        sponsorInput.value = refParam;
        
        // Auto-validate if it looks like a valid referral hash or username
        if (/^[a-f0-9]{32}$/i.test(refParam) || /^[a-zA-Z0-9_]{3,20}$/.test(refParam)) {
            setTimeout(() => validateSponsor(refParam), 100);
        }
    }

    // Test forgot password system
    window.testForgotPassword = function(email) {
        if (!email) {
            email = prompt('Enter email address to test password reset:');
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
                if (data.status) {
                    Swal.fire({
                        title: 'Test Successful!',
                        text: 'Password reset email would be sent to: ' + email,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Test Result',
                        text: data.message || 'Password reset system tested',
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

    console.log('Registration page initialized successfully');
    console.log('Available test function: testForgotPassword()');

    // ========================================
    // Two-Step Form Navigation
    // ========================================
    
    let currentStep = 1;
    const totalSteps = 2;
    
    // Step navigation buttons
    const nextStep1Btn = document.getElementById('nextStep1');
    const prevStep2Btn = document.getElementById('prevStep2');
    const submitFormBtn = document.getElementById('submitForm');
    
    // Step elements
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const stepIndicators = document.querySelectorAll('.step');
    
    // Debug: Check if elements exist
    console.log('Step elements found:', {
        step1: !!step1,
        step2: !!step2,
        nextStep1Btn: !!nextStep1Btn,
        prevStep2Btn: !!prevStep2Btn,
        submitFormBtn: !!submitFormBtn,
        stepIndicators: stepIndicators.length
    });
    
    // Initialize the form properly
    function initializeSteps() {
        console.log('Initializing steps...');
        
        // Force hide all steps first
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.remove('active');
            step.style.display = 'none';
        });
        
        // Force show only step 1
        if (step1) {
            step1.style.display = 'block';
            step1.classList.add('active');
            console.log('Step 1 set to active and visible');
        }
        
        // Make sure step 2 is hidden
        if (step2) {
            step2.style.display = 'none';
            step2.classList.remove('active');
            console.log('Step 2 set to hidden');
        }
        
        // Set initial step indicators
        updateStepIndicators();
        console.log('Step indicators updated');
    }
    
    // Call initialization
    initializeSteps();
    
    // Step validation
    function validateStep1() {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        
        if (!username || username.length < 3) {
            showValidation('username', 'Username must be at least 3 characters', 'error');
            return false;
        }
        
        if (!email || !isValidEmail(email)) {
            showValidation('email', 'Please enter a valid email address', 'error');
            return false;
        }
        
        return true;
    }
    
    function validateStep2() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const agree = document.getElementById('agree').checked;
        
        if (!password || password.length < 8) {
            showValidation('password', 'Password must be at least 8 characters', 'error');
            return false;
        }
        
        if (password !== confirmPassword) {
            showValidation('passwordConfirm', 'Passwords do not match', 'error');
            return false;
        }
        
        if (!agree) {
            Swal.fire({
                title: 'Terms Required',
                text: 'You must agree to the Terms & Conditions to continue.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        return true;
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function updateStepIndicators() {
        stepIndicators.forEach((indicator, index) => {
            const stepNum = index + 1;
            
            if (stepNum < currentStep) {
                indicator.classList.remove('active');
                indicator.classList.add('completed');
            } else if (stepNum === currentStep) {
                indicator.classList.remove('completed');
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        });
    }
    
    function showStep(stepNumber) {
        console.log('Switching to step:', stepNumber);
        
        // Hide all steps with force
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.remove('active');
            step.style.display = 'none';
        });
        
        // Show current step with force
        const currentStepElement = document.getElementById('step' + stepNumber);
        if (currentStepElement) {
            currentStepElement.style.display = 'block';
            currentStepElement.classList.add('active');
            console.log('Step', stepNumber, 'is now active');
        }
        
        // Update step indicators
        currentStep = stepNumber;
        updateStepIndicators();
    }
    
    // Next Step 1 button
    if (nextStep1Btn) {
        nextStep1Btn.addEventListener('click', function() {
            if (validateStep1()) {
                showStep(2);
                
                // Add a smooth scroll to top for better UX
                document.querySelector('.register-card').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    }
    
    // Previous Step 2 button
    if (prevStep2Btn) {
        prevStep2Btn.addEventListener('click', function() {
            showStep(1);
        });
    }
    
    // Enhanced form submission
    if (submitFormBtn) {
        submitFormBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (validateStep2()) {
                // Show loading state
                submitFormBtn.disabled = true;
                submitFormBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                
                // Submit the form
                form.submit();
            }
        });
    }
    
    // Allow Enter key to navigate steps
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            
            if (currentStep === 1 && nextStep1Btn) {
                nextStep1Btn.click();
            } else if (currentStep === 2 && submitFormBtn) {
                submitFormBtn.click();
            }
        }
    });
    
    console.log('Two-step registration initialized successfully');
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
