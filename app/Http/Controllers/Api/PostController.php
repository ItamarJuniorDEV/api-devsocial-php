<?php

namespace App\Http\Controllers\Api;

use App\DTO\Post\CreateCommentDTO;
use App\DTO\Post\CreatePostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreateCommentRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $posts,
    ) {}

    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = $this->posts->create(
            (int) $request->user()->id,
            CreatePostDTO::fromRequest($request)
        );

        $post->load(['user', 'comments.user'])->loadCount(['likes', 'comments']);
        $post->setAttribute('mine', true);
        $post->setAttribute('liked', false);

        return response()->json(['data' => new PostResource($post)], 201);
    }

    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $post  = Post::findOrFail($id);
        $liked = $this->posts->toggleLike($post->id, (int) $request->user()->id);

        return response()->json(['data' => ['liked' => $liked]]);
    }

    public function comment(CreateCommentRequest $request, int $id): JsonResponse
    {
        $post    = Post::findOrFail($id);
        $this->authorize('comment', $post);

        $comment = $this->posts->comment(
            $post->id,
            (int) $request->user()->id,
            CreateCommentDTO::fromRequest($request)
        );

        return response()->json(['data' => new PostCommentResource($comment->load('user'))], 201);
    }
}
