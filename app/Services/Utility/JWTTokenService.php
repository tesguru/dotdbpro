<?php

namespace App\Services\Utility;

use App\Enums\RoleEnums;
use App\Traits\JsonResponseTrait;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class JWTTokenService
{
    use JsonResponseTrait;
    private const COOKIE_NAME = 'auth_token';
    private const BLACKLIST_PREFIX = 'blacklisted_token:';

    public static function generateToken(array $data): string
    {
        $token = (new JWTTokenService())->configureToken($data);
        Cookie::queue(
            self::COOKIE_NAME,
            $token,
            config('app.jwt_expiration') / 60,
            '/',
            null,
            true,
            true
        );
        return $token;
    }

    public static function extractToken(Request $request): ?string
    {
        return $request->cookie(self::COOKIE_NAME) ?? $request->bearerToken();
    }

    public static function decodeToken(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key(config('app.jwt_secret'), 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }

    private function configureToken(array $data): string
    {
        $payload = [
            'email_address' => $data['email_address'],
            'user_id'=>$data['user_id'],
            'username' => $data['username'],
            'iat' => now()->timestamp,
            'exp' => now()->addSeconds((int) config('app.jwt_expiration'))->timestamp,
        ];

        return JWT::encode($payload, config('app.jwt_secret'), 'HS256');
    }

    private static function blacklistToken(string $token, int $expiry): void
    {
        $cacheKey = self::BLACKLIST_PREFIX . md5($token);
        Cache::put($cacheKey, true, now()->addSeconds($expiry - now()->timestamp));
    }
    public static function isTokenBlacklisted(string $token): bool
    {
        return Cache::has(self::BLACKLIST_PREFIX . md5($token));
    }

    public static function refreshToken(Request $request, array $data): string
    {
        $oldToken = JWTTokenService::extractToken($request);
        $decodedToken = JWTTokenService::decodeToken($oldToken);
        self::blacklistToken($oldToken, $decodedToken['exp']);
        return JWTTokenService::generateToken($data);
    }
}
