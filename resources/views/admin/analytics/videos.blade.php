@extends('components.layout')

@section('title', 'Video Analytics')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Video Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Videos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Videos</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['videoStats']['total_videos'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-video text-primary"></i>
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
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['videoStats']['active_videos'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-play-circle text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Views</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" data-target="{{ $data['videoStats']['total_views'] }}">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-show text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Earnings</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" data-target="{{ $data['videoStats']['total_earnings_paid'] }}">0</span>
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

    <!-- Video Performance Charts -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Video Views Trend</h4>
                </div>
                <div class="card-body">
                    <div id="video_views_trend_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Video Categories</h4>
                </div>
                <div class="card-body">
                    <div id="video_categories_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Videos -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Most Watched Videos</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Video Title</th>
                                    <th scope="col">Views</th>
                                    <th scope="col">Cost Per Click</th>
                                    <th scope="col">Video URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['videoStats']['most_watched_videos']) && count($data['videoStats']['most_watched_videos']) > 0)
                                    @foreach($data['videoStats']['most_watched_videos'] as $video)
                                    <tr>
                                        <td>{{ Str::limit($video->title ?? 'Untitled Video', 50) }}</td>
                                        <td>{{ $video->view_count ?? 0 }}</td>
                                        <td>${{ number_format($video->cost_per_click ?? 0, 4) }}</td>
                                        <td>
                                            @if($video->video_url)
                                                <a href="{{ $video->video_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-link-external"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">No URL</span>
                                            @endif
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
// Video Views Trend Chart
var trendData = @json($data['videoStats']['video_views_trend'] ?? []);
var dates = [];
var views = [];
var earnings = [];

trendData.forEach(function(item) {
    dates.push(item.date);
    views.push(item.views || 0);
    earnings.push(item.earnings || 0);
});

var trendOptions = {
    series: [{
        name: 'Views',
        data: views
    }, {
        name: 'Earnings ($)',
        data: earnings
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

var trendChart = new ApexCharts(document.querySelector("#video_views_trend_chart"), trendOptions);
trendChart.render();

// Video Categories Chart
var categoryData = @json($data['videoStats']['video_categories_performance'] ?? []);
var categoryLabels = [];
var categoryCounts = [];

if (Array.isArray(categoryData) && categoryData.length > 0) {
    categoryData.forEach(function(category) {
        categoryLabels.push(category.category || 'Unknown');
        categoryCounts.push(category.video_count || 0);
    });
} else {
    categoryLabels = ['No Data'];
    categoryCounts = [1];
}

var categoryOptions = {
    series: categoryCounts,
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
        enabled: true
    }
};

var categoryChart = new ApexCharts(document.querySelector("#video_categories_chart"), categoryOptions);
categoryChart.render();

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
