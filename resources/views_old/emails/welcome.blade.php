<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .welcome-message {
            font-size: 18px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 30px;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        
        .user-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        
        .user-info p {
            margin: 5px 0;
            color: #666;
        }
        
        .cta-button {
            display: inline-block;
            padding: 16px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .features-list li {
            padding: 8px 0;
            padding-left: 24px;
            position: relative;
            color: #555;
        }
        
        .features-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 20px;
                width: auto;
            }
            
            .email-header, .email-body, .email-footer {
                padding: 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p>Your account has been successfully created</p>
        </div>
        
        <div class="email-body">
            <div class="welcome-message">
                <p>Hello <strong>{{ $user->username }}</strong>,</p>
                <p>Welcome to {{ config('app.name') }}! We're excited to have you join our community. Your account has been successfully created and you're now ready to start your journey with us.</p>
            </div>
            
            <div class="user-info">
                <h3>Your Account Details:</h3>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Registration Date:</strong> {{ $user->created_at ? $user->created_at->format('F d, Y') : date('F d, Y') }}</p>
                @if($user->ref_by)
                    <p><strong>Sponsor:</strong> {{ App\Models\User::find($user->ref_by)->username ?? 'N/A' }}</p>
                @endif
            </div>
            
            <p>Here's what you can do with your new account:</p>
            
            <ul class="features-list">
                <li>Access your personalized dashboard</li>
                <li>Track your earnings and transactions</li>
                <li>View videos and earn rewards</li>
                <li>Refer friends and earn bonuses</li>
                <li>Manage your profile and settings</li>
                <li>Get support from our team</li>
            </ul>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('verification.notice') }}" class="cta-button">
                    Verify Your Email & Get Started
                </a>
            </div>
            
            <div style="background: #e3f2fd; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px 0; color: #1565c0;">📧 Important: Verify Your Email</h3>
                <p style="margin: 0; color: #1976d2;">
                    Please verify your email address to activate all features of your account. Check your inbox for the verification email.
                </p>
            </div>
            
            <p>If you have any questions or need assistance, don't hesitate to contact our support team. We're here to help you succeed!</p>
            
            <p>Best regards,<br>
            <strong>The {{ config('app.name') }} Team</strong></p>
        </div>
        
        <div class="email-footer">
            <p>Thank you for choosing {{ config('app.name') }}</p>
            
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Twitter</a>
                <a href="#">Instagram</a>
                <a href="#">Support</a>
            </div>
            
            <p>
                This email was sent to {{ $user->email }}<br>
                If you didn't create this account, please ignore this email.
            </p>
            
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
