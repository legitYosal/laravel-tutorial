<?php

namespace Database\Factories;

use App\Models\PostPicture;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostPictureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostPicture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'image_name'=>$this->faker->name,
            'image_path'=> $this->faker->file('public/files'),
            'post_id'=>Post::factory()->create()->id,
        ];
    }
}
