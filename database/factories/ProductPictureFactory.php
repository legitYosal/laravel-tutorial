<?php

namespace Database\Factories;

use App\Models\ProductPicture;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPictureFactory extends Factory
{
    use \App\Traits\FakeImage;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductPicture::class;

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
            'product_id'=> function () {
                Product::factory()->create()->id;
            },
        ];
    }
}
