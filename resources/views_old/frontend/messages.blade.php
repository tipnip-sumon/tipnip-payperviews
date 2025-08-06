<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @section('content')
        <div class="container-fluid my-4">
            <!-- Statistics Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-1 fw-bold">{{ $stats['total_messages'] }}</h3>
                                    <p class="mb-0">Total Messages</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fe fe-mail fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-1 fw-bold">{{ $stats['unread_messages'] }}</h3>
                                    <p class="mb-0">Unread Messages</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fe fe-mail-open fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-1 fw-bold">{{ $stats['received_messages'] }}</h3>
                                    <p class="mb-0">Received</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fe fe-inbox fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-1 fw-bold">{{ $stats['sent_messages'] }}</h3>
                                    <p class="mb-0">Sent</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fe fe-send fs-1 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Row -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fe fe-zap me-2"></i>Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                    <a href="{{ route('user.messages.inbox') }}" class="btn btn-success w-100 btn-lg">
                                        <i class="fe fe-inbox me-2"></i>View Inbox
                                        @if($stats['unread_messages'] > 0)
                                            <span class="badge bg-light text-dark ms-2">{{ $stats['unread_messages'] }}</span>
                                        @endif
                                    </a>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                    <a href="{{ route('user.messages.sent') }}" class="btn btn-primary w-100 btn-lg">
                                        <i class="fe fe-send me-2"></i>Sent Messages
                                    </a>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                    <a href="{{ route('user.sponsor-list') }}" class="btn btn-info w-100 btn-lg">
                                        <i class="fe fe-users me-2"></i>Contact Sponsors
                                    </a>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                    <button class="btn btn-warning w-100 btn-lg" id="compose_message">
                                        <i class="fe fe-edit me-2"></i>Compose Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Messages -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fe fe-clock me-2"></i>Recent Messages
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                    <i class="fe fe-refresh-cw"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($recentMessages->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter text-nowrap table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Type</th>
                                                <th>From/To</th>
                                                <th>Subject</th>
                                                <th class="d-none d-md-table-cell">Message</th>
                                                <th class="d-none d-lg-table-cell">Date</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentMessages as $message)
                                                <tr class="{{ !$message->is_read && $message->to_user_id == auth()->id() ? 'table-warning' : '' }}">
                                                    <td class="text-center">
                                                        @if($message->from_user_id == auth()->id())
                                                            <span class="badge bg-primary"><i class="fe fe-send"></i> <span class="d-none d-sm-inline">Sent</span></span>
                                                        @else
                                                            <span class="badge bg-success"><i class="fe fe-inbox"></i> <span class="d-none d-sm-inline">Received</span></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            @if($message->from_user_id == auth()->id())
                                                                <strong class="text-primary">To:</strong>
                                                                <small>{{ $message->recipient ? (trim($message->recipient->firstname . ' ' . $message->recipient->lastname) ?: $message->recipient->username) : 'Unknown' }}</small>
                                                            @else
                                                                <strong class="text-success">From:</strong>
                                                                <small>{{ $message->sender ? (trim($message->sender->firstname . ' ' . $message->sender->lastname) ?: $message->sender->username) : 'Unknown' }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <strong>{{ $message->subject ?: 'No Subject' }}</strong>
                                                            @if(!$message->is_read && $message->to_user_id == auth()->id())
                                                                <span class="badge bg-warning text-dark mt-1">New</span>
                                                            @endif
                                                            <!-- Show message preview on mobile -->
                                                            <small class="text-muted d-md-none mt-1">{{ Str::limit($message->message, 30) }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <small class="text-muted">{{ Str::limit($message->message, 50) }}</small>
                                                    </td>
                                                    <td class="d-none d-lg-table-cell">
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold">{{ $message->created_at->format('M d, Y') }}</span>
                                                            <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($message->to_user_id == auth()->id())
                                                            @if($message->is_read)
                                                                <span class="badge bg-success-transparent text-success">Read</span>
                                                            @else
                                                                <span class="badge bg-warning-transparent text-warning">Unread</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-info-transparent text-info">Sent</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-primary view-message" data-id="{{ $message->id }}" title="View Message">
                                                                <i class="fe fe-eye"></i>
                                                            </button>
                                                            @if($message->to_user_id == auth()->id())
                                                                <button class="btn btn-success reply-message" data-id="{{ $message->id }}" title="Reply">
                                                                    <i class="fe fe-corner-up-left"></i>
                                                                </button>
                                                            @endif
                                                            <button class="btn btn-danger delete-message" data-id="{{ $message->id }}" title="Delete">
                                                                <i class="fe fe-trash-2"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="{{ route('user.messages.inbox') }}" class="btn btn-outline-primary">
                                        <i class="fe fe-inbox me-2"></i>View All Messages
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fe fe-mail fs-1 text-muted mb-3"></i>
                                    <h4 class="text-muted">No Messages Yet</h4>
                                    <p class="text-muted">You haven't sent or received any messages yet.</p>
                                    <a href="{{ route('user.sponsor-list') }}" class="btn btn-primary">
                                        <i class="fe fe-users me-2"></i>Contact Your Sponsors
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View Message Modal -->
        <div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel">
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
        <div class="modal fade" id="replyMessageModal" tabindex="-1" aria-labelledby="replyMessageModalLabel">
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
        <!-- Sweet Alert -->
        <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {
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
                                                <strong>To:</strong> ${message.recipient.name} (${message.recipient.username})
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Subject:</strong> ${message.subject}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Date:</strong> ${message.created_at}
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
                                }).then(() => {
                                    location.reload();
                                });
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
                                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                                            location.reload();
                                        });
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
            /* Modern responsive card design */
            .card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border-radius: 0.5rem;
                transition: all 0.3s ease;
            }
            
            .card:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }
            
            /* Statistics cards */
            .card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
                border: none;
                overflow: hidden;
                position: relative;
            }
            
            .card.bg-primary::before,
            .card.bg-success::before,
            .card.bg-warning::before,
            .card.bg-info::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 100px;
                height: 100px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                transform: translate(30px, -30px);
            }
            
            /* Table responsiveness */
            .table-responsive {
                border-radius: 0.5rem;
                overflow: hidden;
            }
            
            .table {
                margin-bottom: 0;
            }
            
            .table thead th {
                border-top: none;
                font-weight: 600;
                background: rgba(var(--bs-primary-rgb), 0.1);
                color: var(--bs-primary);
            }
            
            .table tbody tr:hover {
                background-color: rgba(var(--bs-primary-rgb), 0.05);
            }
            
            /* Badge styles */
            .badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
            
            .bg-success-transparent {
                background-color: rgba(var(--bs-success-rgb), 0.1) !important;
            }
            
            .bg-warning-transparent {
                background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
            }
            
            .bg-info-transparent {
                background-color: rgba(var(--bs-info-rgb), 0.1) !important;
            }
            
            /* Button improvements */
            .btn {
                border-radius: 0.375rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
            }
            
            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
            }
            
            /* Mobile optimizations */
            @media (max-width: 767.98px) {
                .container-fluid {
                    padding: 1rem;
                }
                
                .card-body {
                    padding: 1rem;
                }
                
                .btn-lg {
                    padding: 0.75rem 1rem;
                    font-size: 0.9rem;
                }
                
                .table {
                    font-size: 0.875rem;
                }
                
                .btn-group-sm .btn {
                    padding: 0.375rem 0.5rem;
                    margin: 0 1px;
                }
                
                .badge {
                    font-size: 0.7rem;
                    padding: 0.25rem 0.4rem;
                }
                
                /* Stack cards vertically on very small screens */
                .row.g-3 > .col-xl-3 {
                    margin-bottom: 1rem;
                }
            }
            
            /* Small mobile devices */
            @media (max-width: 575.98px) {
                .btn-group {
                    flex-direction: column;
                    width: 100%;
                }
                
                .btn-group .btn {
                    border-radius: 0.375rem !important;
                    margin-bottom: 0.25rem;
                }
                
                .table-responsive {
                    font-size: 0.8rem;
                }
                
                .card-title {
                    font-size: 1rem;
                }
                
                h3 {
                    font-size: 1.5rem;
                }
            }
            
            /* Message warning highlighting */
            .table-warning {
                background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
                border-left: 3px solid var(--bs-warning);
            }
            
            /* Modal responsiveness */
            @media (max-width: 575.98px) {
                .modal-dialog {
                    margin: 0.5rem;
                }
                
                .modal-lg {
                    max-width: calc(100vw - 1rem);
                }
            }
            
            /* Loading spinner */
            .spinner-border {
                width: 2rem;
                height: 2rem;
            }
            
            /* Empty state styling */
            .text-center.py-5 {
                padding: 3rem 1rem !important;
            }
            
            /* Card tools alignment */
            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .card-tools {
                margin-left: auto;
            }
            
            @media (max-width: 575.98px) {
                .card-header {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .card-tools {
                    margin-left: 0;
                    text-align: center;
                }
            }
        </style>
    @endpush
</x-smart_layout>
