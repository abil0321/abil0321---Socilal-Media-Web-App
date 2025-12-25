<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // * ANCHOR: Posts Business Routes
    Route::prefix('posts')->group(function () {
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
    Route::prefix('message')->group(function () {
        Route::get('{user_id}', [MessageController::class, 'show'])->name('message.show');
        Route::post('{user_id}', [MessageController::class, 'send'])->name('message.send');
        Route::delete('{message_id}', [MessageController::class, 'delete'])->name('message.delete');
    });
});
