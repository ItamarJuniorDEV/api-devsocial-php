<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user;
    }

    public function search(string $query, int $perPage): LengthAwarePaginator
    {
        return User::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->paginate($perPage);
    }
}
