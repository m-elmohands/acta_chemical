<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use HasLocalizedAttributes;

    protected array $localizedFields = ['value'];

    protected $fillable = [
        'key',
        'group',
        'label',
        'value_en',
        'value_ar',
        'image',
    ];

    protected $casts = [
        'value_en' => 'string',
        'value_ar' => 'string',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return filled($this->image)
            ? url('storage/'.ltrim($this->image, '/'))
            : null;
    }

    public function localizedValue(?string $locale = null): ?string
    {
        return $this->localized('value', $locale);
    }
}
