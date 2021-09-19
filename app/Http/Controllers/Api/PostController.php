<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Http\Requests\Post\PostRequest;
use App\Http\Requests\Post\PostImageRequest;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Like;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    //
    public function index(PostRequest $request) 
    {
        $filtering_params = [];
        if ($request->has('user_id'))
            $filtering_params = $filtering_params + [
                'user_id' => $request->user_id,
            ];
        
        Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
        ])->validate();

        $queryset = Post::with('images')->withCount('likes');
        if ($request->has('sort_by_like')) {
            $queryset = $queryset->orderBy('likes_count', 'desc');
        }
        $queryset = $queryset->orderBy('created_at', 'desc');
        if($request->has('user_id')){
            $queryset = $queryset->where('user_id', $request->user_id);
        }
        $queryset = $queryset->paginate(10);
        return PostResource::collection($queryset);
    }
    public function show(PostRequest $request, Post $post) 
    {
        return new PostResource($post);
    }
    public function store(PostRequest $request) 
    {
        $validated_data = $request->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);

        if (sizeof($pictures) > 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be more than 3'],
            ]);          
        } else if (sizeof($pictures) < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be less than 1'],
            ]);          
        }

        $new_post = Post::create($validated_data);
        foreach ($pictures as $picture) {
            PostPicture::save_and_create(
                $picture['file'], $new_post->id,
            );
        }

        return new PostResource($new_post);
    }
    
    public function update(PostRequest $request, Post $post) 
    { # this function only updates the post not it files
        $validated_data = $request->validated();
        $post->update($validated_data);
        return new PostResource($post);
    }
    public function destroy(PostRequest $request, Post $post) 
    {
        $post->delete();
        return response()->json([], 204);
    }
    
    public function add_picture(PostImageRequest $request, Post $post) 
    {
        $file = $request->validated()['file'];

        $old_images = $post->images;
        if (sizeof($old_images) >= 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pictures' => ['Pictures most not be more than 3'],
            ]);          
        }

        return response()->json([
            'data'=> PostPicture::save_and_create(
                $file, $post->id,
            )
        ]);
        
    }
    public function delete_picture(PostImageRequest $request, Post $post, PostPicture $picture)
    {
        $picture->delete();
        return response()->json([], 204);
    }
    public function toggle_like(Request $request, Post $post) {
        $user_id = auth()->user()->id;
        $post_id = $post->id;
        $liked = Like::where(['user_id'=>$user_id, 'post_id'=>$post_id])->first();
        if ($liked !==null) {
            $liked->delete();
            return response()->json([], 204);
        } else {
            Like::create(['user_id'=>$user_id, 'post_id'=>$post_id]);
            return response()->json([], 200);
        }
    }
}
