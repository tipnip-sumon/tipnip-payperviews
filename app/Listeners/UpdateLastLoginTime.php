<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class UpdateLastLoginTime
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            
            if ($user) {
                // Update last login time
                $user->updateLastLogin();
                
                // Log the login event (optional)
                Log::info('User login time updated', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'last_login_at' => $user->last_login_at,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            // Log any errors but don't break the login process
            Log::error('Failed to update last login time', [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
