<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Http;

trait MockHttpPostTrait
{
    public function mockHttpPost() {
        Http::shouldReceive('post');
    } 
}
