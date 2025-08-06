@extends('components.layout')

@section('top_title', 'Create New Plan')
@section('title', 'Add Investment Plan')

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
        border-left: 4px solid #007bff;
    }
    .form-section h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .preview-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
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
                            <i class="fe fe-plus-circle text-primary me-2"></i>Create New Investment Plan
                        </h4>
                        <p class="text-muted mb-0">Set up a new investment plan with video earning capabilities</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-1"></i>Back to Plans
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

        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-8">
                <form action="{{ route('admin.plans.store') }}" method="POST" id="planForm">
                    @csrf
                    
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
                                           value="{{ old('name') }}"
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
                                        <option value="1" {{ old('video_access_enabled', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('video_access_enabled') == '0' ? 'selected' : '' }}>Inactive</option>
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
                                      placeholder="Describe the benefits and features of this plan...">{{ old('description') }}</textarea>
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
                                               value="{{ old('fixed_amount') }}"
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
                                    <label class="form-label fw-semibold">Investment Duration <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="time" 
                                               class="form-control @error('time') is-invalid @enderror" 
                                               value="{{ old('time') }}"
                                               step="1" 
                                               min="1" 
                                               max="365"
                                               placeholder="30"
                                               required
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
                                    <label class="form-label fw-semibold">Interest Rate (Optional)</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="interest" 
                                               class="form-control @error('interest') is-invalid @enderror" 
                                               value="{{ old('interest') }}"
                                               step="0.01" 
                                               min="0"
                                               placeholder="5.00"
                                               onkeyup="updatePreview()">
                                        <span class="input-group-text">%</span>
                                        @error('interest')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Daily/monthly interest rate (optional)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Interest Type</label>
                                    <select name="interest_type" class="form-select @error('interest_type') is-invalid @enderror">
                                        <option value="currency" {{ old('interest_type', 'currency') == 'currency' ? 'selected' : '' }}>Currency</option>
                                        <option value="percentage" {{ old('interest_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
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
                                           value="{{ old('daily_video_limit') }}"
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
                                               value="{{ old('video_earning_rate') }}"
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
                                               {{ old('featured') ? 'checked' : '' }}>
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
                                               {{ old('capital_back') ? 'checked' : '' }}>
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
                                               {{ old('lifetime') ? 'checked' : '' }}>
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
                                               value="{{ old('minimum') }}"
                                               step="0.00000001" 
                                               min="0"
                                               placeholder="0.00">
                                        @error('minimum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Minimum investment amount (optional)</small>
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
                                               value="{{ old('maximum') }}"
                                               step="0.00000001" 
                                               min="0"
                                               placeholder="0.00">
                                        @error('maximum')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Maximum investment amount (optional)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-save me-1"></i>Create Plan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fe fe-refresh-cw me-1"></i>Reset Form
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
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-eye me-2"></i>Live Preview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="preview-card" id="planPreview">
                                <h4 class="mb-3" id="previewName">Plan Name</h4>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Investment Amount:</span>
                                        <span id="previewAmount">$0.00</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Duration:</span>
                                        <span id="previewDuration">0 days</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Daily Videos:</span>
                                        <span id="previewVideos">0 videos</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Per Video:</span>
                                        <span id="previewRate">$0.000000</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Daily Earning:</span>
                                        <span id="previewDaily">$0.00</span>
                                    </div>
                                </div>
                                <div class="preview-info">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Earning:</span>
                                        <span id="previewTotal">$0.00</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="badge bg-light text-dark" id="previewStatus">Inactive</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fe fe-help-circle me-2"></i>Quick Help
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Set competitive earning rates</li>
                                <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Balance video limits with earnings</li>
                                <li class="mb-2"><i class="fe fe-check text-success me-2"></i>Consider market rates</li>
                                <li class="mb-0"><i class="fe fe-check text-success me-2"></i>Test before activation</li>
                            </ul>
                        </div>
                    </div>
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
        statusBadge.className = 'badge bg-success';
    } else {
        statusBadge.textContent = 'Inactive';
        statusBadge.className = 'badge bg-secondary';
    }
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    
    // Add event listeners to all form fields
    const formFields = document.querySelectorAll('#planForm input, #planForm select');
    formFields.forEach(field => {
        field.addEventListener('input', updatePreview);
        field.addEventListener('change', updatePreview);
    });
});

// Form validation
document.getElementById('planForm').addEventListener('submit', function(e) {
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
