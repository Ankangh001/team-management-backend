<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AuthController;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

/*|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|*/ 

Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    // VerifyCsrfToken::class,
])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::put('/posts/{id}/toggle-pin', [PostController::class, 'togglePin']);
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// // Public Post Routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
Route::get('/stats', [PostController::class, 'stats']);

Route::middleware('auth:sanctum')->group(function () {
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:super_admin'])->get('/admin/users', [AdminUserController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:super_admin'])->post('/admin/users/{user}/assign-role', [AdminUserController::class, 'assignRole']);
Route::get('/team-members', function () {
    return User::role(['team_member_a', 'team_member_b'])->get(['id', 'name', 'email']);
});
