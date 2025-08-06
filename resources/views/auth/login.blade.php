@extends('layouts.form_layout')
@section('top_title','Login')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            <img src="{{ asset('assets/images/logo/payperviews-icon.svg') }}" alt="PayPerViews" style="width: 40px; height: 40px; filter: brightness(0) invert(1);">
                        </div>
                        <h1 class="brand-title">Welcome Back to PayPerViews!</h1>
                        <p class="brand-subtitle">Experience the future of video earning with our stunning platform</p>
                        
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
                            <!-- Back Button -->
                            <div class="back-button-container">
                                <a href="{{ url('/') }}" class="back-button">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to Home</span>
                                </a>
                                <button type="button" class="refresh-button" onclick="refreshToCleanLogin()">
                                    <i class="fas fa-sync-alt"></i>
                                    <span>Refresh</span>
                                </button>
                            </div>
                            
                            <div class="login-icon">
                                <img src="{{ asset('assets/images/logo/payperviews-icon.svg') }}" alt="PayPerViews" style="width: 32px; height: 32px; filter: brightness(0) invert(1);">
                            </div>
                            <h2 class="form-title">Sign In to PayPerViews</h2>
                            <p class="form-subtitle">Access your earning dashboard</p>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}" class="stunning-form" id="login-form">
                            @csrf
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token-field">
                            
                            <!-- Email Verification Alert (Enhanced) -->
                            @if(session('show_resend_verification') || session('user_email'))
                                <div class="stunning-alert alert-warning" id="email-verification-alert">
                                    <div class="alert-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="alert-content">
                                        <p><strong>Email Verification Required</strong></p>
                                        <p>Your email address is not verified yet. Please check your email and click the verification link to activate your account.</p>
                                        
                                        <div class="resend-verification-form" style="margin-top: 15px; text-align: center;">
                                            <p style="color: rgba(255, 255, 255, 0.8); font-size: 14px; margin-bottom: 15px;">
                                                Didn't receive the email?
                                            </p>
                                            <button onclick="resendVerificationEmail('{{ session('user_email') }}')" 
                                                    id="static-resend-verification-btn" 
                                                    class="btn btn-sm"
                                                    style="
                                                        background: linear-gradient(135deg, #f59e0b, #d97706);
                                                        border: none;
                                                        color: white;
                                                        padding: 10px 20px;
                                                        border-radius: 8px;
                                                        font-weight: 600;
                                                        cursor: pointer;
                                                        transition: all 0.3s ease;
                                                        font-size: 14px;
                                                        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
                                                    "
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(245,158,11,0.4)'"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(245,158,11,0.3)'">
                                                <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                                                Resend Verification Email
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Username or Email Field -->
                            <div class="input-group-beautiful">
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input id="username" 
                                           type="text" 
                                           class="stunning-input @error('username') is-invalid @enderror" 
                                           name="username" 
                                           value="{{ old('username', $rememberedLogin ?? '') }}" 
                                           placeholder=" "
                                           required 
                                           autocomplete="username email" 
                                           autofocus>
                                    <label for="username" class="floating-label">
                                        {{ __('Username or Email') }}
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
                                <div class="input-hint">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        You can login using either your username or email address
                                    </small>
                                </div>
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
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') || (!empty($rememberedLogin)) ? 'checked' : '' }}>
                                    <label for="remember">
                                        {{ __('Remember Me for 7 days') }}
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
                        
                        <!-- Session Notifications (for logout messages) -->
                        @if(isset($sessionNotifications) && $sessionNotifications->count() > 0)
                            @foreach($sessionNotifications as $username => $userNotifications)
                                @foreach($userNotifications as $notification)
                                    <div class="stunning-alert alert-warning session-logout-notification">
                                        <div class="alert-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="alert-content">
                                            <div class="notification-header"> 
                                                <strong>{{ $notification->title }}</strong>
                                                <small class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            </div>
                                            <div class="notification-message">
                                                <strong>{{ $username }}:</strong> {{ $notification->message }}
                                            </div>
                                            @if($notification->new_login_ip)
                                                <div class="notification-details">
                                                    <small>New login from: {{ $notification->new_login_ip }} ({{ $notification->new_login_device }})</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @endif
                        
                        <!-- Success/Error Messages -->
                        @if ($errors->any())
                            <div class="stunning-alert alert-error">
                                <div class="alert-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="alert-content">
                                    @if ($errors->has('csrf'))
                                        Your session has expired. Please refresh the page and try again.
                                    @elseif ($errors->has('throttle'))
                                        Too many login attempts. Please try again later.
                                    @else
                                        {{ $errors->first() }}
                                    @endif
                                </div>
                            </div>  
                        @endif
                        
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

    /* Standard Background */
    .stunning-login-wrapper {
        min-height: 100vh;
        position: relative;
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        background-attachment: fixed;
    }

    .animated-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        overflow: hidden;
        /* Disabled animated background elements for better readability */
        display: none;
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
        position: relative;
    }

    /* Back Button and Refresh Button Styles */
    .back-button-container {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10;
        display: flex;
        gap: 10px;
    }

    .back-button, .refresh-button {
        display: inline-flex;
        align-items: center;
        padding: 12px 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        gap: 8px;
        cursor: pointer;
    }

    .refresh-button {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.2);
        color: rgba(34, 197, 94, 0.8);
    }

    .back-button:hover, .refresh-button:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(102, 126, 234, 0.4);
        color: #667eea;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }

    .refresh-button:hover {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.4);
        color: #22c55e;
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
    }

    .back-button i, .refresh-button i {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .back-button:hover i {
        transform: translateX(-3px);
    }

    .refresh-button:hover i {
        transform: rotate(180deg);
    }

    .back-button span, .refresh-button span {
        font-weight: 500;
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

    /* Input Hint */
    .input-hint {
        margin-top: 8px;
        opacity: 0.8;
        animation: hintFadeIn 0.3s ease-out;
    }

    .input-hint small {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: rgba(148, 163, 184, 0.8);
        line-height: 1.4;
    }

    .input-hint i {
        margin-right: 6px;
        font-size: 12px;
        opacity: 0.7;
    }

    @keyframes hintFadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 0.8;
            transform: translateY(0);
        }
    }

    /* Remembered Login Indicator */
    .remembered-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 24px;
        height: 24px;
        background: rgba(34, 197, 94, 0.15);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(34, 197, 94, 0.8);
        font-size: 10px;
        z-index: 6;
        opacity: 0;
        animation: rememberIndicatorFadeIn 0.5s ease-out 0.3s forwards;
        cursor: help;
        backdrop-filter: blur(5px);
    }

    .remembered-login .remembered-indicator {
        opacity: 1;
    }

    @keyframes rememberIndicatorFadeIn {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .remembered-login .stunning-input {
        border-color: rgba(34, 197, 94, 0.3);
        background: rgba(34, 197, 94, 0.05);
    }

    .remembered-login .stunning-input:focus {
        border-color: rgba(34, 197, 94, 0.6);
        box-shadow: 
            0 0 0 6px rgba(34, 197, 94, 0.15),
            0 20px 50px rgba(34, 197, 94, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
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

    /* Stunning Alerts - Enhanced visibility */
    .stunning-alert {
        display: flex;
        align-items: center;
        padding: 18px 25px;
        border-radius: 15px;
        margin-top: 25px;
        backdrop-filter: blur(15px);
        border: 2px solid;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: alertSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
    }

    .stunning-alert.alert-success {
        background: rgba(16, 185, 129, 0.25);
        border-color: rgba(16, 185, 129, 0.6);
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .stunning-alert.alert-warning {
        background: rgba(245, 158, 11, 0.25);
        border-color: rgba(245, 158, 11, 0.6);
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .stunning-alert.alert-error {
        background: rgba(239, 68, 68, 0.25);
        border-color: rgba(239, 68, 68, 0.6);
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    /* Session expiry specific styling */
    .stunning-alert.session-expired {
        background: rgba(245, 158, 11, 0.2) !important;
        border: 2px solid rgba(245, 158, 11, 0.4) !important;
        color: #f59e0b !important;
        animation: sessionExpiredPulse 2s infinite;
    }
    
    @keyframes sessionExpiredPulse {
        0%, 100% { 
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); 
        }
        50% { 
            box-shadow: 0 0 0 10px rgba(245, 158, 11, 0.1); 
        }
    }

    .alert-icon {
        margin-right: 15px;
        font-size: 20px;
    }

    .alert-content {
        flex: 1;
        font-weight: 500;
    }

    /* Session Logout Notification Styles - Enhanced visibility */
    .session-logout-notification {
        background: rgba(245, 158, 11, 0.3) !important;
        border-color: rgba(245, 158, 11, 0.7) !important;
        border-left: 4px solid #f59e0b;
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .notification-header strong {
        font-size: 16px;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .notification-time {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 400;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .notification-message {
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 6px;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .notification-details {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.9);
        padding-top: 6px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
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

        /* Back Button and Refresh Button Mobile Styles */
        .back-button, .refresh-button {
            padding: 10px 16px;
            font-size: 13px;
            border-radius: 12px;
        }

        .back-button i, .refresh-button i {
            font-size: 11px;
        }

        .back-button-container {
            flex-direction: column;
            gap: 8px;
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

        /* Back Button and Refresh Button Small Mobile Styles */
        .back-button, .refresh-button {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 10px;
            gap: 6px;
        }

        .back-button i, .refresh-button i {
            font-size: 10px;
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

    /* Enhanced Email Verification Popup Styling */
    .swal2-popup.email-verification-popup {
        border-radius: 20px;
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
        border: 2px solid rgba(245, 158, 11, 0.3);
    }

    .email-verification-popup .swal2-title {
        color: #1f2937 !important;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .email-verification-popup .swal2-content {
        color: #4b5563 !important;
    }

    .email-verification-popup .swal2-confirm {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
        transition: all 0.3s ease !important;
    }

    .email-verification-popup .swal2-confirm:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
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
    
    // Clean refresh function to avoid 419 errors
    function refreshToCleanLogin() {
        // Visual feedback
        const refreshButton = document.querySelector('.refresh-button');
        if (refreshButton) {
            refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Refreshing...</span>';
            refreshButton.style.opacity = '0.7';
            refreshButton.disabled = true;
        }
        
        // Clear any session storage or local storage that might cause issues
        if (typeof(Storage) !== "undefined") {
            sessionStorage.removeItem('login_attempt');
            sessionStorage.removeItem('csrf_token');
            localStorage.removeItem('login_session');
        }
        
        // Create timestamp
        const timestamp = Math.floor(Date.now() / 1000); 
        
        // Small delay to show the loading state
        setTimeout(() => {
            // Navigate to login URL with logout parameters
            window.location.href = `/login?from_logout=1&t=${timestamp}`;
        }, 500);
    }
    
    // ENHANCED FORM SUBMISSION WITH COMPREHENSIVE VALIDATION AND SWEETALERT
    document.querySelector('.stunning-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submission for validation
        
        const form = this;
        const button = this.querySelector('.stunning-submit');
        const btnText = button.querySelector('.btn-text');
        const btnLoader = button.querySelector('.btn-loader');
        const btnIcon = button.querySelector('.btn-icon');
        
        // Get form values
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('signin-password').value;
        
        // Clear previous error styling
        document.querySelectorAll('.stunning-input').forEach(input => {
            input.parentElement.style.borderColor = '';
            input.parentElement.style.background = '';
            input.classList.remove('is-invalid');
        });
        
        // Clear previous error messages
        document.querySelectorAll('.beautiful-error').forEach(error => {
            error.style.display = 'none';
        });
        
        let hasValidationErrors = false;
        let errorMessages = [];
        
        // Client-side validation
        if (!username) {
            showFieldError('username', 'Username or email is required');
            errorMessages.push('Username or email is required');
            hasValidationErrors = true;
        } else if (username.length < 3) {
            showFieldError('username', 'Username or email must be at least 3 characters');
            errorMessages.push('Username or email must be at least 3 characters');
            hasValidationErrors = true;
        }
        
        if (!password) {
            showFieldError('signin-password', 'Password is required');
            errorMessages.push('Password is required');
            hasValidationErrors = true;
        } else if (password.length < 8) {
            showFieldError('signin-password', 'Password must be at least 8 characters');
            errorMessages.push('Password must be at least 8 characters');
            hasValidationErrors = true;
        }
        
        // If validation fails, show SweetAlert and stop
        if (hasValidationErrors) {
            const errorHtml = errorMessages.map(error => `• ${error}`).join('<br>');
            
            Swal.fire({
                title: 'Validation Error!',
                html: `Please correct the following:<br><br>${errorHtml}`,
                icon: 'error',
                confirmButtonText: 'Fix Errors',
                width: '500px'
            });
            return false;
        }
        
        // Show loading state
        btnText.style.opacity = '0';
        btnIcon.style.opacity = '0';
        btnLoader.style.display = 'block';
        button.disabled = true;
        
        // Refresh CSRF token before submission
        refreshCSRFToken().then(() => {
            // Submit form via AJAX for better error handling
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                if (response.status === 419) {
                    // CSRF token expired - refresh and retry
                    const newToken = await refreshCSRFToken();
                    if (newToken) {
                        formData.set('_token', newToken);
                        return fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': newToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    }
                }
                return response;
            })
            .then(response => {
                if (response.ok || response.redirected) {
                    // Success - show SweetAlert and redirect
                    Swal.fire({
                        title: 'Login Successful!',
                        text: 'Welcome back! Redirecting to your dashboard...',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false
                    }).then(() => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            window.location.href = '/user/dashboard';
                        }
                    });
                } else if (response.status === 422) {
                    // Validation errors
                    return response.json().then(data => {
                        displayLoginErrors(data.errors || data.message, 'validation_failed', data);
                        resetSubmitButton();
                    });
                } else if (response.status === 429) {
                    // Too many attempts
                    Swal.fire({
                        title: 'Too Many Attempts!',
                        text: 'You have made too many login attempts. Please wait a few minutes before trying again.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    resetSubmitButton();
                } else {
                    // Other errors - handle specific error types
                    return response.json().then(data => {
                        // Extract error type and additional data from response
                        const errorType = data.error_type || null;
                        const errorMessage = data.message || 'Login failed. Please check your credentials and try again.';
                        const extraData = {
                            input_type: data.input_type,
                            remaining_attempts: data.remaining_attempts,
                            unlock_time_human: data.unlock_time_human,
                            user_status: data.user_status,
                            needs_verification: data.needs_verification,
                            user_email: data.user_email
                        };
                        
                        // Check if it's an email verification error
                        if (data.needs_verification || errorType === 'email_not_verified') {
                            showEmailVerificationError(data.user_email || getEmailFromForm());
                        } else {
                            displayLoginErrors(errorMessage, errorType, extraData);
                        }
                        resetSubmitButton();
                    }).catch(() => {
                        // Fallback if JSON parsing fails
                        return response.text().then(text => {
                            let errorMessage = 'Login failed. Please check your credentials and try again.';
                            let errorType = null;
                            
                            // Try to extract specific error information from HTML response
                            if (text.includes('password') && (text.includes('invalid') || text.includes('incorrect'))) {
                                errorMessage = 'Incorrect password. Please check your password and try again.';
                                errorType = 'invalid_password';
                            } else if (text.includes('username') && text.includes('not found')) {
                                errorMessage = 'Username not found. Please check your username or register for a new account.';
                                errorType = 'user_not_found';
                            } else if (text.includes('email') && text.includes('not found')) {
                                errorMessage = 'Email address not found. Please check your email or register for a new account.';
                                errorType = 'user_not_found';
                            } else if (text.includes('account') && (text.includes('suspended') || text.includes('deactivated'))) {
                                errorMessage = 'Your account has been suspended. Please contact support.';
                                errorType = 'account_inactive';
                            } else if (text.includes('locked')) {
                                errorMessage = 'Your account is temporarily locked. Please try again later.';
                                errorType = 'account_locked';
                            } else if (text.includes('email') && text.includes('verification')) {
                                showEmailVerificationError(getEmailFromForm());
                                resetSubmitButton();
                                return;
                            }
                            
                            displayLoginErrors(errorMessage, errorType);
                            resetSubmitButton();
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                
                Swal.fire({
                    title: 'Connection Error!',
                    text: 'Unable to connect to the server. Please check your internet connection and try again.',
                    icon: 'error',
                    confirmButtonText: 'Retry'
                });
                resetSubmitButton();
            });
        }).catch(() => {
            // CSRF refresh failed, fall back to normal form submission
            form.submit();
        });
        
        function resetSubmitButton() {
            btnText.style.opacity = '1';
            btnIcon.style.opacity = '1';
            btnLoader.style.display = 'none';
            button.disabled = false;
        }
        
        return false; // Prevent default form submission
    });
    
    // Function to show field-specific errors
    function showFieldError(fieldId, message) {
        const input = document.getElementById(fieldId);
        if (input) {
            // Add error styling
            input.classList.add('is-invalid');
            input.parentElement.style.borderColor = 'rgba(239, 68, 68, 0.8)';
            input.parentElement.style.background = 'rgba(239, 68, 68, 0.1)';
            
            // Find or create error message element
            let errorDiv = input.parentElement.parentElement.querySelector('.beautiful-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'beautiful-error';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i><span></span>';
                input.parentElement.parentElement.appendChild(errorDiv);
            }
            
            errorDiv.querySelector('span').textContent = message;
            errorDiv.style.display = 'flex';
            
            // Scroll to field
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    // Function to display login errors from server - Enhanced for specific error types
    function displayLoginErrors(errors, errorType = null, extraData = {}) {
        if (typeof errors === 'string') {
            // Single error message - determine icon and title based on error type
            let icon = 'error';
            let title = 'Login Error!';
            
            if (errorType === 'user_not_found') {
                icon = 'question';
                title = extraData.input_type === 'email' ? 'Email Address Not Found' : 'Username Not Found';
            } else if (errorType === 'account_inactive') {
                icon = 'warning';
                title = 'Account Suspended';
            } else if (errorType === 'account_locked') {
                icon = 'warning';
                title = 'Account Temporarily Locked';
            } else if (errorType === 'email_not_verified') {
                icon = 'info';
                title = 'Email Verification Required';
            } else if (errorType === 'invalid_password') {
                icon = 'error';
                title = 'Incorrect Password';
            } else if (errorType === 'account_locked_now') {
                icon = 'warning';
                title = 'Account Locked';
            }
            
            const swalConfig = {
                title: title,
                text: errors,
                icon: icon,
                confirmButtonText: 'OK',
                width: '500px'
            };
            
            // Add special handling for specific error types
            if (errorType === 'user_not_found') {
                swalConfig.showCancelButton = true;
                swalConfig.cancelButtonText = 'Register Now';
                swalConfig.confirmButtonColor = '#007bff';
                swalConfig.cancelButtonColor = '#28a745';
                
                Swal.fire(swalConfig).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = '/register';
                    }
                });
                return;
            } else if (errorType === 'email_not_verified') {
                swalConfig.showCancelButton = true;
                swalConfig.cancelButtonText = 'Resend Email';
                swalConfig.confirmButtonColor = '#007bff';
                swalConfig.cancelButtonColor = '#ffc107';
                
                Swal.fire(swalConfig).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        const email = getEmailFromForm();
                        if (email) {
                            resendVerificationEmail(email);
                        } else {
                            showEmailVerificationError();
                        }
                    }
                });
                return;
            } else if (errorType === 'account_locked' || errorType === 'account_locked_now') {
                swalConfig.showCancelButton = true;
                swalConfig.cancelButtonText = 'Contact Support';
                swalConfig.confirmButtonColor = '#007bff';
                swalConfig.cancelButtonColor = '#dc3545';
                
                Swal.fire(swalConfig).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        // You can add support contact functionality here
                        window.location.href = '/contact';
                    }
                });
                return;
            }
            
            Swal.fire(swalConfig);
            return;
        }
        
        if (!errors || typeof errors !== 'object') {
            Swal.fire({
                title: 'Login Failed!',
                text: 'Please check your credentials and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Display field-specific errors
        let errorList = [];
        
        Object.keys(errors).forEach(field => {
            const errorMessages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
            const firstError = errorMessages[0];
            
            // Map field names to display names
            let fieldDisplayName = field;
            if (field === 'username') fieldDisplayName = 'Username/Email';
            if (field === 'password') fieldDisplayName = 'Password';
            
            errorList.push(`${fieldDisplayName}: ${firstError}`);
            
            // Show field-specific error with proper styling
            if (field === 'password') {
                showFieldError('signin-password', firstError);
            } else {
                showFieldError(field, firstError);
            }
        });
        
        // Show comprehensive error message with SweetAlert
        if (errorList.length > 0) {
            const errorHtml = errorList.map(error => `• ${error}`).join('<br>');
            
            let title = 'Login Errors!';
            let icon = 'error';
            
            if (errorType === 'invalid_password') {
                title = 'Password Incorrect';
                icon = 'error';
            } else if (errorType === 'validation_failed') {
                title = 'Please Check Your Input';
                icon = 'warning';
            }
            
            Swal.fire({
                title: title,
                html: `Please correct the following:<br><br>${errorHtml}`,
                icon: icon,
                confirmButtonText: 'Fix Errors',
                width: '600px'
            });
        }
    }
    
    // CSRF Token refresh function
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
                
                return data.csrf_token;
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                return null;
            });
    }

    // Helper function to get email from form
    function getEmailFromForm() {
        const usernameField = document.getElementById('username');
        if (usernameField && usernameField.value) {
            const value = usernameField.value.trim();
            // Check if it looks like an email (contains @ and at least one dot after @)
            if (value.includes('@') && value.indexOf('.') > value.indexOf('@')) {
                return value;
            }
        }
        
        // Also check if there's an email in the static alert
        const staticAlert = document.getElementById('email-verification-alert');
        if (staticAlert) {
            const staticButton = document.getElementById('static-resend-verification-btn');
            if (staticButton && staticButton.onclick) {
                // Try to extract email from onclick attribute
                const onclickStr = staticButton.getAttribute('onclick');
                if (onclickStr) {
                    const match = onclickStr.match(/resendVerificationEmail\(['"]([^'"]+)['"]\)/);
                    if (match && match[1] && match[1] !== '') {
                        return match[1];
                    }
                }
            }
        }
        
        return null;
    }
    
    // Function to show email verification error with resend button
    function showEmailVerificationError(email) {
        // Always show resend button if we can extract an email, otherwise try to get it from the form
        if (!email) {
            email = getEmailFromForm();
        }
        
        const showResendButton = true; // Always show resend button since users need it
        
        Swal.fire({
            title: 'Email Verification Required',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p style="margin-bottom: 15px;">
                        <i class="fas fa-envelope" style="color: #f59e0b; margin-right: 8px;"></i>
                        Your email address is not verified yet.
                    </p>
                    <p style="margin-bottom: 20px; color: #6b7280;">
                        Please check your email and click the verification link to activate your account.
                    </p>
                    ${showResendButton ? `
                    <div style="text-align: center; margin-top: 20px;">
                        <p style="color: #6b7280; font-size: 14px; margin-bottom: 15px;">
                            Didn't receive the email or email expired?
                        </p>
                        <button onclick="resendVerificationEmail('${email || ''}')" 
                                id="resend-verification-btn" 
                                style="
                                    background: linear-gradient(135deg, #f59e0b, #d97706);
                                    border: none;
                                    color: white;
                                    padding: 12px 24px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    font-size: 14px;
                                    box-shadow: 0 2px 8px rgba(245,158,11,0.3);
                                "
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(245,158,11,0.3)'">
                            <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                            Resend Verification Email
                        </button>
                        <div style="margin-top: 10px;">
                            <small style="color: #9ca3af; font-size: 12px;">
                                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                We'll send a new verification link to your email
                            </small>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'OK',
            width: '500px',
            showCloseButton: true,
            allowOutsideClick: false,
            customClass: {
                popup: 'email-verification-popup'
            }
        });
    }
    
    // Function to resend verification email via AJAX
    function resendVerificationEmail(email) {
        // If no email provided, try to get it from the form
        if (!email) {
            email = getEmailFromForm();
        }
        
        // If still no email, ask user to enter it
        if (!email) {
            Swal.fire({
                title: 'Email Address Required',
                html: `
                    <div style="text-align: left; margin-bottom: 20px;">
                        <p style="margin-bottom: 15px; color: #6b7280;">
                            Please enter your email address to resend the verification email:
                        </p>
                        <input type="email" 
                               id="resend-email-input" 
                               placeholder="Enter your email address"
                               style="
                                   width: 100%;
                                   padding: 12px;
                                   border: 2px solid #e5e7eb;
                                   border-radius: 8px;
                                   font-size: 14px;
                                   transition: border-color 0.3s ease;
                               "
                               onfocus="this.style.borderColor='#f59e0b'"
                               onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Send Verification Email',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const emailInput = document.getElementById('resend-email-input');
                    const inputEmail = emailInput.value.trim();
                    
                    if (!inputEmail) {
                        Swal.showValidationMessage('Email address is required');
                        return false;
                    }
                    
                    if (!inputEmail.includes('@') || !inputEmail.includes('.')) {
                        Swal.showValidationMessage('Please enter a valid email address');
                        return false;
                    }
                    
                    return inputEmail;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Retry with the entered email
                    resendVerificationEmail(result.value);
                }
            });
            return;
        }
        
        const button = document.getElementById('resend-verification-btn');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Sending...';
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('email', email);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('{{ route("resend.verification") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Email Sent!',
                    html: `
                        <div style="text-align: center;">
                            <p style="margin-bottom: 15px;">
                                ${data.message || 'Verification email sent successfully!'}
                            </p>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 15px;">
                                <i class="fas fa-envelope" style="color: #f59e0b; margin-right: 8px;"></i>
                                Sent to: <strong>${email}</strong>
                            </p>
                            <div style="background: #f3f4f6; padding: 12px; border-radius: 8px; margin-top: 15px;">
                                <small style="color: #6b7280;">
                                    <i class="fas fa-clock" style="margin-right: 4px;"></i>
                                    Please check your email inbox and spam folder
                                </small>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 8000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    title: 'Failed to Send',
                    text: data.message || 'Failed to send verification email. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                
                // Re-enable button
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane" style="margin-right: 8px;"></i>Resend Verification Email';
                }
            }
        })
        .catch(error => {
            console.error('Resend verification error:', error);
            
            Swal.fire({
                title: 'Connection Error!',
                text: 'Unable to send verification email. Please check your connection and try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-paper-plane" style="margin-right: 8px;"></i>Resend Verification Email';
            }
        });
    }

    // Handle server-side success/error messages with SweetAlert
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Continue'
            });
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    @endif

    @if(session('warning'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Warning!',
                text: "{{ session('warning') }}",
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        });
    @endif

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            let errorList = @json($errors->all());
            let errorType = '{{ session("error_type") }}';
            let extraData = {
                input_type: '{{ session("input_type") }}',
                remaining_attempts: {{ session("remaining_attempts") ?? 0 }},
                unlock_time: '{{ session("unlock_time") }}',
                user_status: {{ session("user_status") ?? 0 }}
            };
            
            // If we have multiple errors, display them as a list
            if (errorList.length > 1) {
                let errorHtml = errorList.map(error => `• ${error}`).join('<br>');
                displayLoginErrors(null, errorType, extraData);
                
                // Show detailed errors in a separate alert
                setTimeout(() => {
                    Swal.fire({
                        title: 'Detailed Error Information',
                        html: `The following issues were found:<br><br>${errorHtml}`,
                        icon: 'info',
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                }, 500);
            } else {
                // Single error - use enhanced display
                displayLoginErrors(errorList[0], errorType, extraData);
            }
        });
    @endif

    @if(session('show_resend_verification'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Email Verification Required!',
                html: 'Please verify your email address before logging in.<br><br>Didn\'t receive the email? <a href="#" onclick="resendVerification()">Resend verification email</a>',
                icon: 'info',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            });
        });
    @endif
    
    // Resend verification email function
    function resendVerification() {
        Swal.fire({
            title: 'Resending Verification Email...',
            text: 'Please wait while we send you a new verification email.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                
                // Submit resend verification form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("verification.resend.public") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const email = document.createElement('input');
                email.type = 'hidden';
                email.name = 'email';
                email.value = '{{ session("user_email") }}';
                form.appendChild(email);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // SIMPLE ERROR DISPLAY - NO COMPLICATIONS
    
    // SIMPLE ALERT FUNCTION - NO SESSION EXPIRY COMPLICATIONS

    // REMOVED ALL SESSION MONITORING - LET LARAVEL HANDLE IT
    
    // SIMPLIFIED PAGE LOAD - NO AGGRESSIVE SESSION CHECKING
    window.addEventListener('DOMContentLoaded', function() {
        // Allow logout parameters to stay in URL
        // No automatic URL cleaning to preserve from_logout and timestamp parameters
        
        // Just basic initialization, no session interference
        console.log('Login page loaded - using standard Laravel authentication');
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
        
        // Check if username field is pre-filled from remembered login
        const usernameField = document.getElementById('username');
        const rememberedLogin = '{{ $rememberedLogin ?? "" }}';
        
        if (rememberedLogin && usernameField.value === rememberedLogin) {
            // Add a subtle indicator that this is a remembered login
            const inputWrapper = usernameField.parentElement;
            inputWrapper.classList.add('remembered-login');
            
            // Add a small indicator
            const rememberedIndicator = document.createElement('div');
            rememberedIndicator.className = 'remembered-indicator';
            rememberedIndicator.innerHTML = '<i class="fas fa-clock"></i>';
            rememberedIndicator.title = 'This login was remembered from your last visit';
            inputWrapper.appendChild(rememberedIndicator);
            
            // Show a subtle notification
            setTimeout(() => {
                const hint = usernameField.parentElement.parentElement.querySelector('.input-hint small');
                if (hint) {
                    const originalText = hint.innerHTML;
                    hint.innerHTML = '<i class="fas fa-check-circle me-1"></i>Login remembered from your last visit';
                    hint.style.color = 'rgba(34, 197, 94, 0.8)';
                    
                    // Restore original hint after 3 seconds
                    setTimeout(() => {
                        hint.innerHTML = originalText;
                        hint.style.color = 'rgba(148, 163, 184, 0.8)';
                    }, 3000);
                }
            }, 500);
        }
        
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

<!-- Cache Clearing Script for Login Page -->
<script src="{{ asset('assets_custom/js/login-cache-clear.js') }}"></script>

<!-- Enhanced Cache Busting for Dashboard Redirects -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user came from logout or dashboard redirect
    const urlParams = new URLSearchParams(window.location.search);
    const fromLogout = urlParams.get('from_logout');
    const redirectFrom = urlParams.get('redirect_from');
    
    if (fromLogout === '1' || redirectFrom === 'dashboard') {
        // Clear any cached dashboard data
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(function(registration) {
                return registration.unregister();
            });
        }
        
        // Clear local storage items related to dashboard
        ['dashboard_cache', 'user_data', 'dashboard_state'].forEach(key => {
            localStorage.removeItem(key);
            sessionStorage.removeItem(key);
        });
        
        // Add meta tags to prevent caching
        const meta = document.createElement('meta');
        meta.httpEquiv = 'Cache-Control';
        meta.content = 'no-cache, no-store, must-revalidate';
        document.head.appendChild(meta);
        
        const pragma = document.createElement('meta');
        pragma.httpEquiv = 'Pragma';
        pragma.content = 'no-cache';
        document.head.appendChild(pragma);
        
        const expires = document.createElement('meta');
        expires.httpEquiv = 'Expires';
        expires.content = '0';
        document.head.appendChild(expires);
        
        console.log('Cache cleared after dashboard redirect');
    }
    
    // Prevent browser back button cache issues
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
});
</script>
@endsection
