<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\JWTAuth;

class RefreshToken extends BaseMiddleware
{
    private $authTtlSeconds;

    public function __construct(JWTAuth $auth)
    {
        parent::__construct($auth);

        $this->authTtlSeconds = config('jwt.ttl') * 60;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request); // Check presence of a token.

        try {
            if (!$this->auth->parseToken()->authenticate()) { // Check user not found. Check token has expired.
                throw new UnauthorizedHttpException('jwt-auth', 'User not found');
            }

            $tokenExpiredAtTimestamp = (int)$this->auth->payload()->get('exp');

            // Do not refresh token if it is a request for notifications
            if ($request->route()->getName() === 'main.notifications') {
                return $next($request); // Token is valid. User logged. Response without any token.
            }

            if ($this->tokenTTLPassedPercent($tokenExpiredAtTimestamp) <= config('jwt.soft_refresh_percent')) {
                return $next($request); // Token is valid. User logged. Response without any token.
            }

            return $this->setAuthenticationHeader($next($request), $this->refreshTokenWithoutBlacklist()); // Token is valid. User logged. Response with fresh token
        } catch (TokenExpiredException $t) { // Token expired. User not logged.
            $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $key = 'block_refresh_token_for_user_' . $payload['sub'];
            $cachedBefore = (int)Cache::has($key);
            if ($cachedBefore) { // If a token alredy was refreshed and sent to the client in the last JWT_BLACKLIST_GRACE_PERIOD seconds.
                Auth::onceUsingId($payload['sub']); // Log the user using id.

                return $next($request); // Token expired. Response without any token because in grace period.
            }
            try {
                $newtoken    = $this->auth->refresh(); // Get new token.
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt   = Carbon::now()->addSeconds($gracePeriod);
                Cache::put($key, $newtoken, $expiresAt);
                Cache::put('token_expired_at_for_user_' . $payload['sub'], $expiresAt->toIso8601String(), $expiresAt);
            } catch (JWTException $e) {
                throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
            }
        }

        $response = $next($request); // Token refreshed and continue.

        return $this->setAuthenticationHeader($response, $newtoken); // Response with new token on header Authorization.
    }

    private function refreshTokenWithoutBlacklist(): string
    {
        $token = $this->auth->manager()->setBlacklistEnabled(false)->refresh(
            $this->auth->getToken()
        );

        auth()->login(
            $this->auth->setToken($token)->toUser()
        );

        return $token;
    }

    private function tokenTTLPassedPercent(int $expiredAt): float
    {
        $passedSeconds = abs(
            now()->unix() - ($expiredAt - $this->authTtlSeconds)
        );

        return round(($passedSeconds * 100) / $this->authTtlSeconds, 1);
    }
}