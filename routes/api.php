<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Http\Controllers\Api\ServerTimeController;
// use App\Http\Controllers\Api\DebugController; // Commented out - controller doesn't exist
use App\Http\Controllers\Api\TicketValidationController;
use App\Http\Controllers\Api\ModalController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Check username availability
Route::post('/check-username', function (Request $request) {
    $request->validate([
        'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/'
    ]);
    
    $username = $request->username;
    $currentUserId = Auth::id();
    
    // Check if username exists (excluding current user if logged in)
    $exists = User::where('username', $username)
                  ->when($currentUserId, function ($query) use ($currentUserId) {
                      return $query->where('id', '!=', $currentUserId);
                  })
                  ->exists();
    
    return response()->json([
        'available' => !$exists,
        'username' => $username
    ]);
})->middleware('web');

// Check email availability
Route::post('/check-email', function (Request $request) {
    $request->validate([
        'email' => 'required|email|max:255'
    ]);
    
    $email = $request->email;
    $currentUserId = Auth::id();
    
    // Check if email exists (excluding current user if logged in)
    $exists = User::where('email', $email)
                  ->when($currentUserId, function ($query) use ($currentUserId) {
                      return $query->where('id', '!=', $currentUserId);
                  })
                  ->exists();
    
    // Check if it's the current user's email
    $isCurrent = false;
    if ($currentUserId) {
        $currentUser = User::find($currentUserId);
        $isCurrent = $currentUser && $currentUser->email === $email;
    }
    
    return response()->json([
        'available' => !$exists && !$isCurrent,
        'exists' => $exists,
        'is_current' => $isCurrent,
        'email' => $email
    ]);
})->middleware('web');

// Get user balance endpoint
Route::get('/user/balance', function (Request $request) {
    if (Auth::check()) {
        return response()->json([
            'success' => true,
            'balance' => Auth::user()->deposit_wallet+Auth::user()->interest_wallet ?? 0,
            'formatted' => number_format(Auth::user()->deposit_wallet+Auth::user()->interest_wallet ?? 0, 2)
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => 'Not authenticated'
        ], 401);
    }
})->middleware('web');

// Authentication check endpoint for mobile apps
Route::get('/auth/check', function (Request $request) {
    if (Auth::check()) {
        return response()->json([
            'authenticated' => true,
            'user' => Auth::user(),
            'status' => 'success'
        ]);
    }
    
    return response()->json([
        'authenticated' => false,
        'status' => 'unauthenticated'
    ], 401);
})->middleware('auth');

// User profile endpoint for mobile apps
Route::get('/user/profile', function (Request $request) {
    return response()->json([
        'user' => Auth::user(),
        'status' => 'success'
    ]);
})->middleware('auth');

// Dashboard data endpoint for mobile apps
Route::get('/user/dashboard', function (Request $request) {
    $user = Auth::user();
    return response()->json([
        'user' => $user,
        'balance' => $user->balance ?? 0,
        'status' => 'success'
    ]);
})->middleware('auth');

// Ticket validation endpoints
Route::middleware('auth')->group(function () {
    Route::post('/tickets/validate', [TicketValidationController::class, 'validateTicket']);
    Route::post('/tickets/mark-used', [TicketValidationController::class, 'markTicketAsUsed']);
    Route::get('/tickets/history', [TicketValidationController::class, 'getTicketHistory']);
});

// Server time endpoint for countdown
Route::get('/server-time', [ServerTimeController::class, 'now']);

// Modal API endpoints
Route::middleware('web')->group(function () {
    Route::post('/modal/session-update', [ModalController::class, 'updateModalSession']);
    Route::post('/modal/analytics', [ModalController::class, 'trackModalAnalytics']);
    Route::get('/modal/settings/{modalName}', [ModalController::class, 'getModalSettings']);
    Route::get('/modal/quick-stats', [ModalController::class, 'quickStats']);
});

// Admin modal management endpoints (add middleware as needed)
// Route::middleware(['auth'])->group(function () {
//     Route::put('/modal/settings/{modalName}', [ModalController::class, 'updateModalSettings']);
//     Route::post('/modal/bulk-action', [ModalController::class, 'bulkAction']);
// });

// Debug endpoints (can be removed in production)
// Route::get('/debug/video-limit', [DebugController::class, 'videoLimitStatus'])->middleware('auth'); // Commented out - controller doesn't exist

// Video API endpoints for mobile app
Route::middleware('auth')->group(function () {
    // Get user video stats
    Route::get('/user/video-stats', function (Request $request) {
        $user = Auth::user();
        
        // Get today's video views and earnings
        $todayViews = \App\Models\VideoView::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
            
        $todayEarnings = \App\Models\VideoView::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('earned_amount');
            
        $totalEarned = \App\Models\VideoView::where('user_id', $user->id)
            ->sum('earned_amount');
            
        // Get daily progress
        $dailyLimit = 10; // Default daily limit
        if ($user->activeInvestment) {
            $dailyLimit = $user->activeInvestment->plan->daily_video_limit ?? 10;
        }
        
        return response()->json([
            'success' => true,
            'today_views' => $todayViews,
            'today_earnings' => $todayEarnings,
            'total_earned' => $totalEarned,
            'daily_progress' => [
                'current' => $todayViews,
                'target' => $dailyLimit
            ]
        ]);
    });
    
    // Get available daily videos
    Route::get('/user/daily-videos', function (Request $request) {
        $user = Auth::user();
        
        try {
            // Get videos that user hasn't watched today
            $watchedToday = \App\Models\VideoView::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->pluck('video_id')
                ->toArray();
                
            // Get available videos (limit to 10 for daily viewing)
            $videos = \App\Models\VideoLink::where('status', 1)
                ->whereNotIn('id', $watchedToday)
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->map(function($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title ?: 'Video #' . $video->id,
                        'url' => $video->url,
                        'duration' => $video->duration ?? 180,
                        'earning_amount' => $video->earning_amount ?? 0.001,
                        'watched' => false
                    ];
                })
                ->values();
            
            return response()->json([
                'success' => true,
                'videos' => $videos,
                'message' => count($videos) > 0 ? 'Videos loaded successfully' : 'No new videos available today'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error loading daily videos: ' . $e->getMessage());
            
            // Fallback: Get any 10 random active videos
            $fallbackVideos = \App\Models\VideoLink::where('status', 1)
                ->inRandomOrder()
                ->limit(10)
                ->get()
                ->map(function($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title ?: 'Video #' . $video->id,
                        'url' => $video->url,
                        'duration' => $video->duration ?? 180,
                        'earning_amount' => $video->earning_amount ?? 0.001,
                        'watched' => false
                    ];
                });
            
            return response()->json([
                'success' => true,
                'videos' => $fallbackVideos,
                'message' => 'Videos loaded successfully (fallback mode)'
            ]);
        }
    });
});
