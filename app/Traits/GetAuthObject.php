<?php 

namespace App\Traits;

trait GetAuthObject {
    protected function get_auth(): object
    {
        return auth();
    }
}