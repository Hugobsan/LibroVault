<?php

namespace App\Providers;

use App\Services\SemanticService;
use App\Services\FileService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Facades
        $this->app->singleton('file-manager', function ($app) {
            return new FileService();
        });

        $this->app->singleton('semantic-manager', function ($app) {
            return new SemanticService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
