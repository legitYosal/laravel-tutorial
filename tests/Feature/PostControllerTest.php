<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;

use App\Models\Post;
use App\Models\PostPicture;
use App\Models\Like;
use Illuminate\Testing\Fluent\AssertableJson;

class PostControllerTest extends FeatureTestCase
{
    use \Tests\Traits\MockHttpPostTrait;

    public $basePathRoute = '/api/post/';
    private $PicturesPathExtenstion = '/picture/';
    private $LikesPathExtenstion = '/like/'; 
    

    public function setUpData()
    {
        $this->mockHttpPost();
        $this->user = $this->getFakeUser();
        $this->post = Post::factory()->for(
            $this->user, 'user'
        )->create();
    }

    public function test_get_method() {
        $response = $this->baseAuthRequest()
            ->get($this->basePathRoute.$this->post->id.'/');
        $response
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('data')
                    ->where('data.id', $this->post->id)
                    ->where('data.title', $this->post->title)
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

        $this->assertDatabaseHas('posts', [
            'id' => $response_data['data']['id'],
        ]);
    }

    public function test_delete_method() {
        $response = $this->baseAuthRequest()
        ->delete(
            $this->basePathRoute.$this->post->id.'/',
        );
        $response->assertStatus(204);
    }

    public function test_update_method() {
        $putPayload = [
            'title' => $this->faker->unique()->name(),
        ];

        $response = $this->baseAuthRequest()
            ->put(
                $this->basePathRoute.$this->post->id.'/',
                $data=$putPayload,
            );
        $response->assertStatus(200);
        $this->post->refresh();
        $this->assertEquals(
            $putPayload['title'],
            $this->post->title,
        );
    }

    public function test_add_image_method() {
        $imagePayload = [
            'file' => $this->randomFakeImage(),
        ];
        $response = $this->baseAuthRequest()
            ->post(
                $this->basePathRoute.$this->post->id.$this->PicturesPathExtenstion, 
                $data=$imagePayload
            );
        $response->assertStatus(201);

        $response_data = $response->decodeResponseJson();

        $this->assertDatabaseHas('post_pictures', [
            'id' => $response_data['data']['id'],
        ]);
    }
    public function test_delete_image_method() {
        $picture = PostPicture::factory()->for(
            $this->post, 'post'
        )->create();

        $response = $this->baseAuthRequest()
        ->delete(
            $this->basePathRoute.$this->post->id.$this->PicturesPathExtenstion.$picture->id.'/',
        );
        $response->assertStatus(204);
    }

    public function test_like_post_method() {
        $response = $this->baseAuthRequest()
        ->post(
            $this->basePathRoute.$this->post->id.$this->LikesPathExtenstion,
        );
        $response->assertStatus(201);
        $this->assertTrue(
            $this->post->likes()->where(
                'user_id', $this->user->id
            )->exists()
        );
    }
    public function test_revoke_like_method() {
        $this->post->likes()->save(
            New Like([
                'user_id'=>$this->user->id
            ])
        );

        $response = $this->baseAuthRequest()
        ->post(
            $this->basePathRoute.$this->post->id.$this->LikesPathExtenstion,
        );
        $response->assertStatus(204);
        $this->assertFalse(
            $this->post->likes()->where(
                'user_id', $this->user->id
            )->exists()
        );
    }
}
