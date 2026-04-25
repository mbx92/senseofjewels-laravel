<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Page;
use App\Models\Product;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Voucher;
use App\Services\DiscountService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected DiscountService $discountService) {}

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

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with([
                'category',
                'images' => fn($q) => $q->where('is_primary', true)->limit(1),
            ])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $settings = Setting::query()->get()->keyBy('key');

        foreach ($newArrivals as $p) {
            $p->discounted_price = $this->discountService->applyProductDiscount($p);
        }
        foreach ($featuredProducts as $p) {
            $p->discounted_price = $this->discountService->applyProductDiscount($p);
        }

        $promos = Voucher::query()
            ->with('discount')
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', Carbon::now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', Carbon::now()))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('pages.landing', compact('sections', 'newArrivals', 'featuredProducts', 'categories', 'settings', 'promos'));
    }
}
