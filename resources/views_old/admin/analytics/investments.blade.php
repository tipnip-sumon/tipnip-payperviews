@extends('components.layout')

@section('title', 'Investment Analytics')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Investment Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Investments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Investments</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['investmentStats']['total_investments'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-trending-up text-primary"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Active Investments</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['investmentStats']['active_investments'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-check-circle text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Completed</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['investmentStats']['completed_investments'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-check-double text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Returns Paid</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['investmentStats']['total_returns_paid'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-dollar text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Charts -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Investment Trend</h4>
                </div>
                <div class="card-body">
                    <div id="investment_trend_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Plan Popularity</h4>
                </div>
                <div class="card-body">
                    <div id="plan_popularity_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Plans Performance -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Investment Plans Performance</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Plan Name</th>
                                    <th scope="col">Min Amount</th>
                                    <th scope="col">Max Amount</th>
                                    <th scope="col">Interest Rate</th>
                                    <th scope="col">Investment Count</th>
                                    <th scope="col">Total Invested</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['investmentStats']['investments_by_plan']) && count($data['investmentStats']['investments_by_plan']) > 0)
                                    @foreach($data['investmentStats']['investments_by_plan'] as $plan)
                                    <tr>
                                        <td>{{ $plan->name ?? 'Unknown Plan' }}</td>
                                        <td>${{ number_format($plan->minimum ?? 0, 2) }}</td>
                                        <td>${{ number_format($plan->maximum ?? 0, 2) }}</td>
                                        <td>{{ number_format($plan->interest ?? 0, 2) }}{{ $plan->interest_type ? '%' : ' USD' }}</td>
                                        <td>{{ $plan->investment_count ?? 0 }}</td>
                                        <td>${{ number_format($plan->total_invested ?? 0, 2) }}</td>
                                        <td>
                                            @if($plan->status)
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No investment plans data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
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
// Investment Trend Chart
var trendData = @json($data['investmentStats']['investment_trend'] ?? []);
var dates = [];
var counts = [];
var amounts = [];

trendData.forEach(function(item) {
    dates.push(item.date);
    counts.push(item.count || 0);
    amounts.push(item.total || 0);
});

var trendOptions = {
    series: [{
        name: 'Investment Count',
        data: counts
    }, {
        name: 'Investment Amount ($)',
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
    colors: ['#405189', '#0ab39c'],
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

var trendChart = new ApexCharts(document.querySelector("#investment_trend_chart"), trendOptions);
trendChart.render();

// Plan Popularity Chart
var popularityData = @json($data['investmentStats']['plan_popularity'] ?? []);
var planLabels = [];
var planScores = [];

if (Array.isArray(popularityData) && popularityData.length > 0) {
    popularityData.forEach(function(plan) {
        planLabels.push(plan.name || 'Unknown');
        planScores.push(plan.popularity_score || 0);
    });
} else {
    planLabels = ['No Data'];
    planScores = [1];
}

var popularityOptions = {
    series: planScores,
    chart: {
        height: 300,
        type: 'donut',
    },
    labels: planLabels,
    colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
    legend: {
        position: 'bottom'
    },
    dataLabels: {
        enabled: true
    }
};

var popularityChart = new ApexCharts(document.querySelector("#plan_popularity_chart"), popularityOptions);
popularityChart.render();

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
