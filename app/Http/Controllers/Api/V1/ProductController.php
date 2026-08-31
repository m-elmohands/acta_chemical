<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Commerce\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Catalog */
class ProductController extends ApiController
{
    /**
     * Search and filter active products.
     *
     * @unauthenticated
     *
     * @queryParam search string Search product names and SKU. Example: acid
     * @queryParam category string Category ID or slug. Example: acids
     * @queryParam featured boolean Return featured products only. Example: true
     * @queryParam in_stock boolean Return available products only. Example: true
     * @queryParam min_price numeric Minimum regular price in EGP. Example: 100
     * @queryParam max_price numeric Maximum regular price in EGP. Example: 2000
     * @queryParam sort string Sort by featured, price_asc, price_desc, latest, or name. Example: price_asc
     * @queryParam per_page integer Page size between 1 and 50. Example: 20
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->with(['category.media', 'media']);

        $query->when($request->filled('category'), function (Builder $query) use ($request): void {
            $category = $request->string('category')->toString();
            $query->whereHas('category', fn (Builder $query) => $query
                ->where('id', $category)
                ->orWhere('slug', $category));
        });
        $query->when($request->boolean('featured'), fn (Builder $query) => $query->where('is_featured', true));
        $query->when($request->boolean('in_stock'), fn (Builder $query) => $query
            ->where(fn (Builder $query) => $query->where('track_inventory', false)->orWhere('stock_quantity', '>', 0)));
        $query->when($request->filled('search'), function (Builder $query) use ($request): void {
            $search = '%'.$request->string('search')->toString().'%';
            $query->where(fn (Builder $query) => $query
                ->where('name_en', 'like', $search)
                ->orWhere('name_ar', 'like', $search)
                ->orWhere('sku', 'like', $search));
        });
        $query->when($request->filled('min_price'), fn (Builder $query) => $query->where('price', '>=', $request->input('min_price')));
        $query->when($request->filled('max_price'), fn (Builder $query) => $query->where('price', '<=', $request->input('max_price')));

        match ($request->input('sort', 'featured')) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'latest' => $query->latest(),
            'name' => $query->orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };

        $products = $query->paginate(min(max($request->integer('per_page', 20), 1), 50));

        return $this->success(ProductResource::collection($products->items())->resolve(), 'Products retrieved successfully.', meta: [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }

    /**
     * Retrieve an active product.
     *
     * @unauthenticated
     */
    public function show(Product $product): JsonResponse
    {
        abort_unless($product->is_active && $product->category()->where('is_active', true)->exists(), 404);
        $product->load(['category.media', 'media']);

        return $this->success((new ProductResource($product))->resolve(), 'Product retrieved successfully.');
    }
}
