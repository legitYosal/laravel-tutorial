<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Post;
use App\Models\Product;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProductResource;


class IndexController extends Controller
{
    //
    private $caching_timeout = 5 * 60;

    public function index(Request $request)
    {
        # 10 last products
        # 10 most liked posts

        $popular_posts = Cache::remember('popular_posts', $this->caching_timeout, function () {
            error_log('awefawefa');
            return Post::baseQuery()
                ->orderBy('likes_count', 'desc')
                ->orderBy('created_at', 'desc')->take(10)->get();
        });

        
        $last_products = Cache::remember('last_posts', $this->caching_timeout, function () {
            return Product::with('prices')->with('images')
                ->orderBy('created_at', 'desc')->get();
        });
        
        return response()->json([
            'popular_posts' => PostResource::collection($popular_posts),
            'last_products' => ProductResource::collection($last_products),
        ]);
    }
}
