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
}
