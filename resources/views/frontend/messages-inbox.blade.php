<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @section('content')
        <div class="container-fluid my-4">
            <!-- Navigation -->
            <div class="row mb-3">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.messages') }}">Messages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inbox</li>
                        </ol>
                    </nav>
                </div> 
            </div>
            
            <!-- Quick Actions -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('user.messages') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i>Back to Messages
                                    </a>
                                    <a href="{{ route('user.messages.sent') }}" class="btn btn-outline-primary ml-2">
                                        <i class="fas fa-paper-plane mr-1"></i>Sent Messages
                                    </a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" id="refresh_inbox">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Inbox Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-inbox mr-2"></i>{{ $pageTitle }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="inbox_table" class="table table-vcenter text-nowrap table-bordered border-bottom">
                                    <thead>
                                        <tr>
                                            <th>S No.</th>
                                            <th>From</th>
                                            <th>Subject</th>
                                            <th>Preview</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View Message Modal -->
        <div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="viewMessageModalLabel">
                            <i class="fas fa-envelope me-2"></i>View Message
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="message_content">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading message...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success d-none" id="reply_button">
                            <i class="fas fa-reply me-1"></i>Reply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reply Message Modal -->
        <div class="modal fade" id="replyMessageModal" tabindex="-1" aria-labelledby="replyMessageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="replyMessageModalLabel">
                            <i class="fas fa-reply me-2"></i>Reply to Message
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="replyMessageForm">
                        <div class="modal-body">
                            <input type="hidden" id="reply_message_id" name="message_id">
                            <div class="mb-3">
                                <label for="reply_subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="reply_subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="reply_message" class="form-label">Message</label>
                                <textarea class="form-control" id="reply_message" name="message" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i>Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
        
        <!-- DataTables JS -->
        <script src="{{ asset('assets_custom/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
        
        <!-- Sweet Alert -->
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        
        <script type="text/javascript">
            var table;
            
            $(document).ready(function() {
                // Check if DataTable already exists and destroy it
                if ($.fn.DataTable.isDataTable('#inbox_table')) {
                    $('#inbox_table').DataTable().destroy();
                }
                
                // Initialize DataTable
                table = $('#inbox_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.messages.inbox') }}",
                        type: "GET"
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'sender', name: 'sender'},
                        {data: 'subject', name: 'subject'},
                        {data: 'message_preview', name: 'message_preview', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ],
                    order: [[4, 'desc']],
                    pageLength: 25,
                    responsive: true
                });

                // Refresh inbox
                $('#refresh_inbox').click(function() {
                    if (table) {
                        table.ajax.reload(null, false);
                    }
                    Swal.fire({
                        title: 'Refreshed!',
                        text: 'Inbox has been refreshed.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });

                // View message
                $(document).on('click', '.view-message', function() {
                    var messageId = $(this).data('id');
                    
                    $('#message_content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading message...</p></div>');
                    
                    var viewModal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
                    viewModal.show();
                    
                    $.ajax({
                        url: "{{ url('/user/messages') }}/" + messageId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                var message = response.message;
                                
                                var messageHtml = `
                                    <div class="message-details">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>From:</strong> ${message.sender.name} (${message.sender.username})
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Date:</strong> ${message.created_at}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>Subject:</strong> ${message.subject}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="message-body bg-light p-3 rounded">
                                                    <strong>Message:</strong><br>
                                                    ${message.message.replace(/\n/g, '<br>')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                $('#message_content').html(messageHtml);
                                
                                // Show reply button if user can reply
                                if (message.can_reply) {
                                    $('#reply_button').removeClass('d-none').attr('data-id', messageId);
                                } else {
                                    $('#reply_button').addClass('d-none');
                                }
                                
                                // Refresh table to update read status
                                if (table) {
                                    table.ajax.reload(null, false);
                                }
                            } else {
                                $('#message_content').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function() {
                            $('#message_content').html('<div class="alert alert-danger">Failed to load message.</div>');
                        }
                    });
                });

                // Reply button click
                $(document).on('click', '#reply_button', function() {
                    var messageId = $(this).data('id');
                    $('#reply_message_id').val(messageId);
                    $('#reply_subject').val('Re: ');
                    $('#reply_message').val('');
                    
                    // Hide view modal and show reply modal
                    var viewModal = bootstrap.Modal.getInstance(document.getElementById('viewMessageModal'));
                    viewModal.hide();
                    
                    var replyModal = new bootstrap.Modal(document.getElementById('replyMessageModal'));
                    replyModal.show();
                });

                // Reply message
                $(document).on('click', '.reply-message', function() {
                    var messageId = $(this).data('id');
                    $('#reply_message_id').val(messageId);
                    $('#reply_subject').val('Re: ');
                    $('#reply_message').val('');
                    
                    var replyModal = new bootstrap.Modal(document.getElementById('replyMessageModal'));
                    replyModal.show();
                });

                // Submit reply form
                $('#replyMessageForm').submit(function(e) {
                    e.preventDefault();
                    
                    var messageId = $('#reply_message_id').val();
                    var formData = $(this).serialize();
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();
                    
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                    
                    $.ajax({
                        url: "{{ url('/user/messages') }}/" + messageId + "/reply",
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var replyModal = bootstrap.Modal.getInstance(document.getElementById('replyMessageModal'));
                            replyModal.hide();
                            
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success'
                                });
                                if (table) {
                                    table.ajax.reload();
                                }
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to send reply. Please try again.',
                                icon: 'error'
                            });
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // Delete message
                $(document).on('click', '.delete-message', function() {
                    var messageId = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this message!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('/user/messages') }}/" + messageId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire('Deleted!', response.message, 'success');
                                        if (table) {
                                            table.ajax.reload();
                                        }
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Failed to delete message.', 'error');
                                }
                            });
                        }
                    });
                });
                
                // Cleanup DataTable on page unload
                $(window).on('beforeunload', function() {
                    if (table && $.fn.DataTable.isDataTable('#inbox_table')) {
                        table.destroy();
                    }
                });
            });
        </script>
        
        <style>
            .message-body {
                max-height: 300px;
                overflow-y: auto;
            }
            .badge {
                font-size: 0.875em;
            }
        </style>
    @endpush
</x-smart_layout>
