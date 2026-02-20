<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepository
{
    public function create(array $data): Post;
    public function find(int $id): ?Post;
    public function feedForUser(int $authUserId, array $followedUserIds, int $perPage): LengthAwarePaginator;
    public function postsByUser(int $userId, int $perPage, ?string $type = null): LengthAwarePaginator;
    public function toggleLike(int $postId, int $userId): bool;
    public function isLikedMap(array $postIds, int $userId): array;
    public function createComment(int $postId, int $userId, string $body): PostComment;
    public function search(string $query, int $perPage): LengthAwarePaginator;
}
