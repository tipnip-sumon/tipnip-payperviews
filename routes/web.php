<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\User\InvestController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SupportController;
use App\Http\Controllers\admin\AdminKycController;
use App\Http\Controllers\User\VideoViewController;
use App\Http\Controllers\User\RequirementsController;
use App\Http\Controllers\admin\VideoLinkController;
use App\Http\Controllers\Gateway\DepositController;

use App\Http\Controllers\admin\GeneralSettingController;
use App\Http\Controllers\admin\AdminTransReceiveController;
use App\Http\Controllers\admin\UserController as AdminUserController;
use App\Http\Controllers\admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\admin\SupportController as AdminSupportController;
use App\Http\Controllers\admin\AnalyticsController;
use App\Http\Controllers\admin\ModalManagementController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\BrowserCacheController;

// =============================================================================
// STORAGE ROUTES (Fix for development server symbolic link issues)
// =============================================================================

// Serve storage files directly (workaround for php artisan serve symbolic link issues)
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    
    if (!file_exists($file)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($file);
    return response()->file($file, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

// =============================================================================
// PUBLIC ROUTES
// =============================================================================

Route::get('/',[LandController::class, 'index']);

// CSRF Token refresh routes
Route::get('/csrf-refresh', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.refresh');

Route::post('/csrf-refresh', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'success' => true
    ]);
})->name('csrf.refresh.post');

Route::get('/session-check', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'authenticated' => \Illuminate\Support\Facades\Auth::check()
    ]);
})->name('session.check');

Route::post('/csrf-validate', function () {
    return response()->json(['valid' => true]);
})->name('csrf.validate');

// =============================================================================
// BROWSER CACHE CLEARING ROUTES
// =============================================================================

// Primary cache clearing route - matches your requested URL pattern
Route::get('/browser_cache_clear/only_this_domain', [BrowserCacheController::class, 'clearDomainCache'])
    ->name('browser.cache.clear.domain');

// Advanced cache clearing with parameters
Route::get('/browser_cache_clear/advanced', [BrowserCacheController::class, 'clearDomainCacheAdvanced'])
    ->name('browser.cache.clear.advanced');

// API endpoint for cache status
Route::get('/api/cache-clear-status', [BrowserCacheController::class, 'cacheClearStatus'])
    ->name('api.cache.clear.status');

// Alternative routes for different clearing types
Route::get('/clear-cache/{type?}', [BrowserCacheController::class, 'clearDomainCacheAdvanced'])
    ->where('type', 'all|cache|cookies|storage|execution')
    ->name('browser.cache.clear.type');

// Country data endpoint
Route::get('get-countries', function () {
    $c        = json_decode(file_get_contents(resource_path('views/country/country.json')));
    foreach ($c as $k => $country) {
        $countries[] = [
            'country'      => $country->country,
            'dial_code'    => $country->dial_code,
            'country_code' => $k,
        ];
    }
    return response()->json([
        'remark'  => 'country_data',
        'status'  => 'success',
        'data'    => [
            'countries' => $countries,
        ],
    ]);
});

// =============================================================================
// ADMIN ROUTES
// =============================================================================

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('clear.login.cache');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login')->middleware('throttle:5,1');
Route::get('/admin/video-leaderboard', [VideoLinkController::class, 'leaderboard'])->name('video.leaderboard')->middleware(['ok-user','prevent-back']);

// Newsletter Routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/admin/newsletter/stats', [NewsletterController::class, 'stats'])->name('admin.newsletter.stats')->middleware(['ok-user','prevent-back']);

// Guest Install Suggestion Routes
Route::post('/dismiss-install-suggestion', function () {
    $request = request();
    $dismissType = $request->input('dismiss_type', 'daily');
    $date = $request->input('date', now()->format('Y-m-d'));
    
    if ($dismissType === 'permanent') {
        session(['install_suggestion_dismissed_permanently' => true]);
    } elseif ($dismissType === 'daily') {
        session(['install_suggestion_last_shown_date' => $date]);
    } else {
        // Just mark as shown today
        session(['install_suggestion_last_shown_date' => $date]);
    }
    
    return response()->json([
        'success' => true, 
        'message' => 'Install suggestion dismissed',
        'dismiss_type' => $dismissType,
        'date' => $date
    ]);
})->name('guest.dismiss-install-suggestion');

// =============================================================================
// USER AUTHENTICATION & DASHBOARD
// =============================================================================

// Home route redirects properly for both authenticated and non-authenticated users
Route::get('/home', function () {
    if (!Auth::check()) {
        return redirect()->route('login', ['t' => time()])
            ->with('info', 'Please log in to access your dashboard.');
    }
    return redirect()->route('user.dashboard');
})->name('home');

// User Dashboard Route
Route::get('/user/dashboard', [App\Http\Controllers\User\UserController::class, 'home'])
    ->name('user.dashboard')->middleware(['auth', 'fresh.login', 'no-cache']);

// Dashboard Performance Metrics API
Route::get('/user/dashboard/performance', [App\Http\Controllers\User\UserController::class, 'getPerformanceMetrics'])
    ->name('dashboard.performance')->middleware(['auth']);

// =============================================================================
// AUTHENTICATION ROUTES  
// =============================================================================

// Using Laravel's built-in auth routes for login
Auth::routes(['verify' => true]);

// Override login routes with custom middleware
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('clear.login.cache');

// CSRF token refresh endpoint to prevent 419 errors after logout
Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token(),
        'success' => true
    ]);
})->name('refresh.csrf');



Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])
    ->name('register');

Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])
    ->name('register.submit');

// Sponsor validation route for real-time checking
Route::post('/validate-sponsor', [App\Http\Controllers\Auth\RegisterController::class, 'validateSponsor'])
    ->name('validate.sponsor');

// Username validation route for real-time checking
Route::post('/validate-username', [App\Http\Controllers\Auth\RegisterController::class, 'validateUsername'])
    ->name('validate.username');

// Email validation route for real-time checking
Route::post('/validate-email', [App\Http\Controllers\Auth\RegisterController::class, 'validateEmail'])
    ->name('validate.email');

// Email verification resend route for login form
Route::post('/resend-verification', [App\Http\Controllers\Auth\LoginController::class, 'resendVerification'])
    ->name('resend.verification')
    ->middleware('throttle:3,1'); // Allow max 3 attempts per minute

// Main logout route - properly configured with session handling
Route::match(['GET', 'POST'], '/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout')
    ->middleware(['web']);

// Alternative simple logout route (no middleware at all)
Route::get('/simple-logout', function(\Illuminate\Http\Request $request) {
    try {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            // Clear all session data first
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Then logout
            \Illuminate\Support\Facades\Auth::logout();
            
            \Illuminate\Support\Facades\Log::info('Simple logout successful', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);
        }
        
        // Clear any cached auth data
        \Illuminate\Support\Facades\Cache::forget('user_' . ($user->id ?? 'unknown'));
        
        // Force redirect to login with cache busting
        return redirect('/login?logout=1&t=' . time())
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->with('success', 'You have been logged out successfully.');
            
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Logout error: ' . $e->getMessage());
        
        // Emergency logout - clear everything
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login?emergency_logout=1&t=' . time());
    }
})->name('simple.logout');

// Password reset routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->name('password.update');

Route::get('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])
    ->name('password.confirm');

Route::post('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);

// =============================================================================
// EMAIL VERIFICATION ROUTES
// =============================================================================

Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
    ->middleware(['auth'])
    ->name('verification.resend');

Route::post('/email/resend-public', [App\Http\Controllers\Auth\VerificationController::class, 'resendPublic'])
    ->middleware(['throttle:3,1'])
    ->name('verification.resend.public');

// =============================================================================
// PROTECTED MAIN ROUTES - Authentication Required
// =============================================================================

// Main invest route - requires authentication  
// Route::get('/invest', function () {
//     return redirect()->route('invest.index');
// })->middleware('auth')->name('main.invest');

// =============================================================================
// REFERRAL ROUTES
// =============================================================================

Route::get('/ref/{hash}', function ($hash) {
    $user = App\Models\User::findByReferralHash($hash);
    if ($user) {
        return redirect()->route('register', ['ref' => $hash]);
    }
    return redirect()->route('register');
})->name('referral.hash');

// =============================================================================
// ADMIN DASHBOARD & MANAGEMENT
// =============================================================================

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware(['ok-user','prevent-back','cache.control']);
    Route::get('/payment', [AdminController::class, 'payment'])->name('admin.payment')->middleware(['ok-user','prevent-back']);
    Route::get('/transfer_member', [AdminTransReceiveController::class, 'index'])->name('admin.transfer_member')->middleware(['ok-user','prevent-back']);
    Route::post('/transfer_member', [AdminTransReceiveController::class, 'store'])->name('admin.transfer_member.store')->middleware(['ok-user','prevent-back']);
    Route::get('/transfer_history', [AdminTransReceiveController::class, 'history'])->name('admin.transfer_history')->middleware(['ok-user','prevent-back']);
    Route::get('/transfer_details/{id}', [AdminTransReceiveController::class, 'details'])->name('admin.transfer_details')->middleware(['ok-user','prevent-back']);
    Route::get('/transfer_reports', [AdminTransReceiveController::class, 'reports'])->name('admin.transfer_reports')->middleware(['ok-user','prevent-back']);
    Route::delete('/transfer_delete/{id}', [AdminTransReceiveController::class, 'destroy'])->name('admin.transfer_delete')->middleware(['ok-user','prevent-back']);
    Route::get('/transfer_stats', [AdminTransReceiveController::class, 'getTransferStats'])->name('admin.transfer_stats')->middleware(['ok-user','prevent-back']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout')->middleware(['ok-user','prevent-back']);
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout.get')->middleware(['ok-user','prevent-back']); // Allow GET for logout links
    
    // Emergency logout route for expired sessions (no CSRF verification)
    Route::post('/emergency-logout', [AdminController::class, 'emergencyLogout'])->name('admin.emergency.logout');
    Route::get('/emergency-logout', [AdminController::class, 'emergencyLogout'])->name('admin.emergency.logout.get');
    
    // Session management routes
    Route::post('/extend-session', [AdminController::class, 'extendSession'])->name('admin.extend-session')->middleware(['ok-user']);
    Route::get('/csrf-token', [AdminController::class, 'getCsrfToken'])->name('admin.csrf-token');
    Route::get('/session-status', [AdminController::class, 'getSessionStatus'])->name('admin.session-status');
    
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/change-password', [AdminController::class, 'changePassword'])->name('admin.change-password')->middleware(['ok-user','prevent-back']);
    
    // Deposit Management Routes  
    Route::controller(AdminPaymentController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/deposits', 'index')->name('admin.deposits.index');
        Route::get('/deposits/pending', 'pending')->name('admin.deposits.pending');
        Route::get('/deposits/approved', 'approved')->name('admin.deposits.approved');
        Route::get('/deposits/rejected', 'rejected')->name('admin.deposits.rejected');
        Route::get('/deposits/{id}', 'show')->name('admin.deposits.show');
        Route::post('/deposits/{id}/approve', 'approve')->name('admin.deposits.approve');
        Route::post('/deposits/{id}/reject', 'reject')->name('admin.deposits.reject');
        Route::post('/deposits/bulk-action', 'bulkAction')->name('admin.deposits.bulk-action');
        Route::get('/deposits/export', 'export')->name('admin.deposits.export');
    });
    
    // Withdrawal Management Routes
    Route::controller(AdminPaymentController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/withdrawals', 'withdrawals')->name('admin.withdrawals.index');
        Route::get('/withdrawals/pending', 'pendingWithdrawals')->name('admin.withdrawals.pending');
        Route::get('/withdrawals/approved', 'approvedWithdrawals')->name('admin.withdrawals.approved');
        Route::get('/withdrawals/rejected', 'rejectedWithdrawals')->name('admin.withdrawals.rejected');
        Route::get('/withdrawals/export', 'showWithdrawalsExport')->name('admin.withdrawals.export');
        Route::get('/withdrawals/download', 'exportWithdrawals')->name('admin.withdrawals.download');
        Route::get('/withdrawals/{id}', 'showWithdrawal')->name('admin.withdrawals.show');
        Route::post('/withdrawals/{id}/approve', 'approveWithdrawal')->name('admin.withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', 'rejectWithdrawal')->name('admin.withdrawals.reject');
        Route::post('/withdrawals/bulk-action', 'withdrawalBulkAction')->name('admin.withdrawals.bulk-action');
    });
    
    // Withdrawal Methods Management Routes 
    Route::controller(App\Http\Controllers\admin\WithdrawMethodController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/withdraw-methods', 'index')->name('admin.withdraw-methods.index');
        Route::get('/withdraw-methods/create', 'create')->name('admin.withdraw-methods.create');
        Route::post('/withdraw-methods', 'store')->name('admin.withdraw-methods.store');
        Route::get('/withdraw-methods/{withdrawMethod}', 'show')->name('admin.withdraw-methods.show');
        Route::get('/withdraw-methods/{withdrawMethod}/edit', 'edit')->name('admin.withdraw-methods.edit');
        Route::put('/withdraw-methods/{withdrawMethod}', 'update')->name('admin.withdraw-methods.update');
        Route::delete('/withdraw-methods/{withdrawMethod}', 'destroy')->name('admin.withdraw-methods.destroy');
        Route::post('/withdraw-methods/{withdrawMethod}/toggle-status', 'toggleStatus')->name('admin.withdraw-methods.toggle-status');
        Route::post('/withdraw-methods/update-sort-order', 'updateSortOrder')->name('admin.withdraw-methods.update-sort-order');
        Route::get('/withdraw-methods/statistics', 'statistics')->name('admin.withdraw-methods.statistics');
        Route::post('/withdraw-methods/seed-defaults', 'seedDefaults')->name('admin.withdraw-methods.seed-defaults');
    });
    
    // User Management Routes 
    Route::controller(AdminUserController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/users/search', 'search')->name('admin.users.search');
        Route::get('/users', 'index')->name('admin.users.index');
        Route::get('/users/active', 'active')->name('admin.users.active');
        Route::get('/users/inactive', 'inactive')->name('admin.users.inactive');
        Route::get('/users/banned', 'banned')->name('admin.users.banned');
        Route::get('/users/export', 'export')->name('admin.users.export');
        Route::get('/users/download', 'downloadExport')->name('admin.users.download');
        Route::get('/users/{id}', 'show')->name('admin.users.show');
        Route::get('/users/{id}/edit', 'edit')->name('admin.users.edit');
        Route::put('/users/{id}', 'update')->name('admin.users.update');
        Route::post('/users/change-status', 'changeStatus')->name('admin.users.change-status');
        Route::post('/users/ban', 'banUser')->name('admin.users.ban');
        Route::post('/users/check-username', 'checkUsernameAvailability')->name('admin.users.check-username');
        Route::post('/users/check-email', 'checkEmailAvailability')->name('admin.users.check-email');
        
        // User Verification Routes
        Route::prefix('users/verification')->name('admin.users.verification.')->group(function () {
            Route::get('/dashboard', 'verificationDashboard')->name('dashboard');
            Route::get('/settings', 'verificationSettings')->name('settings');
            Route::post('/settings', 'updateVerificationSettings')->name('settings.update');
            Route::get('/reports', 'verificationReports')->name('reports');
            Route::get('/reports/export', 'exportVerificationReports')->name('reports.export');
            Route::get('/email', 'emailVerificationIndex')->name('email');
            Route::get('/sms', 'smsVerificationIndex')->name('sms');
            Route::get('/phone', 'phoneVerificationIndex')->name('phone');
            Route::get('/identity', 'identityVerificationIndex')->name('identity');
            Route::get('/kyc', 'kycVerificationIndex')->name('kyc');
            Route::get('/2fa', 'twoFactorIndex')->name('2fa');
            Route::post('/email/verify/{id}', 'verifyEmail')->name('email.verify');
            Route::post('/email/unverify/{id}', 'unverifyEmail')->name('email.unverify');
            Route::post('/sms/verify/{id}', 'verifySms')->name('sms.verify');
            Route::post('/sms/unverify/{id}', 'unverifySms')->name('sms.unverify');
            Route::post('/phone/verify/{id}', 'verifyPhone')->name('phone.verify');
            Route::post('/phone/unverify/{id}', 'unverifyPhone')->name('phone.unverify');
            Route::post('/identity/verify/{id}', 'verifyIdentity')->name('identity.verify');
            Route::post('/identity/unverify/{id}', 'unverifyIdentity')->name('identity.unverify');
            Route::post('/kyc/verify/{id}', 'verifyKyc')->name('kyc.verify');
            Route::post('/kyc/unverify/{id}', 'unverifyKyc')->name('kyc.unverify');
            Route::post('/2fa/enable/{id}', 'enable2fa')->name('2fa.enable');
            Route::post('/2fa/disable/{id}', 'disable2fa')->name('2fa.disable');
            Route::post('/2fa/reset/{id}', 'reset2fa')->name('2fa.reset');
            Route::post('/2fa/force/{id}', 'force2fa')->name('2fa.force');
            Route::post('/bulk-verify', 'bulkVerify')->name('bulk.verify');
            Route::post('/bulk-unverify', 'bulkUnverify')->name('bulk.unverify');
            Route::post('/send-verification-email/{id}', 'sendVerificationEmail')->name('send.email');
            Route::post('/send-verification-sms/{id}', 'sendVerificationSms')->name('send.sms');
            Route::post('/send-verification-phone/{id}', 'sendVerificationPhone')->name('send.phone');
            Route::post('/send-verification-identity/{id}', 'sendVerificationIdentity')->name('send.identity');
            Route::post('/send-verification-identity-bulk', 'sendBulkIdentityInstructions')->name('send.identity.bulk');
        });
    });

    // Video Links Management Routes
    Route::controller(VideoLinkController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/video-links', 'index')->name('admin.video-links.index');
        Route::get('/video-links/create', 'create')->name('admin.video-links.create');
        Route::get('/video-links/export', 'export')->name('admin.video-links.export');
        Route::get('/video-links/export/advanced', 'advancedExport')->name('admin.video-links.export.advanced');
        Route::get('/video-links/sample-csv', 'downloadSampleCsv')->name('admin.video-links.sample-csv');
        Route::post('/video-links', 'store')->name('admin.video-links.store');
        Route::post('/video-links/bulk-action', 'bulkAction')->name('admin.video-links.bulk-action');
        Route::post('/video-links/import', 'import')->name('admin.video-links.import');
        Route::get('/video-links/{id}', 'show')->name('admin.video-links.show');
        Route::get('/video-links/{id}/edit', 'edit')->name('admin.video-links.edit');
        Route::put('/video-links/{id}', 'update')->name('admin.video-links.update');
        Route::delete('/video-links/{id}', 'destroy')->name('admin.video-links.destroy');
    });
    
    // General Settings Routes
    Route::controller(GeneralSettingController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/settings/general', 'index')->name('admin.settings.general');
        Route::put('/settings/general', 'update')->name('admin.settings.update');
        Route::get('/settings/media', 'mediaSettings')->name('admin.settings.media');
        Route::post('/settings/media', 'updateMediaSettings')->name('admin.settings.media.update');
        Route::get('/settings/seo', 'seoSettings')->name('admin.settings.seo');
        Route::post('/settings/seo', 'updateSeoSettings')->name('admin.settings.seo.update');
        Route::get('/settings/content', 'contentSettings')->name('admin.settings.content');
        Route::post('/settings/content', 'updateContentSettings')->name('admin.settings.content.update');
        Route::get('/settings/theme', 'themeSettings')->name('admin.settings.theme');
        Route::post('/settings/theme', 'updateThemeSettings')->name('admin.settings.theme.update');
        Route::get('/settings/social-media', 'socialMediaSettings')->name('admin.settings.social-media');
        Route::post('/settings/social-media', 'updateSocialMediaSettings')->name('admin.settings.social-media.update');
        Route::get('/settings/mail-config', 'mailConfig')->name('admin.settings.mail-config');
        Route::post('/settings/mail-config', 'updateMailConfig')->name('admin.settings.mail-config.update');
        Route::get('/settings/sms-config', 'smsConfig')->name('admin.settings.sms-config');
        Route::post('/settings/sms-config', 'updateSmsConfig')->name('admin.settings.sms-config.update');
        Route::get('/settings/security', 'securitySettings')->name('admin.settings.security');
        Route::post('/settings/security', 'updateSecuritySettings')->name('admin.settings.security.update');
        Route::post('/settings/clear-cache', 'clearCache')->name('admin.settings.clear-cache');
        Route::post('/settings/toggle-maintenance', 'toggleMaintenanceMode')->name('admin.settings.toggle-maintenance');
        Route::get('/settings/system-info', 'getSystemInfo')->name('admin.settings.system-info');
        Route::post('/settings/test-email', 'testEmail')->name('admin.settings.test-email');
        Route::get('/settings/export', 'exportSettings')->name('admin.settings.export');
        Route::post('/settings/import', 'importSettings')->name('admin.settings.import');
    });
    
    // Commission Level Management Routes
    Route::controller(App\Http\Controllers\admin\CommissionLevelController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/admin/commission-levels', 'index')->name('admin.commission-levels.index');
        Route::get('/admin/commission-levels/create', 'create')->name('admin.commission-levels.create');
        Route::post('/admin/commission-levels', 'store')->name('admin.commission-levels.store');
        Route::get('/admin/commission-levels/{commissionLevel}/edit', 'edit')->name('admin.commission-levels.edit');
        Route::put('/admin/commission-levels/{commissionLevel}', 'update')->name('admin.commission-levels.update');
        Route::delete('/admin/commission-levels/{commissionLevel}', 'destroy')->name('admin.commission-levels.destroy');
        Route::post('/admin/commission-levels/{commissionLevel}/toggle-active', 'toggleActive')->name('admin.commission-levels.toggle-active');
        Route::post('/admin/commission-levels/reset-defaults', 'resetToDefaults')->name('admin.commission-levels.reset-defaults');
        Route::post('/admin/commission-levels/bulk-update', 'bulkUpdate')->name('admin.commission-levels.bulk-update');
    });
    
    // Support Management Routes
    Route::controller(AdminSupportController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/support', 'index')->name('admin.support.index');
        Route::get('/support/tickets', 'tickets')->name('admin.support.tickets');
        Route::get('/support/tickets/{id}', 'show')->name('admin.support.show');
        Route::post('/support/tickets/{id}/reply', 'reply')->name('admin.support.reply');
        Route::post('/support/tickets/{id}/status', 'updateStatus')->name('admin.support.update-status');
        Route::post('/support/tickets/{id}/star', 'toggleStar')->name('admin.support.toggle-star');
        Route::post('/support/bulk-action', 'bulkAction')->name('admin.support.bulk-action');
        Route::get('/support/export', 'export')->name('admin.support.export');
    });
    
    // Admin Notification Management Routes
    Route::controller(App\Http\Controllers\admin\NotificationController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/notifications', 'index')->name('admin.notifications.index');
        Route::get('/notifications/create', 'create')->name('admin.notifications.create');
        Route::post('/notifications', 'store')->name('admin.notifications.store');
        Route::get('/notifications/dropdown', 'getDropdownNotifications')->name('admin.notifications.dropdown');
        Route::get('/notifications/stats', 'getStats')->name('admin.notifications.stats');
        Route::get('/notifications/unread-count', 'getUnreadCount')->name('admin.notifications.unread-count');
        Route::get('/notifications/{id}', 'show')->name('admin.notifications.show');
        Route::post('/notifications/{id}/read', 'markAsRead')->name('admin.notifications.read');
        Route::post('/notifications/{id}/mark-read', 'markAsRead')->name('admin.notifications.mark-read');
        Route::post('/notifications/{id}/unread', 'markAsUnread')->name('admin.notifications.unread');
        Route::post('/notifications/read-all', 'markAllAsRead')->name('admin.notifications.read-all');
        Route::post('/notifications/mark-all-read', 'markAllAsRead')->name('admin.notifications.mark-all-read');
        Route::delete('/notifications/{id}', 'delete')->name('admin.notifications.delete');
        Route::delete('/notifications/clear-all', 'clearAll')->name('admin.notifications.clear-all');
        Route::post('/notifications/{id}/duplicate', 'duplicate')->name('admin.notifications.duplicate');
        Route::get('/notifications/{id}/redirect', 'redirect')->name('admin.notifications.redirect');
        Route::post('/notifications/send-announcement', 'sendAnnouncement')->name('admin.notifications.send-announcement');
        
        // Settings Routes
        Route::get('/notifications/settings/index', 'settings')->name('admin.notifications.settings');
        Route::post('/notifications/settings/save', 'saveSettings')->name('admin.notifications.settings.save');
        Route::post('/notifications/settings/reset', 'resetSettings')->name('admin.notifications.settings.reset');
        Route::get('/notifications/settings/get', 'getSettings')->name('admin.notifications.settings.get');
        Route::post('/notifications/cleanup', 'cleanup')->name('admin.notifications.cleanup');
        
        // Real-time Routes
        Route::get('/notifications/realtime/index', 'realtime')->name('admin.notifications.realtime');
        Route::get('/notifications/realtime/get', 'getRealtime')->name('admin.notifications.realtime.get');
        Route::post('/notifications/test', 'testNotification')->name('admin.notifications.test');
    });
    
    // Sub-Admin Management Routes
    Route::controller(App\Http\Controllers\admin\SubAdminController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/sub-admins', 'index')->name('admin.sub-admins.index');
        Route::get('/sub-admins/create', 'create')->name('admin.sub-admins.create');
        Route::get('/sub-admins/permissions', 'permissions')->name('admin.sub-admins.permissions');
        Route::post('/sub-admins', 'store')->name('admin.sub-admins.store');
        Route::get('/sub-admins/{id}', 'show')->name('admin.sub-admins.show');
        Route::get('/sub-admins/{id}/edit', 'edit')->name('admin.sub-admins.edit');
        Route::put('/sub-admins/{id}', 'update')->name('admin.sub-admins.update');
        Route::delete('/sub-admins/{id}', 'destroy')->name('admin.sub-admins.destroy');
        Route::get('/sub-admins/{id}/toggle-status', 'toggleStatus')->name('admin.sub-admins.toggle-status');
        Route::get('/sub-admins/{id}/reset-password', 'resetPassword')->name('admin.sub-admins.reset-password');
    });
    
    // Admin Plan Management
    Route::controller(App\Http\Controllers\admin\PlanController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/plans', 'index')->name('admin.plans.index');
        Route::get('/plans/create', 'create')->name('admin.plans.create');
        Route::post('/plans', 'store')->name('admin.plans.store');
        Route::get('/plans/{id}', 'show')->name('admin.plans.show');
        Route::get('/plans/{id}/edit', 'edit')->name('admin.plans.edit');
        Route::put('/plans/{id}', 'update')->name('admin.plans.update');
        Route::delete('/plans/{id}', 'destroy')->name('admin.plans.destroy');
        Route::get('/plans/{id}/statistics', 'statistics')->name('admin.plans.statistics');
        Route::post('/plans/{id}/toggle-status', 'toggleStatus')->name('admin.plans.toggle-status');
    });
    
    // Analytics Management Routes 
    Route::controller(AnalyticsController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/analytics', 'index')->name('admin.analytics.index');
        Route::get('/analytics/users', 'userAnalytics')->name('admin.analytics.users');
        Route::get('/analytics/revenue', 'revenueAnalytics')->name('admin.analytics.revenue');
        Route::get('/analytics/videos', 'videoAnalytics')->name('admin.analytics.videos');
        Route::get('/analytics/investments', 'investmentAnalytics')->name('admin.analytics.investments');
        Route::get('/analytics/performance', 'performanceMetrics')->name('admin.analytics.performance');
        Route::get('/analytics/chart-data', 'chartData')->name('admin.analytics.chart-data');
        Route::get('/analytics/chart-page', 'chartPage')->name('admin.analytics.chart-page');
        Route::get('/analytics/chart-dashboard', 'chartDashboard')->name('admin.analytics.chart-dashboard');
        Route::get('/analytics/lottery', 'lotteryAnalytics')->name('admin.analytics.lottery');
        Route::get('/analytics/date-range', 'getAnalyticsByDateRange')->name('admin.analytics.date-range');
        Route::get('/analytics/realtime', 'realtimeData')->name('admin.analytics.realtime');
        Route::get('/analytics/export', 'exportAnalytics')->name('admin.analytics.export');
        Route::post('/analytics/settings', 'saveSettings')->name('admin.analytics.settings');
    });
    
    // Referral Benefits Management Routes
    Route::controller(App\Http\Controllers\admin\ReferralBenefitsController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/referral-benefits', 'index')->name('admin.referral-benefits.index');
        Route::post('/referral-benefits/update-settings', 'updateSettings')->name('admin.referral-benefits.update-settings');
        Route::post('/referral-benefits/recalculate', 'recalculateQualifications')->name('admin.referral-benefits.recalculate');
        Route::get('/referral-benefits/qualified-users', 'qualifiedUsers')->name('admin.referral-benefits.qualified-users');
        Route::get('/referral-benefits/user-details/{userId}', 'userDetails')->name('admin.referral-benefits.user-details');
        Route::post('/referral-benefits/toggle-user-status/{userId}', 'toggleUserStatus')->name('admin.referral-benefits.toggle-user-status');
        Route::post('/referral-benefits/recalculate-user/{userId}', 'recalculateUser')->name('admin.referral-benefits.recalculate-user');
        Route::get('/referral-benefits/bonus-transactions', 'bonusTransactions')->name('admin.referral-benefits.bonus-transactions');
        Route::get('/referral-benefits/bonus-transactions/data', 'getBonusTransactions')->name('admin.referral-benefits.bonus-transactions.data');
        Route::get('/referral-benefits/transaction-details/{transactionId}', 'transactionDetails')->name('admin.referral-benefits.transaction-details');
        Route::get('/referral-benefits/export-qualified-users', 'exportQualifiedUsers')->name('admin.referral-benefits.export-qualified-users');
        Route::get('/referral-benefits/export-transactions', 'exportTransactions')->name('admin.referral-benefits.export-transactions');
    });

    // Modal Management Routes
    Route::controller(ModalManagementController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/modals', 'index')->name('admin.modal.index');
        Route::get('/modals/create', 'create')->name('admin.modal.create');
        Route::post('/modals', 'store')->name('admin.modal.store');
        Route::get('/modals/{id}', 'show')->name('admin.modal.show');
        Route::get('/modals/{id}/edit', 'edit')->name('admin.modal.edit');
        Route::put('/modals/{id}', 'update')->name('admin.modal.update');
        Route::delete('/modals/{id}', 'destroy')->name('admin.modal.destroy');
        Route::post('/modals/{id}/toggle-status', 'toggleStatus')->name('admin.modal.toggle-status');
        Route::get('/modals/analytics', 'analytics')->name('admin.modal.analytics');
        Route::post('/modals/bulk-action', 'bulkAction')->name('admin.modal.bulk-action');
        Route::get('/modals/quick-stats', 'quickStats')->name('admin.modal.quick-stats');
    });

    // Admin Popup Management Routes
    Route::controller(App\Http\Controllers\admin\PopupController::class)->middleware(['ok-user','prevent-back'])->group(function () {
        Route::get('/popups', 'index')->name('admin.popups.index');
        Route::get('/popups/create', 'create')->name('admin.popups.create');
        Route::post('/popups', 'store')->name('admin.popups.store');
        Route::get('/popups/{popup}', 'show')->name('admin.popups.show');
        Route::get('/popups/{popup}/edit', 'edit')->name('admin.popups.edit');
        Route::put('/popups/{popup}', 'update')->name('admin.popups.update');
        Route::delete('/popups/{popup}', 'destroy')->name('admin.popups.destroy');
        Route::post('/popups/{popup}/toggle-status', 'toggleStatus')->name('admin.popups.toggle-status');
        Route::get('/popups/{popup}/preview', 'preview')->name('admin.popups.preview');
        Route::get('/popups/{popup}/analytics', 'analytics')->name('admin.popups.analytics');
        Route::post('/popups/{popup}/duplicate', 'duplicate')->name('admin.popups.duplicate');
    });
});

Route::get('admin/payment', [AdminPaymentController::class, 'index'])->name('admin.payment');

// =============================================================================
// USER MANAGEMENT & PROFILE
// =============================================================================

Route::resource('users', UserController::class);

// Removed duplicate /home route - using the one defined above that redirects to user.dashboard

Route::controller(UserController::class)->middleware('auth','prevent-back')->group(function () {
    Route::any('deposit/history', 'depositHistory')->name('deposit.history');
    Route::post('find-user','findUser')->name('findUser');
    Route::post('transfer-balance','transferBalanceSubmit')->name('transfer-balance');
    Route::get('/user/transactions','transfer_funds')->name('user.transfer_funds');
    Route::get('/user/transactions/history','transferHistory')->name('user.transfer_history');
    Route::get('/user/refferral-history','referralEarnings')->name('user.refferral-history');
    Route::get('/user/generation-history','generationHistory')->name('user.generation-history');
    Route::get('/user/referral','referralIndex')->name('referral.index');
    Route::get('/user/sponsor-list','sponsorList')->name('user.sponsor-list');
    Route::get('/user/sponsor/{sponsorId}/details','getSponsorDetails')->name('user.sponsor.details');
    Route::get('/user/sponsor/{sponsorId}/performance','getSponsorPerformance')->name('user.sponsor.performance');
    Route::post('/user/sponsor/contact','contactSponsor')->name('user.sponsor.contact');
    Route::get('/user/messages','messages')->name('user.messages');
    Route::get('/user/messages/inbox','inbox')->name('user.messages.inbox');
    Route::get('/user/messages/sent','sentMessages')->name('user.messages.sent');
    Route::get('/user/messages/{messageId}','viewMessage')->name('user.messages.view');
    Route::post('/user/messages/{messageId}/mark-read','markMessageAsRead')->name('user.messages.mark-read');
    Route::post('/user/messages/{messageId}/reply','replyToMessage')->name('user.messages.reply');
    Route::delete('/user/messages/{messageId}','deleteMessage')->name('user.messages.delete');
    Route::get('/user/session-notifications','getSessionNotifications')->name('user.session-notifications');
    Route::get('/user/session-notifications/check','checkSessionNotifications')->name('user.session-notifications.check');
    Route::post('/user/session-notifications/mark-read','markNotificationsAsRead')->name('user.session-notifications.mark-read');
    Route::post('/user/tab-cleanup','cleanupUserTab')->name('user.tab-cleanup');
    Route::get('/user/team-tree','teamTree')->name('user.team-tree');
    Route::get('/user/team-tree/data','teamTreeData')->name('user.team-tree.data'); 
    Route::get('/user/team-tree/level','getTeamByLevel')->name('user.team-tree.level');
    Route::get('/user/team-tree/available-levels','getAvailableLevels')->name('user.team-tree.available-levels');
    Route::post('/user/send-message','sendMessage')->name('user.send-message');
    Route::get('/user/details/{userId}','getUserDetails')->name('user.details');
    Route::post('/user/dismiss-install-suggestion','dismissInstallSuggestion')->name('user.dismiss-install-suggestion');
});

Route::controller(HomeController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/profile/{userId?}','profile')->name('profile');
    Route::get('/edit/profile','editProfile')->name('editProfile');
});

// =============================================================================
// SUPPORT ROUTES
// =============================================================================

Route::controller(SupportController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/support', 'index')->name('user.support.index');
    Route::get('/user/support/tickets', 'tickets')->name('user.support.tickets');
    Route::get('/user/support/create', 'createTicket')->name('user.support.create');
    Route::post('/user/support/create', 'storeTicket')->name('user.support.store');
    Route::get('/user/support/tickets/{id}/details', 'ticketDetails')->name('user.support.details');
    Route::post('/user/support/tickets/{id}/reply', 'replyToTicket')->name('user.support.reply');
    Route::post('/user/support/tickets/{id}/close', 'closeTicketAction')->name('user.support.close');
    Route::post('/user/support/tickets/{id}/reopen', 'reopenTicket')->name('user.support.reopen');
    Route::post('/user/support/tickets/{id}/rate', 'rateTicket')->name('user.support.rate');
    Route::post('/user/support/tickets/{id}/star', 'toggleStar')->name('user.support.star');
    Route::get('/user/support/knowledge', 'knowledge')->name('user.support.knowledge');
    Route::get('/user/support/contact', 'contact')->name('user.support.contact');
    Route::post('/user/support/contact', 'sendContact')->name('user.support.contact.send');
});

// =============================================================================
// USER DASHBOARD FEATURES
// =============================================================================

// User Requirements Dashboard
Route::controller(RequirementsController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/requirements', 'index')->name('user.requirements');
});

// Notification Routes
Route::controller(\App\Http\Controllers\User\NotificationController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/notifications', 'index')->name('user.notifications.index');
    Route::get('/user/notifications/dropdown', 'getDropdownNotifications')->name('user.notifications.dropdown');
    Route::get('/user/notifications/{id}', 'show')->name('user.notifications.show');
    Route::get('/user/notifications/settings', 'settings')->name('user.notifications.settings');
    Route::post('/user/notifications/settings', 'updateSettings')->name('user.notifications.settings.update');
    Route::post('/user/notifications/test', 'sendTestNotification')->name('user.notifications.test');
    Route::post('/user/notifications/{id}/read', 'markAsRead')->name('user.notifications.read');
    Route::post('/user/notifications/read-all', 'markAllAsRead')->name('user.notifications.read-all');
    Route::delete('/user/notifications/clear-all', 'clearAll')->name('user.notifications.clear-all');
    Route::delete('/user/notifications/{id}', 'delete')->name('user.notifications.delete');
    Route::get('/user/notifications/count', 'getUnreadCount')->name('user.notifications.count');
    Route::get('/user/notifications/{id}/redirect', 'redirect')->name('user.notifications.redirect');
});

// Popup API Routes for Users
Route::controller(\App\Http\Controllers\User\PopupController::class)->group(function () {
    Route::get('/api/popups', 'getPopups')->name('api.popups.get');
    Route::post('/api/popups/{popup}/view', 'recordView')->name('api.popups.view');
    Route::post('/api/popups/{popup}/click', 'handleClick')->name('api.popups.click');
});

// Sponsor Ticket Routes
Route::controller(\App\Http\Controllers\User\SponsorTicketController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/sponsor-tickets', 'index')->name('user.sponsor-tickets.index');
    Route::get('/user/sponsor-tickets/{ticket}/transfer', 'showTransfer')->name('user.sponsor-tickets.transfer');
    Route::post('/user/sponsor-tickets/{ticket}/transfer', 'transfer')->name('user.sponsor-tickets.transfer.submit');
    Route::get('/user/sponsor-tickets/history', 'transferHistory')->name('user.sponsor-tickets.history');
    Route::post('/user/sponsor-tickets/{ticket}/use-token', 'useAsToken')->name('user.sponsor-tickets.use-token');
    Route::get('/user/sponsor-tickets/available', 'getAvailableTickets')->name('user.sponsor-tickets.available');
});

// =============================================================================
// INVESTMENT ROUTES
// =============================================================================

Route::controller(InvestController::class)->middleware(['auth'])->group(function () {
    Route::get('/plans','plans')->name('plans');
    Route::get('/invest','index')->name('invest.index');
    Route::post('/get-plan-amount','getPlanAmount')->name('get-plan-amount');
    Route::post('/invest','invest')->name('invest.submit');
    Route::get('/invest/history','log')->name('invest.history');
    Route::get('/invest/statistics','statistics')->name('invest.statistics');
    Route::get('/invest/log','log')->name('invest.log');
});

// Ticket Validation Routes for Investment
Route::controller(App\Http\Controllers\Api\TicketValidationController::class)->middleware('auth','prevent-back')->group(function () {
    Route::post('/tickets/validate', 'validateTicket')->name('tickets.validate');
    Route::post('/tickets/mark-used', 'markTicketAsUsed')->name('tickets.mark-used');
    Route::get('/tickets/history', 'getTicketHistory')->name('tickets.history');
});

// =============================================================================
// WITHDRAWAL ROUTES
// =============================================================================

Route::controller(App\Http\Controllers\User\WithdrawController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/withdraw','index')->name('user.withdraw');
    Route::post('/user/withdraw','withdraw')->name('user.withdraw.submit');
    Route::get('/user/withdraw/history','history')->name('user.withdraw.history');
    Route::get('/user/withdraw/wallet','walletIndex')->name('user.withdraw.wallet');
    Route::post('/user/withdraw/wallet','walletWithdraw')->name('user.withdraw.wallet.submit');
    Route::get('/user/withdraw/wallet/history','walletHistory')->name('user.withdraw.wallet.history');
});

// =============================================================================
// PAYMENT & DEPOSIT ROUTES
// =============================================================================

Route::controller(DepositController::class)->middleware('auth','prevent-back')->group(function(){
    Route::get('/deposit', 'index')->name('deposit.index');
    Route::post('insert', 'depositInsert')->name('deposit.insert');
    Route::get('confirm', 'depositConfirm')->name('deposit.confirm');
    Route::get('manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
    Route::post('manual', 'manualDepositUpdate')->name('deposit.manual.update');
});

// NOWPayments payment gateway routes
Route::middleware(['auth','prevent-back'])->group(function () {
    Route::post('/pay', [PaymentController::class, 'createCryptoPayment'])->name('pay');
    Route::delete('/pay/delete/{user_id}', [PaymentController::class, 'cancelCryptoPayment']);
    Route::post('/payment/ipn', [PaymentController::class, 'handleIPN'])->name('payments');
    Route::get('/user/payment/history',[PaymentController::class, 'paymentHistory'])->name('user.payment_history');
    Route::post('/user/payment/create', [PaymentController::class, 'createPayment'])->name('user.payment.create');
    Route::put('/user/payment/{id}', [PaymentController::class, 'updatePayment'])->name('user.payment.update');
    Route::delete('/user/payment/{id}', [PaymentController::class, 'deletePayment'])->name('user.payment.delete');
    Route::get('/user/payment/{id}', [PaymentController::class, 'getPayment'])->name('user.payment.get');
});

// =============================================================================
// KYC ROUTES
// =============================================================================

Route::controller(App\Http\Controllers\User\KycController::class)->middleware('auth')->group(function () {
    Route::get('/user/kyc', 'index')->name('user.kyc.index');
    Route::get('/user/kyc/form', 'create')->name('user.kyc.create');
    Route::post('/user/kyc/submit', 'store')->name('user.kyc.store');
    Route::get('/user/kyc/status', 'status')->name('user.kyc.status');
    Route::post('/user/kyc/check-document', 'checkDocumentNumber')->name('user.kyc.check-document');
    Route::get('/user/kyc/{id}/pdf', 'generateKycPdf')->name('user.kyc.pdf');
    Route::get('/user/kyc/{id}/download/{type}', 'downloadDocument')->name('user.kyc.download');
    Route::get('/user/kyc/{id}/view/{type}', 'viewDocument')->name('user.kyc.view');
});

// Admin KYC Routes
Route::prefix('admin')->middleware(['ok-user','prevent-back'])->group(function () {
    Route::controller(AdminKycController::class)->group(function () {
        Route::get('/kyc', 'index')->name('admin.kyc.index');
        Route::get('/kyc/{id}', 'show')->name('admin.kyc.show');
        Route::post('/kyc/{id}/update-status', 'updateStatus')->name('admin.kyc.update-status');
        Route::post('/kyc/bulk-approve', 'bulkApprove')->name('admin.kyc.bulk-approve');
        Route::post('/kyc/bulk-change-status', 'bulkChangeStatus')->name('admin.kyc.bulk-change-status');
        Route::post('/kyc/{id}/mark-under-review', 'markUnderReview')->name('admin.kyc.mark-under-review');
        Route::get('/kyc/under-review', 'getUnderReview')->name('admin.kyc.under-review');
        Route::get('/kyc/{id}/document/view/{type}', 'viewDocument')->name('admin.kyc.document.view');
        Route::get('/kyc/{id}/document/download/{type}', 'downloadDocument')->name('admin.kyc.document.download');
        Route::get('/kyc/statistics/data', 'statistics')->name('admin.kyc.statistics');
    });

    // Transfer & Withdrawal Conditions Routes
    Route::controller(\App\Http\Controllers\admin\TransferWithdrawConditionsController::class)->group(function () {
        Route::get('/transfer-withdraw-conditions', 'index')->name('admin.transfer-withdraw-conditions.index');
        Route::put('/transfer-withdraw-conditions', 'update')->name('admin.transfer-withdraw-conditions.update');
        Route::post('/transfer-withdraw-conditions/reset', 'resetToDefaults')->name('admin.transfer-withdraw-conditions.reset');
        Route::get('/transfer-withdraw-conditions/summary', 'getConditionsSummary')->name('admin.transfer-withdraw-conditions.summary');
    });
});

// =============================================================================
// VIDEO ROUTES
// =============================================================================

Route::get('/videos', [VideoLinkController::class, 'publicGallery'])->name('videos.public');

// Redirect old service worker URLs to correct route
Route::get('/videos/public', function () {
    return redirect('/videos', 301);
})->name('videos.public.redirect');

Route::prefix('user')->middleware(['auth', 'verified','prevent-back'])->group(function () {
    // Dashboard API Routes for real-time updates
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/data', [App\Http\Controllers\User\DashboardApiController::class, 'getDashboardData'])->name('data');
        Route::get('/quick-stats', [App\Http\Controllers\User\DashboardApiController::class, 'getQuickStats'])->name('quick-stats');
        Route::get('/investment-data', [App\Http\Controllers\User\DashboardApiController::class, 'getInvestmentData'])->name('investment-data');
        Route::get('/transaction-data', [App\Http\Controllers\User\DashboardApiController::class, 'getTransactionData'])->name('transaction-data');
        Route::get('/referral-data', [App\Http\Controllers\User\DashboardApiController::class, 'getReferralData'])->name('referral-data');
        Route::get('/video-system-data', [App\Http\Controllers\User\DashboardApiController::class, 'getVideoSystemData'])->name('video-system-data');
        Route::get('/performance-metrics', [App\Http\Controllers\User\DashboardApiController::class, 'getPerformanceMetrics'])->name('performance-metrics');
        Route::get('/recent-activities', [App\Http\Controllers\User\DashboardApiController::class, 'getRecentActivities'])->name('recent-activities');
        Route::post('/clear-cache', [App\Http\Controllers\User\DashboardApiController::class, 'clearCache'])->name('clear-cache');
        Route::post('/update-share-count', [App\Http\Controllers\User\DashboardApiController::class, 'updateShareCount'])->name('update-share-count');
        
        // Global Real-time Updates API
        Route::get('/global-data', [App\Http\Controllers\User\GlobalRealtimeController::class, 'getGlobalData'])->name('global-data');
        Route::post('/clear-global-cache', [App\Http\Controllers\User\GlobalRealtimeController::class, 'clearCache'])->name('clear-global-cache');
    });
    
    Route::post('/video/{id}/record-view', [VideoLinkController::class, 'recordView'])->name('video.record-view');
    Route::get('/video-history', [VideoLinkController::class, 'viewingHistory'])->name('video.history');
    Route::get('/video-earnings', [VideoLinkController::class, 'videoEarnings'])->name('video.earnings');
    Route::get('/video-daily-report', [VideoLinkController::class, 'dailyReport'])->name('video.daily-report');
    Route::get('/video/{id}/stats', [VideoLinkController::class, 'videoStats'])->name('video.stats');
    
    Route::controller(App\Http\Controllers\User\SearchController::class)->group(function () {
        Route::get('/search', 'results')->name('search.results');
        Route::get('/search/suggestions', 'suggestions')->name('search.suggestions');
    });
    Route::get('/video-recent-activity', [VideoLinkController::class, 'recentActivity'])->name('video.recent-activity');
});

Route::controller(VideoViewController::class)->middleware('auth','prevent-back')->group(function () {
    Route::get('/user/video-views/gallery', 'gallery')->name('user.video-views.gallery');
    Route::get('/user/video-views', 'index')->name('user.video-views.index');
    Route::get('/user/video-views/history', 'history')->name('user.video-views.history');
    Route::get('/user/video-views/{video}/watch', 'showVideo')->name('user.video-views.show');
    Route::post('/user/video-views/watch', 'watch')->name('user.video-views.watch');
    Route::post('/user/video-views/record-view/{videoId}', 'recordView')->name('user.video-views.record-view');
    Route::get('/user/video-views/earnings', 'earnings')->name('user.video-views.earnings');
    Route::get('/user/video-history','publicGallery')->name('gallery');
    Route::get('/videos/{id}','show')->name('video.show');
});

// =============================================================================
// POLICIES ROUTES
// =============================================================================

Route::get('/policies/{type}', function ($type) {
    $validTypes = ['terms-of-service', 'privacy-policy'];
    
    if (!in_array($type, $validTypes)) {
        abort(404);
    }
    
    $title = ucwords(str_replace('-', ' ', $type));
    
    return view('policies.' . str_replace('-', '_', $type), compact('title'));
})->name('policies')->where('type', 'terms-of-service|privacy-policy');

// =============================================================================
// PROFILE ROUTES
// =============================================================================

Route::middleware(['auth','prevent-back'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::get('/profile/password', 'showPasswordForm')->name('profile.password');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::get('/profile/security', 'security')->name('profile.security');
        Route::delete('/profile/avatar', 'deleteAvatar')->name('profile.avatar.delete');
    });
});

// =============================================================================
// LOTTERY ROUTES
// =============================================================================

// Unified Lottery & Ticket Center Routes
Route::middleware('auth','prevent-back')->prefix('lottery/unified')->name('lottery.unified.')->group(function () {
    Route::get('/', [App\Http\Controllers\User\UnifiedLotteryController::class, 'index'])->name('index');
    Route::get('/activity', [App\Http\Controllers\User\UnifiedLotteryController::class, 'allActivity'])->name('activity.all');
    Route::get('/available-plans', [App\Http\Controllers\User\UnifiedLotteryController::class, 'availablePlans'])->name('available.plans');
    Route::post('/track-share', [App\Http\Controllers\User\UnifiedLotteryController::class, 'trackShare'])->name('track.share');
    Route::get('/countdown', [App\Http\Controllers\User\UnifiedLotteryController::class, 'getCountdown'])->name('countdown');
    Route::post('/transfer-tokens', [App\Http\Controllers\User\UnifiedLotteryController::class, 'transferTokens'])->name('transfer.tokens');
    Route::post('/transfer-token', [App\Http\Controllers\User\UnifiedLotteryController::class, 'transferTokens'])->name('transfer.token');
    Route::post('/use-token', [App\Http\Controllers\User\UnifiedLotteryController::class, 'useToken'])->name('use.token');
    Route::post('/create-token', [App\Http\Controllers\User\UnifiedLotteryController::class, 'createToken'])->name('create.token');
});

// Share System Routes
Route::middleware('auth','prevent-back')->group(function () {
    Route::get('/lottery/share', [App\Http\Controllers\User\UnifiedLotteryController::class, 'shareSystem'])->name('lottery.share');
    Route::post('/lottery/track-share', [App\Http\Controllers\User\UnifiedLotteryController::class, 'trackShare'])->name('lottery.track.share');
});

Route::middleware('auth','prevent-back')->prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [App\Http\Controllers\LotteryController::class, 'index'])->name('index');
    Route::get('/results', [App\Http\Controllers\LotteryController::class, 'results'])->name('results');
    Route::get('/statistics', [App\Http\Controllers\LotteryController::class, 'statistics'])->name('statistics');
    Route::get('/draws', [App\Http\Controllers\LotteryController::class, 'activeDraws'])->name('draw.details');
    Route::get('/draw/{id}', [App\Http\Controllers\LotteryController::class, 'drawDetails'])->name('draw.detail');
    Route::post('/status-check', [App\Http\Controllers\LotteryController::class, 'statusCheck'])->name('status.check');
    
    Route::middleware('auth')->group(function () {
        Route::post('/buy-ticket', [App\Http\Controllers\LotteryController::class, 'buyTicket'])->name('buy.ticket');
        Route::get('/my-tickets', [App\Http\Controllers\LotteryController::class, 'myTickets'])->name('my.tickets');
        Route::get('/my-winnings', [App\Http\Controllers\LotteryController::class, 'myWinnings'])->name('my.winnings');
        Route::post('/claim-prize/{winner}', [App\Http\Controllers\LotteryController::class, 'claimPrize'])->name('claim.prize');
    });
});

// Special Lottery Tickets Routes
Route::middleware('auth','prevent-back')->prefix('special-tickets')->name('special.tickets.')->group(function () {
    Route::get('/', [App\Http\Controllers\SpecialTicketController::class, 'index'])->name('index');
    Route::get('/tokens', [App\Http\Controllers\SpecialTicketController::class, 'tokens'])->name('tokens');
    Route::post('/calculate-discount', [App\Http\Controllers\SpecialTicketController::class, 'calculateDiscount'])->name('calculate.discount');
    Route::get('/history', [App\Http\Controllers\SpecialTicketController::class, 'history'])->name('history');
    Route::get('/statistics', [App\Http\Controllers\SpecialTicketController::class, 'statistics'])->name('statistics');
    Route::get('/transfer', [App\Http\Controllers\SpecialTicketController::class, 'transfer'])->name('transfer');
    Route::post('/send-transfer', [App\Http\Controllers\SpecialTicketController::class, 'sendTransfer'])->name('send.transfer');
    Route::get('/incoming', [App\Http\Controllers\SpecialTicketController::class, 'incoming'])->name('incoming');
    Route::get('/outgoing', [App\Http\Controllers\SpecialTicketController::class, 'outgoing'])->name('outgoing');
    Route::post('/accept-transfer/{transferId}', [App\Http\Controllers\SpecialTicketController::class, 'acceptTransfer'])->name('accept.transfer');
    Route::post('/reject-transfer/{transferId}', [App\Http\Controllers\SpecialTicketController::class, 'rejectTransfer'])->name('reject.transfer');
    Route::post('/cancel-transfer/{transferId}', [App\Http\Controllers\SpecialTicketController::class, 'cancelTransfer'])->name('cancel.transfer');
});

// Admin Lottery Routes   
Route::middleware(['ok-user','prevent-back'])->prefix('admin/lottery')->name('admin.lottery.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\LotteryController::class, 'index'])->name('index');
    
    // Draw Management Routes
    Route::get('/draws', [App\Http\Controllers\admin\LotteryController::class, 'draws'])->name('draws');
    Route::get('/draws/create', [App\Http\Controllers\admin\LotteryController::class, 'createDraw'])->name('draws.create');
    Route::post('/draws', [App\Http\Controllers\admin\LotteryController::class, 'storeDraw'])->name('draws.store');
    Route::get('/draws/{id}', [App\Http\Controllers\admin\LotteryController::class, 'drawDetails'])->name('draws.details');
    Route::get('/draws/{id}/edit', [App\Http\Controllers\admin\LotteryController::class, 'editDraw'])->name('draws.edit');
    Route::put('/draws/{id}', [App\Http\Controllers\admin\LotteryController::class, 'updateDraw'])->name('draws.update');
    Route::post('/draws/{id}/perform', [App\Http\Controllers\admin\LotteryController::class, 'performDraw'])->name('draws.perform');
    Route::post('/draws/{id}/cancel', [App\Http\Controllers\admin\LotteryController::class, 'cancelDraw'])->name('draws.cancel');
    Route::post('/draws/{id}/distribute', [App\Http\Controllers\admin\LotteryController::class, 'distributePrizes'])->name('draws.distribute');
    Route::get('/draws/{id}/manual-winners', [App\Http\Controllers\admin\LotteryController::class, 'manualWinners'])->name('draws.manual-winners');
    Route::post('/draws/{id}/manual-winners', [App\Http\Controllers\admin\LotteryController::class, 'storeManualWinners'])->name('draws.store-manual-winners');
    Route::delete('/draws/{id}', [App\Http\Controllers\admin\LotteryController::class, 'deleteDraw'])->name('draws.delete');
    Route::post('/draws/bulk-action', [App\Http\Controllers\admin\LotteryController::class, 'drawsBulkAction'])->name('draws.bulk-action');
    Route::get('/draws/{id}/export', [App\Http\Controllers\admin\LotteryController::class, 'exportDraw'])->name('draws.export');
    
    // Auto Lottery Routes
    Route::post('/auto-generate', [App\Http\Controllers\admin\LotteryController::class, 'autoGenerateDraw'])->name('auto-generate');
    Route::post('/draws/{id}/execute', [App\Http\Controllers\admin\LotteryController::class, 'executeAutoDraw'])->name('draws.execute');
    Route::get('/draws/{id}/manual-selection', [App\Http\Controllers\admin\LotteryController::class, 'manualWinnerSelection'])->name('draws.manual-selection');
    Route::post('/draws/{id}/save-winners', [App\Http\Controllers\admin\LotteryController::class, 'saveManualWinners'])->name('draws.save-winners');
    
    // Manual Winner Manipulation Routes
    Route::get('/draws/{id}/winner-manipulation', [App\Http\Controllers\admin\LotteryController::class, 'manualWinnerManipulation'])->name('draws.winner-manipulation');
    Route::post('/draws/{id}/manual-winners', [App\Http\Controllers\admin\LotteryController::class, 'storeManualWinners'])->name('draws.store-manual-winners');
    Route::delete('/draws/{id}/manual-winners/{winnerId}', [App\Http\Controllers\admin\LotteryController::class, 'removeManualWinner'])->name('draws.remove-manual-winner');
    Route::patch('/draws/{id}/manual-winners/{winnerId}/change-position', [App\Http\Controllers\admin\LotteryController::class, 'changeWinnerPosition'])->name('draws.change-winner-position');
    Route::get('/draws/{id}/tickets/{ticketId}/validate', [App\Http\Controllers\admin\LotteryController::class, 'validateTicketAvailability'])->name('draws.validate-ticket');
    Route::post('/draws/{id}/save-manual-tickets', [App\Http\Controllers\admin\LotteryController::class, 'saveManualWinningTickets'])->name('draws.save-manual-tickets');
    Route::delete('/draws/{id}/clear-manual-winners', [App\Http\Controllers\admin\LotteryController::class, 'clearManualWinners'])->name('draws.clear-manual-winners');
    
    // Ticket management routes 
    Route::get('/draws/{id}/tickets', [App\Http\Controllers\admin\LotteryController::class, 'getDrawTickets'])->name('draws.tickets');
    
    // Ticket Management Routes
    Route::get('/tickets', [App\Http\Controllers\admin\LotteryController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/{id}', [App\Http\Controllers\admin\LotteryController::class, 'ticketDetails'])->name('tickets.details');
    Route::post('/tickets/bulk-action', [App\Http\Controllers\admin\LotteryController::class, 'ticketsBulkAction'])->name('tickets.bulk-action');
    
    // Winner Management Routes
    Route::get('/winners', [App\Http\Controllers\admin\LotteryController::class, 'winners'])->name('winners');
    Route::get('/winners/{id}', [App\Http\Controllers\admin\LotteryController::class, 'winnerDetails'])->name('winners.details');
    Route::post('/winners/{id}/force-claim', [App\Http\Controllers\admin\LotteryController::class, 'forceClaimPrize'])->name('winners.force-claim');
    Route::post('/winners/distribute-all', [App\Http\Controllers\admin\LotteryController::class, 'distributeAllPrizes'])->name('winners.distribute-all');
    Route::post('/winners/notify', [App\Http\Controllers\admin\LotteryController::class, 'notifyWinners'])->name('winners.notify');
    
    Route::get('/report', [App\Http\Controllers\admin\LotteryController::class, 'report'])->name('report');
    Route::get('/export', [App\Http\Controllers\admin\LotteryController::class, 'export'])->name('export');
});

// Admin Lottery Settings Management Routes
Route::middleware(['ok-user','prevent-back'])->prefix('admin/lottery-settings')->name('admin.lottery-settings.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\LotterySettingsController::class, 'index'])->name('index');
    Route::post('/update', [App\Http\Controllers\admin\LotterySettingsController::class, 'update'])->name('update');
    Route::post('/reset', [App\Http\Controllers\admin\LotterySettingsController::class, 'resetToDefaults'])->name('reset');
    Route::get('/backup', [App\Http\Controllers\admin\LotterySettingsController::class, 'backup'])->name('backup');
    Route::post('/backup', [App\Http\Controllers\admin\LotterySettingsController::class, 'createBackup'])->name('backup.create');
    Route::get('/export', [App\Http\Controllers\admin\LotterySettingsController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\admin\LotterySettingsController::class, 'import'])->name('import');
});

// Admin System Commands (Emergency Command Runner)
Route::middleware(['ok-user','prevent-back'])->prefix('admin/system-commands')->name('admin.system-commands.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\SystemCommandController::class, 'index'])->name('index');
    Route::post('/execute', [App\Http\Controllers\admin\SystemCommandController::class, 'execute'])->name('execute');
    Route::get('/status', [App\Http\Controllers\admin\SystemCommandController::class, 'status'])->name('status');
});

// Admin Maintenance Mode Management
Route::middleware(['ok-user','prevent-back'])->prefix('admin/maintenance')->name('admin.maintenance.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\MaintenanceController::class, 'index'])->name('index');
    Route::post('/enable', [App\Http\Controllers\admin\MaintenanceController::class, 'enable'])->name('enable');
    Route::post('/disable', [App\Http\Controllers\admin\MaintenanceController::class, 'disable'])->name('disable');
    Route::post('/enable-scenario', [App\Http\Controllers\admin\MaintenanceController::class, 'enableScenario'])->name('enable-scenario');
    Route::get('/status', [App\Http\Controllers\admin\MaintenanceController::class, 'status'])->name('status');
    Route::get('/generate-secret', [App\Http\Controllers\admin\MaintenanceController::class, 'generateSecret'])->name('generate-secret');
    Route::get('/preview', [App\Http\Controllers\admin\MaintenanceController::class, 'preview'])->name('preview');
    Route::get('/templates', [App\Http\Controllers\admin\MaintenanceController::class, 'templates'])->name('templates');
    Route::get('/scenarios', [App\Http\Controllers\admin\MaintenanceController::class, 'scenarios'])->name('scenarios');
    Route::post('/schedule-disable', [App\Http\Controllers\admin\MaintenanceController::class, 'scheduleDisable'])->name('schedule-disable');
    Route::post('/test-bypass', [App\Http\Controllers\admin\MaintenanceController::class, 'testBypass'])->name('test-bypass');
    Route::post('/validate-secret', [App\Http\Controllers\admin\MaintenanceController::class, 'validateSecret'])->name('validate-secret');
});

// Admin Schedule Management
Route::middleware(['ok-user','prevent-back'])->prefix('admin/schedule')->name('admin.schedule.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\ScheduleController::class, 'index'])->name('index');
    Route::post('/run', [App\Http\Controllers\admin\ScheduleController::class, 'runSchedule'])->name('run');
});
// Admin Settings Management
// Admin Queue Management
Route::middleware(['ok-user','prevent-back'])->prefix('admin/queue')->name('admin.queue.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\QueueController::class, 'index'])->name('index');
    Route::get('/worker-status', [App\Http\Controllers\admin\QueueController::class, 'workerStatus'])->name('worker-status');
    Route::post('/start-worker', [App\Http\Controllers\admin\QueueController::class, 'startWorker'])->name('start-worker');
    Route::get('/counts', [App\Http\Controllers\admin\QueueController::class, 'getCounts'])->name('counts');
});

// Admin Failed Jobs Management
Route::middleware(['ok-user','prevent-back'])->prefix('admin/failed-jobs')->name('admin.failed-jobs.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\FailedJobsController::class, 'index'])->name('index');
    Route::post('/retry/{id}', [App\Http\Controllers\admin\FailedJobsController::class, 'retry'])->name('retry');
    Route::delete('/delete/{id}', [App\Http\Controllers\admin\FailedJobsController::class, 'delete'])->name('delete');
    Route::post('/retry-all', [App\Http\Controllers\admin\FailedJobsController::class, 'retryAll'])->name('retry-all');
    Route::delete('/clear-all', [App\Http\Controllers\admin\FailedJobsController::class, 'clearAll'])->name('clear-all');
});

// Admin Email Campaign Routes
Route::middleware(['ok-user','prevent-back'])->prefix('admin/email-campaigns')->name('admin.email-campaigns.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\EmailCampaignController::class, 'index'])->name('index');
    Route::get('/analytics', [App\Http\Controllers\admin\EmailCampaignController::class, 'analytics'])->name('analytics');
    Route::get('/templates', [App\Http\Controllers\admin\EmailCampaignController::class, 'templates'])->name('templates');
    Route::get('/queue', [App\Http\Controllers\admin\EmailCampaignController::class, 'queue'])->name('queue');
    Route::get('/settings', [App\Http\Controllers\admin\EmailCampaignController::class, 'settings'])->name('settings');
    
    // Campaign Actions
    Route::post('/send-kyc-reminders', [App\Http\Controllers\admin\EmailCampaignController::class, 'sendKycReminders'])->name('send-kyc-reminders');
    Route::post('/send-inactive-reminders', [App\Http\Controllers\admin\EmailCampaignController::class, 'sendInactiveReminders'])->name('send-inactive-reminders');
    Route::post('/send-password-resets', [App\Http\Controllers\admin\EmailCampaignController::class, 'sendPasswordResets'])->name('send-password-resets');
    Route::post('/send-to-all-users', [App\Http\Controllers\admin\EmailCampaignController::class, 'sendToAllUsers'])->name('send-to-all-users');
    
    // Queue Management
    Route::get('/queue-status', [App\Http\Controllers\admin\EmailCampaignController::class, 'queueStatus'])->name('queue-status');
    Route::post('/retry-failed', [App\Http\Controllers\admin\EmailCampaignController::class, 'retryFailed'])->name('retry-failed');
    Route::post('/clear-failed', [App\Http\Controllers\admin\EmailCampaignController::class, 'clearFailed'])->name('clear-failed');
    
    // Template Management
    Route::put('/templates/{id}', [App\Http\Controllers\admin\EmailCampaignController::class, 'updateTemplate'])->name('update-template');
    Route::get('/templates/{slug}', [App\Http\Controllers\admin\EmailCampaignController::class, 'getTemplate'])->name('get-template');
    
    // Command Execution
    Route::post('/run-command', [App\Http\Controllers\admin\EmailCampaignController::class, 'runCommand'])->name('run-command');
});

// =============================================================================
// FALLBACK ROUTE - Redirect unauthenticated users to login
// =============================================================================

// Catch all undefined routes and redirect unauthenticated users to login
Route::fallback(function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
    }
    
    // If authenticated but page doesn't exist, show 404
    abort(404);
});
