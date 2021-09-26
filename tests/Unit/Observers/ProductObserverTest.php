<?php

namespace Tests\Unit\Observers;
use Tests\TestCase;
use Illuminate\Support\Facades\Bus;

use App\Jobs\PostToExternalApi;
use App\Models\Product;

class ProductObserverTest extends TestCase
{
    public function test_created()
    {
        Bus::fake();
        Product::factory()->create();
        Bus::assertDispatched(PostToExternalApi::class);
    }
}
