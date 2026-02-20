<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __construct(
        private readonly FollowService $follow,
    ) {}

    public function toggle(Request $request, int $id): JsonResponse
    {
        $following = $this->follow->toggle((int) $request->user()->id, $id);

        return response()->json(['data' => ['following' => $following]]);
    }
}
