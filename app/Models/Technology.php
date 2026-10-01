<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Technology extends Model
{
    use HasTranslations;

    protected $fillable = [
        'vue_iconify',
        'svg_icon',
        'name',
        'short_description',
    ];

    protected $casts = [
        'name' => 'json',
        'short_description' => 'json',
    ];

    public $translatable = [
        'name',
        'short_description',
    ];
}
