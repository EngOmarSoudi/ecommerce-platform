<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\CashPaymentGateway;
use App\Services\Payment\StripePaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $defaultGateway = config('services.payment.default', 'cash');

            return match($defaultGateway) {
                'stripe' => new StripePaymentGateway(),
                'cash' => new CashPaymentGateway(),
                default => new CashPaymentGateway(),
            };
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
