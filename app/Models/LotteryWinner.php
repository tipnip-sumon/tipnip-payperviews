<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotteryWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'lottery_draw_id',
        'lottery_ticket_id',
        'user_id',
        'prize_position',
        'prize_name',
        'prize_amount',
        'claim_status',
        'claimed_at',
        'claim_method',
        'is_manual_selection',
        'selected_at',
        'selected_by',
        'winner_index',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'selected_at' => 'datetime',
        'prize_amount' => 'decimal:2',
        'is_manual_selection' => 'boolean',
    ];

    /**
     * Get the lottery draw
     */
    public function lotteryDraw()
    {
        return $this->belongsTo(LotteryDraw::class);
    }

    /**
     * Alias for lotteryDraw relationship
     */
    public function draw()
    {
        return $this->lotteryDraw();
    }

    /**
     * Get the winning ticket
     */
    public function lotteryTicket()
    {
        return $this->belongsTo(LotteryTicket::class);
    }

    /**
     * Alias for lotteryTicket relationship
     */
    public function ticket()
    {
        return $this->lotteryTicket();
    }

    /**
     * Get the user who won
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if prize is claimed
     */
    public function isClaimed()
    {
        return $this->claim_status === 'claimed';
    }

    /**
     * Check if prize claim is pending
     */
    public function isPending()
    {
        return $this->claim_status === 'pending';
    }

    /**
     * Check if prize claim is expired
     */
    public function isExpired()
    {
        return $this->claim_status === 'expired';
    }

    /**
     * Claim the prize
     */
    public function claimPrize($method = 'manual')
    {
        if ($this->isClaimed()) {
            throw new \Exception('Prize already claimed.');
        }

        if ($this->isExpired()) {
            throw new \Exception('Prize claim has expired.');
        }

        // Add prize to user balance
        $user = $this->user;
        $user->deposit_wallet += $this->prize_amount;
        $user->save();

        // Create transaction record
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $this->prize_amount;
        $transaction->post_balance = $user->deposit_wallet;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->trx = getTrx();
        $transaction->remark = 'lottery_prize_claim';
        $transaction->details = 'Lottery prize claimed for Draw #' . $this->lottery_draw_id . ' - ' . $this->getPrizePositionText();
        $transaction->save();

        // Update claim status
        $this->update([
            'claim_status' => 'claimed',
            'claimed_at' => now(),
            'claim_method' => $method
        ]);

        return true;
    }

    /**
     * Get claim status badge
     */
    public function getClaimStatusBadge()
    {
        return match($this->claim_status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'claimed' => '<span class="badge bg-success">Claimed</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>',
            default => '<span class="badge bg-light">Unknown</span>',
        };
    }

    /**
     * Get prize position text
     */
    public function getPrizePositionText()
    {
        return match($this->prize_position) {
            1 => '🥇 1st Place',
            2 => '🥈 2nd Place', 
            3 => '🥉 3rd Place',
            default => "#{$this->prize_position} Place",
        };
    }

    /**
     * Scope for pending claims
     */
    public function scopePending($query)
    {
        return $query->where('claim_status', 'pending');
    }

    /**
     * Scope for claimed prizes
     */
    public function scopeClaimed($query)
    {
        return $query->where('claim_status', 'claimed');
    }

    /**
     * Scope for user winners
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
