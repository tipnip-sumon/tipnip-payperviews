<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.withdraw-methods.index') }}">Withdrawal Methods</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Method</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center my-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-plus me-2"></i>Add New Withdrawal Method
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.withdraw-methods.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Method Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Method Key <span class="text-danger">*</span></label>
                                    <input type="text" name="method_key" class="form-control @error('method_key') is-invalid @enderror" 
                                           value="{{ old('method_key') }}" required placeholder="e.g., paypal, bank_transfer">
                                    <small class="text-muted">Unique identifier for this method (lowercase, underscores allowed)</small>
                                    @error('method_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Form ID and Rate -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Form ID</label>
                                    <input type="number" name="form_id" class="form-control @error('form_id') is-invalid @enderror" 
                                           value="{{ old('form_id', 0) }}" min="0">
                                    <small class="text-muted">Form configuration ID (0 for default)</small>
                                    @error('form_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Exchange Rate</label>
                                    <div class="input-group">
                                        <input type="number" name="rate" class="form-control @error('rate') is-invalid @enderror" 
                                               value="{{ old('rate', 1) }}" step="0.00000001" min="0">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                    <small class="text-muted">Conversion rate to USD (1 for USD)</small>
                                    @error('rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Amount Limits -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Minimum Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="min_amount" class="form-control @error('min_amount') is-invalid @enderror" 
                                               value="{{ old('min_amount', 1) }}" step="0.01" min="0" required>
                                    </div>
                                    @error('min_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Maximum Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="max_amount" class="form-control @error('max_amount') is-invalid @enderror" 
                                               value="{{ old('max_amount', 10000) }}" step="0.01" min="0" required>
                                    </div>
                                    @error('max_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Daily Limit <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="daily_limit" class="form-control @error('daily_limit') is-invalid @enderror" 
                                               value="{{ old('daily_limit', 5000) }}" step="0.01" min="0" required>
                                    </div>
                                    @error('daily_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Charge Configuration -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fixed Charge</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="fixed_charge" class="form-control @error('fixed_charge') is-invalid @enderror" 
                                               value="{{ old('fixed_charge', 0) }}" step="0.01" min="0">
                                    </div>
                                    <small class="text-muted">Fixed amount charged per withdrawal (0 for no fixed charge)</small>
                                    @error('fixed_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Percentage Charge</label>
                                    <div class="input-group">
                                        <input type="number" name="percent_charge" class="form-control @error('percent_charge') is-invalid @enderror" 
                                               value="{{ old('percent_charge', 0) }}" step="0.01" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Percentage of withdrawal amount charged (0 for no percentage charge)</small>
                                    @error('percent_charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Charge Preview -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <strong>Charge Preview:</strong>
                                        <div id="charge-preview">
                                            Both fixed and percentage charges can be applied together. 
                                            <br><strong>Example:</strong> For $100 withdrawal with $5 fixed + 2% = $5 + $2 = $7 total charge
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Legacy Fields (for backward compatibility) -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">Legacy Fields (Auto-calculated)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Min Limit</label>
                                                    <input type="hidden" name="min_limit" value="0">
                                                    <input type="number" class="form-control" id="min_limit_display" readonly>
                                                    <small class="text-muted">Auto-synced with min_amount</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Max Limit</label>
                                                    <input type="hidden" name="max_limit" value="0">
                                                    <input type="number" class="form-control" id="max_limit_display" readonly>
                                                    <small class="text-muted">Auto-synced with max_amount</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Charge Type</label>
                                                    <input type="hidden" name="charge_type" value="fixed">
                                                    <input type="text" class="form-control" id="charge_type_display" readonly>
                                                    <small class="text-muted">Auto-determined</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Legacy Charge</label>
                                                    <input type="hidden" name="charge" value="0">
                                                    <input type="number" class="form-control" id="charge_display" readonly>
                                                    <small class="text-muted">Auto-calculated</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Settings -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Processing Time <span class="text-danger">*</span></label>
                                    <input type="text" name="processing_time" class="form-control @error('processing_time') is-invalid @enderror" 
                                           value="{{ old('processing_time', '1-3 business days') }}" required>
                                    @error('processing_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency <span class="text-danger">*</span></label>
                                    <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" 
                                           value="{{ old('currency', 'USD') }}" required maxlength="10">
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Icon and Sort Order -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Icon Class</label>
                                    <div class="input-group">
                                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                               value="{{ old('icon') }}" placeholder="e.g., fe fe-credit-card, fab fa-paypal">
                                        <span class="input-group-text icon-preview">
                                            <i class="fe fe-credit-card"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Font Awesome or Feather icon class</small>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                           value="{{ old('sort_order', 0) }}" min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                              rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">User Instructions</label>
                                    <textarea name="instructions" class="form-control @error('instructions') is-invalid @enderror" 
                                              rows="4" placeholder="Instructions for users on what account details they need to provide">{{ old('instructions') }}</textarea>
                                    @error('instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" 
                                               {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            <strong>Active Status</strong>
                                            <small class="d-block text-muted">Enable this withdrawal method for users</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.withdraw-methods.index') }}" class="btn btn-light">
                                <i class="fe fe-arrow-left me-1"></i>Back to List
                            </a>
                            <div>
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fe fe-refresh-cw me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-save me-1"></i>Create Method
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Auto-generate method key from name
            $('input[name="name"]').on('input', function() {
                const name = $(this).val();
                const methodKey = name.toLowerCase()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
                $('input[name="method_key"]').val(methodKey);
            });

            // Update icon preview
            $('input[name="icon"]').on('input', function() {
                const iconClass = $(this).val();
                if (iconClass) {
                    $('.icon-preview').html('<i class="' + iconClass + '"></i>');
                } else {
                    $('.icon-preview').html('<i class="fe fe-credit-card"></i>');
                }
            });

            // Update charge preview
            function updateChargePreview() {
                const fixedCharge = parseFloat($('input[name="fixed_charge"]').val()) || 0;
                const percentCharge = parseFloat($('input[name="percent_charge"]').val()) || 0;
                
                let previewText = '';
                
                if (fixedCharge > 0 && percentCharge > 0) {
                    previewText = `Combined charges: $${fixedCharge.toFixed(2)} fixed + ${percentCharge}% of amount<br>`;
                    previewText += `<strong>Example:</strong> For $100 withdrawal = $${fixedCharge.toFixed(2)} + $${(100 * percentCharge / 100).toFixed(2)} = $${(fixedCharge + (100 * percentCharge / 100)).toFixed(2)} total charge`;
                } else if (fixedCharge > 0) {
                    previewText = `Fixed charge: $${fixedCharge.toFixed(2)} per withdrawal<br>`;
                    previewText += `<strong>Example:</strong> Any withdrawal amount will have $${fixedCharge.toFixed(2)} charge`;
                } else if (percentCharge > 0) {
                    previewText = `Percentage charge: ${percentCharge}% of withdrawal amount<br>`;
                    previewText += `<strong>Example:</strong> For $100 withdrawal = $${(100 * percentCharge / 100).toFixed(2)} charge`;
                } else {
                    previewText = 'No charges - Free withdrawals<br>';
                    previewText += '<strong>Example:</strong> Users will receive the full amount they request';
                }
                
                $('#charge-preview').html(previewText);
                
                // Update legacy fields
                updateLegacyFields();
            }
            
            // Update legacy fields for backward compatibility
            function updateLegacyFields() {
                const minAmount = parseFloat($('input[name="min_amount"]').val()) || 0;
                const maxAmount = parseFloat($('input[name="max_amount"]').val()) || 0;
                const fixedCharge = parseFloat($('input[name="fixed_charge"]').val()) || 0;
                const percentCharge = parseFloat($('input[name="percent_charge"]').val()) || 0;
                
                // Update hidden fields
                $('input[name="min_limit"]').val(minAmount);
                $('input[name="max_limit"]').val(maxAmount);
                
                // Determine charge type and value
                let chargeType = 'fixed';
                let chargeValue = fixedCharge;
                
                if (fixedCharge > 0 && percentCharge > 0) {
                    chargeType = 'fixed'; // Default to fixed when both exist
                    chargeValue = fixedCharge;
                } else if (percentCharge > 0) {
                    chargeType = 'percent';
                    chargeValue = percentCharge;
                }
                
                $('input[name="charge_type"]').val(chargeType);
                $('input[name="charge"]').val(chargeValue);
                
                // Update display fields
                $('#min_limit_display').val(minAmount.toFixed(2));
                $('#max_limit_display').val(maxAmount.toFixed(2));
                $('#charge_type_display').val(chargeType);
                $('#charge_display').val(chargeValue.toFixed(2));
            }

            $('input[name="fixed_charge"], input[name="percent_charge"]').on('input change', updateChargePreview);
            $('input[name="min_amount"], input[name="max_amount"]').on('input change', updateLegacyFields);

            // Validate amount limits
            $('input[name="min_amount"], input[name="max_amount"], input[name="daily_limit"]').on('change', function() {
                const minAmount = parseFloat($('input[name="min_amount"]').val()) || 0;
                const maxAmount = parseFloat($('input[name="max_amount"]').val()) || 0;
                const dailyLimit = parseFloat($('input[name="daily_limit"]').val()) || 0;

                if (maxAmount > 0 && minAmount >= maxAmount) {
                    toastr.warning('Maximum amount should be greater than minimum amount');
                }

                if (dailyLimit > 0 && maxAmount > dailyLimit) {
                    toastr.warning('Daily limit should be greater than or equal to maximum amount');
                }
            });
            
            // Initialize charge preview
            updateChargePreview();
        });
    </script>
    @endpush
</x-layout>
