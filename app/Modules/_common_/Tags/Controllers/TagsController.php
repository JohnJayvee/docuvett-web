<?php

namespace App\Modules\_common_\Tags\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function getAutocomplete(Request $request)
    {
        $search = $request->input('search');
        $search = is_string($search) ? $search : '';
        $tags = [];
        if ($search != '') {
            $tags = Tag::containing($search)->limit(10)->get();
        }
        return response()->json($tags);
    }
}