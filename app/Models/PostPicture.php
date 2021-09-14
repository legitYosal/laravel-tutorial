<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_name',
        'image_path',
        'post_id',
    ];

    public static function save_and_create($file, $post_id)
    {
        $path = $file->store('public/files/post/images/');
        $name = $file->getClientOriginalName();

        return PostPicture::create([
            'image_path' => $path,
            'image_name' => $name,
            'post_id' => $post_id,
        ]);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
