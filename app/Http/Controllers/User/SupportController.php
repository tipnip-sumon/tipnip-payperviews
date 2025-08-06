<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SupportController extends Controller
{
    public function index()
    {
        $pageTitle = "Support Center";
        $user = Auth::user();
        
        // Get unread messages count
        $unreadCount = Message::where('to_user_id', $user->id)->unread()->count();
        
        // Get recent messages
        $recentMessages = Message::where(function($query) use ($user) {
            $query->where('from_user_id', $user->id)
                  ->orWhere('to_user_id', $user->id);
        })
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
        
        return view('frontend.support.index', compact('pageTitle', 'unreadCount', 'recentMessages'));
    }
    
    public function tickets(Request $request)
    {
        $pageTitle = "Support Tickets";
        $user = Auth::user();
        
        if ($request->ajax()) {
            $query = Message::where(function($q) use ($user) {
                $q->where('from_user_id', $user->id)
                  ->orWhere('to_user_id', $user->id);
            })
            ->whereNull('reply_to_id') // Only parent messages, not replies
            ->with(['sender', 'recipient', 'replies']);
            
            // Apply filters
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->priority) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('subject', 'like', '%' . $request->search . '%')
                      ->orWhere('message', 'like', '%' . $request->search . '%');
                });
            }
            
            return DataTables::of($query)
                ->addColumn('subject', function ($row) {
                    $readClass = $row->is_read ? '' : 'fw-bold';
                    $starIcon = $row->is_starred ? '<i class="fas fa-star text-warning me-1"></i>' : '';
                    return '<div class="' . $readClass . '">' . $starIcon . $row->subject . '</div>';
                })
                ->addColumn('type', function ($row) use ($user) {
                    if ($row->from_user_id == $user->id) {
                        return '<span class="badge bg-primary">Sent</span>';
                    } else {
                        return '<span class="badge bg-success">Received</span>';
                    }
                })
                ->addColumn('other_party', function ($row) use ($user) {
                    if ($row->from_user_id == $user->id) {
                        return $row->recipient->firstname . ' ' . $row->recipient->lastname;
                    } else {
                        return $row->sender->firstname . ' ' . $row->sender->lastname;
                    }
                })
                ->addColumn('priority', function ($row) {
                    $class = $row->priority_badge;
                    return '<span class="badge ' . $class . '">' . $row->priority_text . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        'open' => '<span class="badge bg-success">Open</span>',
                        'pending' => '<span class="badge bg-warning">Pending</span>',
                        'closed' => '<span class="badge bg-secondary">Closed</span>',
                        'resolved' => '<span class="badge bg-info">Resolved</span>'
                    ];
                    return $statusMap[$row->status] ?? '<span class="badge bg-primary">Open</span>';
                })
                ->addColumn('replies_count', function ($row) {
                    $count = $row->replies->count();
                    return $count > 0 ? '<span class="badge bg-info">' . $count . '</span>' : '0';
                })
                ->addColumn('created_at', function ($row) {
                    return '<span data-bs-toggle="tooltip" title="' . $row->created_at . '">' . 
                           $row->created_at->diffForHumans() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="loadTicketDetails(' . $row->id . ')" data-bs-toggle="tooltip" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="replyToTicket(' . $row->id . ')" data-bs-toggle="tooltip" title="Reply">
                                <i class="fas fa-reply"></i>
                            </button>
                            <button class="btn btn-outline-info" onclick="toggleStar(' . $row->id . ')" data-bs-toggle="tooltip" title="Star">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>';
                })
                ->addIndexColumn()
                ->rawColumns(['subject', 'type', 'priority', 'status', 'replies_count', 'created_at', 'action'])
                ->make(true);
        }
        
        return view('frontend.support.tickets', compact('pageTitle'));
    }
    
    public function createTicket()
    {
        $pageTitle = "Create Support Ticket";
        
        // Get available admins using helper method
        $admins = $this->getAvailableAdmins();
        
        return view('frontend.support.create', compact('pageTitle', 'admins'));
    }
    
    public function storeTicket(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|in:general,technical,billing,account,feature,bug',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'priority' => 'required|in:low,normal,high,urgent',
                'to_user_id' => 'nullable|exists:users,id',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip'
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            // Handle multiple attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    try {
                        $path = $file->store('support_attachments', 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType()
                        ];
                    } catch (\Exception $e) {
                        return back()->withErrors(['attachments' => 'Failed to upload attachment: ' . $file->getClientOriginalName()])->withInput();
                    }
                }
            }
            
            // If no specific recipient, send to best available admin
            $toUserId = $request->to_user_id;
            if (!$toUserId) {
                $adminUser = $this->getBestAdminForTicket();
                
                if (!$adminUser) {
                    return back()->withErrors(['general' => 'No admin user found to send ticket to. Please contact support directly.'])->withInput();
                }
                
                $toUserId = $adminUser->id;
            } else {
                // Validate that the selected recipient is actually an admin
                $selectedUser = User::find($toUserId);
                if (!$selectedUser || !$this->isAdmin($selectedUser)) {
                    // If selected user is not admin, redirect to an admin instead
                    $adminUser = $this->getBestAdminForTicket();
                    
                    if ($adminUser) {
                        $toUserId = $adminUser->id;
                    } else {
                        return back()->withErrors(['general' => 'Selected recipient is not an admin. Please select a valid admin.'])->withInput();
                    }
                }
            }
            
            // Validate that regular users can only send tickets to admins
            $currentUser = Auth::user();
            if (!$this->isAdmin($currentUser)) {
                // Regular user - ensure ticket goes to admin
                $recipientUser = User::find($toUserId);
                if (!$recipientUser || !$this->isAdmin($recipientUser)) {
                    return back()->withErrors(['general' => 'Support tickets can only be sent to admin users.'])->withInput();
                }
            }
            
            // Validate that the current user exists and is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')->withErrors(['general' => 'You must be logged in to create a support ticket.']);
            }
            
            $messageData = [
                'from_user_id' => Auth::id(),
                'to_user_id' => $toUserId,
                'subject' => $request->subject,
                'message' => $request->message,
                'category' => $request->category,
                'priority' => $request->priority,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
                'message_type' => 'support',
                'status' => 'open',
                'is_read' => false,
                'is_starred' => false
            ];
            
            $message = Message::create($messageData);
            
            if ($message) {
                // Log the ticket creation for admin notification
                Log::info('Support ticket created', [
                    'ticket_id' => $message->id,
                    'from_user' => Auth::user()->username,
                    'to_admin' => User::find($toUserId)->username ?? 'Unknown',
                    'subject' => $request->subject,
                    'priority' => $request->priority
                ]);
                
                return redirect()->route('user.support.tickets')->with('success', 'Support ticket created successfully and sent to admin!');
            } else {
                return back()->withErrors(['general' => 'Failed to create support ticket. Please try again.'])->withInput();
            }
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Support ticket creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['attachments']),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['general' => 'An error occurred while creating your support ticket. Please try again or contact support directly.'])->withInput();
        }
    }
    
    public function ticketDetails($id)
    {
        $ticket = Message::with(['sender', 'recipient', 'replies.sender', 'replies.recipient'])
                         ->where(function($query) {
                             $query->where('from_user_id', Auth::id())
                                   ->orWhere('to_user_id', Auth::id());
                         })
                         ->findOrFail($id);
        
        // Mark as read if user is recipient
        if ($ticket->to_user_id == Auth::id() && !$ticket->is_read) {
            $ticket->markAsRead();
        }
        
        $html = view('frontend.support.partials.ticket-content', compact('ticket'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'ticket' => $ticket
        ]);
    }
    
    public function replyToTicket(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $originalTicket = Message::where(function($query) {
                                    $query->where('from_user_id', Auth::id())
                                          ->orWhere('to_user_id', Auth::id());
                                 })
                                 ->findOrFail($id);
        
        // Handle multiple attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support_attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        // Determine recipient (opposite of current user)
        $toUserId = $originalTicket->from_user_id == Auth::id() 
                   ? $originalTicket->to_user_id 
                   : $originalTicket->from_user_id;
        
        $reply = Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $toUserId,
            'subject' => 'Re: ' . $originalTicket->subject,
            'message' => $request->message,
            'priority' => $originalTicket->priority,
            'reply_to_id' => $originalTicket->id,
            'attachments' => !empty($attachments) ? json_encode($attachments) : null,
            'message_type' => 'support',
            'status' => 'open'
        ]);
        
        // Update original ticket status to open if it was closed
        if ($originalTicket->status == 'closed') {
            $originalTicket->update(['status' => 'open']);
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Reply sent successfully!'
        ]);
    }
    
    public function toggleStar($id)
    {
        $message = Message::where(function($query) {
                             $query->where('from_user_id', Auth::id())
                                   ->orWhere('to_user_id', Auth::id());
                         })
                         ->findOrFail($id);
        
        $message->toggleStar();
        
        return response()->json(['success' => true, 'starred' => $message->is_starred]);
    }
    
    public function closeTicketAction($id)
    {
        $ticket = Message::where(function($query) {
                            $query->where('from_user_id', Auth::id())
                                  ->orWhere('to_user_id', Auth::id());
                         })
                         ->findOrFail($id);
        
        $ticket->update(['status' => 'closed']);
        
        return response()->json([
            'success' => true, 
            'message' => 'Ticket closed successfully!'
        ]);
    }
    
    public function reopenTicket($id)
    {
        $ticket = Message::where(function($query) {
                            $query->where('from_user_id', Auth::id())
                                  ->orWhere('to_user_id', Auth::id());
                         })
                         ->where('status', 'closed')
                         ->findOrFail($id);
        
        $ticket->update(['status' => 'open']);
        
        return response()->json([
            'success' => true, 
            'message' => 'Ticket reopened successfully!'
        ]);
    }
    
    public function rateTicket(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $ticket = Message::where(function($query) {
                            $query->where('from_user_id', Auth::id())
                                  ->orWhere('to_user_id', Auth::id());
                         })
                         ->findOrFail($id);
        
        // Store rating in ticket's metadata or create a separate rating record
        $ratingData = [
            'rating' => $request->rating,
            'comment' => $request->comment,
            'rated_at' => now(),
            'rated_by' => Auth::id()
        ];
        
        $metadata = $ticket->metadata ? json_decode($ticket->metadata, true) : [];
        $metadata['rating'] = $ratingData;
        
        $ticket->update(['metadata' => json_encode($metadata)]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Thank you for your feedback!'
        ]);
    }
    
    public function knowledge()
    {
        $pageTitle = "Knowledge Base";
        
        $faqs = [
            [
                'question' => 'How do I make a deposit?',
                'answer' => 'You can make a deposit by going to the Deposit section in your dashboard. Choose your preferred payment method and follow the instructions.',
                'category' => 'Deposits'
            ],
            [
                'question' => 'How do I withdraw my earnings?',
                'answer' => 'Navigate to the Withdraw section and choose between deposit withdraw (with 20% fee) or wallet withdraw (from your wallets).',
                'category' => 'Withdrawals'
            ],
            [
                'question' => 'How do video views work?',
                'answer' => 'You can watch videos to earn based on your active plan. Each plan has daily video limits and specific earnings per view.',
                'category' => 'Video Views'
            ],
            [
                'question' => 'What are the different plans available?',
                'answer' => 'We offer various investment plans with different deposit amounts, video limits, and earning rates. Check the plans section for details.',
                'category' => 'Plans'
            ],
            [
                'question' => 'How do I upgrade my plan?',
                'answer' => 'You can upgrade your plan by making an additional deposit that meets the requirements of the higher plan.',
                'category' => 'Plans'
            ],
            [
                'question' => 'When will I receive my withdrawal?',
                'answer' => 'Withdrawals are processed within 24-48 hours after admin approval. Processing time may vary based on the payment method.',
                'category' => 'Withdrawals'
            ]
        ];
        
        return view('frontend.support.knowledge', compact('pageTitle', 'faqs'));
    }
    
    public function contact()
    {
        $pageTitle = "Contact Us";
        
        return view('frontend.support.contact', compact('pageTitle'));
    }
    
    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|in:general,technical,billing,account,feature,bug,feedback',
            'priority' => 'required|in:low,normal,high,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Send to best available admin/superadmin user
        $adminUser = $this->getBestAdminForTicket();
        
        if ($adminUser) {
            $message = Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $adminUser->id,
                'subject' => 'Contact Form: ' . $request->subject,
                'message' => "Name: {$request->name}\nEmail: {$request->email}\n\nMessage:\n{$request->message}",
                'category' => $request->category,
                'priority' => $request->priority,
                'message_type' => 'contact',
                'status' => 'open'
            ]);
            
            // Log contact form submission
            Log::info('Contact form submitted', [
                'from_user' => Auth::user()->username,
                'to_admin' => $adminUser->username,
                'subject' => $request->subject,
                'category' => $request->category
            ]);
            
            // If user wants to create a ticket, redirect to ticket details
            if ($request->create_ticket) {
                return redirect()->route('user.support.tickets')->with('success', 'Your message has been sent as a support ticket to admin! You can track its progress in your tickets.');
            }
        }
        
        return back()->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }
    
    /**
     * Check if a user is an admin or superadmin
     */
    private function isAdmin($user)
    {
        return $user->isAdmin();
    }
    
    /**
     * Get available admin users for ticket routing
     */
    private function getAvailableAdmins()
    {
        return User::getAvailableAdmins();
    }
    
    /**
     * Get the best admin user to route a ticket to
     */
    private function getBestAdminForTicket()
    {
        return User::getBestAdminForSupport();
    }
}
