<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Verification Required</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .warning-box {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            border-left: 4px solid #e17055;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .warning-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 15px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        .benefits {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .benefit-icon {
            color: #28a745;
            margin-right: 10px;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .urgent {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .cta-button { display: block; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $settings->site_name ?? 'Platform' }}</div>
            <h1 style="color: #343a40; margin: 0;">📋 KYC Verification Required</h1>
        </div>

        <div class="warning-box">
            <div class="warning-icon">⚠️</div>
            <h2 style="margin: 0 0 10px 0; color: #d63031;">Action Required</h2>
            <p style="margin: 0; font-weight: 500;">Your account requires KYC (Know Your Customer) verification to unlock all platform features.</p>
        </div>

        <p>Hi <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>,</p>

        <p>We noticed that your KYC verification is still pending. To ensure the security of our platform and comply with regulations, we require all users to complete their identity verification.</p>

        <div class="urgent">
            🚨 Without KYC verification, your account access may be limited
        </div>

        <div class="benefits">
            <h3 style="color: #495057; margin-top: 0;">✅ Benefits of KYC Verification:</h3>
            
            <div class="benefit-item">
                <span class="benefit-icon">🔓</span>
                <span>Unrestricted access to all platform features</span>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">💰</span>
                <span>Higher transaction and withdrawal limits</span>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">🛡️</span>
                <span>Enhanced account security and protection</span>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">⚡</span>
                <span>Faster processing of withdrawals and transfers</span>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">🎯</span>
                <span>Access to exclusive investment opportunities</span>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $kycUrl }}" class="cta-button">🚀 Complete KYC Verification Now</a>
        </div>

        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #2196f3;">
            <h4 style="color: #1976d2; margin-top: 0;">📋 What You'll Need:</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Government-issued photo ID (passport, driver's license, or national ID)</li>
                <li>Proof of address (utility bill, bank statement, or official document)</li>
                <li>Clear photos or scans of your documents</li>
                <li>5-10 minutes of your time</li>
            </ul>
        </div>

        <p style="margin: 30px 0 20px 0;"><strong>Account Details:</strong></p>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace;">
            <strong>Username:</strong> {{ $user->username }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y') }}
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>⏰ Need Help?</strong><br>
            If you're experiencing any issues with the KYC process, our support team is here to help:
            <br><br>
            📧 Email: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a><br>
            🔗 Login: <a href="{{ $loginUrl }}">Access Your Account</a>
        </div>

        <p style="margin: 30px 0;">Thank you for your cooperation in helping us maintain a secure platform for all users.</p>

        <p>Best regards,<br>
        <strong>{{ $settings->site_name ?? 'Platform' }} Security Team</strong></p>

        <div class="footer">
            <p>This is an automated security reminder. Please do not reply to this email.</p>
            <p style="margin: 5px 0;">If you believe this email was sent in error, please contact our support team.</p>
            <p style="margin: 10px 0;">{{ $settings->site_name ?? 'Platform' }} © {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
