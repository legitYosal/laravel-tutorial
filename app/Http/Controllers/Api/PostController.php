<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //
    public function index(Request $request) 
    {
        $filtering_params = [];
        if ($request->has('user_id')) {
            $filtering_params = $filtering_params + [
                'user_id' => $request->user_id
            ];
        }
        $validator = Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
        ]);
        $validator->validate();
        $data = $validator->validated();

        $queryset = Post::all();
        foreach ($data as $key => $value) {
            $queryset = $queryset->where($key, $value);
        }
        return $queryset->sortByDesc('created_at');
    }
    public function show(Request $request, Post $post) 
    {
        return $post;
    }
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:256'],
            'description' => ['required', 'max:2048'],
            'pictures.*.file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $validated_data = $validator->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);

        if (sizeof($pictures) > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be more than 3'],
            ]);          
        }
        $new_post = Post::create($validated_data);
        foreach ($pictures as $picture) {
            PostPicture::save_and_create(
                $picture['file'], $new_post->id,
            );
        }

        return $new_post;
    }
    // public function update(Request $request, Category $category) 
    // {

    //     $category->update($request->all());
    //     return $category;
    // }
    // public function destroy(Request $request, Category $category) 
    // {
    //     $category->delete();
    //     return 204;
    // }
    
}
