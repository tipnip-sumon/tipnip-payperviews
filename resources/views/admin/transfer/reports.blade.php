@extends('components.layout')

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fe fe-bar-chart-2 me-2"></i>Transfer Reports & Analytics
                </h4>
                <div class="d-flex gap-2">
                    <select class="form-select" id="period-selector" style="width: auto;">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                    <button class="btn btn-primary" onclick="refreshReports()">
                        <i class="fe fe-refresh-cw me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Statistics -->
                <div class="row mb-4" id="stats-summary">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="fe fe-send fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1" id="total-transfers">{{ $transfers->sum('count') }}</h4>
                                <small class="text-muted">Total Transfers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fe fe-dollar-sign fa-2x text-success mb-2"></i>
                                <h4 class="mb-1" id="total-amount">${{ number_format($transfers->sum('total'), 2) }}</h4>
                                <small class="text-muted">Total Amount</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="fe fe-trending-up fa-2x text-info mb-2"></i>
                                <h4 class="mb-1" id="avg-amount">${{ $transfers->count() > 0 ? number_format($transfers->sum('total') / $transfers->sum('count'), 2) : '0.00' }}</h4>
                                <small class="text-muted">Average Transfer</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <i class="fe fe-calendar fa-2x text-warning mb-2"></i>
                                <h4 class="mb-1" id="period-count">{{ $transfers->count() }}</h4>
                                <small class="text-muted">{{ ucfirst($period) }} Periods</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Transfer Volume Chart</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="transferChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Amount Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="amountChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Detailed Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="reports-table">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Transfer Count</th>
                                        <th>Total Amount</th>
                                        <th>Average Amount</th>
                                        <th>Growth %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $index => $transfer)
                                    <tr>
                                        <td>
                                            @if($period == 'daily')
                                                {{ \Carbon\Carbon::parse($transfer->date)->format('M d, Y') }}
                                            @elseif($period == 'weekly')
                                                Week {{ $transfer->week }}
                                            @else
                                                {{ \DateTime::createFromFormat('!m', $transfer->month)->format('F') }} {{ $transfer->year }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ number_format($transfer->count) }}</span>
                                        </td>
                                        <td class="text-success font-weight-bold">
                                            ${{ number_format($transfer->total, 2) }}
                                        </td>
                                        <td>
                                            ${{ $transfer->count > 0 ? number_format($transfer->total / $transfer->count, 2) : '0.00' }}
                                        </td>
                                        <td>
                                            @php
                                                $previousIndex = $index + 1;
                                                $growth = 0;
                                                if (isset($transfers[$previousIndex])) {
                                                    $prevTotal = $transfers[$previousIndex]->total;
                                                    if ($prevTotal > 0) {
                                                        $growth = (($transfer->total - $prevTotal) / $prevTotal) * 100;
                                                    }
                                                }
                                            @endphp
                                            @if($growth > 0)
                                                <span class="text-success">
                                                    <i class="fe fe-trending-up"></i> +{{ number_format($growth, 1) }}%
                                                </span>
                                            @elseif($growth < 0)
                                                <span class="text-danger">
                                                    <i class="fe fe-trending-down"></i> {{ number_format($growth, 1) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    document.getElementById('period-selector').addEventListener('change', function() {
        const period = this.value;
        window.location.href = `{{ route('admin.transfer_reports') }}?period=${period}`;
    });
});

function refreshReports() {
    const period = document.getElementById('period-selector').value;
    window.location.href = `{{ route('admin.transfer_reports') }}?period=${period}`;
}

function initializeCharts() {
    const transferData = @json($transfers);
    
    // Transfer Volume Chart
    const transferCtx = document.getElementById('transferChart').getContext('2d');
    const transferChart = new Chart(transferCtx, {
        type: 'line',
        data: {
            labels: transferData.map(item => {
                if ('{{ $period }}' === 'daily') {
                    return new Date(item.date).toLocaleDateString();
                } else if ('{{ $period }}' === 'weekly') {
                    return `Week ${item.week}`;
                } else {
                    return `${item.year}-${String(item.month).padStart(2, '0')}`;
                }
            }).reverse(),
            datasets: [{
                label: 'Transfer Count',
                data: transferData.map(item => item.count).reverse(),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
    
    // Amount Distribution Chart
    const amountCtx = document.getElementById('amountChart').getContext('2d');
    const amountChart = new Chart(amountCtx, {
        type: 'doughnut',
        data: {
            labels: transferData.slice(0, 5).map(item => {
                if ('{{ $period }}' === 'daily') {
                    return new Date(item.date).toLocaleDateString();
                } else if ('{{ $period }}' === 'weekly') {
                    return `Week ${item.week}`;
                } else {
                    return `${item.year}-${String(item.month).padStart(2, '0')}`;
                }
            }),
            datasets: [{
                data: transferData.slice(0, 5).map(item => item.total),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#FF9F40',
                    '#FF6384'
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
}
</script>
@endsection
