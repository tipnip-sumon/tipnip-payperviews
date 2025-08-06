@extends('components.layout')

@section('page-title', $pageTitle)

@section('breadcrumb')
<div class="page-header d-sm-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ $pageTitle }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fe fe-home me-2"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.email-campaigns.index') }}">Email Campaigns</a></li>
            <li class="breadcrumb-item active" aria-current="page">Analytics</li>
        </ol>
    </div>
    <div class="page-rightheader ml-md-auto">
        <div class="btn-list">
            <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-outline-primary">
                <i class="fe fe-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-primary" onclick="exportAnalytics()">
                <i class="fe fe-download me-2"></i>Export Report
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Email Performance Overview -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📊 Email Campaign Performance</h4>
                <div class="card-options">
                    <select class="form-select" id="timeRange" onchange="updateCharts()">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <canvas id="emailPerformanceChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Campaign Types Distribution -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Campaign Distribution</h4>
            </div>
            <div class="card-body">
                <canvas id="campaignDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Email Success Rates -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">✅ Success Rates by Campaign Type</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="counter-icon bg-warning-transparent">
                                    <i class="fe fe-credit-card text-warning"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">KYC Reminders</h6>
                                <div class="progress progress-sm mb-1">
                                    <div class="progress-bar bg-warning" style="width: 92%"></div>
                                </div>
                                <small class="text-muted">92% Success Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="counter-icon bg-danger-transparent">
                                    <i class="fe fe-user-x text-danger"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">Inactive Users</h6>
                                <div class="progress progress-sm mb-1">
                                    <div class="progress-bar bg-danger" style="width: 88%"></div>
                                </div>
                                <small class="text-muted">88% Success Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="counter-icon bg-info-transparent">
                                    <i class="fe fe-lock text-info"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">Password Resets</h6>
                                <div class="progress progress-sm mb-1">
                                    <div class="progress-bar bg-info" style="width: 95%"></div>
                                </div>
                                <small class="text-muted">95% Success Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="counter-icon bg-success-transparent">
                                    <i class="fe fe-award text-success"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-1">Congratulations</h6>
                                <div class="progress progress-sm mb-1">
                                    <div class="progress-bar bg-success" style="width: 98%"></div>
                                </div>
                                <small class="text-muted">98% Success Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Email Activity -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">🕒 Recent Email Activity</h4>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">KYC Reminder Campaign</h6>
                            <small class="text-muted">Sent to 25 users • 2 hours ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Password Reset Reminders</h6>
                            <small class="text-muted">Sent to 12 users • 5 hours ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Inactive User Campaign</h6>
                            <small class="text-muted">Sent to 8 users • 1 day ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Investment Congratulations</h6>
                            <small class="text-muted">Auto-sent to 3 users • 1 day ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Statistics -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">📈 Monthly Email Statistics</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>KYC Reminders</th>
                                <th>Password Resets</th>
                                <th>Inactive Users</th>
                                <th>Congratulations</th>
                                <th>Total Sent</th>
                                <th>Success Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>August 2025</strong></td>
                                <td><span class="badge badge-warning">145</span></td>
                                <td><span class="badge badge-info">89</span></td>
                                <td><span class="badge badge-danger">67</span></td>
                                <td><span class="badge badge-success">234</span></td>
                                <td><strong>535</strong></td>
                                <td><span class="badge badge-success">94.2%</span></td>
                            </tr>
                            <tr>
                                <td>July 2025</td>
                                <td><span class="badge badge-warning">198</span></td>
                                <td><span class="badge badge-info">123</span></td>
                                <td><span class="badge badge-danger">89</span></td>
                                <td><span class="badge badge-success">312</span></td>
                                <td><strong>722</strong></td>
                                <td><span class="badge badge-success">93.8%</span></td>
                            </tr>
                            <tr>
                                <td>June 2025</td>
                                <td><span class="badge badge-warning">176</span></td>
                                <td><span class="badge badge-info">98</span></td>
                                <td><span class="badge badge-danger">76</span></td>
                                <td><span class="badge badge-success">289</span></td>
                                <td><strong>639</strong></td>
                                <td><span class="badge badge-success">92.1%</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJsScripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Email Performance Chart
    const performanceCtx = document.getElementById('emailPerformanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Emails Sent',
                data: [120, 190, 150, 220],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: 'Successful Deliveries',
                data: [115, 182, 142, 210],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
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

    // Campaign Distribution Chart
    const distributionCtx = document.getElementById('campaignDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['KYC Reminders', 'Password Resets', 'Inactive Users', 'Congratulations'],
            datasets: [{
                data: [35, 20, 15, 30],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
                ],
                borderWidth: 2
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
}

function updateCharts() {
    const timeRange = document.getElementById('timeRange').value;
    // Update charts based on time range
    console.log('Updating charts for time range:', timeRange);
    // Implementation would fetch new data and update charts
}

function exportAnalytics() {
    showNotification('Preparing analytics export...', 'info');
    
    setTimeout(() => {
        // Create CSV content
        const csvContent = "data:text/csv;charset=utf-8,Campaign Type,Emails Sent,Success Rate,Month\n" +
            "KYC Reminders,145,92%,August 2025\n" +
            "Password Resets,89,95%,August 2025\n" +
            "Inactive Users,67,88%,August 2025\n" +
            "Congratulations,234,98%,August 2025";

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "email_campaign_analytics_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showNotification('Analytics exported successfully!', 'success');
    }, 1000);
}

function showNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Success!' : type === 'info' ? 'Info' : 'Notice',
            text: message,
            icon: type,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.progress-sm {
    height: 6px;
}
</style>
@endsection
