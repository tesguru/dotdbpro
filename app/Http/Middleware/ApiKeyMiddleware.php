<?php

namespace App\Http\Middleware;

use App\Traits\JsonResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    use JsonResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-key');
        if (!$apiKey || $apiKey != config('app.api_key')) {
            return $this->errorResponse(statusCode: 401, message: 'Invalid api key');
        }
        return $next($request);
    }
}
