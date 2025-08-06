<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Maintenance</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }
        
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #6366f1;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        
        .message {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .contact {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }
        
        .contact a {
            color: #6366f1;
            text-decoration: none;
        }
        
        .contact a:hover {
            text-decoration: underline;
        }
        
        .status {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Under Maintenance</h1>
        <p>
            <span class="status"></span>
            We're currently performing maintenance
        </p>
        
        @if(isset($maintenanceData['message']) && $maintenanceData['message'])
            <div class="message">
                {{ $maintenanceData['message'] }}
            </div>
        @else
            <div class="message">
                We'll be back shortly. Thank you for your patience.
            </div>
        @endif
        
        <div class="contact">
            Need help? Contact us at 
            <a href="mailto:support@{{ request()->getHost() }}">support@{{ request()->getHost() }}</a>
        </div>
    </div>
    
    @if(isset($maintenanceData['refresh']) && $maintenanceData['refresh'])
        <script>
            setTimeout(() => window.location.reload(), {{ $maintenanceData['refresh'] * 1000 }});
        </script>
    @endif
</body>
</html>
