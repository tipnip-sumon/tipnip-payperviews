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
                    <li class="breadcrumb-item active" aria-current="page">{{ $withdrawMethod->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row my-4">
        <!-- Method Details -->
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                {!! $withdrawMethod->icon_html !!}
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $withdrawMethod->name }}</h5>
                                <small class="text-muted">{{ $withdrawMethod->method_key }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.withdraw-methods.edit', $withdrawMethod->id) }}" class="btn btn-primary btn-sm">
                            <i class="fe fe-edit me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteMethod()">
                            <i class="fe fe-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Status and Basic Info -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Status Information</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-3" style="width: 100px;">Status:</span>
                                    <span class="badge bg-{{ $withdrawMethod->status ? 'success' : 'danger' }} fs-12">
                                        {{ $withdrawMethod->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-3" style="width: 100px;">Currency:</span>
                                    <span class="fw-semibold">{{ $withdrawMethod->currency }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-3" style="width: 100px;">Sort Order:</span>
                                    <span class="fw-semibold">{{ $withdrawMethod->sort_order }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Processing Info -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Processing Information</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-3" style="width: 120px;">Processing Time:</span>
                                    <span class="fw-semibold">{{ $withdrawMethod->processing_time }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-3" style="width: 120px;">Created:</span>
                                    <span class="fw-semibold">{{ $withdrawMethod->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Limits -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Amount Limits</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <h5 class="text-primary mb-1">${{ number_format($withdrawMethod->min_amount, 2) }}</h5>
                                            <small class="text-muted">Minimum Amount</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <h5 class="text-warning mb-1">${{ number_format($withdrawMethod->max_amount, 2) }}</h5>
                                            <small class="text-muted">Maximum Amount</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <h5 class="text-info mb-1">${{ number_format($withdrawMethod->daily_limit, 2) }}</h5>
                                            <small class="text-muted">Daily Limit</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charge Information -->
                        <div class="col-md-12">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Charge Configuration</h6>
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fe fe-info me-2"></i>
                                        <div>
                                            <strong>Charge Type:</strong> {{ ucfirst($withdrawMethod->charge_type) }}
                                            <br>
                                            <strong>Charge Amount:</strong> {{ $withdrawMethod->charge_display }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Charge Examples -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h6>$100 Withdrawal</h6>
                                                <p class="mb-1">Charge: ${{ number_format($withdrawMethod->calculateCharge(100), 2) }}</p>
                                                <p class="text-success mb-0">You'll receive: ${{ number_format($withdrawMethod->getFinalAmount(100), 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h6>$500 Withdrawal</h6>
                                                <p class="mb-1">Charge: ${{ number_format($withdrawMethod->calculateCharge(500), 2) }}</p>
                                                <p class="text-success mb-0">You'll receive: ${{ number_format($withdrawMethod->getFinalAmount(500), 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h6>$1000 Withdrawal</h6>
                                                <p class="mb-1">Charge: ${{ number_format($withdrawMethod->calculateCharge(1000), 2) }}</p>
                                                <p class="text-success mb-0">You'll receive: ${{ number_format($withdrawMethod->getFinalAmount(1000), 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($withdrawMethod->description)
                        <div class="col-md-12">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Description</h6>
                                <p class="text-muted">{{ $withdrawMethod->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Instructions -->
                        @if($withdrawMethod->instructions)
                        <div class="col-md-12">
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">User Instructions</h6>
                                <div class="alert alert-light">
                                    {{ $withdrawMethod->instructions }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Actions -->
        <div class="col-xl-4">
            <!-- Quick Actions -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Quick Actions</div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.withdraw-methods.edit', $withdrawMethod->id) }}" class="btn btn-primary">
                            <i class="fe fe-edit me-2"></i>Edit Method
                        </a>
                        
                        <button type="button" class="btn btn-{{ $withdrawMethod->status ? 'warning' : 'success' }}" 
                                onclick="toggleStatus()">
                            <i class="fe fe-{{ $withdrawMethod->status ? 'eye-off' : 'eye' }} me-2"></i>
                            {{ $withdrawMethod->status ? 'Deactivate' : 'Activate' }}
                        </button>
                        
                        <a href="{{ route('admin.withdraw-methods.index') }}" class="btn btn-outline-secondary">
                            <i class="fe fe-arrow-left me-2"></i>Back to List
                        </a>
                        
                        <button type="button" class="btn btn-outline-danger" onclick="deleteMethod()">
                            <i class="fe fe-trash me-2"></i>Delete Method
                        </button>
                    </div>
                </div>
            </div>

            <!-- Method Statistics (placeholder for future implementation) -->
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Usage Statistics</div>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <i class="fe fe-bar-chart fs-48 text-muted mb-3"></i>
                        <p class="text-muted">Statistics will be available after users start using this method.</p>
                    </div>
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
                    <p>Are you sure you want to delete the withdrawal method "<strong>{{ $withdrawMethod->name }}</strong>"?</p>
                    <p class="text-danger small">This action cannot be undone and may affect existing withdrawal requests.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.withdraw-methods.destroy', $withdrawMethod->id) }}" method="POST" class="d-inline">
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
    <script>
        function toggleStatus() {
            $.ajax({
                url: `/admin/withdraw-methods/{{ $withdrawMethod->id }}/toggle-status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function() {
                    toastr.error('Failed to update status');
                }
            });
        }

        function deleteMethod() {
            $('#deleteModal').modal('show');
        }
    </script>
    @endpush
</x-layout>
