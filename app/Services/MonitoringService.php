<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public static function report(Throwable $e): void
    {
        // Structured error log
        Log::error('application_exception', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->take(5),
        ]);

        // Optional Sentry (if SDK exists and DSN configured)
        if (class_exists('Sentry\\Laravel\\Facade')) {
            try {
                \Sentry\Laravel\Facade::captureException($e);
            } catch (\Throwable $ignored) {}
        }
    }
}
