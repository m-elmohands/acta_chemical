<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Commerce\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Catalog */
class CategoryController extends ApiController
{
    /**
     * List active categories.
     *
     * @unauthenticated
     *
     * @queryParam per_page integer Page size between 1 and 50. Example: 20
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->with('media')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(min(max($request->integer('per_page', 20), 1), 50));

        return $this->success(CategoryResource::collection($categories->items())->resolve(), 'Categories retrieved successfully.', meta: [
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
        ]);
    }

    /**
     * Retrieve an active category.
     *
     * @unauthenticated
     */
    public function show(Category $category): JsonResponse
    {
        abort_unless($category->is_active, 404);
        $category->load('media');

        return $this->success((new CategoryResource($category))->resolve(), 'Category retrieved successfully.');
    }
}
