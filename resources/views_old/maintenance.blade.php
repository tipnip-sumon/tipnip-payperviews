<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $site_name ?? 'ViewCash' }} - Maintenance Mode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .maintenance-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .maintenance-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .feature-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .feature-description {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .back-soon {
            margin-top: 2rem;
            font-size: 1rem;
            opacity: 0.7;
        }
        
        .social-links {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 50%;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .social-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 1rem;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-message {
                font-size: 1rem;
            }
            
            .maintenance-features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        <h1 class="maintenance-title">{{ $site_name ?? 'ViewCash' }}</h1>
        <p class="maintenance-message">
            {{ $message ?? 'We are currently performing scheduled maintenance to improve your experience. Please check back soon.' }}
        </p>
        
        <div class="maintenance-features">
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">Earn Money</h3>
                <p class="feature-description">Watch videos and earn real money</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎥</div>
                <h3 class="feature-title">Quality Content</h3>
                <p class="feature-description">Enjoy high-quality video content</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3 class="feature-title">Fast Payouts</h3>
                <p class="feature-description">Quick and secure payments</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3 class="feature-title">Referral System</h3>
                <p class="feature-description">Earn from your referrals</p>
            </div>
        </div>
        
        <div class="back-soon">
            <p>⏰ We'll be back online shortly. Thank you for your patience!</p>
        </div>
        
        <div class="social-links">
            <a href="#" class="social-link" title="Facebook">📘</a>
            <a href="#" class="social-link" title="Twitter">🐦</a>
            <a href="#" class="social-link" title="Instagram">📷</a>
            <a href="#" class="social-link" title="YouTube">📺</a>
        </div>
    </div>
    
    <script>
        // Auto-refresh page every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
        
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const featureCards = document.querySelectorAll('.feature-card');
            
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>
