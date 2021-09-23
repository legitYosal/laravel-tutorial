<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\User;
use App\Models\Post;

use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                User::factory()->create()->id;
            },
            'post_id' => function () {
                Post::factory()->create()->id;
            } 
        ];
    }
}
