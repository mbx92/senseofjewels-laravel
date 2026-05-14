<?php

namespace App\Support;

use App\Models\Section;

final class HeroSlidesState
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function slides(?Section $section): array
    {
        $s = $section?->settings ?? [];
        $existing = $s['hero_slides'] ?? [];

        if (! is_array($existing) || count($existing) === 0) {
            $legacyImages = $s['hero_images'] ?? [];
            if (empty($legacyImages) && $section?->image_url) {
                $legacyImages = [$section->image_url];
            }
            if (is_array($legacyImages) && count($legacyImages) > 0) {
                return collect($legacyImages)->map(function (string $image, int $index) use ($section, $s) {
                    return [
                        'image' => $image,
                        'title' => $index === 0 ? (string) ($section?->title ?? '') : '',
                        'subtitle' => $index === 0 ? (string) ($section?->subtitle ?? '') : '',
                        'description' => $index === 0 ? (string) ($section?->content ?? '') : '',
                        'cta_text' => $index === 0 ? (string) ($section?->cta_text ?? '') : '',
                        'cta_url' => $index === 0 ? (string) ($section?->cta_url ?? '') : '',
                        'text_position' => $index === 0 ? (string) ($s['text_position'] ?? 'top-left') : 'top-left',
                        'focus_x' => 50,
                        'focus_y' => 50,
                        'zoom' => 100,
                    ];
                })->values()->all();
            }
        }

        if (is_array($existing) && count($existing) > 0) {
            return collect($existing)->map(function ($slide) {
                if (! is_array($slide)) {
                    return self::emptySlide();
                }

                return [
                    'image' => trim((string) ($slide['image'] ?? '')),
                    'title' => trim((string) ($slide['title'] ?? '')),
                    'subtitle' => trim((string) ($slide['subtitle'] ?? '')),
                    'description' => trim((string) ($slide['description'] ?? '')),
                    'cta_text' => trim((string) ($slide['cta_text'] ?? '')),
                    'cta_url' => trim((string) ($slide['cta_url'] ?? '')),
                    'text_position' => trim((string) ($slide['text_position'] ?? '')) ?: 'top-left',
                    'focus_x' => max(0, min(100, (int) ($slide['focus_x'] ?? 50))),
                    'focus_y' => max(0, min(100, (int) ($slide['focus_y'] ?? 50))),
                    'zoom' => max(80, min(160, (int) ($slide['zoom'] ?? 100))),
                ];
            })->values()->all();
        }

        return [self::emptySlide()];
    }

    /**
     * @return array<string, mixed>
     */
    public static function emptySlide(): array
    {
        return [
            'image' => '',
            'title' => '',
            'subtitle' => '',
            'description' => '',
            'cta_text' => '',
            'cta_url' => '',
            'text_position' => 'top-left',
            'focus_x' => 50,
            'focus_y' => 50,
            'zoom' => 100,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function textPositions(): array
    {
        return [
            'top-left' => 'Atas kiri',
            'top-center' => 'Atas tengah',
            'top-right' => 'Atas kanan',
            'middle-left' => 'Tengah kiri',
            'middle-center' => 'Tengah',
            'middle-right' => 'Tengah kanan',
            'bottom-left' => 'Bawah kiri',
            'bottom-center' => 'Bawah tengah',
            'bottom-right' => 'Bawah kanan',
        ];
    }
}
