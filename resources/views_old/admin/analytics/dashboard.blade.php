@extends('components.layout')
@section('title', 'Analytics Dashboard')

@section('content')
<div class="row py-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@lang('Analytics Dashboard')</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadAnalytics('today')">@lang('Today')</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadAnalytics('week')">@lang('This Week')</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadAnalytics('month')">@lang('This Month')</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadAnalytics('year')">@lang('This Year')</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">@lang('Total Users')</h6>
                                        <h4 class="mb-0" id="total-users">{{ $gs->totalUsers ?? 0 }}</h4>
                                    </div>
                                    <i class="las la-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">@lang('Total Revenue')</h6>
                                        <h4 class="mb-0" id="total-revenue">{{ showAmount($gs->totalRevenue ?? 0) }}</h4>
                                    </div>
                                    <i class="las la-dollar-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">@lang('Total Videos')</h6>
                                        <h4 class="mb-0" id="total-videos">{{ $gs->totalVideos ?? 0 }}</h4>
                                    </div>
                                    <i class="las la-video fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">@lang('Active Plans')</h6>
                                        <h4 class="mb-0" id="active-plans">{{ $gs->activePlans ?? 0 }}</h4>
                                    </div>
                                    <i class="las la-chart-line fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">@lang('User Registration Trends')</h6>
                            </div>
                            <div class="card-body">
                                <div id="userChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">@lang('Revenue Analytics')</h6>
                            </div>
                            <div class="card-body">
                                <div id="revenueChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">@lang('Video Performance')</h6>
                            </div>
                            <div class="card-body">
                                <div id="videoChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">@lang('Investment Overview')</h6>
                            </div>
                            <div class="card-body">
                                <div id="investmentChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script-lib')
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
<script>
    let userChart, revenueChart, videoChart, investmentChart;
    
    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalytics('month'); // Load current month by default
    });

    function loadAnalytics(period) {
        // Show loading state
        showLoading();
        
        // Fetch analytics data
        fetch(`{{ route('admin.analytics.chart-data') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                updateSummaryCards(data);
                updateCharts(data);
                hideLoading();
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
                hideLoading();
                showNotification('Error loading analytics data', 'error');
            });
    }

    function updateSummaryCards(data) {
        document.getElementById('total-users').textContent = data.summary?.total_period_users || 0;
        document.getElementById('total-revenue').textContent = data.summary?.total_period_revenue || '0.00';
        document.getElementById('total-videos').textContent = data.summary?.total_period_views || 0;
        document.getElementById('active-plans').textContent = data.summary?.total_period_investments || 0;
    }

    function updateCharts(data) {
        // User Registration Chart
        if (userChart) {
            userChart.destroy();
        }
        userChart = new ApexCharts(document.querySelector("#userChart"), {
            series: [{
                name: 'New Users',
                data: data.user_registrations || []
            }],
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false }
            },
            colors: ['#3b82f6'],
            xaxis: {
                categories: data.dates || []
            },
            yaxis: {
                title: { text: 'Users' }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 6,
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 1
            }
        });
        userChart.render();

        // Revenue Chart
        if (revenueChart) {
            revenueChart.destroy();
        }
        revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
            series: [{
                name: 'Revenue',
                data: data.daily_revenue || []
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false }
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1
                }
            },
            xaxis: {
                categories: data.dates || []
            },
            yaxis: {
                title: { text: 'Amount' }
            }
        });
        revenueChart.render();

        // Video Performance Chart
        if (videoChart) {
            videoChart.destroy();
        }
        videoChart = new ApexCharts(document.querySelector("#videoChart"), {
            series: [{
                name: 'Video Views',
                data: data.video_views || []
            }],
            chart: {
                type: 'donut',
                height: 300
            },
            labels: data.dates || [],
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
            legend: {
                position: 'bottom'
            }
        });
        videoChart.render();

        // Investment Chart
        if (investmentChart) {
            investmentChart.destroy();
        }
        investmentChart = new ApexCharts(document.querySelector("#investmentChart"), {
            series: [{
                name: 'Investment Amount',
                data: data.daily_investments_amount || []
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            colors: ['#8b5cf6'],
            xaxis: {
                categories: data.dates || []
            },
            yaxis: {
                title: { text: 'Amount' }
            }
        });
        investmentChart.render();
    }

    function showLoading() {
        // Add loading spinners to chart containers
        const chartContainers = ['#userChart', '#revenueChart', '#videoChart', '#investmentChart'];
        chartContainers.forEach(container => {
            document.querySelector(container).innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
        });
    }

    function hideLoading() {
        // Loading will be hidden when charts render
    }

    function showNotification(message, type = 'info') {
        // Simple notification system
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-info';
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
</script>
@endpush
@endsection
