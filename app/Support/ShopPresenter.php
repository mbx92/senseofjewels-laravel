<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CurrencyService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class ShopPresenter
{
    /**
     * @param  LengthAwarePaginator<int, Product>  $products
     * @return array<string, mixed>
     */
    public static function index(
        LengthAwarePaginator $products,
        iterable $categories,
        bool $cartEnabled,
        ?string $search,
        ?string $category,
        CurrencyService $currency,
    ): array {
        $items = collect($products->items())->map(fn (Product $p) => self::listingProduct($p, $currency, $cartEnabled))->values()->all();

        return [
            'categories' => collect($categories)->map(fn (Category $c) => [
                'slug' => $c->slug,
                'name' => $c->name,
            ])->values()->all(),
            'products' => [
                'data' => $items,
                'links' => $products->linkCollection()->values()->all(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
            'cartEnabled' => $cartEnabled,
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
            'translations' => self::indexTranslations(),
        ];
    }

    /**
     * @param  iterable<int, Product>  $relatedProducts
     * @return array<string, mixed>
     */
    public static function show(
        Product $product,
        iterable $relatedProducts,
        bool $cartEnabled,
        CurrencyService $currency,
    ): array {
        $inventoryEnabled = Setting::boolOf('inventory_enabled', true);
        $canPurchase = ! $inventoryEnabled || $product->stock > 0;
        $waMessage = 'Halo Sense of Jewels, saya ingin order '.$product->name.' (SKU: '.$product->sku.'). Link: '.route('shop.show', $product->slug);
        $productWhatsappUrl = Setting::whatsappUrl($waMessage);

        $description = trim((string) $product->description);
        $descriptionIsHtml = str_contains($description, '<');
        $descriptionHtml = $description !== ''
            ? ($descriptionIsHtml ? $description : nl2br(e($description)))
            : '';

        $gallery = $product->images->map(fn ($img) => ['url' => $img->url])->values()->all();

        $specs = [];
        if (! empty($product->specifications) && is_array($product->specifications)) {
            foreach ($product->specifications as $label => $value) {
                $specs[] = [
                    'label' => (string) Str::of($label)->replace(['_', '-'], ' ')->headline(),
                    'value' => is_array($value) ? implode(', ', $value) : (string) $value,
                ];
            }
        }

        $price = (float) $product->price;
        $discounted = $product->discounted_price ?? null;
        $discountPct = $discounted !== null && $price > 0
            ? (int) round(($price - $discounted) / $price * 100)
            : null;
        $saveFormatted = $discounted !== null ? $currency->format((int) ($price - $discounted)) : null;

        $related = collect($relatedProducts)->map(function (Product $p) use ($currency) {
            $dp = $p->discounted_price ?? null;
            $pr = (float) $p->price;

            return [
                'slug' => $p->slug,
                'name' => $p->name,
                'image_url' => $p->image_url,
                'price_formatted' => $currency->format((int) $pr),
                'discounted_price_formatted' => $dp !== null ? $currency->format((int) $dp) : null,
                'discount_percent' => $dp !== null && $pr > 0 ? (int) round(($pr - $dp) / $pr * 100) : null,
            ];
        })->values()->all();

        return [
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'sku' => $product->sku,
                'weight' => $product->weight,
                'stock' => (int) $product->stock,
                'short_description' => $product->short_description,
                'description_html' => $descriptionHtml,
                'has_description' => $description !== '',
                'gallery' => $gallery,
                'main_image_url' => $product->image_url,
                'is_featured' => (bool) $product->is_featured,
                'category_name' => $product->category?->name,
                'price_formatted' => $currency->format((int) $price),
                'discounted_price_formatted' => $discounted !== null ? $currency->format((int) $discounted) : null,
                'discount_percent' => $discountPct,
                'save_formatted' => $saveFormatted,
                'has_discount' => $discounted !== null,
                'specifications' => $specs,
                'inventory_enabled' => $inventoryEnabled,
                'can_purchase' => $canPurchase,
            ],
            'relatedProducts' => $related,
            'cartEnabled' => $cartEnabled,
            'productWhatsappUrl' => $productWhatsappUrl,
            'translations' => self::showTranslations(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function indexTranslations(): array
    {
        return [
            'ready_to_wear' => __('Ready to Wear'),
            'subtitle' => 'Fine jewelry for your daily narrative. Explore our complete collection of modern heirlooms.',
            'all' => __('All'),
            'search_placeholder' => strtoupper(__('Search Collection...')),
            'empty_title' => 'Our collection is empty based on your current selection.',
            'reset_filters' => __('Reset Filters'),
            'view_details' => __('View Details'),
            'bestseller' => __('Bestseller'),
            'add_to_cart' => __('Add to Cart'),
            'order_via_whatsapp' => __('Order via WhatsApp'),
            'whatsapp_not_configured' => __('WhatsApp is not configured yet'),
            'uncategorized' => __('Uncategorized'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function showTranslations(): array
    {
        return [
            'home' => __('Home'),
            'shop' => __('Shop'),
            'no_image' => __('No Image'),
            'featured' => __('Featured'),
            'inventory_off' => __('Inventory Off'),
            'ready_to_order' => __('Ready to Order'),
            'in_stock' => __('In Stock'),
            'out_of_stock' => __('Out of Stock'),
            'add_to_cart' => __('Add to Cart'),
            'order_via_whatsapp' => __('Order via WhatsApp'),
            'whatsapp_not_configured' => __('WhatsApp is not configured yet'),
            'sku' => __('SKU'),
            'description' => __('Description'),
            'no_description' => __('No detailed description available for this product.'),
            'specifications' => __('Specifications'),
            'you_may_also_like' => __('You May Also Like'),
            'related_products' => __('Related Products'),
            'view_all' => __('View All'),
            'view_product' => __('View Product'),
            'save' => __('Save'),
        ];
    }

    private static function listingProduct(Product $p, CurrencyService $currency, bool $cartEnabled): array
    {
        $price = (float) $p->price;
        $discounted = $p->discounted_price ?? null;
        $discountPct = $discounted !== null && $price > 0
            ? (int) round(($price - $discounted) / $price * 100)
            : null;

        $waMessage = 'Halo Sense of Jewels, saya ingin order '.$p->name.' (SKU: '.$p->sku.'). Link: '.route('shop.show', $p->slug);
        $whatsappUrl = Setting::whatsappUrl($waMessage);

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'name' => $p->name,
            'sku' => $p->sku,
            'category_name' => $p->category?->name,
            'image_url' => $p->image_url,
            'is_featured' => (bool) $p->is_featured,
            'price_formatted' => $currency->format((int) $price),
            'discounted_price_formatted' => $discounted !== null ? $currency->format((int) $discounted) : null,
            'discount_percent' => $discountPct,
            'whatsapp_url' => $whatsappUrl,
        ];
    }
}
