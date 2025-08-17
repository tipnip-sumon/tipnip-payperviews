<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\ValidUser;
use App\Http\Middleware\PreventBack;
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
use App\Http\Middleware\TrackModalSession;
use App\Http\Middleware\AutoSessionTimeout;
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
        
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.admin' => AdminAuth::class,  // Custom admin session middleware
            'prevent-back' => PreventBack::class,
            'admin.permission' => AdminPermissionMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'cache.control' => CacheControlMiddleware::class,
            'clear.login.cache' => ClearLoginCache::class,
            'no-cache' => NoCache::class,
            'device.detect' => DeviceDetectionMiddleware::class,
            'admin.session' => AdminSessionHandler::class,
            'fresh.login' => FreshLogin::class,
            'session.cleanup' => SessionCleanup::class,
            'track.modal.session' => TrackModalSession::class,
            'auto.timeout' => AutoSessionTimeout::class,
            // 'session.security' => SessionSecurity::class, // REMOVED - was causing route access issues
        ]);
        $middleware->appendToGroup('ensure.admin', [
            EnsureUserIsAdmin::class,
        ]);
        
        // Add device detection to web group for smart layouts
        $middleware->appendToGroup('web', [
            DeviceDetectionMiddleware::class,
            TrackModalSession::class,
            // AutoSessionTimeout::class, // TEMPORARILY DISABLED - Add automatic session timeout monitoring
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
        // Custom error page handling
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Not Found',
                    'message' => 'The requested resource was not found.',
                    'status' => 404
                ], 404);
            }
            
            return response()->view('errors.404', [
                'exception' => $e
            ], 404);
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Access Denied',
                    'message' => 'You do not have permission to access this resource.',
                    'status' => 403
                ], 403);
            }
            
            return response()->view('errors.403', [
                'exception' => $e
            ], 403);
        });
        
        // Log security-related 403 errors
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                \Illuminate\Support\Facades\Log::warning('403 Access Denied', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'message' => $e->getMessage()
                ]);
            }
        });
    })->create();

