<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Basket;
use App\Models\Category;
use App\Models\Email;
use App\Models\Favorites;
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
     *     path="/api/register",
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
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="name", type="string", example="Андрей"),
     *                 @OA\Property(property="surname", type="string", example="Лясковский"),
     *                 @OA\Property(property="address", type="string", example="1-й Красногвардейский проезд, 22с1, Москва"),
     *                 @OA\Property(property="phone", type="string", example="+7 (903) 111-11-11"),
     *                 @OA\Property(property="email", type="string", example="lae3145@mail.ru"),
     *                 @OA\Property(property="email_verified_at", type="string", example="null"),
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

                if (! $token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                return $this->respondWithToken($token);
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
     *                 @OA\Property(property="title", type="string", example="Акции"),
     *                 @OA\Property(property="code", type="string", example="sales"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
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
        $category->save();

        return response()->json(['Message' => 'Category successfully created']);
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
        try {
            $category = Category::find($request->id);
            $category->delete();
            return response()->json(['Message' => 'Category successfully deleted']);
        } catch (\Exception $exception) {
            return response()->json(['Message' => 'Error']);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Избранное",
     *     tags={"Profile"},
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
     *                 @OA\Property(property="product_id", type="number", example="2"),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function favorites()
    {
        return Favorites::where('user_id', auth()->user()->id)->get();
    }




    /**
     * @OA\Post(
     *     path="/api/add_favorites",
     *     summary="Добавить в избранное",
     *     tags={"Profile"},
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
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="product_id", type="number", example="2"),
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
        $favorite->user_id = Auth::user()->id;
        $favorite->product_id = $request->product_id;
        $favorite->save();

        return response()->json($favorite);
    }





    /**
     * @OA\Delete(
     *     path="/api/delete_favorites",
     *     summary="Удалить товар из избранного",
     *     tags={"Profile"},
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
        try {
            $favorite = Favorites::find($request->id);
            $favorite->delete();
            return response()->json(['Message' => 'Favorite successfully deleted']);
        } catch (\Exception $exception) {
            return response()->json(['Message' => 'Error']);
        }
    }




    /**
     * @OA\Get(
     *     path="/api/basket",
     *     summary="Корзина",
     *     tags={"Profile"},
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
     *                 @OA\Property(property="product_id", type="number", example="2"),
     *                 @OA\Property(property="user_id", type="number", example="5"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function basket()
    {
        return Basket::where('user_id', auth()->user()->id)->get();
    }


    /**
     * @OA\Post(
     *     path="/api/add_basket",
     *     summary="Добавить в корзину",
     *     tags={"Profile"},
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
     *                 @OA\Property(property="id", type="number", example="1"),
     *                 @OA\Property(property="product_id", type="number", example="2"),
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
        $basket->user_id = Auth::user()->id;
        $basket->product_id = $request->product_id;
        $basket->save();

        return response()->json($basket);
    }



    /**
     * @OA\Delete(
     *     path="/api/delete_basket",
     *     summary="Удалить товар из коризны",
     *     tags={"Profile"},
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
        try {
            $basket = Basket::find($request->id);
            $basket->delete();
            return response()->json(['Message' => 'Basket successfully deleted']);
        } catch (\Exception $exception) {
            return response()->json(['Message' => 'Error']);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/test",
     *     summary="тест",
     *     tags={"Admin"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  description="file to upload",
     *                  type="array",
     *                  @OA\Items(
     *                      type="string",
     *                      format="text",
     *                  ),
     *              )
     *            )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="accept",
     *         in="header",
     *         description="test",
     *         @OA\Schema(
     *             type="string",
     *             default="multipart/form-data"
     *         )
     *     ),
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
     *     ),
     *
     * )
     */
    public function test(Request $request): Request
    {
        return $request;
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
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 43200
        ]);
    }
}
