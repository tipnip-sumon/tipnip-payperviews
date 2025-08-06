<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Clear Error - {{ $domain }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            max-width: 600px;
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        .domain-badge {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 1rem 0;
        }
        .btn-custom {
            background: linear-gradient(45deg, #4ECDC4, #44A08D);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            margin: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        .btn-retry {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
        }
        .timestamp {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 1rem;
            margin: 1.5rem 0;
            border-radius: 0 8px 8px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="h2 mb-3">⚠️ Cache Clear Failed</h1>
        
        <p class="lead">Unable to clear browser cache for:</p>
        <div class="domain-badge">{{ $domain }}</div>
        
        <div class="error-details">
            <strong>Error Details:</strong><br>
            {{ $error }}
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Alternative Solutions:</strong>
            <ul class="mt-2 text-start">
                <li>Manually clear your browser cache (Ctrl+Shift+Delete)</li>
                <li>Try opening the site in an incognito/private window</li>
                <li>Refresh the page with Ctrl+F5 (hard refresh)</li>
                <li>Check if your browser supports the Clear-Site-Data header</li>
            </ul>
        </div>
        
        <div class="mt-4">
            <a href="{{ url('/browser_cache_clear/only_this_domain') }}" class="btn-custom btn-retry">
                <i class="fas fa-redo me-2"></i>Try Again
            </a>
            <a href="{{ url('/') }}" class="btn-custom">
                <i class="fas fa-home me-2"></i>Return to Homepage
            </a>
            <button onclick="manualCacheClear()" class="btn-custom">
                <i class="fas fa-tools me-2"></i>Manual Clear
            </button>
        </div>
        
        <div class="timestamp">
            <i class="fas fa-clock me-1"></i>
            Error occurred at: {{ $timestamp }}
        </div>
    </div>

    <script>
        function manualCacheClear() {
            try {
                // Try to clear what we can manually
                if (typeof(Storage) !== "undefined") {
                    localStorage.clear();
                    sessionStorage.clear();
                    console.log('Manual storage clear completed');
                }
                
                if ('caches' in window) {
                    caches.keys().then(names => {
                        names.forEach(name => caches.delete(name));
                        console.log('Manual cache API clear completed');
                    });
                }
                
                alert('Manual cache clearing attempted. Please refresh the page.');
                location.reload(true);
                
            } catch (error) {
                console.error('Manual cache clear failed:', error);
                alert('Manual cache clear failed. Please try clearing your browser cache manually (Ctrl+Shift+Delete).');
            }
        }
        
        // Show browser-specific instructions
        document.addEventListener('DOMContentLoaded', function() {
            const userAgent = navigator.userAgent;
            let browserInstructions = '';
            
            if (userAgent.includes('Chrome')) {
                browserInstructions = 'Chrome: Press Ctrl+Shift+Delete → Select "Cached images and files" → Clear data';
            } else if (userAgent.includes('Firefox')) {
                browserInstructions = 'Firefox: Press Ctrl+Shift+Delete → Select "Cache" → Clear Now';
            } else if (userAgent.includes('Safari')) {
                browserInstructions = 'Safari: Develop menu → Empty Caches (or Cmd+Option+E)';
            } else if (userAgent.includes('Edge')) {
                browserInstructions = 'Edge: Press Ctrl+Shift+Delete → Select "Cached images and files" → Clear';
            }
            
            if (browserInstructions) {
                const instructionDiv = document.createElement('div');
                instructionDiv.className = 'alert alert-warning mt-3';
                instructionDiv.innerHTML = `<i class="fas fa-browser me-2"></i><strong>Browser-specific instructions:</strong><br>${browserInstructions}`;
                document.querySelector('.error-card').appendChild(instructionDiv);
            }
        });
    </script>
</body>
</html>
