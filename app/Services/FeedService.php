<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\UserRelation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedService
{
    public function feed(int $authUserId, int $perPage): LengthAwarePaginator
    {
        $followedIds = UserRelation::where('user_from', $authUserId)
            ->pluck('user_to')
            ->toArray();

        $ids = array_map('intval', $followedIds);
        $ids[] = $authUserId;

        $paginator = Post::whereIn('user_id', $ids)
            ->with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $postIds = [];
        foreach ($paginator->items() as $post) {
            $postIds[] = (int) $post->id;
        }

        $likedMap = [];
        if (count($postIds) > 0) {
            $rows = PostLike::select('post_id')
                ->where('user_id', $authUserId)
                ->whereIn('post_id', $postIds)
                ->get();

            foreach ($rows as $row) {
                $likedMap[(int) $row->post_id] = true;
            }
        }

        foreach ($paginator->items() as $post) {
            $post->setAttribute('mine', (int) $post->user_id === $authUserId);
            $post->setAttribute('liked', isset($likedMap[(int) $post->id]));
        }

        return $paginator;
    }
}
