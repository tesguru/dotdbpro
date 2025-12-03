<?php

namespace App\Http\Middleware;

use App\Enum\GeneralWord;
use App\Traits\JsonResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministrativeMiddleware
{
    use JsonResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminRequest = $request->get('tokenPayload', [])['userType'] ?? null;
        if ($adminRequest != GeneralWord::HOSPITAL_MANAGER->value) {
            return $this->errorResponse(403, 'Access Denied, Only Administrative can access this resource');
        }
        return $next($request);
    }
}
