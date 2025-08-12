<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\ValidUser;
// use App\Http\Middleware\PreventBack;
use App\Http\Middleware\AdminOptimized;
use App\Http\Middleware\AdminPermissionMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\CacheControlMiddleware;
use App\Http\Middleware\ClearLoginCache;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\NoCache;
use App\Http\Middleware\DeviceDetectionMiddleware;
use App\Http\Middleware\AdminSessionHandler;
use App\Http\Middleware\FreshLogin;
use App\Http\Middleware\SessionCleanup;
// use App\Http\Middleware\SessionSecurity; // REMOVED - no longer needed
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php', 
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('ok-user', [
            ValidUser::class
        ]);
        $middleware->appendToGroup('admin-optimized', [
            ValidUser::class,
            AdminOptimized::class
        ]);
        $middleware->appendToGroup('auth:admin', [
            AdminAuth::class,
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            // 'prevent-back' => PreventBack::class,
            'admin.permission' => AdminPermissionMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'cache.control' => CacheControlMiddleware::class,
            'clear.login.cache' => ClearLoginCache::class,
            'no-cache' => NoCache::class,
            'device.detect' => DeviceDetectionMiddleware::class,
            'admin.session' => AdminSessionHandler::class,
            'fresh.login' => FreshLogin::class,
            'session.cleanup' => SessionCleanup::class,
            // 'session.security' => SessionSecurity::class, // REMOVED - was causing route access issues
        ]);
        $middleware->appendToGroup('ensure.admin', [
            EnsureUserIsAdmin::class,
        ]);
        
        // Add device detection to web group for smart layouts
        $middleware->appendToGroup('web', [
            DeviceDetectionMiddleware::class,
            // SessionCleanup::class, // MOVED to alias only - can be applied selectively
        ]);
        
        // Custom logout middleware group (web without CSRF)
        $middleware->appendToGroup('logout', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // NOTE: Explicitly excluding VerifyCsrfToken for logout routes
        ]);
        
        // $middleware->appendToGroup('auth', [
        //     AdminMiddleware::class,
        // ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

