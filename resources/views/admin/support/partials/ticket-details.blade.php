<div class="ticket-details">
    <!-- Ticket Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h5 class="mb-1">{{ $ticket->subject }}</h5>
            <div class="text-muted">
                <small>
                    <i class="fas fa-user me-1"></i>{{ $ticket->sender ? $ticket->sender->username : 'Unknown User' }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-calendar me-1"></i>{{ $ticket->created_at->format('M d, Y h:i A') }}
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'pending' ? 'warning' : 'secondary') }} me-2">
                {{ ucfirst($ticket->status) }}
            </span>
            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                {{ ucfirst($ticket->priority) }} Priority
            </span>
            @if($ticket->is_starred)
                <i class="fas fa-star text-warning ms-2"></i>
            @endif
        </div>
    </div>

    <!-- Original Message -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <strong>Original Message</strong>
                <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="message-content">
                {!! nl2br(e($ticket->message)) !!}
            </div>
            
            @if($ticket->attachments)
                @php
                    $attachments = is_string($ticket->attachments) ? json_decode($ticket->attachments, true) : $ticket->attachments;
                @endphp
                <div class="mt-3">
                    <strong>Attachments:</strong>
                    <div class="mt-2">
                        @foreach($attachments as $attachment)
                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="attachment-item">
                                <i class="fas fa-paperclip me-1"></i>{{ $attachment['name'] }}
                                <small class="text-muted">({{ number_format($attachment['size'] / 1024, 1) }} KB)</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Replies -->
    @php
        $replies = \App\Models\Message::where('reply_to_id', $ticket->id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();
    @endphp

    @if($replies->count() > 0)
        <div class="mb-4">
            <h6 class="mb-3">Conversation ({{ $replies->count() }} {{ Str::plural('reply', $replies->count()) }})</h6>
            <div class="reply-container">
                @foreach($replies as $reply)
                    @php
                        $metadata = $reply->metadata ? json_decode($reply->metadata, true) : [];
                        $isAdminReply = isset($metadata['admin_reply']) && $metadata['admin_reply'];
                    @endphp
                    <div class="card mb-3 {{ $isAdminReply ? 'admin-reply' : 'user-reply' }}">
                        <div class="card-header py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($isAdminReply)
                                        <i class="fas fa-user-shield text-primary me-1"></i>
                                        <strong>{{ $metadata['admin_name'] ?? 'Admin' }}</strong>
                                        <span class="badge bg-primary ms-1">Admin</span>
                                    @else
                                        <i class="fas fa-user text-purple me-1"></i>
                                        <strong>{{ $reply->sender ? $reply->sender->username : 'User' }}</strong>
                                        <span class="badge bg-purple ms-1">User</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $reply->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="message-content">
                                {!! nl2br(e($reply->message)) !!}
                            </div>
                            
                            @if($reply->attachments)
                                @php
                                    $replyAttachments = is_string($reply->attachments) ? json_decode($reply->attachments, true) : $reply->attachments;
                                @endphp
                                <div class="mt-2">
                                    <small><strong>Attachments:</strong></small>
                                    <div class="mt-1">
                                        @foreach($replyAttachments as $attachment)
                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="attachment-item me-2">
                                                <i class="fas fa-paperclip me-1"></i>{{ $attachment['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reply Form -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-reply me-2"></i>Reply to Ticket
            </h6>
        </div>
        <div class="card-body">
            <form id="reply-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                
                <div class="mb-3">
                    <label for="reply-message" class="form-label">Message</label>
                    <textarea class="form-control" id="reply-message" name="message" rows="4" required 
                              placeholder="Type your reply here..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="reply-attachments" class="form-label">Attachments (Optional)</label>
                    <input type="file" class="form-control" id="reply-attachments" name="attachments[]" 
                           multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                    <div class="form-text">
                        Maximum 5 files, 5MB each. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT
                    </div>
                    <div id="attachment-preview" class="mt-2"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reply-status" class="form-label">Update Status</label>
                            <select class="form-select" id="reply-status" name="status">
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending (Waiting for User)</option>
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reply-priority" class="form-label">Update Priority</label>
                            <select class="form-select" id="reply-priority" name="priority">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary" id="send-reply-btn">
                            <i class="fas fa-paper-plane me-2"></i>Send Reply
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-warning toggle-star-btn" data-id="{{ $ticket->id }}">
                            <i class="fas fa-star me-1"></i>
                            {{ $ticket->is_starred ? 'Unstar' : 'Star' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ticket Rating -->
    @php
        $metadata = $ticket->metadata ? json_decode($ticket->metadata, true) : [];
        $rating = isset($metadata['rating']) ? $metadata['rating'] : null;
    @endphp
    @if($rating)
        <div class="alert alert-info mt-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-star text-warning me-2"></i>
                <strong>Customer Rating:</strong>
                <div class="ms-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $rating['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="ms-2">({{ $rating['rating'] }}/5)</span>
                </div>
            </div>
            @if(isset($rating['comment']) && $rating['comment'])
                <div class="mt-2">
                    <strong>Comment:</strong> {{ $rating['comment'] }}
                </div>
            @endif
        </div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Handle file preview
    $('#reply-attachments').change(function() {
        const files = Array.from(this.files);
        let previewHtml = '';
        
        files.forEach((file, index) => {
            previewHtml += `
                <div class="d-inline-block me-2 mb-2">
                    <div class="border rounded p-2 bg-light">
                        <small class="d-block">
                            <i class="fas fa-file me-1"></i>${file.name}
                            <span class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</span>
                        </small>
                    </div>
                </div>
            `;
        });
        
        $('#attachment-preview').html(previewHtml);
    });

    // Handle reply form submission
    $('#reply-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $('#send-reply-btn');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
        
        $.ajax({
            url: '{{ route("admin.support.reply", $ticket->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                // Show success message
                showAlert('success', response.message);
                
                // Reset form
                $('#reply-form')[0].reset();
                $('#attachment-preview').empty();
                
                // Close modal and refresh table
                $('#ticketModal').modal('hide');
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                }
            } else {
                showAlert('error', 'Failed to send reply');
            }
        })
        .fail(function(xhr) {
            let errorMessage = 'Error sending reply';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('error', errorMessage);
        })
        .always(function() {
            // Restore button state
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Handle star toggle
    $('.toggle-star-btn').click(function(e) {
        e.preventDefault();
        const ticketId = $(this).data('id');
        const button = $(this);
        
        const starUrl = '{{ route("admin.support.toggle-star", ":id") }}'.replace(':id', ticketId);
        $.post(starUrl, {
            _token: '{{ csrf_token() }}'
        })
            .done(function(response) {
                if (response.success) {
                    // Update button text and icon
                    const icon = button.find('i');
                    const text = response.starred ? 'Unstar' : 'Star';
                    button.html(`<i class="fas fa-star me-1"></i>${text}`);
                    
                    showAlert('success', response.message);
                } else {
                    showAlert('error', 'Failed to update ticket');
                }
            })
            .fail(function() {
                showAlert('error', 'Error updating ticket');
            });
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        
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
