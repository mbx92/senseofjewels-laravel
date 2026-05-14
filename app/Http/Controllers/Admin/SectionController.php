<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSectionRequest;
use App\Http\Requests\Admin\HeroSectionRequest;
use App\Http\Requests\Admin\StorySectionRequest;
use App\Models\Page;
use App\Models\Section;
use App\Support\AdminMediaPath;
use App\Support\HeroSlidesState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

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

    public function hero(): Response
    {
        $page = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'hero')
            ->first();

        $s = $section?->settings ?? [];
        $sv = fn (string $key, string $default = '') => $s[$key] ?? $default;

        return Inertia::render('Admin/Sections/Hero', [
            'textPositions' => HeroSlidesState::textPositions(),
            'initial' => [
                'is_active' => (bool) ($section?->is_active ?? true),
                'title' => (string) ($section?->title ?? ''),
                'subtitle' => (string) ($section?->subtitle ?? ''),
                'description' => (string) ($section?->content ?? ''),
                'cta_text' => (string) ($section?->cta_text ?? ''),
                'cta_url' => (string) ($section?->cta_url ?? ''),
                'text_position' => $sv('text_position', 'top-left'),
                'season_badge' => $sv('season_badge'),
                'eyebrow' => $sv('eyebrow'),
                'banner1_label' => $sv('banner1_label'),
                'banner1_title' => $sv('banner1_title'),
                'banner1_subtitle' => $sv('banner1_subtitle'),
                'banner1_cta_text' => $sv('banner1_cta_text'),
                'banner1_cta_url' => $sv('banner1_cta_url'),
                'banner1_image' => $sv('banner1_image'),
                'banner1_text_position' => $sv('banner1_text_position', 'bottom-left'),
                'banner2_label' => $sv('banner2_label'),
                'banner2_title' => $sv('banner2_title'),
                'banner2_subtitle' => $sv('banner2_subtitle'),
                'banner2_cta_text' => $sv('banner2_cta_text'),
                'banner2_cta_url' => $sv('banner2_cta_url'),
                'banner2_image' => $sv('banner2_image'),
                'banner2_text_position' => $sv('banner2_text_position', 'bottom-left'),
                'heroSlides' => HeroSlidesState::slides($section),
            ],
        ]);
    }

    public function updateHero(HeroSectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $heroSlides = [];
        if ($request->filled('hero_slides')) {
            $decodedSlides = json_decode((string) $request->hero_slides, true);
            if (is_array($decodedSlides)) {
                $heroSlides = collect($decodedSlides)
                    ->filter(fn ($slide) => is_array($slide))
                    ->map(function (array $slide) {
                        $focusX = (int) ($slide['focus_x'] ?? 50);
                        $focusY = (int) ($slide['focus_y'] ?? 50);
                        $zoom = (int) ($slide['zoom'] ?? 100);

                        return [
                            'image' => trim((string) ($slide['image'] ?? '')),
                            'title' => trim((string) ($slide['title'] ?? '')),
                            'subtitle' => trim((string) ($slide['subtitle'] ?? '')),
                            'description' => trim((string) ($slide['description'] ?? '')),
                            'cta_text' => trim((string) ($slide['cta_text'] ?? '')),
                            'cta_url' => trim((string) ($slide['cta_url'] ?? '')),
                            'text_position' => trim((string) ($slide['text_position'] ?? '')) ?: 'top-left',
                            'focus_x' => max(0, min(100, $focusX)),
                            'focus_y' => max(0, min(100, $focusY)),
                            'zoom' => max(80, min(160, $zoom)),
                        ];
                    })
                    ->filter(function (array $slide) {
                        return $slide['image'] !== '' || $slide['title'] !== '' || $slide['description'] !== '' || $slide['cta_text'] !== '';
                    })
                    ->values()
                    ->all();
            }
        }

        $primarySlide = $heroSlides[0] ?? null;

        $data = [
            'type' => 'hero',
            'title' => $primarySlide['title'] ?? $request->title,
            'subtitle' => $primarySlide['subtitle'] ?? $request->subtitle,
            'content' => $primarySlide['description'] ?? $request->description,
            'cta_text' => $primarySlide['cta_text'] ?? $request->cta_text,
            'cta_url' => $primarySlide['cta_url'] ?? $request->cta_url,
            'is_active' => $request->boolean('is_active'),
            'settings' => [
                'season_badge' => $request->season_badge,
                'eyebrow' => $request->eyebrow,
                'banner1_label' => $request->banner1_label,
                'banner1_title' => $request->banner1_title,
                'banner1_subtitle' => $request->banner1_subtitle,
                'banner1_cta_text' => $request->banner1_cta_text,
                'banner1_cta_url' => $request->banner1_cta_url,
                'banner1_image' => $request->banner1_image,
                'banner2_label' => $request->banner2_label,
                'banner2_title' => $request->banner2_title,
                'banner2_subtitle' => $request->banner2_subtitle,
                'banner2_cta_text' => $request->banner2_cta_text,
                'banner2_cta_url' => $request->banner2_cta_url,
                'banner2_image' => $request->banner2_image,
                'text_position' => $primarySlide['text_position'] ?? $request->text_position,
                'banner1_text_position' => $request->banner1_text_position,
                'banner2_text_position' => $request->banner2_text_position,
                'hero_slides' => $heroSlides,
            ],
        ];

        // Backward compatibility for views/services that still consume hero_images.
        $heroImages = collect($heroSlides)
            ->pluck('image')
            ->filter(fn ($url) => ! empty($url))
            ->values()
            ->all();

        if (empty($heroImages) && $request->filled('hero_images')) {
            $decoded = json_decode((string) $request->hero_images, true);
            if (is_array($decoded)) {
                $heroImages = array_values(array_filter($decoded));
            }
        }
        $data['settings']['hero_images'] = $heroImages;

        // First image → image_path for backward compat
        if (! empty($heroImages)) {
            $data['image_path'] = $heroImages[0];
        }

        Section::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'hero'],
            $data,
        );

        return redirect()->route('admin.hero')->with('success', 'Hero section berhasil diperbarui.');
    }

    // ── About ─────────────────────────────────────────────────────────────

    public function about(): Response
    {
        $page = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'about')
            ->first();

        return Inertia::render('Admin/Sections/About', [
            'section' => [
                'title' => $section?->title ?? '',
                'content' => $section?->content ?? '',
                'image_url' => $section?->image_path ? (str_starts_with((string) $section->image_path, 'http') ? $section->image_path : Storage::url($section->image_path)) : '',
                'is_active' => (bool) ($section?->is_active ?? true),
            ],
        ]);
    }

    public function updateAbout(AboutSectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $data = [
            'type' => 'about',
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('image')) {
            $section = Section::query()
                ->where('page_id', $page->id)
                ->where('key', 'about')
                ->first();

            if ($section?->image_path && ! str_starts_with((string) $section->image_path, 'http')) {
                Storage::disk('public')->delete($section->image_path);
            }

            $data['image_path'] = $request->file('image')
                ->store('images/sections', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image_path'] = AdminMediaPath::fromPublicUrl($request->string('image_url')->toString());
        }

        Section::query()->updateOrCreate(
            ['page_id' => $page->id, 'key' => 'about'],
            $data,
        );

        return redirect()->route('admin.about')->with('success', 'About section berhasil diperbarui.');
    }

    // ── Story ─────────────────────────────────────────────────────────────

    public function story(): Response
    {
        $page = $this->getHomePage();
        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('key', 'story')
            ->first();
        $settings = $section?->settings ?? [];

        return Inertia::render('Admin/Sections/Story', [
            'section' => [
                'eyebrow' => $settings['eyebrow'] ?? '',
                'title' => $section?->title ?? '',
                'subtitle' => $section?->subtitle ?? '',
                'content' => $section?->content ?? '',
                'cta_text' => $section?->cta_text ?? '',
                'cta_url' => $section?->cta_url ?? '',
                'image_path' => $section?->image_path
                    ? ((str_starts_with((string) $section->image_path, 'http') || str_starts_with((string) $section->image_path, '/'))
                        ? $section->image_path
                        : Storage::url($section->image_path))
                    : '',
                'secondary_image' => $settings['secondary_image'] ?? '',
                'is_active' => (bool) ($section?->is_active ?? true),
            ],
        ]);
    }

    public function updateStory(StorySectionRequest $request): RedirectResponse
    {
        $page = $this->getHomePage();

        $data = [
            'type' => 'story',
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'content' => $request->content,
            'cta_text' => $request->cta_text,
            'cta_url' => $request->cta_url,
            'is_active' => $request->boolean('is_active'),
            'image_path' => $request->image_path ?: null,
            'settings' => [
                'eyebrow' => $request->eyebrow,
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
