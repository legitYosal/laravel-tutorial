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

Route::apiResource('category', Api\CategoryController::class)->middleware('auth:api');
Route::apiResource('post', Api\PostController::class)->middleware('auth:api');

Route::post('auth/register', [Api\Authentication::class, 'register']);
Route::post('auth/token/obtain', [Api\Authentication::class, 'login']);

Route::post('test', [Api\TestUpload::class, 'upload']);
