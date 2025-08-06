@extends('components.layout')

@section('top_title', 'Plan Details')
@section('title', 'Plan Information')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        border: none;
    }
    .stat-card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        transition: transform 0.15s ease-in-out;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .earnings-chart {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
    }
    .investor-row {
        transition: background-color 0.15s ease-in-out;
    }
    .investor-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <!-- Page Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="fe fe-eye text-primary me-2"></i>Plan Details
                        </h4>
                        <p class="text-muted mb-0">Comprehensive information about "{{ $plan->name }}"</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-1"></i>Back to Plans
                        </a>
                        <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-primary">
                            <i class="fe fe-edit me-1"></i>Edit Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Plan Overview -->
            <div class="col-lg-4">
                <div class="card info-card text-white mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fe fe-package fa-3x"></i>
                        </div>
                        <h3 class="mb-2">{{ $plan->name }}</h3>
                        @if($plan->description)
                            <p class="mb-3 opacity-75">{{ $plan->description }}</p>
                        @endif
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="mb-1">${{ number_format($plan->fixed_amount, 2) }}</h4>
                                <small class="opacity-75">Investment Amount</small>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1">{{ $plan->time }}</h4>
                                <small class="opacity-75">Days Duration</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            @if($plan->video_access_enabled)
                                <span class="badge bg-success fs-6">Active Plan</span>
                            @else
                                <span class="badge bg-secondary fs-6">Inactive Plan</span>
                            @endif
                            @if($plan->featured)
                                <span class="badge bg-warning fs-6 ms-1">Featured</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Plan Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fe fe-settings me-2"></i>Plan Configuration
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Daily Video Limit</small>
                                <div class="fw-bold">{{ $plan->daily_video_limit }} videos</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Per Video Earning</small>
                                <div class="fw-bold text-success">${{ number_format($plan->video_earning_rate, 6) }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Daily Earning</small>
                                <div class="fw-bold text-primary">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Total Earning</small>
                                <div class="fw-bold text-info">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate * $plan->time, 2) }}</div>
                            </div>
                        </div>
                        @if($plan->interest)
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Interest Rate</small>
                                <div class="fw-bold">{{ $plan->interest }}% {{ $plan->interest_type ?? 'daily' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Capital Return</small>
                                <div class="fw-bold">{{ $plan->capital_back ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Lifetime Plan</small>
                                <div class="fw-bold">{{ $plan->lifetime ? 'Yes' : 'No' }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Plan Status</small>
                                <div class="fw-bold">{{ $plan->status ? 'Active' : 'Inactive' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fe fe-clock me-2"></i>Plan Timeline
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Created</small>
                            <div class="fw-bold">{{ $plan->created_at->format('M d, Y h:i A') }}</div>
                            <small class="text-muted">{{ $plan->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-bold">{{ $plan->updated_at->format('M d, Y h:i A') }}</div>
                            <small class="text-muted">{{ $plan->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics & Analytics -->
            <div class="col-lg-8">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fe fe-users text-primary fa-2x mb-2"></i>
                                <h4 class="mb-1">{{ $plan->invests->count() ?? 0 }}</h4>
                                <small class="text-muted">Total Investors</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fe fe-dollar-sign text-success fa-2x mb-2"></i>
                                <h4 class="mb-1">${{ number_format($plan->invests->sum('amount') ?? 0, 2) }}</h4>
                                <small class="text-muted">Total Investment</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fe fe-trending-up text-info fa-2x mb-2"></i>
                                <h4 class="mb-1">${{ number_format($plan->invests->avg('amount') ?? 0, 2) }}</h4>
                                <small class="text-muted">Avg Investment</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fe fe-percent text-warning fa-2x mb-2"></i>
                                <h4 class="mb-1">{{ $plan->invests->where('status', 1)->count() ?? 0 }}</h4>
                                <small class="text-muted">Active Investments</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earning Breakdown -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fe fe-bar-chart-2 me-2"></i>Earning Breakdown
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="earnings-chart">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <h5 class="text-primary mb-1">${{ number_format($plan->video_earning_rate, 6) }}</h5>
                                        <small class="text-muted">Per Video</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <h5 class="text-success mb-1">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}</h5>
                                        <small class="text-muted">Daily Maximum</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <h5 class="text-info mb-1">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate * 30, 2) }}</h5>
                                        <small class="text-muted">Monthly Potential</small>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 33.33%"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: 33.33%"></div>
                                <div class="progress-bar bg-info" role="progressbar" style="width: 33.34%"></div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">
                                    Total plan earning potential: <strong>${{ number_format($plan->daily_video_limit * $plan->video_earning_rate * $plan->time, 2) }}</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Investors -->
                @if($plan->invests && $plan->invests->count() > 0)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fe fe-users me-2"></i>Recent Investors
                        </h6>
                        <small class="text-muted">Last 10 investments</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-bottom-0">Investor</th>
                                        <th class="border-bottom-0">Amount</th>
                                        <th class="border-bottom-0">Date</th>
                                        <th class="border-bottom-0">Status</th>
                                        <th class="border-bottom-0">ROI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plan->invests->take(10) as $invest)
                                    <tr class="investor-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle me-2">
                                                    {{ substr($invest->user->firstname ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $invest->user->firstname ?? '' }} {{ $invest->user->lastname ?? '' }}</div>
                                                    <small class="text-muted">{{ $invest->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">${{ number_format($invest->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $invest->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $invest->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($invest->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $dailyEarning = $plan->daily_video_limit * $plan->video_earning_rate;
                                                $daysActive = $invest->created_at->diffInDays(now());
                                                $totalEarned = min($daysActive, $plan->time) * $dailyEarning;
                                                $roi = $invest->amount > 0 ? ($totalEarned / $invest->amount) * 100 : 0;
                                            @endphp
                                            <span class="text-{{ $roi > 100 ? 'success' : 'warning' }}">{{ number_format($roi, 1) }}%</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fe fe-users text-muted fa-3x mb-3"></i>
                        <h5 class="text-muted">No Investors Yet</h5>
                        <p class="text-muted">This plan hasn't received any investments yet.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Any additional JavaScript for the show page can go here
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any tooltips or interactive elements
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
