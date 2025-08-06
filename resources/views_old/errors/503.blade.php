<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Under Maintenance</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .maintenance-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .maintenance-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .maintenance-subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .maintenance-message {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: left;
        }

        .maintenance-message h5 {
            color: #495057;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .maintenance-message p {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .countdown-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
            color: white;
        }

        .countdown-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 1rem;
            min-width: 80px;
            backdrop-filter: blur(5px);
        }

        .countdown-number {
            font-size: 1.8rem;
            font-weight: 600;
            display: block;
        }

        .countdown-label {
            font-size: 0.8rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .contact-info h6 {
            color: #495057;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .contact-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 25px;
            text-decoration: none;
            color: #667eea;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .contact-link:hover {
            color: white;
            background: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .progress-container {
            margin: 2rem 0;
        }

        .progress-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .progress {
            height: 8px;
            border-radius: 10px;
            background: rgba(102, 126, 234, 0.1);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            animation: progressAnimation 3s ease-in-out infinite;
        }

        @keyframes progressAnimation {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 0%; }
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
            75% { transform: translateY(-15px) rotate(270deg); }
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #dc3545;
            border-radius: 50%;
            margin-right: 8px;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        @media (max-width: 768px) {
            .maintenance-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-icon {
                font-size: 3rem;
            }
            
            .countdown-item {
                min-width: 60px;
                padding: 0.8rem;
            }
            
            .countdown-number {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="maintenance-container">
        <div class="maintenance-card">
            <!-- Status Indicator -->
            <div class="mb-3">
                <span class="status-indicator"></span>
                <small class="text-muted">System Status: Under Maintenance</small>
            </div>

            <!-- Main Icon -->
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>

            <!-- Title and Subtitle -->
            <h1 class="maintenance-title">We'll Be Right Back!</h1>
            <p class="maintenance-subtitle">
                Our website is currently undergoing scheduled maintenance to improve your experience.
            </p>

            <!-- Custom Message Section -->
            @if(isset($maintenanceData['message']) && $maintenanceData['message'])
                <div class="maintenance-message">
                    <h5><i class="fas fa-info-circle me-2"></i>Maintenance Notice</h5>
                    <p>{{ $maintenanceData['message'] }}</p>
                </div>
            @else
                <div class="maintenance-message">
                    <h5><i class="fas fa-cog me-2"></i>What's Happening?</h5>
                    <p>We're performing important updates to enhance security, improve performance, and add new features. This maintenance is essential for providing you with the best possible experience.</p>
                </div>
            @endif

            <!-- Countdown Timer (if retry time is set) -->
            @if(isset($maintenanceData['retry']) && $maintenanceData['retry'])
                <div class="countdown-container">
                    <div class="countdown-title">
                        <i class="fas fa-clock me-2"></i>Estimated completion time
                    </div>
                    <div class="countdown-timer" id="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">00</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">00</span>
                            <span class="countdown-label">Minutes</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">00</span>
                            <span class="countdown-label">Seconds</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-label">
                    <i class="fas fa-spinner me-2"></i>Maintenance in progress...
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar"></div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <h6><i class="fas fa-headset me-2"></i>Need Immediate Assistance?</h6>
                <div class="contact-links">
                    <a href="mailto:support@{{ request()->getHost() }}" class="contact-link">
                        <i class="fas fa-envelope"></i>
                        Email Support
                    </a>
                    <a href="tel:+1234567890" class="contact-link">
                        <i class="fas fa-phone"></i>
                        Call Us
                    </a>
                    <a href="#" onclick="window.open('https://twitter.com', '_blank')" class="contact-link">
                        <i class="fab fa-twitter"></i>
                        Updates
                    </a>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your data is safe and secure. We appreciate your patience.
                </small>
            </div>
        </div>
    </div>

    <!-- Auto-refresh script (if refresh time is set) -->
    @if(isset($maintenanceData['refresh']) && $maintenanceData['refresh'])
        <script>
            // Auto-refresh the page after specified seconds
            setTimeout(function() {
                window.location.reload();
            }, {{ $maintenanceData['refresh'] * 1000 }});
        </script>
    @endif

    <!-- Countdown Timer Script -->
    @if(isset($maintenanceData['retry']) && $maintenanceData['retry'])
        <script>
            // Set the countdown time (retry seconds from now)
            const countdownTime = new Date().getTime() + ({{ $maintenanceData['retry'] }} * 1000);

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = countdownTime - now;

                if (distance < 0) {
                    document.getElementById("countdown").innerHTML = "<div class='text-center'><i class='fas fa-check-circle text-success me-2'></i>Maintenance should be complete!</div>";
                    // Try to reload the page
                    setTimeout(() => window.location.reload(), 2000);
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
                document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
                document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');
            }

            // Update countdown every second
            setInterval(updateCountdown, 1000);
            updateCountdown(); // Initial call
        </script>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
