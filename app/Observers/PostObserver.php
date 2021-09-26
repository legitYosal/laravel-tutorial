<?php

namespace App\Observers;

use App\Models\Post;
 
use Illuminate\Support\Facades\Http;
use App\Jobs\PostToExternalApi;

class PostObserver
{
    public function created(Post $post)
    {
        PostToExternalApi::dispatch(
            'https://gorest.co.in/public/v1/users',
            $post->toArray(),
        );
    }

}
