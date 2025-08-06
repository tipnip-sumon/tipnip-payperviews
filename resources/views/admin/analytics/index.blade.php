@extends('components.layout')

@section('title', 'Analytics Dashboard')

@section('content') 
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Analytics Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Users</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-success fs-14 mb-0">
                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['overview']['total_users'] }}">0</span>
                            </h4>
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-underline">View all users</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-user text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Revenue</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-success fs-14 mb-0">
                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['overview']['total_revenue'] }}">0</span>
                            </h4>
                            <a href="{{ route('admin.deposits.index') }}" class="text-decoration-underline">View deposits</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-dollar-circle text-info"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Active Videos</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-danger fs-14 mb-0">
                                <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['overview']['total_videos'] }}">0</span>
                            </h4>
                            <a href="{{ route('admin.video-links.index') }}" class="text-decoration-underline">Manage videos</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-play-circle text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Support Tickets</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-success fs-14 mb-0">
                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +1.12%
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['overview']['support_tickets_open'] }}">0</span>
                            </h4>
                            <a href="{{ route('admin.support.tickets') }}" class="text-decoration-underline">View tickets</a>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-support text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Revenue Analytics</h4>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-soft-secondary btn-sm">
                            ALL
                        </button>
                        <button type="button" class="btn btn-soft-secondary btn-sm">
                            1M
                        </button>
                        <button type="button" class="btn btn-soft-secondary btn-sm">
                            6M
                        </button>
                        <button type="button" class="btn btn-soft-primary btn-sm">
                            1Y
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pb-2">
                    <div class="w-100">
                        <div id="revenue_chart" data-colors='["--vz-primary", "--vz-success", "--vz-danger"]' class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Video Views by Category</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fw-semibold text-uppercase fs-12">Sort by: </span><span class="text-muted">Current Week <i class="mdi mdi-chevron-down ms-1"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="video_views_chart" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Recent User Registrations</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-info btn-sm">
                            <i class="ri-file-list-3-line align-middle"></i> Generate Report
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Country</th>
                                    <th scope="col">Join Date</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['userStats']['user_registrations_trend']))
                                    @foreach($data['userStats']['user_registrations_trend']->take(5) as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar-xs rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center">
                                                        <i class="bx bx-user text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">New User {{ $loop->iteration }}</div>
                                            </div>
                                        </td>
                                        <td>user{{ $loop->iteration }}@example.com</td>
                                        <td>{{ $user->date }}</td>
                                        <td>{{ $user->date }}</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">No recent registrations</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Top Performing Videos</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-primary btn-sm">
                            <i class="ri-share-line align-middle"></i> Share
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Video</th>
                                    <th scope="col">Views</th>
                                    <th scope="col">Earnings</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['videoStats']['most_watched_videos']) && count($data['videoStats']['most_watched_videos']) > 0)
                                    @foreach($data['videoStats']['most_watched_videos'] as $video)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar-xs rounded bg-warning-subtle d-flex align-items-center justify-content-center">
                                                        <i class="bx bx-play text-warning"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">{{ Str::limit($video->title ?? 'Video ' . $loop->iteration, 30) }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $video->view_count ?? 0 }}</td>
                                        <td>${{ number_format($video->cost_per_click ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No video data available</td>
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
// Revenue Chart
var revenueChartOptions = {
    series: [{
        name: 'Revenue',
        data: @json($data['chartData']['daily_revenue'] ?? [])
    }, {
        name: 'User Registrations', 
        data: @json($data['chartData']['user_registrations'] ?? [])
    }, {
        name: 'Video Views',
        data: @json($data['chartData']['video_views'] ?? [])
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
        categories: @json($data['chartData']['dates'] ?? [])
    },
    colors: ['#405189', '#0ab39c', '#f06548'],
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

var revenueChart = new ApexCharts(document.querySelector("#revenue_chart"), revenueChartOptions);
revenueChart.render();

// Video Views Pie Chart
var videoCategories = @json($data['videoStats']['video_categories_performance'] ?? []);
var categoryData = [];
var categoryLabels = [];

if (Array.isArray(videoCategories) && videoCategories.length > 0) {
    videoCategories.forEach(function(category) {
        categoryLabels.push(category.category || 'Unknown');
        categoryData.push(category.video_count || 0);
    });
} else {
    // Default data if no categories
    categoryLabels = ['No Data'];
    categoryData = [1];
}

var videoViewsOptions = {
    series: categoryData,
    chart: {
        height: 300,
        type: 'donut',
    },
    labels: categoryLabels,
    colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
    legend: {
        position: 'bottom'
    },
    dataLabels: {
        enabled: false
    }
};

var videoViewsChart = new ApexCharts(document.querySelector("#video_views_chart"), videoViewsOptions);
videoViewsChart.render();

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
