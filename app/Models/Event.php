<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasLocalizedAttributes;

    protected array $localizedFields = ['title', 'content'];

    protected $fillable = [
        'title_en',
        'title_ar',
        'content_en',
        'content_ar',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image'])) {
            return url('storage/'.$this->attributes['image']);
        }
    }
}
