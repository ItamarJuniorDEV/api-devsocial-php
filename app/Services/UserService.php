<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function updateProfile(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->fill($data)->save();

        return $user;
    }

    public function uploadAvatar(int $userId, $file): string
    {
        $user = User::findOrFail($userId);
        $path = $file->store('avatars', 'public');

        $user->fill(['avatar' => $path])->save();

        return $path;
    }

    public function uploadCover(int $userId, $file): string
    {
        $user = User::findOrFail($userId);
        $path = $file->store('covers', 'public');

        $user->fill(['cover' => $path])->save();

        return $path;
    }
}
