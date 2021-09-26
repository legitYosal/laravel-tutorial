<?php

namespace Tests\Unit\Jobs;


use Tests\TestCase;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

use App\Jobs\PostToExternalApi;

class PostToExternalApiTest extends TestCase
{
    public function test_example()
    {
        Http::shouldReceive('post');
        $url = 'test_url';
        $data = ['test'=>11212];
        (New PostToExternalApi(
            $url, $data
        ))->handle();
    }
}
