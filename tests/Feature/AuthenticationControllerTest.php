<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationControllerTest extends TestCase
{
    public $basePathRoute = '/api/auth/';

    protected function setUpData() {}

    public function test_register_user()
    {
        $payload = [
            'name' => $this->faker->name,
            'mobile' => '09'.$this->faker->unique()->numberBetween(
                100000000,999999999
            ),
            'password' => Str::random(20),
        ];

        $response = $this->withHeaders(
            $this->defaultHeaders
        )->post(
            $this->basePathRoute.'register/',
            $data=$payload,
        );

        $response->assertStatus(201);
        $response
            ->assertJsonPath(
                'data.mobile', $payload['mobile'],
            );
        
        $response_data = $response->decodeResponseJson();

        $this->assertDatabaseHas('users', [
            'id' => $response_data['data']['id'],
        ]);
    }
    public function test_login_user() 
    {
        $password = Str::random(20);
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $payload = [
            'mobile' => $user->mobile,
            'password' => $password,
        ];

        $response = $this->withHeaders(
            $this->defaultHeaders
        )->post(
            $this->basePathRoute.'token/obtain/',
            $data=$payload,
        );
        $response->assertStatus(200);
    }
}
