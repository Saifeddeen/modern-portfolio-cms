<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'vue_iconify'       => $this->vue_iconify,
            'svg_icon'          => $this->svg_icon,
            'hero_image'        => $this->hero_image,
            'name'              => $this->name,
            'short_description' => $this->short_description,
            'full_description'  => $this->full_description,
        ];
    }
}
