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
        'email',
        'job_title',
        'bio',
        'avatar',
        'cv_link',
        'phone',
        'address'
    ];

    public array $translatable = [
        'name',
        'job_title',
        'bio',
        'address'
    ];

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
