<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        // firstOrCreate ensures there is always exactly 1 row for site settings
        $settings = SiteSetting::firstOrCreate(['id' => 1]);

        return Inertia::render('Settings/Edit', [
            'settings' => $settings,
            'locales' => config('app.locales', ['en' => 'English']), // Pass available languages to Vue
        ]);
    }

    public function update(Request $request)
    {
        $settings = SiteSetting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'array'],
            'job_title' => ['required', 'array'],
            'bio' => ['nullable', 'array'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'cv_link' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        // Handle Translatable Fields
        $settings->setTranslations('name', $validated['name']);
        $settings->setTranslations('job_title', $validated['job_title']);
        $settings->setTranslations('bio', $validated['bio'] ?? []);

        $settings->title = $validated['title'];

        // Handle File Uploads
        if ($request->hasFile('logo')) {
            if ($settings->getRawOriginal('logo')) Storage::disk('public')->delete($settings->getRawOriginal('logo'));
            $settings->logo = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('avatar')) {
            if ($settings->getRawOriginal('avatar')) Storage::disk('public')->delete($settings->getRawOriginal('avatar'));
            $settings->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle CV File Upload
        if ($request->hasFile('cv_link')) {
            if ($settings->getRawOriginal('cv_link')) Storage::disk('public')->delete($settings->getRawOriginal('cv_link'));
            $settings->cv_link = $request->file('cv_link')->store('cvs', 'public');
        }

        $settings->save();

        return back()->with('success', 'Settings updated successfully.');
    }
}
