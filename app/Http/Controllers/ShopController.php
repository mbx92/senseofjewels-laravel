<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CurrencyService;
use App\Services\DiscountService;
use App\Support\ShopPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function __construct(protected DiscountService $discountService) {}

    public function index(Request $request, CurrencyService $currency): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $products = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $category = $request->string('category')->toString();

                $query->whereHas('category', function ($categoryQuery) use ($category) {
                    $categoryQuery->where('slug', $category)->orWhere('id', $category);
                });
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        foreach ($products as $product) {
            $product->discounted_price = $this->discountService->applyProductDiscount($product);
        }

        $cartEnabled = Setting::cartEnabled();

        $search = $request->filled('search') ? $request->string('search')->trim()->toString() : null;
        $category = $request->filled('category') ? $request->string('category')->toString() : null;

        return Inertia::render(
            'Shop/Index',
            ShopPresenter::index($products, $categories, $cartEnabled, $search, $category, $currency)
        );
    }

    public function show(string $slug, CurrencyService $currency): Response
    {
        $product = Product::query()
            ->with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $product->discounted_price = $this->discountService->applyProductDiscount($product);

        $relatedProducts = Product::query()
            ->with('images')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
            ->orderByDesc('is_featured')
            ->take(4)
            ->get();

        foreach ($relatedProducts as $related) {
            $related->discounted_price = $this->discountService->applyProductDiscount($related);
        }

        $cartEnabled = Setting::cartEnabled();

        return Inertia::render(
            'Shop/Show',
            ShopPresenter::show($product, $relatedProducts, $cartEnabled, $currency)
        );
    }
}
