<?php

namespace App\Services\Utility;

use App\Models\DailySearch;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchLimitService
{
    private const GUEST_SEARCH_LIMIT = 8;
    private const AUTHENTICATED_SEARCH_LIMIT = 100; // or unlimited

    /**
     * Check if the user/guest can perform a search
     */
    public function canSearch(Request $request): array
    {
        $userId = $this->getUserId($request);
        $ipAddress = $request->ip();
        $today = Carbon::today();

        // Get or create daily search record
        $dailySearch = DailySearch::firstOrCreate(
            [
                'ip_address' => $ipAddress,
                'user_id' => $userId,
                'date' => $today,
            ],
            ['count' => 0]
        );

        $limit = $userId ? self::AUTHENTICATED_SEARCH_LIMIT : self::GUEST_SEARCH_LIMIT;
        $remaining = max(0, $limit - $dailySearch->count);

        return [
            'can_search' => $dailySearch->count < $limit,
            'count' => $dailySearch->count,
            'limit' => $limit,
            'remaining' => $remaining,
            'is_authenticated' => !is_null($userId),
        ];
    }

    /**
     * Increment search count
     */
    public function incrementSearchCount(Request $request): void
    {
        $userId = $this->getUserId($request);
        $ipAddress = $request->ip();
        $today = Carbon::today();

        DailySearch::updateOrCreate(
            [
                'ip_address' => $ipAddress,
                'user_id' => $userId,
                'date' => $today,
            ],
            [
                'count' => DB::raw('count + 1'),
            ]
        );
    }

    /**
     * Get remaining searches for the user/guest
     */
    public function getRemainingSearches(Request $request): int
    {
        $status = $this->canSearch($request);
        return $status['remaining'];
    }

    /**
     * Reset search count (useful for testing or admin actions)
     */
    public function resetSearchCount(Request $request): void
    {
        $userId = $this->getUserId($request);
        $ipAddress = $request->ip();
        $today = Carbon::today();

        DailySearch::where('ip_address', $ipAddress)
            ->where('user_id', $userId)
            ->where('date', $today)
            ->update(['count' => 0]);
    }

    /**
     * Extract user ID from JWT token
     */
    private function getUserId(Request $request): ?int
    {
        $token = \App\Services\Utility\JWTTokenService::extractToken($request);

        if (!$token) {
            return null;
        }

        $decoded = \App\Services\Utility\JWTTokenService::decodeToken($token);

        if (!$decoded || \App\Services\Utility\JWTTokenService::isTokenBlacklisted($token)) {
            return null;
        }

        return $decoded['user_id'] ?? null;
    }
}
