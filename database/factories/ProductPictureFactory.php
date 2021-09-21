<?php

namespace Database\Factories;

use App\Models\ProductPicture;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPictureFactory extends Factory
{
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
        return [
            'image_name'=>$this->faker->name,
            'image_path'=> $this->faker->file('public/files'),
            'product_id'=>Product::factory()->create()->id,
        ];
    }
}
