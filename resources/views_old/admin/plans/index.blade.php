@extends('components.layout')

@section('top_title', 'Admin Plans Management')
@section('title', 'Plans Management')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .plan-card {
        transition: transform 0.2s ease-in-out;
        border: 1px solid #e3e6f0;
    }
    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
    }
    .plan-featured {
        position: relative;
        overflow: hidden;
    }
    .plan-featured::before {
        content: "Featured";
        position: absolute;
        top: 10px;
        right: -30px;
        background: #28a745;
        color: white;
        padding: 5px 40px;
        transform: rotate(45deg);
        font-size: 12px;
        font-weight: bold;
    }
    .status-badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    .stats-card {
        background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
</style>
@endpush

@section('content')
<div class="row my-4">
    <div class="col-lg-12">
        <!-- Page Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="fe fe-package text-primary me-2"></i>Investment Plans Management
                        </h4>
                        <p class="text-muted mb-0">Manage investment plans, pricing, and video earning configurations</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="refreshPlans()">
                            <i class="fe fe-refresh-cw me-1"></i>Refresh
                        </button>
                        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-1"></i>Add New Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fe fe-alert-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Plans Statistics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card stats-card text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">{{ $plans->count() }}</h3>
                                <p class="mb-0">Total Plans</p>
                            </div>
                            <div class="text-white-50">
                                <i class="fe fe-package fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">{{ $plans->where('video_access_enabled', true)->count() }}</h3>
                                <p class="mb-0">Active Plans</p>
                            </div>
                            <div class="text-white-50">
                                <i class="fe fe-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">{{ $plans->where('video_access_enabled', false)->count() }}</h3>
                                <p class="mb-0">Inactive Plans</p>
                            </div>
                            <div class="text-white-50">
                                <i class="fe fe-pause-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">${{ number_format($plans->avg('fixed_amount'), 0) }}</h3>
                                <p class="mb-0">Avg Plan Price</p>
                            </div>
                            <div class="text-white-50">
                                <i class="fe fe-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plans Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fe fe-list me-2"></i>All Plans
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" placeholder="Search plans..." id="searchPlans">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fe fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($plans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="plansTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-bottom-0">Plan Details</th>
                                    <th class="border-bottom-0">Pricing</th>
                                    <th class="border-bottom-0">Video Settings</th>
                                    <th class="border-bottom-0">Duration</th>
                                    <th class="border-bottom-0">Status</th>
                                    <th class="border-bottom-0">Statistics</th>
                                    <th class="border-bottom-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $plan)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle me-3">
                                                    <i class="fe fe-package"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold">{{ $plan->name }}</h6>
                                                    @if($plan->description)
                                                        <small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                                    @endif
                                                    @if($plan->featured)
                                                        <span class="badge bg-warning ms-1">Featured</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-bold text-success fs-5">${{ number_format($plan->fixed_amount, 2) }}</span>
                                                @if($plan->minimum && $plan->maximum)
                                                    <div class="small text-muted">
                                                        Range: ${{ number_format($plan->minimum) }} - ${{ number_format($plan->maximum) }}
                                                    </div>
                                                @endif
                                                @if($plan->interest)
                                                    <div class="small text-info">
                                                        {{ $plan->interest }}% {{ $plan->interest_type ?? 'daily' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $plan->daily_video_limit }} videos/day</div>
                                                <div class="small text-success">
                                                    ${{ number_format($plan->video_earning_rate, 6) }} per video
                                                </div>
                                                <div class="small text-muted">
                                                    Daily earning: ${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-semibold">{{ $plan->time }} {{ $plan->time_name ?? 'days' }}</span>
                                                @if($plan->lifetime)
                                                    <div class="small text-primary">Lifetime</div>
                                                @endif
                                                @if($plan->capital_back)
                                                    <div class="small text-success">Capital Back</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($plan->video_access_enabled)
                                                <span class="badge bg-success status-badge" onclick="togglePlanStatus({{ $plan->id }}, false)" style="cursor: pointer;" title="Click to deactivate">
                                                    <i class="fe fe-check-circle me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary status-badge" onclick="togglePlanStatus({{ $plan->id }}, true)" style="cursor: pointer;" title="Click to activate">
                                                    <i class="fe fe-pause-circle me-1"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="text-muted">Investors: {{ $plan->invests->count() ?? 0 }}</div>
                                                <div class="text-muted">Total Investment: ${{ number_format($plan->invests->sum('amount') ?? 0, 2) }}</div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.plans.edit', $plan->id) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit Plan">
                                                    <i class="fe fe-edit-2"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        onclick="viewPlanDetails({{ $plan->id }})"
                                                        data-bs-toggle="tooltip" 
                                                        title="View Details">
                                                    <i class="fe fe-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="deletePlan({{ $plan->id }}, '{{ $plan->name }}')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Delete Plan">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fe fe-package text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">No Plans Found</h5>
                        <p class="text-muted">Create your first investment plan to get started.</p>
                        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-1"></i>Create First Plan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="planDetailsContent">
                <!-- Plan details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function refreshPlans() {
    window.location.reload();
}

function deletePlan(planId, planName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete plan "${planName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the plan.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/plans') }}/${planId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function viewPlanDetails(planId) {
    console.log('viewPlanDetails called with planId:', planId);
    
    $('#planDetailsModal').modal('show');
    $('#planDetailsContent').html('<div class="text-center"><i class="fe fe-loader spin"></i> Loading...</div>');
    
    const url = `{{ url('admin/plans') }}/${planId}/statistics`;
    console.log('Fetching URL:', url);
    
    // Fetch plan details via AJAX
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
        
        if (data.error) {
            $('#planDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="fe fe-alert-circle me-2"></i>Error loading plan details: ${data.error}
                </div>
            `);
            return;
        }
        
        const detailsHtml = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fe fe-users me-2"></i>Investor Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary mb-1">${data.total_investors}</h4>
                                    <small class="text-muted">Total Investors</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">${data.active_investments}</h4>
                                    <small class="text-muted">Active Investments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fe fe-dollar-sign me-2"></i>Financial Overview</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-success mb-1">$${parseFloat(data.total_investment_amount).toFixed(2)}</h4>
                                    <small class="text-muted">Total Investment</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-1">$${parseFloat(data.average_investment).toFixed(2)}</h4>
                                    <small class="text-muted">Average Investment</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fe fe-video me-2"></i>Earning Potential</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Daily Earning Potential</small>
                                <div class="fw-bold text-primary">$${parseFloat(data.daily_earning_potential).toFixed(6)}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Monthly Earning Potential</small>
                                <div class="fw-bold text-success">$${parseFloat(data.monthly_earning_potential).toFixed(2)}</div>
                            </div>
                            <div>
                                <small class="text-muted">Total Earned So Far</small>
                                <div class="fw-bold text-info">$${parseFloat(data.total_earned).toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fe fe-clock me-2"></i>Plan Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Plan Age</small>
                                <div class="fw-bold">${data.plan_age_days} days</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Performance Rating</small>
                                <div class="fw-bold">
                                    ${data.total_investors > 10 ? '<span class="text-success">Excellent</span>' : 
                                      data.total_investors > 5 ? '<span class="text-warning">Good</span>' : 
                                      '<span class="text-info">New</span>'}
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">ROI Estimate</small>
                                <div class="fw-bold text-success">
                                    ${data.total_investment_amount > 0 ? 
                                      ((data.total_earned / data.total_investment_amount) * 100).toFixed(1) + '%' : '0%'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <div class="d-flex justify-content-between">
                    <a href="{{ url('admin/plans') }}/${planId}" class="btn btn-primary">
                        <i class="fe fe-eye me-1"></i>View Full Details
                    </a>
                    <a href="{{ url('admin/plans') }}/${planId}/edit" class="btn btn-success">
                        <i class="fe fe-edit me-1"></i>Edit Plan
                    </a>
                </div>
            </div>
        `;
        
        $('#planDetailsContent').html(detailsHtml);
    })
    .catch(error => {
        console.error('Fetch error:', error);
        console.error('Error stack:', error.stack);
        $('#planDetailsContent').html(`
            <div class="alert alert-danger">
                <i class="fe fe-alert-circle me-2"></i>Failed to load plan details. 
                <br><strong>Error:</strong> ${error.message}
                <br><small>Check browser console for more details.</small>
                <div class="mt-2">
                    <button class="btn btn-sm btn-secondary" onclick="viewPlanDetails(${planId})">
                        <i class="fe fe-refresh-cw me-1"></i>Retry
                    </button>
                </div>
            </div>
        `);
    });
}

function togglePlanStatus(planId, newStatus) {
    const statusText = newStatus ? 'activate' : 'deactivate';
    
    Swal.fire({
        title: `${statusText.charAt(0).toUpperCase() + statusText.slice(1)} Plan?`,
        text: `Are you sure you want to ${statusText} this plan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${statusText} it!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url('admin/plans') }}/${planId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to update plan status.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to update plan status.', 'error');
            });
        }
    });
}

// Search functionality
document.getElementById('searchPlans').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const table = document.getElementById('plansTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
