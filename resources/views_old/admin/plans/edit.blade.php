@extends('components.layout')

@section('top_title', 'Edit Plan')
@sec                        </a>
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

        <div class="row">itle', 'Edit Investment Plan')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #28a745;
    }
    .form-section h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .preview-card {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
    }
    .form-control:focus, .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25);
    }
    .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
    }
    .preview-info {
        background: rgba(255,255,255,0.1);
        border-radius: 0.5rem;
        padding: 1rem;
        margin: 0.5rem 0;
    }
    .stats-card {
        background: linear-gradient(45deg, #17a2b8, #20c997);
        color: white;
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
                            <i class="fe fe-edit text-success me-2"></i>Edit Investment Plan
                        </h4>
                        <p class="text-muted mb-0">Modify the settings and configuration for "{{ $plan->name }}"</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-1"></i>Back to Plans
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="showPlanStats()">
                            <i class="fe fe-bar-chart me-1"></i>View Statistics
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-8">
                <!-- Plan Statistics Overview -->
                @if($plan->invests && $plan->invests->count() > 0)
                <div class="card stats-card text-white mb-4">
                    <div class="card-body">
                        <h6 class="card-title text-white mb-3">Current Plan Performance</h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <h4 class="mb-1">{{ $plan->invests->count() }}</h4>
                                    <small>Active Investors</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <h4 class="mb-1">${{ number_format($plan->invests->sum('amount'), 2) }}</h4>
                                    <small>Total Investment</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <h4 class="mb-1">${{ number_format($plan->invests->avg('amount') ?? 0, 2) }}</h4>
                                    <small>Avg Investment</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <h4 class="mb-1">{{ $plan->created_at->diffForHumans() }}</h4>
                                    <small>Plan Age</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST" id="planEditForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5><i class="fe fe-info me-2"></i>Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $plan->name) }}"
                                           placeholder="e.g., Premium Plan"
                                           required
                                           onkeyup="updatePreview()">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Plan Status <span class="text-danger">*</span></label>
                                    <select name="video_access_enabled" 
                                            class="form-select @error('video_access_enabled') is-invalid @enderror"
                                            onchange="updatePreview()">
                                        <option value="1" {{ old('video_access_enabled', $plan->video_access_enabled) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('video_access_enabled', $plan->video_access_enabled) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('video_access_enabled')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3"
                                      placeholder="Describe the benefits and features of this plan...">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing Configuration -->
                    <div class="form-section">
                        <h5><i class="fe fe-dollar-sign me-2"></i>Pricing Configuration</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Fixed Investment Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="fixed_amount" 
                                               class="form-control @error('fixed_amount') is-invalid @enderror" 
                                               value="{{ old('fixed_amount', $plan->fixed_amount) }}"
                                               step="0.01" 
                                               min="0" 
                                               placeholder="100.00"
                                               required
                                               onkeyup="updatePreview()">
                                        @error('fixed_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">The investment amount required for this plan</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Investment Duration</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="time" 
                                               class="form-control @error('time') is-invalid @enderror" 
                                               value="{{ old('time', $plan->time) }}"
                                               step="1" 
                                               min="1" 
                                               max="365"
                                               placeholder="30"
                                               onkeyup="updatePreview()">
                                        <span class="input-group-text">days</span>
                                        @error('time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Duration for investment return (1-365 days)</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Interest Rate</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="interest" 
                                               class="form-control @error('interest') is-invalid @enderror" 
                                               value="{{ old('interest', $plan->interest) }}"
                                               step="0.01" 
                                               min="0"
                                               placeholder="5.00"
                                               onkeyup="updatePreview()">
                                        <span class="input-group-text">%</span>
                                        @error('interest')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Daily/monthly interest rate</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Interest Type</label>
                                    <select name="interest_type" class="form-select @error('interest_type') is-invalid @enderror">
                                        <option value="daily" {{ old('interest_type', $plan->interest_type_text) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="monthly" {{ old('interest_type', $plan->interest_type_text) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="total" {{ old('interest_type', $plan->interest_type_text) == 'total' ? 'selected' : '' }}>Total Return</option>
                                    </select>
                                    @error('interest_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Earning Settings -->
                    <div class="form-section">
                        <h5><i class="fe fe-video me-2"></i>Video Earning Configuration</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Daily Video Limit <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="daily_video_limit" 
                                           class="form-control @error('daily_video_limit') is-invalid @enderror" 
                                           value="{{ old('daily_video_limit', $plan->daily_video_limit) }}"
                                           min="1" 
                                           placeholder="20"
                                           required
                                           onkeyup="updatePreview()">
                                    @error('daily_video_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum videos a user can watch per day</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Earning Per Video <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="video_earning_rate" 
                                               class="form-control @error('video_earning_rate') is-invalid @enderror" 
                                               value="{{ old('video_earning_rate', $plan->video_earning_rate) }}"
                                               step="0.000001" 
                                               min="0" 
                                               placeholder="0.001000"
                                               required
                                               onkeyup="updatePreview()">
                                        @error('video_earning_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Amount earned per video watched</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Settings -->
                    <div class="form-section">
                        <h5><i class="fe fe-settings me-2"></i>Advanced Settings</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="featured" 
                                               value="1"
                                               id="featured"
                                               {{ old('featured', $plan->featured) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="featured">
                                            Featured Plan
                                        </label>
                                        <small class="d-block text-muted">Mark as featured plan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="capital_back" 
                                               value="1"
                                               id="capital_back"
                                               {{ old('capital_back', $plan->capital_back) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="capital_back">
                                            Capital Return
                                        </label>
                                        <small class="d-block text-muted">Return initial investment</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="lifetime" 
                                               value="1"
                                               id="lifetime"
                                               {{ old('lifetime', $plan->lifetime) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="lifetime">
                                            Lifetime Plan
                                        </label>
                                        <small class="d-block text-muted">No expiration date</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Range Settings -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Minimum Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="minimum" 
                                               class="form-control @error('minimum') is-invalid @enderror" 
                                               value="{{ old('minimum', $plan->minimum) }}"
                                               step="0.01" 
                                               min="0"
                                               placeholder="0.00">
                                        @error('minimum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Minimum investment amount</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Maximum Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="maximum" 
                                               class="form-control @error('maximum') is-invalid @enderror" 
                                               value="{{ old('maximum', $plan->maximum) }}"
                                               step="0.01" 
                                               min="0"
                                               placeholder="0.00">
                                        @error('maximum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Maximum investment amount</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fe fe-save me-1"></i>Update Plan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fe fe-refresh-cw me-1"></i>Reset Changes
                        </button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-danger">
                            <i class="fe fe-x me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-success text-white">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-eye me-2"></i>Live Preview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="preview-card" id="planPreview">
                                <h4 class="mb-3" id="previewName">{{ $plan->name }}</h4>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Investment Amount:</span>
                                        <span id="previewAmount">${{ number_format($plan->fixed_amount, 2) }}</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Duration:</span>
                                        <span id="previewDuration">{{ $plan->time }} days</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Daily Videos:</span>
                                        <span id="previewVideos">{{ $plan->daily_video_limit }} videos</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Per Video:</span>
                                        <span id="previewRate">${{ number_format($plan->video_earning_rate, 6) }}</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Daily Earning:</span>
                                        <span id="previewDaily">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate, 2) }}</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Earning:</span>
                                        <span id="previewTotal">${{ number_format($plan->daily_video_limit * $plan->video_earning_rate * $plan->time, 2) }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="badge {{ $plan->video_access_enabled ? 'bg-light text-dark' : 'bg-secondary' }}" id="previewStatus">
                                        {{ $plan->video_access_enabled ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Info Card -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-info me-2"></i>Plan Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2">
                                    <strong>Created:</strong> {{ $plan->created_at->format('M d, Y h:i A') }}
                                </li>
                                <li class="mb-2">
                                    <strong>Last Updated:</strong> {{ $plan->updated_at->format('M d, Y h:i A') }}
                                </li>
                                <li class="mb-2">
                                    <strong>Plan ID:</strong> #{{ $plan->id }}
                                </li>
                                @if($plan->invests && $plan->invests->count() > 0)
                                <li class="mb-0">
                                    <strong>Active Investments:</strong> {{ $plan->invests->count() }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Warning Card -->
                    @if($plan->invests && $plan->invests->count() > 0)
                    <div class="card border-warning mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-alert-triangle me-2"></i>Important Notice
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="small mb-0">This plan has active investments. Changes to earning rates and video limits may affect existing investors.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plan Statistics Modal -->
<div class="modal fade" id="planStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plan Statistics - {{ $plan->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="planStatsContent">
                <!-- Statistics content will be loaded here -->
                <div class="text-center">
                    <i class="fe fe-loader spin fa-2x"></i>
                    <p class="mt-2">Loading statistics...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
function updatePreview() {
    // Get form values
    const name = document.querySelector('input[name="name"]').value || 'Plan Name';
    const amount = parseFloat(document.querySelector('input[name="fixed_amount"]').value) || 0;
    const duration = parseInt(document.querySelector('input[name="time"]').value) || 0;
    const videoLimit = parseInt(document.querySelector('input[name="daily_video_limit"]').value) || 0;
    const videoRate = parseFloat(document.querySelector('input[name="video_earning_rate"]').value) || 0;
    const status = document.querySelector('select[name="video_access_enabled"]').value === '1';
    
    // Update preview
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewAmount').textContent = '$' + amount.toFixed(2);
    document.getElementById('previewDuration').textContent = duration + ' days';
    document.getElementById('previewVideos').textContent = videoLimit + ' videos';
    document.getElementById('previewRate').textContent = '$' + videoRate.toFixed(6);
    
    const dailyEarning = videoLimit * videoRate;
    document.getElementById('previewDaily').textContent = '$' + dailyEarning.toFixed(2);
    
    const totalEarning = dailyEarning * duration;
    document.getElementById('previewTotal').textContent = '$' + totalEarning.toFixed(2);
    
    const statusBadge = document.getElementById('previewStatus');
    if (status) {
        statusBadge.textContent = 'Active';
        statusBadge.className = 'badge bg-light text-dark';
    } else {
        statusBadge.textContent = 'Inactive';
        statusBadge.className = 'badge bg-secondary';
    }
}

function showPlanStats() {
    console.log('Loading plan statistics for plan ID: {{ $plan->id }}');
    
    // Show modal with loading state
    $('#planStatsModal').modal('show');
    $('#planStatsContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading detailed statistics...</p>
        </div>
    `);
    
    // Fetch detailed plan statistics
    const url = `/admin/plans/{{ $plan->id }}/statistics`;
    console.log('Fetching statistics from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Statistics response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Statistics data received:', data);
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        // Calculate additional metrics
        const dailyPotential = data.daily_earning_potential || 0;
        const monthlyPotential = data.monthly_earning_potential || 0;
        const totalInvestment = data.total_investment_amount || 0;
        const totalEarned = data.total_earned || 0;
        const roiPercentage = totalInvestment > 0 ? ((totalEarned / totalInvestment) * 100).toFixed(2) : '0.00';
        
        // Create comprehensive statistics HTML
        const statsHtml = `
            <div class="row">
                <!-- Investor Overview -->
                <div class="col-md-6 mb-4">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fe fe-users me-2"></i>Investor Overview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <h3 class="text-primary mb-1">${data.total_investors || 0}</h3>
                                    <small class="text-muted">Total Investors</small>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-success mb-1">${data.active_investments || 0}</h3>
                                    <small class="text-muted">Active Investments</small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Conversion Rate:</span>
                                <strong>${data.total_investors > 0 ? ((data.active_investments / data.total_investors) * 100).toFixed(1) : '0'}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Performance -->
                <div class="col-md-6 mb-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fe fe-dollar-sign me-2"></i>Financial Performance
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Investment:</span>
                                    <strong class="text-success">$${totalInvestment.toLocaleString()}</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Average Investment:</span>
                                    <strong class="text-info">$${(data.average_investment || 0).toLocaleString()}</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Earned:</span>
                                    <strong class="text-warning">$${totalEarned.toLocaleString()}</strong>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">ROI:</span>
                                    <strong class="text-primary">${roiPercentage}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Earning Potential -->
                <div class="col-md-6 mb-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fe fe-video me-2"></i>Earning Potential
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Daily Potential:</span>
                                    <strong class="text-warning">$${dailyPotential.toFixed(6)}</strong>
                                </div>
                                <small class="text-muted">{{ $plan->daily_video_limit }} videos × ${{ number_format($plan->video_earning_rate, 6) }}</small>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Monthly Potential:</span>
                                    <strong class="text-success">$${monthlyPotential.toFixed(2)}</strong>
                                </div>
                                <small class="text-muted">30 days earning potential</small>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Plan Duration Earning:</span>
                                    <strong class="text-info">$${(dailyPotential * {{ $plan->time }}).toFixed(2)}</strong>
                                </div>
                                <small class="text-muted">{{ $plan->time }} days total potential</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Performance -->
                <div class="col-md-6 mb-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fe fe-trending-up me-2"></i>Plan Performance
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Plan Age:</span>
                                    <strong>${data.plan_age_days || 0} days</strong>
                                </div>
                                <small class="text-muted">Since {{ $plan->created_at->format('M d, Y') }}</small>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge ${data.plan_status === 'Active' ? 'bg-success' : 'bg-secondary'}">
                                        {{ $plan->video_access_enabled ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Performance Rating:</span>
                                    <strong class="${data.total_investors > 10 ? 'text-success' : data.total_investors > 5 ? 'text-warning' : 'text-info'}">
                                        ${data.total_investors > 10 ? 'Excellent' : data.total_investors > 5 ? 'Good' : 'New'}
                                    </strong>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Investment Efficiency:</span>
                                    <strong class="text-primary">
                                        ${data.total_investors > 0 ? (totalInvestment / data.total_investors).toFixed(0) : '0'} $/investor
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fe fe-zap me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.plans.show', $plan->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fe fe-eye me-1"></i>View Full Details
                                </a>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="$('#planStatsModal').modal('hide'); $('#planEditForm').scrollIntoView();">
                                    <i class="fe fe-edit me-1"></i>Edit Plan Settings
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="showPlanStats()">
                                    <i class="fe fe-refresh-cw me-1"></i>Refresh Statistics
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Footer -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fe fe-info me-1"></i>
                    Statistics updated in real-time • Last updated: ${new Date().toLocaleString()}
                </small>
            </div>
        `;
        
        $('#planStatsContent').html(statsHtml);
    })
    .catch(error => {
        console.error('Error loading plan statistics:', error);
        $('#planStatsContent').html(`
            <div class="alert alert-danger">
                <h6 class="alert-heading">
                    <i class="fe fe-alert-triangle me-2"></i>Error Loading Statistics
                </h6>
                <p class="mb-2"><strong>Error:</strong> ${error.message}</p>
                <small class="text-muted">Please check your connection and try again. Check browser console for more details.</small>
                <hr>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm" onclick="showPlanStats()">
                        <i class="fe fe-refresh-cw me-1"></i>Retry
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fe fe-x me-1"></i>Close
                    </button>
                </div>
            </div>
        `);
    });
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all form fields
    const formFields = document.querySelectorAll('#planEditForm input, #planEditForm select');
    formFields.forEach(field => {
        field.addEventListener('input', updatePreview);
        field.addEventListener('change', updatePreview);
    });
});

// Form validation
document.getElementById('planEditForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Basic validation
    const requiredFields = this.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fill in all required fields.',
            icon: 'error'
        });
    }
});
</script>
@endpush
