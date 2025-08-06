<x-smart_layout>
@section('top_title', 'Terms of Service')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    .stunning-policy-page {
        min-height: 100vh;
        background: linear-gradient(135deg, 
            #667eea 0%, 
            #764ba2 25%, 
            #f093fb 50%, 
            #f5576c 75%, 
            #4facfe 100%);
        background-size: 400% 400%;
        animation: gradientShift 12s ease infinite;
        position: relative;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
        padding: 40px 20px;
    }

    .stunning-policy-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
        pointer-events: none;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .stunning-policy-container {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .stunning-policy-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 0 80px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stunning-policy-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .stunning-policy-title {
        color: white;
        font-size: 36px;
        font-weight: 700;
        margin: 0 0 16px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .stunning-policy-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 18px;
        margin: 0;
    }

    .stunning-policy-content {
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.8;
        font-size: 16px;
    }

    .stunning-policy-content h2 {
        color: white;
        font-size: 24px;
        font-weight: 600;
        margin: 30px 0 16px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .stunning-policy-content h3 {
        color: white;
        font-size: 20px;
        font-weight: 600;
        margin: 24px 0 12px 0;
    }

    .stunning-policy-content p {
        margin-bottom: 16px;
    }

    .stunning-policy-content ul, 
    .stunning-policy-content ol {
        padding-left: 24px;
        margin-bottom: 16px;
    }

    .stunning-policy-content li {
        margin-bottom: 8px;
    }

    .stunning-policy-content strong {
        color: white;
        font-weight: 600;
    }

    .highlight-box {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        backdrop-filter: blur(10px);
    }

    .warning-box {
        background: rgba(255, 193, 7, 0.2);
        border: 1px solid rgba(255, 193, 7, 0.5);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }

    .stunning-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: 30px;
    }

    .stunning-back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .stunning-policy-card {
            padding: 24px;
        }
        
        .stunning-policy-title {
            font-size: 28px;
        }
        
        .stunning-policy-content {
            font-size: 15px;
        }
    }
</style>

<div class="stunning-policy-page">
    <div class="stunning-policy-container">
        <div class="stunning-policy-card">
            <div class="stunning-policy-header">
                <h1 class="stunning-policy-title">🎰 {{ $title }}</h1>
                <p class="stunning-policy-subtitle">Gaming, Lottery & Investment Platform</p>
                <p class="stunning-policy-subtitle">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="stunning-policy-content">
                <div class="warning-box">
                    <h3><i class="fas fa-exclamation-triangle"></i> Important Notice</h3>
                    <p><strong>By accessing and using this platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. This is a legally binding agreement for our gaming, lottery, and investment services.</strong></p>
                </div>

                <h2>1. Acceptance of Terms & Eligibility</h2>
                <h3>1.1 Agreement</h3>
                <p>Welcome to {{ config('app.name', 'our platform') }}. These Terms of Service govern your use of our lottery, gaming, advertisement viewing, and investment platform services.</p>
                
                <h3>1.2 Age Requirements</h3>
                <ul>
                    <li><strong>Minimum Age:</strong> You must be at least 18 years old to use our services</li>
                    <li><strong>Legal Compliance:</strong> You must comply with all local gambling and gaming laws</li>
                    <li><strong>Verification:</strong> Age verification may be required at any time</li>
                </ul>

                <h2>2. Account Registration & Security</h2>
                <h3>2.1 Registration Requirements</h3>
                <ul>
                    <li>Provide accurate, complete, and current information</li>
                    <li>One account per person only</li>
                    <li>Valid email address required for verification</li>
                    <li>KYC (Know Your Customer) verification may be mandatory</li>
                </ul>

                <h3>2.2 Account Security</h3>
                <ul>
                    <li>You are responsible for maintaining password security</li>
                    <li>Notify us immediately of any unauthorized access</li>
                    <li>Do not share your account credentials with anyone</li>
                    <li>Enable two-factor authentication when available</li>
                </ul>

                <h2>3. 🎰 Lottery Services</h2>
                <h3>3.1 Lottery Participation</h3>
                <ul>
                    <li><strong>Ticket Purchase:</strong> All ticket purchases are final and non-refundable</li>
                    <li><strong>Random Draws:</strong> Winners determined by certified random number generation</li>
                    <li><strong>Prize Distribution:</strong> Prizes awarded according to published prize structure</li>
                    <li><strong>Draw Schedule:</strong> Draws conducted at scheduled times</li>
                </ul>

                <h3>3.2 Prize Claims & Payments</h3>
                <ul>
                    <li>Prizes must be claimed within the specified time period</li>
                    <li>Unclaimed prizes may be forfeited</li>
                    <li>Verification may be required for large prize claims</li>
                    <li>Taxes on winnings are the responsibility of the winner</li>
                </ul>

                <div class="highlight-box">
                    <h3><i class="fas fa-shield-alt"></i> Fair Play Guarantee</h3>
                    <p>All lottery draws use certified random number generation systems. Results are auditable and transparent to ensure fair play for all participants.</p>
                </div>

                <h2>4. 💰 Investment & Deposit Services</h2>
                <h3>4.1 Investment Plans</h3>
                <ul>
                    <li><strong>Risk Disclosure:</strong> All investments carry inherent risk of loss</li>
                    <li><strong>No Guarantees:</strong> Returns are not guaranteed and may vary</li>
                    <li><strong>Plan Terms:</strong> Each plan has specific terms and conditions</li>
                    <li><strong>Early Withdrawal:</strong> Fees may apply for early withdrawal</li>
                </ul>

                <h3>4.2 Financial Transactions</h3>
                <ul>
                    <li>Minimum and maximum limits apply to deposits and withdrawals</li>
                    <li>Processing times vary by payment method</li>
                    <li>Transaction fees may apply as disclosed</li>
                    <li>Anti-money laundering checks may be performed</li>
                </ul>

                <h2>5. 📺 Advertisement Viewing System</h2>
                <h3>5.1 Video Rewards</h3>
                <ul>
                    <li><strong>Completion Required:</strong> Watch entire videos to earn rewards</li>
                    <li><strong>Daily Limits:</strong> Maximum viewing limits may apply</li>
                    <li><strong>Fair Use:</strong> Automated viewing or fraud is prohibited</li>
                    <li><strong>Reward Changes:</strong> Reward amounts subject to change</li>
                </ul>

                <h2>6. 👥 Referral & Commission System</h2>
                <h3>6.1 Referral Program</h3>
                <ul>
                    <li>Earn commissions for referring active users</li>
                    <li>Multi-level commission structure available</li>
                    <li>Commissions paid according to schedule</li>
                    <li>Fraudulent referrals prohibited and may result in account closure</li>
                </ul>

                <h2>7. ❌ Prohibited Activities</h2>
                <p>The following activities are strictly forbidden:</p>
                <ul>
                    <li><strong>Multiple Accounts:</strong> Creating fake accounts or using multiple identities</li>
                    <li><strong>System Manipulation:</strong> Attempting to hack, exploit, or manipulate our systems</li>
                    <li><strong>Illegal Activities:</strong> Money laundering or other illegal financial activities</li>
                    <li><strong>Automated Tools:</strong> Using bots, scripts, or automated tools</li>
                    <li><strong>Account Sharing:</strong> Sharing credentials with third parties</li>
                    <li><strong>Fraud:</strong> Any fraudulent or deceptive practices</li>
                </ul>

                <h2>8. 🔒 Privacy & Data Protection</h2>
                <ul>
                    <li>Personal information handled according to our Privacy Policy</li>
                    <li>Industry-standard security measures implemented</li>
                    <li>Data may be shared with service providers as necessary</li>
                    <li>You have rights regarding your personal data</li>
                </ul>

                <h2>9. ⚠️ Disclaimers & Risk Warnings</h2>
                <h3>9.1 Service Availability</h3>
                <ul>
                    <li>Services provided "as is" without warranties</li>
                    <li>No guarantee of uninterrupted service</li>
                    <li>Maintenance may temporarily interrupt services</li>
                </ul>

                <h3>9.2 Financial Risk Warning</h3>
                <div class="warning-box">
                    <p><strong>⚠️ Important Risk Warning:</strong></p>
                    <ul>
                        <li>Never invest more than you can afford to lose</li>
                        <li>All gaming and investment activities carry financial risk</li>
                        <li>Past performance does not guarantee future results</li>
                        <li>Seek independent financial advice if needed</li>
                    </ul>
                </div>

                <h2>10. 🚫 Account Suspension & Termination</h2>
                <ul>
                    <li>We reserve the right to suspend accounts for violations</li>
                    <li>Termination may result in forfeiture of funds in certain cases</li>
                    <li>You may close your account subject to withdrawal of balance</li>
                    <li>Some obligations survive account termination</li>
                </ul>

                <h2>11. 📝 Modifications to Terms</h2>
                <ul>
                    <li>Terms may be updated periodically</li>
                    <li>Significant changes will be communicated</li>
                    <li>Continued use constitutes acceptance of changes</li>
                    <li>Review terms regularly for updates</li>
                </ul>

                <h2>12. ⚖️ Governing Law & Disputes</h2>
                <ul>
                    <li>Terms governed by applicable local laws</li>
                    <li>Disputes resolved through appropriate legal channels</li>
                    <li>Jurisdiction determined by applicable law</li>
                </ul>

                <div class="highlight-box">
                    <h3><i class="fas fa-headset"></i> Need Support?</h3>
                    <p>Questions about these terms? Contact our support team:</p>
                    <p><strong>Email:</strong> support@{{ str_replace(['http://', 'https://'], '', config('app.url', 'example.com')) }}</p>
                    <p><strong>Support Tickets:</strong> Available in your account dashboard</p>
                    
                    <div style="margin-top: 15px;">
                        <a href="{{ route('user.support.create') }}" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; color: white; text-decoration: none; margin-right: 10px;">
                            <i class="fas fa-plus"></i> Create Support Ticket
                        </a>
                        <a href="{{ route('policies', 'privacy-policy') }}" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; color: white; text-decoration: none;">
                            <i class="fas fa-shield-alt"></i> Privacy Policy
                        </a>
                    </div>
                </div>

                <p style="text-align: center; margin-top: 30px; color: rgba(255,255,255,0.7);">
                    <small>© {{ date('Y') }} {{ config('app.name', 'Gaming Platform') }}. All rights reserved.</small>
                </p>

                <a href="javascript:history.back()" class="stunning-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
</x-smart_layout>
