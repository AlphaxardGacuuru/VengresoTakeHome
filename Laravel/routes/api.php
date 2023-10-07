<?php

use App\Http\Controllers\DivCountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:sanctum'])->group(function () {
    // Authenticated User
    Route::get('auth', [UserController::class, 'auth']);

    Route::apiResources([
        'div-counts' => DivCountController::class,
        'notifications' => NotificationController::class,
        'users' => UserController::class
    ]);
// });

Broadcast::routes(['middleware' => ['auth:sanctum']]);