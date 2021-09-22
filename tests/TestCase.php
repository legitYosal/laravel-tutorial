<?php

namespace Tests;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Tests\BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;


    public  function setUp(): void {
        parent::setUp();

        $this->setUpFaker();
        
        Storage::fake('local');
        
        $this->setUpData();
    }

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

    protected $defaultHeaders = [
        'Accept' => 'application/json',
    ];
    protected function baseAuthRequest() {
        $this->assertUserIsNull();
        return $this->actingAs($this->user, 'api')
            ->withHeaders($this->defaultHeaders);
    }

}
