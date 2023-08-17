<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/subcategories",
     *     summary="Подкатегории",
     *     tags={"Subcategory"},
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
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="code", type="string", example="cat_food"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function subcategories(): JsonResponse
    {
        return response()->json(Subcategory::all());
    }


    /**
     * @OA\Get(
     *     path="/api/get_subcategories_by_category_id/{category_id}",
     *     summary="Вывод подкатегорий по id категории",
     *     tags={"Subcategory"},
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
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="category_title", type="string", example="Коты"),
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="code", type="string", example="cat_food"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getSubcategoriesByCategoryId($category_id): JsonResponse
    {
        $categories = Subcategory::where('category_id', $category_id)->first();
        return response()->json($categories);
    }



    /**
     * @OA\Post(
     *     path="/api/add_subcategory",
     *     summary="Добавить подкатегорию",
     *     tags={"Subcategory"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="code",
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
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="code", type="string", example="cat_food"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function addSubcategory(Request $request): JsonResponse
    {
        $category_title = Category::where('id', $request->category_id)->first()->title;

        $subcategory = new Subcategory();
        $subcategory->category_id = $request->category_id;
        $subcategory->category_title = $category_title;
        $subcategory->title = $request->title;
        $subcategory->code = $request->code;
        $subcategory->save();

        return response()->json($subcategory);
    }


    /**
     * @OA\Delete (
     *     path="/api/delete_subcategory",
     *     summary="Удалить подкатегорию",
     *     tags={"Subcategory"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         )
     *     ),
     * )
     */
    public function deleteSubcategory(Request $request): JsonResponse
    {
        if (Subcategory::find($request->id) != null) {
            $address = Subcategory::find($request->id);
            $address->delete();
            return response()->json(['Message' => 'Subcategory successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }






    /**
     * Get the token array structure.
     *
     * @param string $token
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
