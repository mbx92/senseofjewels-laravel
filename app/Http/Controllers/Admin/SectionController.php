<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSectionRequest;
use App\Http\Requests\Admin\HeroSectionRequest;
use App\Http\Requests\Admin\StorySectionRequest;
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
            'content'   => $request->description,
            'cta_text'  => $request->cta_text,
            'cta_url'   => $request->cta_url,
            'is_active' => $request->boolean('is_active'),
            'settings'  => [
                'season_badge'     => $request->season_badge,
                'eyebrow'          => $request->eyebrow,
                'banner1_label'    => $request->banner1_label,
                'banner1_title'    => $request->banner1_title,
                'banner1_subtitle' => $request->banner1_subtitle,
                'banner1_cta_text' => $request->banner1_cta_text,
                'banner1_cta_url'  => $request->banner1_cta_url,
                'banner1_image'    => $request->banner1_image,
                'banner2_label'    => $request->banner2_label,
                'banner2_title'    => $request->banner2_title,
                'banner2_subtitle' => $request->banner2_subtitle,
                'banner2_cta_text' => $request->banner2_cta_text,
                'banner2_cta_url'  => $request->banner2_cta_url,
                'banner2_image'    => $request->banner2_image,
                'text_position'         => $request->text_position,
                'banner1_text_position' => $request->banner1_text_position,
                'banner2_text_position' => $request->banner2_text_position,
            ],
        ];

        // Hero images carousel (JSON array from multi-picker)
        $heroImages = [];
        if ($request->filled('hero_images')) {
            $decoded = json_decode($request->hero_images, true);
            if (is_array($decoded)) {
                $heroImages = array_values(array_filter($decoded));
            }
        }
        $data['settings']['hero_images'] = $heroImages;

        // First image → image_path for backward compat
        if (!empty($heroImages)) {
            $data['image_path'] = $heroImages[0];
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

    // ── Story ─────────────────────────────────────────────────────────────

    public function story(): View
    {
        $page    = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'story')
            ->first();

        return view('admin.sections.story', compact('section'));
    }

    public function updateStory(StorySectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $data = [
            'type'      => 'story',
            'title'     => $request->title,
            'subtitle'  => $request->subtitle,
            'content'   => $request->content,
            'cta_text'  => $request->cta_text,
            'cta_url'   => $request->cta_url,
            'is_active' => $request->boolean('is_active'),
            'image_path' => $request->image_path ?: null,
            'settings'  => [
                'eyebrow'         => $request->eyebrow,
                'secondary_image' => $request->secondary_image ?: null,
            ],
        ];

        Section::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'story'],
            $data,
        );

        return redirect()->route('admin.story')->with('success', 'Story section berhasil diperbarui.');
    }
}
