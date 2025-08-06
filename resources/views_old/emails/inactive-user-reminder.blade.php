<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ready to Start Investing?</title>
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
            color: #28a745;
            margin-bottom: 10px;
        }
        .opportunity-box {
            background: linear-gradient(135deg, #00cec9 0%, #55a3ff 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
        }
        .stats-box {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 184, 148, 0.6);
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
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .benefit-icon {
            color: #28a745;
            margin-right: 15px;
            font-size: 20px;
            width: 30px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .missed-opportunity {
            background: linear-gradient(135deg, #ff7675 0%, #fd79a8 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .action-card {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #2196f3;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .cta-button { display: block; width: 100%; }
            .quick-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $settings->site_name ?? 'Platform' }}</div>
            <h1 style="color: #28a745; margin: 0;">💰 Ready to Start Investing?</h1>
        </div>

        <div class="opportunity-box">
            <h2 style="margin: 0 0 15px 0;">🚀 Your Investment Journey Awaits!</h2>
            <p style="margin: 0; font-size: 18px; opacity: 0.95;">You have funds ready to work for you. Don't let them sit idle!</p>
        </div>

        <p>Hi <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>,</p>

        <p>We noticed that you have been away from our platform for 
        @if(is_numeric($daysSinceLastLogin))
            <strong>{{ $daysSinceLastLogin }} days</strong>
        @else
            <strong>quite some time</strong>
        @endif
        . Your account has funds available, but they're not currently generating returns through our investment plans.</p>

        <div class="stats-box">
            <h3 style="margin: 0 0 10px 0;">⏰ Time is Money!</h3>
            <p style="margin: 0; font-size: 16px;">Every day your funds remain uninvested is a missed opportunity for growth.</p>
        </div>

        <div class="benefits">
            <h3 style="color: #495057; margin-top: 0; text-align: center;">🎯 Why Our Users Love Our Investment Plans:</h3>
            
            <div class="benefit-item">
                <span class="benefit-icon">📈</span>
                <div>
                    <strong>Competitive Returns</strong><br>
                    <small>Earn attractive returns on your investment with our carefully designed plans</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">🔒</span>
                <div>
                    <strong>Secure & Reliable</strong><br>
                    <small>Your investments are protected with bank-level security measures</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">⚡</span>
                <div>
                    <strong>Instant Activation</strong><br>
                    <small>Start earning returns immediately after investment</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">📊</span>
                <div>
                    <strong>Flexible Plans</strong><br>
                    <small>Choose from multiple investment plans that suit your risk appetite</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">💎</span>
                <div>
                    <strong>Compound Growth</strong><br>
                    <small>Watch your money grow exponentially with our compound interest plans</small>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $investUrl }}" class="cta-button">🚀 Explore Investment Plans</a>
        </div>

        <div class="missed-opportunity">
            <h3 style="margin: 0 0 10px 0;">⚠️ Don't Miss Out!</h3>
            <p style="margin: 0;">The longer you wait, the more potential returns you're missing. Start investing today!</p>
        </div>

        <div class="quick-actions">
            <div class="action-card">
                <h4 style="margin: 0 0 5px 0; color: #1976d2;">👀 View Plans</h4>
                <p style="margin: 0; font-size: 14px;">Explore all available investment options</p>
            </div>
            <div class="action-card">
                <h4 style="margin: 0 0 5px 0; color: #1976d2;">📊 Dashboard</h4>
                <p style="margin: 0; font-size: 14px;">Check your account balance and history</p>
            </div>
        </div>

        <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;">
            <h4 style="color: #155724; margin-top: 0;">💡 Getting Started is Easy:</h4>
            <ol style="margin: 10px 0; padding-left: 20px; color: #155724;">
                <li>Log in to your account</li>
                <li>Browse our investment plans</li>
                <li>Choose a plan that fits your goals</li>
                <li>Invest and start earning returns immediately</li>
            </ol>
        </div>

        <p style="margin: 30px 0 20px 0;"><strong>Account Summary:</strong></p>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace;">
            <strong>Username:</strong> {{ $user->username }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Last Login:</strong> 
            @if($user->last_login_at)
                {{ $user->last_login_at->format('F j, Y \a\t g:i A') }}
            @else
                Not recorded
            @endif
            <br>
            <strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>🤝 Need Assistance?</strong><br>
            Our support team is ready to help you choose the best investment plan for your goals:
            <br><br>
            📧 Email: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a><br>
            🔗 Dashboard: <a href="{{ $dashboardUrl }}">Access Your Account</a><br>
            🔐 Login: <a href="{{ $loginUrl }}">Sign In Now</a>
        </div>

        <p style="margin: 30px 0;">Don't let your funds sit idle any longer. Start your investment journey today and watch your money grow!</p>

        <p>Best regards,<br>
        <strong>{{ $settings->site_name ?? 'Platform' }} Investment Team</strong></p>

        <div class="footer">
            <p>This is an automated reminder to help you maximize your investment potential.</p>
            <p style="margin: 5px 0;">You can update your email preferences in your account settings.</p>
            <p style="margin: 10px 0;">{{ $settings->site_name ?? 'Platform' }} © {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
