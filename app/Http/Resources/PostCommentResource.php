<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'created_at' => $this->created_at,
            'user'       => new UserResource($this->whenLoaded('user')),
        ];
    }
}
