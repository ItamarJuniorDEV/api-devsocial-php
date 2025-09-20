<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreateCommentRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $posts,
    ) {}

    #[OA\Post(
        path: '/api/posts',
        summary: 'Criar novo post',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['type'],
                properties: [
                    new OA\Property(property: 'type', type: 'string', enum: ['text', 'photo']),
                    new OA\Property(property: 'body', type: 'string'),
                ]
            )
        ),
        tags: ['Posts'],
        responses: [
            new OA\Response(response: 201, description: 'Post criado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = $this->posts->create(
            (int) $request->user()->id,
            $request->validated()
        );

        $post->load(['user', 'comments.user'])->loadCount(['likes', 'comments']);
        $post->setAttribute('mine', true);
        $post->setAttribute('liked', false);

        return response()->json(['data' => new PostResource($post)], 201);
    }

    #[OA\Post(
        path: '/api/posts/{id}/like',
        summary: 'Curtir ou descurtir um post',
        security: [['sanctum' => []]],
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Estado do like atualizado'),
            new OA\Response(response: 404, description: 'Post não encontrado'),
        ]
    )]
    public function toggleLike(Request $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $liked = $this->posts->toggleLike($post->id, (int) $request->user()->id);

        return response()->json(['data' => ['liked' => $liked]]);
    }

    #[OA\Post(
        path: '/api/posts/{id}/comments',
        summary: 'Comentar em um post',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['body'],
                properties: [
                    new OA\Property(property: 'body', type: 'string'),
                ]
            )
        ),
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'Comentário criado'),
            new OA\Response(response: 404, description: 'Post não encontrado'),
        ]
    )]
    public function comment(CreateCommentRequest $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $this->authorize('comment', $post);

        $comment = $this->posts->comment(
            $post->id,
            (int) $request->user()->id,
            $request->validated()
        );

        return response()->json(['data' => new PostCommentResource($comment->load('user'))], 201);
    }
}
