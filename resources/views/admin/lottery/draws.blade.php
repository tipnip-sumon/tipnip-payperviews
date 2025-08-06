<x-layout>
    @section('top_title', 'Lottery Draws Management')
    
    @section('content')
        <div class="row my-4">
            @section('title', 'Lottery Draws Management')
            
            <!-- Statistics Cards -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Draws</span> 
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ $stats['total_draws'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-primary my-auto float-end">
                                    <i class="fe fe-calendar"></i> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Pending Draws</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ $stats['pending_draws'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-success my-auto float-end">
                                    <i class="fe fe-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Completed Draws</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">{{ $stats['completed_draws'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-warning my-auto float-end">
                                    <i class="fe fe-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mt-0 text-start">
                                    <span class="fw-semibold">Total Prize Pool</span>
                                    <h3 class="mb-0 mt-1 text-white mb-2">${{ number_format($stats['total_prize_pool'] ?? 0, 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icon1 bg-white text-info my-auto float-end">
                                    <i class="fe fe-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-check"></i> Success</h6>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fe fe-alert-triangle"></i> Error</h6>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Lottery Draws Management --> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Lottery Draws Management</h4>
                            <p class="text-muted mb-0">Manage lottery draws and their status</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                                <i class="fe fe-plus"></i> Create New Draw
                            </a>
                            <a href="{{ route('admin.lottery.export') }}" class="btn btn-success">
                                <i class="fe fe-download"></i> Export
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form method="GET" action="{{ route('admin.lottery.draws') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="drawn" {{ request('status') == 'drawn' ? 'selected' : '' }}>Drawn</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-search"></i> Filter
                                            </button>
                                            <a href="{{ route('admin.lottery.draws') }}" class="btn btn-secondary">
                                                <i class="fe fe-x"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form id="bulkActionForm" method="POST" action="{{ route('admin.lottery.draws.bulk-action') }}">
                                    @csrf
                                    <div class="d-flex gap-2 align-items-center">
                                        <select name="action" class="form-select" style="width: auto;">
                                            <option value="">Bulk Actions</option>
                                            <option value="cancel">Cancel</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                        <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                                            Apply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Draws Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Draw Number</th>
                                        <th>Draw Date</th>
                                        <th>Total Tickets</th>
                                        <th>Prize Pool</th>
                                        <th>Status</th>
                                        <th>Winners</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($draws ?? [] as $draw)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="draw_ids[]" value="{{ $draw->id }}" 
                                                       class="form-check-input draw-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $draw->draw_number ?? '#' . $draw->id }}</strong>
                                            </td>
                                            <td>
                                                {{ $draw->draw_date ? $draw->draw_date->format('M d, Y') : 'N/A' }}
                                                <br><small class="text-muted">{{ $draw->draw_time ? $draw->draw_time->format('H:i') : '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $draw->total_tickets_sold ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">${{ number_format($draw->total_prize_pool ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                @switch($draw->status ?? 'pending')
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('drawn')
                                                        <span class="badge bg-info">Drawn</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Completed</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($draw->status ?? 'Unknown') }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if(isset($draw->winners) && $draw->winners->count() > 0)
                                                    <span class="badge bg-success">{{ $draw->winners->count() }}</span>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" 
                                                            id="dropdownMenuButton{{ $draw->id }}" data-bs-toggle="dropdown" 
                                                            aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $draw->id }}">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.lottery.draws.details', $draw->id) }}">
                                                                <i class="fe fe-eye"></i> View Details
                                                            </a>
                                                        </li>
                                                        @if($draw->status == 'pending')
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.lottery.draws.perform', $draw->id) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-success"
                                                                            onclick="return confirm('Are you sure you want to perform this draw?')">
                                                                        <i class="fe fe-play"></i> Perform Draw
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.lottery.draws.cancel', $draw->id) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-warning"
                                                                            onclick="return confirm('Are you sure you want to cancel this draw?')">
                                                                        <i class="fe fe-x-circle"></i> Cancel Draw
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.lottery.draws.delete', $draw->id) }}" 
                                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this draw?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fe fe-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fe fe-calendar display-4 text-muted"></i>
                                                    <h5 class="mt-3">No Draws Found</h5>
                                                    <p class="text-muted">Start by creating your first lottery draw.</p>
                                                    <a href="{{ route('admin.lottery.draws.create') }}" class="btn btn-primary">
                                                        <i class="fe fe-plus"></i> Create New Draw
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if(isset($draws) && $draws->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $draws->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

@push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script>
    // Select All Functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.draw-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk Action Confirmation
    function confirmBulkAction() {
        const selectedBoxes = document.querySelectorAll('.draw-checkbox:checked');
        const action = document.querySelector('select[name="action"]').value;
        
        if (selectedBoxes.length === 0) {
            alert('Please select at least one draw.');
            return false;
        }
        
        if (!action) {
            alert('Please select an action.');
            return false;
        }
        
        const actionText = action === 'delete' ? 'delete' : action;
        return confirm(`Are you sure you want to ${actionText} ${selectedBoxes.length} draw(s)?`);
    }

    // Add selected draw IDs to bulk action form
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const selectedBoxes = document.querySelectorAll('.draw-checkbox:checked');
        
        // Remove existing hidden inputs
        const existingInputs = this.querySelectorAll('input[name="draw_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add hidden inputs for selected draws
        selectedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'draw_ids[]';
            hiddenInput.value = checkbox.value;
            this.appendChild(hiddenInput);
        });
    });
</script>
@endpush
</x-layout>
