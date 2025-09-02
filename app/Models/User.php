<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'birthdate',
        'city',
        'work',
        'bio',
        'avatar',
        'cover',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthdate'         => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function following(): HasMany
    {
        return $this->hasMany(UserRelation::class, 'user_from');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(UserRelation::class, 'user_to');
    }
}
