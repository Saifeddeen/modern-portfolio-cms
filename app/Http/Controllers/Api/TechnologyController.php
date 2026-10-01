<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TechnologyResource;
use App\Models\Technology;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $technologies = Technology::orderBy('id', 'desc')->get();

        return $this->successResponse(
            data: TechnologyResource::collection($technologies),
            message: 'Technologies fetched successfully.'
        );
    }
}
