<?php

namespace App\Services;

use App\Events\UserFollowed;
use App\Models\UserRelation;

class FollowService
{
    public function toggle(int $authUserId, int $userId): bool
    {
        $relation = UserRelation::where('user_from', $authUserId)
            ->where('user_to', $userId)
            ->first();

        if ($relation) {
            $relation->delete();
            $following = false;
        } else {
            UserRelation::create([
                'user_from' => $authUserId,
                'user_to' => $userId,
            ]);
            $following = true;
        }

        UserFollowed::dispatch($authUserId, $userId, $following);

        return $following;
    }
}
