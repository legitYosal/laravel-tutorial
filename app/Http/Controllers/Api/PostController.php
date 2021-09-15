<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Like;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    //
    public function index(Request $request) 
    {
        $filtering_params = [];
        if ($request->has('user_id')) {
            $filtering_params = $filtering_params + [
                'user_id' => $request->user_id,
            ];
        }
        
        $validator = Validator::make($filtering_params, [
            'user_id' => ['sometimes', 'exists:users,id'],
        ]);
        $validator->validate();
        $filters = $validator->validated();
        
        $queryset = Post::with('images')->withCount('likes');
        if ($request->has('sort_by_like')) {
            $queryset = $queryset->orderBy('likes_count', 'desc');
        }
        $queryset = $queryset->orderBy('created_at', 'desc');
        if(sizeof($filters) > 0){
            $queryset = $queryset->where($filters);
        }
        $queryset = $queryset->paginate(10);
        return PostResource::collection($queryset);
    }
    public function show(Request $request, Post $post) 
    {
        return new PostResource($post);
    }
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:256'],
            'description' => ['required', 'max:2048'],
            'pictures' => ['required'],
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
    
    public function update(Request $request, Post $post) 
    { # this function only updates the post not it files
        if (auth()->user()->id !== $post->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'max:256'],
            'description' => ['sometimes', 'max:2048'],
            // 'pictures.*.file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $validated_data = $validator->validated();

        // if (array_key_exists('pictures', $validated_data)) {
        //     $pictures = $validated_data['pictures'];
        //     unset($validated_data['pictures']);

        // }
        $post->update($validated_data);
        return new PostResource($post);
        // $category->update($request->all());
        // return $category;
    }
    public function destroy(Request $request, Post $post) 
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }
        $post->delete();
        return response()->json([], 204);
    }
    
    public function add_picture(Request $request, Post $post) 
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'mimes:jpeg,png,jpg,gif,svg', 'image', 'max:2048'], 
        ]);
        $validator->validate();
        $file = $validator->validated()['file'];

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
        

        // return new PostResource($post);
    }
    public function delete_picture(Request $request, Post $post, PostPicture $picture)
    {
        if ($picture->post_id !== $post->id || $post->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to access'
            ], 403);
        }
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
