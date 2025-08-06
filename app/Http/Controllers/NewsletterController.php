<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        try {
            // Validate the email
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid email address.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = strtolower(trim($request->email));

            // Check if email already exists
            $existingSubscription = DB::table('newsletter_subscribers')
                ->where('email', $email)
                ->first();

            if ($existingSubscription) {
                if ($existingSubscription->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are already subscribed to our newsletter!',
                        'type' => 'info'
                    ]);
                } else {
                    // Reactivate subscription
                    DB::table('newsletter_subscribers')
                        ->where('email', $email)
                        ->update([
                            'is_active' => true,
                            'updated_at' => Carbon::now()
                        ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Welcome back! Your subscription has been reactivated.'
                    ]);
                }
            }

            // Create new subscription
            DB::table('newsletter_subscribers')->insert([
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'source' => 'public_gallery',
                'is_active' => true,
                'subscribed_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Log successful subscription
            Log::info('Newsletter subscription', [
                'email' => $email,
                'ip' => $request->ip(),
                'source' => 'public_gallery'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you! You\'ve been successfully subscribed to PayPerViews updates.'
            ]);

        } catch (\Exception $e) {
            // Log error
            Log::error('Newsletter subscription failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Subscription failed. Please try again later.'
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid email address.'
                ], 422);
            }

            $email = strtolower(trim($request->email));

            $updated = DB::table('newsletter_subscribers')
                ->where('email', $email)
                ->update([
                    'is_active' => false,
                    'unsubscribed_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            if ($updated) {
                Log::info('Newsletter unsubscription', ['email' => $email]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'You have been successfully unsubscribed.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found in our subscription list.'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Newsletter unsubscription failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unsubscription failed. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get newsletter statistics (admin only)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $stats = [
                'total_subscribers' => DB::table('newsletter_subscribers')->count(),
                'active_subscribers' => DB::table('newsletter_subscribers')->where('is_active', true)->count(),
                'inactive_subscribers' => DB::table('newsletter_subscribers')->where('is_active', false)->count(),
                'recent_subscriptions' => DB::table('newsletter_subscribers')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count(),
                'subscriptions_today' => DB::table('newsletter_subscribers')
                    ->whereDate('created_at', Carbon::today())
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter stats failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics.'
            ], 500);
        }
    }
}
