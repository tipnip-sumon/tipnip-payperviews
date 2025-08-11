@extends('layouts.form_layout')
@section('top_title','Register') 

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content') 
<style>
    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 40px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .register-title {
        color: white;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .register-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-label {
        display: block;
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: white;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-input:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
    }

    .form-input.valid {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.1);
    }

    .form-input.invalid {
        border-color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
    }

    .validation-message {
        margin-top: 8px;
        font-size: 14px;
        font-weight: 500;
        min-height: 20px;
        transition: all 0.3s ease;
    }

    .validation-message.success {
        color: #28a745;
    }

    .validation-message.error {
        color: #dc3545;
    }

    .validation-message.checking {
        color: #ffc107;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: white;
    }

    .password-field {
        position: relative;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        margin: 25px 0;
    }

    .checkbox-input {
        margin-right: 12px;
        transform: scale(1.2);
    }

    .checkbox-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        line-height: 1.4;
    }

    .checkbox-label a {
        color: white;
        text-decoration: underline;
    }

    .submit-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 12px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 20px 0;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
    }

    .submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        color: rgba(255, 255, 255, 0.8);
    }

    .login-link a {
        color: white;
        text-decoration: underline;
        font-weight: 600;
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

    @media (max-width: 768px) {
        .register-card {
            padding: 30px 20px;
            margin: 10px;
        }
        
        .register-title {
            font-size: 28px;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join our platform and start earning</p>
        </div>

        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf
            
            <!-- Sponsor ID -->
            <div class="form-group">
                <label for="sponsor" class="form-label">Sponsor ID (Optional)</label>
                <input type="text" id="sponsor" name="sponsor" class="form-input" 
                       placeholder="Enter sponsor username or referral code"
                       value="{{ request('ref') }}">
                <div id="sponsorMessage" class="validation-message"></div>
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="username" class="form-label">Username *</label>
                <input type="text" id="username" name="username" class="form-input" 
                       placeholder="Choose a unique username" required>
                <div id="usernameMessage" class="validation-message"></div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input" 
                       placeholder="Enter your email address" required>
                <div id="emailMessage" class="validation-message"></div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password *</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Create a strong password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
                <div id="passwordMessage" class="validation-message"></div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <div class="password-field">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                           placeholder="Confirm your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                    </button>
                </div>
                <div id="confirmPasswordMessage" class="validation-message"></div>
            </div>

            <!-- Terms Agreement -->
            <div class="checkbox-container">
                <input type="checkbox" id="agree" name="agree" class="checkbox-input" required>
                <label for="agree" class="checkbox-label">
                    I agree to the <a href="#" target="_blank">Terms of Service</a> 
                    and <a href="#" target="_blank">Privacy Policy</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
                Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Registration page loaded');
    
    // Debug CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
    if (!csrfToken) {
        console.error('CSRF token not found in meta tag!');
    }

    // Form elements
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Input elements
    const sponsorInput = document.getElementById('sponsor');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    // Message elements
    const sponsorMessage = document.getElementById('sponsorMessage');
    const usernameMessage = document.getElementById('usernameMessage');
    const emailMessage = document.getElementById('emailMessage');
    const passwordMessage = document.getElementById('passwordMessage');
    const confirmPasswordMessage = document.getElementById('confirmPasswordMessage');

    // Validation timeouts
    let sponsorTimeout, usernameTimeout, emailTimeout;

    // Validation states
    const validationState = {
        sponsor: true, // Optional field, so default to true
        username: false,
        email: false,
        password: false,
        confirmPassword: false
    };

    // Utility functions
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

    function setInputState(input, message, state) {
        input.classList.remove('valid', 'invalid');
        message.classList.remove('success', 'error', 'checking');
        
        if (state === 'valid') {
            input.classList.add('valid');
            message.classList.add('success');
        } else if (state === 'invalid') {
            input.classList.add('invalid');
            message.classList.add('error');
        } else if (state === 'checking') {
            message.classList.add('checking');
        }
    }

    function updateSubmitButton() {
        const allValid = Object.values(validationState).every(state => state === true);
        submitBtn.disabled = !allValid;
    }

    // Password toggle function
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId === 'password' ? 'passwordIcon' : 'confirmPasswordIcon');
        
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

    // Sponsor validation
    async function validateSponsor(sponsor) {
        if (!sponsor.trim()) {
            sponsorMessage.textContent = '';
            setInputState(sponsorInput, sponsorMessage, '');
            validationState.sponsor = true;
            updateSubmitButton();
            return;
        }

        setInputState(sponsorInput, sponsorMessage, 'checking');
        sponsorMessage.textContent = 'Checking sponsor...';

        try {
            const response = await fetch('/validate-sponsor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ sponsor: sponsor }),
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.valid) {
                sponsorMessage.textContent = `✓ Valid sponsor: ${data.sponsor_name || data.sponsor_username}`;
                setInputState(sponsorInput, sponsorMessage, 'valid');
                validationState.sponsor = true;
            } else {
                sponsorMessage.textContent = data.message || 'Sponsor not found';
                setInputState(sponsorInput, sponsorMessage, 'invalid');
                validationState.sponsor = false;
            }
        } catch (error) {
            console.error('Sponsor validation error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                csrfToken: getCSRFToken()
            });
            sponsorMessage.textContent = 'Unable to validate sponsor. Please check your internet connection.';
            setInputState(sponsorInput, sponsorMessage, 'invalid');
            validationState.sponsor = false;
        }

        updateSubmitButton();
    }

    // Username validation
    async function validateUsername(username) {
        if (!username.trim()) {
            usernameMessage.textContent = '';
            setInputState(usernameInput, usernameMessage, '');
            validationState.username = false;
            updateSubmitButton();
            return;
        }

        // Basic format validation
        const regex = /^[a-zA-Z0-9_]{3,20}$/;
        if (!regex.test(username)) {
            usernameMessage.textContent = 'Username must be 3-20 characters (letters, numbers, underscores only)';
            setInputState(usernameInput, usernameMessage, 'invalid');
            validationState.username = false;
            updateSubmitButton();
            return;
        }

        setInputState(usernameInput, usernameMessage, 'checking');
        usernameMessage.textContent = 'Checking availability...';

        try {
            const response = await fetch('/validate-username', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ username: username }),
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.available) {
                usernameMessage.textContent = '✓ Username is available';
                setInputState(usernameInput, usernameMessage, 'valid');
                validationState.username = true;
            } else {
                usernameMessage.textContent = data.message || 'Username is not available';
                setInputState(usernameInput, usernameMessage, 'invalid');
                validationState.username = false;
            }
        } catch (error) {
            console.error('Username validation error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                csrfToken: getCSRFToken()
            });
            usernameMessage.textContent = 'Unable to check username availability. Please try again.';
            setInputState(usernameInput, usernameMessage, 'invalid');
            validationState.username = false;
        }

        updateSubmitButton();
    }

    // Email validation
    async function validateEmail(email) {
        if (!email.trim()) {
            emailMessage.textContent = '';
            setInputState(emailInput, emailMessage, '');
            validationState.email = false;
            updateSubmitButton();
            return;
        }

        // Basic format validation
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(email)) {
            emailMessage.textContent = 'Please enter a valid email address';
            setInputState(emailInput, emailMessage, 'invalid');
            validationState.email = false;
            updateSubmitButton();
            return;
        }

        setInputState(emailInput, emailMessage, 'checking');
        emailMessage.textContent = 'Checking availability...';

        try {
            const response = await fetch('/validate-email', {
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

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.available) {
                emailMessage.textContent = '✓ Email is available';
                setInputState(emailInput, emailMessage, 'valid');
                validationState.email = true;
            } else {
                emailMessage.textContent = data.message || 'Email is already registered';
                setInputState(emailInput, emailMessage, 'invalid');
                validationState.email = false;
            }
        } catch (error) {
            console.error('Email validation error:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                csrfToken: getCSRFToken()
            });
            emailMessage.textContent = 'Unable to check email availability. Please try again.';
            setInputState(emailInput, emailMessage, 'invalid');
            validationState.email = false;
        }

        updateSubmitButton();
    }

    // Password validation
    function validatePassword(password) {
        if (!password) {
            passwordMessage.textContent = '';
            setInputState(passwordInput, passwordMessage, '');
            validationState.password = false;
            updateSubmitButton();
            return;
        }

        if (password.length < 8) {
            passwordMessage.textContent = 'Password must be at least 8 characters';
            setInputState(passwordInput, passwordMessage, 'invalid');
            validationState.password = false;
        } else {
            passwordMessage.textContent = '✓ Password looks good';
            setInputState(passwordInput, passwordMessage, 'valid');
            validationState.password = true;
        }

        updateSubmitButton();
        validateConfirmPassword(); // Re-validate confirm password
    }

    // Confirm password validation
    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (!confirmPassword) {
            confirmPasswordMessage.textContent = '';
            setInputState(confirmPasswordInput, confirmPasswordMessage, '');
            validationState.confirmPassword = false;
            updateSubmitButton();
            return;
        }

        if (password === confirmPassword) {
            confirmPasswordMessage.textContent = '✓ Passwords match';
            setInputState(confirmPasswordInput, confirmPasswordMessage, 'valid');
            validationState.confirmPassword = true;
        } else {
            confirmPasswordMessage.textContent = 'Passwords do not match';
            setInputState(confirmPasswordInput, confirmPasswordMessage, 'invalid');
            validationState.confirmPassword = false;
        }

        updateSubmitButton();
    }

    // Event listeners
    sponsorInput.addEventListener('input', function() {
        clearTimeout(sponsorTimeout);
        sponsorTimeout = setTimeout(() => {
            validateSponsor(this.value.trim());
        }, 500);
    });

    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        usernameTimeout = setTimeout(() => {
            validateUsername(this.value.trim());
        }, 500);
    });

    emailInput.addEventListener('input', function() {
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(() => {
            validateEmail(this.value.trim());
        }, 500);
    });

    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    confirmPasswordInput.addEventListener('input', function() {
        validateConfirmPassword();
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Check if all validations pass
        if (!Object.values(validationState).every(state => state === true)) {
            Swal.fire({
                title: 'Validation Error!',
                text: 'Please fix all validation errors before submitting.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Set loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span>Creating Account...';

        try {
            const formData = new FormData(form);
            
            // Ensure CSRF token is included
            if (!formData.has('_token')) {
                formData.append('_token', getCSRFToken());
            }
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your account has been created successfully. Please check your email for verification.',
                    icon: 'success',
                    confirmButtonText: 'Continue to Login'
                }).then(() => {
                    window.location.href = '/login';
                });
            } else if (response.status === 422) {
                const data = await response.json();
                let errorMessage = 'Please fix the following errors:\n\n';
                
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `• ${data.errors[field][0]}\n`;
                    });
                } else {
                    errorMessage = data.message || 'Validation failed';
                }

                Swal.fire({
                    title: 'Validation Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                throw new Error(`HTTP ${response.status}`);
            }
        } catch (error) {
            console.error('Registration error:', error);
            Swal.fire({
                title: 'Registration Failed!',
                text: 'An error occurred while creating your account. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Account';
        }
    });

    // Initialize validation for pre-filled sponsor field
    if (sponsorInput.value) {
        validateSponsor(sponsorInput.value);
    }
});
</script>

@endsection
