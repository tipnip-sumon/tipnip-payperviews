<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LotteryTicket;
use App\Models\SpecialTicketTransfer;
use App\Models\User;
use App\Services\SpecialTicketService;
use Illuminate\Support\Facades\DB;

class SponsorTicketController extends Controller
{
    /**
     * Show sponsor tickets dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $pageTitle = 'My Sponsor Tickets';
        
        // Get sponsor tickets (tickets where user is the sponsor/original owner)
        // Now using unified lottery_tickets table with token_type = 'special'
        $sponsorTickets = LotteryTicket::where('sponsor_user_id', $user->id)
                                     ->where('original_owner_id', $user->id)
                                     ->where('token_type', 'special')
                                     ->with(['referralUser', 'currentOwner'])
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        
        // Get received tickets (tickets transferred to this user)
        $receivedTickets = LotteryTicket::where('current_owner_id', $user->id)
                                      ->where('original_owner_id', '!=', $user->id)
                                      ->where('token_type', 'special')
                                      ->with(['originalOwner', 'referralUser'])
                                      ->orderBy('created_at', 'desc')
                                      ->get();
        
        // Get transfer statistics
        $transferStats = [
            'total_sponsor_tickets' => $sponsorTickets->count(),
            'active_sponsor_tickets' => $sponsorTickets->where('status', 'active')->count(),
            'transferred_out' => $sponsorTickets->where('current_owner_id', '!=', $user->id)->count(),
            'still_owned' => $sponsorTickets->where('current_owner_id', $user->id)->count(),
            'received_tickets' => $receivedTickets->count(),
            'usable_tokens' => $receivedTickets->where('status', 'active')->where('is_valid_token', true)->count(),
        ];
        
        return view('user.sponsor_tickets.index', compact(
            'pageTitle', 'sponsorTickets', 'receivedTickets', 'transferStats'
        ));
    }
    
    /**
     * Show transfer form
     */
    public function showTransfer($ticketId)
    {
        $user = Auth::user();
        $ticket = LotteryTicket::where('token_type', 'special')->findOrFail($ticketId);
        
        // Validate ownership and transferability
        if ($ticket->current_owner_id != $user->id) {
            return back()->with('error', 'You do not own this ticket.');
        }
        
        if (!$ticket->canBeTransferred()) {
            return back()->with('error', 'This ticket cannot be transferred.');
        }
        
        $pageTitle = 'Transfer Sponsor Ticket';
        
        return view('user.sponsor_tickets.transfer', compact('pageTitle', 'ticket'));
    }
    
    /**
     * Process ticket transfer
     */
    public function transfer(Request $request, $ticketId)
    {
        $request->validate([
            'recipient_username' => 'required|string|exists:users,username',
            'transfer_message' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        $ticket = LotteryTicket::where('token_type', 'special')->findOrFail($ticketId);
        
        // Validate ownership
        if ($ticket->current_owner_id != $user->id) {
            return back()->with('error', 'You do not own this ticket.');
        }
        
        // Get recipient
        $recipient = User::where('username', $request->recipient_username)->first();
        if (!$recipient) {
            return back()->with('error', 'Recipient user not found.');
        }
        
        // Prevent self-transfer
        if ($recipient->id == $user->id) {
            return back()->with('error', 'You cannot transfer a ticket to yourself.');
        }
        
        try {
            DB::beginTransaction();
            
            // Create transfer record
            $transfer = SpecialTicketTransfer::createTransfer(
                $ticket->id,
                $user->id,
                $recipient->id,
                'gift',
                $request->transfer_message ?? "Sponsor ticket transfer to {$recipient->username}",
                0 // No payment for gift transfers
            );
            
            // For direct transfers, auto-accept
            $transfer->acceptTransfer();
            
            DB::commit();
            
            return redirect()->route('user.sponsor-tickets.index')
                           ->with('success', "Sponsor ticket successfully transferred to {$recipient->username}!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Show transfer history
     */
    public function transferHistory()
    {
        $user = Auth::user();
        $pageTitle = 'Transfer History';
        
        // Get outgoing transfers (sent by user)
        $outgoingTransfers = SpecialTicketTransfer::where('from_user_id', $user->id)
                                                ->with(['specialTicket', 'toUser'])
                                                ->orderBy('created_at', 'desc')
                                                ->get();
        
        // Get incoming transfers (received by user)
        $incomingTransfers = SpecialTicketTransfer::where('to_user_id', $user->id)
                                                ->with(['specialTicket', 'fromUser'])
                                                ->orderBy('created_at', 'desc')
                                                ->get();
        
        return view('user.sponsor_tickets.history', compact(
            'pageTitle', 'outgoingTransfers', 'incomingTransfers'
        ));
    }
    
    /**
     * Use ticket as discount token
     */
    public function useAsToken(Request $request, $ticketId)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_amount' => 'required|numeric|min:1',
        ]);
        
        $user = Auth::user();
        $ticket = LotteryTicket::where('token_type', 'special')->findOrFail($ticketId);
        
        // Validate ownership and usage rights
        if (!$ticket->canBeUsedByUser($user->id)) {
            return back()->with('error', 'You cannot use this ticket for discount.');
        }
        
        try {
            $specialTicketService = new SpecialTicketService();
            $result = $specialTicketService->applyTokensToPlantPurchase(
                $user->id,
                $request->plan_id,
                $request->plan_amount,
                [$ticket->id]
            );
            
            if ($result['success']) {
                return back()->with('success', 
                    "Discount applied! You saved \${$result['total_discount']} on your purchase.");
            } else {
                return back()->with('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to apply discount: ' . $e->getMessage());
        }
    }
    
    /**
     * Get available tickets for AJAX
     */
    public function getAvailableTickets()
    {
        $user = Auth::user();
        
        $tickets = LotteryTicket::where('token_type', 'special')
                               ->where('current_owner_id', $user->id)
                               ->where('status', 'active')
                               ->where('is_transferable', true)
                               ->select('id', 'ticket_number', 'created_at', 'transfer_count')
                               ->get();
        
        return response()->json($tickets);
    }
}
