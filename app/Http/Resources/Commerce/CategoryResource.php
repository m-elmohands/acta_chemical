<?php

namespace App\Http\Resources\Commerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'slug' => $this->slug,
            'name' => $this->localized('name'),
            'translations' => ['name' => $this->translations('name')],
            'description' => $this->localized('description'),
            'image_url' => $this->getFirstMediaUrl('image') ?: null,
            'image_thumbnail_url' => $this->getFirstMediaUrl('image', 'thumb') ?: null,
        ];
    }
}
