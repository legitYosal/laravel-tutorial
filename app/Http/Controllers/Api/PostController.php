<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Requests\Post\PostIndexRequest;
use App\Http\Requests\Post\PostDestroyRequest;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\Requests\Post\PostImageRequest;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Like;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    //
    use \App\Traits\GetAuthObject;

    public $page_size = 10;

    public function index(PostIndexRequest $request) 
    {
        $queryset = Post::baseQuery();
        if ($request->has('sort_by_like')) {
            $queryset = $queryset->orderBy('likes_count', 'desc');
        }
        $queryset = $queryset->orderBy('created_at', 'desc');
        if($request->has('user_id')){
            $queryset = $queryset->where('user_id', $request->user_id);
        }
        $queryset = $queryset->paginate($this->page_size);
        return PostResource::collection($queryset);
    }
    public function show(PostIndexRequest $request, Post $post) 
    {
        return new PostResource($post);
    }
    public function store(PostStoreRequest $request) 
    {
        $validated_data = $request->validated();
        $validated_data['user_id'] = auth()->user()->id;

        $pictures = $validated_data['pictures'];
        unset($validated_data['pictures']);

        $new_post = Post::create($validated_data);
        foreach ($pictures as $picture) {
            PostPicture::save_and_create(
                $picture['file'], $new_post->id,
            );
        }

        return new PostResource($new_post);
    }
    
    public function update(PostUpdateRequest $request, Post $post) 
    { # this function only updates the post not it files
        $validated_data = $request->validated();
        $post->update($validated_data);
        return new PostResource($post);
    }
    public function destroy(PostDestroyRequest $request, Post $post) 
    {
        $post->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
    
    public function add_picture(PostImageRequest $request, Post $post) 
    {
        $file = $request->validated()['file'];

        return response()->json([
            'data'=> PostPicture::save_and_create(
                $file, $post->id,
            )
        ], Response::HTTP_CREATED);
        
    }
    public function delete_picture(PostImageRequest $request, Post $post, PostPicture $picture)
    {
        $picture->delete();
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
    
    public function toggle_like(Request $request, Post $post) {
        $user = $this->get_user();
        $liked = $post->likes()->where(['user_id' => $user->id])->first();
        if ($liked !== null) {
            $liked->delete();
            return response()->json([], Response::HTTP_NO_CONTENT);
        } else {
            if ($user->checkCanLike()) {
                $post->likes()->save(
                    New Like(['user_id' => $user->id])
                );
                $user->incrementUserLikes();
                return response()->json([], Response::HTTP_CREATED);
            } else {
                return response()->json(
                    $user->getLimitedLikeErrorData(), Response::HTTP_TOO_MANY_REQUESTS
                );
            }
        }
    }
}
