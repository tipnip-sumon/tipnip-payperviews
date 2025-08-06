<?php

use App\Console\Commands\LotteryDrawCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;


Schedule::command('cronJob')
    ->everyMinute();

Schedule::command('app:payment-status')
    ->everySecond();

Schedule::command('app:daily-video-assignments')
    ->dailyAt('00:00')
    ->timezone('Asia/Dhaka')
    ->onSuccess(function () {
        Log::info('Daily video assignments successfully assigned.');
    })
    ->onFailure(function () {
        Log::error('Failed to assign daily video assignments.');
    });

// Daily commission distribution - runs 30 minutes after video assignments
Schedule::command('commissions:distribute-daily')
    ->dailyAt('00:30')
    ->timezone('Asia/Dhaka')
    ->onSuccess(function () {
        Log::info('Daily commission distribution completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Daily commission distribution failed.');
    });

// Clean up old video assignments weekly
Schedule::command('video:cleanup-assignments --days=30')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Asia/Dhaka')
    ->onSuccess(function () {
        Log::info('Video assignments cleanup completed.');
    });

// Weekly lottery draw - runs every Sunday at 8 PM Bangladesh time  
Schedule::command('lottery:draw')
    ->weekly()
    ->sundays()
    ->at('20:00')
    ->timezone('Asia/Dhaka')
    ->onSuccess(function () {
        Log::info('Weekly lottery draw completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Weekly lottery draw failed.');
    });

// Auto lottery process - runs every 5 minutes
Schedule::command('lottery:auto-process')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/auto-lottery.log'))
    ->onSuccess(function () {
        Log::info('Auto lottery process completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Auto lottery process failed.');
    });

// Lottery data optimization - runs daily at 3:00 AM to clean virtual tickets
Schedule::command('lottery:optimize --days=7 --force')
    ->dailyAt('03:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/lottery-optimization.log'))
    ->onSuccess(function () {
        Log::info('Lottery data optimization completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Lottery data optimization failed.');
    });

// Daily lottery summaries creation - runs daily at 1:00 AM
Schedule::command('lottery:optimize --summaries --force')
    ->dailyAt('01:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/lottery-summaries.log'))
    ->onSuccess(function () {
        Log::info('Daily lottery summaries created successfully.');
    })
    ->onFailure(function () {
        Log::error('Daily lottery summaries creation failed.');
    });

// Clean up old session notifications daily at 2:00 AM
Schedule::command('notifications:cleanup --days=30 --force')
    ->dailyAt('02:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/notifications-cleanup.log'))
    ->onSuccess(function () {
        Log::info('Session notifications cleanup completed.');
    })
    ->onFailure(function () {
        Log::error('Session notifications cleanup failed.');
    });

// Clean up old lottery summaries weekly on Sunday at 4:00 AM
Schedule::command('lottery:delete-summaries --days=180 --duplicates --force')
    ->weeklyOn(0, '04:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/lottery-summaries-cleanup.log'))
    ->onSuccess(function () {
        Log::info('Lottery summaries cleanup completed.');
    })
    ->onFailure(function () {
        Log::error('Lottery summaries cleanup failed.');
    });

// Additional cleanup for very old notifications (90+ days) weekly
Schedule::command('notifications:cleanup --days=90 --force')
    ->weeklyOn(0, '03:00') // Sunday at 3:00 AM
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/notifications-cleanup.log'))
    ->onSuccess(function () {
        Log::info('Old notifications cleanup completed.');
    })
    ->onFailure(function () {
        Log::error('Old notifications cleanup failed.');
    });

/*
|--------------------------------------------------------------------------
| Automated Email Notifications
|--------------------------------------------------------------------------
| These scheduled commands handle automated email notifications for user 
| engagement, security, and compliance purposes using Laravel Queue system.
*/

// KYC Pending Reminders - Daily at 10:00 AM
Schedule::command('email:kyc-pending-reminders --limit=100')
    ->dailyAt('10:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/kyc-reminders.log'))
    ->onSuccess(function () {
        Log::info('KYC pending reminders sent successfully.');
    })
    ->onFailure(function () {
        Log::error('KYC pending reminders failed.');
    });

// Monthly Password Resets - First day of every month at 6:00 AM
Schedule::command('email:monthly-password-resets --limit=200')
    ->monthlyOn(1, '06:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/monthly-password-resets.log'))
    ->onSuccess(function () {
        Log::info('Monthly password resets completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Monthly password resets failed.');
    });

// Inactive User Reminders - Every 3 days at 2:00 PM
Schedule::command('email:inactive-user-reminders --days=15 --limit=50')
    ->cron('0 14 */3 * *') // Every 3 days at 2:00 PM
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/inactive-user-reminders.log'))
    ->onSuccess(function () {
        Log::info('Inactive user reminders sent successfully.');
    })
    ->onFailure(function () {
        Log::error('Inactive user reminders failed.');
    });

// Weekly KYC Reminder for Long-pending Users - Every Sunday at 11:00 AM
Schedule::command('email:kyc-pending-reminders --limit=200')
    ->weeklyOn(0, '11:00') // Sunday at 11:00 AM
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/weekly-kyc-reminders.log'))
    ->onSuccess(function () {
        Log::info('Weekly KYC reminders sent successfully.');
    })
    ->onFailure(function () {
        Log::error('Weekly KYC reminders failed.');
    });
