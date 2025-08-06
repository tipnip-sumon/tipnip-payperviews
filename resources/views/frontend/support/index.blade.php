<x-smart_layout>
    @section('top_title', $pageTitle)
    @section('title', $pageTitle)
    
    @push('style')
        <style>
            .support-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 15px;
                padding: 30px;
                color: white;
                margin-bottom: 25px;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
                transition: transform 0.3s ease;
            }
            .support-card:hover {
                transform: translateY(-5px);
            }
            .feature-card {
                background: #fff;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 20px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                text-align: center;
                border: 1px solid #f0f0f0;
            }
            .feature-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            }
            .feature-icon {
                font-size: 3rem;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .recent-message {
                background: #fff;
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 10px;
                border-left: 4px solid #667eea;
                transition: all 0.3s ease;
            }
            .recent-message:hover {
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
            .unread-badge {
                background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
                color: white;
                padding: 5px 10px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .btn-support {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 10px;
                padding: 12px 25px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }
            .btn-support:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                color: white;
                text-decoration: none;
            }
        </style>
    @endpush

    @section('content')
        <div class="container">
            <!-- Welcome Section -->
            <div class="support-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-3"><i class="fas fa-headset me-3"></i>Support Center</h2>
                        <p class="mb-4 opacity-90">Welcome to our support center! We're here to help you with any questions or issues you might have. Choose from the options below to get the assistance you need.</p>
                        @if($unreadCount > 0)
                            <div class="unread-badge">
                                <i class="fas fa-envelope me-2"></i>{{ $unreadCount }} unread message{{ $unreadCount > 1 ? 's' : '' }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-support fa-6x opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h5 class="mb-3">Create Ticket</h5>
                        <p class="text-muted mb-4">Submit a new support ticket for personalized assistance</p>
                        <a href="{{ route('user.support.create') }}" class="btn-support">
                            <i class="fas fa-plus me-2"></i>New Ticket
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <h5 class="mb-3">My Tickets</h5>
                        <p class="text-muted mb-4">View and manage your existing support tickets</p>
                        <a href="{{ route('user.support.tickets') }}" class="btn-support">
                            <i class="fas fa-eye me-2"></i>View Tickets
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="mb-3">Knowledge Base</h5>
                        <p class="text-muted mb-4">Find answers to frequently asked questions</p>
                        <a href="{{ route('user.support.knowledge') }}" class="btn-support">
                            <i class="fas fa-search me-2"></i>Browse FAQ
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="mb-3">Contact Us</h5>
                        <p class="text-muted mb-4">Get in touch with our support team directly</p>
                        <a href="{{ route('user.support.contact') }}" class="btn-support">
                            <i class="fas fa-paper-plane me-2"></i>Contact
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            @if($recentMessages->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 py-3">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Messages</h5>
                            </div>
                            <div class="card-body">
                                @foreach($recentMessages as $message)
                                    <div class="recent-message">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    @if(!$message->is_read && $message->to_user_id == auth()->id())
                                                        <span class="badge bg-primary me-2">New</span>
                                                    @endif
                                                    @if($message->is_starred)
                                                        <i class="fas fa-star text-warning me-2"></i>
                                                    @endif
                                                    <span class="badge {{ $message->priority_badge }} me-2">{{ $message->priority_text }}</span>
                                                    <h6 class="mb-0">{{ $message->subject }}</h6>
                                                </div>
                                                <p class="text-muted small mb-2">{{ $message->preview }}</p>
                                                <div class="d-flex align-items-center text-muted small">
                                                    <span class="me-3">
                                                        <i class="fas fa-user me-1"></i>
                                                        {{ $message->from_user_id == auth()->id() ? 'To: ' . $message->recipient->firstname : 'From: ' . $message->sender->firstname }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-clock me-1"></i>{{ $message->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <button class="btn btn-sm btn-outline-primary" onclick="loadTicketDetails('{{ $message->id }}')">>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div class="text-center mt-3">
                                    <a href="{{ route('user.support.tickets') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-list me-2"></i>View All Tickets
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Help Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <h4 class="mb-3">Need Help Getting Started?</h4>
                            <p class="text-muted mb-4">Check out our knowledge base for common questions and step-by-step guides.</p>
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-deposit-money fa-2x text-primary mb-2"></i>
                                                <h6>Deposits & Plans</h6>
                                                <small class="text-muted">Learn about making deposits and choosing plans</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-video fa-2x text-success mb-2"></i>
                                                <h6>Video Viewing</h6>
                                                <small class="text-muted">How to watch videos and earn rewards</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="p-3">
                                                <i class="fas fa-money-withdrawal fa-2x text-warning mb-2"></i>
                                                <h6>Withdrawals</h6>
                                                <small class="text-muted">Understanding withdrawal methods and fees</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Details Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
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
    @endsection

    @push('script')
        <script>
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

            // Auto-refresh unread count every 2 minutes
            setInterval(function() {
                // You can add AJAX call here to update unread count
            }, 120000);
        </script>
    @endpush
</x-smart_layout>
