<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendKycPendingReminderJob;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SendInactiveUserReminderJob;
use App\Jobs\SendMonthlyPasswordResetJob;

class EmailCampaignController extends Controller
{
    /**
     * Display email campaign dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = 'Email Campaign Management';
        
        // Get statistics
        $stats = [
            'kyc_pending' => User::where('kv', 0)->where('status', 1)->count(),
            'inactive_users' => User::where('status', 1)
                ->whereHas('deposits')
                ->whereDoesntHave('invests', function($query) {
                    $query->where('status', 1);
                })
                ->where(function($query) {
                    $query->where('last_login_at', '<', now()->subDays(15))
                          ->orWhereNull('last_login_at');
                })
                ->count(),
            'password_reset_due' => User::where('status', 1)
                ->where(function($query) {
                    $query->whereNull('password_changed_at')
                          ->orWhere('password_changed_at', '<', now()->subDays(30));
                })
                ->count(),
            'total_active_users' => User::where('status', 1)->count(),
            'queue_pending' => DB::table('jobs')->where('queue', 'emails')->count(),
            'queue_failed' => DB::table('failed_jobs')->count(),
        ];

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        }

        return view('admin.email-campaigns.index', compact('pageTitle', 'stats'));
    }

    /**
     * Display email campaign analytics
     */
    public function analytics()
    {
        $pageTitle = 'Email Campaign Analytics';
        
        return view('admin.email-campaigns.analytics', compact('pageTitle'));
    }

    /**
     * Display email templates
     */
    public function templates()
    {
        $pageTitle = 'Email Templates';
        
        // Get templates from database
        $templates = EmailTemplate::where('is_active', true)->get();
        
        return view('admin.email-campaigns.templates', compact('pageTitle', 'templates'));
    }

    /**
     * Update email template
     */
    public function updateTemplate(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $template = EmailTemplate::findOrFail($id);
        
        $template->update([
            'subject' => $request->subject,
            'content' => $request->content,
            'updated_by' => auth()->guard('admin')->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully!'
        ]);
    }

    /**
     * Get template content
     */
    public function getTemplate($slug)
    {
        $template = EmailTemplate::where('slug', $slug)->first();
        
        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Display queue management
     */
    public function queue()
    {
        $pageTitle = 'Queue Management';
        
        // Get queue statistics
        $queueStats = [
            'pending' => DB::table('jobs')->where('queue', 'emails')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'completed' => 0, // This would need to be tracked separately
        ];
        
        return view('admin.email-campaigns.queue', compact('pageTitle', 'queueStats'));
    }

    /**
     * Display email campaign settings
     */
    public function settings()
    {
        $pageTitle = 'Email Campaign Settings';
        
        return view('admin.email-campaigns.settings', compact('pageTitle'));
    }

    /**
     * Send KYC pending reminders
     */
    public function sendKycReminders(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:500'
        ]);

        try {
            $limit = $request->get('limit', 50);
            
            $users = User::where('kv', 0)
                ->where('status', 1)
                ->whereNotNull('email_verified_at')
                ->whereNotNull('email')
                ->limit($limit)
                ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found with pending KYC verification.'
                ]);
            }

            $count = 0;
            foreach ($users as $user) {
                SendKycPendingReminderJob::dispatchSync($user);
                $count++;
            }

            Log::info('KYC reminders queued by admin', [
                'admin_id' => auth()->guard('admin')->id(),
                'count' => $count
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully queued {$count} KYC reminder emails.",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue KYC reminders', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue KYC reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send monthly password resets
     */
    public function sendPasswordResets(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:200',
            'force' => 'boolean'
        ]);

        try {
            $limit = $request->get('limit', 50);
            $force = $request->get('force', false);
            
            $query = User::where('status', 1)
                ->whereNotNull('email_verified_at')
                ->whereNotNull('email');

            if (!$force) {
                $oneMonthAgo = Carbon::now()->subDays(30);
                $query->where(function($q) use ($oneMonthAgo) {
                    $q->whereNull('password_changed_at')
                      ->orWhere('password_changed_at', '<', $oneMonthAgo);
                });
            }

            $users = $query->limit($limit)->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found that need password reset.'
                ]);
            }

            $count = 0;
            foreach ($users as $user) {
                SendMonthlyPasswordResetJob::dispatchSync($user);
                $count++;
            }

            Log::info('Password resets queued by admin', [
                'admin_id' => auth()->guard('admin')->id(),
                'count' => $count,
                'force' => $force
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully queued {$count} password reset emails.",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue password resets', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue password resets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send inactive user reminders
     */
    public function sendInactiveReminders(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1|max:200',
            'days' => 'integer|min:1|max:365'
        ]);

        try {
            $limit = $request->get('limit', 50);
            $days = $request->get('days', 15);
            
            $inactiveDate = Carbon::now()->subDays($days);
            
            $users = User::where('status', 1)
                ->whereNotNull('email_verified_at')
                ->whereNotNull('email')
                ->whereHas('deposits')
                ->whereDoesntHave('invests', function($query) {
                    $query->where('status', 1);
                })
                ->where(function($query) use ($inactiveDate) {
                    $query->where('last_login_at', '<', $inactiveDate)
                          ->orWhereNull('last_login_at');
                })
                ->limit($limit)
                ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No inactive users found (inactive for {$days}+ days with deposits but no investments)."
                ]);
            }

            $count = 0;
            foreach ($users as $user) {
                SendInactiveUserReminderJob::dispatchSync($user);
                $count++;
            }

            Log::info('Inactive user reminders queued by admin', [
                'admin_id' => auth()->guard('admin')->id(),
                'count' => $count,
                'days' => $days
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully queued {$count} inactive user reminder emails.",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to queue inactive user reminders', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue inactive user reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run email commands manually
     */
    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|in:kyc-reminders,password-resets,inactive-reminders'
        ]);

        try {
            $command = $request->command;
            $output = '';

            switch ($command) {
                case 'kyc-reminders':
                    Artisan::call('email:kyc-pending-reminders', ['--limit' => 50]);
                    $output = Artisan::output();
                    break;
                    
                case 'password-resets':
                    Artisan::call('email:monthly-password-resets', ['--limit' => 50]);
                    $output = Artisan::output();
                    break;
                    
                case 'inactive-reminders':
                    Artisan::call('email:inactive-user-reminders', ['--days' => 15, '--limit' => 50]);
                    $output = Artisan::output();
                    break;
            }

            Log::info("Email command run by admin: {$command}", [
                'admin_id' => auth()->guard('admin')->id(),
                'output' => $output
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Command executed successfully.',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to run email command: {$command}", ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run command: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get campaign statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'kyc_pending' => User::where('kv', 0)->where('status', 1)->count(),
                'inactive_users' => User::where('status', 1)
                    ->whereHas('deposits')
                    ->whereDoesntHave('invests', function($query) {
                        $query->where('status', 1);
                    })
                    ->where(function($query) {
                        $query->where('last_login_at', '<', now()->subDays(15))
                              ->orWhereNull('last_login_at');
                    })
                    ->count(),
                'password_reset_due' => User::where('status', 1)
                    ->where(function($query) {
                        $query->whereNull('password_changed_at')
                              ->orWhere('password_changed_at', '<', now()->subDays(30));
                    })
                    ->count(),
                'queue_pending' => DB::table('jobs')->where('queue', 'emails')->count(),
                'queue_failed' => DB::table('failed_jobs')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics.'
            ], 500);
        }
    }

    /**
     * Get queue status
     */
    public function queueStatus()
    {
        try {
            $pending = DB::table('jobs')->where('queue', 'emails')->count();
            $failed = DB::table('failed_jobs')->count();

            return response()->json([
                'success' => true,
                'pending' => $pending,
                'failed' => $failed
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get queue status.'
            ], 500);
        }
    }

    /**
     * Retry failed jobs
     */
    public function retryFailed()
    {
        try {
            $failedCount = DB::table('failed_jobs')->count();
            
            if ($failedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No failed jobs to retry.'
                ]);
            }

            // Use Artisan command to retry failed jobs
            Artisan::call('queue:retry', ['id' => 'all']);
            $output = Artisan::output();

            Log::info('Failed jobs retried by admin', [
                'admin_id' => auth()->guard('admin')->id(),
                'failed_count' => $failedCount,
                'output' => $output
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully retried {$failedCount} failed jobs.",
                'count' => $failedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retry failed jobs', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear failed jobs
     */
    public function clearFailed()
    {
        try {
            $failedCount = DB::table('failed_jobs')->count();
            
            if ($failedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No failed jobs to clear.'
                ]);
            }

            // Clear all failed jobs
            DB::table('failed_jobs')->delete();

            Log::info('Failed jobs cleared by admin', [
                'admin_id' => auth()->guard('admin')->id(),
                'cleared_count' => $failedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully cleared {$failedCount} failed jobs.",
                'count' => $failedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear failed jobs', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear jobs: ' . $e->getMessage()
            ], 500);
        }
    }
}
