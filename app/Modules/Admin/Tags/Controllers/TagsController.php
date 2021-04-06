<?php

namespace App\Modules\Admin\Tags\Controllers;

use App\Models\Tag\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{

    public function index(Request $request)
    {
        list($column, $order) = explode(',', $request->input('sortBy', 'id,asc'));
        $search = $request->input('search');
        $search = is_string($search) ? $search : '';
        $pageSize = (int) $request->input('pageSize', 10);

        $list = Tag
            ::containing($search)
            ->orderBy($column, $order)
            ->paginate($pageSize);

        return $list;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'displayName' => 'required',
        ];

        $this->validate($request, $rules);

        $tag = Tag::findOrCreateFromString($request->displayName);
        if($tag->id) {
            return response()->json([
                'message' => "The given data was invalid.",
                'errors' => [
                    'displayName' => ['tag name already exists']
                ]
            ], 422);
        }
        $tag->save();

        return response()->json([
            'message' => 'Successfully created'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $rules = [
            'displayName' => 'required',
        ];

        $this->validate($request, $rules);

        $foundTag = Tag::findFromString($request->displayName);
        if($foundTag && $foundTag->id != $tag->id) {
            return response()->json([
                'message' => "The given data was invalid.",
                'errors' => [
                    'displayName' => ['tag name already exists']
                ]
            ], 422);
        }

        $tag->name = $request->displayName;
        $tag->save();

        return response()->json([
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'Successfully deleted'
        ]);
    }
}
