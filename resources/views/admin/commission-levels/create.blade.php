<x-layout>
@section('title', 'Add Commission Level')
@section('content')

<div class="page">
    <div class="page-header">
        <div class="page-header-title">
            <h4 class="page-title">
                <i class="fe fe-plus me-2"></i>
                Add Commission Level
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
                    </div>
                    <form action="{{ route('admin.commission-levels.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Level Number</label>
                                        <input type="number" name="level" class="form-control" 
                                               value="{{ old('level', $nextLevel) }}" 
                                               min="1" max="20" required>
                                        <small class="form-hint">The referral level (1-20)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Commission Percentage</label>
                                        <div class="input-group">
                                            <input type="number" name="percentage" class="form-control" 
                                                   value="{{ old('percentage') }}" 
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
                                          placeholder="Optional description for this commission level">{{ old('description') }}</textarea>
                                <small class="form-hint">Optional description to help identify this level</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }} 
                                           class="form-check-input">
                                    <span class="form-check-label">Active</span>
                                </label>
                                <small class="form-hint">Only active levels will distribute commissions</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-save me-2"></i>Create Commission Level
                                    </button>
                                    <a href="{{ route('admin.commission-levels.index') }}" class="btn btn-secondary ms-2">
                                        Cancel
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <div class="text-muted">
                                        <small>Total commission cap: 10%</small>
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
                        <h3 class="card-title">Commission Guidelines</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-green text-white me-3">
                                    <i class="fe fe-check"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">Total Cap: 100%</div>
                                    <div class="text-muted">Maximum total commission percentage</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-blue text-white me-3">
                                    <i class="fe fe-layers"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">Level Range: 1-20</div>
                                    <div class="text-muted">Supported referral levels</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-orange text-white me-3">
                                    <i class="fe fe-trending-down"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">Decreasing Pattern</div>
                                    <div class="text-muted">Higher levels typically get lower percentages</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center px-0">
                                <span class="avatar avatar-sm bg-purple text-white me-3">
                                    <i class="fe fe-zap"></i>
                                </span>
                                <div>
                                    <div class="font-weight-medium">Real-time Distribution</div>
                                    <div class="text-muted">Commissions are distributed instantly</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Present Levels</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($commissionLevels as $level)
                                        <tr>
                                            <td>{{ $level->level }}</td>
                                            <td>{{ number_format($level->percentage, 2) }}%</td>
                                        </tr>
                                        @php $total += $level->percentage; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td>{{ number_format($total, 2) }}%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <small class="text-muted">Current commission structure from database</small>
                    </div>
                </div>
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
<!-- This file is part of the Mainur Sir project. -->
