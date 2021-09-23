<?php

namespace Tests\Unit\User;

use Tests\BaseTestCase;

class LikeLimitTest extends BaseTestCase
{
    public function test_like_limiation()
    {
        $user = $this->getFakeUser();
        $user_like_limit = 10;

        config([
            'posts.like_limit_per_user_count' => $user_like_limit,
        ]);

        $this->assertTrue($user->checkCanLike());

        for ($i=0; $i < $user_like_limit; $i++) {
            $user->incrementUserLikes();
        }

        $this->assertFalse($user->checkCanLike());

    }
}
