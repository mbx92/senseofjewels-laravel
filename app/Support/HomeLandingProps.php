<?php

namespace App\Support;

use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Section;
use App\Models\Service;
use App\Models\Voucher;
use App\Services\CurrencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class HomeLandingProps
{
    public static function build(
        Collection $sections,
        Collection $newArrivals,
        Collection $featuredProducts,
        Collection $categories,
        Collection $services,
        Collection $portfolioItems,
        Collection $settings,
        Collection $promos,
        bool $instagramFeedEnabled,
        string $instagramUrl,
        CurrencyService $currency,
        string $shopIndexUrl,
    ): array {
        $hero = $sections->get('hero');
        $about = $sections->get('about');
        $story = $sections->get('story');
        $contact = $sections->get('contact');

        $h = fn (string $key, string $default = '') => ($hero?->settings[$key] ?? null) ?: $default;
        $s = fn (string $key, string $default = '') => ($story?->settings[$key] ?? null) ?: $default;

        $heroSlides = self::buildHeroSlides($hero, $h, $shopIndexUrl);

        $promoStorageKey = $promos->isNotEmpty()
            ? 'soj_promo_seen_'.md5($promos->map(fn ($p) => $p->id.'|'.($p->updated_at?->timestamp ?? ''))->implode(','))
            : null;

        return [
            'hero' => self::heroPayload($hero, $heroSlides, $h),
            'about' => self::aboutPayload($about),
            'services' => self::servicesPayload($services),
            'portfolioItems' => self::portfolioPayload($portfolioItems),
            'promos' => self::promosPayload($promos, $currency),
            'promoStorageKey' => $promoStorageKey,
            'newArrivals' => $newArrivals->map(fn (Product $p) => self::productCard($p, $currency))->values()->all(),
            'featuredProducts' => $featuredProducts->map(fn (Product $p) => self::productCard($p, $currency))->values()->all(),
            'categories' => $categories->map(fn (Category $c) => [
                'slug' => $c->slug,
                'name' => $c->name,
                'image_url' => $c->image_url ?? null,
            ])->values()->all(),
            'story' => self::storyPayload($story, $s),
            'contact' => self::contactPayload($contact, $settings),
            'instagramFeedEnabled' => $instagramFeedEnabled,
            'instagramUrl' => $instagramUrl,
            'translations' => self::translations(),
        ];
    }

    /**
     * @param  callable(string, string): string  $h
     * @return array<int, array<string, mixed>>
     */
    private static function buildHeroSlides(?Section $hero, callable $h, string $shopIndexUrl): array
    {
        $heroSlides = $hero?->settings['hero_slides'] ?? null;
        if (! is_array($heroSlides) || count($heroSlides) === 0) {
            $heroImages = $hero?->settings['hero_images'] ?? null;
            $heroImages = is_array($heroImages) && count($heroImages) > 0
                ? array_values(array_filter($heroImages))
                : ($hero?->image_url ? [$hero->image_url] : []);
            $heroSlides = collect($heroImages)->map(function (string $image, int $index) use ($hero, $h, $shopIndexUrl) {
                return [
                    'image' => $image,
                    'title' => $index === 0 ? (string) ($hero?->title ?? '') : '',
                    'subtitle' => $index === 0 ? (string) ($hero?->subtitle ?? '') : '',
                    'description' => $index === 0 ? (string) ($hero?->content ?? '') : '',
                    'cta_text' => $index === 0 ? (string) ($hero?->cta_text ?? '') : '',
                    'cta_url' => $index === 0 ? (string) ($hero?->cta_url ?? $shopIndexUrl) : $shopIndexUrl,
                    'text_position' => $index === 0 ? (string) $h('text_position', 'top-left') : 'top-left',
                    'focus_x' => 50,
                    'focus_y' => 50,
                    'zoom' => 100,
                ];
            })->values()->all();
        }

        return collect($heroSlides)->filter(function ($slide) {
            return is_array($slide) && ! empty($slide['image']);
        })->values()->all();
    }

    /**
     * @param  callable(string, string): string  $h
     * @return array<string, mixed>
     */
    private static function heroPayload(?Section $hero, array $heroSlides, callable $h): array
    {
        if (! $hero) {
            return ['enabled' => false];
        }

        return [
            'enabled' => true,
            'slides' => $heroSlides,
            'season_badge' => $h('season_badge'),
            'eyebrow' => $h('eyebrow'),
            'banner1' => [
                'image' => $h('banner1_image'),
                'text_position' => $h('banner1_text_position', 'bottom-left'),
                'text_position_css' => self::textPosCss($h('banner1_text_position', 'bottom-left')),
                'label' => $h('banner1_label'),
                'title' => $h('banner1_title'),
                'subtitle' => $h('banner1_subtitle'),
                'cta_text' => $h('banner1_cta_text'),
                'cta_url' => $h('banner1_cta_url', route('shop.index')),
            ],
            'banner2' => [
                'image' => $h('banner2_image'),
                'text_position' => $h('banner2_text_position', 'bottom-left'),
                'text_position_css' => self::textPosCss($h('banner2_text_position', 'bottom-left')),
                'label' => $h('banner2_label'),
                'title' => $h('banner2_title'),
                'subtitle' => $h('banner2_subtitle'),
                'cta_text' => $h('banner2_cta_text'),
                'cta_url' => $h('banner2_cta_url', route('shop.index')),
            ],
        ];
    }

    private static function textPosCss(string $pos): string
    {
        $map = [
            'top-left' => 'justify-content:flex-start;align-items:flex-start;text-align:left',
            'top-center' => 'justify-content:flex-start;align-items:center;text-align:center',
            'top-right' => 'justify-content:flex-start;align-items:flex-end;text-align:right',
            'middle-left' => 'justify-content:center;align-items:flex-start;text-align:left',
            'middle-center' => 'justify-content:center;align-items:center;text-align:center',
            'middle-right' => 'justify-content:center;align-items:flex-end;text-align:right',
            'bottom-left' => 'justify-content:flex-end;align-items:flex-start;text-align:left',
            'bottom-center' => 'justify-content:flex-end;align-items:center;text-align:center',
            'bottom-right' => 'justify-content:flex-end;align-items:flex-end;text-align:right',
        ];

        return $map[$pos] ?? $map['top-left'];
    }

    private static function aboutPayload(?Section $about): ?array
    {
        if (! $about) {
            return null;
        }

        return [
            'title' => $about->title,
            'content' => $about->content,
        ];
    }

    /**
     * @return list<array{title: string, summary: ?string, image_url: ?string}>
     */
    private static function servicesPayload(Collection $services): array
    {
        return $services->map(function (Service $service) {
            $image = self::resolvePublicUrl($service->image_path);

            return [
                'title' => $service->title,
                'summary' => $service->summary,
                'image_url' => $image,
            ];
        })->values()->all();
    }

    /**
     * @return list<array{title: string, category: ?string, description: ?string, image_url: ?string}>
     */
    private static function portfolioPayload(Collection $items): array
    {
        return $items->map(function (PortfolioItem $item) {
            return [
                'title' => $item->title,
                'category' => $item->category,
                'description' => $item->description,
                'image_url' => self::resolvePublicUrl($item->image_path),
            ];
        })->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function promosPayload(Collection $promos, CurrencyService $currency): array
    {
        return $promos->map(function (Voucher $promo) use ($currency) {
            $discount = $promo->discount;
            $discountLabel = null;
            if ($discount) {
                $discountLabel = $discount->type === 'percent'
                    ? __('Discount').' '.number_format((float) $discount->value, 0).'%'
                    : __('Save').' '.$currency->format((int) $discount->value);
            }

            return [
                'id' => $promo->id,
                'code' => $promo->code,
                'description' => $promo->description,
                'image_url' => $promo->image_url,
                'ends_at' => $promo->ends_at?->format('d M Y'),
                'minimum_order_amount' => (float) $promo->minimum_order_amount,
                'minimum_order_formatted' => $promo->minimum_order_amount > 0 ? $currency->format((int) $promo->minimum_order_amount) : null,
                'discount_type' => $discount?->type,
                'discount_value' => $discount ? (float) $discount->value : null,
                'discount_label' => $discountLabel,
            ];
        })->values()->all();
    }

    /**
     * @param  callable(string, string): string  $s
     */
    private static function storyPayload(?Section $story, callable $s): ?array
    {
        if (! $story) {
            return null;
        }

        return [
            'eyebrow' => $s('eyebrow'),
            'title' => $story->title,
            'subtitle' => $story->subtitle,
            'content' => $story->content,
            'cta_text' => $story->cta_text,
            'cta_url' => $story->cta_url ?: '#',
            'main_image' => $story->image_url,
            'secondary_image' => $s('secondary_image'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function contactPayload(?Section $contact, Collection $settings): array
    {
        $contactEmail = trim((string) $settings->get('contact_email', 'hello@senseofjewels.id'));
        $contactPhone = trim((string) $settings->get('contact_phone', '+62 812 0000 0000'));
        $contactAddress = trim((string) $settings->get('contact_address', 'Seminyak, Bali, Indonesia'));
        $contactWhatsapp = preg_replace('/\D+/', '', (string) $settings->get('contact_whatsapp', ''));
        $waLink = $contactWhatsapp ? 'https://wa.me/'.$contactWhatsapp : null;

        $rawMapsEmbed = trim((string) $settings->get('contact_maps_embed', ''));
        $mapsSrc = '';
        if ($rawMapsEmbed !== '') {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $rawMapsEmbed, $matches)) {
                $mapsSrc = $matches[1];
            } else {
                $mapsSrc = $rawMapsEmbed;
            }
        }

        return [
            'enabled' => (bool) $contact,
            'title' => $contact?->title ?? __('Contact Us'),
            'intro' => $contact?->content ?? __('We are happy to help with custom requests, product details, and order support.'),
            'email' => $contactEmail,
            'phone' => $contactPhone,
            'phone_href' => preg_replace('/\s+/', '', $contactPhone),
            'address' => $contactAddress,
            'wa_link' => $waLink,
            'maps_src' => $mapsSrc ?: null,
        ];
    }

    private static function productCard(Product $p, CurrencyService $currency): array
    {
        $price = (float) $p->price;
        $discounted = $p->discounted_price ?? null;
        $discPct = $discounted !== null && $price > 0
            ? (int) round(($price - $discounted) / $price * 100)
            : null;

        return [
            'slug' => $p->slug,
            'name' => $p->name,
            'image_url' => $p->image_url,
            'is_featured' => (bool) $p->is_featured,
            'price_formatted' => $currency->format((int) $price),
            'discounted_price_formatted' => $discounted !== null ? $currency->format((int) $discounted) : null,
            'discount_percent' => $discPct,
        ];
    }

    private static function resolvePublicUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, '/') || str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * @return array<string, string>
     */
    private static function translations(): array
    {
        return [
            'about_us' => __('About Us'),
            'our_services' => __('Our Services'),
            'what_we_offer' => __('What We Offer'),
            'portfolio' => __('Portfolio'),
            'selected_works' => __('Selected Works'),
            'special_offer' => __('Special Offer'),
            'promo_code' => __('Promo Code'),
            'discount' => __('Discount'),
            'save' => __('Save'),
            'valid_until' => __('Valid until'),
            'minimum_order' => __('Minimum order'),
            'shop_now' => __('Shop Now'),
            'maybe_later' => __('Maybe Later'),
            'special_offers' => __('Special Offers'),
            'active_promotions' => __('Active Promotions'),
            'min_order' => __('Min. order'),
            'new_arrivals' => __('New Arrivals'),
            'view_all' => __('View All'),
            'view_product' => __('View Product'),
            'no_products_yet' => __('No products yet.'),
            'featured_products' => __('Featured Products'),
            'featured_editor_pick' => __('Kurasi perhiasan pilihan dengan detail paling menonjol, material terbaik, dan karakter desain yang kuat.'),
            'explore_collection' => __('Explore Collection'),
            'no_featured_products' => __('No active featured products yet. Mark products as featured from admin to show them on the landing page.'),
            'gorgeous_collections' => __('Gorgeous Collections'),
            'attractive_jewellery' => 'Attractive Jewellery',
            'no_categories_yet' => __('No categories yet.'),
            'get_in_touch' => __('Get in Touch'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'address' => __('Address'),
            'chat_whatsapp' => __('Chat WhatsApp'),
            'send_message' => __('Send Message'),
            'your_name' => __('Your Name'),
            'your_email' => __('Your Email'),
            'subject' => __('Subject'),
            'your_message' => __('Your Message'),
            'featured_badge' => 'Featured',
        ];
    }
}
