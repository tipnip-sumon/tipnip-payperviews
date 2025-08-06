<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 my-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-primary">
                                <i class="ti ti-users fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Total Users</h6>
                            <span class="fs-12 text-muted">All registered users</span>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-semibold mb-0">{{ number_format($stats['total_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-success">
                                <i class="ti ti-user-check fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Active Users</h6>
                            <span class="fs-12 text-muted">Currently active</span>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-semibold mb-0">{{ number_format($stats['active_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-warning">
                                <i class="ti ti-user-pause fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Inactive Users</h6>
                            <span class="fs-12 text-muted">Deactivated accounts</span>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-semibold mb-0">{{ number_format($stats['inactive_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-danger">
                                <i class="ti ti-user-x fs-16"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Banned Users</h6>
                            <span class="fs-12 text-muted">Banned accounts</span>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-semibold mb-0">{{ number_format($stats['banned_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="ri-user-line me-2"></i>Users Management
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.users.export') }}" class="btn btn-sm btn-primary">
                            <i class="ri-download-line me-1"></i>Export Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Debug Info -->
                    @if(!auth('admin')->check())
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> You are not logged in as admin. Please <a href="{{ route('admin.index') }}">login as admin</a> to view users.
                        </div>
                    @else
                        <div class="alert alert-success">
                            <strong>Success:</strong> Logged in as: {{ auth('admin')->user()->username }}
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Banned</option>
                                </select>
                        </div>
                        <div class="col-md-3">
                                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                                    <option value="200" {{ request('per_page') == '200' ? 'selected' : '' }}>200 per page</option>
                                </select>
                        </div>
                        <div class="col-md-4">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Search users..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Name</th>
                                    <th width="12%">Username</th>
                                    <th width="18%">Email</th>
                                    <th width="8%">Status</th>
                                    <th width="15%">Balance</th>
                                    <th width="10%">Joined</th>
                                    <th width="12%">Actions</th>
                                    <th width="5%">ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(auth('admin')->check() && isset($users))
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                            <td>
                                                <div class="user-name">
                                                    <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                                                    @if($user->mobile)
                                                        <br><small class="text-muted">{{ $user->mobile }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-transparent">{{ $user->username }}</span>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                                                <br>
                                                <div class="verification-badges mt-1">
                                                    @if($user->ev)
                                                        <span class="badge bg-success-transparent" title="Email Verified">✓ Email</span>
                                                    @else
                                                        <span class="badge bg-danger-transparent" title="Email Not Verified">✗ Email</span>
                                                    @endif
                                                    
                                                    @if($user->kv)
                                                        <span class="badge bg-info-transparent" title="KYC Verified">✓ KYC</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($user->status == 0)
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-danger">Banned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="balance-info">
                                                    <div><strong>D:</strong> ${{ number_format($user->deposit_wallet ?? 0, 2) }}</div>
                                                    <div><strong>I:</strong> ${{ number_format($user->interest_wallet ?? 0, 2) }}</div>
                                                    <div class="text-primary"><strong>Total: ${{ number_format(($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0), 2) }}</strong></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $user->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td><small class="text-muted">#{{ $user->id }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fe fe-users fs-48 text-muted mb-3"></i>
                                                    <h6 class="text-muted">No users found</h6>
                                                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="auth-required">
                                                <i class="fe fe-lock fs-48 text-warning mb-3"></i>
                                                <h6 class="text-warning">Authentication Required</h6>
                                                <p class="text-muted">Please log in as admin to view users</p>
                                                <a href="{{ route('admin.index') }}" class="btn btn-primary btn-sm">Login as Admin</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination and Info -->
                    @if(auth('admin')->check() && isset($users) && $users->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                    (Page {{ $users->currentPage() }} of {{ $users->lastPage() }})
                                </span>
                            </div>
                            <div class="pagination-links">
                                {{ $users->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('styles')
    <style>
        .user-name {
            line-height: 1.2;
        }
        .balance-info {
            font-size: 0.85rem;
            line-height: 1.3;
        }
        .verification-badges .badge {
            font-size: 0.7rem;
            margin-right: 2px;
        }
        .empty-state, .auth-required {
            padding: 2rem;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .pagination-info {
            font-size: 0.9rem;
        }
        .btn-group .btn {
            border-radius: 0.25rem;
            margin-right: 2px;
        }
        .table thead th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .table td {
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        
        /* Loading animation */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .table td, .table th {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Auto-submit search form after typing stops
        let searchTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 1000); // Submit after 1 second of no typing
        });

        // Add loading state to form submissions
        document.getElementById('filterForm').addEventListener('submit', function() {
            document.querySelector('.table-responsive').classList.add('loading');
        });

        // Remove loading state when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.table-responsive').classList.remove('loading');
        });
    </script>
    @endpush
</x-layout>
