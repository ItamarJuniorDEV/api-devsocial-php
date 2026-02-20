<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $search,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'q'        => ['required', 'string', 'min:1', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);
        $result  = $this->search->search(trim($request->query('q')), $perPage);

        $users = [];
        foreach ($result['users']->items() as $user) {
            $users[] = new UserResource($user);
        }

        $authId = (int) $request->user()->id;
        $posts  = [];

        foreach ($result['posts']->items() as $post) {
            $post->setAttribute('mine', (int) $post->user_id === $authId);
            $post->setAttribute('liked', $post->likes()->where('user_id', $authId)->exists());
            $posts[] = new PostResource($post);
        }

        return response()->json([
            'data' => [
                'users' => $users,
                'posts' => $posts,
            ],
            'meta' => [
                'users' => [
                    'current_page' => $result['users']->currentPage(),
                    'last_page'    => $result['users']->lastPage(),
                    'per_page'     => $result['users']->perPage(),
                    'total'        => $result['users']->total(),
                ],
                'posts' => [
                    'current_page' => $result['posts']->currentPage(),
                    'last_page'    => $result['posts']->lastPage(),
                    'per_page'     => $result['posts']->perPage(),
                    'total'        => $result['posts']->total(),
                ],
            ],
        ]);
    }
}
