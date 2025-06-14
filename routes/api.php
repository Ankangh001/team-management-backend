<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AuthController;

// Authentication Routes
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware('web')->group(function () {
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/login', [AuthController::class, 'login']);
// });

// Public Post Routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
Route::get('/stats', [PostController::class, 'stats']);

// Protected Post & Comment Routes
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);
Route::post('/posts/{id}/like', [PostController::class, 'like']);
Route::post('/posts/{id}/comments', [CommentController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
