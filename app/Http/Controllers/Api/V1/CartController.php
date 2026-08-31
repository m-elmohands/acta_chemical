<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Commerce\CartItemRequest;
use App\Http\Resources\Commerce\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** @group Cart */
class CartController extends ApiController
{
    /** Retrieve the current cart and calculated subtotal. */
    public function show(Request $request): JsonResponse
    {
        return $this->cartResponse($this->cart($request));
    }

    /** Add a product to the cart. */
    public function store(CartItemRequest $request): JsonResponse
    {
        $product = Product::query()->where('is_active', true)->findOrFail($request->integer('product_id'));
        $quantity = $request->integer('quantity');
        $this->ensureStock($product, $quantity);
        $cart = $this->cart($request);

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $newQuantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $this->ensureStock($product, $newQuantity);
        $item->quantity = $newQuantity;
        $item->save();

        return $this->cartResponse($cart, 'Cart updated successfully.');
    }

    /** Change a cart item's quantity. */
    public function update(CartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->cart($request);
        abort_unless($cartItem->cart_id === $cart->id, 404);
        $cartItem->load('product');
        $this->ensureStock($cartItem->product, $request->integer('quantity'));
        $cartItem->update(['quantity' => $request->integer('quantity')]);

        return $this->cartResponse($cart, 'Cart updated successfully.');
    }

    /** Remove an item from the cart. */
    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        $cart = $this->cart($request);
        abort_unless($cartItem->cart_id === $cart->id, 404);
        $cartItem->delete();

        return $this->cartResponse($cart, 'Cart item removed successfully.');
    }

    private function cart(Request $request): Cart
    {
        return $request->user()->cart()->firstOrCreate();
    }

    private function cartResponse(Cart $cart, string $message = 'Cart retrieved successfully.'): JsonResponse
    {
        $cart->load(['items.product.category.media', 'items.product.media']);

        return $this->success((new CartResource($cart))->resolve(), $message);
    }

    private function ensureStock(Product $product, int $quantity): void
    {
        abort_if($product->track_inventory && $quantity > $product->stock_quantity, 422, 'Requested quantity is not available.');
    }
}
