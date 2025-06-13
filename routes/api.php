<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;

use App\Http\Controllers\Api\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);
Route::get('/stats', [PostController::class, 'stats']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);