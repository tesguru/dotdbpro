<?php

namespace App\Http\Middleware;

use App\Traits\JsonResponseTrait;
use App\Models\DailySearch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DailySearchLimit
{
    use JsonResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Sanctum
        if ($user = $request->user()) {
            Log::info('Authenticated user access', [
                'user_id' => $user->id,
                'route' => $request->route()->getName()
            ]);
            return $next($request); // Unlimited access for authenticated users
        }

        // Anonymous user - apply IP-based limit
        $ipAddress = self::getClientIp($request);
        $today = now()->toDateString();

        Log::info('Anonymous user access', [
            'ip' => $ipAddress,
            'today' => $today,
            'route' => $request->route()->getName()
        ]);

        // Use database for tracking
        $searchRecord = DailySearch::firstOrCreate(
            [
                'ip_address' => $ipAddress,
                'date' => $today,
            ],
            ['count' => 0]
        );

        if ($searchRecord->count >= 5) {
            Log::warning('Daily search limit reached', [
                'ip' => $ipAddress,
                'count' => $searchRecord->count
            ]);

            return $this->errorResponse(
                429,
                'You have reached your daily search limit of 5 searches. Please login to continue.',
                [
                    'limit_reached' => true,
                    'searches_remaining' => 0,
                    'reset_time' => now()->endOfDay()->format('Y-m-d H:i:s')
                ]
            );
        }

        // Increment count
        $searchRecord->increment('count');
        $updatedCount = $searchRecord->fresh()->count;

        $response = $next($request);

        // Add search info to response
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            $data['searches_remaining'] = 5 - $updatedCount;
            $data['searches_used'] = $updatedCount;
            $response->setData($data);
        }

        return $response;
    }

    public static function clearSearchLimit(Request $request = null)
    {
        if (!$request) {
            $request = request();
            if (!$request) return;
        }

        $ipAddress = self::getClientIp($request);
        $today = now()->toDateString();

        Log::info('Clearing search limit', [
            'ip' => $ipAddress,
            'today' => $today
        ]);

        // Delete from database
        DailySearch::where('ip_address', $ipAddress)
                   ->where('date', $today)
                   ->delete();

        // Also clear any cache entries
        $identifier = md5($ipAddress);
        $cacheKey = "search_limit_{$identifier}";
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
    }

    public static function getClientIp(Request $request): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            $ip = $request->server($header);

            if ($ip) {
                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }
}
