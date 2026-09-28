<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'logo',
        'name',
        'job_title',
        'bio',
        'avatar',
        'cv_link',
    ];

    // Define translatable fields
    public array $translatable = [
        'name',
        'job_title',
        'bio',
    ];

    // Accessors to get full URL for files
    public function getLogoAttribute($value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function getAvatarAttribute($value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function getCvLinkAttribute($value): ?string
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
