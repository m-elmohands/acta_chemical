<?php

namespace App\Http\Resources\Commerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->items->sum(function ($item): int {
            $price = $item->product->sale_price ?? $item->product->price;

            return (int) round(((float) $price) * 100) * $item->quantity;
        });

        return [
            'items' => $this->items->map(function ($item): array {
                $price = $item->product->sale_price ?? $item->product->price;
                $unitPrice = (int) round(((float) $price) * 100);

                return [
                    'id' => $item->id,
                    'product' => new ProductResource($item->product),
                    'quantity' => $item->quantity,
                    'unit_price_cents' => $unitPrice,
                    'line_total_cents' => $unitPrice * $item->quantity,
                ];
            })->values(),
            'items_count' => $this->items->sum('quantity'),
            'subtotal_cents' => $subtotal,
            'subtotal' => number_format($subtotal / 100, 2, '.', ''),
            'currency' => 'EGP',
        ];
    }
}
