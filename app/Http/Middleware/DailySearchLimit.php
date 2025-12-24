<?php
namespace App\Http\Middleware;

use App\Traits\JsonResponseTrait;
use Closure;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class DailySearchLimit
{
    use JsonResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        // Check if user_id is provided in request (simple auth check)
        $userId = $request->header('X-User-Id') ?? $request->input('user_id');

        if ($userId && UserAccount::find($userId)) {
            // Valid user - unlimited searches
            return $next($request);
        }

        // Anonymous user - apply IP-based limit
        $ipAddress = self::getClientIp($request); // Make it static
        $identifier = md5($ipAddress);
        $cacheKey = "search_limit_{$identifier}";
        $today = now()->toDateString();

        $searchData = Cache::get($cacheKey, [
            'count' => 0,
            'date' => $today,
            'ip' => $ipAddress
        ]);

        if ($searchData['date'] !== $today) {
            $searchData = [
                'count' => 0,
                'date' => $today,
                'ip' => $ipAddress
            ];
        }

        if ($searchData['count'] >= 5) {
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

        $searchData['count']++;
        $minutesUntilMidnight = now()->diffInMinutes(now()->endOfDay());
        Cache::put($cacheKey, $searchData, now()->addMinutes($minutesUntilMidnight));

        $response = $next($request);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            $data['searches_remaining'] = 5 - $searchData['count'];
            $data['searches_used'] = $searchData['count'];
            $response->setData($data);
        }

        return $response;
    }

    // Make this static so it can be called from UserService
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
