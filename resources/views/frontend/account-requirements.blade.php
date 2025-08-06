<x-smart_layout>
    @section('top_title', 'Account Requirements')
    @section('title', 'Account Requirements')
    
    @push('styles')
    <style>
        .requirement-card {
            border: none;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .requirement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .requirement-completed {
            border-left: 4px solid #28a745;
        }
        
        .requirement-pending {
            border-left: 4px solid #ffc107;
        }
        
        .requirement-failed {
            border-left: 4px solid #dc3545;
        }
        
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .feature-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .progress-ring {
            width: 120px;
            height: 120px;
        }
        
        .progress-circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
    @endpush
    
    @section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-shield-check fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Account Requirements</h3>
                            <p class="mb-0">Complete these requirements to unlock transfer and withdrawal features</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Summary -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card requirement-card text-center">
                <div class="card-body py-4">
                    <div class="progress-ring mx-auto mb-3">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-circle" cx="60" cy="60" r="54" 
                                    style="stroke: #e9ecef; stroke-dasharray: 339.292; stroke-dashoffset: 0;"></circle>
                            <circle class="progress-circle" cx="60" cy="60" r="54" 
                                    style="stroke: #28a745; stroke-dasharray: 339.292; stroke-dashoffset: {{ 339.292 * (1 - ($summary['overall_status']['completed_requirements'] / $summary['overall_status']['total_requirements'])) }};"></circle>
                        </svg>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h3 class="mb-0 text-primary">{{ round(($summary['overall_status']['completed_requirements'] / $summary['overall_status']['total_requirements']) * 100) }}%</h3>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>
                    <h5 class="text-primary">Overall Progress</h5>
                    <p class="text-muted mb-0">{{ $summary['overall_status']['completed_requirements'] }} of {{ $summary['overall_status']['total_requirements'] }} requirements completed</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card requirement-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="status-icon {{ $summary['transfer']['allowed'] ? 'status-completed' : 'status-pending' }} me-3">
                            <i class="fas fa-{{ $summary['transfer']['allowed'] ? 'check' : 'clock' }}"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Transfer Status</h6>
                            <p class="mb-0 text-{{ $summary['transfer']['allowed'] ? 'success' : 'warning' }}">
                                {{ $summary['transfer']['allowed'] ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>
                    </div>
                    @if($summary['transfer']['allowed'])
                        <div class="mt-3">
                            <span class="feature-badge">
                                <i class="fas fa-exchange-alt me-1"></i>
                                Ready to Transfer
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card requirement-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="status-icon {{ $summary['withdrawal']['allowed'] ? 'status-completed' : 'status-pending' }} me-3">
                            <i class="fas fa-{{ $summary['withdrawal']['allowed'] ? 'check' : 'clock' }}"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Withdrawal Status</h6>
                            <p class="mb-0 text-{{ $summary['withdrawal']['allowed'] ? 'success' : 'warning' }}">
                                {{ $summary['withdrawal']['allowed'] ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>
                    </div>
                    @if($summary['withdrawal']['allowed'])
                        <div class="mt-3">
                            <span class="feature-badge">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Ready to Withdraw
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements List -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card requirement-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Transfer Requirements
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($summary['transfer']['requirements'] as $requirement)
                        <div class="d-flex align-items-center mb-3 p-3 rounded requirement-{{ $requirement['status'] ? 'completed' : 'pending' }}">
                            <div class="status-icon {{ $requirement['status'] ? 'status-completed' : 'status-pending' }} me-3">
                                <i class="{{ $requirement['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $requirement['name'] }}</h6>
                                <p class="mb-0 text-muted small">{{ $requirement['description'] }}</p>
                            </div>
                            <div>
                                @if($requirement['status'])
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Completed
                                    </span>
                                @else
                                    <a href="{{ $requirement['action_url'] }}" class="btn btn-outline-primary btn-sm">
                                        {{ $requirement['action_text'] }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card requirement-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Withdrawal Requirements
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($summary['withdrawal']['requirements'] as $requirement)
                        <div class="d-flex align-items-center mb-3 p-3 rounded requirement-{{ $requirement['status'] ? 'completed' : 'pending' }}">
                            <div class="status-icon {{ $requirement['status'] ? 'status-completed' : 'status-pending' }} me-3">
                                <i class="{{ $requirement['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $requirement['name'] }}</h6>
                                <p class="mb-0 text-muted small">{{ $requirement['description'] }}</p>
                            </div>
                            <div>
                                @if($requirement['status'])
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Completed
                                    </span>
                                @else
                                    <a href="{{ $requirement['action_url'] }}" class="btn btn-outline-danger btn-sm">
                                        {{ $requirement['action_text'] }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if(!$summary['transfer']['allowed'] || !$summary['withdrawal']['allowed'])
        <div class="row mt-4">
            <div class="col-12">
                <div class="card requirement-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-rocket me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($summary['transfer']['requirements'] as $requirement)
                                @if(!$requirement['status'])
                                    <div class="col-md-6 col-lg-3 mb-3">
                                        <a href="{{ $requirement['action_url'] }}" class="btn btn-outline-primary w-100">
                                            <i class="{{ $requirement['icon'] }} me-2"></i>
                                            {{ $requirement['action_text'] }}
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @endsection
</x-smart_layout>
