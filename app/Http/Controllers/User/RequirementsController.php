<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirementsController extends Controller
{
    /**
     * Display the account requirements page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load condition helper
        if (!function_exists('getRequirementsSummary')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $summary = getRequirementsSummary($user);
        
        $data = [
            'pageTitle' => 'Account Requirements',
            'summary' => $summary,
            'user' => $user
        ];
        
        return view('frontend.account-requirements', $data);
    }

    /**
     * Get requirements summary via AJAX
     */
    public function getRequirementsSummary()
    {
        $user = Auth::user();
        
        // Load condition helper
        if (!function_exists('getRequirementsSummary')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $summary = getRequirementsSummary($user);
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}
