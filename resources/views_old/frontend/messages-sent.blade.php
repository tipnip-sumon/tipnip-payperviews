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
                            <li class="breadcrumb-item active" aria-current="page">Sent Messages</li>
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
                                    <a href="{{ route('user.messages.inbox') }}" class="btn btn-outline-success ml-2">
                                        <i class="fas fa-inbox mr-1"></i>Inbox
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('user.sponsor-list') }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-edit mr-1"></i>Send New Message
                                    </a>
                                    <button type="button" class="btn btn-success" id="refresh_sent">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Sent Messages Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paper-plane mr-2"></i>{{ $pageTitle }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="sent_table" class="table table-vcenter text-nowrap table-bordered border-bottom">
                                    <thead>
                                        <tr>
                                            <th>S No.</th>
                                            <th>To</th>
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
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewMessageModalLabel">
                            <i class="fas fa-envelope me-2"></i>View Sent Message
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
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}">
        
        <!-- DataTables JS -->
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
        
        <!-- Sweet Alert -->
        <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
        
        <script type="text/javascript">
            var table;
            
            $(document).ready(function() {
                // Initialize DataTable
                table = $('#sent_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('user.messages.sent') }}",
                        type: "GET",
                        error: function(xhr, error, code) {
                            console.log('DataTables Ajax Error:', xhr, error, code);
                            alert('Error loading data: ' + xhr.responseText);
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'receiver', name: 'receiver'},
                        {data: 'subject', name: 'subject'},
                        {data: 'message_preview', name: 'message_preview', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ],
                    order: [[4, 'desc']],
                    pageLength: 25,
                    responsive: true,
                    language: {
                        loadingRecords: "Loading sent messages...",
                        emptyTable: "No sent messages found",
                        zeroRecords: "No sent messages found"
                    }
                });

                // Refresh sent messages
                $('#refresh_sent').click(function() {
                    table.ajax.reload();
                    Swal.fire({
                        title: 'Refreshed!',
                        text: 'Sent messages have been refreshed.',
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
                                                <strong>To:</strong> ${message.receiver.name} (${message.receiver.username})
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
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    This is a message you sent. The recipient ${message.is_read ? 'has read' : 'has not yet read'} this message.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                $('#message_content').html(messageHtml);
                            } else {
                                $('#message_content').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function() {
                            $('#message_content').html('<div class="alert alert-danger">Failed to load message.</div>');
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
                                        table.ajax.reload();
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
