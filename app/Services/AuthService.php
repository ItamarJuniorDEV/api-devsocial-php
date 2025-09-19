<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create($data);
        $token = $user->createToken('api')->plainTextToken;

        UserRegistered::dispatch($user);

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
