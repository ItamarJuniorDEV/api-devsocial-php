<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Repositories\Contracts\PostRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentPostRepository implements PostRepository
{
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function find(int $id): ?Post
    {
        return Post::find($id);
    }

    public function feedForUser(int $authUserId, array $followedUserIds, int $perPage): LengthAwarePaginator
    {
        $ids = $followedUserIds;
        $ids[] = $authUserId;

        return Post::whereIn('user_id', $ids)
            ->with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function postsByUser(int $userId, int $perPage, ?string $type = null): LengthAwarePaginator
    {
        $builder = Post::where('user_id', $userId)
            ->with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at');

        if ($type !== null) {
            $builder->where('type', $type);
        }

        return $builder->paginate($perPage);
    }

    public function toggleLike(int $postId, int $userId): bool
    {
        $exists = PostLike::where('post_id', $postId)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            PostLike::where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();

            return false;
        }

        PostLike::create(['post_id' => $postId, 'user_id' => $userId]);

        return true;
    }

    public function isLikedMap(array $postIds, int $userId): array
    {
        if (count($postIds) === 0) {
            return [];
        }

        $map  = [];
        $rows = DB::table('post_likes')
            ->select('post_id')
            ->where('user_id', $userId)
            ->whereIn('post_id', $postIds)
            ->get();

        foreach ($rows as $row) {
            $map[(int) $row->post_id] = true;
        }

        return $map;
    }

    public function createComment(int $postId, int $userId, string $body): PostComment
    {
        return PostComment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'body'    => $body,
        ]);
    }

    public function search(string $query, int $perPage): LengthAwarePaginator
    {
        return Post::where('type', 'text')
            ->where('body', 'like', '%' . $query . '%')
            ->with(['user'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
