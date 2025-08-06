<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Cleared Successfully - {{ $domain }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cache-card {
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
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1.5rem;
            animation: bounce 1s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        .domain-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 1rem 0;
        }
        .feature-list {
            text-align: left;
            max-width: 400px;
            margin: 2rem auto;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            animation: fadeInLeft 0.6s ease-out;
            animation-fill-mode: both;
        }
        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }
        .feature-item:nth-child(4) { animation-delay: 0.4s; }
        .feature-item:nth-child(5) { animation-delay: 0.5s; }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .btn-custom {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
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
        .timestamp {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="cache-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="h2 mb-3">🧹 Cache Cleared Successfully!</h1>
        
        <p class="lead">Browser cache has been completely cleared for:</p>
        <div class="domain-badge">{{ $fullDomain }}</div>
        
        <div class="feature-list">
            <div class="feature-item">
                <i class="fas fa-check text-success me-3"></i>
                <span>Browser cache cleared</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check text-success me-3"></i>
                <span>Local storage cleaned</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check text-success me-3"></i>
                <span>Session storage cleared</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check text-success me-3"></i>
                <span>Cookies removed (domain-specific)</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check text-success me-3"></i>
                <span>Service worker cache cleared</span>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn-custom">
                <i class="fas fa-home me-2"></i>Return to Homepage
            </a>
            <button onclick="location.reload()" class="btn-custom">
                <i class="fas fa-redo me-2"></i>Refresh Page
            </button>
        </div>
        
        <div class="timestamp">
            <i class="fas fa-clock me-1"></i>
            Cache cleared at: {{ $timestamp }}
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                {{ $message }}
            </small>
        </div>
    </div>

    <script>
        // Additional JavaScript cache clearing
        document.addEventListener('DOMContentLoaded', function() {
            // Clear localStorage
            try {
                localStorage.clear();
                console.log('✅ localStorage cleared');
            } catch(e) {
                console.warn('localStorage clear failed:', e);
            }
            
            // Clear sessionStorage
            try {
                sessionStorage.clear();
                console.log('✅ sessionStorage cleared');
            } catch(e) {
                console.warn('sessionStorage clear failed:', e);
            }
            
            // Clear IndexedDB
            if (window.indexedDB) {
                indexedDB.databases().then(databases => {
                    databases.forEach(db => {
                        indexedDB.deleteDatabase(db.name);
                    });
                    console.log('✅ IndexedDB clearing initiated');
                }).catch(e => console.warn('IndexedDB clear failed:', e));
            }
            
            // Clear Service Worker cache
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(registrations => {
                    registrations.forEach(registration => {
                        registration.unregister();
                    });
                    console.log('✅ Service Worker cache cleared');
                }).catch(e => console.warn('Service Worker clear failed:', e));
            }
            
            // Clear Cache API
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                    console.log('✅ Cache API cleared');
                }).catch(e => console.warn('Cache API clear failed:', e));
            }
            
            console.log('🧹 Complete cache clearing process executed for {{ $domain }}');
        });
        
        // Prevent caching of this page
        window.addEventListener('beforeunload', function() {
            // Final cleanup
            if (window.caches) {
                caches.keys().then(names => names.forEach(name => caches.delete(name)));
            }
        });
    </script>
</body>
</html>
