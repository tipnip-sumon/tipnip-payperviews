<!-- Ticket Details Modal -->
<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ticketDetailsModalLabel">
                    <i class="fas fa-ticket-alt me-2"></i>{{ __('Ticket Details') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticketDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                    <p class="mt-2">{{ __('Loading ticket details...') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="replyModalLabel">
                    <i class="fas fa-reply me-2"></i>{{ __('Reply to Ticket') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm" enctype="multipart/form-data">
                    <input type="hidden" id="replyTicketId" name="ticket_id">
                    
                    <div class="form-group mb-3">
                        <label for="replyMessage" class="form-label">{{ __('Your Reply') }} <span class="text-danger">*</span></label>
                        <textarea name="message" id="replyMessage" class="form-control" rows="6" 
                                  placeholder="{{ __('Type your reply here...') }}" required></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="replyAttachments" class="form-label">{{ __('Attachments') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <input type="file" name="attachments[]" id="replyAttachments" class="form-control" 
                               multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
                        <div class="form-text">
                            {{ __('Supported formats: JPG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP. Max size: 10MB per file.') }}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                </button>
                <button type="button" class="btn btn-success" id="submitReply">
                    <i class="fas fa-paper-plane me-2"></i>{{ __('Send Reply') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="ratingModalLabel">
                    <i class="fas fa-star me-2"></i>{{ __('Rate Support') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ratingForm">
                    <input type="hidden" id="ratingTicketId" name="ticket_id">
                    
                    <div class="text-center mb-4">
                        <p class="mb-3">{{ __('How would you rate our support?') }}</p>
                        <div class="star-rating">
                            <i class="fas fa-star star" data-rating="1"></i>
                            <i class="fas fa-star star" data-rating="2"></i>
                            <i class="fas fa-star star" data-rating="3"></i>
                            <i class="fas fa-star star" data-rating="4"></i>
                            <i class="fas fa-star star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="selectedRating" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="ratingComment" class="form-label">{{ __('Additional Comments') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                        <textarea name="comment" id="ratingComment" class="form-control" rows="3" 
                                  placeholder="{{ __('Tell us about your experience...') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                </button>
                <button type="button" class="btn btn-warning" id="submitRating">
                    <i class="fas fa-star me-2"></i>{{ __('Submit Rating') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load ticket details
    window.loadTicketDetails = function(ticketId) {
        $('#ticketDetailsModal').modal('show');
        
        $.ajax({
            url: "{{ route('user.support.details', ':id') }}".replace(':id', ticketId),
            method: 'GET',
            success: function(response) {
                $('#ticketDetailsContent').html(response.html);
                attachTicketEventHandlers();
            },
            error: function() {
                $('#ticketDetailsContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('Failed to load ticket details. Please try again.') }}
                    </div>
                `);
            }
        });
    };
    
    // Reply to ticket
    window.replyToTicket = function(ticketId) {
        $('#replyTicketId').val(ticketId);
        $('#replyMessage').val('');
        $('#replyAttachments').val('');
        $('#replyModal').modal('show');
    };
    
    // Submit reply
    $('#submitReply').on('click', function() {
        const formData = new FormData();
        const ticketId = $('#replyTicketId').val();
        const message = $('#replyMessage').val().trim();
        
        if (!message) {
            toastr.error('{{ __("Please enter your reply message") }}');
            return;
        }
        
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('message', message);
        
        // Add attachments
        const files = $('#replyAttachments')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('attachments[]', files[i]);
        }
        
        // Show loading
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Sending...") }}').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('user.support.reply', ':id') }}".replace(':id', ticketId),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message || '{{ __("Reply sent successfully") }}');
                $('#replyModal').modal('hide');
                
                // Refresh ticket details if modal is open
                if ($('#ticketDetailsModal').hasClass('show')) {
                    loadTicketDetails(ticketId);
                }
                
                // Refresh tickets table
                if (typeof ticketsTable !== 'undefined') {
                    ticketsTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || '{{ __("Failed to send reply") }}');
            },
            complete: function() {
                $('#submitReply').html('<i class="fas fa-paper-plane me-2"></i>{{ __("Send Reply") }}').prop('disabled', false);
            }
        });
    });
    
    // Rate support
    window.rateSupport = function(ticketId) {
        $('#ratingTicketId').val(ticketId);
        $('#selectedRating').val('');
        $('#ratingComment').val('');
        $('.star').removeClass('active');
        $('#ratingModal').modal('show');
    };
    
    // Star rating handler
    $(document).on('click', '.star', function() {
        const rating = $(this).data('rating');
        $('#selectedRating').val(rating);
        
        $('.star').removeClass('active');
        for (let i = 1; i <= rating; i++) {
            $(`.star[data-rating="${i}"]`).addClass('active');
        }
    });
    
    // Submit rating
    $('#submitRating').on('click', function() {
        const ticketId = $('#ratingTicketId').val();
        const rating = $('#selectedRating').val();
        const comment = $('#ratingComment').val();
        
        if (!rating) {
            toastr.error('{{ __("Please select a rating") }}');
            return;
        }
        
        // Show loading
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Submitting...") }}').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('user.support.rate', ':id') }}".replace(':id', ticketId),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                rating: rating,
                comment: comment
            },
            success: function(response) {
                toastr.success(response.message || '{{ __("Thank you for your feedback") }}');
                $('#ratingModal').modal('hide');
                
                // Refresh ticket details if modal is open
                if ($('#ticketDetailsModal').hasClass('show')) {
                    loadTicketDetails(ticketId);
                }
                
                // Refresh tickets table
                if (typeof ticketsTable !== 'undefined') {
                    ticketsTable.ajax.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || '{{ __("Failed to submit rating") }}');
            },
            complete: function() {
                $('#submitRating').html('<i class="fas fa-star me-2"></i>{{ __("Submit Rating") }}').prop('disabled', false);
            }
        });
    });
    
    // Close ticket
    window.closeTicket = function(ticketId) {
        if (confirm('{{ __("Are you sure you want to close this ticket?") }}')) {
            $.ajax({
                url: "{{ route('user.support.close', ':id') }}".replace(':id', ticketId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message || '{{ __("Ticket closed successfully") }}');
                    
                    // Refresh ticket details if modal is open
                    if ($('#ticketDetailsModal').hasClass('show')) {
                        loadTicketDetails(ticketId);
                    }
                    
                    // Refresh tickets table
                    if (typeof ticketsTable !== 'undefined') {
                        ticketsTable.ajax.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || '{{ __("Failed to close ticket") }}');
                }
            });
        }
    };
    
    // Reopen ticket
    window.reopenTicket = function(ticketId) {
        if (confirm('{{ __("Are you sure you want to reopen this ticket?") }}')) {
            $.ajax({
                url: "{{ route('user.support.reopen', ':id') }}".replace(':id', ticketId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message || '{{ __("Ticket reopened successfully") }}');
                    
                    // Refresh ticket details if modal is open
                    if ($('#ticketDetailsModal').hasClass('show')) {
                        loadTicketDetails(ticketId);
                    }
                    
                    // Refresh tickets table
                    if (typeof ticketsTable !== 'undefined') {
                        ticketsTable.ajax.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || '{{ __("Failed to reopen ticket") }}');
                }
            });
        }
    };
    
    // Attach event handlers for ticket details content
    function attachTicketEventHandlers() {
        // Reply button handler
        $(document).off('click', '.btn-reply').on('click', '.btn-reply', function() {
            const ticketId = $(this).data('ticket-id');
            replyToTicket(ticketId);
        });
        
        // Close button handler
        $(document).off('click', '.btn-close-ticket').on('click', '.btn-close-ticket', function() {
            const ticketId = $(this).data('ticket-id');
            closeTicket(ticketId);
        });
        
        // Reopen button handler
        $(document).off('click', '.btn-reopen-ticket').on('click', '.btn-reopen-ticket', function() {
            const ticketId = $(this).data('ticket-id');
            reopenTicket(ticketId);
        });
        
        // Rate button handler
        $(document).off('click', '.btn-rate').on('click', '.btn-rate', function() {
            const ticketId = $(this).data('ticket-id');
            rateSupport(ticketId);
        });
        
        // Download attachment handler
        $(document).off('click', '.download-attachment').on('click', '.download-attachment', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            window.open(url, '_blank');
        });
        
        // Image lightbox handler
        $(document).off('click', '.image-attachment').on('click', '.image-attachment', function(e) {
            e.preventDefault();
            const src = $(this).attr('href');
            const title = $(this).data('title') || 'Attachment';
            
            // Simple lightbox
            const lightbox = $(`
                <div class="lightbox-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div class="lightbox-content" style="max-width: 90%; max-height: 90%; position: relative;">
                        <img src="${src}" alt="${title}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <button class="lightbox-close" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 24px; cursor: pointer;">&times;</button>
                    </div>
                </div>
            `);
            
            $('body').append(lightbox);
            
            lightbox.on('click', function(e) {
                if (e.target === this || $(e.target).hasClass('lightbox-close')) {
                    $(this).remove();
                }
            });
        });
    }
});
</script>

<style>
.star-rating .star {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.3s ease;
    margin: 0 0.25rem;
}

.star-rating .star:hover,
.star-rating .star.active {
    color: #ffc107;
}

.star-rating .star:hover ~ .star {
    color: #ddd;
}

.modal-xl {
    max-width: 95%;
}

.ticket-conversation {
    max-height: 500px;
    overflow-y: auto;
}

.message-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 4px solid #e3e6f0;
}

.message-item.admin {
    background-color: #f8f9fa;
    border-left-color: #28a745;
}

.message-item.user {
    background-color: #e3f2fd;
    border-left-color: #007bff;
}

.message-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.message-author {
    font-weight: 600;
    color: #495057;
}

.message-time {
    font-size: 0.875rem;
    color: #6c757d;
}

.message-content {
    color: #495057;
    line-height: 1.6;
}

.attachment-list {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e3e6f0;
}

.attachment-item {
    display: inline-block;
    margin: 0.25rem;
    padding: 0.5rem 1rem;
    background-color: #f8f9fa;
    border: 1px solid #e3e6f0;
    border-radius: 0.25rem;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.attachment-item:hover {
    background-color: #e9ecef;
    color: #007bff;
    text-decoration: none;
}

.lightbox-overlay {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 0.5rem;
    }
    
    .star-rating .star {
        font-size: 1.5rem;
        margin: 0 0.1rem;
    }
    
    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
