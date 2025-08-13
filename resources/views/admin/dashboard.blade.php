<x-layout>
    @section('top_title','Admin Dashboard')
    @section('title','Admin Dashboard')

    @push('style')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .stats-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stats-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
        .activity-item {
            padding: 0.75rem;
            border-left: 3px solid #e9ecef;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        .activity-item.success {
            border-left-color: #28a745;
        }
        .activity-item.warning {
            border-left-color: #ffc107;
        }
        .activity-item.danger {
            border-left-color: #dc3545;
        }
        .activity-item.info {
            border-left-color: #17a2b8;
        }
        .quick-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .table-modern {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .admin-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        .admin-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .admin-balance {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
        }
        .admin-role-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .last-login-info {
            background: rgba(255,255,255,0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        
        /* Performance Metrics Styles - ULTRA STRONG OVERRIDE */
        .performance-metrics-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            background-color: #667eea !important;
            border: none !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3) !important;
            position: relative !important;
        }
        
        .performance-metrics-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            z-index: 1;
        }
        
        .performance-metrics-banner .card-body {
            background: transparent !important;
            background-color: transparent !important;
            padding: 2rem !important;
            position: relative;
            z-index: 2;
        }
        
        .performance-metrics-banner * {
            background: none !important;
            background-color: transparent !important;
        }
        
        .metric-item {
            padding: 15px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.3) !important;
            background-color: rgba(255, 255, 255, 0.3) !important;
            border: 2px solid rgba(255, 255, 255, 0.5) !important;
            transition: all 0.3s ease !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            position: relative !important;
            z-index: 3 !important;
        }
        
        .metric-item:hover {
            background: rgba(255, 255, 255, 0.4) !important;
            background-color: rgba(255, 255, 255, 0.4) !important;
            transform: translateY(-5px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
        }
        
        .metric-item i {
            font-size: 1.5rem !important;
            margin-bottom: 8px !important;
            display: block !important;
            color: #000000 !important;
            text-shadow: none !important;
            filter: none !important;
            font-weight: 900 !important;
        }
        
        .metric-item h6 {
            font-size: 1.3rem !important;
            font-weight: 900 !important;
            margin: 6px 0 !important;
            color: #000000 !important;
            text-shadow: none !important;
            letter-spacing: 0.5px !important;
        }
        
        .metric-item small {
            font-size: 0.85rem !important;
            color: #000000 !important;
            text-shadow: none !important;
            font-weight: 700 !important;
        }
        
        /* Performance banner text visibility - BLACK ON WHITE */
        .performance-metrics-banner .text-white {
            color: #ffffff !important;
            text-shadow: 0 0 10px #000000, 0 0 20px #000000, 0 0 30px #000000 !important;
            font-weight: 900 !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        .performance-metrics-banner h5 {
            color: #ffffff !important;
            text-shadow: 0 0 10px #000000, 0 0 20px #000000, 0 0 30px #000000 !important;
            font-weight: 900 !important;
            letter-spacing: 1px !important;
            position: relative !important;
            z-index: 10 !important;
            background: none !important;
        }
        
        .performance-metrics-banner small {
            color: #ffffff !important;
            text-shadow: 0 0 8px #000000, 0 0 16px #000000 !important;
            font-weight: 700 !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        .performance-metrics-banner .fa-tachometer-alt {
            color: #ffffff !important;
            text-shadow: 0 0 15px #000000, 0 0 30px #000000 !important;
            font-weight: 900 !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        /* NUCLEAR OVERRIDE - Kill all white backgrounds */
        .card.performance-metrics-banner,
        .card.performance-metrics-banner *,
        .card.performance-metrics-banner .card-body,
        .card.performance-metrics-banner .row,
        .card.performance-metrics-banner .col-lg-8,
        .card.performance-metrics-banner .col-lg-4,
        .card.performance-metrics-banner .d-flex {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Force visible debugging */
        .performance-metrics-banner {
            border: 3px solid #ff0000 !important; /* Red border for debugging */
        }
        
        /* Animated performance indicators */
        @keyframes pulse-metric {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .metric-item.active {
            animation: pulse-metric 2s infinite;
        }
    </style>
    @endpush

    @section('content')
        @php
            // Get real-time statistics
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::where('status', 1)->count();
            $totalInvestments = \App\Models\Invest::sum('amount');
            $totalDeposits = \App\Models\Deposit::where('status', 1)->sum('amount');
            $totalWithdrawals = \App\Models\Withdrawal::where('status', 1)->sum('amount');
            $totalVideoViews = \App\Models\VideoView::count();
            $totalVideoEarnings = \App\Models\VideoView::sum('earned_amount');
            $totalVideoLinks = \App\Models\VideoLink::count();
            $activeVideoLinks = \App\Models\VideoLink::where('status', 1)->count();
            $pendingKyc = \App\Models\KycVerification::where('status', 'pending')->count();
            $todayUsers = \App\Models\User::whereDate('created_at', today())->count();
            $todayEarnings = \App\Models\VideoView::whereDate('viewed_at', today())->sum('earned_amount');
            $recentTransactions = \App\Models\Transaction::with('user')->latest()->limit(5)->get();
            $recentUsers = \App\Models\User::latest()->limit(5)->get();
        @endphp

        <!-- Welcome Header -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="quick-stats">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="{{ siteLogo() }}"
                                         class="rounded-circle"
                                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);"
                                         alt="Admin Logo"> 
                                </div>
                                <div>
                                    <h3 class="mb-1">Welcome back, {{ auth()->guard('admin')->user()->name ?? 'Admin' }}!</h3>
                                    <p class="mb-1 opacity-75">
                                        <i class="fas fa-shield-alt me-2"></i>{{ ucfirst(auth()->guard('admin')->user()->role ?? 'admin') }}
                                        @if(auth()->guard('admin')->user()->is_super_admin)
                                            <span class="badge bg-warning ms-2">Super Admin</span>
                                        @endif
                                    </p>
                                    <p class="mb-0 opacity-75">
                                        <i class="fas fa-clock me-2"></i>
                                        Last login: {{ auth()->guard('admin')->user()->last_login_at ? auth()->guard('admin')->user()->last_login_at->diffForHumans() : 'First time' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h4 mb-0">${{ number_format(auth()->guard('admin')->user()->balance ?? 0, 2) }}</div>
                                    <small class="opacity-75">Your Balance</small>
                                </div>
                                <div class="col-6">
                                    <div class="h4 mb-0">{{ $todayUsers }}</div>
                                    <small class="opacity-75">New Users Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <!-- Performance Metrics Banner -->
        @if(isset($performance_metrics))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card performance-metrics-banner border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tachometer-alt fa-3x me-3 text-white"></i>
                                    <div>
                                        <h5 class="mb-1 text-white fw-bold">System Performance Metrics</h5>
                                        <small class="text-white" style="opacity: 0.9;">Real-time admin dashboard performance and user activity monitoring</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row text-center text-white fw-bold">
                                    <div class="col-4">
                                        <div class="metric-item">
                                            <i class="fas fa-clock"></i>
                                            <h6 class="mb-0 text-white fw-bold">{{ $performance_metrics['loading_time'] }}ms</h6>
                                            <small class="text-white">Load Time</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-item">
                                            <i class="fas fa-database"></i>
                                            <h6 class="mb-0 text-white fw-bold">{{ $performance_metrics['query_count'] }}</h6>
                                            <small class="text-white">DB Queries</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="metric-item">
                                            <i class="fas fa-users"></i>
                                            <h6 class="mb-0 text-white fw-bold">{{ $performance_metrics['concurrent_users'] }}</h6>
                                            <small class="text-white">Online Users</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-white" style="opacity: 0.9; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);">
                                        <i class="fas fa-memory me-1"></i>Memory: {{ $performance_metrics['memory_usage'] }}MB
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-sync-alt me-1"></i>{{ $performance_metrics['load_timestamp'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Admin Personal Stats -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Admin Balance</span>
                                    <h3 class="mb-0 mt-1">${{ number_format(auth()->guard('admin')->user()->balance ?? 0, 2) }}</h3>
                                    <small class="opacity-75">Available Funds</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-wallet stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card success border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Total Users</span>
                                    <h3 class="mb-0 mt-1">{{ number_format($totalUsers) }}</h3>
                                    <small class="opacity-75">{{ $activeUsers }} Active</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-users stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card info border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Total Deposits</span>
                                    <h3 class="mb-0 mt-1">${{ number_format($totalDeposits, 2) }}</h3>
                                    <small class="opacity-75">All Time</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-arrow-down stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card warning border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Total Withdrawals</span>
                                    <h3 class="mb-0 mt-1">${{ number_format($totalWithdrawals, 2) }}</h3>
                                    <small class="opacity-75">Processed</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-arrow-up stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Activity & Financial Summary -->
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card table-modern border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">Admin Activity Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 border-end">
                                    <div class="h4 text-primary mb-1">${{ number_format(auth()->guard('admin')->user()->total_transferred ?? 0, 2) }}</div>
                                    <small class="text-muted">Total Transferred</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border-end">
                                    <div class="h4 text-success mb-1">{{ auth()->guard('admin')->user()->login_attempts ?? 0 }}</div>
                                    <small class="text-muted">Login Attempts Today</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <div class="h4 text-info mb-1">{{ $todayEarnings > 0 ? '$' . number_format($todayEarnings, 2) : 'N/A' }}</div>
                                    <small class="text-muted">Today's Platform Earnings</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-clock text-muted me-3"></i>
                                    <div>
                                        <strong>Last Login:</strong><br>
                                        <small class="text-muted">
                                            {{ auth()->guard('admin')->user()->last_login_at ? auth()->guard('admin')->user()->last_login_at->format('M d, Y h:i A') : 'First time login' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-globe text-muted me-3"></i>
                                    <div>
                                        <strong>Last IP:</strong><br>
                                        <small class="text-muted">{{ auth()->guard('admin')->user()->last_login_ip ?? 'Unknown' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-shield text-muted me-3"></i>
                                    <div>
                                        <strong>Role:</strong><br>
                                        <small class="text-muted">{{ ucfirst(auth()->guard('admin')->user()->role ?? 'admin') }}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-calendar text-muted me-3"></i>
                                    <div>
                                        <strong>Account Created:</strong><br>
                                        <small class="text-muted">{{ auth()->guard('admin')->user()->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card table-modern border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">Platform Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Total Investments</span>
                            <strong class="text-primary">${{ number_format($totalInvestments, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Video Views</span>
                            <strong class="text-success">{{ number_format($totalVideoViews) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Video Earnings</span>
                            <strong class="text-warning">${{ number_format($totalVideoEarnings, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Active Video Links</span>
                            <strong class="text-info">{{ $activeVideoLinks }} / {{ $totalVideoLinks }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Pending KYC</span>
                            <strong class="text-danger">{{ $pendingKyc }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Platform Health</span>
                            <span class="badge bg-success">Excellent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card danger border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Video Views</span>
                                    <h3 class="mb-0 mt-1">{{ number_format($totalVideoViews) }}</h3>
                                    <small class="opacity-75">${{ number_format($totalVideoEarnings, 2) }} Earned</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-play-circle stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);" border-0">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Today's Deposits</span>
                                    <h3 class="mb-0 mt-1">${{ number_format(\App\Models\Deposit::whereDate('created_at', today())->where('status', 1)->sum('amount'), 2) }}</h3>
                                    <small class="opacity-75">{{ \App\Models\Deposit::whereDate('created_at', today())->where('status', 1)->count() }} Transactions</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-calendar-day stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);" border-0">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold opacity-75">Today's Withdrawals</span>
                                    <h3 class="mb-0 mt-1">${{ number_format(\App\Models\Withdrawal::whereDate('created_at', today())->where('status', 1)->sum('amount'), 2) }}</h3>
                                    <small class="opacity-75">{{ \App\Models\Withdrawal::whereDate('created_at', today())->where('status', 1)->count() }} Transactions</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-end">
                                    <i class="fas fa-money-bill-wave stats-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="progress-circle bg-primary mx-auto mb-2">
                            <span>{{ $activeVideoLinks }}</span>
                        </div>
                        <h6 class="mb-1">Active Videos</h6>
                        <small class="text-muted">Out of {{ $totalVideoLinks }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="progress-circle bg-success mx-auto mb-2">
                            <span>${{ number_format($totalWithdrawals/1000, 0) }}K</span>
                        </div>
                        <h6 class="mb-1">Withdrawals</h6>
                        <small class="text-muted">Total Processed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="progress-circle bg-warning mx-auto mb-2">
                            <span>{{ $pendingKyc }}</span>
                        </div>
                        <h6 class="mb-1">Pending KYC</h6>
                        <small class="text-muted">Needs Review</small>
                    </div>
                </div>
            </div>
            @php
                $activeUsers = $activeUsers ?: 1; // Avoid division by zero
                $totalUsers = $totalUsers ?: 1; // Avoid division by zero
                $activeRate = ($activeUsers / $totalUsers) * 100;
                $activeRate = number_format($activeRate, 0);
            @endphp
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="progress-circle bg-info mx-auto mb-2">
                            <span>{{ $activeRate }}%</span>
                        </div>
                        <h6 class="mb-1">Active Rate</h6>
                        <small class="text-muted">User Activity</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Data -->
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card table-modern border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $transaction->user->firstname ?? 'N/A' }} {{ $transaction->user->lastname ?? '' }}</div>
                                                    <small class="text-muted">{{ $transaction->user->username ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-status 
                                                {{ $transaction->trx_type == '+' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $transaction->trx_type == '+' ? 'Credit' : 'Debit' }}
                                            </span>
                                        </td>
                                        <td class="fw-medium">
                                            <span class="{{ $transaction->trx_type == '+' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->trx_type }}${{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-status bg-success">Completed</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card table-modern border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">Recent Users</h5>
                    </div>
                    <div class="card-body">
                        <div class="recent-activity">
                            @foreach($recentUsers as $user)
                            <div class="activity-item {{ $user->status == 1 ? 'success' : 'warning' }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-{{ $user->status == 1 ? 'success' : 'warning' }} rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $user->firstname }} {{ $user->lastname }}</div>
                                        <small class="text-muted">{{ $user->username }}</small>
                                        <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge-status {{ $user->status == 1 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                        </div>
                        <h6 class="mb-2">Transfer Funds</h6>
                        <p class="text-muted small mb-3">Send money to users</p>
                        <a href="{{ route('admin.transfer_member') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-paper-plane me-1"></i>Transfer
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-video fa-2x text-success"></i>
                        </div>
                        <h6 class="mb-2">Video Management</h6>
                        <p class="text-muted small mb-3">Manage video links</p>
                        <a href="{{ route('admin.video-links.index') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye me-1"></i>View Videos
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                        <h6 class="mb-2">User Management</h6>
                        <p class="text-muted small mb-3">Manage user accounts</p>
                        <a href="{{ route('admin.kyc.index') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-users me-1"></i>View KYC
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-bar fa-2x text-info"></i>
                        </div>
                        <h6 class="mb-2">Analytics</h6>
                        <p class="text-muted small mb-3">View detailed reports</p>
                        <a href="{{ route('admin.video-links.export.advanced') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-line me-1"></i>Analytics
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-wallet fa-2x text-danger"></i>
                        </div>
                        <h6 class="mb-2">Deposits</h6>
                        <p class="text-muted small mb-3">Manage deposits</p>
                        <a href="{{ route('admin.deposits.pending') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-wallet me-1"></i>Deposits
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-cog fa-2x text-secondary"></i>
                        </div>
                        <h6 class="mb-2">Settings</h6>
                        <p class="text-muted small mb-3">Configure system</p>
                        <a href="{{ route('admin.settings.general') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-cog me-1"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-layout>