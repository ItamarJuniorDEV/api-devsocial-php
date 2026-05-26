<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class SearchService
{
    public function search(string $query, int $perPage): array
    {
        return [
            'users' => User::where('name', 'like', '%'.$query.'%')
                ->orWhere('email', 'like', '%'.$query.'%')
                ->paginate($perPage),
            'posts' => Post::where('type', 'text')
                ->where('body', 'like', '%'.$query.'%')
                ->with(['user'])
                ->withCount(['likes', 'comments'])
                ->orderByDesc('created_at')
                ->paginate($perPage),
        ];
    }
}
