<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPrice;
use Tests\Feature\Traits\MockHttpPost as TraitsMockHttpPost;
use Tests\TestCase;
use Tests\Traits\MockHttpPostTrait;

class IndexControllerTest extends TestCase
{
    use MockHttpPostTrait;

    public $basePathRoute = '/api/index/';
    public function setUpData()
    {
        $this->mockHttpPost();

        $this->user = $this->getFakeUser();

        $this->posts = Post::factory()->has(
            PostPicture::factory()->count(3), 'images'
        )->count(25)->create(['user_id'=>$this->user->id]);
        
        $this->products = Product::factory()->for(
            $this->user, 'user'
        )->has(
            ProductPicture::factory()->count(2), 'images'
        )->has(
            ProductPrice::factory()->count(5), 'prices'
        )->count(25)->create(['user_id' => $this->user->id]);

    }

    public function test_index() {
        $response = $this->baseAuthRequest()
            ->get($this->basePathRoute);
        $response->assertStatus(200);
    }
}
