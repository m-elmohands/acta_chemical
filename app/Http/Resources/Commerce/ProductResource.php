<?php

namespace App\Http\Resources\Commerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $priceCents = (int) round(((float) $this->price) * 100);
        $salePriceCents = $this->sale_price === null ? null : (int) round(((float) $this->sale_price) * 100);

        return [
            'id' => $this->id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $this->localized('name'),
            'short_description' => $this->localized('short_description'),
            'description' => $this->localized('description'),
            'translations' => [
                'name' => $this->translations('name'),
                'short_description' => $this->translations('short_description'),
                'description' => $this->translations('description'),
            ],
            'price' => number_format($priceCents / 100, 2, '.', ''),
            'sale_price' => $salePriceCents === null ? null : number_format($salePriceCents / 100, 2, '.', ''),
            'effective_price_cents' => $salePriceCents ?? $priceCents,
            'currency' => 'EGP',
            'in_stock' => ! $this->track_inventory || $this->stock_quantity > 0,
            'stock_quantity' => $this->track_inventory ? $this->stock_quantity : null,
            'is_featured' => $this->is_featured,
            'thumbnail_url' => $this->getFirstMediaUrl('thumbnail') ?: null,
            'gallery' => $this->getMedia('gallery')->map(fn ($media) => [
                'url' => $media->getUrl(),
                'thumbnail_url' => $media->getUrl('thumb'),
            ])->values(),
        ];
    }
}
