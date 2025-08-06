<x-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    
    @section('content')
    <div class="d-sm-flex d-block align-items-center justify-content-between page-header-breadcrumb">
        <h4 class="fw-medium mb-0">{{ $pageTitle }}</h4>
        <div class="ms-sm-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Withdrawal Methods</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 my-4" id="statistics-cards">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-primary">
                                <i class="fe fe-credit-card fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Total Methods</p>
                                    <h4 class="fw-semibold mb-1" id="total-methods">{{ $withdrawMethods->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-success">
                                <i class="fe fe-check-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Active Methods</p>
                                    <h4 class="fw-semibold mb-1" id="active-methods">{{ $withdrawMethods->where('status', true)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-warning">
                                <i class="fe fe-x-circle fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Inactive Methods</p>
                                    <h4 class="fw-semibold mb-1" id="inactive-methods">{{ $withdrawMethods->where('status', false)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="avatar avatar-md avatar-rounded bg-info">
                                <i class="fe fe-percent fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill ms-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <p class="text-muted mb-0">Percent Charge</p>
                                    <h4 class="fw-semibold mb-1" id="percent-methods">{{ $withdrawMethods->where('charge_type', 'percent')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        <i class="fe fe-credit-card me-2"></i>Withdrawal Methods Management
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.withdraw-methods.seed-defaults') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-info btn-sm" 
                                    onclick="return confirm('This will create/update default withdrawal methods. Continue?')">
                                <i class="fe fe-database me-1"></i>Seed Defaults
                            </button>
                        </form>
                        <a href="{{ route('admin.withdraw-methods.create') }}" class="btn btn-primary btn-sm">
                            <i class="fe fe-plus me-1"></i>Add Method
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fe fe-alert-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Key</th>
                                    <th>Status</th>
                                    <th>Limits</th>
                                    <th>Charge</th>
                                    <th>Processing Time</th>
                                    <th>Sort Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-methods">
                                @forelse($withdrawMethods as $method)
                                    <tr data-id="{{ $method->id }}" data-sort="{{ $method->sort_order }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    {!! $method->icon_html !!}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $method->name }}</h6>
                                                    @if($method->description)
                                                        <small class="text-muted">{{ Str::limit($method->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $method->method_key }}</code>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" 
                                                       type="checkbox" 
                                                       data-id="{{ $method->id }}"
                                                       {{ $method->status ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    <span class="badge bg-{{ $method->status ? 'success' : 'danger' }}">
                                                        {{ $method->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm">
                                                <div><strong>Min:</strong> ${{ number_format($method->min_amount, 2) }}</div>
                                                <div><strong>Max:</strong> ${{ number_format($method->max_amount, 2) }}</div>
                                                <div><strong>Daily:</strong> ${{ number_format($method->daily_limit, 2) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $method->charge_display }}</span>
                                        </td>
                                        <td>{{ $method->processing_time }}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm sort-order-input" 
                                                   style="width: 70px;" 
                                                   value="{{ $method->sort_order }}" 
                                                   data-id="{{ $method->id }}"
                                                   min="0">
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.withdraw-methods.show', $method->id) }}" 
                                                   class="btn btn-info-light" title="View">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.withdraw-methods.edit', $method->id) }}" 
                                                   class="btn btn-primary-light" title="Edit">
                                                    <i class="fe fe-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger-light delete-method" 
                                                        data-id="{{ $method->id }}" 
                                                        data-name="{{ $method->name }}" title="Delete">
                                                    <i class="fe fe-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fe fe-credit-card fs-48 text-muted mb-3"></i>
                                                <h5 class="text-muted">No withdrawal methods found</h5>
                                                <p class="text-muted">Get started by creating your first withdrawal method or seeding defaults.</p>
                                                <div class="mt-3">
                                                    <a href="{{ route('admin.withdraw-methods.create') }}" class="btn btn-primary me-2">
                                                        <i class="fe fe-plus me-1"></i>Add Method
                                                    </a>
                                                    <form action="{{ route('admin.withdraw-methods.seed-defaults') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info">
                                                            <i class="fe fe-database me-1"></i>Seed Defaults
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($withdrawMethods->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $withdrawMethods->firstItem() }} to {{ $withdrawMethods->lastItem() }} 
                                of {{ $withdrawMethods->total() }} results
                            </div>
                            {{ $withdrawMethods->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the withdrawal method "<strong id="delete-method-name"></strong>"?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="delete-form" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('script')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Status toggle functionality
            $('.status-toggle').on('change', function() {
                const methodId = $(this).data('id');
                const isChecked = $(this).is(':checked');
                
                $.ajax({
                    url: `/admin/withdraw-methods/${methodId}/toggle-status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Update badge
                            const badge = $(`.status-toggle[data-id="${methodId}"]`).siblings('label').find('.badge');
                            badge.removeClass('bg-success bg-danger')
                                 .addClass(response.status ? 'bg-success' : 'bg-danger')
                                 .text(response.status ? 'Active' : 'Inactive');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update status');
                        // Revert toggle
                        $(`.status-toggle[data-id="${methodId}"]`).prop('checked', !isChecked);
                    }
                });
            });

            // Delete method functionality
            $('.delete-method').on('click', function() {
                const methodId = $(this).data('id');
                const methodName = $(this).data('name');
                
                $('#delete-method-name').text(methodName);
                $('#delete-form').attr('action', `/admin/withdraw-methods/${methodId}`);
                $('#deleteModal').modal('show');
            });

            // Sort order update
            $('.sort-order-input').on('change', function() {
                const methodId = $(this).data('id');
                const newOrder = $(this).val();
                
                updateSortOrder(methodId, newOrder);
            });

            function updateSortOrder(methodId, sortOrder) {
                $.ajax({
                    url: '{{ route("admin.withdraw-methods.update-sort-order") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        items: [{
                            id: methodId,
                            sort_order: sortOrder
                        }]
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Sort order updated');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update sort order');
                    }
                });
            }

            // Load statistics
            loadStatistics();

            function loadStatistics() {
                $.get('{{ route("admin.withdraw-methods.statistics") }}', function(data) {
                    $('#total-methods').text(data.total_methods);
                    $('#active-methods').text(data.active_methods);
                    $('#inactive-methods').text(data.inactive_methods);
                    $('#percent-methods').text(data.percent_charge_methods);
                });
            }
        });
    </script>
    @endpush
</x-layout>
