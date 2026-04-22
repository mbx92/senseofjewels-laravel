<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->with([
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->where('slug', 'home')
            ->where('is_active', true)
            ->first();

        $sections = $page?->sections instanceof Collection
            ? $page->sections->keyBy('key')
            : collect();

        return view('home.index', [
            'page' => $page,
            'sections' => $sections,
            'settings' => Setting::query()->pluck('value', 'key'),
            'services' => Service::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'portfolioItems' => PortfolioItem::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
