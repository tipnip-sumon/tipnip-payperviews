<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations on Your First Investment!</title>
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
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .celebration-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
            position: relative;
            z-index: 2;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .celebration-title {
            font-size: 32px;
            margin: 80px 0 30px 0;
            text-align: center;
            color: #2d3436;
        }
        .congratulation-box {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        .congratulation-box::before {
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
                rgba(255,255,255,0.1) 10px,
                rgba(255,255,255,0.1) 20px
            );
            animation: shine 3s linear infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        .investment-details {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            border-left: 5px solid #00b894;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            font-weight: bold;
            color: #2d3436;
        }
        .amount-highlight {
            font-size: 24px;
            color: #00b894;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
            padding: 18px 35px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            margin: 15px 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.4);
            transition: all 0.3s ease;
            border: none;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.6);
        }
        .cta-secondary {
            background: linear-gradient(135deg, #00cec9 0%, #55a3ff 100%);
            box-shadow: 0 4px 15px rgba(0, 206, 201, 0.4);
        }
        .benefits {
            background: linear-gradient(135deg, #e17055 0%, #fd79a8 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        .benefit-icon {
            margin-right: 15px;
            font-size: 24px;
            width: 40px;
            text-align: center;
        }
        .milestone-box {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 25px 0;
        }
        .next-steps {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .step-number {
            background: #2196f3;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .social-proof {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
            text-align: center;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { padding: 25px; }
            .cta-button { display: block; width: 100%; margin: 10px 0; }
            .celebration-title { font-size: 24px; }
            .detail-row { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="celebration-bg"></div>
        
        <div class="header">
            <div class="logo">{{ $settings->site_name ?? 'Investment Platform' }}</div>
        </div>

        <div class="celebration-title">🎉 Congratulations!</div>

        <div class="congratulation-box">
            <h2 style="margin: 0 0 15px 0; font-size: 28px;">🚀 Welcome to Your Investment Journey!</h2>
            <p style="margin: 0; font-size: 18px; opacity: 0.95;">
                You've just taken your first step towards financial growth and prosperity!
            </p>
        </div>

        <p style="font-size: 18px; color: #2d3436;">Hi <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>,</p>

        <p style="font-size: 16px;">We're thrilled to congratulate you on making your first investment with us! This is a significant milestone in your financial journey, and we're honored to be part of it.</p>

        <div class="investment-details">
            <h3 style="color: #00b894; margin-top: 0; text-align: center;">📊 Your Investment Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">💰 Investment Amount:</span>
                <span class="detail-value amount-highlight">${{ number_format($investment->amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📈 Investment Plan:</span>
                <span class="detail-value">{{ $planName }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">🎯 Expected Return:</span>
                <span class="detail-value">
                    {{ $planDetails['interest'] }}{{ $planDetails['return_type'] === 'percentage' ? '%' : '$' }}
                    @if($planDetails['time'] > 0)
                        per {{ $planDetails['time'] }} {{ $planDetails['time_name'] }}
                    @endif
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">📅 Investment Date:</span>
                <span class="detail-value">{{ $investmentDate }}</span>
            </div>
            
            @if($maturityDate !== 'Ongoing')
            <div class="detail-row">
                <span class="detail-label">⏰ Maturity Date:</span>
                <span class="detail-value">{{ $maturityDate }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">🆔 Investment ID:</span>
                <span class="detail-value">#{{ $investment->id }}</span>
            </div>
        </div>

        <div class="milestone-box">
            <h3 style="margin: 0 0 10px 0;">🏆 Achievement Unlocked!</h3>
            <p style="margin: 0; font-size: 16px;">
                <strong>First Investment Milestone</strong><br>
                You're now part of our investor community!
            </p>
        </div>

        <div class="benefits">
            <h3 style="margin-top: 0; text-align: center;">🌟 What This Means for You:</h3>
            
            <div class="benefit-item">
                <span class="benefit-icon">💹</span>
                <div>
                    <strong>Passive Income Generation</strong><br>
                    <small>Your money is now working for you 24/7</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">🔒</span>
                <div>
                    <strong>Secure & Reliable Returns</strong><br>
                    <small>Professional management with transparent tracking</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">📊</span>
                <div>
                    <strong>Real-time Monitoring</strong><br>
                    <small>Track your investment performance anytime</small>
                </div>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">🎁</span>
                <div>
                    <strong>Exclusive Investor Benefits</strong><br>
                    <small>Access to premium features and opportunities</small>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $dashboardUrl }}" class="cta-button">📊 View Dashboard</a>
            <a href="{{ $investmentUrl }}" class="cta-button cta-secondary">📈 Track Investment</a>
        </div>

        <div class="next-steps">
            <h4 style="color: #1976d2; margin-top: 0;">🗺️ Your Next Steps:</h4>
            
            <div class="step-item">
                <div class="step-number">1</div>
                <div>
                    <strong>Monitor Your Progress</strong><br>
                    <small>Check your dashboard regularly to track returns and performance</small>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-number">2</div>
                <div>
                    <strong>Consider Diversification</strong><br>
                    <small>Explore other investment plans to diversify your portfolio</small>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-number">3</div>
                <div>
                    <strong>Share the Opportunity</strong><br>
                    <small>Refer friends and family to earn additional rewards</small>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-number">4</div>
                <div>
                    <strong>Stay Informed</strong><br>
                    <small>Keep your profile updated and read our investment insights</small>
                </div>
            </div>
        </div>

        <div class="social-proof">
            <h4 style="color: #155724; margin-top: 0;">🤝 Join Thousands of Successful Investors</h4>
            <p style="margin: 10px 0; color: #155724;">
                You're now part of a growing community of smart investors who are building their financial future with us.
            </p>
            <small style="color: #155724; opacity: 0.8;">
                Welcome to the investor family! 🎉
            </small>
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 12px; margin: 25px 0;">
            <h4 style="color: #856404; margin-top: 0;">💡 Pro Tips for New Investors:</h4>
            <ul style="color: #856404; margin: 10px 0; padding-left: 20px;">
                <li>Regularly check your investment performance in the dashboard</li>
                <li>Consider reinvesting your returns for compound growth</li>
                <li>Complete your KYC verification for higher investment limits</li>
                <li>Enable notifications to stay updated on your investments</li>
                <li>Contact support if you have any questions or concerns</li>
            </ul>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin: 25px 0; font-family: monospace;">
            <h4 style="margin-top: 0; color: #495057;">📋 Quick Account Summary:</h4>
            <strong>Username:</strong> {{ $user->username }}<br>
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Investment Status:</strong> Active Investor 🌟<br>
            <strong>Total Investments:</strong> 1 (Your first one!) 🎉<br>
            <strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}
        </div>

        <div style="background: #e3f2fd; padding: 20px; border-radius: 12px; margin: 25px 0;">
            <h4 style="color: #1976d2; margin-top: 0;">🆘 Need Help?</h4>
            <p style="color: #1976d2; margin: 10px 0;">
                Our support team is here to assist you with any questions about your investment:
            </p>
            <div style="color: #1976d2;">
                📧 Email: <a href="mailto:{{ $supportEmail }}" style="color: #1976d2;">{{ $supportEmail }}</a><br>
                🔗 Dashboard: <a href="{{ $dashboardUrl }}" style="color: #1976d2;">Access Your Account</a><br>
                👤 Profile: <a href="{{ $profileUrl }}" style="color: #1976d2;">Update Your Information</a>
            </div>
        </div>

        <p style="margin: 30px 0; font-size: 16px;">Once again, congratulations on this important step! We're excited to help you achieve your financial goals and look forward to supporting your investment journey.</p>

        <p style="font-size: 16px;">Here's to your financial success! 🥂</p>

        <p>Warm regards,<br>
        <strong>{{ $settings->site_name ?? 'Platform' }} Investment Team</strong></p>

        <div class="footer">
            <p>🎉 This is a special congratulation email for your first investment milestone!</p>
            <p style="margin: 5px 0;">Track your investment progress anytime in your dashboard.</p>
            <p style="margin: 10px 0;">{{ $settings->site_name ?? 'Platform' }} © {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
