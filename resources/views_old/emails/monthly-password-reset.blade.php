<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Password Update</title>
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
            color: #dc3545;
            margin-bottom: 10px;
        }
        .security-alert {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .password-box {
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
            border: 3px solid #fd79a8;
        }
        .password-text {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 2px dashed #fd79a8;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(253, 121, 168, 0.4);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(253, 121, 168, 0.6);
        }
        .security-tips {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .tip-item {
            display: flex;
            align-items: flex-start;
            margin: 10px 0;
        }
        .tip-icon {
            color: #28a745;
            margin-right: 10px;
            font-size: 16px;
            margin-top: 2px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .cta-button { display: block; width: 100%; }
            .password-text { font-size: 18px; letter-spacing: 1px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $settings->site_name ?? 'Platform' }}</div>
            <h1 style="color: #dc3545; margin: 0;">🔒 Monthly Password Update</h1>
        </div>

        <div class="security-alert">
            <h2 style="margin: 0 0 10px 0;">🛡️ Security Notice</h2>
            <p style="margin: 0; font-weight: 500;">Your password has been automatically updated as part of our monthly security protocol.</p>
        </div>

        <p>Hi <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>,</p>

        <p>As part of our enhanced security measures, we automatically update user passwords monthly to protect your account from unauthorized access.</p>

        <div class="password-box">
            <h3 style="margin: 0 0 15px 0;">🔑 Your New Password</h3>
            <div class="password-text">{{ $newPassword }}</div>
            <p style="margin: 15px 0 0 0; font-size: 14px; opacity: 0.9;">
                ⚠️ Please save this password securely and change it after login
            </p>
        </div>

        <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>📋 Important Information:</strong><br><br>
            <strong>Password Reset Date:</strong> {{ $resetDate }}<br>
            <strong>Account:</strong> {{ $user->username }} ({{ $user->email }})<br>
            <strong>Next Reset:</strong> {{ now()->addMonth()->format('F j, Y') }}
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $loginUrl }}" class="cta-button">🚀 Login with New Password</a>
        </div>

        <div class="warning-box">
            <strong>🔒 Security Requirement:</strong><br>
            You will be required to change this password on your next login for additional security.
        </div>

        <div class="security-tips">
            <h4 style="color: #155724; margin-top: 0;">🛡️ Password Security Tips:</h4>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Change your password immediately after logging in</span>
            </div>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Use a combination of uppercase, lowercase, numbers, and symbols</span>
            </div>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Make your password at least 12 characters long</span>
            </div>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Don't use the same password for multiple accounts</span>
            </div>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Consider using a password manager</span>
            </div>
            
            <div class="tip-item">
                <span class="tip-icon">✅</span>
                <span>Never share your password with anyone</span>
            </div>
        </div>

        <div style="background: #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #fdcb6e;">
            <strong>🚨 Security Alert:</strong><br>
            If you did not expect this password reset or believe your account may be compromised, please contact our security team immediately.
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $changePasswordUrl }}" style="color: #007bff; text-decoration: none; font-weight: bold;">
                🔧 Change Password After Login
            </a>
        </div>

        <p style="margin: 30px 0;">This automated security measure helps protect your account and ensures the highest level of security for all our users.</p>

        <p>Best regards,<br>
        <strong>{{ $settings->site_name ?? 'Platform' }} Security Team</strong></p>

        <div class="footer">
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p style="margin: 5px 0;">For security concerns, contact: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
            <p style="margin: 10px 0;">{{ $settings->site_name ?? 'Platform' }} © {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
