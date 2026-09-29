<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $skills = Skill::orderBy('id', 'desc')->get();

        return $this->successResponse(
            data: SkillResource::collection($skills),
            message: 'Skills fetched successfully.'
        );
    }
}
