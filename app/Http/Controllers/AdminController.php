<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/add_admin",
     *     summary="Добавить админа",
     *     tags={"Admin"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin added successfully",
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
    public function addAdmin(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        $user->role = 'admin';
        $user->save();

        return response()->json(['Message' => $user->name . ' is now admin']);
    }




    /**
     * @OA\Post(
     *     path="/api/delete_admin",
     *     summary="Удалить админа",
     *     tags={"Admin"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin deleted successfully",
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
    public function deleteAdmin(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        $user->role = 'user';
        $user->save();

        return response()->json(['Message' => $user->name . ' is no longer an admin']);
    }



    /**
     * @OA\Get(
     *     path="/api/user/{user_id}",
     *     summary="Информаиця о пользователе",
     *     tags={"Profile"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
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
    public function getUser($user_id)
    {
        return User::find($user_id);
    }
}
