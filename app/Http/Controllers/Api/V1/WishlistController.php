<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Commerce\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Wishlist */
class WishlistController extends ApiController
{
    /** List the customer's saved products. */
    public function index(Request $request): JsonResponse
    {
        $products = $request->user()->wishlistProducts()
            ->where('products.is_active', true)
            ->with(['category.media', 'media'])
            ->paginate(min(max($request->integer('per_page', 20), 1), 50));

        return $this->success(ProductResource::collection($products->items())->resolve(), 'Wishlist retrieved successfully.', meta: [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }

    /** Add an active product to the wishlist. */
    public function store(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);
        $request->user()->wishlistProducts()->syncWithoutDetaching([$product->id]);

        return $this->success(null, 'Product added to wishlist.', 201);
    }

    /** Remove a product from the wishlist. */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $request->user()->wishlistProducts()->detach($product->id);

        return $this->success(null, 'Product removed from wishlist.');
    }
}
