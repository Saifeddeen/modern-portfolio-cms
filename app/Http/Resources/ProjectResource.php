<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'slug'               => $this->slug,
            'title'              => $this->title,
            'subtitle'           => $this->subtitle,
            'owner'              => $this->owner,
            'short_description'  => $this->short_description,
            'long_description'   => $this->long_description,
            'hero_image'         => $this->hero_image,
            'gallery_images'     => $this->getMedia('gallery')->map(function ($media) {
                return $media->getUrl();
            }),
            'github_link'        => $this->github_link,
            'project_link'       => $this->project_link,
            'start_date'         => $this->start_date?->format('Y-m-d'),
            'project_date'       => $this->project_date?->format('Y-m-d'),
            'is_featured'        => $this->is_featured,
            'is_published'       => $this->is_published,
            'services'           => ServiceResource::collection($this->whenLoaded('services')),
            'skills'             => SkillResource::collection($this->whenLoaded('skills')),
            'technologies'       => TechnologyResource::collection($this->whenLoaded('technologies')),
        ];
    }
}
