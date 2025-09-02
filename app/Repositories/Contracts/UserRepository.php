<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function create(array $data): User;
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function update(User $user, array $data): User;
    public function search(string $query, int $perPage): LengthAwarePaginator;
}
