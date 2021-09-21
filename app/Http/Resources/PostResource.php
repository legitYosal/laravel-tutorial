<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function postIsLikedByUser($user, $post_id) {
        return $user->likes()->where('post_id', $post_id)->exists();
    }
    public function getLikesCount() {
        return $this->likes()->count();
    }
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title'=> $this->title,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'images' => $this->images,
            'likes' => $this->likes_count ? $this->likes_count:$this->getLikesCount(),
            'is_liked' => $this->postIsLikedByUser(
                        auth()->user(), $this->id,
                    ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
