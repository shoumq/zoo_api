<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     version="1.0",
 *     title="Shop API"
 * )
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }



    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Вход",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
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
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    /**
     * @OA\Post(
     *     path="/api/auth/me",
     *     summary="Информаиця о текущем пользователе",
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
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Выход",
     *     tags={"Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
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
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Обновление токена",
     *     tags={"Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully refresh",
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
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
