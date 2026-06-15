<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class ThrottleRequests
{
    public function handle($request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): mixed
    {
        $key = 'throttle:' . $request->ip() . ':' . floor(time() / ($decayMinutes * 60));

        Cache::add($key, 0, $decayMinutes * 60);
        $hits = Cache::increment($key);

        $remaining = max(0, $maxAttempts - $hits);
        $retryAfter = $decayMinutes * 60 - (time() % ($decayMinutes * 60));

        if ($hits > $maxAttempts) {
            return response()->json(['error' => 'Too Many Requests'], 429)
                ->header('X-RateLimit-Limit', $maxAttempts)
                ->header('X-RateLimit-Remaining', 0)
                ->header('Retry-After', $retryAfter)
                ->header('X-RateLimit-Reset', time() + $retryAfter);
        }

        $response = $next($request);

        return $response
            ->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', $remaining);
    }
}
