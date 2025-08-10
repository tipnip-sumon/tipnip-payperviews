<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - PayPerViews</title>
    
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
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 18px;
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
    }

    .alert-text {
        color: #e67e22;
        font-size: 14px;
        margin-bottom: 12px;
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
        <div class="register-header">
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join PayPerViews and start earning</p>
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

            <!-- Sponsor ID -->
            <div class="form-group">
                <label for="sponsor" class="form-label">Sponsor ID (Optional)</label>
                <input type="text" id="sponsor" name="sponsor" class="form-input" 
                       placeholder="Enter sponsor username"
                       value="{{ request('ref') ?? old('sponsor') }}">
                <div class="validation-message" id="sponsorValidation"></div>
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="username" class="form-label">Username *</label>
                <input type="text" id="username" name="username" class="form-input" 
                       placeholder="Choose a unique username" 
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

            <!-- Password -->
            <div class="form-group" style="position: relative;">
                <label for="password" class="form-label">Password *</label>
                <input type="password" id="password" name="password" class="form-input" 
                       placeholder="Create a strong password" required>
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

            <!-- Terms and Conditions -->
            <div class="checkbox-container">
                <input type="checkbox" id="agree" name="agree" class="checkbox-input" required>
                <label for="agree" class="checkbox-label">
                    I agree to the <a href="/policies/terms-of-service" target="_blank">Terms of Service</a> 
                    and <a href="/policies/privacy-policy" target="_blank">Privacy Policy</a> *
                </label>
                <div class="validation-message" id="agreeValidation"></div>
            </div>

            <!-- Display Server Errors -->
            @if ($errors->any())
                <div class="validation-message error" style="display: block; margin-bottom: 15px;">
                    @foreach ($errors->all() as $error)
                        • {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <button type="submit" class="submit-btn" id="submitBtn">
                Create Account
            </button>
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
