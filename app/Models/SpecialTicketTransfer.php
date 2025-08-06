<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Carbon\Carbon;

class SpecialTicketTransfer extends Model
{
    protected $fillable = [
        'special_ticket_id',
        'from_user_id',
        'to_user_id',
        'transfer_amount',
        'transfer_type',
        'transfer_message',
        'status',
        'transfer_requested_at',
        'transfer_completed_at',
        'expires_at',
        'transfer_code',
        'requires_acceptance',
    ];

    protected $casts = [
        'transfer_amount' => 'decimal:2',
        'transfer_requested_at' => 'datetime',
        'transfer_completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'requires_acceptance' => 'boolean',
    ];

    /**
     * Get the special ticket being transferred
     */
    public function specialTicket()
    {
        return $this->belongsTo(SpecialLotteryTicket::class, 'special_ticket_id');
    }

    /**
     * Get the user who is transferring the ticket
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who will receive the ticket
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Generate unique transfer code
     */
    public static function generateTransferCode()
    {
        do {
            $code = 'TRANSFER-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
        } while (self::where('transfer_code', $code)->exists());
        
        return $code;
    }

    /**
     * Create a new transfer request
     */
    public static function createTransfer($ticketId, $fromUserId, $toUserId, $transferType = 'gift', $message = null, $amount = 0)
    {
        $ticket = SpecialLotteryTicket::findOrFail($ticketId);
        
        // Validate ticket can be transferred
        if (!$ticket->canBeTransferred()) {
            throw new \Exception('This ticket cannot be transferred.');
        }

        // Validate ownership
        if ($ticket->current_owner_id != $fromUserId) {
            throw new \Exception('You do not own this ticket.');
        }

        // Prevent self-transfer
        if ($fromUserId == $toUserId) {
            throw new \Exception('You cannot transfer a token to yourself.');
        }

        // Check if recipient exists
        $recipient = User::find($toUserId);
        if (!$recipient) {
            throw new \Exception('Recipient user not found.');
        }

        // Create transfer request
        $transfer = self::create([
            'special_ticket_id' => $ticketId,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'transfer_amount' => $amount,
            'transfer_type' => $transferType,
            'transfer_message' => $message,
            'status' => 'pending',
            'transfer_requested_at' => now(),
            'expires_at' => now()->addHours(48), // 48 hours to accept
            'transfer_code' => self::generateTransferCode(),
            'requires_acceptance' => true,
        ]);

        return $transfer;
    }

    /**
     * Accept the transfer
     */
    public function acceptTransfer()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Transfer is not pending.');
        }

        // Auto-expire if past expiry time
        if ($this->checkAndExpire()) {
            throw new \Exception('Transfer request has expired.');
        }

        DB::beginTransaction();
        try {
            // Update ticket ownership
            $this->specialTicket->update([
                'current_owner_id' => $this->to_user_id,
                'transfer_count' => $this->specialTicket->transfer_count + 1,
                'last_transferred_at' => now(),
            ]);

            // Handle payment if required
            if ($this->transfer_amount > 0 && $this->transfer_type === 'sale') {
                $buyer = $this->toUser;
                $seller = $this->fromUser;

                // Check buyer has enough balance
                if ($buyer->deposit_wallet < $this->transfer_amount) {
                    throw new \Exception('Insufficient balance to complete purchase.');
                }

                // Transfer money
                $buyer->deposit_wallet -= $this->transfer_amount;
                $seller->deposit_wallet += $this->transfer_amount;
                $buyer->save();
                $seller->save();

                // Create transaction records
                $this->createTransactionRecord($buyer->id, $this->transfer_amount, '-', 'purchase_special_token');
                $this->createTransactionRecord($seller->id, $this->transfer_amount, '+', 'sell_special_token');
            }

            // Update transfer status
            $this->update([
                'status' => 'completed',
                'transfer_completed_at' => now(),
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject the transfer
     */
    public function rejectTransfer()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Transfer is not pending.');
        }

        $this->update(['status' => 'rejected']);
    }

    /**
     * Cancel the transfer (by sender)
     */
    public function cancelTransfer()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Transfer is not pending.');
        }

        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if transfer is expired
     */
    public function isExpired()
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Auto-expire transfer if past expiry time
     */
    public function checkAndExpire()
    {
        if ($this->status === 'pending' && $this->isExpired()) {
            $this->update(['status' => 'expired']);
            return true;
        }
        return false;
    }

    /**
     * Get all expired pending transfers and mark them as expired
     */
    public static function expirePendingTransfers()
    {
        $expiredTransfers = self::where('status', 'pending')
                              ->where('expires_at', '<', now())
                              ->get();

        foreach ($expiredTransfers as $transfer) {
            $transfer->update(['status' => 'expired']);
        }

        return $expiredTransfers->count();
    }

    /**
     * Create transaction record
     */
    private function createTransactionRecord($userId, $amount, $type, $remark)
    {
        $user = User::find($userId);
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->deposit_wallet;
        $transaction->charge = 0;
        $transaction->trx_type = $type;
        $transaction->trx = getTrx();
        $transaction->wallet_type = 'deposit_wallet';
        $transaction->remark = $remark;
        $transaction->details = "Special ticket transfer: {$this->transfer_code}";
        $transaction->save();
    }

    /**
     * Get transfer status badge
     */
    public function getStatusBadge()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-light">Unknown</span>',
        };
    }

    /**
     * Scope for pending transfers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope for user's incoming transfers
     */
    public function scopeIncoming($query, $userId)
    {
        return $query->where('to_user_id', $userId);
    }

    /**
     * Scope for user's outgoing transfers
     */
    public function scopeOutgoing($query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }
}
