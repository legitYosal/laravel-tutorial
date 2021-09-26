<?php

namespace Tests\Unit\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use App\Jobs\MostLikedWinner;

class MostLikedWinnerTest extends TestCase
{
    use \Tests\Traits\MockHttpPostTrait;

    public function test_job()
    {
        Bus::fake();

        $today = now();
        $yesterday = now()->subDays(1);
        $today_posts = Post::factory()->count(25)->create([
            'created_at' => $today,
            'updated_at' => $today,
        ]);
        $today_champ = $today_posts[0];

        $yesterday_posts = Post::factory()->count(25)->create([
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);
        $yesterday_champ = $yesterday_posts[0];

        for ($i = 0; $i < 10; $i ++) {
            $today_champ->likes()->save(
                New Like([
                    'user_id'=>User::factory()->create()->id,
                ])
            );
        }
        for ($i = 0; $i < 20; $i ++) {
            $yesterday_champ->likes()->save(
                New Like([
                    'user_id'=>User::factory()->create()->id,
                ])
            );
        }

        $jobObject = New MostLikedWinner();

        $this->assertEquals(
            $today_champ->id,
            $jobObject->getTodayChampion()->id,
        );

        Http::shouldReceive('post');
        $jobObject->handle();
    }
}
