<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsedTicket;
use App\Models\LotteryTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TicketValidationController extends Controller
{
    /**
     * Validate a ticket number for usage.
     */
    public function validateTicket(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ticket_number' => 'required|string|min:3|max:50',
            'usage_type' => 'string|in:investment,deposit,video_access,account_activation'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket number format',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticketNumber = strtoupper(trim($request->ticket_number));
        $userId = Auth::id();
        $usageType = $request->usage_type ?? 'investment';

        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to validate tickets',
                'error' => 'unauthenticated'
            ], 401);
        }

        try {
            // Check if ticket was already used by this user
            if (UsedTicket::isTicketUsedByUser($ticketNumber, $userId)) {
                $usedTicket = UsedTicket::where('ticket_number', $ticketNumber)
                                      ->where('user_id', $userId)
                                      ->first();
                
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket has already been used and cannot be reused.',
                    'is_reuse' => true,
                    'status' => $usedTicket->status,
                    'used_at' => $usedTicket->used_at->format('M d, Y H:i:s'),
                    'usage_type' => $usedTicket->usage_type
                ]);
            }

            // Check if ticket exists in lottery system (if you have a lottery_tickets table)
            $isValidTicket = $this->checkTicketExists($ticketNumber);
            
            if (!$isValidTicket) {
                // Mark as invalid in database
                UsedTicket::markAsInvalid($ticketNumber, $userId, [
                    'validation_method' => 'api_check',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'usage_type' => $usageType
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ticket number not found or already used by another user.',
                    'status' => 'invalid',
                    'can_retry' => false
                ]);
            }

            // Calculate time-based discount
            $discountInfo = $this->calculateTimeBasedDiscount();

            return response()->json([
                'success' => true,
                'message' => 'Ticket is valid and available for use',
                'status' => 'valid',
                'discount_percentage' => $discountInfo['discount'],
                'time_remaining_hours' => $discountInfo['timeRemaining'],
                'can_use' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validating ticket. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Validation error'
            ], 500);
        }
    }

    /**
     * Mark a ticket as used during investment/deposit.
     */
    public function markTicketAsUsed(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ticket_number' => 'required|string|min:3|max:50',
            'usage_type' => 'required|string|in:investment,deposit,video_access,account_activation',
            'discount_amount' => 'numeric|min:0',
            'discount_percentage' => 'numeric|min:0|max:100',
            'investment_id' => 'nullable|integer|exists:invests,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticketNumber = strtoupper(trim($request->ticket_number));
        $userId = Auth::id();

        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to mark tickets as used',
                'error' => 'unauthenticated'
            ], 401);
        }

        try {
            // Double check that ticket hasn't been used
            if (UsedTicket::isTicketUsedByUser($ticketNumber, $userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket has already been used'
                ]);
            }

            // Mark ticket as used
            $usedTicket = UsedTicket::markAsUsed(
                $ticketNumber,
                $userId,
                $request->usage_type,
                $request->discount_amount ?? 0,
                $request->discount_percentage ?? 0,
                $request->investment_id,
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'marked_via' => 'api'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Ticket marked as used successfully',
                'used_ticket_id' => $usedTicket->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking ticket as used',
                'error' => config('app.debug') ? $e->getMessage() : 'Processing error'
            ], 500);
        }
    }

    /**
     * Get user's ticket usage history.
     */
    public function getTicketHistory(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $limit = $request->get('limit', 10);

        // Check if user is authenticated
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to view ticket history',
                'error' => 'unauthenticated'
            ], 401);
        }

        try {
            $history = UsedTicket::getUserTicketHistory($userId, $limit);
            $stats = UsedTicket::getUserTicketStats($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'history' => $history,
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving ticket history',
                'error' => config('app.debug') ? $e->getMessage() : 'Retrieval error'
            ], 500);
        }
    }

    /**
     * Check if ticket exists in the lottery system.
     * This method ensures tickets are valid and available for use.
     */
    private function checkTicketExists(string $ticketNumber): bool
    {
        // Enhanced validation patterns - each pattern checks independently
        $validPatterns = [
            // TKT prefix format (legacy)
            '/^TKT\w+$/i',
            // LT prefix format (legacy)
            '/^LT\w+$/i',
            // COMM prefix format (like COMM-2FE5-015E_1) - legacy
            '/^COMM-[A-F0-9]{4}-[A-F0-9]{4}_\d+$/i',
            // 6 or more digits (simple numeric)
            '/^[0-9]{6,}$/',
            // Pure hex format with dashes (new standard: XXXX-XXXX-XXXX-XXXX)
            '/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/i',
            // General alphanumeric with dashes (backward compatibility)
            '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/i',
            // Mixed case alphanumeric without dashes (8+ characters)
            '/^[A-Z0-9]{8,}$/i',
            // Tickets with trailing underscore (like 6106-AC74-D736-1771_)
            '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}_$/i',
            // Tickets with underscore and suffix (like 6106-AC74-D736-1771_LT1)
            '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}_[A-Z0-9]+$/i'
        ];
        
        // Check if ticket matches any valid pattern
        $isValidFormat = false;
        foreach ($validPatterns as $pattern) {
            if (preg_match($pattern, $ticketNumber)) {
                $isValidFormat = true;
                break;
            }
        }
        
        if (!$isValidFormat) {
            return false;
        }
        
        // Check if ticket is already used by ANY user
        if (UsedTicket::isTicketUsed($ticketNumber)) {
            return false;
        }
        
        // Check if exists in lottery_tickets table with proper status
        if (class_exists(LotteryTicket::class)) {
            return LotteryTicket::where('ticket_number', $ticketNumber)
                ->where('is_virtual', 0) // Exclude virtual tickets
                ->where('status', 'active') // Only active tickets are valid
                ->exists();
        }
        
        // Fallback: Check database directly if model doesn't exist
        try {
            // Use a comprehensive check including used_tickets table
            $result = DB::select("
                SELECT 
                    lt.ticket_number,
                    lt.status,
                    lt.is_virtual,
                    ut.ticket_number as used_ticket
                FROM lottery_tickets lt
                LEFT JOIN used_tickets ut ON lt.ticket_number = ut.ticket_number
                WHERE lt.ticket_number = ?
                    AND lt.is_virtual = 0
                    AND lt.status = 'active'
                    AND ut.ticket_number IS NULL
            ", [$ticketNumber]);
            
            return !empty($result);
        } catch (\Exception $e) {
            // If table doesn't exist, assume properly formatted tickets are valid
            return true;
        }
    }

    /**
     * Calculate time-based discount percentage.
     */
    private function calculateTimeBasedDiscount(): array
    {
        $purchaseTime = now();
        $drawTime = now()->addHours(24); // Assuming 24 hours until next draw
        
        $totalTime = 24 * 60 * 60; // 24 hours in seconds
        $remainingTime = abs($drawTime->diffInSeconds($purchaseTime)); // Use abs to ensure positive
        
        // Calculate discount percentage (max 5%, full discount when max time remaining)
        $maxDiscount = 5;
        $discountPercentage = max(0, ($remainingTime / $totalTime) * $maxDiscount);
        
        // For new tickets (close to 24 hours remaining), give full discount
        if ($remainingTime >= ($totalTime * 0.95)) { // If more than 95% of time remaining
            $discountPercentage = $maxDiscount;
        }
        
        return [
            'discount' => round($discountPercentage, 2),
            'timeRemaining' => max(0, floor($remainingTime / 3600)) // Hours remaining
        ];
    }
}
