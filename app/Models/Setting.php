<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasLocalizedAttributes;

    protected array $localizedFields = ['title', 'description'];

    protected $fillable = [
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'watts',
        'email',
        'phone',
        'logo',
        'lat',
        'lng',
        'address',
    ];

    protected $appends = ['logo_url', 'map'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? url('storage/'.$this->logo) : null;
    }

    public function getMapAttribute()
    {
        if ($this->lat && $this->lng) {
            return "https://www.google.com/maps?q={$this->lat},{$this->lng}";

        }
    }
}
