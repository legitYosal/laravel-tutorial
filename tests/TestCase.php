<?php

namespace Tests;
use App\Models\User;
use Tests\BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected function setUpData() {
        $this->user = $this->getFakeUser();
    }

    public $basePathRoute;
    public $user;

    private function assertUserIsNull() {
        if (!$this->user || !($this->user instanceof User)) {
            throw new \Exception(
                'class must declare a ->user instance'
            );
        }
    }

    protected function baseAuthRequest() {
        $this->assertUserIsNull();
        return $this->actingAs($this->user, 'api')
            ->withHeaders($this->defaultHeaders);
    }

}
