<?php

namespace App\Commerce\Actions;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrderFromCart
{
    /** @param array<string, mixed> $data */
    public function execute(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data): Order {
            $address = Address::query()
                ->where('user_id', $user->id)
                ->findOrFail($data['address_id']);
            $cart = $user->cart()->with('items')->first();

            if ($cart === null || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => ['The cart is empty.']]);
            }

            $products = Product::query()
                ->whereIn('id', $cart->items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $subtotal = 0;
            $orderLines = [];

            foreach ($cart->items as $cartItem) {
                $product = $products->get($cartItem->product_id);

                if ($product === null || ! $product->is_active) {
                    throw ValidationException::withMessages(['cart' => ['A product in the cart is no longer available.']]);
                }

                if ($product->track_inventory && $cartItem->quantity > $product->stock_quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ["Insufficient stock for {$product->name_en}."],
                    ]);
                }

                $unitPrice = (int) round(((float) ($product->sale_price ?? $product->price)) * 100);
                $lineTotal = $unitPrice * $cartItem->quantity;
                $subtotal += $lineTotal;
                $orderLines[] = [
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'unit_price_cents' => $unitPrice,
                    'quantity' => $cartItem->quantity,
                    'line_total_cents' => $lineTotal,
                ];

                if ($product->track_inventory) {
                    $product->decrement('stock_quantity', $cartItem->quantity);
                }
            }

            $deliveryFee = config('commerce.delivery_fee_cents');
            $order = $user->orders()->create([
                'reference' => (string) Str::uuid(),
                'address_id' => $address->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'],
                'subtotal_cents' => $subtotal,
                'delivery_fee_cents' => $deliveryFee,
                'total_cents' => $subtotal + $deliveryFee,
                'currency' => config('commerce.currency'),
                'shipping_address' => $address->only([
                    'label', 'first_name', 'last_name', 'phone', 'street', 'building',
                    'floor', 'apartment', 'city', 'state', 'country', 'delivery_notes',
                ]),
                'customer_notes' => $data['customer_notes'] ?? null,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($orderLines);
            $cart->items()->delete();

            return $order->load('items');
        });
    }
}
