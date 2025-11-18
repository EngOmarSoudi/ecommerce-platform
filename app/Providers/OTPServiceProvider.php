<?php

namespace App\Providers;

use App\Services\Auth\OTPServiceInterface;
use App\Services\Auth\StubOTPService;
use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OTPServiceInterface::class, StubOTPService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
