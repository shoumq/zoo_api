<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\BasketResource;
use App\Http\Resources\FavoritesResource;
use App\Http\Resources\FavoriteStoresResource;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Basket;
use App\Models\Category;
use App\Models\Email;
use App\Models\Favorites;
use App\Models\FavoriteStores;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class MainController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Регистрация",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="surname",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *    ),
     *    @OA\Parameter(
     *         name="password_confirmed",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTExNDgzNTAsImV4cCI6MTY5Mzc0MDM1MCwibmJmIjoxNjkxMTQ4MzUwLCJqdGkiOiJDdlhPZnd6dWM0Rko5MklOIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmdsr6YWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.ZXquNUAvkXvTxCzf6Uj639B8n5K41Lm-j5xOPm08J3o"),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="number", example="1866240000"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function registerStore(RegisterRequest $request)
    {
        try {
            if ($request->password == $request->password_confirmed) {
                $user = new User();
                $user->name = $request->name;
                $user->surname = $request->surname;
                $user->email = $request->email;
                $user->address = $request->address;
                $user->password = Hash::make($request->password);
                $user->phone = $request->phone;
                $user->save();


                $credentials = request(['email', 'password']);

                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 43200,
                    'user' => $user
                ]);
            }

            return response()->json("Password error");
        } catch (\Exception $e) {
            return response()->json("error: " . $e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Все категории",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="title", type="string", example="Акции", minLength=1, maxLength=100),
     *                 @OA\Property(property="code", type="string", example="sales", minLength=1, maxLength=150),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *                 @OA\Property(property="image", type="string", example="image_title"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function categories(): JsonResponse
    {
        return response()->json(Category::all());
    }


    /**
     * @OA\Post(
     *     path="/api/add_category",
     *     summary="Добавить категорию",
     *     tags={"Category"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=true,
     *    ),
     *    @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Parameter(
     *         name="icon",
     *         in="query",
     *         required=false,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Category successfully created",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addCategory(Request $request): JsonResponse
    {
        $category = new Category();
        $category->title = $request->title;
        $category->code = $request->code;
        $category->image = $request->image;
        $category->icon = $request->icon;
        $category->save();

        return response()->json($category);
    }



    /**
     * @OA\Patch(
     *     path="/api/change_category",
     *     summary="Изменить категорию",
     *     tags={"Category"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *    ),
     *    @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=false,
     *    ),
     *     @OA\Parameter(
     *         name="image",
     *         in="query",
     *         required=false,
     *    ),
     *     @OA\Parameter(
     *         name="icon",
     *         in="query",
     *         required=false,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Category successfully changed",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function changeCategory(Request $request): JsonResponse
    {
        $category = Category::find($request->id);
        if ($category != null) {
            $category->title = ($request->title == null) ? $category->title : $request->title;
            $category->code = ($request->code == null) ? $category->code : $request->code;
            $category->image = ($request->image == null) ? $category->image : $request->image;
            $category->icon = ($request->icon == null) ? $category->icon : $request->icon;
            $category->save();

            return response()->json($category);
        }
        return response()->json(['Message' => 'Not found.'], 404);
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_category",
     *     summary="Удалить категорию",
     *     tags={"Category"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Category successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteCategory(Request $request): JsonResponse
    {
        if (Category::find($request->id) != null) {
            $category = Category::find($request->id);
            $category->delete();
            return response()->json(['Message' => 'Category successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Избранное",
     *     tags={"Favorites"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function favorites(): array
    {
        return FavoritesResource::collection(Favorites::where('user_id', auth()->user()->id)->get())->resolve();
    }


    /**
     * @OA\Post(
     *     path="/api/add_favorites",
     *     summary="Добавить в избранное",
     *     tags={"Favorites"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addFavorites(Request $request): JsonResponse
    {
        $favorite = new Favorites();
        $favorite->user_id = auth()->user()->id;
        $favorite->product_id = $request->product_id;
        $favorite->save();

        return response()->json(FavoritesResource::collection([$favorite])->resolve());
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_favorites",
     *     summary="Удалить товар из избранного",
     *     tags={"Favorites"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteFavorites(Request $request): JsonResponse
    {
        if (Favorites::find($request->id) != null) {
            $favorite = Favorites::find($request->id);
            $favorite->delete();
            return response()->json(['Message' => 'Favorite successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/basket",
     *     summary="Корзина",
     *     tags={"Basket"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function basket(): array
    {
        return BasketResource::collection(Basket::where('user_id', auth()->user()->id)->get())->resolve();
    }


    /**
     * @OA\Post(
     *     path="/api/add_basket",
     *     summary="Добавить в корзину",
     *     tags={"Basket"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addBasket(Request $request): JsonResponse
    {
        $basket = new Basket();
        $basket->user_id = auth()->user()->id;
        $basket->product_id = $request->product_id;
        $basket->save();

        return response()->json($basket);
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_basket",
     *     summary="Удалить товар из коризны",
     *     tags={"Basket"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Basket successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteBasket(Request $request): JsonResponse
    {
        if (Basket::find($request->id) != null) {
            $basket = Basket::find($request->id);
            $basket->delete();
            return response()->json(['Message' => 'Basket successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/subscribe_to_newsletter",
     *     summary="Подписаться на рассылку",
     *     tags={"Profile"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="user_email",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function subscribeToNewsletter(Request $request): JsonResponse
    {
        $email = new Email();
        $email->user_email = $request->user_email;
        $email->save();

        return response()->json(['Message' => 'Successfully']);
    }


    /**
     * @OA\Get(
     *     path="/api/favorite_stores",
     *     summary="Любимые магазины",
     *     tags={"Favorite Stores"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="shop_id", type="number", example="2"),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function favorite_stores(): array
    {
        return FavoriteStoresResource::collection(FavoriteStores::where('user_id', auth()->user()->id)->get())->resolve();
    }


    /**
     * @OA\Post(
     *     path="/api/add_favorite_store",
     *     summary="Добавить любимый магазин",
     *     tags={"Favorite Stores"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="shop_id", type="number", example="2"),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addFavoriteStores(Request $request): JsonResponse
    {
        $basket = new FavoriteStores();
        $basket->user_id = auth()->user()->id;
        $basket->shop_id = $request->store_id;
        $basket->save();

        return response()->json($basket);
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_favorite_store",
     *     summary="Удалить дюбимый магазин",
     *     tags={"Favorite Stores"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite store successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteFavoriteStores(Request $request): JsonResponse
    {
        if (FavoriteStores::find($request->id) != null) {
            $basket = FavoriteStores::find($request->id);
            $basket->delete();
            return response()->json(['Message' => 'Favorite store successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Заказы",
     *     tags={"Order"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function orders(): array
    {
        return OrderResource::collection(Order::where('user_id', auth()->user()->id)->get())->resolve();
    }


    /**
     * @OA\Post(
     *     path="/api/add_order",
     *     summary="Добавить заказ",
     *     tags={"Order"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="14"),
     *                 @OA\Property(property="product_info", type="string", format="array", example={ "id": 1, "title": "Кошачий корм deluxe", "category_id": "2", "category_title": "Коты", "subcategory_id": "1", "subcategory_title": "Кошачий корм", "description": "Кошачий корм deluxe +++", "price": "1435.0", "created_at": "2023-08-11T22:13:23.000000Z", "updated_at": "2023-08-11T22:13:23.000000Z" }),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addOrder(Request $request): JsonResponse
    {
        $order = new Order();
        $order->product_id = $request->product_id;
        $order->user_id = auth()->user()->id;
        $order->save();

        return response()->json(OrderResource::collection([$order])->resolve());
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_order",
     *     summary="Удалить заказ",
     *     tags={"Order"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteOrder(Request $request): JsonResponse
    {
        if (Order::find($request->id) != null) {
            $basket = Order::find($request->id);
            $basket->delete();
            return response()->json(['Message' => 'Order successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     summary="Адреса доставки",
     *     tags={"Address"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="user_id", type="number", example="2"),
     *                 @OA\Property(property="title", type="string", example="Адрес №1"),
     *                 @OA\Property(property="city", type="string", example="Москва"),
     *                 @OA\Property(property="street_and_house", type="string", example="Новодмитровская улица, 2к5"),
     *                 @OA\Property(property="apartment_number", type="string", example="452"),
     *                 @OA\Property(property="entrance_number", type="string", example="2"),
     *                 @OA\Property(property="floor", type="string", example="23"),
     *                 @OA\Property(property="additionally", type="string", example="Домофон: 452В"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getAddresses()
    {
        return Address::where('user_id', auth()->user()->id)->get();
    }


    /**
     * @OA\Post(
     *     path="/api/add_address",
     *     summary="Добавить адрес доставки",
     *     tags={"Address"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="street_and_house",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="apartment_number",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="entrance_number",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="floor",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="additionally",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="user_id", type="number", example="2"),
     *                 @OA\Property(property="title", type="string", example="Адрес №1"),
     *                 @OA\Property(property="city", type="string", example="Москва"),
     *                 @OA\Property(property="street_and_house", type="string", example="Новодмитровская улица, 2к5"),
     *                 @OA\Property(property="apartment_number", type="string", example="452"),
     *                 @OA\Property(property="entrance_number", type="string", example="2"),
     *                 @OA\Property(property="floor", type="string", example="23"),
     *                 @OA\Property(property="additionally", type="string", example="Домофон: 452В"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function addAddress(Request $request): JsonResponse
    {
        $address = new Address();
        $address->title = $request->title;
        $address->city = $request->city;
        $address->street_and_house = $request->street_and_house;
        $address->apartment_number = $request->apartment_number;
        $address->entrance_number = $request->entrance_number;
        $address->floor = $request->floor;
        $address->additionally = $request->additionally;
        $address->user_id = auth()->user()->id;
        $address->save();

        return response()->json($address);
    }


    /**
     * @OA\Delete(
     *     path="/api/delete_address",
     *     summary="Удалить адрес доставки",
     *     tags={"Address"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Address successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteAddress(Request $request): JsonResponse
    {
        if (Address::find($request->id) != null) {
            $address = Address::find($request->id);
            $address->delete();
            return response()->json(['Message' => 'Address successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/get_category_info/{category_id}",
     *     summary="Информация о категории по ID",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="path",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="title", type="string", example="Коты"),
     *                 @OA\Property(property="code", type="number", example="cats"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getCategoryInfo($category_id): JsonResponse
    {
        if (Category::where('id', $category_id)->first() != null) {
            $category = Category::where('id', $category_id)->first();
            return response()->json($category);
        } else {
            return response()->json(['Message' => 'Category not found.'], 404);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/get_subcategory_info/{subcategory_id}",
     *     summary="Информация о категории по ID",
     *     tags={"Subcategory"},
     *     @OA\Parameter(
     *         name="subcategory_id",
     *         in="path",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="category_title", type="string", example="Коты"),
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="code", type="number", example="cat_food"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getSubcategoryInfo($subcategory_id): JsonResponse
    {
        if (Subcategory::where('id', $subcategory_id)->first() != null) {
            $category = Subcategory::where('id', $subcategory_id)->first();
            return response()->json($category);
        } else {
            return response()->json(['Message' => 'Subcategory not found.'], 404);
        }
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 43200
        ]);
    }
}
