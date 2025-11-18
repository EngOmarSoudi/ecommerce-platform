<?php

namespace App\Providers;

use App\Services\Search\DatabaseSearchService;
use App\Services\Search\SearchServiceInterface;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SearchServiceInterface::class, function ($app) {
            // Default to database search
            // Can be switched to MeiliSearch later
            return new DatabaseSearchService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
