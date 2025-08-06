<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        /* Email-safe CSS */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background-color: #667eea;
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 30px 20px;
            line-height: 1.6;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #667eea;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666666;
            font-size: 14px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
        .notice {
            background-color: #e3f2fd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <!-- Fallback text for email clients that don't support HTML -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Welcome to {{ config('app.name') }}! Your account has been successfully created.
    </div>
    
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">Your account has been successfully created</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <p><strong>Hello {{ $user->username }},</strong></p>
            
            <p>Welcome to {{ config('app.name') }}! We're excited to have you join our community. Your account has been successfully created and you're now ready to start your journey with us.</p>
            
            <!-- User Information -->
            <div class="user-info">
                <h3 style="margin-top: 0; color: #333;">Your Account Details:</h3>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Registration Date:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                @if($user->ref_by)
                    <p><strong>Sponsor:</strong> {{ App\Models\User::find($user->ref_by)->username ?? 'N/A' }}</p>
                @endif
            </div>
            
            <p>Here's what you can do with your new account:</p>
            
            <ul>
                <li>Access your personalized dashboard</li>
                <li>Track your earnings and transactions</li>
                <li>View videos and earn rewards</li>
                <li>Refer friends and earn bonuses</li>
                <li>Manage your profile and settings</li>
                <li>Get support from our team</li>
            </ul>
            
            <!-- Call to Action -->
            <div style="text-align: center;">
                <a href="{{ route('verification.notice') }}" class="button">
                    Verify Your Email & Get Started
                </a>
            </div>
            
            <!-- Important Notice -->
            <div class="notice">
                <h4 style="margin-top: 0; color: #1565c0;">📧 Important: Verify Your Email</h4>
                <p style="margin-bottom: 0; color: #1976d2;">
                    Please verify your email address to activate all features of your account. Check your inbox for the verification email.
                </p>
            </div>
            
            <p>If you have any questions or need assistance, don't hesitate to contact our support team. We're here to help you succeed!</p>
            
            <p>Best regards,<br>
            <strong>The {{ config('app.name') }} Team</strong></p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing {{ config('app.name') }}</p>
            <p>This email was sent to {{ $user->email }}</p>
            <p>If you didn't create this account, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Tracking pixel for email open detection -->
    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="" style="display: block; height: 1px; width: 1px;">
</body>
</html>
