<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule flash sales processing every minute
Schedule::command('promotions:process-flash-sales')->everyMinute();

// Schedule payout generation daily
Schedule::command('payouts:generate')->daily();
