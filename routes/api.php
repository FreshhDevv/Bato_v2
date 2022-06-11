<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function() {

    // User
    Route::get('/user', [AuthController::class, 'user']);

    Route::put('/user', [AuthController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Post
    Route::get('/posts', [ProductController::class, 'index']); // all posts

    Route::post('/posts/add', [ProductController::class, 'store']); // create post

    Route::get('/posts/{id}', [ProductController::class, 'show']); // get single post

    Route::put('/posts/{id}', [ProductController::class, 'update']); // update post

    Route::delete('/posts/{id}', [ProductController::class, 'destroy']); // delete post 

    // Comment
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']); // all comments of a post

    Route::post('/posts/{id}/comments', [CommentController::class, 'store']); // create comment on a post

    Route::put('/comments/{id}', [CommentController::class, 'update']); // update a comment

    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); // delete a comment

    // Like
    Route::post('/posts/{id}/likes', [LikeController::class, 'likeOrUnlike']); // like or dislike back a post
});