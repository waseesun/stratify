<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CategoryRequest;
use App\Models\Category;
use Exception;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Category model",
 * @OA\Property(property="id", type="integer", format="int64", description="Category ID"),
 * @OA\Property(property="name", type="string", description="Category name", example="Electronics"),
 * example={
 * "id": 1,
 * "name": "Electronics"
 * }
 * )
 *
 * @OA\Schema(
 * schema="CategoryRequest",
 * title="Category Request",
 * description="Request body for creating or updating a category",
 * @OA\Property(property="name", type="string", description="Name of the category", example="Books"),
 * required={"name"}
 * )
 */
class CategoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/categories",
     * operationId="getCategoriesList",
     * tags={"Categories"},
     * summary="Get a list of all categories",
     * description="Retrieves a list of all categories.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Category")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json($categories);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/categories/{category}",
     * operationId="getCategoryById",
     * tags={"Categories"},
     * summary="Get a single category by ID",
     * description="Retrieves a single category by its ID.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="category",
     * in="path",
     * description="ID of the category to retrieve",
     * required=true,
     * @OA\Schema(type="integer", format="int64")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $category)
    {
        try {
            $category = Category::find($category);
            return response()->json($category);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/categories",
     * operationId="createCategory",
     * tags={"Categories"},
     * summary="Create a new category",
     * description="Creates a new category. Requires superadmin privileges.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Category data",
     * @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Category created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to create categories (e.g., not a superadmin).",
     * @OA\JsonContent(
     * @OA\Property(property="messages", type="array", @OA\Items(type="string"), description="Array of forbidden messages"),
     * example={"messages": {"You are not authorized to perform this action."}}
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Unprocessable Entity (Validation Error)",
     * @OA\JsonContent(
     * @OA\Property(property="errors", type="object", description="Validation errors"),
     * example={"errors": {"name": {"The name field is required."}}}
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(CategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            $category = Category::create($validated);
            return response()->json($category);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/categories/{category}",
     * operationId="deleteCategory",
     * tags={"Categories"},
     * summary="Delete a category by ID",
     * description="Deletes a category by its ID. Requires superadmin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="category",
     * in="path",
     * description="ID of the category to delete",
     * required=true,
     * @OA\Schema(type="integer", format="int64")
     * ),
     * @OA\Response(
     * response=204,
     * description="Category deleted successfully (No Content)"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete categories (e.g., not a superadmin).",
     * @OA\JsonContent(
     * @OA\Property(property="messages", type="array", @OA\Items(type="string"), description="Array of forbidden messages"),
     * example={"messages": {"You are not authorized to perform this action."}}
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $category)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                'errors' => 'You are not authorized to delete categories.'
            ], 403);
        }

        try {
            $category = Category::find($category);
            $category->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
