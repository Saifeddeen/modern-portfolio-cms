<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechnologyController extends Controller
{
    public function index()
    {
        $technologies = Technology::orderBy('id', 'desc')->get();

        return Inertia::render('Technologies/Index', [
            'technologies' => $technologies,
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vue_iconify' => ['nullable', 'string', 'max:255'],
            'svg_icon' => ['nullable', 'string', new ValidSvg],
            'name' => ['required', 'array'],
            'short_description' => ['nullable', 'array'],
        ]);

        $technology = Technology::create([
            'vue_iconify' => $validated['vue_iconify'],
            'svg_icon'    => $validated['svg_icon'],
        ]);

        $technology->setTranslations('name', $validated['name']);
        $technology->setTranslations('short_description', $validated['short_description'] ?? []);
        $technology->save();

        return back()->with('success', 'Technology created successfully.');
    }

    public function update(Request $request, Technology $technology)
    {
        $validated = $request->validate([
            'vue_iconify' => ['nullable', 'string', 'max:255'],
            'svg_icon' => ['nullable', 'string', new ValidSvg],
            'name' => ['required', 'array'],
            'short_description' => ['nullable', 'array'],
        ]);

        $technology->vue_iconify = $validated['vue_iconify'];
        $technology->svg_icon    = $validated['svg_icon'];

        $technology->setTranslations('name', $validated['name']);
        $technology->setTranslations('short_description', $validated['short_description'] ?? []);
        $technology->save();

        return back()->with('success', 'Technology updated successfully.');
    }

    public function destroy(Technology $technology)
    {
        $technology->delete();
        return back()->with('success', 'Technology deleted successfully.');
    }
}
