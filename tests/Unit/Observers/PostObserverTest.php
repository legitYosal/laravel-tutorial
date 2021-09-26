<?php

namespace Tests\Unit\Observers;
use Tests\TestCase;

use Illuminate\Support\Facades\Bus;

use App\Jobs\PostToExternalApi;
use App\Models\Post;

class PostObserverTest extends TestCase
{
    public function test_created()
    {
        Bus::fake();
        Post::factory()->create();
        Bus::assertDispatched(PostToExternalApi::class);
    }
}
