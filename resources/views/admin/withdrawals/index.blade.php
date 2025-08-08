<x-layout>
    @section('page_title', 'Withdrawal Management')
    
    @section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <style>
        /* Account details styling */
        .details-content {
            max-height: 80px;
            overflow-y: auto;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem !important;
            line-height: 1.4;
            white-space: pre-wrap;
            word-break: break-all;
        }
        
        .copy-btn {
            padding: 2px 6px;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 3px;
            transition: all 0.2s ease;
        }
        
        .copy-btn:hover {
            transform: scale(1.05);
        }
        
        .copy-btn i {
            font-size: 0.8rem;
        }
        
        /* DataTables responsive styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }
        
        /* Custom styling for better mobile experience */
        @media (max-width: 768px) {
            .details-content {
                max-height: 60px;
                font-size: 0.7rem !important;
            }
            
            .copy-btn {
                padding: 1px 4px;
                font-size: 0.65rem;
            }
        }
        
        /* Bulk actions styling */
        #bulkActions {
            display: none;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        /* Toast container */
        .toast-container {
            z-index: 9999;
        }
        
        .toast {
            min-width: 300px;
        }
    </style>
    @endsection
    @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="ri-money-dollar-circle-line me-2"></i>
                                    Withdrawal Management
                                </h4>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary fs-6">
                                        Total: {{ number_format($withdrawals->total()) }}
                                    </span>
                                    @if($withdrawals->total() > 5000)
                                        <span class="badge bg-warning fs-6">
                                            <i class="ri-speed-line me-1"></i>Large Dataset
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="method" class="form-label">Payment Method</label>
                                    <select name="method" id="method" class="form-select">
                                        <option value="">All Methods</option>
                                        @foreach($paymentMethods as $methodId => $methodName)
                                            <option value="{{ $methodId }}" {{ request('method') == $methodId ? 'selected' : '' }}>
                                                {{ $methodName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="per_page" class="form-label">Records per Page</label>
                                    <select name="per_page" id="per_page" class="form-select">
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                        <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                                        <option value="1000" {{ $perPage == 1000 ? 'selected' : '' }}>1,000</option>
                                        <option value="5000" {{ $perPage == 5000 ? 'selected' : '' }}>5,000</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-search-line me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-secondary">
                                            <i class="ri-refresh-line me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Bulk Actions -->
                        <div id="bulkActions">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">
                                    <span id="selectedCount">0</span> withdrawals selected
                                </span>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                                        <i class="ri-check-line me-1"></i>Bulk Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="bulkReject()">
                                        <i class="ri-close-line me-1"></i>Bulk Reject
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="withdrawalsTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>TRX</th>
                                        <th>Amount</th>
                                        <th>Charge</th>
                                        <th>Final Amount</th>
                                        <th>Method</th>
                                        <th>Account Details</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawals as $withdrawal)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input withdrawal-checkbox" value="{{ $withdrawal->id }}">
                                            </td>
                                            <td><span class="badge bg-secondary">#{{ $withdrawal->id }}</span></td>
                                            <td>
                                                <div>
                                                    <strong>{{ $withdrawal->user->username ?? 'N/A' }}</strong><br>
                                                </div>
                                            </td>
                                            <td><code>{{ $withdrawal->trx }}</code></td>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    ${{ number_format($withdrawal->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-danger">
                                                    ${{ number_format($withdrawal->charge, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    ${{ number_format($withdrawal->final_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($withdrawal->method->name ?? 'N/A') }}</span>
                                            </td>
                                            <td>
                                                <div class="position-relative">
                                                    <div class="details-content">
                                                        @php
                                                            $withdrawalInfo = $withdrawal->withdraw_information;
                                                            $displayData = '';
                                                            $copyData = '';
                                                            
                                                            // Force decode JSON if it's a string
                                                            if (is_string($withdrawalInfo)) {
                                                                $decoded = json_decode($withdrawalInfo, true);
                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                    $withdrawalInfo = $decoded;
                                                                }
                                                            }
                                                            
                                                            // Only show the 'details' field if it exists
                                                            if (is_array($withdrawalInfo) || is_object($withdrawalInfo)) {
                                                                $infoArray = (array)$withdrawalInfo;
                                                                if (isset($infoArray['details']) && !empty($infoArray['details'])) {
                                                                    $displayData = htmlspecialchars($infoArray['details']);
                                                                    $copyData = $infoArray['details'];
                                                                }
                                                            } elseif (!empty($withdrawalInfo) && is_string($withdrawalInfo)) {
                                                                // If it's still a string after attempted decode, show it as-is
                                                                $displayData = "<div class='text-info'>{$withdrawalInfo}</div>";
                                                                $copyData = $withdrawalInfo;
                                                            }
                                                            
                                                            // Fallback if no data found
                                                            if (empty($displayData)) {
                                                                $displayData = '<span class="text-muted">No account details provided</span>';
                                                                $copyData = 'No account details provided';
                                                            }
                                                        @endphp
                                                        
                                                        {!! $displayData !!}
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm copy-btn mt-1"
                                                            onclick="copyToClipboard(`{{ addslashes($copyData) }}`, this)"
                                                            title="Copy account details">
                                                        <i class="ri-file-copy-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                @if($withdrawal->status == 2)
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($withdrawal->status == 1)
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($withdrawal->status == 3)
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $withdrawal->created_at->format('M d, Y') }}<br>{{ $withdrawal->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                @if($withdrawal->status == 2)
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                                onclick="approveWithdrawal({{ $withdrawal->id }})"
                                                                title="Approve withdrawal">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="rejectWithdrawal({{ $withdrawal->id }})"
                                                                title="Reject withdrawal">
                                                            <i class="ri-close-line"></i>
                                                        </button>
                                                        <a href="{{ url('/admin/withdrawals/' . $withdrawal->id) }}" 
                                                           class="btn btn-info btn-sm"
                                                           title="View details">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ url('/admin/withdrawals/' . $withdrawal->id) }}" 
                                                       class="btn btn-info btn-sm"
                                                       title="View details">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                                    No withdrawals found
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($withdrawals->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $withdrawals->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this withdrawal?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="approveForm" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this withdrawal?</p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Reason for rejection:</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="rejectForm" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="reason" id="rejectReasonField">
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionTitle">Bulk Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bulkApproveContent">
                        <p>Are you sure you want to approve the selected withdrawals?</p>
                    </div>
                    <div id="bulkRejectContent">
                        <p>Are you sure you want to reject the selected withdrawals?</p>
                        <div class="mb-3">
                            <label for="bulkRejectReason" class="form-label">Reason for rejection:</label>
                            <textarea class="form-control" id="bulkRejectReason" name="reason" rows="3" placeholder="Enter reason for rejection..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="bulkActionForm" action="{{ route('admin.withdrawals.bulk-action') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" id="bulkActionType" name="action">
                        <input type="hidden" id="bulkWithdrawals" name="withdrawals">
                        <input type="hidden" id="bulkRejectReasonField" name="reason">
                        <button type="submit" id="bulkActionBtn" class="btn">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @push('script')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        // Withdrawal management functions in isolated scope
        (function() {
            $(document).ready(function() {
                // Initialize DataTables
                $('#withdrawalsTable').DataTable({
                    responsive: true,
                    pageLength: {{ $perPage }},
                    lengthMenu: [50, 100, 500, 1000, 5000],
                    order: [[1, 'desc']], // Order by ID column descending
                    columnDefs: [
                        { 
                            targets: [0, -1], // First and last columns (checkbox and actions)
                            orderable: false 
                        },
                        {
                            targets: '_all',
                            className: 'text-nowrap'
                        }
                    ],
                    language: {
                        lengthMenu: "Show _MENU_ withdrawals per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ withdrawals",
                        infoEmpty: "No withdrawals found",
                        search: "Search withdrawals:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    drawCallback: function() {
                        // Reinitialize tooltips after each draw
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });

                // Handle bulk actions
                $('#selectAll').on('change', function() {
                    $('.withdrawal-checkbox').prop('checked', this.checked);
                    updateBulkActions();
                });

                $(document).on('change', '.withdrawal-checkbox', function() {
                    updateBulkActions();
                });

                function updateBulkActions() {
                    const checkedBoxes = $('.withdrawal-checkbox:checked');
                    const count = checkedBoxes.length;
                    
                    $('#selectedCount').text(count);
                    
                    if (count > 0) {
                        $('#bulkActions').show();
                    } else {
                        $('#bulkActions').hide();
                    }
                    
                    // Update select all checkbox state
                    const totalCheckboxes = $('.withdrawal-checkbox').length;
                    $('#selectAll').prop('indeterminate', count > 0 && count < totalCheckboxes);
                    $('#selectAll').prop('checked', count === totalCheckboxes);
                }

                // Add form submission handler for bulk actions
                const bulkForm = document.getElementById('bulkActionForm');
                if (bulkForm) {
                    bulkForm.addEventListener('submit', function(e) {
                        const actionType = document.getElementById('bulkActionType').value;
                        
                        // If rejecting, validate that reason is provided
                        if (actionType === 'reject') {
                            const reason = document.getElementById('bulkRejectReason').value.trim();
                            if (!reason) {
                                e.preventDefault();
                                alert('Please provide a reason for rejection.');
                                return false;
                            }
                            document.getElementById('bulkRejectReasonField').value = reason;
                        }
                        
                        // Show loading state
                        const submitBtn = document.getElementById('bulkActionBtn');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                        
                        // Allow form to submit normally
                        return true;
                    });
                }
            });
        })();

        // Global functions that need to be accessible from onclick handlers
        function approveWithdrawal(id) {
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            const form = document.getElementById('approveForm');
            form.action = `/admin/withdrawals/${id}/approve`;
            modal.show();
        }

        function rejectWithdrawal(id) {
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            const form = document.getElementById('rejectForm');
            form.action = `/admin/withdrawals/${id}/reject`;
            
            // Add form submission handler for individual reject
            form.onsubmit = function(e) {
                const reason = document.getElementById('rejectReason').value.trim();
                if (!reason) {
                    e.preventDefault();
                    alert('Please provide a reason for rejection.');
                    return false;
                }
                document.getElementById('rejectReasonField').value = reason;
                return true;
            };
            
            modal.show();
        }

        function bulkApprove() {
            const checkedBoxes = document.querySelectorAll('.withdrawal-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one withdrawal to approve.');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            document.getElementById('bulkActionType').value = 'approve';
            document.getElementById('bulkWithdrawals').value = JSON.stringify(ids);
            document.getElementById('bulkRejectReasonField').value = ''; // Clear reason for approve
            document.getElementById('bulkActionTitle').textContent = `Bulk Approve ${ids.length} Withdrawals`;
            document.getElementById('bulkApproveContent').style.display = 'block';
            document.getElementById('bulkRejectContent').style.display = 'none';
            document.getElementById('bulkActionBtn').className = 'btn btn-success';
            document.getElementById('bulkActionBtn').textContent = 'Approve Selected';

            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            modal.show();
        }

        function bulkReject() {
            const checkedBoxes = document.querySelectorAll('.withdrawal-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one withdrawal to reject.');
                return;
            }

            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            document.getElementById('bulkActionType').value = 'reject';
            document.getElementById('bulkWithdrawals').value = JSON.stringify(ids);
            document.getElementById('bulkActionTitle').textContent = `Bulk Reject ${ids.length} Withdrawals`;
            document.getElementById('bulkApproveContent').style.display = 'none';
            document.getElementById('bulkRejectContent').style.display = 'block';
            document.getElementById('bulkActionBtn').className = 'btn btn-danger';
            document.getElementById('bulkActionBtn').textContent = 'Reject Selected';

            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            modal.show();
        }

        // Copy to clipboard functionality
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success feedback
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="ri-check-line text-success"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-primary');
                
                // Show toast notification
                showToast('Account details copied to clipboard!', 'success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function(err) {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                showToast('Account details copied to clipboard!', 'success');
                
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="ri-check-line text-success"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-primary');
                
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            });
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toastId = 'toast_' + Date.now();
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'primary'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="ri-${type === 'success' ? 'check-circle' : 'information'}-line me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            // Show toast
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 3000
            });
            bsToast.show();

            // Remove toast element after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
    </script>
    @endpush
</x-layout>
