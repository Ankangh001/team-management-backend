<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

use Illuminate\Support\Facades\Response;

Route::get('/direct-image/{filename}', function ($filename) {
    $path = storage_path('app/public/uploads/profile/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found.');
    }

    return Response::file($path);
});


Route::get('/direct-post-image/{filename}', function ($filename) {
    $path = storage_path('app/public/posts/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found.');
    }

    return Response::file($path);
});