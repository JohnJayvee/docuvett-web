<?php

namespace App\Modules\Admin\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Utils\Utils;
use App\Mail\UserRegistered;
use App\Models\Role\Role;
use App\Models\User\Queries\UserQueries;
use App\Models\User\User;
use App\Modules\_common_\Auth\Controllers\RegisterController;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mail;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;


class UsersController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        $curUserLevel = Utils::getCurrentUser()->max_user_level;
        $query = $this->buildQuery($request);

        $users = $query->get()
            ->filter(function (User $value) use ($curUserLevel) {
                return $curUserLevel >= $value->max_user_level;
            });

        foreach ($users as $user) {
            $role = $user->roles->pluck('id');
            $role = count($role) > 0 ? $role[0] : 0;
            $user['role'] = $role;
            $tags = [];
            if ($user->tags->count()) {
                $tags = $user->tags->map(function ($tag) {
                    return $tag->name;
                });
            }
            unset($user->tags);
            $user['tagList'] = $tags;
        }

        $page = $request->input('page', 1);
        $pageSize = (int)$request->input('pageSize', 10);

        return new LengthAwarePaginator(
            array_values($users->forPage($page, $pageSize)->toArray()),
            $users->count(),
            $pageSize,
            $page,
            ['path' => config('app.url')]
        );
    }

    private function buildQuery(Request $request): Builder
    {
        list($column, $order) = explode(',', $request->input('sortBy', 'id,asc'));
        $search = $request->input('search', '');
        return User
            ::when($search, function ($query) use ($search) {
                return $query
                    ->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            })
            ->with(['tags', 'roles'])
            ->orderBy($column, $order);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $request = Utils::updateRequestPhone($request);
        $rules = RegisterController::$rules;
        $rules['role'] = 'required';

        $this->validate($request, $rules);
        $data = $request->all();
        $errors = UserQueries::getErrors($data);
        if (count($errors) > 0) {
            return response()->json([
                'errors' => $errors
            ], 422);
        }

        $this->roleIsAvailableForCurrentUser($request->role);

        $user = User::create($data);
        $user->syncRoles([$request->input('role')]);

        if ($request->hasFile('file')) {
            $user->avatar = Storage::url($request->file('file')->storePublicly('public/users/' . $user->id));
            $user->save();
        }

        Mail::send(new UserRegistered($user, $data['password'], false));

        return response()->json([
            'message' => 'Successfully created'
        ]);
    }

    /**
     * Check that requested role is available for callable user
     *
     * @param int|null $roleId
     *
     * @return bool
     * @throws Exception
     */
    public function roleIsAvailableForCurrentUser(?int $roleId): bool
    {
        $user = Utils::getCurrentUser();
        $rawRoles = config('laratrust_seeder.role_structure');
        $rolesLevels = array_keys($rawRoles);
        $requestedRole = Role::find($roleId);

        if ($user && $requestedRole) {
            $requestedRoleLevel = array_search($requestedRole->name, $rolesLevels);
            if ($requestedRoleLevel !== false) {
                if ($requestedRoleLevel <= $user->max_user_level) {
                    return true;
                }
            }
        }

        throw new UnprocessableEntityHttpException("Current user doesn't meet required permissions to set selected role.");
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request = Utils::updateRequestPhone($request);
        $rules = [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required',
            'phone' => 'sometimes|phone:AUTO,AU,mobile,fixed_line_or_mobile|unique:users,phone,' . $user->id,
        ];
        if ($request->password || $request->password_confirmation) {
            $rules['password'] = 'required|min:4|confirmed';
        }
        $this->validate($request, $rules);

        $data = $request->all();

        $errors = UserQueries::getErrors($data);
        if (count($errors) > 0) {
            return response()->json([
                'errors' => $errors
            ], 422);
        }

        $this->roleIsAvailableForCurrentUser($request->role);

        if ($request->hasFile('file')) {
            $data['avatar'] = Storage::url($request->file('file')->storePublicly('public/users/' . $user->id));
        }

        $user->fill($data)->save();
        if (isset($data['tags']) && is_array($data['tags'])) {
            $user->syncTags($data['tags']);
        }

        if ($request->input('role')) {
            $user->syncRoles([$request->input('role')])->save();
        }

        return response()->json([
            'message' => 'Successful edited'
        ]);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $rawRoles = config('laratrust_seeder.role_structure');
        $rolesLevels = array_keys($rawRoles);
        try {
            $roleIsSuitable = false;
            if (isset($rolesLevels[$user->max_user_level])) {
                $userTopRoleName = $rolesLevels[$user->max_user_level];
                $userTopRole = Role::whereName($userTopRoleName)->first();
                if ($userTopRole) {
                    $roleIsSuitable = $this->roleIsAvailableForCurrentUser($userTopRole->id);
                }
            }
            if (!$roleIsSuitable) {
                throw new Exception(); // Just initiate to raise Not enough permissions exception
            }
        } catch (Exception $exception) {
            throw new UnprocessableEntityHttpException("Current user doesn't have enough permissions to delete selected user.");
        }

        try {
            Storage::deleteDirectory('public/users/' . $user->id);
        } catch (Exception $e) {
        }

        $user->syncRoles([])->delete();

        return response()->json([
            'message' => 'The user has been successfully deleted'
        ], Response::HTTP_ACCEPTED);
    }

    public function customers(): JsonResponse
    {
        $customers = User::byRoleAndOrdered('customer')->get();

        return response()->json($customers);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function get(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function getAutocomplete(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $search = is_string($search) ? $search : '';
        $users = [];
        if ($search != '') {
            $users = User::where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->limit(10)->get();
        }
        return response()->json($users);
    }

    public function loginAs(Request $request): JsonResponse
    {
        try {
            $user = User::findOrFail($request->user_id);
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);

            $showMessages = $user->isAbleTo('system-messages.list');

            return response()
                ->json([
                    'token' => "Bearer $token",
                    'system_messages' => $showMessages
                ])
                ->header('Authorization', $token);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
