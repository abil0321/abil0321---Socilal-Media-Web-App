<?php

use App\Http\Controllers\Api\V1\JWTAuthController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Middleware\JWTMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    //* ANCHOR: Authentication
    Route::post('/register', [JWTAuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [JWTAuthController::class, 'login'])->name('auth.login');

    // * ANCHOR: Posts Business Routes
    Route::middleware([JWTMiddleware::class])->prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::get('{id}', [PostController::class, 'show'])->name('posts.show');
        Route::put('{id}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('{id}', [PostController::class, 'destroy'])->name('posts.destroy');

        // * ANCHOR: Likes Business Routes
        Route::prefix('likes')->group(function () {
            Route::post('/', [PostController::class, 'likePost'])->name('posts.likes.add');
            Route::delete('{post_id}', [PostController::class, 'unlikePost'])->name('posts.likes.remove');
        });

        // * ANCHOR: Comments Business Routes
        Route::prefix('comments')->group(function () {
            Route::post('/', [PostController::class, 'commentPost'])->name('posts.comments.add');
            Route::delete('{post_id}', [PostController::class, 'RemoveCommentPost'])->name('posts.comments.remove');
        });
    });

    // * ANCHOR: Message Business Routes
    Route::middleware(JWTMiddleware::class)->prefix('message')->group(function () {
        Route::post('/', [MessageController::class, 'store'])->name('message.send');
        Route::get('{id}', [MessageController::class, 'show'])->name('message.show');
        Route::get('/getMessage/{user_id}', [MessageController::class, 'showByUser'])->name('message.show_by_user');
        Route::delete('{id}', [MessageController::class, 'destroy'])->name('message.delete');
    });
});
