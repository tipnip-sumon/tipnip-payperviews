<x-layout>
@section('title', 'Edit Commission Level')
@section('content')

<div class="page">
    <div class="page-header">
        <div class="page-header-title">
            <h4 class="page-title">
                <i class="fe fe-edit me-2"></i>
                Edit Commission Level {{ $commissionLevel->level }}
            </h4>
        </div>
        <div class="page-header-content">
            <a href="{{ route('admin.commission-levels.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left me-2"></i>Back to Levels
            </a>
        </div>
    </div>

    <div class="page-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-4 my-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Commission Level Details</h3>
                        <div class="card-options">
                            <span class="badge badge-outline-primary">Level {{ $commissionLevel->level }}</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.commission-levels.update', $commissionLevel) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Level Number</label>
                                        <input type="number" name="level" class="form-control" 
                                               value="{{ old('level', $commissionLevel->level) }}" 
                                               min="1" max="20" required>
                                        <small class="form-hint">The referral level (1-20)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Commission Percentage</label>
                                        <div class="input-group">
                                            <input type="number" name="percentage" class="form-control" 
                                                   value="{{ old('percentage', $commissionLevel->percentage) }}" 
                                                   step="0.01" min="0" max="100" required
                                                   id="percentage-input">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="form-hint">Percentage of original earning (0-100%)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" 
                                          placeholder="Optional description for this commission level">{{ old('description', $commissionLevel->description) }}</textarea>
                                <small class="form-hint">Optional description to help identify this level</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $commissionLevel->is_active) ? 'checked' : '' }} 
                                           class="form-check-input">
                                    <span class="form-check-label">Active</span>
                                </label>
                                <small class="form-hint">Only active levels will distribute commissions</small>
                            </div>

                            @if($commissionLevel->created_at != $commissionLevel->updated_at)
                                <div class="alert alert-info">
                                    <i class="fe fe-info me-2"></i>
                                    <strong>Last Updated:</strong> {{ $commissionLevel->updated_at->format('F j, Y \a\t g:i A') }}
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-save me-2"></i>Update Commission Level
                                    </button>
                                    <a href="{{ route('admin.commission-levels.index') }}" class="btn btn-secondary ms-2">
                                        Cancel
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <div class="text-muted">
                                        <small>Created: {{ $commissionLevel->created_at->format('M j, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Level Statistics</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $totalCommissions = \App\Models\ReferralCommission::where('level', $commissionLevel->level)->count();
                            $totalAmount = \App\Models\ReferralCommission::where('level', $commissionLevel->level)->sum('commission_amount');
                            $todayCommissions = \App\Models\ReferralCommission::where('level', $commissionLevel->level)
                                ->whereDate('created_at', today())->count();
                        @endphp
                        
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-blue text-white me-3">
                                    <i class="fe fe-hash"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">{{ number_format($totalCommissions) }}</div>
                                    <div class="text-muted">Total Commissions</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-green text-white me-3">
                                    <i class="fe fe-dollar-sign"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">${{ number_format($totalAmount, 6) }}</div>
                                    <div class="text-muted">Total Amount Distributed</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-orange text-white me-3">
                                    <i class="fe fe-calendar"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">{{ number_format($todayCommissions) }}</div>
                                    <div class="text-muted">Today's Commissions</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-purple text-white me-3">
                                    <i class="fe fe-activity"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">{{ $commissionLevel->is_active ? 'Active' : 'Inactive' }}</div>
                                    <div class="text-muted">Current Status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Impact Analysis</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $currentTotal = \App\Models\CommissionLevelSetting::where('id', '!=', $commissionLevel->id)
                                ->where('is_active', true)->sum('percentage');
                            $currentPercentage = old('percentage', $commissionLevel->percentage);
                            $isActive = old('is_active', $commissionLevel->is_active);
                            $newTotal = $currentTotal + ($isActive ? $currentPercentage : 0);
                        @endphp
                        
                        <div class="mb-3">
                            <label class="form-label">Current Total (excluding this level)</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" style="width: {{ ($currentTotal / 100) * 100 }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($currentTotal, 2) }}% / 100.00%</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Total (with this level)</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $newTotal > 100 ? 'bg-danger' : ($newTotal > 80 ? 'bg-warning' : 'bg-success') }}" 
                                     style="width: {{ min(($newTotal / 100) * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($newTotal, 2) }}% / 100.00%</small>
                        </div>

                        @if($newTotal > 100)
                            <div class="alert alert-danger alert-sm">
                                <i class="fe fe-alert-triangle me-2"></i>
                                Exceeds 100% cap by {{ number_format($newTotal - 100, 2) }}%
                            </div>
                        @elseif($newTotal > 80)
                            <div class="alert alert-warning alert-sm">
                                <i class="fe fe-alert-circle me-2"></i>
                                Near the 80% cap limit
                            </div>
                        @else
                            <div class="alert alert-success alert-sm">
                                <i class="fe fe-check me-2"></i>
                                Within safe limits
                            </div>
                        @endif
                    </div>
                </div>

                @if($totalCommissions > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-warning">
                                <i class="fe fe-alert-triangle me-2"></i>Warning
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                This level has <strong>{{ number_format($totalCommissions) }} existing commission records</strong>.
                            </p>
                            <p class="text-muted mb-0">
                                Changes will only affect future commissions, not past ones.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const percentageInput = document.getElementById('percentage-input');
    
    percentageInput.addEventListener('input', function() {
        const value = parseFloat(this.value) || 0;
        
        // Visual feedback for percentage ranges
        this.classList.remove('is-valid', 'is-invalid');
        
        if (value > 100) {
            this.classList.add('is-invalid');
        } else if (value > 0) {
            this.classList.add('is-valid');
        }
    });
});
</script>
@endpush
</x-layout>
