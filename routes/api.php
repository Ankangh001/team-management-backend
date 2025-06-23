<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;

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
    VerifyCsrfToken::class,
])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::put('/posts/{id}/toggle-pin', [PostController::class, 'togglePin']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']); // list + search
    Route::get('/user', [UserController::class, 'me']);
    Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']); // assign role
    Route::post('/users/{user}/toggle-role', [UserController::class, 'toggleTeamViewer']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/inbox', [MessageController::class, 'inbox']);
});

Route::middleware(['auth:sanctum', 'role:super_admin'])->get('/admin/users', [AdminUserController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:super_admin'])->post('/admin/users/{user}/assign-role', [AdminUserController::class, 'assignRole']);

//Public Post Routes
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
Route::get('/stats', [PostController::class, 'stats']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/team-members', [UserController::class, 'getTeamMembers']);
Route::get('/posts/{id}', [PostController::class, 'show']);