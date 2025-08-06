{{-- filepath: d:\mainur_sir-2\resources\views\auth\verify.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Verify Email' }} - {{ config('app.name') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .verification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .verification-body {
            padding: 30px;
        }
        .email-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .user-email {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="verification-container">
                    <div class="verification-header">
                        <i class="fas fa-envelope-open email-icon"></i>
                        <h3 class="mb-0">Verify Your Email</h3>
                        <p class="mb-0 opacity-75">{{ config('app.name') }}</p>
                    </div>
                    
                    <div class="verification-body">
                        @if (session('success'))
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ session('info') }}
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <h5 class="mb-3">Check Your Email</h5>
                            <p class="text-muted">
                                We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.
                            </p>
                            
                            <div class="user-email">
                                <i class="fas fa-envelope me-2"></i>
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-verify">
                                <i class="fas fa-paper-plane me-2"></i>
                                Resend Verification Email
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted small">
                                Didn't receive the email? Check your spam folder or 
                                <a href="{{ route('verification.resend') }}" onclick="event.preventDefault(); document.querySelector('form').submit();">
                                    click here to resend
                                </a>
                            </p>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>
                            <a href="{{ route('logout') }}" class="btn btn-outline-danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </div>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide success messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-info');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
