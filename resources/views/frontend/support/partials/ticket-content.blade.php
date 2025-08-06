<!-- Ticket Header -->
<div class="ticket-header mb-4">
    <div class="row">
        <div class="col-md-8">
            <h5 class="mb-2">
                @if($ticket->is_starred)
                    <i class="fas fa-star text-warning me-2"></i>
                @endif
                {{ $ticket->subject }}
            </h5>
            <div class="ticket-meta">
                <span class="badge bg-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'info') }} me-2">
                    {{ ucfirst($ticket->priority) }} Priority
                </span>
                <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : ($ticket->status === 'closed' ? 'secondary' : 'warning') }} me-2">
                    {{ ucfirst($ticket->status) }}
                </span>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Created {{ $ticket->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="ticket-actions">
                @if($ticket->status !== 'closed')
                    <button class="btn btn-sm btn-success btn-reply me-1" data-ticket-id="{{ $ticket->id }}">
                        <i class="fas fa-reply me-1"></i>Reply
                    </button>
                    <button class="btn btn-sm btn-secondary btn-close-ticket me-1" data-ticket-id="{{ $ticket->id }}">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                @else
                    <button class="btn btn-sm btn-warning btn-reopen-ticket me-1" data-ticket-id="{{ $ticket->id }}">
                        <i class="fas fa-redo me-1"></i>Reopen
                    </button>
                @endif
                
                @php
                    $hasRating = false;
                    if ($ticket->metadata) {
                        $metadata = json_decode($ticket->metadata, true);
                        $hasRating = isset($metadata['rating']);
                    }
                @endphp
                
                @if($ticket->status === 'closed' && !$hasRating)
                    <button class="btn btn-sm btn-warning btn-rate" data-ticket-id="{{ $ticket->id }}">
                        <i class="fas fa-star me-1"></i>Rate
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ticket Conversation -->
<div class="ticket-conversation">
    <!-- Original Message -->
    <div class="message-item {{ $ticket->from_user_id === auth()->id() ? 'user' : 'admin' }}">
        <div class="message-header">
            <div class="message-author">
                <i class="fas fa-user-circle me-2"></i>
                @if($ticket->from_user_id === auth()->id())
                    You
                @else
                    {{ $ticket->sender->firstname ?? 'Support' }} {{ $ticket->sender->lastname ?? 'Team' }}
                @endif
            </div>
            <div class="message-time">
                {{ $ticket->created_at->format('M d, Y - g:i A') }}
            </div>
        </div>
        <div class="message-content">
            {!! nl2br(e($ticket->message)) !!}
        </div>
        
        @if($ticket->attachments)
            @php
                $attachments = json_decode($ticket->attachments, true);
            @endphp
            @if($attachments && count($attachments) > 0)
                <div class="attachment-list">
                    <h6><i class="fas fa-paperclip me-2"></i>Attachments:</h6>
                    @foreach($attachments as $attachment)
                        @php
                            $isImage = in_array(strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                            $icon = $isImage ? 'fas fa-image' : 'fas fa-file';
                        @endphp
                        <a href="{{ Storage::url($attachment['path']) }}" 
                           class="attachment-item {{ $isImage ? 'image-attachment' : 'download-attachment' }}"
                           data-title="{{ $attachment['name'] }}"
                           {{ !$isImage ? 'download' : '' }}>
                            <i class="{{ $icon }} me-2"></i>
                            {{ $attachment['name'] }}
                            <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
    
    <!-- Replies -->
    @foreach($ticket->replies as $reply)
        <div class="message-item {{ $reply->from_user_id === auth()->id() ? 'user' : 'admin' }}">
            <div class="message-header">
                <div class="message-author">
                    <i class="fas fa-user-circle me-2"></i>
                    @if($reply->from_user_id === auth()->id())
                        You
                    @else
                        {{ $reply->sender->firstname ?? 'Support' }} {{ $reply->sender->lastname ?? 'Team' }}
                    @endif
                </div>
                <div class="message-time">
                    {{ $reply->created_at->format('M d, Y - g:i A') }}
                </div>
            </div>
            <div class="message-content">
                {!! nl2br(e($reply->message)) !!}
            </div>
            
            @if($reply->attachments)
                @php
                    $attachments = json_decode($reply->attachments, true);
                @endphp
                @if($attachments && count($attachments) > 0)
                    <div class="attachment-list">
                        <h6><i class="fas fa-paperclip me-2"></i>Attachments:</h6>
                        @foreach($attachments as $attachment)
                            @php
                                $isImage = in_array(strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                                $icon = $isImage ? 'fas fa-image' : 'fas fa-file';
                            @endphp
                            <a href="{{ Storage::url($attachment['path']) }}" 
                               class="attachment-item {{ $isImage ? 'image-attachment' : 'download-attachment' }}"
                               data-title="{{ $attachment['name'] }}"
                               {{ !$isImage ? 'download' : '' }}>
                                <i class="{{ $icon }} me-2"></i>
                                {{ $attachment['name'] }}
                                <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    @endforeach
</div>

<!-- Rating Display -->
@if($ticket->metadata)
    @php
        $metadata = json_decode($ticket->metadata, true);
        $rating = $metadata['rating'] ?? null;
    @endphp
    @if($rating)
        <div class="ticket-rating mt-4 p-3 bg-light rounded">
            <h6><i class="fas fa-star text-warning me-2"></i>Your Rating</h6>
            <div class="d-flex align-items-center mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $rating['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
                <span class="ms-2 fw-bold">{{ $rating['rating'] }}/5</span>
            </div>
            @if($rating['comment'])
                <p class="mb-0 text-muted">{{ $rating['comment'] }}</p>
            @endif
            <small class="text-muted">Rated on {{ \Carbon\Carbon::parse($rating['rated_at'])->format('M d, Y') }}</small>
        </div>
    @endif
@endif
