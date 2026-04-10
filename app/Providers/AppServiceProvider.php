<?php

namespace App\Providers;

use App\Contracts\AstrologyEngineInterface;
use App\Services\Astrology\TuViLibraryAdapter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AstrologyEngineInterface::class, TuViLibraryAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
