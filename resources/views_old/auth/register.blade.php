@extends('layouts.form_layout')
@section('top_title','Register')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content') 
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    .stunning-register-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        background-attachment: fixed;
        position: relative;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    .stunning-register-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
        pointer-events: none;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .stunning-register-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    .stunning-register-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 480px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 0 80px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transform: translateY(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stunning-register-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    .stunning-register-card:hover {
        transform: translateY(-5px);
        box-shadow: 
            0 30px 60px rgba(0, 0, 0, 0.15),
            0 0 100px rgba(255, 255, 255, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .stunning-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .stunning-logo {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        animation: pulse 2s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        100% { transform: scale(1.05); }
    }

    .stunning-logo i {
        font-size: 32px;
        color: white;
    }

    .stunning-title {
        color: white;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: -0.5px;
    }

    .stunning-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        font-weight: 400;
        margin: 0;
    }

    .stunning-form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .stunning-input-container {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stunning-input-container:focus-within {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 
            0 0 0 3px rgba(255, 255, 255, 0.1),
            0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .stunning-input-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 1;
    }

    .stunning-input-container:focus-within::before {
        transform: scaleX(1);
    }

    .stunning-input-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
        font-size: 20px;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .stunning-input-container:focus-within .stunning-input-icon {
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    .stunning-input {
        width: 100%;
        padding: 20px 20px 20px 60px;
        border: none;
        background: transparent;
        color: white;
        font-size: 16px;
        font-weight: 500;
        outline: none;
        transition: all 0.3s ease;
        z-index: 2;
        position: relative;
    }

    .stunning-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .stunning-input:focus::placeholder {
        opacity: 0;
        transform: translateY(-10px);
    }

    .stunning-floating-label {
        position: absolute;
        left: 60px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 16px;
        font-weight: 500;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 3;
    }

    .stunning-input:focus + .stunning-floating-label,
    .stunning-input:not(:placeholder-shown) + .stunning-floating-label {
        top: 8px;
        left: 16px;
        font-size: 12px;
        color: white;
        font-weight: 600;
        background: linear-gradient(90deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stunning-password-toggle {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 3;
        padding: 5px;
        border-radius: 50%;
    }

    .stunning-password-toggle:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .stunning-error-message {
        color: #ff6b6b;
        font-size: 14px;
        margin-top: 8px;
        font-weight: 500;
        opacity: 0;
        animation: slideInError 0.3s ease forwards;
        display: block;
        line-height: 1.4;
        padding: 4px 0;
    }

    .stunning-error-message[style*="display: none"] {
        opacity: 0;
        height: 0;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .stunning-error-message[style*="display: block"] {
        opacity: 1;
        height: auto;
        margin-top: 8px;
        padding: 4px 0;
    }

    /* Server-side error messages */
    .stunning-error-message[class*="field-error"] {
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid rgba(255, 107, 107, 0.3);
        border-radius: 6px;
        padding: 8px 12px;
        margin-top: 8px;
        font-weight: 600;
    }

    /* Real-time validation messages */
    .stunning-error-message[id*="Validation"] {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 6px;
        padding: 6px 10px;
        margin-top: 6px;
        color: #ffc107;
        font-weight: 500;
    }

    /* Password match message */
    #passwordMatch {
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid rgba(255, 107, 107, 0.3);
        border-radius: 6px;
        padding: 6px 10px;
        margin-top: 6px;
        font-weight: 500;
    }

    @keyframes slideInError {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stunning-password-strength {
        margin-top: 8px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stunning-strength-bar {
        display: flex;
        gap: 4px;
        margin-bottom: 8px;
    }

    .stunning-strength-segment {
        height: 4px;
        flex: 1;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .stunning-strength-segment.active {
        background: linear-gradient(90deg, #ff6b6b, #ffd93d, #6bcf7f);
    }

    .stunning-strength-text {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
    }

    .stunning-checkbox-container {
        display: flex;
        align-items: center;
        margin: 24px 0;
        cursor: pointer;
    }

    .stunning-checkbox {
        position: relative;
        width: 20px;
        height: 20px;
        margin-right: 12px;
        cursor: pointer;
    }

    .stunning-checkbox input {
        opacity: 0;
        position: absolute;
        cursor: pointer;
    }

    .stunning-checkbox-custom {
        position: absolute;
        top: 0;
        left: 0;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .stunning-checkbox input:checked + .stunning-checkbox-custom {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-color: white;
    }

    .stunning-checkbox-custom::after {
        content: '';
        position: absolute;
        left: 6px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) scale(0);
        transition: transform 0.2s ease;
    }

    .stunning-checkbox input:checked + .stunning-checkbox-custom::after {
        transform: rotate(45deg) scale(1);
    }

    .stunning-checkbox-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 14px;
        font-weight: 500;
        line-height: 1.4;
    }

    .stunning-checkbox-label a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .stunning-checkbox-label a:hover {
        border-bottom-color: white;
    }

    .stunning-submit-btn {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 16px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        margin: 24px 0;
    }

    .stunning-submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .stunning-submit-btn:hover::before {
        left: 100%;
    }

    .stunning-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    }

    .stunning-submit-btn:active {
        transform: translateY(0);
    }

    .stunning-footer-links {
        text-align: center;
        margin-top: 24px;
    }

    .stunning-footer-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 14px;
        margin-bottom: 12px;
    }

    .stunning-footer-link {
        color: white;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .stunning-footer-link:hover {
        border-bottom-color: white;
        color: white;
    }

    .stunning-forgot-password {
        text-align: center;
        margin-top: 16px;
    }

    .stunning-forgot-link {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .stunning-forgot-link:hover {
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stunning-register-card {
            margin: 20px;
            padding: 30px 24px;
        }
        
        .stunning-title {
            font-size: 28px;
        }
        
        .stunning-input {
            padding: 16px 16px 16px 50px;
            font-size: 15px;
        }
        
        .stunning-floating-label {
            left: 50px;
            font-size: 15px;
        }
        
        .stunning-input:focus + .stunning-floating-label,
        .stunning-input:not(:placeholder-shown) + .stunning-floating-label {
            left: 12px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .stunning-register-container {
            padding: 10px;
        }
        
        .stunning-register-card {
            padding: 24px 20px;
        }
        
        .stunning-title {
            font-size: 24px;
        }
    }

    /* Animation for page load */
    .stunning-register-card {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="stunning-register-page">
    <div class="stunning-register-container">
        <div class="stunning-register-card">
            <div class="stunning-header">
                <div class="stunning-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="stunning-title">{{ __('Create Account') }}</h1>
                <p class="stunning-subtitle">Join us and start your journey</p>
            </div>

            <form action="{{ route('register') }}" method="POST" id="stunningRegisterForm">
                @csrf
                
                <!-- Sponsor ID Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-handshake"></i>
                        <input type="text" 
                               name="sponsor" 
                               id="sponsor" 
                               class="stunning-input" 
                               placeholder=" "
                               value="{{ old('sponsor', request()->get('ref', '')) }}"
                               autocomplete="off">
                        <label for="sponsor" class="stunning-floating-label">{{ __('Sponsor ID') }}</label>
                    </div>
                    <div id="sponsorValidation" class="stunning-error-message" style="display: none;"></div>
                    @error('sponsor')
                        <div class="stunning-error-message field-error-sponsor">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Username Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-user"></i>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="stunning-input" 
                               placeholder=" "
                               value="{{ old('username') }}"
                               autocomplete="username"
                               pattern="[a-zA-Z0-9_]{3,20}"
                               title="Username must be 3-20 characters long and contain only letters, numbers, and underscores">
                        <label for="username" class="stunning-floating-label">{{ __('Username') }}</label>
                    </div>
                    <div id="usernameValidation" class="stunning-error-message" style="display: none;"></div>
                    @error('username')
                        <div class="stunning-error-message field-error-username">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-envelope"></i>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="stunning-input" 
                               placeholder=" "
                               value="{{ old('email') }}"
                               autocomplete="email">
                        <label for="email" class="stunning-floating-label">{{ __('Email Address') }}</label>
                    </div>
                    <div id="emailValidation" class="stunning-error-message" style="display: none;"></div>
                    @error('email')
                        <div class="stunning-error-message field-error-email">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="stunning-input" 
                               placeholder=" "
                               autocomplete="new-password"
                               minlength="8">
                        <label for="password" class="stunning-floating-label">{{ __('Password') }}</label>
                        <button type="button" class="stunning-password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    <div id="passwordStrength" class="stunning-password-strength" style="display: none;">
                        <div class="stunning-strength-bar">
                            <div class="stunning-strength-segment" id="strength1"></div>
                            <div class="stunning-strength-segment" id="strength2"></div>
                            <div class="stunning-strength-segment" id="strength3"></div>
                            <div class="stunning-strength-segment" id="strength4"></div>
                        </div>
                        <div class="stunning-strength-text" id="strengthText">Enter a password</div>
                    </div>
                    @error('password')
                        <div class="stunning-error-message field-error-password">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-lock"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="stunning-input" 
                               placeholder=" "
                               autocomplete="new-password">
                        <label for="password_confirmation" class="stunning-floating-label">{{ __('Confirm Password') }}</label>
                        <button type="button" class="stunning-password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="passwordConfirmToggleIcon"></i>
                        </button>
                    </div>
                    <div id="passwordMatch" class="stunning-error-message" style="display: none;"></div>
                    @error('password_confirmation')
                        <div class="stunning-error-message field-error-password_confirmation">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="stunning-checkbox-container">
                    <label class="stunning-checkbox">
                        <input type="checkbox" name="agree" required>
                        <span class="stunning-checkbox-custom"></span>
                    </label>
                    <label class="stunning-checkbox-label">
                        {{ __('I agree to the') }} 
                        <a href="{{ route('policies', 'terms-of-service') }}" target="_blank">{{ __('Terms of Service') }}</a> 
                        {{ __('and') }} 
                        <a href="{{ route('policies', 'privacy-policy') }}" target="_blank">{{ __('Privacy Policy') }}</a>
                    </label>
                </div>
                @error('agree')
                    <div class="stunning-error-message field-error-agree">{{ $message }}</div>
                @enderror

                <!-- Submit Button -->
                <button type="submit" class="stunning-submit-btn" id="submitBtn">
                    <span>{{ __('Create Account') }}</span>
                </button>
                
                <!-- Alternative Submit (Fallback) -->
                <div style="text-align: center; margin-top: 10px;">
                    <small style="color: rgba(255, 255, 255, 0.7);">
                        Having issues? 
                        <button type="button" onclick="submitFormDirectly()" style="background: none; border: none; color: white; text-decoration: underline; cursor: pointer; font-size: inherit;">
                            Try alternative submission
                        </button>
                    </small>
                </div>

                <!-- Footer Links -->
                <div class="stunning-footer-links">
                    <p class="stunning-footer-text">{{ __('Already have an account?') }}</p>
                    <a href="{{ route('login') }}" class="stunning-footer-link">{{ __('Sign In') }}</a>
                </div>

                <div class="stunning-forgot-password">
                    <a href="{{ route('password.request') }}" class="stunning-forgot-link">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug information
    console.log('Registration page loaded');
    console.log('Initial CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
    
    // CSRF Token Management
    function refreshCSRFToken() {
        return fetch('/csrf-refresh')
            .then(response => response.json())
            .then(data => {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfInput = document.querySelector('input[name="_token"]');
                
                if (csrfMeta && data.csrf_token) {
                    csrfMeta.setAttribute('content', data.csrf_token);
                }
                if (csrfInput && data.csrf_token) {
                    csrfInput.value = data.csrf_token;
                }
                
                console.log('CSRF token refreshed:', data.csrf_token);
                return data.csrf_token;
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                return null;
            });
    }

    // Refresh CSRF token every 30 seconds to prevent expiration
    setInterval(refreshCSRFToken, 30000);

    // Page visibility API to refresh token when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshCSRFToken();
        }
    });

    // Refresh token before form interaction
    const form = document.getElementById('stunningRegisterForm');
    form.addEventListener('focusin', function() {
        // Refresh token when user starts interacting with form
        refreshCSRFToken();
    });

    // Password toggle functionality
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = inputId === 'password' ? 
            document.getElementById('passwordToggleIcon') : 
            document.getElementById('passwordConfirmToggleIcon');
        
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

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    const strengthSegments = [
        document.getElementById('strength1'),
        document.getElementById('strength2'),
        document.getElementById('strength3'),
        document.getElementById('strength4')
    ];

    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = '';

        if (password.length === 0) {
            strengthContainer.style.display = 'none';
            return;
        }

        strengthContainer.style.display = 'block';

        // Length check
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;

        // Character variety checks
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;

        // Reset segments
        strengthSegments.forEach(segment => {
            segment.classList.remove('active');
        });

        // Activate segments based on score
        for (let i = 0; i < Math.min(score, 4); i++) {
            strengthSegments[i].classList.add('active');
        }

        // Strength feedback
        switch (score) {
            case 0:
            case 1:
                feedback = 'Weak password';
                break;
            case 2:
                feedback = 'Fair password';
                break;
            case 3:
                feedback = 'Good password';
                break;
            case 4:
            case 5:
                feedback = 'Strong password';
                break;
        }

        strengthText.textContent = feedback;
    }

    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });

    // Password match checker
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatchDiv = document.getElementById('passwordMatch');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword.length === 0) {
            passwordMatchDiv.style.display = 'none';
            return;
        }

        if (password === confirmPassword) {
            passwordMatchDiv.style.display = 'none';
            confirmPasswordInput.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.5)';
        } else {
            passwordMatchDiv.style.display = 'block';
            passwordMatchDiv.textContent = 'Passwords do not match';
            confirmPasswordInput.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        }
    }

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Username validation
    const usernameInput = document.getElementById('username');
    const usernameValidation = document.getElementById('usernameValidation');

    usernameInput.addEventListener('input', function() {
        const username = this.value;
        const regex = /^[a-zA-Z0-9_]{3,20}$/;

        if (username.length === 0) {
            usernameValidation.style.display = 'none';
            return;
        }

        if (!regex.test(username)) {
            usernameValidation.style.display = 'block';
            usernameValidation.textContent = 'Username must be 3-20 characters, letters, numbers, and underscores only';
            this.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        } else {
            usernameValidation.style.display = 'none';
            this.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.5)';
        }
    });

    // Email validation
    const emailInput = document.getElementById('email');
    const emailValidation = document.getElementById('emailValidation');

    emailInput.addEventListener('input', function() {
        const email = this.value;
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email.length === 0) {
            emailValidation.style.display = 'none';
            return;
        }

        if (!regex.test(email)) {
            emailValidation.style.display = 'block';
            emailValidation.textContent = 'Please enter a valid email address';
            this.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        } else {
            emailValidation.style.display = 'none';
            this.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.5)';
        }
    });

    // Form submission enhancement with comprehensive validation and SweetAlert
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;

    form.addEventListener('submit', function(e) {
        if (isSubmitting) return;
        
        e.preventDefault(); // Prevent default submission
        
        // Basic client-side validation before submission
        const sponsor = document.getElementById('sponsor').value.trim();
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        const agreeCheckbox = document.querySelector('input[name="agree"]');
        
        // Clear any existing errors
        document.querySelectorAll('.stunning-error-message[class*="field-error"]').forEach(el => {
            el.style.display = 'none';
        });
        
        // Clear input styling
        document.querySelectorAll('.stunning-input-container').forEach(container => {
            container.style.borderColor = '';
            container.style.background = '';
        });
        
        let hasValidationErrors = false;
        
        // Sponsor validation (if provided)
        if (sponsor && sponsor.length > 0) {
            const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
            const hashRegex = /^[a-f0-9]{32,64}$/; // Support different hash lengths
            
            if (!usernameRegex.test(sponsor) && !hashRegex.test(sponsor)) {
                showFieldError('sponsor', 'Invalid sponsor ID format. Please check your referral link.');
                hasValidationErrors = true;
            }
        }
        
        // Username validation
        if (!username) {
            showFieldError('username', 'Username is required');
            hasValidationErrors = true;
        } else if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
            showFieldError('username', 'Username must be 3-20 characters, letters, numbers, and underscores only');
            hasValidationErrors = true;
        }
        
        // Email validation
        if (!email) {
            showFieldError('email', 'Email is required');
            hasValidationErrors = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            hasValidationErrors = true;
        }
        
        // Password validation
        if (!password) {
            showFieldError('password', 'Password is required');
            hasValidationErrors = true;
        } else if (password.length < 8) {
            showFieldError('password', 'Password must be at least 8 characters');
            hasValidationErrors = true;
        }
        
        // Password confirmation validation
        if (!passwordConfirm) {
            showFieldError('password_confirmation', 'Password confirmation is required');
            hasValidationErrors = true;
        } else if (password !== passwordConfirm) {
            showFieldError('password_confirmation', 'Password confirmation does not match');
            hasValidationErrors = true;
        }
        
        // Terms acceptance validation
        if (!agreeCheckbox || !agreeCheckbox.checked) {
            showFieldError('agree', 'You must accept the terms and conditions to register');
            hasValidationErrors = true;
        }
        
        // If validation fails, stop here
        if (hasValidationErrors) {
            console.log('Client-side validation failed');
            Swal.fire({
                title: 'Validation Error!',
                text: 'Please correct the highlighted errors and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        console.log('Client-side validation passed, submitting form...');
        isSubmitting = true;
        
        // Get current CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const csrfInput = form.querySelector('input[name="_token"]');
        
        if (csrfToken && csrfInput) {
            csrfInput.value = csrfToken.getAttribute('content');
        }
        
        // Disable submit button and show loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        submitBtn.disabled = true;
        
        // Attempt form submission
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (response.status === 419) {
                // CSRF token mismatch - refresh and retry
                console.log('CSRF token expired, refreshing...');
                const newToken = await refreshCSRFToken();
                if (newToken) {
                    // Update form token and retry
                    csrfInput.value = newToken;
                    formData.set('_token', newToken);
                    
                    return fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': newToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } else {
                    // If CSRF refresh fails, fall back to normal form submission
                    throw new Error('CSRF_REFRESH_FAILED');
                }
            }
            return response;
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (response.ok || response.redirected) {
                // Success - show SweetAlert success message
                Swal.fire({
                    title: 'Registration Successful!',
                    text: 'Your account has been created successfully. Please check your email for verification.',
                    icon: 'success',
                    confirmButtonText: 'Continue to Login',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            window.location.href = '/login';
                        }
                    }
                });
            } else if (response.status === 422) {
                // Validation errors
                console.log('Validation errors detected');
                return response.json().then(data => {
                    console.log('Validation error data:', data);
                    displayValidationErrors(data.errors || data.message);
                    resetSubmitButton();
                });
            } else {
                console.log('Unexpected response status:', response.status);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            
            if (error.message === 'CSRF_REFRESH_FAILED') {
                // Fall back to normal form submission
                console.log('Falling back to normal form submission...');
                isSubmitting = false;
                form.submit();
            } else {
                Swal.fire({
                    title: 'Registration Error!',
                    text: 'An error occurred while creating your account. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                resetSubmitButton();
            }
        });
    });
    
    function resetSubmitButton() {
        isSubmitting = false;
        submitBtn.innerHTML = '<span>{{ __('Create Account') }}</span>';
        submitBtn.disabled = false;
    }
    
    function displayValidationErrors(errors) {
        console.log('Displaying validation errors:', errors);
        
        // Clear previous error messages - only clear the field-error ones, not validation ones
        document.querySelectorAll('.stunning-error-message[class*="field-error"]').forEach(el => {
            el.style.display = 'none';
        });
        
        // Handle different error formats
        if (typeof errors === 'string') {
            // Single error message
            Swal.fire({
                title: 'Registration Error!',
                text: errors,
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (!errors || typeof errors !== 'object') {
            Swal.fire({
                title: 'Validation Error!',
                text: 'Validation failed. Please check your input and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Display field-specific errors
        let hasErrors = false;
        let errorList = [];
        
        Object.keys(errors).forEach(field => {
            const errorMessages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            const firstError = errorMessages[0];
            
            console.log(`Field ${field} error:`, firstError);
            
            // Add to error list for summary
            errorList.push(`${field.charAt(0).toUpperCase() + field.slice(1)}: ${firstError}`);
            
            // Find the input field
            const input = document.getElementById(field);
            if (input) {
                // Find the specific error div for this field using class
                const errorDiv = document.querySelector(`.field-error-${field}`);
                if (errorDiv) {
                    errorDiv.textContent = firstError;
                    errorDiv.style.display = 'block';
                    hasErrors = true;
                    
                    // Add red border to indicate error
                    input.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.8)';
                    input.parentElement.style.background = 'rgba(255, 107, 107, 0.1)';
                    
                    // Scroll to first error
                    if (Object.keys(errors)[0] === field) {
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        input.focus();
                    }
                } else {
                    // If no specific field error div found, try to find generic one in the field group
                    const fieldGroup = input.closest('.stunning-form-group');
                    const genericErrorDiv = fieldGroup ? fieldGroup.querySelector('.stunning-error-message:not([id]):not([class*="field-error"])') : null;
                    if (genericErrorDiv) {
                        genericErrorDiv.textContent = firstError;
                        genericErrorDiv.style.display = 'block';
                        hasErrors = true;
                        
                        // Add red border to indicate error
                        input.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.8)';
                        input.parentElement.style.background = 'rgba(255, 107, 107, 0.1)';
                    }
                }
            } else {
                // If no specific field found, handle special cases
                console.log(`No input found for field: ${field}`);
                if (field === 'agree') {
                    const errorDiv = document.querySelector('.field-error-agree');
                    if (errorDiv) {
                        errorDiv.textContent = firstError;
                        errorDiv.style.display = 'block';
                        hasErrors = true;
                    }
                }
            }
        });
        
        // Show comprehensive error message with SweetAlert
        if (errorList.length > 0) {
            const errorHtml = errorList.map(error => `• ${error}`).join('<br>');
            
            Swal.fire({
                title: 'Validation Errors!',
                html: `Please correct the following errors:<br><br>${errorHtml}`,
                icon: 'error',
                confirmButtonText: 'Fix Errors',
                width: '600px'
            });
        }
        
        // Show general error message if no field-specific errors were displayed
        if (!hasErrors && errorList.length === 0) {
            Swal.fire({
                title: 'Registration Error!',
                text: 'Please correct the errors and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
    
    // Direct form submission fallback
    window.submitFormDirectly = function() {
        const form = document.getElementById('stunningRegisterForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Refresh CSRF token first
        refreshCSRFToken().then(() => {
            // Remove the event listener temporarily
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            
            // Submit directly
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            submitBtn.disabled = true;
            newForm.submit();
        }).catch(() => {
            // If refresh fails, try anyway
            form.removeEventListener('submit', arguments.callee);
            form.submit();
        });
    };

    // Captcha refresh function
    window.refreshCaptcha = function() {
        const captchaImg = document.querySelector('.captcha-img');
        if (captchaImg) {
            captchaImg.src = captchaImg.src + '?' + Math.random();
        }
    };

    // Input focus animations and error clearing
    const inputs = document.querySelectorAll('.stunning-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            
            // Clear errors when user starts typing
            clearFieldError(this);
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        input.addEventListener('input', function() {
            // Clear errors when user types
            clearFieldError(this);
        });

        // Initial check for pre-filled values
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
    
    // Function to clear field-specific errors
    function clearFieldError(input) {
        // Reset border and background
        input.parentElement.style.borderColor = '';
        input.parentElement.style.background = '';
        
        // Hide validation error message (real-time validation)
        const validationErrorDiv = input.parentElement.parentElement.querySelector('.stunning-error-message[id*="Validation"]');
        if (validationErrorDiv) {
            validationErrorDiv.style.display = 'none';
        }
        
        // Hide server-side error message
        const fieldName = input.getAttribute('name');
        const serverErrorDiv = document.querySelector(`.field-error-${fieldName}`);
        if (serverErrorDiv) {
            serverErrorDiv.style.display = 'none';
        }
        
        // Hide generic error message
        const genericErrorDiv = input.parentElement.parentElement.querySelector('.stunning-error-message:not([id]):not([class*="field-error"])');
        if (genericErrorDiv) {
            genericErrorDiv.style.display = 'none';
        }
    }
    
    // Function to show field-specific errors with better styling
    function showFieldError(fieldName, message) {
        const input = document.getElementById(fieldName);
        if (input) {
            // First try to find the specific field error div
            let errorDiv = document.querySelector(`.field-error-${fieldName}`);
            
            // If not found, try to find generic error div in the field group
            if (!errorDiv) {
                const fieldGroup = input.closest('.stunning-form-group');
                errorDiv = fieldGroup ? fieldGroup.querySelector('.stunning-error-message:not([id]):not([class*="field-error"])') : null;
            }
            
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                
                // Add red border to indicate error
                input.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.8)';
                input.parentElement.style.background = 'rgba(255, 107, 107, 0.1)';
                
                // Scroll to field
                input.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    // Enhanced sponsor validation with real-time checking
    function validateSponsorAsync(sponsor) {
        if (sponsor.length === 0) {
            sponsorValidation.style.display = 'none';
            sponsorInput.parentElement.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            return;
        }

        // Basic format validation first
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const hashRegex = /^[a-f0-9]{32,64}$/; // 32-64 character hex hash to support different hash lengths

        if (!usernameRegex.test(sponsor) && !hashRegex.test(sponsor)) {
            sponsorValidation.style.display = 'block';
            sponsorValidation.textContent = 'Invalid sponsor ID format. Must be 3-20 characters (letters, numbers, underscores) or a valid referral hash.';
            sponsorInput.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
            return;
        }

        // Show loading state
        sponsorValidation.style.display = 'block';
        sponsorValidation.textContent = 'Verifying sponsor...';
        sponsorValidation.style.color = '#ffc107';
        sponsorInput.parentElement.style.borderColor = 'rgba(255, 193, 7, 0.5)';

        // Real-time validation with server
        fetch('/validate-sponsor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ sponsor: sponsor })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                sponsorValidation.style.display = 'block';
                sponsorValidation.textContent = `✓ Valid sponsor: ${data.sponsor_name || data.sponsor_username}`;
                sponsorValidation.style.color = '#28a745';
                sponsorInput.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.5)';
            } else {
                sponsorValidation.style.display = 'block';
                sponsorValidation.textContent = data.message || 'Sponsor ID does not exist in our system.';
                sponsorValidation.style.color = '#dc3545';
                sponsorInput.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
            }
        })
        .catch(error => {
            console.error('Sponsor validation error:', error);
            sponsorValidation.style.display = 'block';
            sponsorValidation.textContent = 'Unable to verify sponsor. Please check your connection.';
            sponsorValidation.style.color = '#dc3545';
            sponsorInput.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        });
    }

    // Handle URL parameters for referral and sponsor validation
    const urlParams = new URLSearchParams(window.location.search);
    const refParam = urlParams.get('ref');
    const sponsorInput = document.getElementById('sponsor');
    const sponsorValidation = document.getElementById('sponsorValidation');

    // Add sponsor input event listener with debounced validation
    let sponsorValidationTimeout;
    sponsorInput.addEventListener('input', function() {
        const sponsor = this.value.trim();
        
        // Clear previous timeout
        if (sponsorValidationTimeout) {
            clearTimeout(sponsorValidationTimeout);
        }
        
        // Reset color first
        sponsorValidation.style.color = '#ff6b6b';
        
        // Debounce validation
        sponsorValidationTimeout = setTimeout(() => {
            validateSponsorAsync(sponsor);
        }, 500); // Wait 500ms after user stops typing
    });
    
    // Handle URL referral parameter
    if (refParam && sponsorInput && !sponsorInput.value) {
        sponsorInput.value = refParam;
        sponsorInput.parentElement.classList.add('focused');
        
        // Validate the pre-filled value
        validateSponsorAsync(refParam);
        
        // Optional: Show a welcome message for referred users
        if (refParam) {
            const welcomeMessage = document.createElement('div');
            welcomeMessage.className = 'alert alert-info mt-3';
            welcomeMessage.style.cssText = `
                background: rgba(13, 202, 240, 0.1);
                border: 1px solid rgba(13, 202, 240, 0.3);
                border-radius: 8px;
                padding: 12px;
                color: rgba(255, 255, 255, 0.9);
                font-size: 14px;
                text-align: center;
                margin-top: 10px;
            `;
            
            // Check if it's a hash or username for better messaging
            const isHash = /^[a-f0-9]{32,64}$/.test(refParam);
            const referrerText = isHash ? 'a referral link' : `<strong>${refParam}</strong>`;
            
            welcomeMessage.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>
                You were referred by ${referrerText}. Welcome to our platform!
            `;
            
            sponsorInput.parentElement.parentElement.appendChild(welcomeMessage);
        }
    }
});

// Handle server-side success/error messages
@if(session('success'))
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'Continue'
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif

@if($errors->any())
    let errorList = @json($errors->all());
    let errorHtml = errorList.map(error => `• ${error}`).join('<br>');
    
    Swal.fire({
        title: 'Registration Errors!',
        html: `Please correct the following:<br><br>${errorHtml}`,
        icon: 'error',
        confirmButtonText: 'Fix Errors',
        width: '600px'
    });
@endif
</script>

@endsection



