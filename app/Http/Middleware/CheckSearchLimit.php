<?php

namespace App\Http\Middleware;
use App\Services\Utility\SearchLimitService;

use App\Traits\JsonResponseTrait;
use Closure;
use Illuminate\Http\Request;

class CheckSearchLimit
{
    use JsonResponseTrait;

    private SearchLimitService $searchLimitService;

    public function __construct(SearchLimitService $searchLimitService)
    {
        $this->searchLimitService = $searchLimitService;
    }

    public function handle(Request $request, Closure $next)
    {
        $searchStatus = $this->searchLimitService->canSearch($request);

        if (!$searchStatus['can_search']) {
            return $this->errorResponse(
                'Daily search limit reached. Please login to continue searching.',
                [
                    'limit_reached' => true,
                    'count' => $searchStatus['count'],
                    'limit' => $searchStatus['limit'],
                    'requires_login' => !$searchStatus['is_authenticated'],
                ],
                429 // Too Many Requests
            );
        }

        // Add search info to request for controllers to use
        $request->merge(['search_status' => $searchStatus]);

        return $next($request);
    }
}
