<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeaturedProjectResource;
use App\Http\Resources\PublishedProjectResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    // 1. Featured Projects
    public function featured()
    {
        $projects = Project::with('technologies')
            ->where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('project_date', 'desc')
            ->get();

        return $this->successResponse(
            data: FeaturedProjectResource::collection($projects),
            message: 'Featured projects fetched successfully.'
        );
    }

    // 2. Published Projects (No Gallery)
    public function index()
    {
        $projects = Project::with(['services', 'skills', 'technologies'])
            ->where('is_published', true)
            ->orderBy('project_date', 'desc')
            ->get();

        return $this->successResponse(
            data: PublishedProjectResource::collection($projects),
            message: 'Published projects fetched successfully.'
        );
    }

    // 3. Specific Project (With Gallery & Sub-data)
    public function show(string $slug)
    {
        $project = Project::with(['services', 'skills', 'technologies'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->successResponse(
            data: new ProjectResource($project),
            message: 'Project fetched successfully.'
        );
    }
}
