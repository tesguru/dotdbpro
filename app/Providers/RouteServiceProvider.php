<?php

namespace App\Providers;

use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }
    public const HOME = '/home';
    protected $namespace = 'App\Http\Controllers\api\v1';

    public function boot(): void
    {
        RateLimiter::for(
            'api',
            function (Request $request) {
                return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
            }
        );
        $this->authenticationRoutes();
        Route::middleware([ApiKeyMiddleware::class])
            ->group(
                function () {
                    $this->domainRoutes();
                    $this->analyticsRoutes();
                    $this->paymentRoutes();
                }
            );
    }
    public function authenticationRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->namespace($this->namespace)
            ->group(
                function () {
                    include base_path('routes/v1/authentication/authenticationroute.php');
                }
            );
    }
    public function domainRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->namespace($this->namespace)
            ->group(
                function () {
                    include base_path('routes/v1/domain/domainroute.php');
                }
            );
    }
      public function analyticsRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->namespace($this->namespace)
            ->group(
                function () {
                    include base_path('routes/v1/analytics/analyticsroute.php');
                }
            );
    }
     public function paymentRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->namespace($this->namespace)
            ->group(
                function () {
                    include base_path('routes/v1/payment/paymentroute.php');
                }
            );
    }
}
