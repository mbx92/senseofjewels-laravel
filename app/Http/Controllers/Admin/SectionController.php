<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSectionRequest;
use App\Http\Requests\Admin\HeroSectionRequest;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SectionController extends Controller
{
    private function getHomePage(): Page
    {
        return Page::query()->firstOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'title' => 'Home', 'is_active' => true],
        );
    }

    // ── Hero ──────────────────────────────────────────────────────────────

    public function hero(): View
    {
        $page    = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'hero')
            ->first();

        return view('admin.sections.hero', compact('section'));
    }

    public function updateHero(HeroSectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $data = [
            'type'      => 'hero',
            'title'     => $request->title,
            'subtitle'  => $request->subtitle,
            'cta_text'  => $request->cta_text,
            'cta_url'   => $request->cta_url,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('background_image')) {
            $section = Section::query()
                ->where('page_id', $page->id)
                ->where('key', 'hero')
                ->first();

            if ($section?->image_path) {
                Storage::disk('public')->delete($section->image_path);
            }

            $data['image_path'] = $request->file('background_image')
                ->store('images/sections', 'public');
        }

        Section::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'hero'],
            $data,
        );

        return redirect()->route('admin.hero')->with('success', 'Hero section berhasil diperbarui.');
    }

    // ── About ─────────────────────────────────────────────────────────────

    public function about(): View
    {
        $page    = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'about')
            ->first();

        return view('admin.sections.about', compact('section'));
    }

    public function updateAbout(AboutSectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $data = [
            'type'      => 'about',
            'title'     => $request->title,
            'content'   => $request->content,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $section = Section::query()
                ->where('page_id', $page->id)
                ->where('key', 'about')
                ->first();

            if ($section?->image_path) {
                Storage::disk('public')->delete($section->image_path);
            }

            $data['image_path'] = $request->file('image')
                ->store('images/sections', 'public');
        }

        Section::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'about'],
            $data,
        );

        return redirect()->route('admin.about')->with('success', 'About section berhasil diperbarui.');
    }
}
