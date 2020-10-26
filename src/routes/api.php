<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\ChannelController;
use App\Http\Controllers\API\UserController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [UserController::class, 'profile']);

    Route::get('users/online', [UserController::class, 'index']);

    Route::apiResource('messages', MessageController::class)->only([
        'index', 'store'
    ]);

    Route::apiResource('channels', ChannelController::class)->only([
        'index'
    ]);
});
