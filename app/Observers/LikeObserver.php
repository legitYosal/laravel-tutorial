<?php

namespace App\Observers;

use App\Models\Like;

class LikeObserver
{
    public function created(Like $like)
    {
        error_log('this must call a notif sender');
    }
}
