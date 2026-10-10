<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Rules\ValidSvg;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SocialLinkController extends Controller
{
    public function index()
    {
        $links = SocialLink::orderBy('id', 'desc')->get();

        return Inertia::render('SocialLinks/Index', [
            'links' => $links,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'link'         => ['required', 'string', 'url'],
            'vue_iconify'  => ['nullable', 'string', 'max:255'],
            'svg_icon'     => ['nullable', 'string', new ValidSvg],
            'is_active'    => ['boolean'],
        ]);

        SocialLink::create($validated);

        return back()->with('success', 'Social link created successfully.');
    }

    public function update(Request $request, SocialLink $socialLink)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'link'         => ['required', 'string', 'url'],
            'vue_iconify'  => ['nullable', 'string', 'max:255'],
            'svg_icon'     => ['nullable', 'string', new ValidSvg],
            'is_active'    => ['boolean'],
        ]);

        $socialLink->update($validated);

        return back()->with('success', 'Social link updated successfully.');
    }

    public function destroy(SocialLink $socialLink)
    {
        $socialLink->delete();
        return back()->with('success', 'Social link deleted successfully.');
    }
}
