<?php

namespace App\Http\Middleware;

use App\Services\Utility\JWTTokenService;
use App\Traits\JsonResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenMiddleware
{
    use JsonResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = JWTTokenService::extractToken($request);
        if (!$token || JWTTokenService::isTokenBlacklisted($token)) {
            return $this->errorResponse(401, message: 'Unauthorized - No token');
        }

        $decoded = JWTTokenService::decodeToken($token);
        if (!$decoded) {
            return $this->errorResponse(401, message: 'Unauthorized - Invalid token');
        }
        $request->setUserResolver(fn () => (object) $decoded);
        return $next($request);
    }
}
