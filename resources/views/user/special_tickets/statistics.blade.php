<x-smart_layout>

@section('title', $pageTitle ?? 'All Tickets Statistics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header bg-gradient-primary rounded-3 text-white p-4 mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title mb-0">
                            <i class="fas fa-chart-line me-3"></i>All Tickets Statistics
                        </h1>
                        <p class="page-subtitle mb-0 opacity-75">Comprehensive analytics of all your tickets performance across all plans</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('special.tickets.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-1"></i>Dashboard
                            </a>
                            <a href="{{ route('special.tickets.history') }}" class="btn btn-outline-light">
                                <i class="fe fe-clock me-1"></i>View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Type Overview Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-ticket-alt text-primary me-2"></i>Ticket Type Overview
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Lottery Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card lottery">
                                <div class="ticket-icon">
                                    <i class="fas fa-dice"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['lottery']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">Lottery Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['lottery']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Special Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card special">
                                <div class="ticket-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['special']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">Special Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['special']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card discount">
                                <div class="ticket-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['discount']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">Discount Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['discount']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Bonus Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card bonus">
                                <div class="ticket-icon">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['bonus']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">Bonus Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['bonus']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Premium Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card premium">
                                <div class="ticket-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['premium']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">Premium Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['premium']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- VIP Tickets -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="ticket-type-card vip">
                                <div class="ticket-icon">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div class="ticket-info">
                                    <h4 class="mb-1">{{ $ticketStats['vip']['count'] ?? 0 }}</h4>
                                    <p class="mb-1">VIP Tickets</p>
                                    <small class="text-muted">${{ number_format($ticketStats['vip']['value'] ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Row -->
    <div class="row mb-4">
        <!-- Total Tickets -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-ticket-alt" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $stats['total_tickets'] ?? 0 }}</h3>
                            <p class="mb-1">Total Tickets</p>
                            <small class="opacity-75">All types included</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Investment -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-dollar-sign" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">${{ number_format($stats['total_investment'] ?? 0, 2) }}</h3>
                            <p class="mb-1">Total Investment</p>
                            <small class="opacity-75">Across all plans</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tickets -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-zap" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $stats['active_tickets'] ?? 0 }}</h3>
                            <p class="mb-1">Active Tickets</p>
                            <small class="opacity-75">Ready to use</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Value -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-coins" style="font-size: 2.5rem;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">${{ number_format($stats['total_ticket_value'] ?? 0, 2) }}</h3>
                            <p class="mb-1">Available Value</p>
                            <small class="opacity-75">Unused potential</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan-wise Statistics -->
    @if(($planStats ?? collect())->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-layer-group text-info me-2"></i>Plan-wise Performance
                        </h4>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" data-filter="all">All Plans</button>
                            <button class="btn btn-outline-primary" data-filter="active">Active Only</button>
                            <button class="btn btn-outline-primary" data-filter="premium">Premium</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($planStats as $planId => $data)
                        <div class="col-lg-4 col-md-6">
                            <div class="plan-card {{ $data['status'] ?? 'active' }}">
                                <div class="plan-header">
                                    <div class="plan-info">
                                        <h5 class="plan-name">{{ $data['plan_name'] ?? 'Unknown Plan' }}</h5>
                                        <span class="plan-status badge bg-{{ $data['status'] === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($data['status'] ?? 'active') }}
                                        </span>
                                    </div>
                                    <div class="plan-icon">
                                        <i class="fas fa-{{ $data['icon'] ?? 'box' }}"></i>
                                    </div>
                                </div>
                                
                                <div class="plan-stats">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="stat-value">{{ $data['total_tickets'] ?? 0 }}</div>
                                            <div class="stat-label">Tickets</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-value">{{ $data['used_tickets'] ?? 0 }}</div>
                                            <div class="stat-label">Used</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-value">${{ number_format($data['total_value'] ?? 0, 0) }}</div>
                                            <div class="stat-label">Value</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="plan-details">
                                    <div class="detail-row">
                                        <span>Utilization Rate:</span>
                                        <span class="fw-bold">{{ $data['utilization_rate'] ?? 0 }}%</span>
                                    </div>
                                    <div class="detail-row">
                                        <span>Discount Earned:</span>
                                        <span class="text-success fw-bold">${{ number_format($data['discount_earned'] ?? 0, 2) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span>Bonus Received:</span>
                                        <span class="text-warning fw-bold">${{ number_format($data['bonus_received'] ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <div class="plan-progress">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Usage Progress</small>
                                        <small>{{ $data['utilization_rate'] ?? 0 }}%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-gradient-primary" style="width: {{ $data['utilization_rate'] ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="plan-actions mt-3">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fe fe-eye me-1"></i>View Details
                                        </button>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fe fe-share me-1"></i>Share
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Usage Analytics -->
    <div class="row mb-4">
        <!-- Usage Distribution -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Usage Distribution
                    </h4>
                </div>
                <div class="card-body">
                    <div class="usage-stats">
                        <div class="usage-item">
                            <div class="usage-info">
                                <div class="usage-label">
                                    <i class="fas fa-check-circle text-success me-2"></i>Used Tickets
                                </div>
                                <div class="usage-value">
                                    <span class="h4 text-success">{{ $stats['used_tickets'] ?? 0 }}</span>
                                    <small class="text-muted">tickets</small>
                                </div>
                            </div>
                            <div class="usage-progress">
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ ($stats['total_tickets'] ?? 0) > 0 ? (($stats['used_tickets'] ?? 0) / $stats['total_tickets']) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ ($stats['total_tickets'] ?? 0) > 0 ? number_format((($stats['used_tickets'] ?? 0) / $stats['total_tickets']) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>

                        <div class="usage-item">
                            <div class="usage-info">
                                <div class="usage-label">
                                    <i class="fas fa-clock text-warning me-2"></i>Pending Tickets
                                </div>
                                <div class="usage-value">
                                    <span class="h4 text-warning">{{ $stats['pending_tickets'] ?? 0 }}</span>
                                    <small class="text-muted">tickets</small>
                                </div>
                            </div>
                            <div class="usage-progress">
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ ($stats['total_tickets'] ?? 0) > 0 ? (($stats['pending_tickets'] ?? 0) / $stats['total_tickets']) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ ($stats['total_tickets'] ?? 0) > 0 ? number_format((($stats['pending_tickets'] ?? 0) / $stats['total_tickets']) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>

                        <div class="usage-item">
                            <div class="usage-info">
                                <div class="usage-label">
                                    <i class="fas fa-times-circle text-danger me-2"></i>Expired Tickets
                                </div>
                                <div class="usage-value">
                                    <span class="h4 text-danger">{{ $stats['expired_tickets'] ?? 0 }}</span>
                                    <small class="text-muted">tickets</small>
                                </div>
                            </div>
                            <div class="usage-progress">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width: {{ ($stats['total_tickets'] ?? 0) > 0 ? (($stats['expired_tickets'] ?? 0) / $stats['total_tickets']) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ ($stats['total_tickets'] ?? 0) > 0 ? number_format((($stats['expired_tickets'] ?? 0) / $stats['total_tickets']) * 100, 1) : 0 }}% of total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share Statistics -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-share-alt text-info me-2"></i>Share Statistics
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="h3 text-success">{{ $shareStats['total_shared'] ?? 0 }}</div>
                            <small class="text-muted">Tickets Shared</small>
                        </div>
                        <div class="col-4">
                            <div class="h3 text-info">{{ $shareStats['total_received'] ?? 0 }}</div>
                            <small class="text-muted">Tickets Received</small>
                        </div>
                        <div class="col-4">
                            <div class="h3 text-warning">{{ $shareStats['pending_shares'] ?? 0 }}</div>
                            <small class="text-muted">Pending Shares</small>
                        </div>
                    </div>

                    <div class="share-activity">
                        <div class="activity-item">
                            <div class="activity-icon bg-success-soft text-success">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Outgoing Shares</div>
                                <div class="activity-meta">
                                    <span class="fw-bold">${{ number_format($shareStats['outgoing_value'] ?? 0, 2) }}</span>
                                    <small class="text-muted">total value shared</small>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon bg-info-soft text-info">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Incoming Shares</div>
                                <div class="activity-meta">
                                    <span class="fw-bold">${{ number_format($shareStats['incoming_value'] ?? 0, 2) }}</span>
                                    <small class="text-muted">total value received</small>
                                </div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon bg-warning-soft text-warning">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Share Network</div>
                                <div class="activity-meta">
                                    <span class="fw-bold">{{ $shareStats['unique_partners'] ?? 0 }}</span>
                                    <small class="text-muted">unique share partners</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-trophy text-warning me-2"></i>Performance Summary
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Metric</th>
                                    <th>Current Value</th>
                                    <th>Percentage</th>
                                    <th>Performance Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chart-line text-primary me-2"></i>
                                            <strong>Ticket Utilization Rate</strong>
                                        </div>
                                    </td>
                                    <td>{{ $stats['used_tickets'] ?? 0 }} / {{ $stats['total_tickets'] ?? 0 }}</td>
                                    <td>
                                        <span class="fw-bold">{{ ($stats['total_tickets'] ?? 0) > 0 ? number_format((($stats['used_tickets'] ?? 0) / $stats['total_tickets']) * 100, 1) : 0 }}%</span>
                                    </td>
                                    <td>
                                        @php $utilizationRate = ($stats['total_tickets'] ?? 0) > 0 ? (($stats['used_tickets'] ?? 0) / $stats['total_tickets']) : 0; @endphp
                                        @if($utilizationRate > 0.8)
                                            <span class="badge bg-success"><i class="fas fa-star me-1"></i>Excellent</span>
                                        @elseif($utilizationRate > 0.6)
                                            <span class="badge bg-warning"><i class="fas fa-thumbs-up me-1"></i>Good</span>
                                        @elseif($utilizationRate > 0.3)
                                            <span class="badge bg-info"><i class="fas fa-arrow-up me-1"></i>Average</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-exclamation me-1"></i>Needs Improvement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fe fe-eye me-1"></i>View Details
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-coins text-success me-2"></i>
                                            <strong>Investment Efficiency</strong>
                                        </div>
                                    </td>
                                    <td>${{ number_format($stats['total_discount_used'] ?? 0, 2) }}</td>
                                    <td>
                                        <span class="fw-bold">{{ ($stats['total_investment'] ?? 0) > 0 ? number_format((($stats['total_discount_used'] ?? 0) / $stats['total_investment']) * 100, 1) : 0 }}%</span>
                                    </td>
                                    <td>
                                        @php $efficiencyRate = ($stats['total_investment'] ?? 0) > 0 ? (($stats['total_discount_used'] ?? 0) / $stats['total_investment']) : 0; @endphp
                                        @if($efficiencyRate > 0.9)
                                            <span class="badge bg-success"><i class="fas fa-star me-1"></i>Outstanding</span>
                                        @elseif($efficiencyRate > 0.7)
                                            <span class="badge bg-warning"><i class="fas fa-thumbs-up me-1"></i>Great</span>
                                        @elseif($efficiencyRate > 0.5)
                                            <span class="badge bg-info"><i class="fas fa-check me-1"></i>Good</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-arrow-up me-1"></i>Can Improve</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fe fe-trending-up me-1"></i>Optimize
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-share-alt text-info me-2"></i>
                                            <strong>Share Activity</strong>
                                        </div>
                                    </td>
                                    <td>{{ ($shareStats['total_shared'] ?? 0) + ($shareStats['total_received'] ?? 0) }} tickets</td>
                                    <td>
                                        <span class="fw-bold">{{ ($stats['total_tickets'] ?? 0) > 0 ? number_format(((($shareStats['total_shared'] ?? 0) + ($shareStats['total_received'] ?? 0)) / $stats['total_tickets']) * 100, 1) : 0 }}%</span>
                                    </td>
                                    <td>
                                        @if(($shareStats['total_shared'] ?? 0) > 0 || ($shareStats['total_received'] ?? 0) > 0)
                                            <span class="badge bg-success"><i class="fas fa-users me-1"></i>Active Sharer</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-user me-1"></i>Solo User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('special.tickets.transfer') }}" class="btn btn-sm btn-outline-info">
                                            <i class="fe fe-share me-1"></i>Share Now
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

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.ticket-type-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
}

.ticket-type-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.ticket-type-card.lottery { border-color: #8b5cf6; }
.ticket-type-card.special { border-color: #f59e0b; }
.ticket-type-card.discount { border-color: #10b981; }
.ticket-type-card.bonus { border-color: #ef4444; }
.ticket-type-card.premium { border-color: #3b82f6; }
.ticket-type-card.vip { border-color: #8b5cf6; }

.ticket-icon {
    width: 3rem;
    height: 3rem;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    font-size: 1.5rem;
    color: #6b7280;
}

.lottery .ticket-icon { background: linear-gradient(135deg, #8b5cf6, #a855f7); color: white; }
.special .ticket-icon { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.discount .ticket-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.bonus .ticket-icon { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.premium .ticket-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.vip .ticket-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }

.plan-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
}

.plan-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.plan-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.plan-info {
    flex: 1;
}

.plan-name {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #374151;
}

.plan-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #6b7280;
}

.plan-stats {
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #374151;
}

.stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.plan-details {
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem 0;
    font-size: 0.875rem;
}

.plan-progress {
    margin-bottom: 1rem;
}

.usage-stats {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.usage-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.usage-info {
    flex: 1;
}

.usage-progress {
    flex: 2;
}

.usage-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.usage-value {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.share-activity {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.activity-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.bg-success-soft { background-color: rgba(16, 185, 129, 0.1); }
.bg-info-soft { background-color: rgba(6, 182, 212, 0.1); }
.bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.activity-meta {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .ticket-type-card {
        margin-bottom: 1rem;
    }
    
    .usage-item {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .plan-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@endsection
</x-smart_layout>