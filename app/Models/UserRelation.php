<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRelation extends Model
{
    protected $fillable = ['user_from', 'user_to'];

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_from');
    }

    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to');
    }
}
