<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    public function sendFCM(string $token, array $payload): bool
    {
        $apiKey = config('services.fcm.key');
        $endpoint = 'https://fcm.googleapis.com/fcm/send';

        if (!$apiKey) {
            Log::warning('FCM key not configured');
            return false;
        }

        try {
            $response = Http::withToken($apiKey)->post($endpoint, [
                'to' => $token,
                'notification' => [
                    'title' => $payload['title'] ?? config('app.name'),
                    'body' => $payload['body'] ?? '',
                ],
                'data' => $payload['data'] ?? [],
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM send failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
