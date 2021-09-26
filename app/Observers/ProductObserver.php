<?php

namespace App\Observers;

use App\Models\Product;

use Illuminate\Support\Facades\Http;
use App\Jobs\PostToExternalApi;

class ProductObserver
{

    public function created(Product $product)
    {
        PostToExternalApi::dispatch(
            'https://gorest.co.in/public/v1/users',
            $product->toArray(),
        );
    }

}
