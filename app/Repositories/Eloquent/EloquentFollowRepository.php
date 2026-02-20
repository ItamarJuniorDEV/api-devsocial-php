<?php

namespace App\Repositories\Eloquent;

use App\Models\UserRelation;
use App\Repositories\Contracts\FollowRepository;

class EloquentFollowRepository implements FollowRepository
{
    public function toggle(int $userFromId, int $userToId): bool
    {
        $relation = UserRelation::where('user_from', $userFromId)
            ->where('user_to', $userToId)
            ->first();

        if ($relation) {
            $relation->delete();

            return false;
        }

        UserRelation::create([
            'user_from' => $userFromId,
            'user_to'   => $userToId,
        ]);

        return true;
    }

    public function getFollowedUserIds(int $userFromId): array
    {
        $ids       = [];
        $relations = UserRelation::where('user_from', $userFromId)->get();

        foreach ($relations as $relation) {
            $ids[] = (int) $relation->user_to;
        }

        return $ids;
    }
}
