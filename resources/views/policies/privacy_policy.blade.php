<x-smart_layout>
@section('top_title', 'Privacy Policy')

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
        max-width: 800px;
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
                <h1 class="stunning-policy-title">{{ $title }}</h1>
                <p class="stunning-policy-subtitle">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="stunning-policy-content">
                <h2>1. Information We Collect</h2>
                <p>We collect information you provide directly to us, such as when you create an account, make a transaction, or contact us for support.</p>

                <h3>Personal Information</h3>
                <ul>
                    <li>Name and contact information (email address, phone number)</li>
                    <li>Username and password</li>
                    <li>Payment and billing information</li>
                    <li>Communications with us</li>
                </ul>

                <h3>Automatically Collected Information</h3>
                <ul>
                    <li>Device information (IP address, browser type, operating system)</li>
                    <li>Usage information (pages visited, time spent, features used)</li>
                    <li>Location information (if you enable location services)</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve our services</li>
                    <li>Process transactions and send related information</li>
                    <li>Send technical notices, updates, security alerts, and support messages</li>
                    <li>Respond to your comments, questions, and customer service requests</li>
                    <li>Communicate with you about products, services, offers, and events</li>
                    <li>Monitor and analyze trends, usage, and activities</li>
                    <li>Detect, investigate, and prevent fraudulent transactions and other illegal activities</li>
                </ul>

                <h2>3. Information Sharing</h2>
                <p>We do not sell, trade, or otherwise transfer your personal information to third parties except as described in this privacy policy:</p>
                
                <h3>Service Providers</h3>
                <p>We may share your information with third-party service providers who perform services on our behalf, such as payment processing, data analysis, email delivery, and customer service.</p>

                <h3>Legal Requirements</h3>
                <p>We may disclose your information if required to do so by law or if we believe that such action is necessary to comply with legal obligations or protect our rights.</p>

                <h2>4. Data Security</h2>
                <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                <h2>5. Data Retention</h2>
                <p>We retain your personal information for as long as necessary to provide our services, comply with legal obligations, resolve disputes, and enforce our agreements.</p>

                <h2>6. Your Rights</h2>
                <p>Depending on your location, you may have certain rights regarding your personal information:</p>
                <ul>
                    <li>Access: You can request access to your personal information</li>
                    <li>Correction: You can request correction of inaccurate information</li>
                    <li>Deletion: You can request deletion of your personal information</li>
                    <li>Portability: You can request a copy of your data in a portable format</li>
                    <li>Objection: You can object to certain processing of your information</li>
                </ul>

                <h2>7. Cookies and Tracking</h2>
                <p>We use cookies and similar tracking technologies to collect information about your browsing activities and to improve our services. You can control cookies through your browser settings.</p>

                <h2>8. Third-Party Links</h2>
                <p>Our service may contain links to third-party websites. We are not responsible for the privacy practices of these external sites and encourage you to read their privacy policies.</p>

                <h2>9. Children's Privacy</h2>
                <p>Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13.</p>

                <h2>10. International Data Transfers</h2>
                <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place for such transfers.</p>

                <h2>11. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>

                <h2>12. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy or our privacy practices, please contact us through our support channels.</p>

                <a href="{{ route('register') }}" class="stunning-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Registration
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
</x-smart_layout>
