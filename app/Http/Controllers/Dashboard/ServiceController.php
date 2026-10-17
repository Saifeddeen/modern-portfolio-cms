<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id', 'desc')->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'locales' => config('app.locales', ['en' => 'English']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vue_iconify'       => ['nullable', 'string', 'max:255'],
            'svg_icon'          => ['nullable', 'string', new ValidSvg],
            'hero_image'        => ['nullable', 'image', 'max:4096'],
            'name'              => ['required', 'array'],
            'short_description' => ['required', 'array'],
            'full_description'  => ['nullable', 'array'],
        ]);

        $heroImagePath = null;
        if ($request->hasFile('hero_image')) {
            $heroImagePath = $request->file('hero_image')->store('services', 'public');
        }

        $service = new Service();
        $service->vue_iconify = $validated['vue_iconify'] ?? null;
        $service->svg_icon    = $validated['svg_icon'] ?? null;
        $service->hero_image  = $heroImagePath;

        $service->setTranslations('name', $validated['name']);
        $service->setTranslations('short_description', $validated['short_description']);
        if (isset($validated['full_description'])) {
            $service->setTranslations('full_description', $validated['full_description']);
        }
        $service->save();

        return back()->with('success', 'Service created successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'vue_iconify'       => ['nullable', 'string', 'max:255'],
            'svg_icon'          => ['nullable', 'string', new ValidSvg],
            'hero_image'        => ['nullable', 'image', 'max:4096'],
            'name'              => ['required', 'array'],
            'short_description' => ['required', 'array'],
            'full_description'  => ['nullable', 'array'],
        ]);

        $service->vue_iconify = $validated['vue_iconify'] ?? null;
        $service->svg_icon    = $validated['svg_icon'] ?? null;

        if ($request->hasFile('hero_image')) {
            if ($service->getRawOriginal('hero_image')) {
                Storage::disk('public')->delete($service->getRawOriginal('hero_image'));
            }
            $service->hero_image = $request->file('hero_image')->store('services', 'public');
        }

        $service->setTranslations('name', $validated['name']);
        $service->setTranslations('short_description', $validated['short_description']);
        if (isset($validated['full_description'])) {
            $service->setTranslations('full_description', $validated['full_description']);
        } else {
            $service->setTranslations('full_description', []);
        }
        $service->save();

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->getRawOriginal('hero_image')) {
            Storage::disk('public')->delete($service->getRawOriginal('hero_image'));
        }

        $service->delete();

        return back()->with('success', 'Service deleted successfully.');
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
                'vue_iconify'           => ['nullable', 'string', 'max:255'],
                'svg_icon'              => ['nullable', 'string', new ValidSvg],
                'name'                  => ['required', 'array'],
                'name.*'                => ['required', 'string', 'max:255'],
                'short_description'     => ['required', 'array'],
                'short_description.*'   => ['required', 'string'],
                'full_description'      => ['nullable', 'array'],
                'full_description.*'    => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Item #" . ($index + 1) . ": " . implode(' ', $validator->errors()->all());
                continue;
            }

            $validated = $validator->validated();

            $service = new Service();
            $service->vue_iconify = $validated['vue_iconify'] ?? null;
            $service->svg_icon    = $validated['svg_icon'] ?? null;

            $service->setTranslations('name', $validated['name']);
            $service->setTranslations('short_description', $validated['short_description']);

            if (isset($validated['full_description'])) {
                $service->setTranslations('full_description', $validated['full_description']);
            }

            $service->save();

            $created++;
        }

        if (! empty($errors)) {
            $message = "Imported {$created} services. Skipped " . count($errors) . " items with errors: " . implode(' | ', $errors);

            return back()->with('warning', $message);
        }

        return back()->with('success', "Successfully imported {$created} services.");
    }
}
