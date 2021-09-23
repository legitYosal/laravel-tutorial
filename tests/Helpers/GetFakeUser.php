<?php

namespace Tests\Helpers;

use App\Models\User;

Trait GetFakeUser{
    protected function getFakeUser(): object {
        return User::factory()->create();
    }
}