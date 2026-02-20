<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Requests\User\UploadCoverRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->users->updateProfile((int) $request->user()->id, $request->validated());

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $path = $this->users->uploadAvatar((int) $request->user()->id, $request->file('avatar'));

        return response()->json([
            'data' => ['avatar_url' => asset('storage/' . $path)],
        ]);
    }

    public function uploadCover(UploadCoverRequest $request): JsonResponse
    {
        $path = $this->users->uploadCover((int) $request->user()->id, $request->file('cover'));

        return response()->json([
            'data' => ['cover_url' => asset('storage/' . $path)],
        ]);
    }
}
