<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Продкуты",
     *     tags={"Product"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="subcategory_id", type="number", example="4"),
     *                 @OA\Property(property="description", type="string", example="Кошачий корм описание"),
     *                 @OA\Property(property="price", type="number", example="329.99"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getProducts(): JsonResponse
    {
        return response()->json(Product::all());
    }


    /**
     * @OA\Get(
     *     path="/api/product/{product_id}",
     *     summary="Продкут по ID",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="product_id",
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
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="subcategory_id", type="number", example="4"),
     *                 @OA\Property(property="description", type="string", example="Кошачий корм описание"),
     *                 @OA\Property(property="price", type="number", example="329.99"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function getProductsById($product_id): JsonResponse
    {
        return response()->json(Product::where('id', $product_id)->first());
    }


    /**
     * @OA\Post(
     *     path="/api/add_product",
     *     summary="Добавить продкут",
     *     tags={"Product"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="subcategory_id",
     *         in="query",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=true,
     *     ),@OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=true,
     *     ),
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema (
     *          type="array",
     *               @OA\Items(
     *                 @OA\Property(property="id", type="number", example="5"),
     *                 @OA\Property(property="title", type="string", example="Кошачий корм"),
     *                 @OA\Property(property="category_id", type="number", example="2"),
     *                 @OA\Property(property="subcategory_id", type="number", example="4"),
     *                 @OA\Property(property="description", type="string", example="Кошачий корм описание"),
     *                 @OA\Property(property="price", type="number", example="329.99"),
     *                 @OA\Property(property="created_at", type="time", example="2023-07-06T08:27:30.000000Z"),
     *                 @OA\Property(property="updated_at", type="time", example="2023-07-06T09:45:07.000000Z"),
     *            ),
     *          )
     *         )
     *     ),
     * )
     */
    public function addProduct(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['string', 'max:50', 'min:1'],
            'description' => ['string', 'max:1000', 'min:1'],
        ]);


        if (Subcategory::where('id', $request->subcategory_id)->first() != null) {
            $category_id = (int)Subcategory::where('id', $request->subcategory_id)->first()->category_id;
            $category_title = Subcategory::where('id', $request->subcategory_id)->first()->category_title;
            $subcategory_title = Subcategory::where('id', $request->subcategory_id)->first()->title;

            $product = new Product();
            $product->title = $request->title;
            $product->description = $request->description;
            $product->category_id = $category_id;
            $product->category_title = $category_title;
            $product->subcategory_id = (int)$request->subcategory_id;
            $product->subcategory_title = $subcategory_title;
            $product->price = $request->price;
            $product->save();

            return response()->json($product);
        } else {
            return response()->json(['Message' => 'Category not found.'], 404);
        }
    }



    /**
     * @OA\Delete(
     *     path="/api/delete_product",
     *     summary="Удалить продукт",
     *     tags={"Product"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=true,
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully deleted",
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Product not found.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function deleteProduct(Request $request): JsonResponse
    {
        if (Product::where('id', $request->product_id)->first() != null) {
            Product::where('id', $request->product_id)->delete();
            return response()->json(['Message' => 'Product successfully deleted.'], 404);
        } else {
            return response()->json(['Message' => 'Product not found.'], 404);
        }
    }
}
