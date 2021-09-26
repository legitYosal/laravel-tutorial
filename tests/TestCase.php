<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase 
{
    use RefreshDatabase;
    use CreatesApplication;
    use WithFaker;
    use \App\Traits\FakeImage;
    use \Tests\Helpers\GetFakeUser;

    protected function clearTestingCache()
    {
        Artisan::call('cache:clear');
    }

    public  function setUp(): void {
        
        parent::setUp();
        
        $this->clearTestingCache();

        $this->setUpFaker();
        
        Storage::fake('local');

        $this->setUpData();
    }
    protected $defaultHeaders = [
        'Accept' => 'application/json',
    ];
    protected function setUpData() {}
}