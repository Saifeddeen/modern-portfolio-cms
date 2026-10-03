<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Skill extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'vue_iconify',
        'svg_icon',
    ];

    public array $translatable = [
        'title',
        'description',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
