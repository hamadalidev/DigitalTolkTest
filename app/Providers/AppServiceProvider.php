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
        $this->app->bind(
            \App\Services\Interfaces\TranslationServiceInterface::class,
            \App\Services\TranslationService::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\TranslationRepositoryInterface::class,
            \App\Repositories\TranslationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            //
    }
}
