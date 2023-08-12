<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
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
Route::get('subcategories', [SubcategoryController::class, 'subcategories']);
Route::get('get_category_info/{category_id}', [MainController::class, 'getCategoryInfo']);
Route::get('get_subcategory_info/{subcategory_id}', [MainController::class, 'getSubcategoryInfo']);

Route::post('test', [MainController::class, 'test']);

Route::post('subscribe_to_newsletter', [MainController::class, 'subscribeToNewsletter']);

Route::get('products', [ProductController::class, 'getProducts']);
Route::get('product/{product_id}', [ProductController::class, 'getProductsById']);

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
    // Favorites
    Route::get('favorites', [MainController::class, 'favorites']);
    Route::post('add_favorites', [MainController::class, 'addFavorites']);
    Route::delete('delete_favorites', [MainController::class, 'deleteFavorites']);

    // Basket
    Route::get('basket', [MainController::class, 'basket']);
    Route::post('add_basket', [MainController::class, 'addBasket']);
    Route::delete('delete_basket', [MainController::class, 'deleteBasket']);


    // Favorite Stores
    Route::get('favorite_stores', [MainController::class, 'favorite_stores']);
    Route::post('add_favorite_store', [MainController::class, 'addFavoriteStores']);
    Route::delete('delete_favorite_store', [MainController::class, 'deleteFavoriteStores']);


    // Orders
    Route::get('orders', [MainController::class, 'orders']);
    Route::post('add_order', [MainController::class, 'addOrder']);
    Route::delete('delete_order', [MainController::class, 'deleteOrder']);


    // Addresses
    Route::get('addresses', [MainController::class, 'getAddresses']);
    Route::post('add_address', [MainController::class, 'addAddress']);
    Route::delete('delete_address', [MainController::class, 'deleteAddress']);
});


Route::group(['middleware' => 'admin'], function () {
    // Category
    Route::post('add_category', [MainController::class, 'addCategory']);
    Route::delete('delete_category', [MainController::class, 'deleteCategory']);

    // Subcategory
    Route::post('add_subcategory', [SubcategoryController::class, 'addSubcategory']);
    Route::delete('delete_subcategory', [SubcategoryController::class, 'deleteSubcategory']);

    // Admin
    Route::post('delete_admin', [AdminController::class, 'deleteAdmin']);
    Route::post('add_admin', [AdminController::class, 'addAdmin']);

    // Profile
    Route::get('user/{user_id}', [AdminController::class, 'getUser']);
    Route::get('display_of_subscribers', [AdminController::class, 'displayOfSubscribers']);

    // Product
    Route::post('add_product', [ProductController::class, 'addProduct']);
    Route::delete('delete_product', [ProductController::class, 'deleteProduct']);
});
