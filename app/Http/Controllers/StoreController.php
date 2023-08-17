<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/get_stores",
     *     summary="Магазины",
     *     tags={"Stores"},
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="title", type="string", example="Авиапарк"),
     *                 @OA\Property(property="code", type="string", example="aviapark"),
     *                 @OA\Property(property="description", type="string", example="Торговый центр"),
     *                 @OA\Property(property="address", type="string", example="Ходынский бул., 4, Москва"),
     *                 @OA\Property(property="rating", type="number", example="5.0"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getStores(): JsonResponse
    {
        return response()->json(Store::all());
    }


    /**
     * @OA\Get(
     *     path="/api/get_store_by_id/{store_id}",
     *     summary="Найти магазин по id",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="store_id",
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
     *                 @OA\Property(property="title", type="string", example="Авиапарк"),
     *                 @OA\Property(property="code", type="string", example="aviapark"),
     *                 @OA\Property(property="description", type="string", example="Торговый центр"),
     *                 @OA\Property(property="address", type="string", example="Ходынский бул., 4, Москва"),
     *                 @OA\Property(property="rating", type="number", example="5.0"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getStoreById($store_id): JsonResponse
    {
        $store = Store::where('id', $store_id)->first();
        if ($store != null) {
            return response()->json($store);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/get_store_by_code/{store_code}",
     *     summary="Найти магазин по code",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="store_code",
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
     *                 @OA\Property(property="title", type="string", example="Авиапарк"),
     *                 @OA\Property(property="code", type="string", example="aviapark"),
     *                 @OA\Property(property="description", type="string", example="Торговый центр"),
     *                 @OA\Property(property="address", type="string", example="Ходынский бул., 4, Москва"),
     *                 @OA\Property(property="rating", type="number", example="5.0"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getStoreByCode($store_code): JsonResponse
    {
        $store = Store::where('code', $store_code)->first();
        if ($store != null) {
            return response()->json($store);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/add_store",
     *     summary="Добавить магазин",
     *     tags={"Stores"},
     *     security={{"bearer_token":{}}},
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
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="rating",
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
     *                 @OA\Property(property="title", type="string", example="Авиапарк"),
     *                 @OA\Property(property="code", type="string", example="aviapark"),
     *                 @OA\Property(property="description", type="string", example="Торговый центр"),
     *                 @OA\Property(property="address", type="string", example="Ходынский бул., 4, Москва"),
     *                 @OA\Property(property="rating", type="number", example="5.0"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function addStore(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['string', 'max:50', 'min:1'],
            'code' => ['string', 'max:150', 'min:1'],
            'description' => ['string', 'max:1000', 'min:1'],
        ]);

        $store = new Store();
        $store->title = $request->title;
        $store->code = $request->code;
        $store->description = $request->description;
        $store->address = $request->address;
        $store->rating = $request->rating;
        $store->save();

        return response()->json($store);
    }



    /**
     * @OA\Delete (
     *     path="/api/delete_store",
     *     summary="Удалить магазин",
     *     tags={"Stores"},
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
    public function deleteStore(Request $request): JsonResponse
    {
        if (Store::find($request->id) != null) {
            $store = Store::find($request->id);
            $store->delete();
            return response()->json(['Message' => 'Store successfully deleted']);
        } else {
            return response()->json(['Message' => 'Not found.'], 404);
        }
    }
}
