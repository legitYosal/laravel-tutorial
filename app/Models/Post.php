<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(PostPicture::class);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function scopeBaseQuery($query)
    {
        return $query->with('images')->withCount('likes')
            ->selectRaw(
                '(
                    select count(*) from `likes` 
                        where 
                            `likes`.`likeable_id`=`posts`.`id` 
                            and `likes`.`likeable_type`=? 
                            and `likes`.`user_id`=?
                ) as `is_liked`', [
                        Post::getLikeableTypeStr(false), auth()->user()->id
                    ]
            );
    }

    public static function getLikeableTypeStr($duoble_slashes=true): string
    {   
        if ($duoble_slashes)
            return addslashes(get_class(
                New Post()
            ));
             // returns "App\\Models\\Post"
        else 
            return get_class(
                New Post()
            );
            // returns "App\Models\Post"
    }
}
