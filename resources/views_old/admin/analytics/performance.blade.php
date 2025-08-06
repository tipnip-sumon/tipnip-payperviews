@extends('components.layout')

@section('title', 'Performance Analytics')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Performance Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Performance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <h5 class="card-title">User Retention</h5>
                            <div class="mb-2">
                                <h3 class="text-primary">{{ $data['user_retention']['weekly'] }}%</h3>
                                <small class="text-muted">Weekly Retention</small>
                            </div>
                            <div>
                                <h4 class="text-info">{{ $data['user_retention']['monthly'] }}%</h4>
                                <small class="text-muted">Monthly Retention</small>
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="bx bx-users text-primary display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <h5 class="card-title">Conversion Rates</h5>
                            <div class="mb-2">
                                <h3 class="text-success">{{ $data['conversion_rates']['user_to_investor'] }}%</h3>
                                <small class="text-muted">User to Investor</small>
                            </div>
                            <div>
                                <h4 class="text-warning">{{ $data['conversion_rates']['user_to_video_watcher'] }}%</h4>
                                <small class="text-muted">User to Video Watcher</small>
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="bx bx-trending-up text-success display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <h5 class="card-title">Session Duration</h5>
                            <h3 class="text-info">{{ $data['average_session_duration']['minutes'] }} min</h3>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bx bx-trending-{{ $data['average_session_duration']['trend'] }}"></i>
                                    {{ ucfirst($data['average_session_duration']['trend']) }}
                                </span>
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="bx bx-time text-info display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <h5 class="card-title">Bounce Rate</h5>
                            <h3 class="text-danger">{{ $data['bounce_rate']['rate'] }}%</h3>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $data['bounce_rate']['trend'] === 'down' ? 'success' : 'danger' }}-subtle text-{{ $data['bounce_rate']['trend'] === 'down' ? 'success' : 'danger' }}">
                                    <i class="bx bx-trending-{{ $data['bounce_rate']['trend'] }}"></i>
                                    {{ ucfirst($data['bounce_rate']['trend']) }}
                                </span>
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="bx bx-exit text-danger display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Lifetime Value -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Lifetime Value</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h2 class="text-primary">${{ number_format($data['lifetime_value']['amount'], 2) }}</h2>
                                <p class="text-muted">Average Customer Lifetime Value</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4 mt-md-0">
                                <h6>Key Metrics:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="bx bx-check text-success"></i> Based on historical data analysis</li>
                                    <li><i class="bx bx-check text-success"></i> Includes investment returns</li>
                                    <li><i class="bx bx-check text-success"></i> Video earning contributions</li>
                                    <li><i class="bx bx-check text-success"></i> User engagement factors</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">User Retention Analysis</h5>
                </div>
                <div class="card-body">
                    <div id="retentionChart"></div>
                    <div class="mt-3">
                        <h6>Insights:</h6>
                        <ul class="list-unstyled text-muted">
                            <li>• Weekly retention shows user engagement levels</li>
                            <li>• Monthly retention indicates long-term value</li>
                            <li>• Higher retention correlates with better user experience</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Conversion Funnel</h5>
                </div>
                <div class="card-body">
                    <div id="conversionChart"></div>
                    <div class="mt-3">
                        <h6>Optimization Opportunities:</h6>
                        <ul class="list-unstyled text-muted">
                            <li>• Focus on converting users to investors</li>
                            <li>• Improve video engagement strategies</li>
                            <li>• Optimize onboarding flow</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Performance Recommendations</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="text-primary">Improve User Retention</h6>
                                <ul class="list-unstyled text-muted small">
                                    <li>• Implement personalized content</li>
                                    <li>• Add gamification elements</li>
                                    <li>• Enhance user notifications</li>
                                    <li>• Create loyalty programs</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="text-success">Boost Conversions</h6>
                                <ul class="list-unstyled text-muted small">
                                    <li>• Optimize investment plans</li>
                                    <li>• Improve video recommendations</li>
                                    <li>• Add social proof elements</li>
                                    <li>• Simplify conversion flows</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="text-info">Reduce Bounce Rate</h6>
                                <ul class="list-unstyled text-muted small">
                                    <li>• Improve page load speeds</li>
                                    <li>• Enhance mobile experience</li>
                                    <li>• Create engaging landing pages</li>
                                    <li>• Optimize user interface</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Retention Chart
    const retentionOptions = {
        series: [{
            name: 'Retention Rate',
            data: [{{ $data['user_retention']['weekly'] }}, {{ $data['user_retention']['monthly'] }}]
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false }
        },
        colors: ['#3b82f6'],
        xaxis: {
            categories: ['Weekly', 'Monthly']
        },
        yaxis: {
            title: { text: 'Retention Rate (%)' }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val + '%';
            }
        }
    };

    const retentionChart = new ApexCharts(document.querySelector("#retentionChart"), retentionOptions);
    retentionChart.render();

    // Conversion Funnel Chart
    const conversionOptions = {
        series: [{{ $data['conversion_rates']['user_to_investor'] }}, {{ $data['conversion_rates']['user_to_video_watcher'] }}],
        chart: {
            type: 'donut',
            height: 250
        },
        labels: ['User to Investor', 'User to Video Watcher'],
        colors: ['#10b981', '#f59e0b'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + '%';
            }
        }
    };

    const conversionChart = new ApexCharts(document.querySelector("#conversionChart"), conversionOptions);
    conversionChart.render();
});
</script>
@endpush
@endsection
