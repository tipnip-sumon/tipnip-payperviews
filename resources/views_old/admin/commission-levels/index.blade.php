<x-layout>
@section('title', 'Commission Level Settings')
@section('content')

<div class="page">
    <div class="page-header">
        <div class="page-header-title">
            <h4 class="page-title">
                <i class="fe fe-percent me-2"></i>
                Commission Level Settings
            </h4>
        </div>
        <div class="page-header-content">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                    <i class="fe fe-edit-2 me-2"></i>Bulk Update
                </button>
                <a href="{{ route('admin.commission-levels.create') }}" class="btn btn-success">
                    <i class="fe fe-plus me-2"></i>Add New Level
                </a>
                <button type="button" class="btn btn-warning" onclick="resetToDefaults()">
                    <i class="fe fe-refresh-cw me-2"></i>Reset to Defaults
                </button>
            </div>
        </div>
    </div>

    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ session('success') }}
            </div>
        @endif

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

        <!-- Summary Card -->
        <div class="row mb-4 my-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Commission Overview</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary me-3">
                                        <i class="fe fe-percent"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Total Commission</h6>
                                        <span class="text-muted">{{ number_format($totalPercentage, 2) }}% of earnings</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-success me-3">
                                        <i class="fe fe-layers"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Active Levels</h6>
                                        <span class="text-muted">{{ $commissionLevels->where('is_active', true)->count() }} levels</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-info me-3">
                                        <i class="fe fe-trending-up"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Max Level</h6>
                                        <span class="text-muted">Level {{ $commissionLevels->max('level') ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar {{ $totalPercentage > 10 ? 'bg-danger' : 'bg-warning' }} me-3">
                                        <i class="fe fe-alert-triangle"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Remaining Cap</h6>
                                        <span class="text-muted">{{ number_format(100 - $totalPercentage, 2) }}% available</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($totalPercentage > 100)
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fe fe-alert-triangle me-2"></i>
                                <strong>Warning:</strong> Total commission percentage exceeds the 100% cap. Please adjust the levels.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Levels Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Commission Levels</h3>
                <div class="card-options">
                    <span class="badge badge-info">{{ $commissionLevels->count() }} levels configured</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissionLevels as $level)
                                <tr class="{{ !$level->is_active ? 'text-muted' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                                                {{ $level->level }}
                                            </div>
                                            <span class="fw-bold">Level {{ $level->level }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $percentage = $level->percentage;
                                            $badgeClass = 'bg-secondary';
                                            if ($percentage >= 30) {
                                                $badgeClass = 'bg-success';
                                            } elseif ($percentage >= 20) {
                                                $badgeClass = 'bg-primary';
                                            } elseif ($percentage >= 10) {
                                                $badgeClass = 'bg-warning';
                                            } elseif ($percentage > 0) {
                                                $badgeClass = 'bg-info';
                                            } else {
                                                $badgeClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} text-white fw-bold">
                                            {{ number_format($level->percentage, 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.commission-levels.toggle-active', $level) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $level->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                                <i class="fe fe-{{ $level->is_active ? 'check' : 'x' }} me-1"></i>
                                                {{ $level->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $level->description ?: 'No description' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $level->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.commission-levels.edit', $level) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fe fe-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteLevel({{ $level->id }})">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="fe fe-percent"></i>
                                            </div>
                                            <p class="empty-title">No commission levels configured</p>
                                            <p class="empty-subtitle text-muted">
                                                Start by adding your first commission level or reset to defaults.
                                            </p>
                                            <div class="empty-action">
                                                <a href="{{ route('admin.commission-levels.create') }}" class="btn btn-primary">
                                                    <i class="fe fe-plus me-2"></i>Add First Level
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.commission-levels.bulk-update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Commission Levels</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bulk-levels-container">
                        @foreach($commissionLevels as $level)
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">Level {{ $level->level }}</label>
                                    <input type="hidden" name="levels[{{ $loop->index }}][id]" value="{{ $level->id }}">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" min="0" max="100" 
                                           name="levels[{{ $loop->index }}][percentage]" 
                                           value="{{ $level->percentage }}" 
                                           class="form-control bulk-percentage"
                                           placeholder="Percentage">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="levels[{{ $loop->index }}][is_active]" value="0">
                                        <input type="checkbox" name="levels[{{ $loop->index }}][is_active]" 
                                               value="1" {{ $level->is_active ? 'checked' : '' }}
                                               class="form-check-input bulk-active">
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge badge-outline-info">{{ number_format($level->percentage, 2) }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Total Active Percentage:</strong> <span id="bulk-total-percentage">0.00</span>% / 100.00%
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update All Levels</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Commission Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this commission level?</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This action cannot be undone. Any existing commission records for this level will remain but no new commissions will be distributed.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Level</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
// Calculate bulk total percentage
function calculateBulkTotal() {
    let total = 0;
    document.querySelectorAll('.bulk-percentage').forEach(function(input) {
        const checkbox = input.closest('.row').querySelector('.bulk-active');
        if (checkbox.checked) {
            total += parseFloat(input.value) || 0;
        }
    });
    
    document.getElementById('bulk-total-percentage').textContent = total.toFixed(2);
    
    // Update styling based on total
    const alertDiv = document.querySelector('#bulkUpdateModal .alert-info');
    if (total > 100) {
        alertDiv.className = 'alert alert-danger';
    } else if (total > 80) {
        alertDiv.className = 'alert alert-warning';
    } else {
        alertDiv.className = 'alert alert-info';
    }
}

// Event listeners for bulk update
document.querySelectorAll('.bulk-percentage, .bulk-active').forEach(function(input) {
    input.addEventListener('change', calculateBulkTotal);
});

// Initial calculation
calculateBulkTotal();

// Delete level function
function deleteLevel(levelId) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ route("admin.commission-levels.destroy", ":id") }}'.replace(':id', levelId);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Reset to defaults function
function resetToDefaults() {
    if (confirm('Are you sure you want to reset all commission levels to defaults? This will delete all current settings.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.commission-levels.reset-defaults") }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = csrfToken;
        
        form.appendChild(csrfField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
</x-layout>
