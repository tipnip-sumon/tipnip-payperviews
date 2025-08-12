<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page Not Found - PayPerViews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        .error-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }
        .attempted-url {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            color: #e74c3c;
            margin: 20px 0;
            word-break: break-all;
        }
        .suggestions {
            text-align: left;
            margin: 30px 0;
        }
        .suggestion-item {
            display: block;
            padding: 12px 20px;
            margin: 8px 0;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
        }
        .suggestion-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
            text-decoration: none;
            color: #495057;
        }
        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        .security-note {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="error-title">Admin Page Not Found</h1>
            <p class="error-subtitle">{{ $message ?? 'The admin page you are looking for does not exist.' }}</p>
            
            @if(isset($attempted_url))
            <div class="attempted-url">
                <strong>Attempted URL:</strong> {{ $attempted_url }}
            </div>
            @endif
            
            @if(isset($suggestions) && count($suggestions) > 0)
            <div class="suggestions">
                <h5><i class="fas fa-lightbulb text-warning"></i> Try these admin pages instead:</h5>
                @foreach($suggestions as $suggestion)
                <a href="{{ $suggestion['url'] }}" class="suggestion-item">
                    <i class="fas fa-arrow-right me-2"></i> {{ $suggestion['text'] }}
                </a>
                @endforeach
            </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('admin.dashboard') }}" class="back-btn">
                    <i class="fas fa-home me-2"></i> Admin Dashboard
                </a>
                <a href="javascript:history.back()" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i> Go Back
                </a>
            </div>
            
            <div class="security-note">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Security Notice:</strong> This attempt has been logged for security purposes. 
                Only access authorized admin pages through the proper navigation menu.
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
