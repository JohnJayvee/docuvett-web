<?php

namespace App\Modules\_common_\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getCurrentUser(): JsonResponse
    {
        $user = Auth::user()->load(['contact', 'companies']);
        $user->roleList = $user->roles()->pluck('name');
        $user->permissionList = $user->allPermissions()->pluck('name');

        return response()->json($user->toArray());
    }
}
