<?php

namespace App\Providers;

use App\Services\ModalService;
use App\View\Composers\ModalComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ModalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ModalService::class, function ($app) {
            return new ModalService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register view composer for all user-facing views
        View::composer([
            'templates.*',
            'user.*',
            'partials.*'
        ], ModalComposer::class);
    }
}
