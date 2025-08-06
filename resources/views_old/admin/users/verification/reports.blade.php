@extends('components.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center">
                        <div class="d-flex align-items-center mb-2 mb-lg-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.users.verification.reports.export', request()->all()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download me-1"></i>
                                <span class="d-none d-sm-inline">Export Report</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                <!-- Date Range Filter -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.users.verification.reports') }}" class="row g-3 align-items-end">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-1"></i>From Date
                                        </label>
                                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-1"></i>To Date
                                        </label>
                                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i>Filter Reports
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Overview -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card bg-primary text-white border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="h3 mb-0 text-white">{{ $stats['total_users'] }}</div>
                                    <div class="small text-white-50">Total Users (Period)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card bg-success text-white border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3">
                                        <i class="fas fa-envelope-open fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="h3 mb-0 text-white">{{ $stats['email_verified'] }}</div>
                                    <div class="small text-white-50">Email Verified</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card bg-info text-white border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-white bg-opacity-25 rounded p-3">
                                        <i class="fas fa-sms fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="h3 mb-0 text-white">{{ $stats['sms_verified'] }}</div>
                                    <div class="small text-white-50">SMS Verified</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card bg-warning text-dark border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-dark bg-opacity-25 rounded p-3">
                                        <i class="fas fa-id-card fa-2x text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="h3 mb-0">{{ $stats['kyc_verified'] }}</div>
                                    <div class="small text-dark-50">KYC Verified</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                <!-- Verification Trends Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line me-2 text-primary"></i>
                                    <h5 class="card-title mb-0">User Registration Trend</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height: 300px;">
                                    <canvas id="verificationTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Status Breakdown -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-pie me-2 text-info"></i>
                                    <h5 class="card-title mb-0">Verification Status Breakdown</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height: 300px;">
                                    <canvas id="verificationStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tasks me-2 text-success"></i>
                                    <h5 class="card-title mb-0">Verification Progress</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="verification-progress">
                                    <div class="progress-item mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-envelope me-2 text-success"></i>
                                                <span class="fw-semibold">Email Verification</span>
                                            </div>
                                            <span class="badge bg-success">{{ $stats['total_users'] > 0 ? round(($stats['email_verified'] / $stats['total_users']) * 100, 2) : 0 }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $stats['total_users'] > 0 ? ($stats['email_verified'] / $stats['total_users']) * 100 : 0 }}%"
                                                 aria-valuenow="{{ $stats['total_users'] > 0 ? ($stats['email_verified'] / $stats['total_users']) * 100 : 0 }}" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-item mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-sms me-2 text-info"></i>
                                                <span class="fw-semibold">SMS Verification</span>
                                            </div>
                                            <span class="badge bg-info">{{ $stats['total_users'] > 0 ? round(($stats['sms_verified'] / $stats['total_users']) * 100, 2) : 0 }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ $stats['total_users'] > 0 ? ($stats['sms_verified'] / $stats['total_users']) * 100 : 0 }}%"
                                                 aria-valuenow="{{ $stats['total_users'] > 0 ? ($stats['sms_verified'] / $stats['total_users']) * 100 : 0 }}" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-id-card me-2 text-warning"></i>
                                                <span class="fw-semibold">KYC Verification</span>
                                            </div>
                                            <span class="badge bg-warning text-dark">{{ $stats['total_users'] > 0 ? round(($stats['kyc_verified'] / $stats['total_users']) * 100, 2) : 0 }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ $stats['total_users'] > 0 ? ($stats['kyc_verified'] / $stats['total_users']) * 100 : 0 }}%"
                                                 aria-valuenow="{{ $stats['total_users'] > 0 ? ($stats['kyc_verified'] / $stats['total_users']) * 100 : 0 }}" 
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-table me-2 text-primary"></i>
                                    <h5 class="card-title mb-0">Verification Summary</h5>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0 fw-semibold">Verification Type</th>
                                                <th class="border-0 fw-semibold">Verified Users</th>
                                                <th class="border-0 fw-semibold">Unverified Users</th>
                                                <th class="border-0 fw-semibold">Completion Rate</th>
                                                <th class="border-0 fw-semibold text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-envelope text-success me-2"></i>
                                                        <span class="fw-semibold">Email Verification</span>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-success-subtle text-success px-3 py-2">{{ $stats['email_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">{{ $stats['total_users'] - $stats['email_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-success fw-bold me-2">
                                                            {{ $stats['total_users'] > 0 ? round(($stats['email_verified'] / $stats['total_users']) * 100, 2) : 0 }}%
                                                        </span>
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $stats['total_users'] > 0 ? ($stats['email_verified'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('admin.users.verification.email') }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cog me-1"></i>
                                                        <span class="d-none d-md-inline">Manage</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-sms text-info me-2"></i>
                                                        <span class="fw-semibold">SMS Verification</span>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-success-subtle text-success px-3 py-2">{{ $stats['sms_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">{{ $stats['total_users'] - $stats['sms_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-info fw-bold me-2">
                                                            {{ $stats['total_users'] > 0 ? round(($stats['sms_verified'] / $stats['total_users']) * 100, 2) : 0 }}%
                                                        </span>
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar bg-info" style="width: {{ $stats['total_users'] > 0 ? ($stats['sms_verified'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('admin.users.verification.sms') }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-cog me-1"></i>
                                                        <span class="d-none d-md-inline">Manage</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-id-card text-warning me-2"></i>
                                                        <span class="fw-semibold">KYC Verification</span>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-success-subtle text-success px-3 py-2">{{ $stats['kyc_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">{{ $stats['total_users'] - $stats['kyc_verified'] }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-warning fw-bold me-2">
                                                            {{ $stats['total_users'] > 0 ? round(($stats['kyc_verified'] / $stats['total_users']) * 100, 2) : 0 }}%
                                                        </span>
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar bg-warning" style="width: {{ $stats['total_users'] > 0 ? ($stats['kyc_verified'] / $stats['total_users']) * 100 : 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('admin.users.verification.kyc') }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-cog me-1"></i>
                                                        <span class="d-none d-md-inline">Manage</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    'use strict';

    // Trend Chart Data
    const trendData = {!! json_encode($trendData) !!};
    
    // Enhanced Chart Configuration
    const chartColors = {
        primary: 'rgb(13, 110, 253)',
        success: 'rgb(25, 135, 84)',
        info: 'rgb(13, 202, 240)',
        warning: 'rgb(255, 193, 7)',
        danger: 'rgb(220, 53, 69)'
    };

    // User Registration Trend Chart
    const trendCtx = document.getElementById('verificationTrendChart').getContext('2d');
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(item => item.date),
            datasets: [{
                label: 'New Users',
                data: trendData.map(item => item.total),
                borderColor: chartColors.primary,
                backgroundColor: chartColors.primary + '20',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: chartColors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            elements: {
                line: {
                    borderWidth: 3
                }
            }
        }
    });

    // Verification Status Doughnut Chart
    const statusCtx = document.getElementById('verificationStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Email Verified', 'SMS Verified', 'KYC Verified', 'Unverified'],
            datasets: [{
                data: [
                    {{ $stats['email_verified'] }},
                    {{ $stats['sms_verified'] }},
                    {{ $stats['kyc_verified'] }},
                    {{ $stats['total_users'] - $stats['email_verified'] - $stats['sms_verified'] - $stats['kyc_verified'] }}
                ],
                backgroundColor: [
                    chartColors.success,
                    chartColors.info,
                    chartColors.warning,
                    chartColors.danger
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 5,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Responsive chart resize
    function resizeCharts() {
        if (trendChart) trendChart.resize();
        if (statusChart) statusChart.resize();
    }

    // Handle window resize
    window.addEventListener('resize', resizeCharts);

    // Handle tab visibility change to ensure charts render properly
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            setTimeout(resizeCharts, 100);
        }
    });

    // Enhanced table interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Add loading states to buttons
        const manageButtons = document.querySelectorAll('.btn');
        manageButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (this.href) {
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
                    this.disabled = true;
                    
                    // Reset after navigation
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                    }, 2000);
                }
            });
        });
    });
</script>
@endpush
