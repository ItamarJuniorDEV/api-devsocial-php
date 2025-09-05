<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'birthdate'  => $this->birthdate?->format('Y-m-d'),
            'city'       => $this->city,
            'work'       => $this->work,
            'bio'        => $this->bio,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'cover_url'  => $this->cover ? asset('storage/' . $this->cover) : null,
            'created_at' => $this->created_at,
        ];
    }
}
