<?php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialLinkResource;
use App\Models\SocialLink;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $links = SocialLink::where('is_active', true)->orderBy('id', 'asc')->get();

        return $this->successResponse(
            data: SocialLinkResource::collection($links),
            message: 'Social links fetched successfully.'
        );
    }
}
