<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $type ?? 'Verification Code' }} - {{ config('app.name') }}</title>
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
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
            position: relative;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .email-body {
            padding: 50px 40px;
            text-align: center;
        }
        
        .verification-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .email-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .email-subtitle {
            font-size: 16px;
            color: #718096;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .verification-code {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 36px;
            font-weight: 800;
            padding: 20px 40px;
            border-radius: 15px;
            letter-spacing: 8px;
            margin: 30px 0;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
            font-family: 'Courier New', monospace;
        }
        
        .info-section {
            background: #f8fafc;
            border-radius: 15px;
            padding: 30px;
            margin: 40px 0;
            border-left: 5px solid #667eea;
        }
        
        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }
        
        .info-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .info-list li {
            color: #4a5568;
            margin-bottom: 8px;
            position: relative;
            padding-left: 25px;
        }
        
        .info-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        
        .email-footer {
            background: #f8fafc;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            color: #718096;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .contact-info {
            color: #4a5568;
            font-size: 14px;
        }
        
        .security-note {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .security-note h4 {
            color: #e53e3e;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .security-note p {
            color: #c53030;
            font-size: 14px;
            margin: 0;
        }
        
        @media (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .verification-code {
                font-size: 28px;
                padding: 15px 30px;
                letter-spacing: 4px;
            }
            
            .logo {
                font-size: 26px;
            }
            
            .email-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <div class="logo">{{ config('app.name') }}</div>
                <p style="margin: 0; font-size: 16px; opacity: 0.9;">Secure Verification Service</p>
            </div>
            
            <div class="email-body">
                <div class="verification-icon">
                    🔐
                </div>
                
                <h1 class="email-title">{{ $type ?? 'Verification Code' }}</h1>
                
                <p class="email-subtitle">
                    Hello {{ $user->name ?? $user->username }},<br>
                    You have requested a verification code for your account. Please use the code below to complete your verification.
                </p>
                
                <div class="verification-code">{{ $code }}</div>
                
                <div class="info-section">
                    <h3 class="info-title">Verification Details:</h3>
                    <ul class="info-list">
                        <li>This code is valid for 10 minutes</li>
                        <li>Use this code only on our official website</li>
                        <li>Never share this code with anyone</li>
                        <li>Request Time: {{ now()->format('M d, Y h:i A') }}</li>
                    </ul>
                </div>
                
                <div class="security-note">
                    <h4>🚨 Security Notice</h4>
                    <p>If you did not request this verification code, please ignore this email and contact our support team immediately. Never share your verification codes with anyone.</p>
                </div>
            </div>
            
            <div class="email-footer">
                <p class="footer-text">
                    This is an automated message from {{ config('app.name') }}. Please do not reply to this email.
                </p>
                
                <div class="contact-info">
                    <strong>Need Help?</strong><br>
                    Contact our support team if you have any questions.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
