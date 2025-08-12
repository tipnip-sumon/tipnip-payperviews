<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Not Found - PayPerViews</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
<style>
    .error-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .error-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .error-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 50px 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        width: 100%;
        max-width: 600px;
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .error-icon {
        font-size: 80px;
        color: #667eea;
        margin-bottom: 30px;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .error-title {
        color: #333;
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .error-subtitle {
        color: #666;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .error-message {
        color: #777;
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .error-details {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 10px;
        padding: 15px;
        margin: 20px 0;
        text-align: left;
        font-size: 14px;
        color: #856404;
    }

    .error-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        text-decoration: none;
        color: white;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .btn-secondary {
        background: rgba(108, 117, 125, 0.1);
        border: 2px solid rgba(108, 117, 125, 0.3);
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 600;
        text-decoration: none;
        color: #6c757d;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: rgba(108, 117, 125, 0.2);
        border-color: rgba(108, 117, 125, 0.5);
        color: #495057;
        transform: translateY(-1px);
    }

    .url-info {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 8px;
        padding: 12px;
        margin: 20px 0;
        word-break: break-all;
        font-family: monospace;
        font-size: 13px;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .error-card {
            padding: 30px 20px;
            margin: 10px;
        }
        
        .error-title {
            font-size: 36px;
        }
        
        .error-subtitle {
            font-size: 20px;
        }
        
        .error-actions {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
</head>

<body>
<div class="error-container">
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="error-title">404</h1>
        <h2 class="error-subtitle">Page Not Found</h2>
        
        <p class="error-message">
            Sorry, the page you're looking for doesn't exist or you don't have permission to access it.
        </p>

        @if(request()->fullUrl())
        <div class="url-info">
            <strong>Attempted URL:</strong><br>
            {{ request()->fullUrl() }}
        </div>
        @endif

        <div class="error-details">
            <strong><i class="fas fa-info-circle"></i> What happened?</strong><br>
            • The URL may have been typed incorrectly<br>
            • The page may have been moved or deleted<br>
            • You may not have permission to access this resource<br>
            • URL manipulation was detected and blocked for security
        </div>

        <div class="error-actions">
            @auth
                <a href="{{ route('user.dashboard') }}" class="btn-primary">
                    <i class="fas fa-tachometer-alt"></i>
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
            @endauth
            
            <a href="{{ url('/') }}" class="btn-secondary">
                <i class="fas fa-home"></i>
                Home Page
            </a>
            
            <a href="javascript:history.back()" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e1e5e9; color: #999; font-size: 14px;">
            If you believe this is an error, please contact our support team.
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Log the 404 error for analytics
    console.log('404 Error occurred:', {
        url: window.location.href,
        referrer: document.referrer,
        timestamp: new Date().toISOString()
    });
    
    // Optional: Send error to analytics
    // You can add analytics tracking here
});
</script>

</body>
</html>
