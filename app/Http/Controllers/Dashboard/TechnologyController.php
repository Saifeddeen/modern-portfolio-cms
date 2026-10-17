<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
                'vue_iconify'       => ['nullable', 'string', 'max:255'],
                'svg_icon'          => ['nullable', 'string', new ValidSvg],
                'name'              => ['required', 'array'],
                'name.*'            => ['required', 'string', 'max:255'],
                'short_description' => ['nullable', 'array'],
                'short_description.*' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Item #" . ($index + 1) . ": " . implode(' ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();

            $technology = Technology::create([
                'vue_iconify' => $validated['vue_iconify'] ?? null,
                'svg_icon'    => $validated['svg_icon'] ?? null,
            ]);

            $technology->setTranslations('name', $validated['name']);
            $technology->setTranslations('short_description', $validated['short_description'] ?? []);
            $technology->save();

            $created++;
        }

        if (! empty($errors)) {
            $message = "Imported {$created} technologies. Skipped " . count($errors) . " items with errors: " . implode(' | ', $errors);

            return back()->with('warning', $message);
        }

        return back()->with('success', "Successfully imported {$created} technologies.");
    }
}
