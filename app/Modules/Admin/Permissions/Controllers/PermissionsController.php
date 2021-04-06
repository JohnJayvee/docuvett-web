<?php

namespace App\Modules\Admin\Permissions\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission
            ::orderBy('display_name', 'asc')
            ->get();

        return response()->json($permissions);
    }
}
