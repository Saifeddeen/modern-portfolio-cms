<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'link'        => $this->link,
            'vue_iconify' => $this->vue_iconify,
            'svg_icon'    => $this->svg_icon,
            'is_active'   => $this->is_active,
        ];
    }
}
