<?php

namespace App\Repositories\Contracts;

interface FollowRepository
{
    public function toggle(int $userFromId, int $userToId): bool;
    public function getFollowedUserIds(int $userFromId): array;
}
