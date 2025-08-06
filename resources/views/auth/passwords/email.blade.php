@extends('layouts.form_layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    .stunning-forgot-page {
        min-height: 100vh;
        background: linear-gradient(135deg, 
            #667eea 0%, 
            #764ba2 25%, 
            #f093fb 50%, 
            #f5576c 75%, 
            #4facfe 100%);
        background-size: 400% 400%;
        animation: gradientShift 12s ease infinite;
        position: relative;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    .stunning-forgot-page::before {
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

    .stunning-forgot-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    .stunning-forgot-card {
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

    .stunning-forgot-card::before {
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

    .stunning-forgot-card:hover {
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
        margin: 0 0 20px 0;
        line-height: 1.5;
    }

    .stunning-success-message {
        background: rgba(108, 207, 127, 0.2);
        border: 1px solid rgba(108, 207, 127, 0.3);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stunning-success-message i {
        color: #6bcf7f;
        font-size: 20px;
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

    .stunning-error-message {
        color: #ff6b6b;
        font-size: 14px;
        margin-top: 8px;
        font-weight: 500;
        opacity: 0;
        animation: slideInError 0.3s ease forwards;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .stunning-forgot-card {
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
        .stunning-forgot-container {
            padding: 10px;
        }
        
        .stunning-forgot-card {
            padding: 24px 20px;
        }
        
        .stunning-title {
            font-size: 24px;
        }
    }
</style>

<div class="stunning-forgot-page">
    <div class="stunning-forgot-container">
        <div class="stunning-forgot-card">
            <div class="stunning-header">
                <div class="stunning-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="stunning-title">{{ __('Forgot Password?') }}</h1>
                <p class="stunning-subtitle">{{ __('No worries! Enter your email address and we\'ll send you a password reset link.') }}</p>
            </div>

            @if (session('status'))
                <div class="stunning-success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" id="stunningForgotForm">
                @csrf
                
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
                               autocomplete="email"
                               required>
                        <label for="email" class="stunning-floating-label">{{ __('Email Address') }}</label>
                    </div>
                    @error('email')
                        <div class="stunning-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="stunning-submit-btn" id="submitBtn">
                    <span>{{ __('Send Reset Link') }}</span>
                </button>

                <!-- Footer Links -->
                <div class="stunning-footer-links">
                    <p class="stunning-footer-text">{{ __('Remember your password?') }}</p>
                    <a href="{{ route('login') }}" class="stunning-footer-link">{{ __('Back to Login') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission enhancement
    const form = document.getElementById('stunningForgotForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
    });

    // Input focus animations
    const inputs = document.querySelectorAll('.stunning-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });

        // Initial check for pre-filled values
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });

    // Email validation
    const emailInput = document.getElementById('email');
    
    emailInput.addEventListener('input', function() {
        const email = this.value;
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email.length === 0) {
            this.parentElement.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            return;
        }

        if (!regex.test(email)) {
            this.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.5)';
        } else {
            this.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.5)';
        }
    });
});
</script>
@endsection

