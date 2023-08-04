<?php

use App\Http\Controllers\AdminController;
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
Route::post('test', [MainController::class, 'test']);
Route::post('subscribe_to_newsletter', [MainController::class, 'subscribeToNewsletter']);

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
    Route::post('add_favorites', [MainController::class, 'addFavorites']);
    Route::delete('delete_favorites', [MainController::class, 'deleteFavorites']);
    Route::get('basket', [MainController::class, 'basket']);
    Route::post('add_basket', [MainController::class, 'addBasket']);
    Route::delete('delete_basket', [MainController::class, 'deleteBasket']);
});


Route::post('add_admin', [AdminController::class, 'addAdmin']);


Route::group(['middleware' => 'admin'], function () {
    Route::post('add_category', [MainController::class, 'addCategory']);
    Route::delete('delete_category', [MainController::class, 'deleteCategory']);
    Route::post('delete_admin', [AdminController::class, 'deleteAdmin']);
    Route::get('user/{user_id}', [AdminController::class, 'getUser']);
    Route::get('display_of_subscribers', [AdminController::class, 'displayOfSubscribers']);
});
