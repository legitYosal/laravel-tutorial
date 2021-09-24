<?php

namespace App\Models;

use DateInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        // 'email',
        'mobile',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'mobile' => $this->mobile,
            'name' => $this->name,
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function notification_token()
    {
        return $this->hasOne(NotificationToken::class);
    }

    private function getUserLikeLimitCacheKey(): string
    {
        return config('posts.like_limit_per_user_cache_prefix').$this->id;
    }
    public function checkCanLike(): bool
    {
        $value = Cache::get($this->getUserLikeLimitCacheKey());
        if ($value) {
            if ($value < config('posts.like_limit_per_user_count'))
                return true;
            else return false;
        } else return true;
    }
    public function incrementUserLikes()
    {
        $value = Cache::get($this->getUserLikeLimitCacheKey());
        if ($value) {
            Cache::increment($this->getUserLikeLimitCacheKey());
        } else {
            Cache::put(
                $this->getUserLikeLimitCacheKey(),
                1, config('posts.like_limit_per_user_duration_seconds'),
            );
        }
    }

    public static function getCachingFullPrefix(): string
    {
        return config('cache.prefix').':';
    }

    public function getLimitedLikeErrorData()
    {   
        $seconds = Redis::connection('cache')->command('ttl', [
            User::getCachingFullPrefix().$this->getUserLikeLimitCacheKey()
        ]);
        error_log(User::getCachingFullPrefix().$this->getUserLikeLimitCacheKey());
        $datetime = now();
        if ($seconds > 0)
            $datetime->add(
                new DateInterval('PT'.$seconds.'S'),
            );
        return [
            'message' => __('lang.You are not able to like'),
            'eliminate_restriction_datetime' => $datetime,
            'eliminate_restriction_seconds' => $seconds,
        ];
    }
}
