<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
use Tests\TestCase;


class IndexControllerTest extends TestCase
{
    public $basePathRoute = '/api/index/';
    public function setUpData()
    {
        $this->user = $this->getFakeUser();
        $this->posts = Post::factory()->for(
            $this->user, 'user'
        )->has(
            PostPicture::factory()->count(3), 'images'
        )->count(25)->create();
        $this->products = Product::factory()->for(
            $this->user, 'user'
        )->has(
            ProductPicture::factory()->count(2), 'images'
        )->has(
            ProductPrice::factory()->count(5), 'prices'
        )->count(25)->create();
    }

    public function test_index() {
        $response = $this->baseAuthRequest()
            ->get($this->basePathRoute);
        $response->assertStatus(200);
    }
}
