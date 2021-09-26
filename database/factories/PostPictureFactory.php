<?php

namespace Database\Factories;

use App\Models\PostPicture;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostPictureFactory extends Factory
{
    use \App\Traits\FakeImage;
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
        $image_file = $this->randomFakeImage();
        return [
            'image_name'=>$image_file->name,
            'image_path'=> 'storage/'.$image_file->name,
            'post_id'=> function () {
                return Post::factory()->create()->id;
            },
        ];
    }
}
