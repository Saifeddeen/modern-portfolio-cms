<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations;

    protected $fillable = [
        'vue_iconify',
        'svg_icon',
        'hero_image',
        'name',
        'short_description',
        'full_description',
    ];

    protected $casts = [
        'name' => 'json',
        'short_description' => 'json',
        'full_description' => 'json',
    ];

    public array $translatable = [
        'name',
        'short_description',
        'full_description',
    ];

    public function getHeroImageAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
