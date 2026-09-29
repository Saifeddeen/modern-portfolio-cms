<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'vue_iconify'       => $this->vue_iconify,
            'svg_icon'          => $this->svg_icon,
            'name'              => $this->name,
            'short_description' => $this->short_description,
        ];
    }
}
