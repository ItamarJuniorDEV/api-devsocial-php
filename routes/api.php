<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok']))->name('ping');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:auth')
        ->name('register');

    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:auth')
        ->name('login');

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('me', [UserController::class, 'me'])->name('me');
        Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::post('avatar', [UserController::class, 'uploadAvatar'])->name('avatar.update');
        Route::post('cover', [UserController::class, 'uploadCover'])->name('cover.update');
    });

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');

    Route::prefix('posts')->name('posts.')->group(function () {
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('{id}/like', [PostController::class, 'toggleLike'])->name('like');
        Route::post('{id}/comments', [PostController::class, 'comment'])->name('comments.store');
    });

    Route::post('/users/{id}/follow', [FollowController::class, 'toggle'])->name('users.follow');

    Route::get('/search', SearchController::class)->name('search');
});
