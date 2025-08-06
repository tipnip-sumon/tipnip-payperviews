@extends('components.layout')

@section('page-title', 'Modal Analytics')

@section('style')
<style>
    .analytics-card {
        transition: transform 0.2s;
    }
    .analytics-card:hover {
        transform: translateY(-2px);
    }
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    .chart-container {
        height: 300px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Modal Analytics</h2>
                    <p class="text-muted">Track modal performance and user interactions</p>
                </div>
                <div>
                    <a href="{{ route('admin.modal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button class="btn btn-primary" onclick="refreshAnalytics()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Total Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-window-restore fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Active Modals</h6>
                            <h3 class="mb-0">{{ $modalSettings->where('is_active', 1)->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Total Shows Today</h6>
                            <h3 class="mb-0">{{ $analytics->sum('shows_today') }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-eye fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Total Clicks Today</h6>
                            <h3 class="mb-0">{{ $analytics->sum('clicks_today') }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-mouse-pointer fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Performance Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Modal Performance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Modal Name</th>
                            <th>Status</th>
                            <th>Shows Today</th>
                            <th>Clicks Today</th>
                            <th>Dismissals Today</th>
                            <th>Conversion Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analytics as $data)
                            <tr>
                                <td>
                                    <strong>{{ $data['title'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $data['modal_name'] }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $data['is_active'] ? 'bg-success' : 'bg-danger' }}">
                                        {{ $data['is_active'] ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $data['shows_today'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ $data['clicks_today'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-warning">{{ $data['dismissals_today'] }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-info">{{ $data['conversion_rate'] }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('{{ $data['modal_name'] }}')">
                                        <i class="fas fa-chart-line"></i> Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card analytics-card">
                <div class="card-header">
                    <h6 class="mb-0">Modal Activity Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card analytics-card">
                <div class="card-header">
                    <h6 class="mb-0">Conversion Rates</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="conversionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">Daily Average</h5>
                                    <h3 class="mb-0">{{ number_format($analytics->avg('shows_today'), 1) }}</h3>
                                    <small class="text-muted">Shows per modal</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-success">Best Performer</h5>
                                    <h6 class="mb-0">{{ $analytics->sortByDesc('shows_today')->first()['title'] ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ $analytics->sortByDesc('shows_today')->first()['shows_today'] ?? 0 }} shows today</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-info">Avg Conversion</h5>
                                    <h3 class="mb-0">{{ number_format($analytics->avg('clicks_today') / max($analytics->avg('shows_today'), 1) * 100, 1) }}%</h3>
                                    <small class="text-muted">Click-through rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Analytics Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Activity Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'bar',
    data: {
        labels: @json($analytics->pluck('title')),
        datasets: [{
            label: 'Shows Today',
            data: @json($analytics->pluck('shows_today')),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Clicks Today',
            data: @json($analytics->pluck('clicks_today')),
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Conversion Chart
const conversionCtx = document.getElementById('conversionChart').getContext('2d');
new Chart(conversionCtx, {
    type: 'doughnut',
    data: {
        labels: @json($analytics->pluck('title')),
        datasets: [{
            data: @json($analytics->pluck('clicks_today')),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function viewDetails(modalName) {
    // Mock detail view - you can enhance this with real data
    const detailContent = `
        <h6>Detailed Analytics for: ${modalName}</h6>
        <div class="row">
            <div class="col-md-6">
                <h6>Performance Metrics</h6>
                <ul class="list-unstyled">
                    <li><strong>Total Views:</strong> ${Math.floor(Math.random() * 1000)}</li>
                    <li><strong>Unique Views:</strong> ${Math.floor(Math.random() * 500)}</li>
                    <li><strong>Click-through Rate:</strong> ${(Math.random() * 50).toFixed(2)}%</li>
                    <li><strong>Dismissal Rate:</strong> ${(Math.random() * 30).toFixed(2)}%</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>User Segments</h6>
                <ul class="list-unstyled">
                    <li><strong>Mobile Users:</strong> ${Math.floor(Math.random() * 60 + 40)}%</li>
                    <li><strong>Desktop Users:</strong> ${Math.floor(Math.random() * 40 + 20)}%</li>
                    <li><strong>New Users:</strong> ${Math.floor(Math.random() * 30 + 20)}%</li>
                    <li><strong>Returning Users:</strong> ${Math.floor(Math.random() * 70 + 30)}%</li>
                </ul>
            </div>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = detailContent;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

function refreshAnalytics() {
    location.reload();
}
</script>
@endpush
