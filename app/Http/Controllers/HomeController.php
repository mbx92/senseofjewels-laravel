<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'title' => 'Home', 'is_active' => true],
        );

        $sections = Section::query()
            ->where('page_id', $page->id)
            ->get()
            ->keyBy('key');

        $newArrivals = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn($q) => $q->where('is_primary', true)->limit(1)])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $settings = Setting::query()->get()->keyBy('key');

        return view('pages.landing', compact('sections', 'newArrivals', 'categories', 'settings'));
    }
}
