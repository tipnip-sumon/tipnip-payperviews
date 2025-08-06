<x-smart_layout>

@section('title', 'Generation History')

@section('content')

<div class="container-fluid py-4">
    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Referral Commission Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($stats['total_earned'], 6) }}</h4>
                                    <p class="mb-0">Total Earned</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($stats['today_earned'], 6) }}</h4>
                                    <p class="mb-0">Today's Earnings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($stats['this_month_earned'], 6) }}</h4>
                                    <p class="mb-0">This Month</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ number_format($stats['total_commissions']) }}</h4>
                                    <p class="mb-0">Total Commissions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Level-wise Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Level-wise Commission Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Count</th>
                                    <th>Total Earned</th>
                                    <th>Average Commission</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($levelStats as $level => $data)
                                    @if($data['count'] > 0)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">Level {{ $level }}</span>
                                            </td>
                                            <td>{{ number_format($data['count']) }}</td>
                                            <td>${{ number_format($data['total'], 6) }}</td>
                                            <td>${{ number_format($data['average'], 6) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $stats['total_earned'] > 0 ? ($data['total'] / $stats['total_earned']) * 100 : 0 }}%">
                                                        {{ $stats['total_earned'] > 0 ? number_format(($data['total'] / $stats['total_earned']) * 100, 1) : 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Commissions</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity->take(10) as $activity)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($activity->date)->format('M d, Y') }}</td>
                                            <td>{{ $activity->count }}</td>
                                            <td>${{ number_format($activity->total, 6) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent activity found.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Earning Days</h5>
                </div>
                <div class="card-body">
                    @if($topEarningDays->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Earned</th>
                                        <th>Rank</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topEarningDays as $index => $day)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                            <td>${{ number_format($day->total, 6) }}</td>
                                            <td>
                                                @if($index == 0)
                                                    <span class="badge bg-warning">🥇 #1</span>
                                                @elseif($index == 1)
                                                    <span class="badge bg-secondary">🥈 #2</span>
                                                @elseif($index == 2)
                                                    <span class="badge bg-dark">🥉 #3</span>
                                                @else
                                                    <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No earning history found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Commission History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detailed Commission History</h5>
                    <div>
                        <small class="text-muted">{{ $referralEarnings->total() }} total records</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($referralEarnings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Earner</th>
                                        <th>Level</th>
                                        <th>Type</th>
                                        <th>Original Earning</th>
                                        <th>Commission %</th>
                                        <th>Commission Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referralEarnings as $earning)
                                        <tr>
                                            <td>
                                                <small>
                                                    {{ \Carbon\Carbon::parse($earning->distributed_at)->format('M d, Y') }}<br>
                                                    <span class="text-muted">{{ \Carbon\Carbon::parse($earning->distributed_at)->format('h:i A') }}</span>
                                                </small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $earning->earner->username ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $earning->earner->email ?? '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">Level {{ $earning->level }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $earning->earning_type == 'daily_video_total' ? 'bg-success' : 'bg-info' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $earning->earning_type ?? $earning->commission_type)) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($earning->original_earning, 6) }}</td>
                                            <td>{{ number_format($earning->commission_percentage, 2) }}%</td>
                                            <td>
                                                <strong class="text-success">${{ number_format($earning->commission_amount, 6) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Distributed
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $referralEarnings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Commission History</h5>
                            <p class="text-muted">You haven't earned any referral commissions yet. Start referring users to earn commissions!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
</x-smart_layout>
