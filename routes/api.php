<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::put('internal/private/change-user-notif-token', [Api\Internal\UserController::class, 'set_user_notif_token'])
        ->middleware('internal_call');

Route::get('index', [Api\IndexController::class, 'index'])->middleware('auth:api');

Route::post('auth/register', [Api\Authentication::class, 'register']);
Route::post('auth/token/obtain', [Api\Authentication::class, 'login']);

Route::apiResource('post', Api\PostController::class)->middleware('auth:api');
Route::post('post/{post}/picture', [Api\PostController::class, 'add_picture'])
    ->middleware('auth:api');
Route::delete('post/{post}/picture/{picture}', [Api\PostController::class, 'delete_picture'])
    ->middleware('auth:api');
Route::post('post/{post}/like', [Api\PostController::class, 'toggle_like'])
    ->middleware('auth:api');

Route::apiResource('product', Api\ProductController::class)->middleware('auth:api');
Route::post('product/{product}/picture', [Api\ProductController::class, 'add_picture'])
    ->middleware('auth:api');
Route::delete('product/{product}/picture/{picture}', [Api\ProductController::class, 'delete_picture'])
    ->middleware('auth:api');
Route::put('product/{product}/price/', [Api\ProductController::class, 'update_price'])
    ->middleware('auth:api');
Route::post('product/{product}/like', [Api\ProductController::class, 'toggle_like'])
    ->middleware('auth:api');