<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'name',
        'link',
        'vue_iconify',
        'svg_icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
