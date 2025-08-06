<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'status',
        'usage_type',
        'discount_amount',
        'discount_percentage',
        'investment_id',
        'metadata',
        'used_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'used_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_USED = 'used';

    const USAGE_INVESTMENT = 'investment';
    const USAGE_DEPOSIT = 'deposit';
    const USAGE_VIDEO_ACCESS = 'video_access';
    const USAGE_ACCOUNT_ACTIVATION = 'account_activation';

    /**
     * Get the user that owns the used ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the investment associated with the used ticket.
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Invest::class, 'investment_id');
    }

    /**
     * Check if a ticket is already used by any user.
     */
    public static function isTicketUsed(string $ticketNumber): bool
    {
        return self::where('ticket_number', strtoupper($ticketNumber))
                   ->whereIn('status', [self::STATUS_VALID, self::STATUS_USED])
                   ->exists();
    }

    /**
     * Check if a ticket is used by a specific user.
     */
    public static function isTicketUsedByUser(string $ticketNumber, int $userId): bool
    {
        return self::where('ticket_number', strtoupper($ticketNumber))
                   ->where('user_id', $userId)
                   ->whereIn('status', [self::STATUS_VALID, self::STATUS_USED])
                   ->exists();
    }

    /**
     * Mark a ticket as used.
     */
    public static function markAsUsed(
        string $ticketNumber, 
        int $userId, 
        string $usageType, 
        float $discountAmount = 0, 
        float $discountPercentage = 0,
        ?int $investmentId = null,
        array $metadata = []
    ): self {
        return self::create([
            'ticket_number' => strtoupper($ticketNumber),
            'user_id' => $userId,
            'status' => self::STATUS_USED,
            'usage_type' => $usageType,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'investment_id' => $investmentId,
            'metadata' => $metadata,
            'used_at' => now(),
        ]);
    }

    /**
     * Mark a ticket as invalid.
     */
    public static function markAsInvalid(
        string $ticketNumber, 
        int $userId, 
        array $metadata = []
    ): self {
        return self::create([
            'ticket_number' => strtoupper($ticketNumber),
            'user_id' => $userId,
            'status' => self::STATUS_INVALID,
            'metadata' => $metadata,
            'used_at' => now(),
        ]);
    }

    /**
     * Get user's ticket usage history.
     */
    public static function getUserTicketHistory(int $userId, int $limit = 10)
    {
        return self::where('user_id', $userId)
                   ->orderBy('used_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get ticket usage statistics for a user.
     */
    public static function getUserTicketStats(int $userId): array
    {
        $stats = self::where('user_id', $userId)
                     ->selectRaw('
                         COUNT(*) as total_tickets,
                         SUM(CASE WHEN status = "used" THEN 1 ELSE 0 END) as used_tickets,
                         SUM(CASE WHEN status = "invalid" THEN 1 ELSE 0 END) as invalid_tickets,
                         SUM(CASE WHEN status = "used" THEN discount_amount ELSE 0 END) as total_savings
                     ')
                     ->first();

        return [
            'total_tickets' => $stats->total_tickets ?? 0,
            'used_tickets' => $stats->used_tickets ?? 0,
            'invalid_tickets' => $stats->invalid_tickets ?? 0,
            'total_savings' => $stats->total_savings ?? 0,
        ];
    }
}
