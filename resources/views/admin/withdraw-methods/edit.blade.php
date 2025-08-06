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
                    <li class="breadcrumb-item active" aria-current="page">Edit {{ $withdrawMethod->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center my-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fe fe-edit me-2"></i>Edit Withdrawal Method: {{ $withdrawMethod->name }}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.withdraw-methods.update', $withdrawMethod->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Method Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $withdrawMethod->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Method Key <span class="text-danger">*</span></label>
                                    <input type="text" name="method_key" class="form-control @error('method_key') is-invalid @enderror" 
                                           value="{{ old('method_key', $withdrawMethod->method_key) }}" required>
                                    <small class="text-muted">Unique identifier for this method (lowercase, underscores allowed)</small>
                                    @error('method_key')
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
                                               value="{{ old('min_amount', $withdrawMethod->min_amount) }}" step="0.01" min="0" required>
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
                                               value="{{ old('max_amount', $withdrawMethod->max_amount) }}" step="0.01" min="0" required>
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
                                               value="{{ old('daily_limit', $withdrawMethod->daily_limit) }}" step="0.01" min="0" required>
                                    </div>
                                    @error('daily_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Charge Configuration -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Charge Type <span class="text-danger">*</span></label>
                                    <select name="charge_type" class="form-select @error('charge_type') is-invalid @enderror" required>
                                        <option value="fixed" {{ old('charge_type', $withdrawMethod->charge_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                        <option value="percent" {{ old('charge_type', $withdrawMethod->charge_type) == 'percent' ? 'selected' : '' }}>Percentage</option>
                                    </select>
                                    @error('charge_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Charge Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text charge-symbol">{{ $withdrawMethod->charge_type == 'percent' ? '%' : '$' }}</span>
                                        <input type="number" name="charge" class="form-control @error('charge') is-invalid @enderror" 
                                               value="{{ old('charge', $withdrawMethod->charge) }}" step="0.01" min="0" required>
                                    </div>
                                    <small class="text-muted">Enter amount for fixed or percentage for percent type</small>
                                    @error('charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Additional Settings -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Processing Time <span class="text-danger">*</span></label>
                                    <input type="text" name="processing_time" class="form-control @error('processing_time') is-invalid @enderror" 
                                           value="{{ old('processing_time', $withdrawMethod->processing_time) }}" required>
                                    @error('processing_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency <span class="text-danger">*</span></label>
                                    <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" 
                                           value="{{ old('currency', $withdrawMethod->currency) }}" required maxlength="10">
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
                                               value="{{ old('icon', $withdrawMethod->icon) }}" placeholder="e.g., fe fe-credit-card, fab fa-paypal">
                                        <span class="input-group-text">
                                            {!! $withdrawMethod->icon_html !!}
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
                                           value="{{ old('sort_order', $withdrawMethod->sort_order) }}" min="0">
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
                                              rows="3">{{ old('description', $withdrawMethod->description) }}</textarea>
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
                                              rows="4" placeholder="Instructions for users on what account details they need to provide">{{ old('instructions', $withdrawMethod->instructions) }}</textarea>
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
                                               {{ old('status', $withdrawMethod->status) ? 'checked' : '' }}>
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
                                <a href="{{ route('admin.withdraw-methods.show', $withdrawMethod->id) }}" class="btn btn-info me-2">
                                    <i class="fe fe-eye me-1"></i>View Details
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-save me-1"></i>Update Method
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
            // Update charge symbol based on charge type
            $('select[name="charge_type"]').on('change', function() {
                const chargeSymbol = $(this).val() === 'percent' ? '%' : '$';
                $('.charge-symbol').text(chargeSymbol);
            });

            // Update icon preview
            $('input[name="icon"]').on('input', function() {
                const iconClass = $(this).val();
                if (iconClass) {
                    $('.input-group-text').html('<i class="' + iconClass + '"></i>');
                } else {
                    $('.input-group-text').html('<i class="fe fe-credit-card"></i>');
                }
            });

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
        });
    </script>
    @endpush
</x-layout>
