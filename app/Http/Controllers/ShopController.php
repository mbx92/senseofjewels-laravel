<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(protected DiscountService $discountService) {}

    public function index(Request $request): View
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

        // Attach discounted price to each product
        foreach ($products as $product) {
            $product->discounted_price = $this->discountService->applyProductDiscount($product);
        }

        return view('shop.index', compact('categories', 'products'));
    }

    public function show(string $slug): View
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

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
