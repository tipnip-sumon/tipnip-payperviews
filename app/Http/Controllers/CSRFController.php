<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CSRFController extends Controller
{
    /**
     * Refresh CSRF token without creating multiple sessions
     */
    public function refreshToken(Request $request)
    {
        try {
            $sessionId = $request->session()->getId();
            
            // Log the session refresh attempt
            Log::info('CSRF token refresh requested', [
                'session_id' => $sessionId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Check if session already exists in database
            $existingSession = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();
            
            if (!$existingSession) {
                // If no session exists, this is a new session
                Log::info('Creating new session for CSRF refresh', ['session_id' => $sessionId]);
            } else {
                // Use existing session
                Log::info('Using existing session for CSRF refresh', ['session_id' => $sessionId]);
            }
            
            // Get current CSRF token
            $csrfToken = $request->session()->token();
            
            if (!$csrfToken) {
                // Regenerate token if none exists
                $request->session()->regenerateToken();
                $csrfToken = $request->session()->token();
                Log::info('Generated new CSRF token', ['session_id' => $sessionId]);
            }
            
            return response()->json([
                'csrf_token' => $csrfToken,
                'session_id' => $sessionId,
                'status' => 'success',
                'message' => 'CSRF token refreshed successfully',
                'timestamp' => now()->timestamp
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to refresh CSRF token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to refresh CSRF token',
                'message' => 'Please refresh the page and try again',
                'status' => 'error'
            ], 500);
        }
    }
    
    /**
     * Get current session info for debugging
     */
    public function sessionInfo(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }
        
        $sessionId = $request->session()->getId();
        
        $sessionData = DB::table('sessions')
            ->where('id', $sessionId)
            ->first();
        
        $totalSessions = DB::table('sessions')->count();
        
        return response()->json([
            'session_id' => $sessionId,
            'session_exists' => $sessionData ? true : false,
            'total_sessions' => $totalSessions,
            'csrf_token' => $request->session()->token(),
            'timestamp' => now()->timestamp,
            'session_data' => $sessionData
        ]);
    }
    
    /**
     * Clean up old/duplicate sessions
     */
    public function cleanupSessions(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }
        
        try {
            // Delete sessions older than 2 hours
            $deletedOld = DB::table('sessions')
                ->where('last_activity', '<', now()->subHours(2)->timestamp)
                ->delete();
            
            // Delete duplicate sessions for the same IP (keep most recent)
            $currentIp = $request->ip();
            $userSessions = DB::table('sessions')
                ->where('ip_address', $currentIp)
                ->orderBy('last_activity', 'desc')
                ->get();
            
            $deletedDuplicates = 0;
            if ($userSessions->count() > 1) {
                // Keep the most recent session, delete others
                $keepSession = $userSessions->first()->id;
                $deletedDuplicates = DB::table('sessions')
                    ->where('ip_address', $currentIp)
                    ->where('id', '!=', $keepSession)
                    ->delete();
            }
            
            return response()->json([
                'status' => 'success',
                'deleted_old' => $deletedOld,
                'deleted_duplicates' => $deletedDuplicates,
                'message' => 'Session cleanup completed'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Session cleanup failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Session cleanup failed'
            ], 500);
        }
    }
}
