@extends('components.layout')

@section('title', 'User Analytics')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">User Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Users</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['userStats']['total_users'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-user text-primary"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Verified Users</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['userStats']['verified_users'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-check-shield text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">KYC Verified</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['userStats']['kyc_verified_users'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-id-card text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Active (30 Days)</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['userStats']['active_users_30_days'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-time text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Registration Trend Chart -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Registration Trend</h4>
                </div>
                <div class="card-body">
                    <div id="user_registration_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users by Country -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Users by Country</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Country</th>
                                    <th scope="col">Users</th>
                                    <th scope="col">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['userStats']['users_by_country'] as $country)
                                <tr>
                                    <td>{{ $country->country ?? 'Unknown' }}</td>
                                    <td>{{ $country->total }}</td>
                                    <td>
                                        @php
                                            $percentage = $data['userStats']['total_users'] > 0 ? round(($country->total / $data['userStats']['total_users']) * 100, 2) : 0;
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
                    <h4 class="card-title mb-0">User Activity Levels</h4>
                </div>
                <div class="card-body">
                    <div id="activity_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
// User Registration Chart
var registrationData = @json($data['userStats']['user_registrations_trend'] ?? []);
var dates = [];
var counts = [];

registrationData.forEach(function(item) {
    dates.push(item.date);
    counts.push(item.count);
});

var registrationOptions = {
    series: [{
        name: 'New Users',
        data: counts
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

var registrationChart = new ApexCharts(document.querySelector("#user_registration_chart"), registrationOptions);
registrationChart.render();

// Activity Levels Chart
var activityLevels = @json($data['userStats']['user_activity_levels'] ?? []);

var activityOptions = {
    series: [activityLevels.highly_active || 0, activityLevels.moderately_active || 0, activityLevels.inactive || 0],
    chart: {
        height: 300,
        type: 'donut',
    },
    labels: ['Highly Active', 'Moderately Active', 'Inactive'],
    colors: ['#0ab39c', '#f7b84b', '#f06548'],
    legend: {
        position: 'bottom'
    },
    dataLabels: {
        enabled: true
    }
};

var activityChart = new ApexCharts(document.querySelector("#activity_chart"), activityOptions);
activityChart.render();

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
