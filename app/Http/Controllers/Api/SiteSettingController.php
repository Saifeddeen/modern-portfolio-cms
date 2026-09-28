<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    use ApiResponse; // Include the trait

    public function index(Request $request)
    {
        $settings = SiteSetting::firstOrCreate(['id' => 1]);

        // Wrap the resource in our custom success response
        return $this->successResponse(
            data: new SiteSettingResource($settings),
            message: 'Site settings fetched successfully.'
        );
    }
}
