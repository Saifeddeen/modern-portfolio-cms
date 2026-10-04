<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'     => $this->title,
            'logo'      => $this->logo,
            'name'      => $this->name,
            'job_title' => $this->job_title,
            'bio'       => $this->bio,
            'avatar'    => $this->avatar,
            'cv_link'   => $this->cv_link,
            'phone'     => $this->phone,
            'address'   => $this->address,
            'email'     => $this->email,
        ];
    }
}
