<x-layout>

@section('title', 'Support Tickets')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
<style>
.ticket-priority-urgent { border-left: 4px solid #dc3545; }
.ticket-priority-high { border-left: 4px solid #ffc107; }
.ticket-priority-normal { border-left: 4px solid #17a2b8; }
.ticket-priority-low { border-left: 4px solid #28a745; }
.ticket-starred { background-color: #fff3cd; }
.attachment-item {
    display: inline-block;
    margin: 5px;
    padding: 8px 12px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    text-decoration: none;
    color: #495057;
}
.attachment-item:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #495057;
}
.reply-container {
    max-height: 400px;
    overflow-y: auto;
}
.admin-reply {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}
.user-reply {
    background-color: #f3e5f5;
    border-left: 4px solid #9c27b0;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Support Tickets</h1>
            <p class="text-muted">Manage and respond to customer support requests</p>
        </div>
        <div>
            <a href="{{ route('admin.support.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-primary" id="export-btn">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="status-filter" class="form-label">Status</label>
                        <select class="form-select" id="status-filter" name="status">
                            <option value="">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="pending">Pending</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="priority-filter" class="form-label">Priority</label>
                        <select class="form-select" id="priority-filter" name="priority">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date-from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date-from" name="date_from">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date-to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date-to" name="date_to">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" id="apply-filters">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" id="clear-filters">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Support Tickets</h6>
            <div>
                <button type="button" class="btn btn-sm btn-warning" id="bulk-actions-btn" disabled>
                    <i class="fas fa-tasks me-2"></i>Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tickets-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Replies</th>
                            <th>Last Activity</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticket-details-content">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-labelledby="bulkActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionsModalLabel">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulk-action-form">
                    <div class="mb-3">
                        <label for="bulk-action" class="form-label">Select Action</label>
                        <select class="form-select" id="bulk-action" name="action" required>
                            <option value="">Choose action...</option>
                            <option value="status_open">Mark as Open</option>
                            <option value="status_pending">Mark as Pending</option>
                            <option value="status_closed">Mark as Closed</option>
                            <option value="star">Star Tickets</option>
                            <option value="unstar">Unstar Tickets</option>
                            <option value="delete" class="text-danger">Delete Tickets</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <strong>Selected Tickets:</strong> <span id="selected-count">0</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="execute-bulk-action">Execute Action</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- jQuery (load first) -->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    let table = $('#tickets-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.support.tickets") }}',
            data: function(d) {
                d.status = $('#status-filter').val();
                d.priority = $('#priority-filter').val();
                d.date_from = $('#date-from').val();
                d.date_to = $('#date-to').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                }
            },
            {data: 'id', name: 'id'},
            {data: 'subject', name: 'subject'},
            {data: 'user', name: 'user'},
            {data: 'status_badge', name: 'status', orderable: false},
            {data: 'priority_badge', name: 'priority', orderable: false},
            {data: 'replies_count', name: 'replies_count', orderable: false},
            {data: 'last_activity', name: 'last_activity', orderable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[8, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
        }
    });

    // Apply filters
    $('#apply-filters').click(function() {
        table.ajax.reload();
    });

    // Clear filters
    $('#clear-filters').click(function() {
        $('#filter-form')[0].reset();
        table.ajax.reload();
    });

    // Select all checkboxes
    $('#select-all').change(function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkActionsButton();
    });

    // Individual checkbox change
    $(document).on('change', '.row-checkbox', function() {
        updateBulkActionsButton();
        
        // Update select all checkbox
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update bulk actions button
    function updateBulkActionsButton() {
        const checkedCount = $('.row-checkbox:checked').length;
        $('#bulk-actions-btn').prop('disabled', checkedCount === 0);
        $('#selected-count').text(checkedCount);
    }

    // View ticket details
    $(document).on('click', '.view-ticket', function(e) {
        e.preventDefault();
        const ticketId = $(this).data('id');
        
        // Show loading in modal
        $('#ticket-details-content').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading ticket details...</p>
            </div>
        `);
        
        $('#ticketModal').modal('show');
        
        // Load ticket details
        const showUrl = '{{ route("admin.support.show", ":id") }}'.replace(':id', ticketId);
        $.get(showUrl)
            .done(function(response) {
                if (response.success) {
                    $('#ticket-details-content').html(response.html);
                } else {
                    $('#ticket-details-content').html('<div class="alert alert-danger">Failed to load ticket details.</div>');
                }
            })
            .fail(function() {
                $('#ticket-details-content').html('<div class="alert alert-danger">Error loading ticket details.</div>');
            });
    });

    // Star/unstar ticket
    $(document).on('click', '.star-ticket', function(e) {
        e.preventDefault();
        const ticketId = $(this).data('id');
        const button = $(this);
        
        const starUrl = '{{ route("admin.support.toggle-star", ":id") }}'.replace(':id', ticketId);
        $.post(starUrl, {
            _token: '{{ csrf_token() }}'
        })
            .done(function(response) {
                if (response.success) {
                    button.toggleClass('text-warning text-muted');
                    showAlert('success', response.message);
                } else {
                    showAlert('error', 'Failed to update ticket');
                }
            })
            .fail(function() {
                showAlert('error', 'Error updating ticket');
            });
    });

    // Change status
    $(document).on('click', '.change-status', function(e) {
        e.preventDefault();
        const ticketId = $(this).data('id');
        const status = $(this).data('status');
        
        const statusUrl = '{{ route("admin.support.update-status", ":id") }}'.replace(':id', ticketId);
        $.post(statusUrl, {
            status: status,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                table.ajax.reload(null, false);
                showAlert('success', response.message);
            } else {
                showAlert('error', 'Failed to update status');
            }
        })
        .fail(function() {
            showAlert('error', 'Error updating status');
        });
    });

    // Bulk actions
    $('#bulk-actions-btn').click(function() {
        const checkedIds = $('.row-checkbox:checked').map(function() {
            return this.value;
        }).get();
        
        if (checkedIds.length === 0) {
            showAlert('warning', 'Please select at least one ticket');
            return;
        }
        
        $('#selected-count').text(checkedIds.length);
        $('#bulkActionsModal').modal('show');
    });

    // Execute bulk action
    $('#execute-bulk-action').click(function() {
        const action = $('#bulk-action').val();
        const checkedIds = $('.row-checkbox:checked').map(function() {
            return this.value;
        }).get();
        
        if (!action) {
            showAlert('warning', 'Please select an action');
            return;
        }
        
        if (action === 'delete' && !confirm('Are you sure you want to delete the selected tickets? This action cannot be undone.')) {
            return;
        }
        
        $.post('{{ route("admin.support.bulk-action") }}', {
            action: action,
            ticket_ids: checkedIds,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                $('#bulkActionsModal').modal('hide');
                $('#bulk-action-form')[0].reset();
                $('#select-all').prop('checked', false);
                table.ajax.reload();
                showAlert('success', response.message);
            } else {
                showAlert('error', 'Failed to execute bulk action');
            }
        })
        .fail(function() {
            showAlert('error', 'Error executing bulk action');
        });
    });

    // Export tickets
    $('#export-btn').click(function() {
        const params = new URLSearchParams({
            status: $('#status-filter').val(),
            priority: $('#priority-filter').val(),
            date_from: $('#date-from').val(),
            date_to: $('#date-to').val()
        });
        
        window.location.href = `{{ route('admin.support.export') }}?${params.toString()}`;
    });

    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-danger';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.container-fluid').prepend(alert);
        
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
});
</script>
@endpush
</x-layout>
