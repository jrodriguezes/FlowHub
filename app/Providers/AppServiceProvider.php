<?php

namespace App\Providers;

use App\Adapters\GitHubAdapter;
use App\Adapters\GoogleAdapter;
use App\Services\ProviderManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProviderManager::class, function ($app) {
            return new ProviderManager($app, [
                'github' => GitHubAdapter::class,
                'google' => GoogleAdapter::class,
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}
