<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use Illuminate\Support\Str;

class InternalUserControllerTest extends FeatureTestCase
{
    public $basePathRoute = '/api/internal/private/change-user-notif-token/';
    public function test_notif()
    {
        $payload = [
            'user_id' => $this->user->id,
            'token' => Str::random(100),
        ];
        $response = $this->withHeaders(
            $this->defaultHeaders + [
                'Authorization' => 'Bearer '.config(
                    'app.secret_internal_service_bearer_token'
                ),
            ]
        )->put($this->basePathRoute, $data=$payload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('notification_tokens', [
            'user_id' => $payload['user_id'],
            'token' => $payload['token'],
        ]);
    }
}
