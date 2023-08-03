<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [MainController::class, 'registerStore']);
Route::get('categories', [MainController::class, 'categories']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('favorites', [MainController::class, 'favorites']);
    Route::get('basket', [MainController::class, 'basket']);
});


Route::group(['middleware' => 'admin'], function () {
    Route::post('add_category', [MainController::class, 'addCategory']);
    Route::delete('delete_category', [MainController::class, 'deleteCategory']);
});
