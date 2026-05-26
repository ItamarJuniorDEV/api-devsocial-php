<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        $comments = [];

        if ($this->relationLoaded('comments')) {
            foreach ($this->comments as $comment) {
                $comments[] = new PostCommentResource($comment);
            }
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'body' => $this->type === 'text' ? $this->body : null,
            'photo_url' => $this->type === 'photo' && $this->body ? asset('storage/'.$this->body) : null,
            'mine' => (bool) ($this->getAttribute('mine') ?? false),
            'liked' => (bool) ($this->getAttribute('liked') ?? false),
            'likes_count' => (int) ($this->likes_count ?? 0),
            'comments_count' => (int) ($this->comments_count ?? 0),
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'comments' => $comments,
        ];
    }
}
