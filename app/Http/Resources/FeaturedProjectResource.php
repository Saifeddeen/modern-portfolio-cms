<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeaturedProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'               => $this->slug,
            'title'              => $this->title,
            'owner'              => $this->owner,
            'short_description'  => $this->short_description,
            'hero_image'         => $this->hero_image,
            'technologies'       => TechnologyResource::collection($this->whenLoaded('technologies')),
        ];
    }
}
