@extends('components.layout')
@section('title', 'Analytics Chart Data')



@section('content') 
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Analytics Chart Data Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                        <li class="breadcrumb-item active">Chart Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Live Analytics Dashboard</h5>
                            <p class="text-muted mb-0">Real-time data visualization and insights</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary" id="refreshData">
                                <i class="bx bx-refresh"></i> Refresh Data
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="exportData">
                                <i class="bx bx-download"></i> Export
                            </button>
                            <div class="form-check form-switch d-inline-block ms-3">
                                <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                                <label class="form-check-label" for="autoRefresh">Auto Refresh</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summaryCards">
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
                                <span class="counter-value" id="totalUsers">0</span>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" id="totalRevenue">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-dollar text-success"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Video Views</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span class="counter-value" id="totalViews">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-play text-warning"></i>
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
                            <p class="text-uppercase fw-medium text-muted mb-0">Investments</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                $<span class="counter-value" id="totalInvestments">0</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-trending-up text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row -->
    <div class="row">
        <!-- User Registrations Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Registrations Trend</h4>
                </div>
                <div class="card-body">
                    <div id="userRegistrationsChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Daily Revenue</h4>
                </div>
                <div class="card-body">
                    <div id="revenueChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="row">
        <!-- Video Views Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Video Views</h4>
                </div>
                <div class="card-body">
                    <div id="videoViewsChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <!-- Investments Chart -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Investment Activity</h4>
                </div>
                <div class="card-body">
                    <div id="investmentsChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Analytics Row -->
    <div class="row">
        <!-- Multi-line Chart -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Combined Analytics Overview</h4>
                </div>
                <div class="card-body">
                    <div id="combinedChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Active Users</h4>
                </div>
                <div class="card-body">
                    <div id="activeUsersChart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row">
        <!-- Raw Data Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Raw Chart Data</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="chartDataTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Users</th>
                                    <th>Revenue</th>
                                    <th>Video Views</th>
                                    <th>Investments</th>
                                    <th>Active Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JSON Data Display -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Live JSON Data Feed</h4>
                    <p class="text-muted mb-0">Real-time analytics data in JSON format</p>
                </div>
                <div class="card-body">
                    <pre id="jsonDataDisplay" class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
Loading data...
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
let chartData = {};
let charts = {};
let autoRefreshInterval;

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Show loading state initially
    document.getElementById('jsonDataDisplay').textContent = 'Loading data...';
    
    loadChartData();
    initializeAutoRefresh();
    
    // Event listeners
    document.getElementById('refreshData').addEventListener('click', function() {
        document.getElementById('jsonDataDisplay').textContent = 'Refreshing data...';
        loadChartData();
    });
    document.getElementById('exportData').addEventListener('click', exportData);
    document.getElementById('autoRefresh').addEventListener('change', function() {
        if (this.checked) {
            initializeAutoRefresh();
        } else {
            clearInterval(autoRefreshInterval);
        }
    });
});

// Load chart data from API
async function loadChartData() {
    try {
        const response = await fetch('{{ route("admin.analytics.chart-data") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            if (response.status === 401 || response.status === 419) {
                document.getElementById('jsonDataDisplay').textContent = 'Authentication error: Please refresh the page and login again.';
                return;
            }
            if (response.status === 403) {
                document.getElementById('jsonDataDisplay').textContent = 'Access denied: You do not have permission to view this data.';
                return;
            }
            if (response.status === 404) {
                document.getElementById('jsonDataDisplay').textContent = 'API endpoint not found. Please check the route configuration.';
                return;
            }
            throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
        }
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Response is not JSON format");
        }
        
        chartData = await response.json();
        
        // Check if chartData is valid
        if (!chartData || typeof chartData !== 'object') {
            throw new Error('Invalid data received from server');
        }
        
        updateSummaryCards();
        renderCharts();
        updateDataTable();
        updateJsonDisplay();
        
        console.log('Chart data loaded successfully', chartData);
    } catch (error) {
        console.error('Error loading chart data:', error);
        document.getElementById('jsonDataDisplay').textContent = 'Error loading data: ' + error.message;
    }
}

// Update summary cards
function updateSummaryCards() {
    if (chartData && chartData.summary) {
        document.getElementById('totalUsers').textContent = chartData.summary.total_period_users || 0;
        document.getElementById('totalRevenue').textContent = chartData.summary.total_period_revenue || 0;
        document.getElementById('totalViews').textContent = chartData.summary.total_period_views || 0;
        document.getElementById('totalInvestments').textContent = chartData.summary.total_period_investments || 0;
    } else {
        // Set default values if no data
        document.getElementById('totalUsers').textContent = '0';
        document.getElementById('totalRevenue').textContent = '0';
        document.getElementById('totalViews').textContent = '0';
        document.getElementById('totalInvestments').textContent = '0';
    }
}

// Render all charts
function renderCharts() {
    if (!chartData || Object.keys(chartData).length === 0) {
        console.warn('No chart data available for rendering');
        return;
    }
    
    try {
        renderUserRegistrationsChart();
        renderRevenueChart();
        renderVideoViewsChart();
        renderInvestmentsChart();
        renderCombinedChart();
        renderActiveUsersChart();
    } catch (error) {
        console.error('Error rendering charts:', error);
        document.getElementById('jsonDataDisplay').textContent = 'Error rendering charts: ' + error.message;
    }
}

// User Registrations Chart
function renderUserRegistrationsChart() {
    const options = {
        series: [{
            name: 'User Registrations',
            data: chartData.user_registrations || []
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
            categories: chartData.dates || []
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

    if (charts.userRegistrations) {
        charts.userRegistrations.destroy();
    }
    charts.userRegistrations = new ApexCharts(document.querySelector("#userRegistrationsChart"), options);
    charts.userRegistrations.render();
}

// Revenue Chart
function renderRevenueChart() {
    const options = {
        series: [{
            name: 'Daily Revenue',
            data: chartData.daily_revenue || []
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false,
            },
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: chartData.dates || []
        },
        colors: ['#0ab39c'],
    };

    if (charts.revenue) {
        charts.revenue.destroy();
    }
    charts.revenue = new ApexCharts(document.querySelector("#revenueChart"), options);
    charts.revenue.render();
}

// Video Views Chart
function renderVideoViewsChart() {
    const options = {
        series: [{
            name: 'Video Views',
            data: chartData.video_views || []
        }],
        chart: {
            height: 350,
            type: 'line',
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
            categories: chartData.dates || []
        },
        colors: ['#f7b84b'],
    };

    if (charts.videoViews) {
        charts.videoViews.destroy();
    }
    charts.videoViews = new ApexCharts(document.querySelector("#videoViewsChart"), options);
    charts.videoViews.render();
}

// Investments Chart
function renderInvestmentsChart() {
    const options = {
        series: [{
            name: 'Investment Count',
            data: chartData.daily_investments_count || []
        }, {
            name: 'Investment Amount',
            data: chartData.daily_investments_amount || []
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
            categories: chartData.dates || []
        },
        colors: ['#299cdb', '#f06548'],
    };

    if (charts.investments) {
        charts.investments.destroy();
    }
    charts.investments = new ApexCharts(document.querySelector("#investmentsChart"), options);
    charts.investments.render();
}

// Combined Chart
function renderCombinedChart() {
    const options = {
        series: [{
            name: 'Users',
            data: chartData.user_registrations || []
        }, {
            name: 'Revenue',
            data: chartData.daily_revenue || []
        }, {
            name: 'Video Views',
            data: chartData.video_views || []
        }],
        chart: {
            height: 350,
            type: 'line',
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
            categories: chartData.dates || []
        },
        colors: ['#405189', '#0ab39c', '#f7b84b'],
    };

    if (charts.combined) {
        charts.combined.destroy();
    }
    charts.combined = new ApexCharts(document.querySelector("#combinedChart"), options);
    charts.combined.render();
}

// Active Users Chart
function renderActiveUsersChart() {
    const options = {
        series: [{
            name: 'Active Users',
            data: chartData.active_users || []
        }],
        chart: {
            height: 300,
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
            categories: chartData.dates || []
        },
        colors: ['#299cdb'],
    };

    if (charts.activeUsers) {
        charts.activeUsers.destroy();
    }
    charts.activeUsers = new ApexCharts(document.querySelector("#activeUsersChart"), options);
    charts.activeUsers.render();
}

// Update data table
function updateDataTable() {
    const tbody = document.querySelector('#chartDataTable tbody');
    tbody.innerHTML = '';
    
    if (chartData && chartData.dates && chartData.dates.length > 0) {
        chartData.dates.forEach((date, index) => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${date}</td>
                <td>${chartData.user_registrations[index] || 0}</td>
                <td>$${chartData.daily_revenue[index] || 0}</td>
                <td>${chartData.video_views[index] || 0}</td>
                <td>$${chartData.daily_investments_amount[index] || 0}</td>
                <td>${chartData.active_users[index] || 0}</td>
            `;
        });
    } else {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td colspan="6" class="text-center text-muted">No data available</td>
        `;
    }
}

// Update JSON display
function updateJsonDisplay() {
    const jsonDisplay = document.getElementById('jsonDataDisplay');
    if (chartData && Object.keys(chartData).length > 0) {
        jsonDisplay.textContent = JSON.stringify(chartData, null, 2);
    } else {
        jsonDisplay.textContent = 'No data available or failed to load data.';
    }
}

// Initialize auto refresh
function initializeAutoRefresh() {
    clearInterval(autoRefreshInterval);
    autoRefreshInterval = setInterval(() => {
        console.log('Auto refreshing data...');
        loadChartData();
    }, 30000); // Refresh every 30 seconds
}

// Export data function
function exportData() {
    const dataStr = JSON.stringify(chartData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'analytics-chart-data-' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    clearInterval(autoRefreshInterval);
});
</script>
@endpush
@endsection

