<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Your Email - {{ $siteName ?? config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-wrapper {
            width: 100%;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .email-container {
            max-width: 650px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            position: relative;
        }
        
        .email-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 30px;
        }
        
        .email-logo {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .email-logo svg {
            width: 50px;
            height: 50px;
            fill: white;
        }
        
        .email-title {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 15px 0;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .email-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            font-weight: 400;
            margin: 0;
            position: relative;
            z-index: 2;
        }
        
        .email-body {
            padding: 50px 40px;
            background: white;
            position: relative;
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-message h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .welcome-message p {
            font-size: 16px;
            color: #7f8c8d;
            line-height: 1.8;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .verification-section {
            text-align: center;
            margin: 40px 0;
            padding: 40px 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            border: 1px solid #e9ecef;
        }
        
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 18px 45px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .verify-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .verify-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
            text-decoration: none;
            color: white;
        }
        
        .verify-button:hover::before {
            left: 100%;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        
        .feature-card {
            background: white;
            padding: 25px 20px;
            border-radius: 16px;
            text-align: center;
            border: 2px solid #f1f3f4;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }
        
        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .feature-desc {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.5;
        }
        
        .security-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffeaa7;
            border-radius: 16px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
        }
        
        .security-notice::before {
            content: '🔒';
            position: absolute;
            top: -10px;
            left: 25px;
            background: #fff3cd;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 18px;
        }
        
        .security-notice h4 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .security-notice p {
            color: #856404;
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .email-footer {
            background: #f8f9fa;
            padding: 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-content {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #764ba2;
        }
        
        .footer-text {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.6;
            margin: 15px 0;
        }
        
        .company-info {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #dee2e6;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .url-fallback {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            word-break: break-all;
        }
        
        .url-fallback p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .url-fallback a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
        }
        
        /* Responsive Design */
        @media (max-width: 640px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-header {
                padding: 40px 25px;
            }
            
            .email-title {
                font-size: 28px;
            }
            
            .email-subtitle {
                font-size: 16px;
            }
            
            .email-body {
                padding: 30px 25px;
            }
            
            .welcome-message h2 {
                font-size: 24px;
            }
            
            .verification-section {
                padding: 30px 20px;
            }
            
            .verify-button {
                padding: 16px 35px;
                font-size: 15px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .email-footer {
                padding: 30px 25px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background: #1a1a1a;
            }
            
            .email-body {
                background: #1a1a1a;
                color: #e0e0e0;
            }
            
            .welcome-message h2 {
                color: #ffffff;
            }
            
            .welcome-message p {
                color: #b0b0b0;
            }
            
            .feature-card {
                background: #2a2a2a;
                border-color: #404040;
                color: #e0e0e0;
            }
            
            .feature-title {
                color: #ffffff;
            }
            
            .feature-desc {
                color: #b0b0b0;
            }
            
            .email-footer {
                background: #2a2a2a;
                border-color: #404040;
            }
            
            .footer-text {
                color: #b0b0b0;
            }
            
            .company-name {
                color: #ffffff;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <div class="logo-container">
                    <div class="email-logo">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="email-title">Verify Your Email</h1>
                <p class="email-subtitle">Welcome to {{ $siteName ?? config('app.name') }}!</p>
            </div>
            <div class="email-body">
                <div class="welcome-message">
                    <h2>🎉 Almost There!</h2>
                    <p>Thank you for joining {{ $siteName ?? config('app.name') }}. You're just one click away from accessing your account and starting your journey with us. Please verify your email address to activate your account.</p>
                </div>
                
                <div class="verification-section">
                    <a href="{{ $verificationUrl }}" class="verify-button">
                        ✨ Verify Email Address
                    </a>
                    <p style="margin-top: 20px; font-size: 14px; color: #7f8c8d;">
                        Click the button above to verify your email address
                    </p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">💰</div>
                        <div class="feature-title">Start Earning</div>
                        <div class="feature-desc">Begin earning money by watching videos and completing tasks</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">👥</div>
                        <div class="feature-title">Refer Friends</div>
                        <div class="feature-desc">Invite friends and earn referral bonuses for each signup</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <div class="feature-title">Track Progress</div>
                        <div class="feature-desc">Monitor your earnings and performance with detailed analytics</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎁</div>
                        <div class="feature-title">Get Rewards</div>
                        <div class="feature-desc">Unlock special bonuses and rewards as you level up</div>
                    </div>
                </div>
                
                <div class="security-notice">
                    <h4>Security & Privacy</h4>
                    <p>This verification link will expire in <strong>60 minutes</strong> for your security. If you didn't create an account with {{ $siteName ?? config('app.name') }}, please ignore this email and no further action is required.</p>
                </div>
                
                <div class="url-fallback">
                    <p><strong>Having trouble with the button?</strong></p>
                    <p>Copy and paste this link into your browser:</p>
                    <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a> 
                </div>
            </div>
            
            <div class="email-footer">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="#">Help Center</a>
                        <a href="#">Contact Support</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                    
                    <p class="footer-text">
                        You're receiving this email because you recently created an account with {{ $siteName ?? config('app.name') }}. 
                        If you didn't sign up, you can safely ignore this email.
                    </p>
                    
                    <div class="company-info">
                        <div class="company-name">{{ $siteName ?? config('app.name') }}</div>
                        <p class="footer-text">
                            Making online earning simple and rewarding<br>
                            © {{ date('Y') }} {{ $siteName ?? config('app.name') }}. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
