<?php

namespace App\Modules\Customer\Tags\Controllers;

use App\Models\Tag\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Utils\Utils;
use Illuminate\Validation\Rule;

class TagsController extends Controller
{
    public function index()
    {
        $tags = Tag
            ::whereUserId(Utils::getCurrentUserId())
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($tags);
    }

    public function store()
    {
        $user_id = Utils::getCurrentUserId();
        $this->validate(request(), [
            'name' => [
                'required',
                Rule::unique('tags')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                })
            ],
            'value' => 'required',
        ]);

        $data = request()->all();
        $data['user_id'] = $user_id;

        $tag = Tag::create($data);

        return response()->json([
            'message' => 'Successfully created'
        ]);
    }

    public function update(Tag $tag)
    {
        $user_id = Utils::getCurrentUserId();
        $this->validate(request(), [
            'name' => [
                'required',
                Rule::unique('tags')->where(function ($query) use ($user_id, $tag) {
                    return $query->where('user_id', $user_id);
                })->ignore($tag->id)
            ],
            'value' => 'required',
        ]);

        $tag->fill(request()->all())->save();

        return response()->json([
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * @param Tag $tag
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'message' => 'The tag has been successfully deleted'
        ]);
    }
}
