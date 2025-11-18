<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limit = '60'): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        // Parse limit (format: "60,1" = 60 requests per 1 minute)
        [$maxAttempts, $decayMinutes] = $this->parseLimitString($limit);
        
        // Check rate limit
        $executed = RateLimiter::attempt(
            $key,
            $maxAttempts,
            function() {
                return true;
            },
            $decayMinutes * 60
        );
        
        if (!$executed) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }
        
        // Add rate limit headers
        $response = $next($request);
        
        return $this->addHeaders(
            $response,
            $maxAttempts,
            RateLimiter::remaining($key, $maxAttempts),
            RateLimiter::availableIn($key)
        );
    }
    
    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'user:' . $user->id;
        }
        
        return 'ip:' . $request->ip();
    }
    
    /**
     * Parse limit string
     */
    protected function parseLimitString(string $limit): array
    {
        if (str_contains($limit, ',')) {
            [$maxAttempts, $decayMinutes] = explode(',', $limit, 2);
            return [(int) $maxAttempts, (int) $decayMinutes];
        }
        
        return [(int) $limit, 1];
    }
    
    /**
     * Add rate limit headers to response
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remaining, int $retryAfter = null): Response
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));
        
        if (!is_null($retryAfter) && $remaining <= 0) {
            $response->headers->set('X-RateLimit-Reset', time() + $retryAfter);
            $response->headers->set('Retry-After', $retryAfter);
        }
        
        return $response;
    }
}
