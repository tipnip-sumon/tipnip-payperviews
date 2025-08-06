@extends('layouts.form_layout')
@section('top_title','Login - PayPerViews')
@section('head')
    <!-- Modern Mobile Web App Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="PayPerViews">
    <meta name="theme-color" content="#667eea">
    <meta name="msapplication-navbutton-color" content="#667eea">
    <meta name="msapplication-TileColor" content="#667eea"> 
    
    <!-- PWA Meta Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    
    <!-- PayPerViews Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo/payperviews-icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/payperviews-icon.svg') }}">
    <link rel="mask-icon" href="{{ asset('assets/images/logo/payperviews-icon.svg') }}" color="#667eea">
@endsection

@section('content')
<div class="stunning-login-wrapper"> 
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
        <div class="floating-particles"></div>
    </div> 
    
    <div class="login-container">
        <div class="row min-vh-100 align-items-center justify-content-center g-0">
            <!-- Left Side - Brand Section -->
            <div class="col-lg-6 d-none d-lg-flex">
                <div class="brand-section">
                    <div class="brand-content">
                        <div class="brand-logo">
                            <img src="{{ asset('assets/images/logo/payperviews-icon.svg') }}" alt="PayPerViews" style="width: 60px; height: 60px; filter: brightness(0) invert(1);">
                        </div>
                        <h1 class="brand-title">Welcome Back!</h1>
                        <p class="brand-subtitle">Experience the future of video earning with PayPerViews - where entertainment meets income</p>
                        
                        <div class="feature-highlights">
                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="highlight-text">
                                    <h6>Earn Real Money</h6>
                                    <p>Watch videos and get paid instantly</p>
                                </div>
                            </div>
                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="highlight-text">
                                    <h6>Fast Payouts</h6>
                                    <p>Quick and secure payment processing</p>
                                </div>
                            </div>
                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="highlight-text">
                                    <h6>Build Network</h6>
                                    <p>Invite friends and multiply earnings</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="form-section">
                    <div class="glass-card">
                        <div class="card-header">
                            <div class="login-icon">
                                <img src="{{ asset('assets/images/logo/payperviews-icon.svg') }}" alt="PayPerViews" style="width: 48px; height: 48px; filter: brightness(0) invert(1);">
                            </div>
                            <h2 class="form-title">Sign In to PayPerViews</h2>
                            <p class="form-subtitle">Access your earning dashboard</p>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}" class="stunning-form">
                            @csrf
                            
                            <!-- Username Field -->
                            <div class="input-group-beautiful">
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input id="username" 
                                           type="text" 
                                           class="stunning-input @error('username') is-invalid @enderror" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           placeholder=" "
                                           required 
                                           autocomplete="username" 
                                           autofocus>
                                    <label for="username" class="floating-label">
                                        {{ __('Username') }}
                                    </label>
                                    <div class="input-border"></div>
                                    <div class="input-focus-effect"></div>
                                    <div class="input-glow"></div>
                                </div>
                                @error('username')
                                    <div class="beautiful-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            
                            <!-- Password Field -->
                            <div class="input-group-beautiful">
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <input id="signin-password" 
                                           type="password" 
                                           class="stunning-input @error('password') is-invalid @enderror" 
                                           name="password" 
                                           placeholder=" "
                                           required 
                                           autocomplete="current-password">
                                    <label for="signin-password" class="floating-label">
                                        Password
                                    </label>
                                    <button type="button" class="stunning-password-toggle" onclick="togglePassword('signin-password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="input-border"></div>
                                    <div class="input-focus-effect"></div>
                                    <div class="input-glow"></div>
                                </div>
                                @error('password')
                                    <div class="beautiful-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            
                            <!-- Remember Me & Forgot Password -->
                            <div class="remember-section">
                                <div class="custom-checkbox">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="forgot-password" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="stunning-submit">
                                <span class="btn-text">{{ __('Sign In') }}</span>
                                <div class="btn-loader">
                                    <div class="spinner"></div>
                                </div>
                                <i class="fas fa-arrow-right btn-icon"></i>
                                <div class="button-glow"></div>
                            </button>
                            
                            <!-- Register Link -->
                            <div class="register-link">
                                Don't have an account? 
                                <a href="{{ route('register') }}">Create Account</a>
                            </div>
                        </form>
                        
                        <!-- Success/Error Messages -->
                        @session('success')
                            <div class="stunning-alert alert-success">
                                <div class="alert-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="alert-content">{{ session('success') }}</div>
                            </div>  
                        @endsession

                         @session('warning')
                            <div class="stunning-alert alert-warning">
                                <div class="alert-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="alert-content">{{ session('warning') }}</div>
                            </div>  
                        @endsession
                        
                        @session('error')
                            <div class="stunning-alert alert-error">
                                <div class="alert-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="alert-content">{{ session('error') }}</div>
                            </div>  
                        @endsession
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
        min-height: 100vh;
        position: relative;
    }

    /* Animated Background */
    .stunning-login-wrapper {
        min-height: 100vh;
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }

    .animated-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        overflow: hidden;
    }

    .gradient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        opacity: 0.7;
        animation: floatingOrbs 20s ease-in-out infinite;
    }

    .orb-1 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.4) 0%, transparent 70%);
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .orb-2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(240, 147, 251, 0.3) 0%, transparent 70%);
        top: 60%;
        right: 10%;
        animation-delay: 7s;
    }

    .orb-3 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(79, 172, 254, 0.4) 0%, transparent 70%);
        bottom: 20%;
        left: 30%;
        animation-delay: 14s;
    }

    .floating-particles {
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.3), transparent),
            radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.2), transparent),
            radial-gradient(1px 1px at 90px 40px, rgba(255, 255, 255, 0.4), transparent),
            radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.3), transparent);
        background-repeat: repeat;
        background-size: 200px 100px;
        animation: particleFloat 30s linear infinite;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    @keyframes floatingOrbs {
        0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
        33% { transform: translate(30px, -30px) scale(1.1) rotate(120deg); }
        66% { transform: translate(-20px, 20px) scale(0.9) rotate(240deg); }
    }

    @keyframes particleFloat {
        0% { transform: translateY(0); }
        100% { transform: translateY(-100px); }
    }

    /* Container Styles */
    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        z-index: 1;
    }

    /* Brand Section */
    .brand-section {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        border-radius: 24px 0 0 24px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-right: none;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 700px;
    }

    .brand-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            radial-gradient(circle, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: floatingDots 30s linear infinite;
    }

    @keyframes floatingDots {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }

    .brand-logo {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
        backdrop-filter: blur(15px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 2;
    }

    .brand-logo i {
        font-size: 40px;
        color: white;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .brand-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
        z-index: 2;
    }

    .brand-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        line-height: 1.6;
        max-width: 400px;
        margin-bottom: 40px;
        position: relative;
        z-index: 2;
    }

    .feature-highlights {
        text-align: left;
        position: relative;
        z-index: 2;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .highlight-item:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .highlight-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        color: white;
        font-size: 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .highlight-text h6 {
        margin: 0 0 5px 0;
        font-weight: 600;
        font-size: 16px;
    }

    .highlight-text p {
        margin: 0;
        opacity: 0.8;
        font-size: 14px;
        line-height: 1.4;
    }

    /* Form Section */
    .form-section {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 700px;
        padding: 40px;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border-radius: 0 24px 24px 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-left: none;
        box-shadow: 
            0 25px 45px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        padding: 60px 50px;
        width: 100%;
        max-width: 500px;
        position: relative;
        overflow: hidden;
        min-height: 700px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .login-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        color: white;
        font-size: 32px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .form-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 10px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .form-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    /* Beautiful Input Groups */
    .input-group-beautiful {
        position: relative;
        margin-bottom: 35px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 25px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
        color: rgba(255, 255, 255, 0.6);
        font-size: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    .stunning-input {
        width: 100%;
        height: 70px;
        padding: 25px 25px 25px 70px;
        background: rgba(255, 255, 255, 0.08);
        border: 2px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        font-size: 18px;
        color: white;
        font-weight: 500;
        outline: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(20px);
        position: relative;
        z-index: 3;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .stunning-input::placeholder {
        color: transparent;
    }

    .stunning-input:hover {
        border-color: rgba(102, 126, 234, 0.4);
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-2px);
        box-shadow: 
            0 15px 40px rgba(0, 0, 0, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .stunning-input:focus {
        border-color: rgba(102, 126, 234, 0.8);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 
            0 0 0 6px rgba(102, 126, 234, 0.15),
            0 20px 50px rgba(102, 126, 234, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .stunning-input:focus + .floating-label {
        transform: translateY(-45px) scale(0.85);
        color: #667eea;
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .stunning-input:focus ~ .input-border {
        transform: scaleX(1);
        opacity: 1;
    }

    .stunning-input:focus ~ .input-focus-effect {
        opacity: 1;
        transform: scale(1);
    }

    .stunning-input:focus ~ .input-glow {
        opacity: 1;
        transform: scale(1.05);
    }

    .stunning-input:not(:placeholder-shown) + .floating-label {
        transform: translateY(-45px) scale(0.85);
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
    }

    .stunning-input:focus ~ .input-icon,
    .stunning-input:not(:placeholder-shown) ~ .input-icon {
        color: #667eea;
        transform: translateY(-50%) scale(1.15);
        text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
    }

    .floating-label {
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
        font-size: 18px;
        font-weight: 500;
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 4;
        background: transparent;
        padding: 0 12px;
        margin-left: -12px;
    }

    .input-border {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        border-radius: 2px;
        transform: scaleX(0);
        transform-origin: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 2;
        opacity: 0;
    }

    .input-focus-effect {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.08) 0%, transparent 70%);
        border-radius: 20px;
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 1;
    }

    .input-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 25px;
        transform: translate(-50%, -50%) scale(0.9);
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 0;
        filter: blur(10px);
    }

    /* Password Toggle */
    .stunning-password-toggle {
        position: absolute;
        right: 25px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 20px;
        cursor: pointer;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 5;
    }

    .stunning-password-toggle:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        transform: translateY(-50%) scale(1.15);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    }

    /* Error Messages */
    .beautiful-error {
        display: flex;
        align-items: center;
        margin-top: 15px;
        padding: 15px 20px;
        background: rgba(245, 87, 108, 0.15);
        border: 1px solid rgba(245, 87, 108, 0.3);
        border-radius: 12px;
        color: #ff6b8a;
        font-size: 15px;
        backdrop-filter: blur(10px);
        animation: errorSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 25px rgba(245, 87, 108, 0.1);
    }

    .beautiful-error i {
        margin-right: 10px;
        font-size: 18px;
    }

    @keyframes errorSlideIn {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Remember Me */
    .remember-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 35px 0 40px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .custom-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        transition: color 0.3s ease;
        position: relative;
    }

    .custom-checkbox:hover {
        color: white;
    }

    .custom-checkbox input {
        appearance: none;
        width: 24px;
        height: 24px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        margin-right: 15px;
        position: relative;
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .custom-checkbox input:checked {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .custom-checkbox input:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .forgot-password {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 16px;
        transition: all 0.3s ease;
        position: relative;
        padding: 5px 0;
    }

    .forgot-password:hover {
        color: #667eea;
        text-decoration: none;
    }

    .forgot-password::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }

    .forgot-password:hover::after {
        width: 100%;
    }

    /* Submit Button */
    .stunning-submit {
        width: 100%;
        height: 70px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 20px;
        color: white;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 15px 35px rgba(102, 126, 234, 0.3),
            0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
    }

    .stunning-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .stunning-submit:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 20px 45px rgba(102, 126, 234, 0.4),
            0 10px 25px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .stunning-submit:hover::before {
        left: 100%;
    }

    .stunning-submit:hover .button-glow {
        opacity: 1;
        transform: scale(1.1);
    }

    .stunning-submit:active {
        transform: translateY(-1px);
    }

    .btn-text {
        transition: all 0.3s ease;
        z-index: 2;
        position: relative;
    }

    .btn-loader {
        display: none;
        position: absolute;
        z-index: 2;
    }

    .btn-icon {
        margin-left: 10px;
        transition: all 0.3s ease;
        z-index: 2;
        position: relative;
    }

    .stunning-submit:hover .btn-icon {
        transform: translateX(5px);
    }

    .button-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.3) 100%);
        border-radius: 25px;
        transform: translate(-50%, -50%) scale(0.9);
        opacity: 0;
        transition: all 0.4s ease;
        pointer-events: none;
        z-index: 0;
        filter: blur(15px);
    }

    .spinner {
        width: 24px;
        height: 24px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Register Link */
    .register-link {
        text-align: center;
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
    }

    .register-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
    }

    .register-link a:hover {
        color: #764ba2;
        text-decoration: none;
    }

    .register-link a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }

    .register-link a:hover::after {
        width: 100%;
    }

    /* Stunning Alerts */
    .stunning-alert {
        display: flex;
        align-items: center;
        padding: 18px 25px;
        border-radius: 15px;
        margin-top: 25px;
        backdrop-filter: blur(15px);
        border: 1px solid;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        animation: alertSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stunning-alert.alert-success {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .stunning-alert.alert-error {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }

    .alert-icon {
        margin-right: 15px;
        font-size: 20px;
    }

    .alert-content {
        flex: 1;
        font-weight: 500;
    }

    @keyframes alertSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .brand-section,
        .glass-card {
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-height: auto;
        }
        
        .glass-card {
            margin-top: 20px;
        }
    }

    @media (max-width: 768px) {
        .login-container {
            padding: 15px;
        }

        .brand-section,
        .form-section {
            padding: 40px 30px;
        }

        .glass-card {
            padding: 40px 30px;
            border-radius: 20px;
            margin-top: 0;
        }

        .brand-title {
            font-size: 2.5rem;
        }

        .form-title {
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .stunning-input {
            height: 65px;
            padding: 22px 22px 22px 65px;
            font-size: 16px;
        }

        .input-icon {
            left: 22px;
            font-size: 18px;
        }

        .floating-label {
            left: 65px;
            font-size: 16px;
        }

        .stunning-password-toggle {
            right: 22px;
            font-size: 18px;
        }

        .remember-section {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
            gap: 15px;
        }

        .stunning-submit {
            height: 65px;
            font-size: 16px;
        }
    }

    @media (max-width: 576px) {
        .login-container {
            padding: 10px;
        }

        .brand-section,
        .form-section {
            padding: 30px 20px;
        }

        .glass-card {
            padding: 30px 20px;
        }

        .stunning-input {
            height: 60px;
            padding: 20px 20px 20px 60px;
            font-size: 15px;
        }

        .input-icon {
            left: 20px;
            font-size: 16px;
        }

        .floating-label {
            left: 60px;
            font-size: 15px;
        }

        .stunning-password-toggle {
            right: 20px;
            font-size: 16px;
        }

        .stunning-submit {
            height: 60px;
            font-size: 15px;
        }

        .brand-title {
            font-size: 2rem;
        }

        .form-title {
            font-size: 1.75rem;
        }
    }

    @media (max-width: 480px) {
        .stunning-input {
            height: 55px;
            padding: 18px 18px 18px 55px;
            font-size: 14px;
        }

        .input-icon {
            left: 18px;
            font-size: 15px;
        }

        .floating-label {
            left: 55px;
            font-size: 14px;
        }

        .stunning-password-toggle {
            right: 18px;
            font-size: 15px;
        }

        .stunning-submit {
            height: 55px;
            font-size: 14px;
        }
    }

    /* Touch device improvements */
    @media (hover: none) and (pointer: coarse) {
        .stunning-input {
            min-height: 55px;
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        .stunning-password-toggle {
            min-width: 44px;
            min-height: 44px;
        }
        
        .custom-checkbox input {
            min-width: 28px;
            min-height: 28px;
        }
    }
</style>

<script>
    // Toggle password visibility
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    
    // Form submission with loading state
    document.querySelector('.stunning-form').addEventListener('submit', function(e) {
        const button = this.querySelector('.stunning-submit');
        const btnText = button.querySelector('.btn-text');
        const btnLoader = button.querySelector('.btn-loader');
        const btnIcon = button.querySelector('.btn-icon');
        
        // Show loading state
        btnText.style.opacity = '0';
        btnIcon.style.opacity = '0';
        btnLoader.style.display = 'block';
        button.disabled = true;
        
        // Reset after 3 seconds if form doesn't submit properly
        setTimeout(() => {
            btnText.style.opacity = '1';
            btnIcon.style.opacity = '1';
            btnLoader.style.display = 'none';
            button.disabled = false;
        }, 3000);
    });
    
    // Auto-hide alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.stunning-alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => {
                    alert.remove();
                }, 400);
            }, 5000);
        });
    });
    
    // Enhanced input focus animations
    document.querySelectorAll('.stunning-input').forEach(input => {
        // Check if input has value on page load
        if (input.value && input.value.trim() !== '') {
            input.parentElement.classList.add('has-value');
        }
        
        input.addEventListener('focus', function() {
            this.parentElement.parentElement.classList.add('focused');
            this.parentElement.classList.add('has-focus');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.parentElement.classList.remove('focused');
            this.parentElement.classList.remove('has-focus');
            
            if (this.value && this.value.trim() !== '') {
                this.parentElement.classList.add('has-value');
            } else {
                this.parentElement.classList.remove('has-value');
            }
        });
        
        // Handle input events for real-time updates
        input.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.parentElement.classList.add('has-value');
            } else {
                this.parentElement.classList.remove('has-value');
            }
        });
        
        // Handle paste events
        input.addEventListener('paste', function() {
            setTimeout(() => {
                if (this.value && this.value.trim() !== '') {
                    this.parentElement.classList.add('has-value');
                } else {
                    this.parentElement.classList.remove('has-value');
                }
            }, 10);
        });
    });
    
    // Window resize handler for responsive adjustments
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Refresh input states on resize
            document.querySelectorAll('.stunning-input').forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    input.parentElement.classList.add('has-value');
                } else {
                    input.parentElement.classList.remove('has-value');
                }
            });
        }, 100);
    });
    
    // Initialize on DOM content loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial states for inputs with values
        document.querySelectorAll('.stunning-input').forEach(input => {
            if (input.value && input.value.trim() !== '') {
                input.parentElement.classList.add('has-value');
            }
        });
        
        // Add subtle entrance animations
        const elements = document.querySelectorAll('.input-group-beautiful, .stunning-submit, .register-link');
        elements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 100 + (index * 150));
        });
    });
    
    // Enhanced error handling
    document.querySelectorAll('.stunning-input.is-invalid').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentElement.parentElement.querySelector('.beautiful-error');
                if (errorDiv) {
                    errorDiv.style.opacity = '0';
                    errorDiv.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 300);
                }
            }
        });
    });
</script>
@endsection
