<?php

namespace App\Models;

use App\Models\GeneralSetting;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\SpecialLotteryTicket;
use App\Models\SpecialTicketTransfer;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'sponsor',
        'mobile',
        'country',
        'ref_by', // Referrer username
        'referral_hash', // Add referral hash
        'deposit_wallet',
        'interest_wallet',
        'ev', // Email verification status
        'sv', // SMS verification status
        'country',
        'balance',
        'status',
        'kv', // KYC verification status
        'email_verified_at', // Add this to fillable
        'identity_verified', // Identity verification status
        'identity_verified_at', // Identity verification timestamp
        'two_fa_status', // 2FA status
        'two_fa_enabled_at', // 2FA enabled timestamp
        'two_fa_secret', // 2FA secret key
        'two_fa_recovery_codes', // 2FA recovery codes
        'two_fa_verified_at', // 2FA verification timestamp
        'two_fa_forced', // Admin forced 2FA flag
        'two_fa_forced_at', // Admin forced 2FA timestamp
        'two_fa_force_expires_at', // 2FA force expiration
        'avatar', // Add this
        'last_login_at',
        'login_attempts',
        'locked_until',
        'last_login_ip',
        'last_login_user_agent',
        // Session tracking fields
        'current_session_id',
        'session_created_at',
        'session_ip_address',
        'session_user_agent',
        'last_activity_at',
        'notification_settings' // Add notification settings
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'identity_verified_at' => 'datetime',
        'two_fa_enabled_at' => 'datetime',
        'two_fa_verified_at' => 'datetime',
        'two_fa_forced_at' => 'datetime',
        'two_fa_force_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'session_created_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'password' => 'hashed',
        'notification_settings' => 'array', // Cast notification settings to array
    ];

          /**
     * Update the user's last login time and generate new session - ENHANCED for single session.
     */
    public function updateLastLogin()
    {
        try {
            // Generate new session ID to invalidate any previous sessions
            $sessionId = $this->generateNewSession();
            
            // Only update login tracking fields
            $this->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_login_user_agent' => request()->userAgent(),
                'login_attempts' => 0, // Reset login attempts on successful login
            ]);
            
            // Store session ID in the current session for validation
            session(['user_session_id' => $sessionId]);
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update last login time for user: ' . $this->id, [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
            ]);
            
            return false;
        }
    }

    /**
     * Get the user's last login time in a human-readable format.
     */
    public function getLastLoginHumanAttribute()
    {
        if ($this->last_login_at) {
            return $this->last_login_at->diffForHumans();
        }
        
        return 'Never';
    }

    /**
     * Get the user's last login time formatted.
     */
    public function getLastLoginFormattedAttribute()
    {
        if ($this->last_login_at) {
            return $this->last_login_at->format('M d, Y h:i A');
        }
        
        return 'Never';
    }

    /**
     * Check if user has 2FA enabled
     */
    public function has2FAEnabled()
    {
        return $this->two_fa_status == 1;
    }

    /**
     * Check if user is forced to use 2FA by admin
     */
    public function is2FAForced()
    {
        return $this->two_fa_forced == true && 
               $this->two_fa_force_expires_at && 
               $this->two_fa_force_expires_at->isFuture();
    }

    /**
     * Check if user needs to set up 2FA (forced but not configured)
     */
    public function needs2FASetup()
    {
        return $this->is2FAForced() && !$this->two_fa_secret;
    }

    /**
     * Get 2FA status text
     */
    public function get2FAStatusText()
    {
        if ($this->has2FAEnabled()) {
            if ($this->is2FAForced()) {
                return 'Enforced by Admin';
            }
            return 'Enabled';
        }
        
        if ($this->is2FAForced()) {
            return 'Setup Required';
        }
        
        return 'Disabled';
    }


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }
    public function videoLinks()
    {
        return $this->hasMany(VideoLink::class)->orderBy('id','desc');
    }
    public function adminNotifications()
    {
        return $this->hasMany(AdminNotification::class)->orderBy('id','desc');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class,'ref_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class,'ref_by');
    }

    public function allReferrals(){
        return $this->referrals()->with('referrer');
    }

    public function invests()
    {
        return $this->hasMany(Invest::class)->orderBy('id','desc');
    }

    /**
     * Get user's active investments
     */
    public function activeInvestments()
    {
        return $this->hasMany(Invest::class)->where('invests.status', 1)->with('plan');
    }

    /**
     * Check if user has any active investments
     */
    public function hasActiveInvestment()
    {
        return $this->activeInvestments()->exists();
    }

    /**
     * Get user's highest plan from active investments
     */
    public function getHighestActivePlan()
    {
        return $this->activeInvestments()
            ->join('plans', 'invests.plan_id', '=', 'plans.id')
            ->orderBy('plans.daily_video_limit', 'desc')
            ->orderBy('plans.video_earning_rate', 'desc')
            ->first()?->plan;
    }

    /**
     * Get daily video limit based on user's active investments
     */
    public function getDailyVideoLimit()
    {
        $highestPlan = $this->getHighestActivePlan();
        return $highestPlan ? $highestPlan->daily_video_limit : 0;
    }

    /**
     * Check if user can access videos
     */
    public function canAccessVideos()
    {
        if (!$this->hasActiveInvestment()) {
            return false;
        }
        
        $highestPlan = $this->getHighestActivePlan();
        return $highestPlan && $highestPlan->video_access_enabled;
    }

    /**
     * Get today's video view count
     */
    public function getTodaysVideoViewCount()
    {
        return \App\Models\VideoView::where('user_id', $this->id)
            ->whereDate('viewed_at', today())
            ->count();
    }

    /**
     * Check if user has reached daily video limit
     */
    public function hasReachedDailyVideoLimit()
    {
        $dailyLimit = $this->getDailyVideoLimit();
        $todaysViews = $this->getTodaysVideoViewCount();
        
        return $todaysViews >= $dailyLimit;
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    /**
     * Get the user's avatar URL with fallback to default image.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Check if it's a full URL or relative path
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            
            // Check if it's stored in storage/app/public
            if (file_exists(storage_path('app/public/' . $this->avatar))) {
                return asset('storage/' . $this->avatar);
            }
            
            // Check if it's in public directory
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }
        }
        
        return asset('assets/images/users/16.jpg');
    }

    /**
     * Get the user's avatar path for storage operations.
     */
    public function getAvatarPathAttribute()
    {
        return $this->avatar ?: 'assets/images/users/16.jpg';
    }

    /**
     * Support ticket relationships
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'from_user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'to_user_id');
    }

    /**
     * Get all messages (sent and received) for this user
     */
    public function allMessages()
    {
        return Message::where('from_user_id', $this->id)
                     ->orWhere('to_user_id', $this->id);
    }

    /**
     * Get unread messages count for this user
     */
    public function getUnreadMessagesCountAttribute()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    public function supportTickets()
    {
        return $this->hasMany(Message::class, 'from_user_id')
                    ->whereNull('reply_to_id')
                    ->where('message_type', 'support');
    }

    public function adminConversations()
    {
        return $this->hasMany(Message::class, 'from_user_id')
                    ->whereHas('recipient', function($query) {
                        $query->where('email', 'like', '%admin%')
                              ->orWhere('username', 'like', '%admin%')
                              ->orWhere('id', 1);
                    });
    }

    /**
     * Admin connection methods
     */
    public function isAdmin()
    {
        // Check if user is in admin table
        $adminExists = Admin::where('email', $this->email)->exists();
        if ($adminExists) {
            return true;
        }

        // Check if user has admin-like characteristics
        return $this->id == 1 || 
               str_contains(strtolower($this->email), 'admin') || 
               str_contains(strtolower($this->username), 'admin');
    }

    public function isSuperAdmin()
    {
        // Check if user is super admin in admin table
        $admin = Admin::where('email', $this->email)->first();
        if ($admin) {
            return $admin->is_super_admin == 1;
        }

        // Fallback: check if it's the first user
        return $this->id == 1;
    }

    public function getAdminPriority()
    {
        if ($this->isSuperAdmin()) {
            return 1; // Highest priority
        }
        
        if ($this->isAdmin()) {
            return 2; // Medium priority
        }
        
        return 3; // Regular user - lowest priority
    }

    public function getAssignedAdmin()
    {
        // Find the best admin to assign this user to
        return static::getAvailableAdmins()->first();
    }

    /**
     * Static methods for admin management
     */
    public static function getAvailableAdmins()
    {
        // First, get all admin emails from the Admin table
        $adminEmails = Admin::pluck('email')->toArray();
        
        return static::where(function($query) use ($adminEmails) {
            // Include users whose emails are in the Admin table
            if (!empty($adminEmails)) {
                $query->whereIn('email', $adminEmails);
            }
            
            // Also include users with admin-like indicators
            $query->orWhere('email', 'like', '%admin%')
                  ->orWhere('username', 'like', '%admin%')
                  ->orWhere('id', 1);
        })
        ->where('status', 1) // Active users only
        ->orderByRaw("
            CASE 
                WHEN id = 1 THEN 1
                WHEN email LIKE '%superadmin%' THEN 2
                WHEN username LIKE '%superadmin%' THEN 2
                WHEN email LIKE '%admin%' THEN 3
                WHEN username LIKE '%admin%' THEN 3
                ELSE 4
            END
        ")
        ->get();
    }

    public static function getBestAdminForSupport()
    {
        $admins = static::getAvailableAdmins();
        
        if ($admins->isEmpty()) {
            // Fallback: try to find any user that has an Admin table entry
            $adminEmails = Admin::pluck('email')->toArray();
            if (!empty($adminEmails)) {
                $admin = static::whereIn('email', $adminEmails)->where('status', 1)->first();
                if ($admin) {
                    return $admin;
                }
            }
            
            // Last fallback to first user if no admins found
            return static::where('id', 1)->first();
        }
        
        return $admins->first();
    }

    public function canReceiveSupportTickets()
    {
        return $this->isAdmin() && $this->status == 1;
    }

    public function canSendSupportTickets()
    {
        return $this->status == 1; // All active users can send tickets
    }

    /**
     * Support ticket statistics
     */
    public function getUnreadSupportTicketsCount()
    {
        if (!$this->isAdmin()) {
            return 0;
        }

        return $this->receivedMessages()
                    ->where('message_type', 'support')
                    ->where('is_read', false)
                    ->count();
    }

    public function getTotalSupportTicketsCount()
    {
        if ($this->isAdmin()) {
            return $this->receivedMessages()
                        ->where('message_type', 'support')
                        ->count();
        }
        
        return $this->sentMessages()
                    ->where('message_type', 'support')
                    ->count();
    }

    public function getActiveSupportTicketsCount()
    {
        if ($this->isAdmin()) {
            return $this->receivedMessages()
                        ->where('message_type', 'support')
                        ->whereIn('status', ['open', 'pending'])
                        ->count();
        }
        
        return $this->sentMessages()
                    ->where('message_type', 'support')
                    ->whereIn('status', ['open', 'pending'])
                    ->count();
    }

    /**
     * Get unified tickets statistics for lottery system
     */
    public function getTicketsStatistics()
    {
        $lotteryTicketsCount = 0;
        $specialTokensCount = 0;
        $sponsorTicketsCount = 0;

        // Get lottery tickets count (regular lottery tickets)
        if (method_exists($this, 'lotteryTickets')) {
            $lotteryTicketsCount = $this->lotteryTickets()
                ->where('token_type', 'lottery')
                ->count();
        }

        // Get special tokens count (active tickets that can be used as tokens)
        if (method_exists($this, 'lotteryTickets')) {
            $specialTokensCount = $this->lotteryTickets()
                ->where('token_type', 'special')
                ->where('status', 'active')
                ->where('is_valid_token', true)
                ->whereNull('used_as_token_at')
                ->count();
        }

        // For sponsor tickets, count tickets where user is the sponsor in unified table
        if (method_exists($this, 'lotteryTickets')) {
            $sponsorTicketsCount = $this->lotteryTickets()
                ->where('token_type', 'special')
                ->where('sponsor_user_id', $this->id)
                ->where('status', 'active')
                ->count();
        }

        return [
            'lottery' => $lotteryTicketsCount,
            'special' => $specialTokensCount,
            'sponsor' => $sponsorTicketsCount,
            'total' => $lotteryTicketsCount + $specialTokensCount + $sponsorTicketsCount
        ];
    }

    /**
     * Generate a unique referral hash for the user.
     */
    public function generateReferralHash()
    {
        do {
            $hash = bin2hex(random_bytes(16)); // Generate 32 character hash
        } while (User::where('referral_hash', $hash)->exists());
        
        $this->referral_hash = $hash;
        $this->save();
        
        return $hash;
    }

    /**
     * Get the user's referral hash, generate if not exists.
     */
    public function getReferralHash()
    {
        if (!$this->referral_hash) {
            return $this->generateReferralHash();
        }
        
        return $this->referral_hash;
    }

    /**
     * Get the user's referral link.
     */
    public function getReferralLink()
    {
        return url('/register?ref=' . $this->getReferralHash());
    }

    /**
     * Find user by referral hash.
     */
    public static function findByReferralHash($hash)
    {
        // First try to find by referral_hash field
        $user = static::where('referral_hash', $hash)->first();
        
        if ($user) {
            return $user;
        }
        
        // If not found by hash, try by username as fallback
        return static::where('username', $hash)->first();
    }

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1)->where('ev', '1')->where('sv', '1');
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', 0);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', 0);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', 2);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithBalance()
    {
        return $this->where(function($userWallet){
            $userWallet->where('deposit_wallet', '>' , 0)->orWhere('interest_wallet', '>', 0);
        });
    }

    public function videoViews()
    {
        return $this->hasMany(VideoView::class);
    }

    public function getTotalVideoEarningsAttribute()
    {
        return $this->videoViews()->sum('earned_amount');
    }
    /**
     * Lock the user account for a specified number of minutes.
     *
     * @param int $minutes
     */
    public function lockAccount($minutes = 10)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }
    
    /**
     * Check if account is locked
     */
    public function isLocked()
    {
        // If there's no lock time set, account is not locked
        if (!$this->locked_until) {
            return false;
        }
        
        // If lock time has passed, automatically unlock and return false
        if ($this->locked_until->isPast()) {
            $this->update([
                'locked_until' => null,
                'login_attempts' => 0
            ]);
            return false;
        }
        
        // Lock is still active
        return true;
    }

    public function updateLoginAttempts()
    {
        try {
            $this->update([
                'last_login_ip' => request()->ip(),
                'last_login_user_agent' => request()->userAgent(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update last login time for user: ' . $this->id, [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
            ]);
            
            return false;
        }
    }
       /**
     * Increment failed login attempts
     */
    public function incrementLoginAttempts()
    {
        $this->updateLoginAttempts();
        $this->increment('login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    /**
     * Check if user has ever made a deposit (Plan 1)
     */
    public function hasInvestedInPlanOne()
    {
        return $this->invests()->where('plan_id', 1)->exists();
    }

    /**
     * Get the current plan ID the user has deposited for
     */
    public function getHighestInvestedPlanId()
    {
        return $this->invests()->max('plan_id') ?? 0;
    }

    /**
     * Get current deposit record
     */
    public function getCurrentDeposit()
    {
        return $this->invests()->where('status', 1)->first();
    }

    /**
     * Get eligible plans for the user based on their deposit history
     */
    public function getEligiblePlans()
    {
        $allPlans = \App\Models\Plan::where('status', 1)->orderBy('id')->get();
        
        // If user has never made a deposit, only show Plan 1
        // if ($this->hasInvestedInPlanOne()) {
        //     return $allPlans->where('id', 1);
        // }
        
        // If user has active deposit, show higher tier plans only
        $currentDeposit = $this->getCurrentDeposit();
        if ($currentDeposit) {
            return $allPlans->where('id', '>', $currentDeposit->plan_id);
        }
        
        // If no active deposit, allow any plan higher than previous
        $highestPlanId = $this->getHighestInvestedPlanId();
        return $allPlans->where('id', '>', $highestPlanId);
    }

    /**
     * Check if user can make deposit for a specific plan
     */
    public function canInvestInPlan($planId)
    {
        $eligiblePlans = $this->getEligiblePlans();
        return $eligiblePlans->contains('id', $planId);
    }

    /**
     * Get the user's KYC submissions.
     */
    // public function kycSubmissions()
    // {
    //     return $this->hasMany(KycSubmission::class);
    // }

    /**
     * Send the email verification notification.
     * Custom implementation to use our mail configuration.
     */
    // public function sendEmailVerificationNotification()
    // {
    //     GeneralSetting::refreshMailConfiguration();
    //     $this->notify(new VerifyEmailNotification);
    // }



    /**
     * Generate and store a new session ID for the user.
     */
    public function generateNewSession()
    {
        $sessionId = bin2hex(random_bytes(32));
        
        $this->update([
            'current_session_id' => $sessionId,
            'session_created_at' => now(),
            'session_ip_address' => request()->ip(),
            'session_user_agent' => request()->userAgent(),
            'last_activity_at' => now(),
        ]);
        
        return $sessionId;
    }

    /**
     * Invalidate the current session.
     */
    public function invalidateSession()
    {
        $this->update([
            'current_session_id' => null,
            'session_created_at' => null,
            'session_ip_address' => null,
            'session_user_agent' => null,
            'last_activity_at' => null,
        ]);
    }

    /**
     * Check if the current session is valid.
     */
    public function isSessionValid($sessionId)
    {
        return $this->current_session_id && $this->current_session_id === $sessionId;
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity()
    {
        if ($this->current_session_id) {
            $this->update([
                'last_activity_at' => now(),
            ]);
        }
    }

    /**
     * Check if session has expired (optional - could add session timeout).
     */
    public function isSessionExpired($timeoutMinutes = 1440) // 24 hours default
    {
        if (!$this->last_activity_at) {
            return true;
        }
        
        return $this->last_activity_at->diffInMinutes(now()) > $timeoutMinutes;
    }

    /**
     * Lottery system relationships
     */
    public function lotteryTickets()
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function lotteryWinners()
    {
        return $this->hasMany(LotteryWinner::class);
    }

    public function lotteryDailySummaries()
    {
        return $this->hasMany(LotteryDailySummary::class);
    }

    /**
     * Get user's active lottery tickets count
     */
    public function getActiveLotteryTicketsCount()
    {
        $currentDraw = LotteryDraw::getCurrentDraw();
        if (!$currentDraw) {
            return 0;
        }
        
        return $this->lotteryTickets()
                    ->where('lottery_draw_id', $currentDraw->id)
                    ->count();
    }

    /**
     * Get user's total lottery winnings
     */
    public function getTotalLotteryWinnings()
    {
        return $this->lotteryWinners()
                    ->where('claim_status', 'claimed')
                    ->sum('prize_amount');
    }

    /**
     * Get user's pending lottery winnings
     */
    public function getPendingLotteryWinnings()
    {
        return $this->lotteryWinners()
                    ->where('claim_status', 'pending')
                    ->sum('prize_amount');
    }

    /**
     * Check if user has unclaimed lottery prizes
     */
    public function hasUnclaimedLotteryPrizes()
    {
        return $this->lotteryWinners()
                    ->where('claim_status', 'pending')
                    ->exists();
    }

    /**
     * Special ticket system relationships - Updated to use unified lottery_tickets table
     */
    public function specialTickets()
    {
        return $this->hasMany(LotteryTicket::class, 'user_id')->where('token_type', 'special');
    }

    public function sentTransfers()
    {
        return $this->hasMany(SpecialTicketTransfer::class, 'from_user_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(SpecialTicketTransfer::class, 'to_user_id');
    }

    /**
     * Get user's available special tickets count
     */
    public function getAvailableSpecialTicketsCount()
    {
        return $this->lotteryTickets()
                    ->where('token_type', 'special')
                    ->where('status', 'active')
                    ->whereNull('used_as_token_at')
                    ->count();
    }

    /**
     * Alias for getAvailableSpecialTicketsCount() for backward compatibility
     */
    public function getAvailableSpecialTokensCount()
    {
        return $this->getAvailableSpecialTicketsCount();
    }

    /**
     * Alias for specialTickets() method for backward compatibility
     */
    public function specialTokens()
    {
        return $this->lotteryTickets()->where('token_type', 'special');
    }

    /**
     * Get all tickets (lottery + special) for unified view
     */
    public function allTickets()
    {
        return $this->lotteryTickets()->whereIn('token_type', ['special', 'sponsor', 'lottery']);
    }

    /**
     * Get user's completed transfers count
     */
    public function getCompletedTransfersCount()
    {
        $sent = $this->sentTransfers()->where('status', 'completed')->count();
        $received = $this->receivedTransfers()->where('status', 'completed')->count();
        
        return $sent + $received;
    }

    /**
     * Get default notification settings
     */
    public function getDefaultNotificationSettings()
    {
        return [
            'email_notifications' => true,
            'sms_notifications' => true,
            'browser_notifications' => true,
            'marketing_notifications' => false,
            'transaction_notifications' => true,
            'security_notifications' => true,
            'lottery_notifications' => true,
            'referral_notifications' => true,
            'maintenance_notifications' => true,
            'system_notifications' => true,
        ];
    }

    /**
     * Get notification settings with defaults
     */
    public function getNotificationSettings()
    {
        $defaults = $this->getDefaultNotificationSettings();
        $userSettings = $this->notification_settings ?? [];
        
        return array_merge($defaults, $userSettings);
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(array $settings)
    {
        $currentSettings = $this->getNotificationSettings();
        $newSettings = array_merge($currentSettings, $settings);
        
        $this->update(['notification_settings' => $newSettings]);
        
        return $newSettings;
    }

    /**
     * Check if user has specific notification enabled
     */
    public function hasNotificationEnabled($type)
    {
        $settings = $this->getNotificationSettings();
        return $settings[$type] ?? true;
    }

    /**
     * Check if user wants email notifications
     */
    public function wantsEmailNotifications()
    {
        return $this->hasNotificationEnabled('email_notifications');
    }

    /**
     * Check if user wants SMS notifications
     */
    public function wantsSmsNotifications()
    {
        return $this->hasNotificationEnabled('sms_notifications');
    }

    /**
     * Check if user wants browser notifications
     */
    public function wantsBrowserNotifications()
    {
        return $this->hasNotificationEnabled('browser_notifications');
    }

    /**
     * Check if user wants marketing notifications
     */
    public function wantsMarketingNotifications()
    {
        return $this->hasNotificationEnabled('marketing_notifications');
    }
}
