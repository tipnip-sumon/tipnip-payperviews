<x-layout>
    @section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4 my-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $pageTitle ?? 'KYC Verifications Management' }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">KYC Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total KYC</p>
                                <h4 class="mb-2" id="total-kyc">{{ $kycVerifications->total() ?? 0 }}</h4>
                                <p class="text-truncate mb-0">All submissions</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Pending</p>
                                <h4 class="mb-2" id="pending-kyc">0</h4>
                                <p class="text-truncate mb-0">Awaiting review</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Approved</p>
                                <h4 class="mb-2" id="approved-kyc">0</h4>
                                <p class="text-truncate mb-0">Verified users</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Rejected</p>
                                <h4 class="mb-2" id="rejected-kyc">0</h4>
                                <p class="text-truncate mb-0">Need resubmission</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.kyc.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Filter by Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Search User</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Name, Username, or Email" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.kyc.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- KYC List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">KYC Verifications</h4>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" onclick="bulkApprove()">
                                    <i class="fas fa-check"></i> Bulk Approve
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>User</th>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Submitted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kycVerifications as $kyc)
                                        @if($kyc && $kyc->id)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input kyc-checkbox" 
                                                       value="{{ $kyc->id }}" 
                                                       {{ $kyc->status != 'pending' ? 'disabled' : '' }}>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ $kyc->user && $kyc->user->avatar ? $kyc->user->avatar_url : asset('assets/images/users/16.png') }}" 
                                                             alt="" class="avatar-xs rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-0">
                                                            {{ $kyc->user ? ($kyc->user->firstname . ' ' . $kyc->user->lastname) : 'N/A' }}
                                                        </h6>
                                                        <small class="text-muted">{{ $kyc->user->username ?? 'N/A' }}</small><br>
                                                        <small class="text-muted">{{ $kyc->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $kyc->document_type ? ucwords(str_replace('_', ' ', $kyc->document_type)) : 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($kyc->status == 'approved') bg-success
                                                    @elseif($kyc->status == 'pending') bg-warning
                                                    @elseif($kyc->status == 'rejected') bg-danger
                                                    @elseif($kyc->status == 'under_review') bg-info
                                                    @else bg-secondary
                                                    @endif
                                                ">
                                                    {{ $kyc->status ? ucfirst($kyc->status) : 'Unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $kyc->submitted_at ? $kyc->submitted_at->format('M d, Y') : ($kyc->created_at ? $kyc->created_at->format('M d, Y') : 'N/A') }}
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.kyc.show', $kyc->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($kyc->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="quickApprove({{ $kyc->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="quickReject({{ $kyc->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @elseif($kyc->status == 'under_review')
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="quickApprove({{ $kyc->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="quickReject({{ $kyc->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5>No KYC Verifications Found</h5>
                                                <p class="text-muted">No KYC verifications match your current filters.</p>
                                            </div>
                                        </td>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($kycVerifications->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $kycVerifications->firstItem() }} to {{ $kycVerifications->lastItem() }} 
                                of {{ $kycVerifications->total() }} results
                            </div>
                            <div>
                                {{ $kycVerifications->appends(request()->query())->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Modal -->
    <div class="modal fade" id="quickActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickActionTitle">Update KYC Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickActionForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="kycId" name="kyc_id">
                        <input type="hidden" id="actionStatus" name="status">
                        
                        <div class="mb-3">
                            <label for="adminRemarks" class="form-label">Admin Remarks</label>
                            <textarea class="form-control" id="adminRemarks" name="admin_remarks" 
                                      rows="3" placeholder="Enter remarks (optional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="confirmAction">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection

    @push('script')
    <script>
        // Handle quick action form - FIXED VERSION
        document.getElementById('quickActionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const kycId = formData.get('kyc_id');
            
            if (!kycId) {
                alert('Invalid KYC ID');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('confirmAction');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;
            
            // Convert FormData to JSON for better compatibility
            const requestData = {
                status: formData.get('status'),
                admin_remarks: formData.get('admin_remarks') || ''
            };
            
            console.log('Sending request:', requestData);
            console.log('KYC ID:', kycId);
            
            fetch(`/admin/kyc/${kycId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Create success alert
                    showAlert('success', data.message);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('quickActionModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Reload page after delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', 'Error: ' + (data.message || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('danger', 'An error occurred while updating the status: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Helper function to show alerts
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                    alert.remove();
                }
            });
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert at the top of container
            const container = document.querySelector('.container-fluid');
            const firstRow = container.querySelector('.row');
            if (firstRow) {
                container.insertBefore(alertDiv, firstRow);
            } else {
                container.prepend(alertDiv);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Quick Approve - FIXED
        function quickApprove(kycId) {
            if (!kycId || kycId == 'undefined') {
                console.error('Invalid KYC ID:', kycId);
                alert('Invalid KYC ID');
                return;
            }
            
            console.log('Quick approve for KYC ID:', kycId);
            
            document.getElementById('kycId').value = kycId;
            document.getElementById('actionStatus').value = 'approved';
            document.getElementById('quickActionTitle').textContent = 'Approve KYC Verification';
            document.getElementById('confirmAction').textContent = 'Approve';
            document.getElementById('confirmAction').className = 'btn btn-success';
            
            const modal = new bootstrap.Modal(document.getElementById('quickActionModal'));
            modal.show();
        }

        // Quick Reject - FIXED
        function quickReject(kycId) {
            if (!kycId || kycId == 'undefined') {
                console.error('Invalid KYC ID:', kycId);
                alert('Invalid KYC ID');
                return;
            }
            
            console.log('Quick reject for KYC ID:', kycId);
            
            document.getElementById('kycId').value = kycId;
            document.getElementById('actionStatus').value = 'rejected';
            document.getElementById('quickActionTitle').textContent = 'Reject KYC Verification';
            document.getElementById('confirmAction').textContent = 'Reject';
            document.getElementById('confirmAction').className = 'btn btn-danger';
            
            const modal = new bootstrap.Modal(document.getElementById('quickActionModal'));
            modal.show();
        }

        // Bulk Approve - FIXED
        function bulkApprove() {
            const selectedCheckboxes = document.querySelectorAll('.kyc-checkbox:checked');
            const selectedKycs = Array.from(selectedCheckboxes).map(cb => cb.value).filter(id => id && id !== 'undefined');
            
            console.log('Selected KYCs:', selectedKycs);
            
            if (selectedKycs.length === 0) {
                alert('Please select at least one KYC verification to approve.');
                return;
            }

            if (confirm(`Are you sure you want to approve ${selectedKycs.length} KYC verification(s)?`)) {
                // Show loading state
                const bulkBtn = document.querySelector('button[onclick="bulkApprove()"]');
                const originalText = bulkBtn.innerHTML;
                bulkBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                bulkBtn.disabled = true;
                
                fetch('{{ route("admin.kyc.bulk-approve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        kyc_ids: selectedKycs
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert('danger', 'Error: ' + (data.message || 'Unknown error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Bulk approve error:', error);
                    showAlert('danger', 'An error occurred during bulk approval: ' + error.message);
                })
                .finally(() => {
                    // Reset button state
                    bulkBtn.innerHTML = originalText;
                    bulkBtn.disabled = false;
                });
            }
        }

        // Load statistics - FIXED
        function loadStatistics() {
            fetch('{{ route("admin.kyc.statistics") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Statistics loaded:', data);
                    document.getElementById('total-kyc').textContent = data.total || 0;
                    document.getElementById('pending-kyc').textContent = data.pending || 0;
                    document.getElementById('approved-kyc').textContent = data.approved || 0;
                    document.getElementById('rejected-kyc').textContent = data.rejected || 0;
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                });
        }

        // Select all checkboxes - FIXED
        document.addEventListener('DOMContentLoaded', function() {
            // Load statistics
            loadStatistics();
            
            // Setup select all functionality
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.kyc-checkbox:not(:disabled)');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
            
            // Auto-hide existing alerts after 5 seconds
            document.querySelectorAll('.alert').forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            });
        });
    </script>
    @endpush
</x-layout>