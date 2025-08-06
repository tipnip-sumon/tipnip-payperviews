<?php

namespace App\Http\Controllers\admin;

use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables; 

class SupportController extends Controller
{
    /**
     * Display the support dashboard
     */
    public function index()
    {
        $stats = [
            'total_tickets' => Message::where('message_type', 'support')->whereNull('reply_to_id')->count(),
            'open_tickets' => Message::where('message_type', 'support')->whereNull('reply_to_id')->where('status', 'open')->count(),
            'pending_tickets' => Message::where('message_type', 'support')->whereNull('reply_to_id')->where('status', 'pending')->count(),
            'closed_tickets' => Message::where('message_type', 'support')->whereNull('reply_to_id')->where('status', 'closed')->count(),
            'today_tickets' => Message::where('message_type', 'support')->whereNull('reply_to_id')->whereDate('created_at', today())->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'satisfaction_rating' => $this->calculateSatisfactionRating()
        ];

        return view('admin.support.index', compact('stats'));
    }

    /**
     * Display all support tickets
     */
    public function tickets(Request $request)
    {
        if ($request->ajax()) {
            $query = Message::with(['sender', 'recipient', 'replies'])
                ->where('message_type', 'support')
                ->whereNull('reply_to_id') // Only parent messages, not replies
                ->select([
                    'id', 'from_user_id', 'to_user_id', 'subject', 'status', 'priority', 
                    'created_at', 'updated_at', 'is_starred', 'metadata'
                ]);

            // Apply filters
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('priority') && $request->priority != '') {
                $query->where('priority', $request->priority);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addColumn('user', function ($ticket) {
                    return $ticket->sender ? $ticket->sender->username : 'Unknown User';
                })
                ->addColumn('replies_count', function ($ticket) {
                    return $ticket->replies()->count();
                })
                ->addColumn('last_activity', function ($ticket) {
                    $lastReply = $ticket->replies()->latest()->first();
                    return $lastReply ? $lastReply->created_at->diffForHumans() : $ticket->created_at->diffForHumans();
                })
                ->addColumn('status_badge', function ($ticket) {
                    $badgeClass = match($ticket->status) {
                        'open' => 'bg-success',
                        'pending' => 'bg-warning',
                        'closed' => 'bg-secondary',
                        default => 'bg-primary'
                    };
                    return "<span class='badge {$badgeClass}'>" . ucfirst($ticket->status) . "</span>";
                })
                ->addColumn('priority_badge', function ($ticket) {
                    $badgeClass = match($ticket->priority) {
                        'urgent' => 'bg-danger',
                        'high' => 'bg-warning',
                        'normal' => 'bg-info',
                        'low' => 'bg-success',
                        default => 'bg-secondary'
                    };
                    return "<span class='badge {$badgeClass}'>" . ucfirst($ticket->priority) . "</span>";
                })
                ->addColumn('actions', function ($ticket) {
                    $starred = $ticket->is_starred ? 'text-warning' : 'text-muted';
                    return "
                        <div class='btn-group' role='group'>
                            <button type='button' class='btn btn-sm btn-outline-primary view-ticket' data-id='{$ticket->id}'>
                                <i class='fas fa-eye'></i>
                            </button>
                            <button type='button' class='btn btn-sm btn-outline-warning star-ticket {$starred}' data-id='{$ticket->id}'>
                                <i class='fas fa-star'></i>
                            </button>
                            <div class='btn-group' role='group'>
                                <button type='button' class='btn btn-sm btn-outline-secondary dropdown-toggle' data-bs-toggle='dropdown'>
                                    <i class='fas fa-cogs'></i>
                                </button>
                                <ul class='dropdown-menu'>
                                    <li><a class='dropdown-item change-status' href='#' data-id='{$ticket->id}' data-status='open'>Mark as Open</a></li>
                                    <li><a class='dropdown-item change-status' href='#' data-id='{$ticket->id}' data-status='pending'>Mark as Pending</a></li>
                                    <li><a class='dropdown-item change-status' href='#' data-id='{$ticket->id}' data-status='closed'>Mark as Closed</a></li>
                                </ul>
                            </div>
                        </div>
                    ";
                })
                ->rawColumns(['status_badge', 'priority_badge', 'actions'])
                ->make(true);
        }

        return view('admin.support.tickets');
    }

    /**
     * Show a specific ticket with details and replies
     */
    public function show($id)
    {
        $ticket = Message::with(['sender', 'recipient', 'replies.sender', 'replies.recipient'])
            ->where('message_type', 'support')
            ->findOrFail($id);

        // Mark as read by admin
        $metadata = $ticket->metadata ? json_decode($ticket->metadata, true) : [];
        if (!isset($metadata['admin_read'])) {
            $metadata['admin_read'] = true;
            $metadata['admin_read_at'] = now()->toISOString();
            $ticket->update(['metadata' => json_encode($metadata)]);
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'html' => view('admin.support.partials.ticket-details', compact('ticket'))->render()
        ]);
    }

    /**
     * Reply to a support ticket
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
        ]);

        $ticket = Message::where('message_type', 'support')->findOrFail($id);

        // Handle file attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('support/attachments', $filename, 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }

        // Find the corresponding user for the admin reply
        $adminUser = $this->getAdminUserForReply();
        
        if (!$adminUser) {
            return response()->json([
                'success' => false,
                'message' => 'No admin user found to send reply from.'
            ], 400);
        }

        // Create reply message - send to the original ticket creator
        $reply = Message::create([
            'from_user_id' => $adminUser->id, // Use the actual admin user ID
            'to_user_id' => $ticket->from_user_id, // Send to ticket creator
            'subject' => 'Re: ' . $ticket->subject,
            'message' => $request->message,
            'category' => $ticket->category,
            'status' => 'open',
            'priority' => $ticket->priority,
            'attachments' => !empty($attachments) ? json_encode($attachments) : null,
            'message_type' => 'support',
            'reply_to_id' => $ticket->id,
            'metadata' => json_encode([
                'admin_reply' => true,
                'admin_id' => $adminUser->id,
                'admin_name' => $adminUser->firstname . ' ' . $adminUser->lastname
            ])
        ]);

        // Update parent ticket status
        $ticket->update([
            'status' => $request->input('status', 'pending'), // Use provided status or default to pending
            'priority' => $request->input('priority', $ticket->priority), // Use provided priority or keep current
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'reply' => $reply->load(['sender', 'recipient'])
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed'
        ]);

        $ticket = Message::where('message_type', 'support')->findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully'
        ]);
    }

    /**
     * Toggle ticket star status
     */
    public function toggleStar($id)
    {
        $ticket = Message::where('message_type', 'support')->findOrFail($id);
        $ticket->update(['is_starred' => !$ticket->is_starred]);

        return response()->json([
            'success' => true,
            'starred' => $ticket->is_starred,
            'message' => $ticket->is_starred ? 'Ticket starred' : 'Ticket unstarred'
        ]);
    }

    /**
     * Bulk actions for tickets
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:status_open,status_pending,status_closed,star,unstar,delete',
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:messages,id'
        ]);

        $tickets = Message::whereIn('id', $request->ticket_ids)
            ->where('message_type', 'support');

        switch ($request->action) {
            case 'status_open':
                $tickets->update(['status' => 'open']);
                $message = 'Tickets marked as open';
                break;
            case 'status_pending':
                $tickets->update(['status' => 'pending']);
                $message = 'Tickets marked as pending';
                break;
            case 'status_closed':
                $tickets->update(['status' => 'closed']);
                $message = 'Tickets marked as closed';
                break;
            case 'star':
                $tickets->update(['is_starred' => true]);
                $message = 'Tickets starred';
                break;
            case 'unstar':
                $tickets->update(['is_starred' => false]);
                $message = 'Tickets unstarred';
                break;
            case 'delete':
                // Also delete replies
                Message::whereIn('reply_to_id', $request->ticket_ids)->delete();
                $tickets->delete();
                $message = 'Tickets deleted';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Export tickets data
     */
    public function export(Request $request)
    {
        $query = Message::with(['sender'])
            ->where('message_type', 'support')
            ->whereNull('reply_to_id') // Only parent messages
            ->select([
                'id', 'from_user_id', 'subject', 'message', 'status', 
                'priority', 'created_at', 'updated_at', 'metadata'
            ]);

        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->get();

        $csv = "ID,User,Subject,Status,Priority,Created At,Rating\n";
        foreach ($tickets as $ticket) {
            $metadata = $ticket->metadata ? json_decode($ticket->metadata, true) : [];
            $rating = isset($metadata['rating']) ? $metadata['rating']['rating'] : 'N/A';
            
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $ticket->id,
                $ticket->sender ? $ticket->sender->username : 'Unknown',
                '"' . str_replace('"', '""', $ticket->subject) . '"',
                $ticket->status,
                $ticket->priority,
                $ticket->created_at->format('Y-m-d H:i:s'),
                $rating
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="support_tickets_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime()
    {
        $tickets = Message::where('message_type', 'support')
            ->whereNull('reply_to_id')
            ->get();

        if ($tickets->isEmpty()) {
            return 'N/A';
        }

        $totalResponseTime = 0;
        $count = 0;

        foreach ($tickets as $ticket) {
            $replies = Message::where('reply_to_id', $ticket->id)
                ->whereNotNull('metadata')
                ->get();

            foreach ($replies as $reply) {
                $metadata = json_decode($reply->metadata, true);
                if (isset($metadata['admin_reply']) && $metadata['admin_reply']) {
                    $responseTime = $reply->created_at->diffInHours($ticket->created_at);
                    $totalResponseTime += $responseTime;
                    $count++;
                    break; // Only count first admin response
                }
            }
        }

        if ($count === 0) {
            return 'N/A';
        }

        $avgHours = $totalResponseTime / $count;
        
        if ($avgHours < 1) {
            return round($avgHours * 60) . ' minutes';
        } elseif ($avgHours < 24) {
            return round($avgHours, 1) . ' hours';
        } else {
            return round($avgHours / 24, 1) . ' days';
        }
    }

    /**
     * Calculate satisfaction rating
     */
    private function calculateSatisfactionRating()
    {
        $ratedTickets = Message::where('message_type', 'support')
            ->whereNull('reply_to_id')
            ->whereNotNull('metadata')
            ->get()
            ->filter(function ($ticket) {
                $metadata = json_decode($ticket->metadata, true);
                return isset($metadata['rating']);
            });

        if ($ratedTickets->isEmpty()) {
            return 'N/A';
        }

        $totalRating = 0;
        foreach ($ratedTickets as $ticket) {
            $metadata = json_decode($ticket->metadata, true);
            $totalRating += $metadata['rating']['rating'];
        }

        $averageRating = $totalRating / $ratedTickets->count();

        return round($averageRating, 1) . '/5';
    }

    /**
     * Get the admin user for replies with proper fallback
     */
    private function getAdminUserForReply()
    {
        // First try to get the best admin for support
        $adminUser = User::getBestAdminForSupport();
        
        if ($adminUser) {
            return $adminUser;
        }
        
        // Fallback: try to find any admin user by email
        $adminEmails = Admin::pluck('email')->toArray();
        if (!empty($adminEmails)) {
            $adminUser = User::whereIn('email', $adminEmails)->where('status', 1)->first();
            if ($adminUser) {
                return $adminUser;
            }
        }
        
        // Last fallback: use any active user with admin-like characteristics
        $adminUser = User::where('status', 1)
            ->where(function($query) {
                $query->where('email', 'like', '%admin%')
                      ->orWhere('username', 'like', '%admin%');
            })
            ->first();
            
        if ($adminUser) {
            return $adminUser;
        }
        
        // Ultimate fallback: use the first active user
        return User::where('status', 1)->first();
    }
}
