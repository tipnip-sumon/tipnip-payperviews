<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    /**
     * Test sponsor validation endpoint
     */
    public function testSponsorValidation(Request $request)
    {
        try {
            Log::info('Test sponsor validation called', ['input' => $request->all()]);
            
            $sponsor = $request->get('sponsor', 'test');
            
            // Test the same logic as in RegisterController
            $sponsorUser = User::findByReferralHash($sponsor);
            
            if (!$sponsorUser) {
                $sponsorUser = User::where('username', $sponsor)->first();
            }
            
            $result = [
                'sponsor_input' => $sponsor,
                'found_by_hash' => User::where('referral_hash', $sponsor)->first() ? true : false,
                'found_by_username' => User::where('username', $sponsor)->first() ? true : false,
                'final_result' => $sponsorUser ? [
                    'id' => $sponsorUser->id,
                    'username' => $sponsorUser->username,
                    'status' => $sponsorUser->status,
                    'referral_hash' => $sponsorUser->referral_hash ?? 'null'
                ] : null,
                'validation_result' => $sponsorUser && $sponsorUser->status == 1 ? 'valid' : 'invalid'
            ];
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Test sponsor validation error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Test database connection and user table
     */
    public function testDatabase()
    {
        try {
            $userCount = User::count();
            $sampleUsers = User::select('id', 'username', 'referral_hash', 'status')
                              ->limit(5)
                              ->get();
            
            return response()->json([
                'status' => 'success',
                'total_users' => $userCount,
                'sample_users' => $sampleUsers,
                'database_connection' => 'working'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'database_connection' => 'failed'
            ], 500);
        }
    }
}
