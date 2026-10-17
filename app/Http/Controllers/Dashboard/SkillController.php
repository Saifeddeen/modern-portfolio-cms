<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'svg_icon'    => ['nullable', 'string', new ValidSvg],
            'title'       => ['required', 'array'],
            'description' => ['nullable', 'array'],
        ]);

        $skill = Skill::create([
            'vue_iconify' => $validated['vue_iconify'],
            'svg_icon'    => $validated['svg_icon'],
        ]);

        $skill->setTranslations('title', $validated['title']);
        $skill->setTranslations('description', $validated['description'] ?? []);
        $skill->save();

        return back()->with('success', 'Skill created successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'vue_iconify' => ['nullable', 'string', 'max:255'],
            'svg_icon'    => ['nullable', 'string', new ValidSvg],
            'title'       => ['required', 'array'],
            'description' => ['nullable', 'array'],
        ]);

        $skill->vue_iconify = $validated['vue_iconify'];
        $skill->svg_icon    = $validated['svg_icon'];

        $skill->setTranslations('title', $validated['title']);
        $skill->setTranslations('description', $validated['description'] ?? []);
        $skill->save();

        return back()->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return back()->with('success', 'Skill deleted successfully.');
    }

    public function importJson(Request $request)
    {
        $request->validate([
            'json_file' => ['required', 'file', 'mimes:json', 'max:2048'],
        ]);

        $content = file_get_contents($request->file('json_file')->getRealPath());
        $items   = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($items)) {
            return back()->withErrors(['json_file' => 'Invalid JSON format. The file must contain a valid JSON array.']);
        }

        $errors  = [];
        $created = 0;

        foreach ($items as $index => $item) {
            $validator = Validator::make($item, [
                'vue_iconify'   => ['nullable', 'string', 'max:255'],
                'svg_icon'      => ['nullable', 'string', new ValidSvg],
                'title'         => ['required', 'array'],
                'title.*'       => ['required', 'string', 'max:255'],
                'description'   => ['nullable', 'array'],
                'description.*' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Item #" . ($index + 1) . ": " . implode(' ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();

            $skill = Skill::create([
                'vue_iconify' => $validated['vue_iconify'] ?? null,
                'svg_icon'    => $validated['svg_icon'] ?? null,
            ]);

            $skill->setTranslations('title', $validated['title']);
            $skill->setTranslations('description', $validated['description'] ?? []);
            $skill->save();

            $created++;
        }

        if (! empty($errors)) {
            $message = "Imported {$created} skills. Skipped " . count($errors) . " items with errors: " . implode(' | ', $errors);

            return back()->with('warning', $message);
        }

        return back()->with('success', "Successfully imported {$created} skills.");
    }
}
