<?php

namespace App\Modules\Admin\Audits\Controllers;

use App\Models\Audit\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return
     */
    public function index(Request $request)
    {
        list($column, $order) = explode(',', $request->input('sortBy', 'id,desc'));
        $search = $request->input('search', '');
        $pageSize = (int) $request->input('pageSize', 10);

        // Allow search by ID. Example "10:User"
        preg_match('/^(\d+):(.*)/', $search, $matches);
        $id = null;
        if (count($matches) === 3) {
            $search = $matches[2];
            $id = $matches[1];
        }

        $list = Audit
            ::when($id, function($query) use ($id) {
                return $query->where('auditable_id', '=', $id);
            })
            ->when($search, function($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    return $q
                        ->where('event', 'like', "%$search%")
                        ->orWhere('auditable_type', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%");
                });
            })
            ->with(['user'])
            ->orderBy($column, $order)
            ->paginate($pageSize);

        return $list;
    }


    /**
     * @param string $auditableType
     * @param $auditableId
     * @return JsonResponse
     */
    public function getByAuditableType($auditableType, $auditableId)
    {
        $audits = Audit::with('user')
            ->where('auditable_type', $auditableType)
            ->where('auditable_id', $auditableId)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json(compact('audits'));
    }

}
