<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $services = Service::orderBy('id', 'desc')->get();

        return $this->successResponse(
            data: ServiceResource::collection($services),
            message: 'Services fetched successfully.'
        );
    }
}
