<?php

namespace App\Services;

use App\Models\LotteryTicket;
use App\Models\LotteryDraw;
use Illuminate\Support\Facades\DB;

class HexadecimalTicketService
{
    /**
     * Generate hexadecimal ticket number in format: D98E-6A6D-EABB-03C3
     * All ticket types use the same format
     */
    public static function generateTicketNumber($drawId = null, $ticketType = 'L')
    {
        do {
            // Generate 4 groups of 4 hexadecimal characters
            $group1 = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars
            $group2 = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars
            $group3 = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars
            $group4 = strtoupper(bin2hex(random_bytes(2))); // 4 hex chars
            
            $ticketNumber = "{$group1}-{$group2}-{$group3}-{$group4}";
            
            // Ensure uniqueness
            $exists = LotteryTicket::where('ticket_number', $ticketNumber)->exists();
            
        } while ($exists);
        
        return $ticketNumber;
    }
    
    /**
     * Generate lottery ticket number
     */
    public static function generateLotteryTicketNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'L');
    }
    
    /**
     * Generate special token number
     */
    public static function generateSpecialTokenNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'S');
    }
    
    /**
     * Generate virtual ticket number
     */
    public static function generateVirtualTicketNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'V');
    }
    
    /**
     * Generate sponsor ticket number
     */
    public static function generateSponsorTicketNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'P');
    }
    
    /**
     * Validate hexadecimal ticket format
     */
    public static function isValidFormat($ticketNumber)
    {
        // Pattern: XXXX-XXXX-XXXX-XXXX (4 groups of 4 hexadecimal characters)
        $pattern = '/^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$/';
        return preg_match($pattern, $ticketNumber);
    }
    
    /**
     * Parse hexadecimal ticket number
     */
    public static function parseTicketNumber($ticketNumber)
    {
        if (!self::isValidFormat($ticketNumber)) {
            return null;
        }
        
        $parts = explode('-', $ticketNumber);
        
        return [
            'group1' => $parts[0],
            'group2' => $parts[1],
            'group3' => $parts[2],
            'group4' => $parts[3],
            'full_number' => $ticketNumber,
            'format' => 'HEXADECIMAL'
        ];
    }
    
    /**
     * Generate multiple ticket numbers at once
     */
    public static function generateBulkTicketNumbers($count, $drawId = null, $ticketType = 'L')
    {
        $tickets = [];
        
        for ($i = 0; $i < $count; $i++) {
            $tickets[] = self::generateTicketNumber($drawId, $ticketType);
        }
        
        return $tickets;
    }
    
    /**
     * Convert existing ticket to hexadecimal format
     */
    public static function convertToHexFormat($existingTicketNumber)
    {
        // Generate a new hex format ticket
        $newTicketNumber = self::generateTicketNumber();
        
        return [
            'old_format' => $existingTicketNumber,
            'new_format' => $newTicketNumber,
            'converted_at' => now()
        ];
    }
    
    /**
     * Get ticket statistics for hexadecimal format
     */
    public static function getFormatStatistics()
    {
        $total = LotteryTicket::count();
        $hexFormat = LotteryTicket::where('ticket_number', 'REGEXP', '^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$')->count();
        $otherFormats = $total - $hexFormat;
        
        return [
            'total_tickets' => $total,
            'hexadecimal_format' => $hexFormat,
            'other_formats' => $otherFormats,
            'hex_percentage' => $total > 0 ? round(($hexFormat / $total) * 100, 2) : 0
        ];
    }
    
    /**
     * Migrate all existing tickets to hexadecimal format
     */
    public static function migrateAllTicketsToHexFormat($batchSize = 100)
    {
        // Get tickets that are NOT in hexadecimal format
        $nonHexTickets = LotteryTicket::whereRaw('ticket_number NOT REGEXP ?', ['^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$'])
                                    ->limit($batchSize)
                                    ->get();
        
        $migrated = 0;
        
        foreach ($nonHexTickets as $ticket) {
            $oldNumber = $ticket->ticket_number;
            $newNumber = self::generateTicketNumber($ticket->lottery_draw_id, 'L');
            
            // Update the ticket with new format
            $ticket->update([
                'ticket_number' => $newNumber,
                'old_ticket_number' => $oldNumber, // Store old number for reference
                'format_migrated_at' => now()
            ]);
            
            $migrated++;
        }
        
        // Count remaining non-hex tickets
        $remaining = LotteryTicket::whereRaw('ticket_number NOT REGEXP ?', ['^[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}$'])->count();
        
        return [
            'migrated_count' => $migrated,
            'remaining' => $remaining
        ];
    }
}
