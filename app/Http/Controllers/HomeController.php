<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Voucher;
use App\Services\CurrencyService;
use App\Services\DiscountService;
use App\Support\HomeLandingProps;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(protected DiscountService $discountService) {}

    public function index(CurrencyService $currency): Response
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'title' => 'Home', 'is_active' => true],
        );

        $sections = Section::query()
            ->where('page_id', $page->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        $newArrivals = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn ($q) => $q->where('is_primary', true)->limit(1)])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with([
                'category',
                'images' => fn ($q) => $q->where('is_primary', true)->limit(1),
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

        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $portfolioItems = PortfolioItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $settings = Setting::query()->pluck('value', 'key');
        $instagramFeedEnabled = Setting::boolOf('instagram_feed_enabled', true);
        $instagramUrl = $settings->get('social_instagram', 'https://instagram.com/senseofjewels');

        foreach ($newArrivals as $p) {
            $p->discounted_price = $this->discountService->applyProductDiscount($p);
        }
        foreach ($featuredProducts as $p) {
            $p->discounted_price = $this->discountService->applyProductDiscount($p);
        }

        $promos = Voucher::query()
            ->with('discount')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', Carbon::now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', Carbon::now()))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $shopIndexUrl = route('shop.index');

        return Inertia::render('Home/Index', HomeLandingProps::build(
            $sections,
            $newArrivals,
            $featuredProducts,
            $categories,
            $services,
            $portfolioItems,
            $settings,
            $promos,
            $instagramFeedEnabled,
            (string) $instagramUrl,
            $currency,
            $shopIndexUrl,
        ));
    }
}
