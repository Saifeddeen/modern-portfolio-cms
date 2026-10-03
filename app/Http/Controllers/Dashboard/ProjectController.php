<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['services', 'skills', 'technologies'])->orderBy('id', 'desc')->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'services' => Service::orderBy('id', 'desc')->get(),
            'skills' => Skill::orderBy('id', 'desc')->get(),
            'technologies' => Technology::orderBy('id', 'desc')->get(),
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')],
            'subtitle' => ['nullable', 'array'],
            'owner' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'array'],
            'long_description' => ['nullable', 'array'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
            'github_link' => ['nullable', 'string', 'url'],
            'project_link' => ['nullable', 'string', 'url'],
            'start_date' => ['nullable', 'date'],
            'project_date' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['exists:technologies,id'],
        ]);

        $project = new Project();
        $project->slug = $validated['slug'] ?? Str::slug($validated['title']['en'] ?? 'project');
        $project->owner = $validated['owner'] ?? null;
        $project->github_link = $validated['github_link'] ?? null;
        $project->project_link = $validated['project_link'] ?? null;
        $project->start_date = $validated['start_date'] ?? null;
        $project->project_date = $validated['project_date'] ?? null;
        $project->is_featured = $validated['is_featured'] ?? false;
        $project->is_published = $validated['is_published'] ?? true;

        $project->setTranslations('title', $validated['title']);
        $project->setTranslations('subtitle', $validated['subtitle'] ?? []);
        $project->setTranslations('short_description', $validated['short_description'] ?? []);
        $project->setTranslations('long_description', $validated['long_description'] ?? []);

        if ($request->hasFile('hero_image')) {
            $project->hero_image = $request->file('hero_image')->store('projects', 'public');
        }

        $project->save();

        // Sync Relations
        $project->services()->sync($validated['services'] ?? []);
        $project->skills()->sync($validated['skills'] ?? []);
        $project->technologies()->sync($validated['technologies'] ?? []);

        // Gallery Images (Spatie)
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $project->addMedia($image)->toMediaCollection('gallery');
            }
        }

        return back()->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project)
    {

        $validated = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string'],
            // Add ->ignore($project->id) so it doesn't fail when updating the same record
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($project->id)],
            'subtitle' => ['nullable', 'array'],
            'owner' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'array'],
            'long_description' => ['nullable', 'array'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
            'github_link' => ['nullable', 'string', 'url'],
            'project_link' => ['nullable', 'string', 'url'],
            'start_date' => ['nullable', 'date'],
            'project_date' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['exists:technologies,id'],
        ]);

        $project->slug = $validated['slug'] ?? Str::slug($validated['title']['en'] ?? 'project');
        $project->owner = $validated['owner'] ?? null;
        $project->github_link = $validated['github_link'] ?? null;
        $project->project_link = $validated['project_link'] ?? null;
        $project->start_date = $validated['start_date'] ?? null;
        $project->project_date = $validated['project_date'] ?? null;
        $project->is_featured = $validated['is_featured'] ?? false;
        $project->is_published = $validated['is_published'] ?? true;

        $project->setTranslations('title', $validated['title']);
        $project->setTranslations('subtitle', $validated['subtitle'] ?? []);
        $project->setTranslations('short_description', $validated['short_description'] ?? []);
        $project->setTranslations('long_description', $validated['long_description'] ?? []);

        if ($request->hasFile('hero_image')) {
            if ($project->getRawOriginal('hero_image')) Storage::disk('public')->delete($project->getRawOriginal('hero_image'));
            $project->hero_image = $request->file('hero_image')->store('projects', 'public');
        }

        $project->save();

        $project->services()->sync($validated['services'] ?? []);
        $project->skills()->sync($validated['skills'] ?? []);
        $project->technologies()->sync($validated['technologies'] ?? []);

        return back()->with('success', 'Project updated successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['services', 'skills', 'technologies']);

        $galleryImages = $project->getMedia('gallery')->map(function ($media) {
            return ['id' => $media->id, 'url' => $media->getUrl()];
        });

        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->getTranslations('title'), // FIX: Get all translations
                'subtitle' => $project->getTranslations('subtitle'),
                'owner' => $project->owner,
                'short_description' => $project->getTranslations('short_description'),
                'long_description' => $project->getTranslations('long_description'),
                'hero_image' => $project->hero_image,
                'gallery_images' => $galleryImages,
                'github_link' => $project->github_link,
                'project_link' => $project->project_link,
                'start_date' => $project->start_date?->format('Y-m-d'),
                'project_date' => $project->project_date?->format('Y-m-d'),
                'is_featured' => $project->is_featured,
                'is_published' => $project->is_published,
                'services' => $project->services,
                'skills' => $project->skills,
                'technologies' => $project->technologies,
            ],
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function storeGallery(Request $request, Project $project)
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $project->addMedia($image)->toMediaCollection('gallery');
            }
        }

        return back()->with('success', 'Gallery images uploaded successfully.');
    }

    public function destroy(Project $project)
    {
        if ($project->getRawOriginal('hero_image')) {
            Storage::disk('public')->delete($project->getRawOriginal('hero_image'));
        }
        $project->delete();
        return back()->with('success', 'Project deleted successfully.');
    }
}
