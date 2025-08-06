<x-smart_layout>
    @section('top_title', $pageTitle ?? 'User Profile Dashboard')
    @section('title', $pageTitle ?? 'User Profile Dashboard')
    @section('content')
        
        <!-- Profile Header -->
        <div class="row mb-4 my-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="position-relative">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="Profile Avatar" 
                                             class="rounded-circle border border-white"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center border border-white"
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user fa-2x text-primary"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Email Verification Badge -->
                                    @if($user->hasVerifiedEmail())
                                        <div class="position-absolute bottom-0 end-0">
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </div>
                                    @else
                                        <div class="position-absolute bottom-0 end-0">
                                            <span class="badge bg-warning rounded-pill">
                                                <i class="fas fa-exclamation"></i>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-1">{{ $user->firstname }} {{ $user->lastname }}</h4>
                                <p class="mb-2">
                                    <i class="fas fa-at me-1"></i> {{ $user->username }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                                    @if($user->hasVerifiedEmail())
                                        <span class="badge bg-success ms-2">Verified</span>
                                    @else
                                        <span class="badge bg-warning ms-2">Unverified</span>
                                    @endif
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-calendar me-1"></i> Member since {{ $user->created_at->format('F Y') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column">
                                    <h5 class="mb-1">Account Balance</h5>
                                    <h3 class="mb-2">${{ number_format($profileStats['account_balance'], 2) }}</h3>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                                            <i class="fas fa-edit"></i> Edit Profile
                                        </a>
                                        <a href="{{ route('profile.security') }}" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-shield-alt"></i> Security
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Total Earnings</p>
                                <h4 class="text-white mb-0">${{ number_format($profileStats['total_earnings'], 4) }}</h4>
                            </div>
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Videos Watched</p>
                                <h4 class="text-white mb-0">{{ number_format($profileStats['total_videos_watched']) }}</h4>
                            </div>
                            <i class="fas fa-play-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Total Deposits</p>
                                <h4 class="text-white mb-0">${{ number_format($profileStats['total_deposits'], 2) }}</h4>
                            </div>
                            <i class="fas fa-arrow-down fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-white mb-1">Referral Earnings</p>
                                <h4 class="text-white mb-0">${{ number_format($profileStats['referral_earnings'], 2) }}</h4>
                            </div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information & Quick Actions -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user text-primary"></i> Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">First Name:</td>
                                        <td>{{ $user->firstname ?: 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Last Name:</td>
                                        <td>{{ $user->lastname ?: 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Username:</td>
                                        <td>{{ $user->username }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Email:</td>
                                        <td>
                                            {{ $user->email }}
                                            @if(!$user->hasVerifiedEmail())
                                                <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-outline-warning ms-2">
                                                    Verify Email
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Phone:</td>
                                        <td>{{ $user->phone ?: 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Country:</td>
                                        <td>{{ $user->country ?: 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Sponsor:</td>
                                        <td>{{ $user->sponsor ?: 'Direct' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Account Status:</td>
                                        <td>
                                            @if($user->status === 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog text-primary"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Profile
                            </a>
                            <a href="{{ route('profile.password') }}" class="btn btn-outline-warning">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                            <a href="{{ route('profile.security') }}" class="btn btn-outline-info">
                                <i class="fas fa-shield-alt"></i> Security Settings
                            </a>
                            @if(!$user->hasVerifiedEmail())
                                <a href="{{ route('verification.notice') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-envelope"></i> Verify Email
                                </a>
                            @endif
                            <a href="{{ route('user.kyc.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-id-card"></i> KYC Verification
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Account Status Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shield-alt text-success"></i> Account Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Email Verification</span>
                            @if($user->hasVerifiedEmail())
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>KYC Verification</span>
                            @if($user->kv == 1)
                                <span class="badge bg-success">Verified</span>
                            @elseif($user->kv == 2)
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Not Verified</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Account Status</span>
                            @if($user->status === 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('style')
    <style>
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-bottom: 1px solid #eee;
            background: transparent;
        }
        
        .table td {
            padding: 0.5rem 0;
        }
        
        .profile-avatar {
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .btn {
            border-radius: 6px;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
    @endpush
</x-smart_layout>