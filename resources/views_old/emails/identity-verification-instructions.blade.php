<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Identity Verification Instructions - {{ $siteName ?? config('app.name') }}</title>
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
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
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
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.05) 20px
            );
            animation: slide 20s linear infinite;
        }
        
        @keyframes slide {
            0% { transform: translateX(-50px); }
            100% { transform: translateX(50px); }
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo i {
            font-size: 36px;
            color: #ffffff;
        }
        
        .email-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .email-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }
        
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        
        .content-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        
        .verification-steps {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            border-left: 5px solid #667eea;
        }
        
        .steps-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .steps-title i {
            margin-right: 10px;
            color: #667eea;
        }
        
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .step-description {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .verification-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        
        .verification-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .documents-required {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .documents-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .documents-title i {
            margin-right: 10px;
        }
        
        .document-list {
            list-style: none;
            padding: 0;
        }
        
        .document-list li {
            padding: 8px 0;
            color: #856404;
            display: flex;
            align-items: center;
        }
        
        .document-list li:before {
            content: '📄';
            margin-right: 10px;
        }
        
        .security-notice {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .security-title {
            font-weight: 600;
            color: #0c5460;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .security-title i {
            margin-right: 10px;
        }
        
        .security-text {
            color: #0c5460;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: #667eea;
            color: #ffffff;
            border-radius: 50%;
            text-decoration: none;
            margin: 0 5px;
            line-height: 40px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        
        .contact-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .contact-item {
            display: inline-block;
            margin: 0 15px;
            color: #666;
            font-size: 14px;
        }
        
        .contact-item i {
            margin-right: 5px;
            color: #667eea;
        }
        
        @media (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-title {
                font-size: 24px;
            }
            
            .verification-steps {
                padding: 20px;
            }
            
            .step-item {
                padding: 10px;
            }
            
            .contact-item {
                display: block;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo-container">
                    <div class="logo">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h1 class="email-title">Identity Verification Required</h1>
                    <p class="email-subtitle">Complete your account verification to unlock all features</p>
                </div>
            </div>

            <!-- Content -->
            <div class="email-content">
                <div class="greeting">
                    Hello {{ $user->firstname }} {{ $user->lastname }},
                </div>

                <div class="content-text">
                    We hope this email finds you well. To ensure the security of your account and comply with regulatory requirements, we need you to complete your identity verification process.
                </div>

                <div class="content-text">
                    Your account is currently <strong>unverified</strong>, which means some features may be limited. Once you complete the verification process, you'll have full access to all our services.
                </div>

                <!-- Verification Steps -->
                <div class="verification-steps">
                    <div class="steps-title">
                        <i class="fas fa-list-check"></i>
                        Verification Process
                    </div>

                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-title">Access Verification Portal</div>
                            <div class="step-description">Click the verification button below to access your personal verification portal.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-title">Upload Required Documents</div>
                            <div class="step-description">Upload clear photos or scans of your identification documents.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Wait for Review</div>
                            <div class="step-description">Our team will review your documents within 24-48 hours.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <div class="step-title">Get Verified</div>
                            <div class="step-description">Once approved, you'll receive confirmation and gain full account access.</div>
                        </div>
                    </div>
                </div>

                <!-- Verification Button -->
                <div style="text-align: center;">
                    <a href="{{ $verificationUrl }}" class="verification-button">
                        Start Verification Process
                    </a>
                </div>

                <!-- Required Documents -->
                <div class="documents-required">
                    <div class="documents-title">
                        <i class="fas fa-file-alt"></i>
                        Required Documents
                    </div>
                    <ul class="document-list">
                        <li>Government-issued photo ID (Passport, Driver's License, or National ID)</li>
                        <li>Proof of address (Utility bill or bank statement - not older than 3 months)</li>
                        <li>Clear selfie photo holding your ID document</li>
                    </ul>
                </div>

                <!-- Security Notice -->
                <div class="security-notice">
                    <div class="security-title">
                        <i class="fas fa-shield-alt"></i>
                        Security & Privacy
                    </div>
                    <div class="security-text">
                        Your documents are encrypted and stored securely. We use industry-standard security measures to protect your personal information. Your data will only be used for verification purposes and will never be shared with third parties.
                    </div>
                </div>

                <div class="content-text">
                    If you have any questions about the verification process or need assistance, please don't hesitate to contact our support team at <strong>{{ $supportEmail }}</strong>.
                </div>

                <div class="content-text">
                    Thank you for your cooperation and for choosing {{ $siteName }}.
                </div>

                <div class="content-text" style="margin-top: 30px;">
                    Best regards,<br>
                    <strong>{{ $siteName }} Security Team</strong>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <div class="footer-text">
                    This email was sent to {{ $user->email }} because you have an account with {{ $siteName }}.
                </div>

                <div class="footer-text">
                    If you didn't request this email, please contact our support team immediately.
                </div>

                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        {{ $supportEmail }}
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-globe"></i>
                        {{ config('app.url') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
