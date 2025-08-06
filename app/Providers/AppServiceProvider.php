<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Auth\Events\Login;
use App\Models\GeneralSetting;
// use Illuminate\Auth\Events\Registered;
use App\Listeners\UpdateLastLoginTime;
// use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);
        
        // Register event listeners
        Event::listen(Login::class, UpdateLastLoginTime::class);
        // Event::listen(Registered::class, SendEmailVerificationNotification::class);
        
        // Register custom Blade directives for permission checking
        $this->registerBladeDirectives();
        
        // Share settings globally with all views
        View::composer('*', function ($view) {
            if (Schema::hasTable('general_settings')) {
                try {
                    $settings = GeneralSetting::getSettings();
                    $view->with('settings', $settings);
                } catch (\Exception $e) {
                    // Fallback settings in case of error
                    $view->with('settings', (object) [
                        'logo' => null,
                        'admin_logo' => null,
                        'favicon' => null,
                        'site_name' => 'ViewCash',
                    ]);
                }
            }
        });
    }
    
    /**
     * Register custom Blade directives
     */
    private function registerBladeDirectives()
    {
        // @hasPermission directive
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if (\App\helpers\PermissionHelper::hasPermission($permission)): ?>";
        });
        
        Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });
        
        // @hasAnyPermission directive
        Blade::directive('hasAnyPermission', function ($permissions) {
            return "<?php if (\App\helpers\PermissionHelper::hasAnyPermission($permissions)): ?>";
        });
        
        Blade::directive('endhasAnyPermission', function () {
            return "<?php endif; ?>";
        });
        
        // @canAccessMenu directive
        Blade::directive('canAccessMenu', function ($menuKey) {
            return "<?php if (\App\helpers\PermissionHelper::canAccessMenu($menuKey)): ?>";
        });
        
        Blade::directive('endcanAccessMenu', function () {
            return "<?php endif; ?>";
        });
        
        // @isSuperAdmin directive
        Blade::directive('isSuperAdmin', function () {
            return "<?php if (\App\helpers\PermissionHelper::isSuperAdmin()): ?>";
        });
        
        Blade::directive('endisSuperAdmin', function () {
            return "<?php endif; ?>";
        });
        
        // @isRole directive
        Blade::directive('isRole', function ($role) {
            return "<?php if (\App\helpers\PermissionHelper::getCurrentAdminRole() === $role): ?>";
        });
        
        Blade::directive('endisRole', function () {
            return "<?php endif; ?>";
        });
    }
}
