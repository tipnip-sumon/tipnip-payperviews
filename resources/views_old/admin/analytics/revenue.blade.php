@extends('components.layout')

@section('title', 'Revenue Analytics')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Revenue Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Revenue</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['revenueStats']['total_revenue'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-dollar-circle text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">This Month</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['revenueStats']['revenue_this_month'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-trending-up text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Withdrawals</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['revenueStats']['total_withdrawals'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-money text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Net Profit</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['revenueStats']['net_profit'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-line-chart text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Charts -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Daily Revenue Trend</h4>
                </div>
                <div class="card-body">
                    <div id="revenue_trend_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Revenue by Gateway</h4>
                </div>
                <div class="card-body">
                    <div id="gateway_revenue_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Gateway Table -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Revenue by Payment Gateway</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Gateway</th>
                                    <th scope="col">Total Revenue</th>
                                    <th scope="col">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['revenueStats']['revenue_by_gateway'] as $gateway)
                                <tr>
                                    <td>{{ $gateway->gateway_name ?? 'Unknown Gateway' }}</td>
                                    <td>${{ number_format($gateway->total, 2) }}</td>
                                    <td>
                                        @php
                                            $percentage = $data['revenueStats']['total_revenue'] > 0 ? round(($gateway->total / $data['revenueStats']['total_revenue']) * 100, 2) : 0;
                                        @endphp
                                        {{ $percentage }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Monthly Comparison</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="mb-1">Deposits</h5>
                                <p class="text-muted mb-2">This Month</p>
                                <h4 class="text-primary">${{ number_format($data['revenueStats']['monthly_comparison']['deposits']['this_month'], 2) }}</h4>
                                <p class="text-muted mb-0">Last Month: ${{ number_format($data['revenueStats']['monthly_comparison']['deposits']['last_month'], 2) }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <h5 class="mb-1">Withdrawals</h5>
                                <p class="text-muted mb-2">This Month</p>
                                <h4 class="text-warning">${{ number_format($data['revenueStats']['monthly_comparison']['withdrawals']['this_month'], 2) }}</h4>
                                <p class="text-muted mb-0">Last Month: ${{ number_format($data['revenueStats']['monthly_comparison']['withdrawals']['last_month'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
// Revenue Trend Chart
var trendData = @json($data['revenueStats']['daily_revenue_trend'] ?? []);
var dates = [];
var amounts = [];

trendData.forEach(function(item) {
    dates.push(item.date);
    amounts.push(item.total || 0);
});

var trendOptions = {
    series: [{
        name: 'Revenue',
        data: amounts
    }],
    chart: {
        height: 350,
        type: 'area',
        toolbar: {
            show: false,
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth'
    },
    xaxis: {
        categories: dates
    },
    colors: ['#405189'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
        },
    },
};

var trendChart = new ApexCharts(document.querySelector("#revenue_trend_chart"), trendOptions);
trendChart.render();

// Gateway Revenue Chart
var gatewayData = @json($data['revenueStats']['revenue_by_gateway'] ?? []);
var gatewayLabels = [];
var gatewayAmounts = [];

if (Array.isArray(gatewayData) && gatewayData.length > 0) {
    gatewayData.forEach(function(gateway) {
        gatewayLabels.push(gateway.gateway_name || 'Unknown');
        gatewayAmounts.push(gateway.total || 0);
    });
} else {
    gatewayLabels = ['No Data'];
    gatewayAmounts = [1];
}

var gatewayOptions = {
    series: gatewayAmounts,
    chart: {
        height: 300,
        type: 'donut',
    },
    labels: gatewayLabels,
    colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
    legend: {
        position: 'bottom'
    },
    dataLabels: {
        enabled: true
    }
};

var gatewayChart = new ApexCharts(document.querySelector("#gateway_revenue_chart"), gatewayOptions);
gatewayChart.render();

// Counter Animation
document.querySelectorAll('.counter-value').forEach(function (element) {
    var target = parseInt(element.getAttribute('data-target'));
    var increment = target / 200;
    var current = 0;
    var timer = setInterval(function () {
        current += increment;
        element.textContent = Math.floor(current);
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        }
    }, 10);
});
</script>
@endpush
