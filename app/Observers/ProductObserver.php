<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{

    public function created(Product $product)
    {
        error_log('call external service');
    }

}
