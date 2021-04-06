<?php

namespace App\Http\Middleware;

use App\Libraries\Utils\Utils;
use App\Models\Permission\Permission;
use Auth;
use Cache;
use Carbon\Carbon;
use Closure;
use JWTAuth;
use Laratrust;
use Mockery\Exception;
use Route;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $accessKey = Route::currentRouteName();

        try {
            /* If route doesn't have name, we didn't check permissions in this case */
            if (empty($accessKey) || Laratrust::isAbleTo($accessKey)) {
                return $next($request);
            }

            if ($request->ajax() || $request->pjax()) {
                $headers = ($time = $this->tokenExpiredAt())
                    ? ['SESSION_EXPIRED_AT' => Carbon::parse($time)->toDateTimeString(), 'GRACE_PERIOD' => Carbon::parse($time)->diffInSeconds()]
                    : [];

                abort(403, $this->prepareResponse($accessKey), $headers);
            }

            return Auth::check() ? redirect()->route('login') : redirect()->home();
        } catch (Exception $e) {
            abort(403, $this->prepareResponse($accessKey));
        }
    }

    private function tokenExpiredAt(): ?string
    {
        ['sub' => $userID] = JWTAuth::payload()->toArray();

        return Cache::get(
            'token_expired_at_for_user_' . $userID
        );
    }

    /**
     * Prepare text response in the human format in related ACL problems
     *
     * @param string|null $accessKey
     *
     * @return string
     */
    private function prepareResponse($accessKey): string
    {
        $permissionName = '';
        try {
            if ($user = Utils::getCurrentUser()) {
                $roles = $user->roles()->get()->implode('display_name', ',');
                if (($permission = Permission::whereName($accessKey)->first('display_name')) && ($permissionName = $permission->display_name)) {
                    $permissionName = '`' . implode(' / ', explode(' ', $permissionName, 2)) . '` ';
                }

                $message = "Access denied. Role `$roles` doesn't have permission $permissionName($accessKey)";
            }
        } catch (\Exception $exception) {
        }

        return $message ?? (empty($accessKey) ? 'Access denied' : "Access denied for `$accessKey`");
    }
}