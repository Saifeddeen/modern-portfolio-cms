<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'owner',
        'short_description',
        'long_description',
        'hero_image',
        'github_link',
        'project_link',
        'start_date',
        'project_date',
        'is_featured',
        'is_published'
    ];

    public array $translatable = [
        'title',
        'subtitle',
        'short_description',
        'long_description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'project_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getHeroImageAttribute($value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }

    // Spatie Media Library collection
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    // Relations
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }
}
