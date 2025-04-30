<?php

namespace App\Providers;

use App\Services\AliasResolver;
use Illuminate\Support\ServiceProvider;

class AliasResolverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AliasResolver::class, function ($app) {
            return new AliasResolver();
        });

        // Register a convenient alias for the facade
        $this->app->alias(AliasResolver::class, 'alias.resolver');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 