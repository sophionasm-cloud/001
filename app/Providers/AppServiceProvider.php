<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Illuminate\Foundation\Vite::class, \App\Support\SafeVite::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!class_exists('Helper')) {
            class_alias(\App\Helpers\Helpers::class, 'Helper');
        }
    }
}
