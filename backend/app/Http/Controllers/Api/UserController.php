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
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    #[OA\Get(
        path: '/api/user/me',
        summary: 'Obter dados do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Dados do perfil'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    #[OA\Put(
        path: '/api/user/profile',
        summary: 'Atualizar perfil do usuário',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', minLength: 3),
                    new OA\Property(property: 'birthdate', type: 'string', format: 'date'),
                    new OA\Property(property: 'city', type: 'string'),
                    new OA\Property(property: 'work', type: 'string'),
                    new OA\Property(property: 'bio', type: 'string'),
                ]
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Perfil atualizado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->users->updateProfile((int) $request->user()->id, $request->validated());

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    #[OA\Post(
        path: '/api/user/avatar',
        summary: 'Enviar avatar do usuário',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [new OA\Property(property: 'avatar', type: 'string', format: 'binary')]
                )
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Avatar atualizado'),
            new OA\Response(response: 422, description: 'Arquivo inválido'),
        ]
    )]
    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $path = $this->users->uploadAvatar((int) $request->user()->id, $request->file('avatar'));

        return response()->json([
            'data' => ['avatar_url' => asset('storage/'.$path)],
        ]);
    }

    #[OA\Post(
        path: '/api/user/cover',
        summary: 'Enviar capa do usuário',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [new OA\Property(property: 'cover', type: 'string', format: 'binary')]
                )
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(response: 200, description: 'Capa atualizada'),
            new OA\Response(response: 422, description: 'Arquivo inválido'),
        ]
    )]
    public function uploadCover(UploadCoverRequest $request): JsonResponse
    {
        $path = $this->users->uploadCover((int) $request->user()->id, $request->file('cover'));

        return response()->json([
            'data' => ['cover_url' => asset('storage/'.$path)],
        ]);
    }
}
