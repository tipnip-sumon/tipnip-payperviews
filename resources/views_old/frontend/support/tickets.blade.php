<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @push('style')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
        
        <style>
            .filter-card {
                background: #fff;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 25px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            .table-card {
                background: #fff;
                border-radius: 15px;
                padding: 25px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }
            .btn-filter {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 10px;
                padding: 10px 25px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            .btn-filter:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                color: white;
            }
            .form-control {
                border-radius: 10px;
                border: 2px solid #e1e5e9;
                padding: 12px 15px;
                transition: all 0.3s ease;
            }
            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            .page-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px 0;
                margin-bottom: 30px;
                border-radius: 0 0 30px 30px;
            }
            .badge-danger { background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%); }
            .badge-warning { background: linear-gradient(135deg, #ffa726 0%, #ffcc02 100%); }
            .badge-info { background: linear-gradient(135deg, #42a5f5 0%, #64b5f6 100%); }
            .badge-secondary { background: linear-gradient(135deg, #90a4ae 0%, #b0bec5 100%); }
        </style>
    @endpush

    @section('content')
        <div class="page-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-0"><i class="fas fa-ticket-alt me-3"></i>{{ $pageTitle }}</h2>
                        <p class="mb-0 mt-2 opacity-75">Manage and track your support tickets</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('user.support.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>New Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Filters -->
            <div class="filter-card">
                <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filter Tickets</h5>
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="pending">Pending</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Priority</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Subject, message content...">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                            <button type="button" class="btn btn-filter" onclick="applyFilters()">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Support Tickets</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="tickets-table">
                        <thead class="table-light">
                            <tr>
                                <th>S.No</th>
                                <th>Subject</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Replies</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ticket Details Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i>Ticket Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="ticket-details-content">
                        <!-- Details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <div class="modal fade" id="replyModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-reply me-2"></i>Reply to Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="reply-form" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="reply-ticket-id">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <textarea class="form-control" name="message" rows="6" required placeholder="Type your reply..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Attachment (Optional)</label>
                                <input type="file" class="form-control" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                <small class="text-muted">Max file size: 5MB. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- jQuery (load first) -->
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
        
        <script>
            let table;
            
            $(document).ready(function() {
                console.log('Initializing tickets page...');
                initializeDataTable();
                setDateDefaults();
                initializeReplyForm();
            });

            function initializeDataTable() {
                console.log('Initializing DataTable...');
                
                // Check if jQuery and DataTables are loaded
                if (typeof $ === 'undefined') {
                    console.error('jQuery is not loaded!');
                    return;
                }
                
                if (typeof $.fn.DataTable === 'undefined') {
                    console.error('DataTables is not loaded!');
                    return;
                }
                
                table = $('#tickets-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('user.support.tickets') }}",
                        type: 'GET',
                        data: function(d) {
                            d.date_from = $('#date_from').val();
                            d.date_to = $('#date_to').val();
                            d.status = $('#status').val();
                            d.priority = $('#priority').val();
                            d.search = $('#search').val();
                            console.log('AJAX data:', d);
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables AJAX error:', {xhr, error, thrown});
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'subject', name: 'subject'},
                        {data: 'type', name: 'type', orderable: false},
                        {data: 'other_party', name: 'other_party'},
                        {data: 'priority', name: 'priority'},
                        {data: 'status', name: 'status'},
                        {data: 'replies_count', name: 'replies_count', orderable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    order: [[7, 'desc']],
                    pageLength: 25,
                    language: {
                        processing: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br>Loading...</div>',
                        emptyTable: '<div class="text-center"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br>No tickets found</div>',
                        zeroRecords: '<div class="text-center"><i class="fas fa-search fa-3x text-muted mb-3"></i><br>No matching tickets found</div>',
                        loadingRecords: '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
                        info: 'Showing _START_ to _END_ of _TOTAL_ tickets',
                        infoEmpty: 'Showing 0 to 0 of 0 tickets',
                        infoFiltered: '(filtered from _MAX_ total tickets)'
                    },
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                        console.log('DataTable drawn successfully');
                    },
                    initComplete: function(settings, json) {
                        console.log('DataTable initialized successfully', json);
                    }
                });
                
                console.log('DataTable initialization complete');
            }

            function setDateDefaults() {
                const today = new Date();
                const lastMonth = new Date();
                lastMonth.setMonth(today.getMonth() - 1);
                
                $('#date_to').val(today.toISOString().split('T')[0]);
                $('#date_from').val(lastMonth.toISOString().split('T')[0]);
            }

            function applyFilters() {
                table.draw();
            }

            function clearFilters() {
                $('#filter-form')[0].reset();
                setDateDefaults();
                table.draw();
            }

            function refreshTable() {
                table.draw();
            }

            function loadTicketDetails(ticketId) {
                $.get("{{ route('user.support.details', ':id') }}".replace(':id', ticketId))
                    .done(function(data) {
                        $('#ticket-details-content').html(data.html);
                        $('#ticketModal').modal('show');
                    })
                    .fail(function() {
                        alert('Error loading ticket details');
                    });
            }

            function replyToTicket(ticketId) {
                $('#reply-ticket-id').val(ticketId);
                $('#replyModal').modal('show');
            }

            function toggleStar(ticketId) {
                $.post("{{ route('user.support.star', ':id') }}".replace(':id', ticketId), {
                    _token: '{{ csrf_token() }}'
                })
                    .done(function(data) {
                        table.draw();
                        // You can show a toast notification here
                    })
                    .fail(function() {
                        alert('Error updating star status');
                    });
            }

            function closeTicket(ticketId) {
                if (confirm('Are you sure you want to close this ticket?')) {
                    $.post("{{ route('user.support.close', ':id') }}".replace(':id', ticketId), {
                        _token: '{{ csrf_token() }}'
                    })
                        .done(function(data) {
                            table.draw();
                            alert('Ticket closed successfully');
                        })
                        .fail(function() {
                            alert('Error closing ticket');
                        });
                }
            }

            function initializeReplyForm() {
                $('#reply-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    const ticketId = $('#reply-ticket-id').val();
                    const formData = new FormData(this);
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    $.ajax({
                        url: "{{ route('user.support.reply', ':id') }}".replace(':id', ticketId),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#replyModal').modal('hide');
                            $('#reply-form')[0].reset();
                            table.draw();
                            alert('Reply sent successfully!');
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let errorMessage = 'Please fix the following errors:\n';
                                for (const field in errors) {
                                    errorMessage += '- ' + errors[field][0] + '\n';
                                }
                                alert(errorMessage);
                            } else {
                                alert('Error sending reply');
                            }
                        }
                    });
                });
            }

            // Auto-refresh every 2 minutes
            setInterval(function() {
                refreshTable();
            }, 120000);
        </script>
    @endpush

    @include('frontend.support.partials.ticket-details')
</x-smart_layout>
