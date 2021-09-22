<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
// use App\Models\Like;
use Illuminate\Testing\Fluent\AssertableJson;

class ProductControllerTest extends TestCase
{
    public $basePathRoute = '/api/product/';
    private $PicturesPathExtenstion = '/picture/';
    

    public function setUpData()
    {
        $this->user = $this->getFakeUser();
        $this->product = Product::factory()->for(
            $this->user, 'user'
        )->create();
        $this->price = ProductPrice::factory()->for(
            $this->product, 'product'
        )->create();
    }

    public function test_get_method() {
        $response = $this->baseAuthRequest()
            ->get($this->basePathRoute.$this->product->id.'/');
        $response
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data')
                    ->where('data.id', $this->product->id)
                    ->where('data.title', $this->product->title)
                    ->etc()
            );
    }
    public function test_post_method() { 
        $postPayload = [
            'title' => $this->faker->unique()->name(),
            'description' => $this->faker->text(),
            'pictures' => [
                [
                    'file' => $this->randomFakeImage(),
                ],
            ],
            'price' => [
                'bought_price' => $this->faker->numberBetween(100, 2000),
                'selling_price' => $this->faker->numberBetween(100, 2000)
            ]
        ];

        $response = $this->baseAuthRequest()
            ->post(
                $this->basePathRoute, 
                $data=$postPayload
            );
        $response->assertStatus(201);
        $response
            ->assertJsonPath(
                'data.title', $postPayload['title'],
            );
        
        $response_data = $response->decodeResponseJson();

        $this->assertDatabaseHas('products', [
            'id' => $response_data['data']['id'],
        ]);
    }

    public function test_delete_method() {
        $response = $this->baseAuthRequest()
        ->delete(
            $this->basePathRoute.$this->product->id.'/',
        );
        $response->assertStatus(204);
    }

    public function test_update_method() {
        $putPayload = [
            'title' => $this->faker->unique()->name(),
        ];

        $response = $this->baseAuthRequest()
            ->put(
                $this->basePathRoute.$this->product->id.'/',
                $data=$putPayload,
            );
        $response->assertStatus(200);
        $this->product->refresh();
        $this->assertEquals(
            $putPayload['title'],
            $this->product->title,
        );
    }

    public function test_add_image_method() {
        $imagePayload = [
            'file' => $this->randomFakeImage(),
        ];
        $response = $this->baseAuthRequest()
            ->post(
                $this->basePathRoute.$this->product->id.$this->PicturesPathExtenstion, 
                $data=$imagePayload
            );
        $response->assertStatus(201);

        $response_data = $response->decodeResponseJson();

        $this->assertDatabaseHas('product_pictures', [
            'id' => $response_data['data']['id'],
        ]);
    }
    public function test_delete_image_method() {
        $picture = ProductPicture::factory()->for(
            $this->product, 'product'
        )->create();

        $response = $this->baseAuthRequest()
        ->delete(
            $this->basePathRoute.$this->product->id.$this->PicturesPathExtenstion.$picture->id.'/',
        );
        $response->assertStatus(204);
    }

}
