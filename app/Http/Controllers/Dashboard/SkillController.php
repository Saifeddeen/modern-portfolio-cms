<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('id', 'desc')->get();

        return Inertia::render('Skills/Index', [
            'skills' => $skills,
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

        $skill = Skill::create([
            'vue_iconify' => $validated['vue_iconify'],
            'svg_icon'    => $validated['svg_icon'],
        ]);

        $skill->setTranslations('name', $validated['name']);
        $skill->setTranslations('short_description', $validated['short_description'] ?? []);
        $skill->save();

        return back()->with('success', 'Skill created successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'vue_iconify' => ['nullable', 'string', 'max:255'],
            'svg_icon' => ['nullable', 'string', new ValidSvg],
            'name' => ['required', 'array'],
            'short_description' => ['nullable', 'array'],
        ]);

        $skill->vue_iconify = $validated['vue_iconify'];
        $skill->svg_icon    = $validated['svg_icon'];

        $skill->setTranslations('name', $validated['name']);
        $skill->setTranslations('short_description', $validated['short_description'] ?? []);
        $skill->save();

        return back()->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return back()->with('success', 'Skill deleted successfully.');
    }
}
