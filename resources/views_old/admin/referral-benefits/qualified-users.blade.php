@extends('components.layout')
@section('title', 'Qualified Users - Referral Benefits')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Qualified Users</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.referral-benefits.index') }}">Referral Benefits</a></li>
                        <li class="breadcrumb-item active">Qualified Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.referral-benefits.qualified-users') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" 
                                       value="{{ request('username') }}" placeholder="Search username...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" 
                                       value="{{ request('email') }}" placeholder="Search email...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Min Referrals</label>
                                <input type="number" name="min_referrals" class="form-control" 
                                       value="{{ request('min_referrals') }}" placeholder="15">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search"></i>
                                    </button>
                                    <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Qualified Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Qualified Users ({{ $qualifiedUsers->total() }})
                        </h5>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportData()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($qualifiedUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Referrals</th>
                                        <th>Benefits</th>
                                        <th>Total Bonuses</th>
                                        <th>Qualified Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($qualifiedUsers as $benefit)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm flex-shrink-0 me-3">
                                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                            {{ strtoupper(substr($benefit->user->username ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $benefit->user->username ?? 'Unknown' }}</h6>
                                                        <small class="text-muted">{{ $benefit->user->email ?? 'No email' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success fs-12">
                                                    {{ $benefit->qualified_referrals_count }} qualified
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $benefit->user->referrals()->count() }} total
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        Transfer: {{ $benefit->transfer_bonus_percentage }}%
                                                    </span>
                                                    <span class="badge bg-success-subtle text-success">
                                                        Receive: {{ $benefit->receive_bonus_percentage }}%
                                                    </span>
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        Withdraw: -{{ $benefit->withdraw_reduction_percentage }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ${{ number_format($benefit->total_bonuses_earned, 2) }}
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $benefit->bonusTransactions()->count() }} transactions
                                                </small>
                                            </td>
                                            <td>
                                                <span>{{ $benefit->qualified_at->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $benefit->qualified_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($benefit->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="{{ route('admin.referral-benefits.user-details', $benefit->user_id) }}">
                                                                <i class="bx bx-show me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="{{ route('admin.users.edit', $benefit->user_id) }}">
                                                                <i class="bx bx-edit me-2"></i>Edit User
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        @if($benefit->is_active)
                                                            <li>
                                                                <button class="dropdown-item text-danger" 
                                                                        onclick="toggleUserStatus({{ $benefit->user_id }}, false)">
                                                                    <i class="bx bx-block me-2"></i>Deactivate Benefits
                                                                </button>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <button class="dropdown-item text-success" 
                                                                        onclick="toggleUserStatus({{ $benefit->user_id }}, true)">
                                                                    <i class="bx bx-check me-2"></i>Activate Benefits
                                                                </button>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <button class="dropdown-item text-warning" 
                                                                    onclick="recalculateUser({{ $benefit->user_id }})">
                                                                <i class="bx bx-refresh me-2"></i>Recalculate
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <p class="text-muted">
                                    Showing {{ $qualifiedUsers->firstItem() }} to {{ $qualifiedUsers->lastItem() }} 
                                    of {{ $qualifiedUsers->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $qualifiedUsers->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="avatar-xl mx-auto mb-4">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-36">
                                    <i class="bx bx-user-x"></i>
                                </div>
                            </div>
                            <h5>No Qualified Users Found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['username', 'email', 'min_referrals', 'status']))
                                    No users match your current filters. Try adjusting your search criteria.
                                @else
                                    No users have qualified for referral benefits yet.
                                @endif
                            </p>
                            @if(request()->hasAny(['username', 'email', 'min_referrals', 'status']))
                                <a href="{{ route('admin.referral-benefits.qualified-users') }}" class="btn btn-outline-primary">
                                    <i class="bx bx-refresh me-2"></i>Clear Filters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
function toggleUserStatus(userId, isActive) {
    const action = isActive ? 'activate' : 'deactivate';
    const title = isActive ? 'Activate Benefits?' : 'Deactivate Benefits?';
    const text = isActive 
        ? 'This user will start receiving referral benefits again.' 
        : 'This user will temporarily lose referral benefits.';

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/referral-benefits/toggle-user-status/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'An error occurred while updating status.', 'error');
            });
        }
    });
}

function recalculateUser(userId) {
    Swal.fire({
        title: 'Recalculate User Benefits?',
        text: 'This will refresh the user\'s qualification status and benefit percentages.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, recalculate!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/referral-benefits/recalculate-user/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'An error occurred while recalculating.', 'error');
            });
        }
    });
}

function exportData() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    
    // Create download link
    const downloadUrl = `{{ route('admin.referral-benefits.qualified-users') }}?${params.toString()}`;
    window.open(downloadUrl, '_blank');
}
</script>
@endpush
@endsection
