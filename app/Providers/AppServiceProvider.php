<?php

namespace App\Providers;

use App\Interfaces\Services\Profile\ProfileServiceInterface;
use App\Services\Profile\EloquentProfileService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProfileServiceInterface::class, EloquentProfileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return Str::replace('App\\Models', 'App\\Policies', $modelClass).'Policy';
        });
    }
}
