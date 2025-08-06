<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLevelSetting;
use App\Services\ReferralDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CommissionLevelController extends Controller
{
    protected $referralService;

    public function __construct(ReferralDistributionService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display commission level settings
     */
    public function index()
    {
        $commissionLevels = CommissionLevelSetting::orderBy('level')->get();
        $totalPercentage = CommissionLevelSetting::getTotalActivePercentage();
        
        return view('admin.commission-levels.index', compact('commissionLevels', 'totalPercentage'));
    }

    /**
     * Show form for creating new commission level
     */
    public function create() 
    {
        $nextLevel = CommissionLevelSetting::max('level') + 1;
        $commissionLevels = CommissionLevelSetting::orderBy('level')->get();

        return view('admin.commission-levels.create', compact('nextLevel','commissionLevels'));
    }

    /**
     * Store new commission level
     */
    public function store(Request $request)
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:20|unique:commission_level_settings,level',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if adding this percentage would exceed the cap
        $currentTotal = CommissionLevelSetting::getTotalActivePercentage();
        $newTotal = $currentTotal + ($request->is_active ? $request->percentage : 0);

        if ($newTotal > 100.0) {
            return back()->withErrors([
                'percentage' => "Adding this percentage would exceed the 100% cap. Current total: {$currentTotal}%"
            ])->withInput();
        }

        CommissionLevelSetting::createCommissionLevel($request->all());

        return redirect()->route('admin.commission-levels.index')
            ->with('success', 'Commission level created successfully.');
    }

    /**
     * Show form for editing commission level
     */
    public function edit(CommissionLevelSetting $commissionLevel)
    {
        return view('admin.commission-levels.edit', compact('commissionLevel'));
    }

    /**
     * Update commission level
     */
    public function update(Request $request, CommissionLevelSetting $commissionLevel)
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:20|unique:commission_level_settings,level,' . $commissionLevel->id,
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if updating this percentage would exceed the cap
        $currentTotal = CommissionLevelSetting::where('id', '!=', $commissionLevel->id)
            ->where('is_active', true)
            ->sum('percentage');
        $newTotal = $currentTotal + ($request->is_active ? $request->percentage : 0);
        
        if ($newTotal > 100.0) {
            return back()->withErrors([
                'percentage' => "Updating this percentage would exceed the 10% cap. Current total (excluding this level): {$currentTotal}%"
            ])->withInput();
        }

        $commissionLevel->update($request->all());

        return redirect()->route('admin.commission-levels.index')
            ->with('success', 'Commission level updated successfully.');
    }

    /**
     * Delete commission level
     */
    public function destroy(CommissionLevelSetting $commissionLevel)
    {
        // Check if this level has any commission records
        $hasCommissions = DB::table('referral_commissions')
            ->where('level', $commissionLevel->level)
            ->exists();

        if ($hasCommissions) {
            return back()->withErrors([
                'delete' => 'Cannot delete commission level that has existing commission records.'
            ]);
        }

        $commissionLevel->delete();

        return redirect()->route('admin.commission-levels.index')
            ->with('success', 'Commission level deleted successfully.');
    }

    /**
     * Toggle active status of commission level
     */
    public function toggleActive(CommissionLevelSetting $commissionLevel)
    {
        $newStatus = !$commissionLevel->is_active;
        
        // If activating, check if it would exceed the cap
        if ($newStatus) {
            $currentTotal = CommissionLevelSetting::where('id', '!=', $commissionLevel->id)
                ->where('is_active', true)
                ->sum('percentage');
            $newTotal = $currentTotal + $commissionLevel->percentage;
            
            if ($newTotal > 10.0) {
                return back()->withErrors([
                    'toggle' => "Activating this level would exceed the 10% cap. Current total: {$currentTotal}%"
                ]);
            }
        }

        $commissionLevel->update(['is_active' => $newStatus]);

        $status = $newStatus ? 'activated' : 'deactivated';
        return redirect()->route('admin.commission-levels.index')
            ->with('success', "Commission level {$status} successfully.");
    }

    /**
     * Reset to default commission levels
     */
    public function resetToDefaults()
    {
        try {
            DB::beginTransaction();
            
            // Clear existing settings
            CommissionLevelSetting::truncate();
            
            // Initialize defaults
            CommissionLevelSetting::initializeDefaults();
            
            DB::commit();
            
            return redirect()->route('admin.commission-levels.index')
                ->with('success', 'Commission levels reset to defaults successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset commission levels: ' . $e->getMessage());
            
            return back()->withErrors([
                'reset' => 'Failed to reset commission levels. Please try again.'
            ]);
        }
    }

    /**
     * Bulk update commission levels
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'levels' => 'required|array',
            'levels.*.id' => 'required|exists:commission_level_settings,id',
            'levels.*.percentage' => 'required|numeric|min:0|max:100',
            'levels.*.is_active' => 'boolean',
        ]);

        // Calculate total percentage for active levels
        $totalPercentage = 0;
        foreach ($request->levels as $levelData) {
            if (isset($levelData['is_active']) && $levelData['is_active']) {
                $totalPercentage += $levelData['percentage'];
            }
        }

        if ($totalPercentage > 10.0) {
            return back()->withErrors([
                'bulk' => "Total active commission percentage cannot exceed 10%. Current total: {$totalPercentage}%"
            ])->withInput();
        }

        try {
            DB::beginTransaction();
            
            foreach ($request->levels as $levelData) {
                CommissionLevelSetting::where('id', $levelData['id'])
                    ->update([
                        'percentage' => $levelData['percentage'],
                        'is_active' => isset($levelData['is_active']) ? $levelData['is_active'] : false
                    ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.commission-levels.index')
                ->with('success', 'Commission levels updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk update commission levels: ' . $e->getMessage());
            
            return back()->withErrors([
                'bulk' => 'Failed to update commission levels. Please try again.'
            ]);
        }
    }
}
