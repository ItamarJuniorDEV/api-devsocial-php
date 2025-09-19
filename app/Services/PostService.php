<?php

namespace App\Services;

use App\DTO\Post\CreateCommentDTO;
use App\DTO\Post\CreatePostDTO;
use App\Events\PostCommented;
use App\Events\PostCreated;
use App\Events\PostLiked;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;

class PostService
{
    public function create(int $authUserId, CreatePostDTO $dto): Post
    {
        $body = $dto->body;

        if ($dto->type === 'photo' && $dto->photo) {
            $body = $dto->photo->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => $authUserId,
            'type' => $dto->type,
            'body' => (string) $body,
        ]);

        PostCreated::dispatch($post);

        return $post;
    }

    public function toggleLike(int $postId, int $authUserId): bool
    {
        $exists = PostLike::where('post_id', $postId)
            ->where('user_id', $authUserId)
            ->exists();

        if ($exists) {
            PostLike::where('post_id', $postId)
                ->where('user_id', $authUserId)
                ->delete();
            $liked = false;
        } else {
            PostLike::create(['post_id' => $postId, 'user_id' => $authUserId]);
            $liked = true;
        }

        PostLiked::dispatch($postId, $authUserId, $liked);

        return $liked;
    }

    public function comment(int $postId, int $authUserId, CreateCommentDTO $dto): PostComment
    {
        $comment = PostComment::create([
            'post_id' => $postId,
            'user_id' => $authUserId,
            'body' => $dto->body,
        ]);

        PostCommented::dispatch($comment);

        return $comment;
    }
}
