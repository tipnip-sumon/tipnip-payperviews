<?php

namespace App\Http\Controllers\admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotterySetting;
use App\Models\GeneralSetting;
use App\Models\ConfigurationChange;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LotterySettingsController extends Controller
{
    /**
     * Display lottery settings management page
     */
    public function index()
    {
        try {
            $settings = LotterySetting::getSettings();
            
            // Get the current prize distribution type
            $currentDistributionType = $settings->prize_distribution_type ?? 'percentage';
            
            // Prepare settings data for the view with proper defaults
            $settingsArray = [
                'is_active' => $settings->is_active ?? true,
                'ticket_price' => $settings->ticket_price ?? 2.00,
                'max_tickets_per_user' => $settings->max_tickets_per_user ?? 10,
                'min_tickets_for_draw' => $settings->min_tickets_for_draw ?? 5,
                'draw_day' => $settings->draw_day ?? 0,
                'draw_hour' => $settings->draw_time ? $settings->draw_time->format('H') : 20,
                'draw_minute' => $settings->draw_time ? $settings->draw_time->format('i') : 0,
                'admin_commission_percentage' => $settings->admin_commission_percentage ?? 10.00,
                'auto_draw' => $settings->auto_draw ?? true,
                'auto_prize_distribution' => $settings->auto_prize_distribution ?? true,
                'ticket_expiry_hours' => $settings->ticket_expiry_hours ?? 24,
                'prize_distribution' => $this->extractPrizePercentages($settings->prize_structure ?? null, $currentDistributionType),
                'prize_structure' => $settings->prize_structure ?? [],
                'number_of_winners' => is_array($settings->prize_structure) ? count($settings->prize_structure) : 1,
                'auto_claim_days' => $settings->auto_claim_days ?? 30,
                'auto_refund_cancelled' => $settings->auto_refund_cancelled ?? true,
                'prize_claim_deadline' => $settings->prize_claim_deadline ?? 30,
                'allow_multiple_winners_per_place' => $settings->allow_multiple_winners_per_place ?? false,
                'prize_distribution_type' => $currentDistributionType,
                'manual_winner_selection' => $settings->manual_winner_selection ?? false,
                'show_virtual_tickets' => $settings->show_virtual_tickets ?? false,
                'virtual_ticket_multiplier' => $settings->virtual_ticket_multiplier ?? 100,
                'virtual_ticket_base' => $settings->virtual_ticket_base ?? 0,
                'virtual_user_id' => $settings->virtual_user_id ?? 1,
                'active_tickets_boost' => $settings->active_tickets_boost ?? 0,
            ];

            // Debug: Log the prize structure and distribution for troubleshooting
            Log::info('Prize Structure Debug', [
                'raw_prize_structure' => $settings->prize_structure ?? 'null',
                'extracted_distribution' => $settingsArray['prize_distribution'],
                'prize_distribution_type' => $settingsArray['prize_distribution_type'],
                'number_of_winners' => $settingsArray['number_of_winners']
            ]);

            // Calculate statistics
            $stats = [
                'total_settings' => count($settingsArray),
                'is_active' => $settings->is_active ?? false,
                'ticket_price' => $settings->ticket_price ?? 0,
                'admin_commission_percentage' => $settings->admin_commission_percentage ?? 0,
            ];

            // Get recent configuration changes
            $recentChanges = ConfigurationChange::getRecent(10, 'lottery');
            
            return view('admin.lottery-settings.index', compact('settings', 'settingsArray', 'stats', 'recentChanges'))
                  ->with('pageTitle', 'Lottery Settings Management');

        } catch (Exception $e) {
            Log::error('Lottery settings management error: ' . $e->getMessage());
            return redirect()->route('admin.lottery.index')->with('error', 'Failed to load lottery settings management page.');
        }
    }

    /** 
     * Update lottery settings
     */
    public function update(Request $request)
    {
        // Handle boolean fields - set to false if not present (unchecked checkboxes)
        $requestData = $request->all();
        $booleanFields = [
            'auto_draw', 'auto_prize_distribution', 'auto_refund_cancelled',
            'allow_multiple_winners_per_place', 'manual_winner_selection', 'show_virtual_tickets'
        ];
        
        foreach ($booleanFields as $field) {
            if (!isset($requestData[$field])) {
                $requestData[$field] = false;
            }
        }
        
        // Create a new request instance with the modified data
        $request->merge($requestData);
        
        // Check the processed show_virtual_tickets value for conditional validation
        $showVirtualTickets = (bool) $request->input('show_virtual_tickets', false);
        
        // Build validation rules based on prize distribution type
        $validationRules = [
            'is_active' => 'required|in:0,1',
            'ticket_price' => 'required|numeric|min:0.01|max:1000',
            'max_tickets_per_user' => 'required|integer|min:1|max:1000',
            'min_tickets_for_draw' => 'required|integer|min:1|max:1000',
            'draw_day' => 'required|integer|min:0|max:6',
            'draw_hour' => 'required|integer|min:0|max:23',
            'draw_minute' => 'required|integer|min:0|max:59',
            'admin_commission_percentage' => 'required|numeric|min:0|max:50',
            'auto_draw' => 'boolean',
            'auto_prize_distribution' => 'boolean',
            'auto_refund_cancelled' => 'boolean',
            'ticket_expiry_hours' => 'required|integer|min:1|max:8760',
            'prize_distribution' => 'required|array',
            'number_of_winners' => 'required|integer|min:1|max:10',
            'auto_claim_days' => 'required|integer|min:1|max:365',
            'prize_claim_deadline' => 'required|integer|min:1|max:365',
            'allow_multiple_winners_per_place' => 'boolean',
            'prize_distribution_type' => 'required|in:percentage,fixed_amount',
            'manual_winner_selection' => 'boolean',
            'show_virtual_tickets' => 'boolean',
            'virtual_ticket_base' => 'nullable|integer|min:0|max:10000',
            'virtual_user_id' => 'required|integer|min:1|max:999999',
            'active_tickets_boost' => 'nullable|integer|min:0|max:100000',
        ];
        
        // Conditional validation for virtual ticket multiplier
        if ($showVirtualTickets) {
            $validationRules['virtual_ticket_multiplier'] = 'required|integer|min:100|max:1000';
        } else {
            $validationRules['virtual_ticket_multiplier'] = 'nullable|integer|min:0|max:1000';
        }
        
        // Debug: Log the request data and validation rules
        Log::info('Lottery Settings Validation Debug', [
            'show_virtual_tickets_raw' => $request->show_virtual_tickets,
            'show_virtual_tickets_bool' => $showVirtualTickets,
            'virtual_ticket_multiplier' => $request->virtual_ticket_multiplier,
            'virtual_ticket_multiplier_validation' => $showVirtualTickets ? 'required|integer|min:100|max:1000' : 'nullable|integer|min:0|max:1000',
            'prize_distribution_type' => $request->prize_distribution_type,
            'prize_distribution' => $request->prize_distribution,
            'validation_rules' => $validationRules
        ]);
        
        // Set prize distribution validation based on type
        if ($request->prize_distribution_type === 'fixed_amount') {
            $validationRules['prize_distribution.*'] = 'required|numeric|min:0|max:1000000';
            // Validate multiple winners if present
            if ($request->has('multiple_winners')) {
                $validationRules['multiple_winners.*.*'] = 'nullable|numeric|min:0|max:1000000';
            }
        } else {
            $validationRules['prize_distribution.*'] = 'required|numeric|min:0|max:100';
            // Validate multiple winners if present
            if ($request->has('multiple_winners')) {
                $validationRules['multiple_winners.*.*'] = 'nullable|numeric|min:0|max:100';
            }
        }
        
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            // Log validation errors for debugging
            Log::error('Lottery Settings Validation Failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['_token'])
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your input and try again.',
                    'errors' => $validator->errors(),
                    'debug_info' => [
                        'show_virtual_tickets' => $request->show_virtual_tickets,
                        'virtual_ticket_multiplier' => $request->virtual_ticket_multiplier,
                        'failed_rules' => $validator->failed()
                    ]
                ], 422);
            }
            
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Validate prize distribution based on type
            if ($request->prize_distribution_type === 'percentage') {
                $totalPercentage = array_sum($request->prize_distribution);
                if (abs($totalPercentage - 100) > 0.01) {
                    $errorMessage = 'Prize distribution percentages must total exactly 100%. Current total: ' . $totalPercentage . '%';
                    
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 422);
                    }
                    
                    return back()->with('error', $errorMessage);
                }
            } else {
                // For fixed amounts, just ensure all values are positive
                $totalAmount = array_sum($request->prize_distribution);
                if ($totalAmount <= 0) {
                    $errorMessage = 'Prize distribution amounts must be greater than 0. Current total: $' . number_format($totalAmount, 2);
                    
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 422);
                    }
                    
                    return back()->with('error', $errorMessage);
                }
            }

            // Prepare prize structure based on distribution type
            $prizeStructure = [];
            foreach ($request->prize_distribution as $index => $value) {
                $position = $index + 1;
                $positionText = $this->getOrdinalNumber($position);
                
                $prizeData = [
                    'name' => $positionText . ' Prize',
                    'type' => $request->prize_distribution_type
                ];
                
                if ($request->prize_distribution_type === 'fixed_amount') {
                    $prizeData['amount'] = $value;
                } else {
                    $prizeData['percentage'] = $value;
                }
                
                // Handle multiple winners if enabled
                if ($request->allow_multiple_winners_per_place && isset($request->multiple_winners[$index])) {
                    $multipleWinners = [];
                    foreach ($request->multiple_winners[$index] as $winnerIndex => $winnerValue) {
                        if (is_numeric($winnerValue) && $winnerValue > 0) {
                            $multipleWinners[] = [
                                'winner' => $winnerIndex + 1,
                                $request->prize_distribution_type === 'fixed_amount' ? 'amount' : 'percentage' => $winnerValue
                            ];
                        }
                    }
                    
                    if (!empty($multipleWinners)) {
                        $prizeData['multiple_winners'] = $multipleWinners;
                    }
                }
                
                $prizeStructure[$position] = $prizeData;
            }

            // Prepare data for update
            $data = [
                'is_active' => (bool) $request->is_active,
                'ticket_price' => $request->ticket_price,
                'max_tickets_per_user' => (int) $request->max_tickets_per_user,
                'min_tickets_for_draw' => (int) $request->min_tickets_for_draw,
                'draw_day' => (int) $request->draw_day,
                'draw_time' => sprintf('%02d:%02d:00', (int) $request->draw_hour, (int) $request->draw_minute),
                'admin_commission_percentage' => $request->admin_commission_percentage,
                'auto_draw' => (bool) $request->auto_draw,
                'auto_prize_distribution' => (bool) $request->auto_prize_distribution,
                'auto_refund_cancelled' => (bool) $request->auto_refund_cancelled,
                'ticket_expiry_hours' => (int) $request->ticket_expiry_hours,
                'auto_claim_days' => (int) $request->auto_claim_days,
                'prize_claim_deadline' => (int) $request->prize_claim_deadline,
                'allow_multiple_winners_per_place' => (bool) $request->allow_multiple_winners_per_place,
                'prize_distribution_type' => $request->prize_distribution_type,
                'manual_winner_selection' => (bool) $request->manual_winner_selection,
                'show_virtual_tickets' => (bool) $request->show_virtual_tickets,
                'virtual_ticket_multiplier' => (int) ($request->virtual_ticket_multiplier ?? 100),
                'virtual_ticket_base' => (int) ($request->virtual_ticket_base ?? 0),
                'virtual_user_id' => (int) ($request->virtual_user_id ?? 1),
                'active_tickets_boost' => (int) ($request->active_tickets_boost ?? 0),
                'prize_structure' => $prizeStructure,
            ];

            LotterySetting::updateSettings($data);

            // Log the change (if you have an audit system)
            $this->logSettingsChange('Updated lottery settings', $data);

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                // Get the updated settings to return current state
                $updatedSettings = LotterySetting::first();
                
                // Parse draw time for frontend
                $drawTime = explode(':', $updatedSettings->draw_time);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Lottery settings updated successfully!',
                    'data' => [
                        'is_active' => (bool) $updatedSettings->is_active,
                        'ticket_price' => (float) $updatedSettings->ticket_price,
                        'max_tickets_per_user' => (int) $updatedSettings->max_tickets_per_user,
                        'min_tickets_for_draw' => (int) $updatedSettings->min_tickets_for_draw,
                        'draw_day' => (int) $updatedSettings->draw_day,
                        'draw_hour' => (int) $drawTime[0],
                        'draw_minute' => (int) $drawTime[1],
                        'admin_commission_percentage' => (float) $updatedSettings->admin_commission_percentage,
                        'auto_draw' => (bool) $updatedSettings->auto_draw,
                        'auto_prize_distribution' => (bool) $updatedSettings->auto_prize_distribution,
                        'auto_refund_cancelled' => (bool) $updatedSettings->auto_refund_cancelled,
                        'ticket_expiry_hours' => (int) $updatedSettings->ticket_expiry_hours,
                        'auto_claim_days' => (int) $updatedSettings->auto_claim_days,
                        'prize_claim_deadline' => (int) $updatedSettings->prize_claim_deadline,
                        'allow_multiple_winners_per_place' => (bool) $updatedSettings->allow_multiple_winners_per_place,
                        'prize_distribution_type' => $updatedSettings->prize_distribution_type,
                        'manual_winner_selection' => (bool) $updatedSettings->manual_winner_selection,
                        'show_virtual_tickets' => (bool) $updatedSettings->show_virtual_tickets,
                        'virtual_ticket_multiplier' => (float) $updatedSettings->virtual_ticket_multiplier,
                        'virtual_ticket_base' => (int) $updatedSettings->virtual_ticket_base,
                        'virtual_user_id' => (int) $updatedSettings->virtual_user_id,
                        'prize_structure' => $updatedSettings->prize_structure
                    ]
                ]);
            }

            return back()->with('success', 'Lottery settings updated successfully!');

        } catch (Exception $e) {
            Log::error('Lottery settings update error: ' . $e->getMessage());
            
            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update lottery settings: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Failed to update lottery settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to defaults
     */
    public function resetToDefaults(Request $request)
    {
        try {
            $defaultData = [
                'is_active' => true,
                'ticket_price' => 2.00,
                'max_tickets_per_user' => 10,
                'min_tickets_for_draw' => 5,
                'draw_day' => 0, // Sunday
                'draw_time' => '20:00:00',
                'admin_commission_percentage' => 10.00,
                'auto_draw' => true,
                'auto_prize_distribution' => true,
                'ticket_expiry_hours' => 24,
                'prize_structure' => [
                    1 => ['name' => 'First Prize', 'percentage' => 100]
                ],
            ];

            LotterySetting::updateSettings($defaultData);

            $this->logSettingsChange('Reset lottery settings to defaults', $defaultData);

            return back()->with('success', 'Lottery settings have been reset to default values!');

        } catch (Exception $e) {
            Log::error('Reset settings error: ' . $e->getMessage());
            return back()->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Create settings backup
     */
    public function createBackup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'backup_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $settings = LotterySetting::getSettings();
            $backupData = [
                'backup_name' => $request->backup_name,
                'created_at' => now()->toISOString(),
                'created_by' => Auth::user()->name ?? 'System',
                'settings' => $settings->toArray(),
            ];

            $filename = 'lottery_settings_backup_' . now()->format('Y-m-d_H-i-s') . '.json';
            Storage::disk('local')->put('backups/lottery/' . $filename, json_encode($backupData, JSON_PRETTY_PRINT));

            return back()->with('success', 'Settings backup created successfully: ' . $filename);

        } catch (Exception $e) {
            Log::error('Settings backup error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Export settings as JSON
     */
    public function export(Request $request)
    {
        try {
            $settings = LotterySetting::getSettings();
            $exportData = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->name ?? 'System',
                'version' => '1.0',
                'settings' => $settings->toArray(),
            ];

            $filename = 'lottery_settings_export_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($exportData)
                           ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                           ->header('Content-Type', 'application/json');

        } catch (Exception $e) {
            Log::error('Settings export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings from JSON
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'backup_file' => 'required|file|mimes:json|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $file = $request->file('backup_file');
            $content = file_get_contents($file->getPathname());
            $importData = json_decode($content, true);

            if (!$importData || !isset($importData['settings'])) {
                return back()->with('error', 'Invalid backup file format.');
            }

            $settings = $importData['settings'];
            
            // Validate imported settings structure
            $requiredFields = ['ticket_price', 'max_tickets_per_user', 'min_tickets_for_draw'];
            foreach ($requiredFields as $field) {
                if (!isset($settings[$field])) {
                    return back()->with('error', "Missing required field: {$field}");
                }
            }

            LotterySetting::updateSettings($settings);

            $this->logSettingsChange('Imported lottery settings from backup file', $settings);

            return back()->with('success', 'Lottery settings imported successfully from backup file!');

        } catch (Exception $e) {
            Log::error('Settings import error: ' . $e->getMessage());
            return back()->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Get ordinal number (1st, 2nd, 3rd, etc.)
     */
    private function getOrdinalNumber($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }
        
        return $number . $suffixes[$number % 10];
    }

    /**
     * Log settings changes for audit trail
     */
    private function logSettingsChange($action, $data)
    {
        try {
            // Get current settings to compare
            $currentSettings = LotterySetting::getSettings();
            
            // Define human-readable setting names
            $settingDisplayNames = [
                'is_active' => 'Lottery System Status',
                'ticket_price' => 'Ticket Price',
                'max_tickets_per_user' => 'Max Tickets Per User',
                'min_tickets_for_draw' => 'Minimum Tickets for Draw',
                'draw_day' => 'Draw Day',
                'draw_time' => 'Draw Time',
                'admin_commission_percentage' => 'Admin Commission',
                'auto_draw' => 'Auto Draw',
                'auto_prize_distribution' => 'Auto Prize Distribution',
                'auto_refund_cancelled' => 'Auto Refund Cancelled',
                'ticket_expiry_hours' => 'Ticket Expiration Hours',
                'auto_claim_days' => 'Auto Claim Days',
                'prize_claim_deadline' => 'Prize Claim Deadline',
                'allow_multiple_winners_per_place' => 'Multiple Winners Per Place',
                'prize_distribution_type' => 'Prize Distribution Type',
                'manual_winner_selection' => 'Manual Winner Selection',
                'show_virtual_tickets' => 'Virtual Ticket Display',
                'virtual_ticket_multiplier' => 'Virtual Ticket Multiplier',
                'virtual_ticket_base' => 'Virtual Ticket Base',
                'prize_structure' => 'Prize Structure'
            ];

            foreach ($data as $key => $newValue) {
                $oldValue = $currentSettings->{$key} ?? null;
                
                // Skip if values are the same
                if ($this->valuesAreEqual($oldValue, $newValue)) {
                    continue;
                }

                $settingName = $settingDisplayNames[$key] ?? ucfirst(str_replace('_', ' ', $key));
                
                // Determine change type
                $changeType = 'update';
                if (in_array($key, ['auto_draw', 'auto_prize_distribution', 'auto_refund_cancelled', 'allow_multiple_winners_per_place', 'manual_winner_selection', 'show_virtual_tickets'])) {
                    $changeType = $newValue ? 'enable' : 'disable';
                }

                ConfigurationChange::logChange(
                    $settingName,
                    $oldValue,
                    $newValue,
                    'lottery',
                    $changeType,
                    $action
                );
            }
        } catch (Exception $e) {
            Log::error('Failed to log settings change: ' . $e->getMessage());
        }
    }

    /**
     * Check if two values are equal (handling different data types)
     */
    private function valuesAreEqual($oldValue, $newValue)
    {
        // Handle boolean comparisons
        if (is_bool($oldValue) || is_bool($newValue)) {
            return (bool) $oldValue === (bool) $newValue;
        }

        // Handle numeric comparisons
        if (is_numeric($oldValue) && is_numeric($newValue)) {
            return abs((float) $oldValue - (float) $newValue) < 0.01;
        }

        // Handle array/object comparisons
        if ((is_array($oldValue) || is_object($oldValue)) && (is_array($newValue) || is_object($newValue))) {
            return json_encode($oldValue) === json_encode($newValue);
        }

        // String comparison
        return (string) $oldValue === (string) $newValue;
    }

    /**
     * Extract prize percentages/amounts from prize structure
     */
    private function extractPrizePercentages($prizeStructure, $distributionType = 'percentage')
    {
        if (!is_array($prizeStructure)) {
            return [100]; // Default single winner takes all
        }

        $values = [];
        foreach ($prizeStructure as $position => $prize) {
            if (is_array($prize)) {
                // Extract value based on the current distribution type
                if ($distributionType === 'fixed_amount') {
                    if (isset($prize['amount'])) {
                        $values[] = $prize['amount'];
                    } elseif (isset($prize['percentage'])) {
                        // If switching from percentage to fixed amount, provide a reasonable default
                        $values[] = $prize['percentage'] > 0 ? 100 : 0;
                    } else {
                        $values[] = 0;
                    }
                } else { // percentage mode
                    if (isset($prize['percentage'])) {
                        $values[] = $prize['percentage'];
                    } elseif (isset($prize['amount'])) {
                        // If switching from fixed amount to percentage, provide a reasonable default
                        $values[] = $prize['amount'] > 0 ? 50 : 0;
                    } else {
                        $values[] = 0;
                    }
                }
            } else {
                // Fallback if structure is different
                $values[] = is_numeric($prize) ? $prize : 0;
            }
        }

        return empty($values) ? [100] : $values;
    }

    /**
     * Show backup page
     */
    public function backup()
    {
        try {
            $backups = [];
            // Use the same path as in createBackup method
            $backupPath = storage_path('app/backups/lottery');
            
            // Create directory if it doesn't exist
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            if (file_exists($backupPath)) {
                $files = glob($backupPath . '/*.json');
                foreach ($files as $file) {
                    $backups[] = [
                        'name' => basename($file, '.json'),
                        'path' => $file,
                        'size' => filesize($file),
                        'created_at' => Carbon::createFromTimestamp(filemtime($file))
                    ];
                }
                
                // Sort backups by creation time (newest first)
                usort($backups, function($a, $b) {
                    return $b['created_at']->timestamp - $a['created_at']->timestamp;
                });
            }
            
            return view('admin.lottery-settings.backup', compact('backups'));
        } catch (Exception $e) {
            Log::error('Backup page error: ' . $e->getMessage());
            return redirect()->route('admin.lottery-settings.index')->with('error', 'Failed to load backup page.');
        }
    }
}
