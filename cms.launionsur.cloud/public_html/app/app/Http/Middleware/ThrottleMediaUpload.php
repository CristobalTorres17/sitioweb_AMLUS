<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleMediaUpload
{
    /**
     * Maximum uploads per hour per user (5 uploads/hour)
     */
    private const MAX_UPLOADS_PER_HOUR = 5;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must be logged in to upload media',
            ], 401);
        }

        $userId = auth()->id();
        $key = "media-upload:{$userId}";
        $limit = self::MAX_UPLOADS_PER_HOUR;
        $decayMinutes = 60;

        // Check if user has exceeded limit in the last hour
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $retryAfter = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'TooManyRequests',
                'message' => "You have reached the maximum of {$limit} uploads per hour. Please try again in {$retryAfter} seconds.",
                'retry_after' => $retryAfter,
            ], 429);
        }

        // Increment the counter for this user
        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
