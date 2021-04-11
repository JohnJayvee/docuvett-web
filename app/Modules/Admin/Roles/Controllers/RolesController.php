<?php

namespace App\Modules\Admin\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Utils\Utils;
use App\Models\Role\Role;
use function foo\func;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        list($column, $order) = explode(',', $request->input('sortBy', 'id,asc'));
        $pageSize = (int) $request->input('pageSize', 10);
        $search = $request->input('search', '');

        $roles = Role
            ::where('display_name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhere('level', 'like', '%' . $search . '%')
            ->with('permissions')
            ->orderBy($column, $order)
            ->paginate($pageSize);

        $roles->each(function($role){
            $role['permission_ids'] = $role->permissions->pluck('id')->toArray();
            unset($role->permissions);
        });

        return response()->json(
            ['data' => $roles ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Role $role)
    {
        $this->validate($request, [
            'display_name' => 'required',
            'level' => 'required',
        ]);

        $role->name = uniqid();
        $role->display_name = $request->input('display_name');
        $role->level = $request->input('level');
        $role->description = $request->input('description');
        $role->dashboard = $request->input('dashboard', null);
        $role->save();

        $permission_ids = request()->input('permission_ids', []);
        $role->syncPermissions($permission_ids);

        return response()->json([
            'message' => 'Successfully created'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Role $role
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Role $role)
    {
        $this->validate(request(), [
            'display_name' => 'required',
            'level' => 'required',
        ]);

        $role->display_name = request()->input('display_name');
        $role->level = request()->input('level');
        $role->description = request()->input('description');
        $role->dashboard = request()->input('dashboard', null);
        $role->save();

        $permission_ids = request()->input('permission_ids', []);
        $role->syncPermissions($permission_ids);

        return response()->json([
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $initialRoles = config('laratrust_seeder.role_structure');
        $result = [
            'data' => ['message' => 'The role has been successfully deleted'],
            'code' => Response::HTTP_ACCEPTED
        ];

        // Check if role is initial
        if (!empty($initialRoles[$role->name])) {
            $result = [
                'data' => ['message' => "Can't delete initial role"],
                'code' => 422
            ];
        } elseif (count($role->users)) {
            $result = [
                'data' => ['message' => "This item is used"],
                'code' => 422
            ];
        } else {
            $role->users()->sync([]); // Delete relationship data
            $role->permissions()->sync([]); // Delete relationship data
            $role->forceDelete();
        }

        return response()->json($result['data'], $result['code']);
    }
    /**
     * @param Role $role
     * @return array
     */
    public function get(Role $role)
    {
        $role['permission_ids'] = $role->permissions->pluck('id')->toArray();

        return response()->json($role);
    }

    public function autocomplete(Request $request)
    {
        $user = Utils::getCurrentUser();
        $role = $user ? $user->roles->first() : null;
        $search = $request->input('search', '');
        $roles = Role
            ::where('display_name', 'like', "%{$search}%")
            ->when($role, function ($query) use ($role) {
                return $query->where('level', '>=', $role->level);
            })
            ->orderBy('display_name', 'asc')
            ->get();
        return response()->json($roles);
    }
}
