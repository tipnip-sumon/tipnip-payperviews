<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\LotteryDraw;

class StandardizedTicketNumberService
{
    /**
     * Generate standardized ticket number
     * Format: DRAW_YYYY_DD-TNNNNN
     * 
     * @param int $drawId
     * @param string $type (L=Lottery, S=Special, P=Sponsor, V=Virtual)
     * @return string
     */
    public static function generateTicketNumber($drawId, $type = 'L')
    {
        // Get current year
        $year = date('Y');
        
        // Format draw ID to 2 digits
        $formattedDrawId = str_pad($drawId, 2, '0', STR_PAD_LEFT);
        
        // Get next sequence number for this draw and type
        $sequence = self::getNextSequenceNumber($drawId, $type);
        
        // Format sequence to 5 digits
        $formattedSequence = str_pad($sequence, 5, '0', STR_PAD_LEFT);
        
        // Create standardized ticket number
        return "DRAW_{$year}_{$formattedDrawId}-{$type}{$formattedSequence}";
    }
    
    /**
     * Get next sequence number for draw and type
     */
    private static function getNextSequenceNumber($drawId, $type)
    {
        // Count existing tickets of this type for this draw using the new format
        $pattern = "DRAW_" . date('Y') . "_" . str_pad($drawId, 2, '0', STR_PAD_LEFT) . "-{$type}%";
        
        $count = DB::table('lottery_tickets')
            ->where('lottery_draw_id', $drawId)
            ->where('ticket_number', 'LIKE', $pattern)
            ->count();
        
        // Also count any tickets currently being generated in this request
        static $currentCounts = [];
        $key = "{$drawId}_{$type}";
        
        if (!isset($currentCounts[$key])) {
            $currentCounts[$key] = $count;
        }
        
        $currentCounts[$key]++;
        
        return $currentCounts[$key];
    }
    
    /**
     * Generate virtual ticket number
     */
    public static function generateVirtualTicketNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'V');
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
     * Generate sponsor ticket number
     */
    public static function generateSponsorTicketNumber($drawId)
    {
        return self::generateTicketNumber($drawId, 'P');
    }
    
    /**
     * Parse ticket number to get components
     */
    public static function parseTicketNumber($ticketNumber)
    {
        if (preg_match('/^DRAW_(\d{4})_(\d{2})-([LSPV])(\d{5})$/', $ticketNumber, $matches)) {
            return [
                'year' => $matches[1],
                'draw_id' => (int)$matches[2],
                'type' => $matches[3],
                'sequence' => (int)$matches[4],
                'type_name' => self::getTypeName($matches[3])
            ];
        }
        
        return null;
    }
    
    /**
     * Get type name from code
     */
    private static function getTypeName($typeCode)
    {
        return match($typeCode) {
            'L' => 'Lottery',
            'S' => 'Special',
            'P' => 'Sponsor', 
            'V' => 'Virtual',
            default => 'Unknown'
        };
    }
    
    /**
     * Validate ticket number format
     */
    public static function isValidFormat($ticketNumber)
    {
        return preg_match('/^DRAW_\d{4}_\d{2}-[LSPV]\d{5}$/', $ticketNumber);
    }
    
    /**
     * Get ticket type from number
     */
    public static function getTicketType($ticketNumber)
    {
        $parsed = self::parseTicketNumber($ticketNumber);
        return $parsed ? $parsed['type_name'] : null;
    }
    
    /**
     * Convert legacy ticket format to standardized format
     */
    public static function convertLegacyFormat($legacyNumber, $drawId, $type = 'L')
    {
        // For existing tickets that need conversion
        return self::generateTicketNumber($drawId, $type);
    }
    
    /**
     * Batch generate ticket numbers for multiple tickets
     */
    public static function batchGenerateTicketNumbers($drawId, $type, $count)
    {
        $tickets = [];
        
        for ($i = 0; $i < $count; $i++) {
            $tickets[] = self::generateTicketNumber($drawId, $type);
        }
        
        return $tickets;
    }
}
